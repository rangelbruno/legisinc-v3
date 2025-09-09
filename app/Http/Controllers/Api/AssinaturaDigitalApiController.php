<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposicao;
use App\Services\AssinaturaDigitalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssinaturaDigitalApiController extends Controller
{
    protected $assinaturaService;

    public function __construct(AssinaturaDigitalService $assinaturaService)
    {
        $this->assinaturaService = $assinaturaService;
    }

    /**
     * Obter dados do certificado do usuário
     */
    public function obterDadosCertificado(Proposicao $proposicao)
    {
        try {
            $user = Auth::user();
            
            $response = [
                'success' => true,
                'data' => [
                    'certificado_cadastrado' => $user->temCertificadoDigital(),
                    'certificado_valido' => $user->certificadoDigitalValido(),
                    'senha_salva' => $user->certificado_digital_senha_salva ?? false,
                    'dados_certificado' => null
                ]
            ];
            
            if ($user->temCertificadoDigital()) {
                $response['data']['dados_certificado'] = [
                    'cn' => $user->certificado_digital_cn,
                    'validade' => $user->certificado_digital_validade,
                    'ativo' => $user->certificado_digital_ativo,
                    'senha_salva' => $user->certificado_digital_senha_salva ?? false,
                    'path' => $user->certificado_digital_path
                ];
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Erro ao obter dados do certificado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados do certificado'
            ], 500);
        }
    }

    /**
     * Validar senha do certificado
     */
    public function validarSenhaCertificado(Request $request, Proposicao $proposicao)
    {
        try {
            $validator = Validator::make($request->all(), [
                'senha' => 'required|string|min:4'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $user = Auth::user();
            
            if (!$user->temCertificadoDigital()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificado não encontrado'
                ], 404);
            }
            
            $caminhoCompleto = $user->getCaminhoCompletoCertificado();
            if (!$caminhoCompleto || !file_exists($caminhoCompleto)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo do certificado não encontrado'
                ], 404);
            }
            
            // Validar senha
            $senhaValida = $this->validarSenhaPFX($caminhoCompleto, $request->senha);
            
            return response()->json([
                'success' => true,
                'senha_valida' => $senhaValida,
                'message' => $senhaValida ? 'Senha válida' : 'Senha incorreta'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao validar senha: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar senha'
            ], 500);
        }
    }

    /**
     * Processar assinatura digital via API
     */
    public function processarAssinatura(Request $request, Proposicao $proposicao)
    {
        try {
            $user = Auth::user();
            $certificadoCadastrado = $user->temCertificadoDigital();
            
            // Validação para certificado cadastrado
            if ($request->input('usar_certificado_cadastrado') === '1') {
                $validator = Validator::make($request->all(), [
                    'senha_certificado' => 'nullable|string|min:1'
                ]);
                
                if (!$certificadoCadastrado) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Certificado não encontrado no perfil'
                    ], 404);
                }
                
                if (!$user->certificadoDigitalValido()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Certificado expirado ou inativo'
                    ], 422);
                }
                
                // Processar com certificado cadastrado
                $result = $this->processarAssinaturaCertificadoCadastrado($request, $proposicao, $user);
                
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Proposição assinada digitalmente com sucesso!',
                        'redirect' => route('proposicoes.show', $proposicao),
                        'data' => [
                            'assinatura_id' => $result['assinatura_id'],
                            'data_assinatura' => now()->toISOString()
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 422);
                }
            }
            
            // Fallback para assinatura tradicional
            return response()->json([
                'success' => false,
                'message' => 'Tipo de assinatura não suportado pela API'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro na API de assinatura: ' . $e->getMessage(), [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Processar assinatura com certificado cadastrado via API
     */
    private function processarAssinaturaCertificadoCadastrado(Request $request, Proposicao $proposicao, $user)
    {
        try {
            $senhaCertificado = null;
            
            // Verificar senha
            if ($user->certificado_digital_senha_salva) {
                $senhaCertificado = $user->getSenhaCertificado();
                if (!$senhaCertificado) {
                    return [
                        'success' => false,
                        'message' => 'Erro ao recuperar senha salva do certificado'
                    ];
                }
            } else {
                $senhaCertificado = $request->input('senha_certificado');
                if (!$senhaCertificado) {
                    return [
                        'success' => false,
                        'message' => 'Senha do certificado é obrigatória'
                    ];
                }
            }
            
            // Obter caminho do certificado
            $caminhoCompleto = $user->getCaminhoCompletoCertificado();
            if (!$caminhoCompleto || !file_exists($caminhoCompleto)) {
                return [
                    'success' => false,
                    'message' => 'Arquivo do certificado não encontrado'
                ];
            }
            
            // Validar senha do certificado
            if (!$this->validarSenhaPFX($caminhoCompleto, $senhaCertificado)) {
                return [
                    'success' => false,
                    'message' => 'Senha do certificado incorreta'
                ];
            }
            
            // Processar assinatura usando o serviço
            $dadosAssinatura = [
                'nome_assinante' => $user->name,
                'email_assinante' => $user->email,
                'tipo_certificado' => 'PFX_CADASTRADO',
                'ip_assinatura' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'certificado_cn' => $user->certificado_digital_cn,
                'certificado_validade' => $user->certificado_digital_validade
            ];
            
            // Usar serviço de assinatura digital
            $resultado = $this->assinaturaService->assinarPDF(
                $this->obterCaminhoPDFParaAssinatura($proposicao),
                $caminhoCompleto,
                $senhaCertificado,
                $dadosAssinatura
            );
            
            if (!$resultado['sucesso']) {
                return [
                    'success' => false,
                    'message' => $resultado['erro']
                ];
            }
            
            // Gerar identificador da assinatura
            $identificador = $this->gerarIdentificadorAssinatura($proposicao, $user, 'PFX_CADASTRADO');
            
            // Dados compactos para o banco
            $dadosCompactos = [
                'id' => $identificador,
                'tipo' => 'PFX_CADASTRADO',
                'nome' => $user->name,
                'data' => now()->format('d/m/Y H:i'),
                'cn' => $user->certificado_digital_cn
            ];
            
            // Atualizar proposição
            $proposicao->update([
                'status' => 'enviado_protocolo',
                'assinatura_digital' => json_encode($dadosCompactos),
                'data_assinatura' => now(),
                'ip_assinatura' => $request->ip(),
                'certificado_digital' => $identificador,
                'arquivo_pdf_assinado' => $this->obterCaminhoRelativo($resultado['arquivo_assinado'])
            ]);
            
            Log::info('Proposição assinada via API com certificado cadastrado', [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => $user->id,
                'certificado_cn' => $user->certificado_digital_cn,
                'senha_salva' => $user->certificado_digital_senha_salva
            ]);
            
            return [
                'success' => true,
                'assinatura_id' => $identificador
            ];
                
        } catch (\Exception $e) {
            Log::error('Erro ao processar assinatura via API: ' . $e->getMessage(), [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao processar assinatura'
            ];
        }
    }

    /**
     * Obter caminho do PDF para assinatura
     */
    private function obterCaminhoPDFParaAssinatura(Proposicao $proposicao): string
    {
        if ($proposicao->arquivo_pdf_path && file_exists(storage_path('app/' . $proposicao->arquivo_pdf_path))) {
            return storage_path('app/' . $proposicao->arquivo_pdf_path);
        }
        
        // Se não existe, tentar gerar
        // Implementar lógica de geração de PDF aqui
        
        throw new \Exception('PDF para assinatura não encontrado');
    }

    /**
     * Gerar identificador único para assinatura
     */
    private function gerarIdentificadorAssinatura(Proposicao $proposicao, $user, string $tipo): string
    {
        return strtoupper(substr(md5($proposicao->id . $user->id . now()->timestamp . $tipo), 0, 32));
    }

    /**
     * Obter caminho relativo para storage
     */
    private function obterCaminhoRelativo(string $caminhoAbsoluto): string
    {
        return str_replace(storage_path('app/'), '', $caminhoAbsoluto);
    }

    /**
     * Validar senha do arquivo PFX
     */
    private function validarSenhaPFX(string $arquivoPFX, string $senha): bool
    {
        try {
            $command = sprintf(
                'openssl pkcs12 -in %s -passin pass:%s -noout -legacy 2>&1',
                escapeshellarg($arquivoPFX),
                escapeshellarg($senha)
            );
            
            exec($command, $output, $returnCode);
            
            Log::info('Validação de senha PFX via API', [
                'arquivo' => basename($arquivoPFX),
                'comando' => $command,
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            
            return $returnCode === 0;
            
        } catch (\Exception $e) {
            Log::error('Erro na validação de senha PFX via API: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Status da assinatura
     */
    public function status(Proposicao $proposicao)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $proposicao->status,
                    'assinado' => $proposicao->assinado ?? false,
                    'data_assinatura' => $proposicao->data_assinatura,
                    'certificado_digital' => $proposicao->certificado_digital,
                    'arquivo_pdf_assinado' => $proposicao->arquivo_pdf_assinado
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status'
            ], 500);
        }
    }
}