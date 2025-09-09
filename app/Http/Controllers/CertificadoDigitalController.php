<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CertificadoDigitalController extends Controller
{
    /**
     * Exibir página de gerenciamento do certificado digital
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('certificado-digital.index', compact('user'));
    }

    /**
     * Upload do certificado digital (.pfx)
     */
    public function upload(Request $request)
    {
        // Validação customizada para certificados digitais
        $validator = Validator::make($request->all(), [
            'certificado' => [
                'required',
                'file',
                'max:5120', // 5MB máximo
            ],
            'senha_teste' => 'required|string|min:4',
        ], [
            'certificado.required' => 'Selecione um arquivo de certificado digital.',
            'certificado.file' => 'O arquivo deve ser um certificado digital válido.',
            'certificado.max' => 'O arquivo não pode ser maior que 5MB.',
            'senha_teste.required' => 'Informe a senha do certificado para validação.',
            'senha_teste.min' => 'A senha deve ter pelo menos 4 caracteres.',
        ]);

        // Validação adicional da extensão do arquivo
        $validator->after(function ($validator) use ($request) {
            if ($request->hasFile('certificado')) {
                $arquivo = $request->file('certificado');
                $extensao = strtolower($arquivo->getClientOriginalExtension());
                
                if (!in_array($extensao, ['pfx', 'p12'])) {
                    $validator->errors()->add('certificado', 'O arquivo deve ter extensão .pfx ou .p12.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = auth()->user();
            $arquivo = $request->file('certificado');
            $senha = $request->input('senha_teste');

            // Gerar nome único para o arquivo
            $nomeArquivo = 'certificado_' . $user->id . '_' . time() . '.pfx';
            $caminhoRelativo = 'certificados-digitais/' . $nomeArquivo;

            // Salvar arquivo temporário para validação
            $caminhoTemp = $arquivo->store('temp');
            $caminhoCompletoTemp = storage_path('app/' . $caminhoTemp);
            
            // Debug: log do processo de salvamento
            \Log::info('Salvando arquivo temporário', [
                'nome_original' => $arquivo->getClientOriginalName(),
                'caminho_temp' => $caminhoTemp,
                'caminho_completo' => $caminhoCompletoTemp,
                'arquivo_existe' => file_exists($caminhoCompletoTemp),
                'eh_arquivo' => is_file($caminhoCompletoTemp),
                'tamanho' => file_exists($caminhoCompletoTemp) ? filesize($caminhoCompletoTemp) : 'N/A'
            ]);

            // Validar certificado com PyHanko
            $validacao = $this->validarCertificado($caminhoCompletoTemp, $senha);
            
            if (!$validacao['valido']) {
                Storage::delete($caminhoTemp);
                return redirect()->back()
                    ->withErrors(['certificado' => $validacao['erro']])
                    ->withInput();
            }

            // Remover certificado anterior se existir
            if ($user->certificado_digital_path && Storage::exists($user->certificado_digital_path)) {
                Storage::delete($user->certificado_digital_path);
            }

            // Mover para pasta definitiva
            Storage::move($caminhoTemp, $caminhoRelativo);

            // Atualizar dados do usuário
            $user->update([
                'certificado_digital_path' => $caminhoRelativo,
                'certificado_digital_nome' => $arquivo->getClientOriginalName(),
                'certificado_digital_upload_em' => now(),
                'certificado_digital_validade' => $validacao['validade'] ?? null,
                'certificado_digital_cn' => $validacao['cn'] ?? null,
                'certificado_digital_ativo' => true,
            ]);
            
            // Verificar se deve salvar a senha do certificado (para substituição no form do parlamentar)
            if ($request->has('salvar_senha_substituicao') && $request->input('salvar_senha_substituicao') == '1') {
                $user->salvarSenhaCertificado($senha);
                \Log::info('Senha do certificado salva na substituição', [
                    'usuario_id' => $user->id
                ]);
            }

            return redirect()->back()
                ->with('success', 'Certificado digital cadastrado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload do certificado digital', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['certificado' => 'Erro interno ao processar o certificado. Tente novamente.'])
                ->withInput();
        }
    }

    /**
     * Remover certificado digital
     */
    public function remover()
    {
        try {
            $user = auth()->user();
            
            if ($user->removerCertificadoDigital()) {
                return redirect()->back()
                    ->with('success', 'Certificado digital removido com sucesso!');
            }

            return redirect()->back()
                ->withErrors(['erro' => 'Erro ao remover certificado digital.']);

        } catch (\Exception $e) {
            \Log::error('Erro ao remover certificado digital', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['erro' => 'Erro interno ao remover o certificado.']);
        }
    }

    /**
     * Ativar/Desativar certificado digital
     */
    public function toggleAtivo(Request $request)
    {
        try {
            $user = auth()->user();
            $ativo = $request->input('ativo', false);

            $user->update([
                'certificado_digital_ativo' => $ativo
            ]);

            $mensagem = $ativo ? 'Certificado digital ativado!' : 'Certificado digital desativado!';
            
            return redirect()->back()
                ->with('success', $mensagem);

        } catch (\Exception $e) {
            \Log::error('Erro ao alterar status do certificado digital', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['erro' => 'Erro ao alterar status do certificado.']);
        }
    }

    /**
     * Testar certificado digital
     */
    public function testar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'senha_teste' => 'required|string',
        ], [
            'senha_teste.required' => 'Informe a senha do certificado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $user = auth()->user();
            
            if (!$user->temCertificadoDigital()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum certificado digital encontrado.'
                ], 404);
            }

            $caminhoCertificado = $user->getCaminhoCompletoCertificado();
            $senha = $request->input('senha_teste');

            $validacao = $this->validarCertificado($caminhoCertificado, $senha);

            return response()->json([
                'success' => $validacao['valido'],
                'message' => $validacao['valido'] ? 'Certificado válido!' : $validacao['erro'],
                'dados' => $validacao['valido'] ? [
                    'cn' => $validacao['cn'] ?? 'N/A',
                    'validade' => $validacao['validade'] ? $validacao['validade']->format('d/m/Y') : 'N/A'
                ] : null
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao testar certificado digital', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao testar o certificado.'
            ], 500);
        }
    }

    /**
     * Validar certificado digital usando PyHanko
     */
    private function validarCertificado(string $caminhoCertificado, string $senha): array
    {
        try {
            // Debug: log do caminho do certificado
            \Log::info('Validando certificado', [
                'caminho' => $caminhoCertificado,
                'existe' => file_exists($caminhoCertificado),
                'eh_arquivo' => is_file($caminhoCertificado),
                'eh_diretorio' => is_dir($caminhoCertificado),
                'tamanho' => file_exists($caminhoCertificado) ? filesize($caminhoCertificado) : 'N/A'
            ]);
            
            // Verificar se arquivo existe
            if (!file_exists($caminhoCertificado)) {
                return [
                    'valido' => false,
                    'erro' => 'Arquivo de certificado não encontrado: ' . $caminhoCertificado,
                    'cn' => null,
                    'validade' => null
                ];
            }
            
            // Verificar se é um diretório
            if (is_dir($caminhoCertificado)) {
                return [
                    'valido' => false,
                    'erro' => 'Caminho é um diretório, não um arquivo: ' . $caminhoCertificado,
                    'cn' => null,
                    'validade' => null
                ];
            }
            
            // Verificar se é um arquivo
            if (!is_file($caminhoCertificado)) {
                return [
                    'valido' => false,
                    'erro' => 'Caminho não é um arquivo válido: ' . $caminhoCertificado,
                    'cn' => null,
                    'validade' => null
                ];
            }
            
            // Validação usando OpenSSL com suporte a algoritmos legacy
            $comando = sprintf(
                'openssl pkcs12 -in "%s" -passin "pass:%s" -noout -legacy 2>&1',
                $caminhoCertificado,
                $senha
            );
            
            \Log::info('Comando OpenSSL', ['comando' => $comando]);

            $output = shell_exec($comando);
            
            // OpenSSL não retorna saída quando sucesso, então output vazio ou sem erro = válido
            if (empty(trim($output ?? ''))) {
                // Extrair informações do certificado com suporte a algoritmos legacy
                $comandoCN = sprintf(
                    'openssl pkcs12 -in "%s" -passin "pass:%s" -nokeys -clcerts -legacy | openssl x509 -noout -subject 2>/dev/null | sed "s/.*CN=\\([^,]*\\).*/\\1/"',
                    $caminhoCertificado,
                    $senha
                );
                
                $comandoValidade = sprintf(
                    'openssl pkcs12 -in "%s" -passin "pass:%s" -nokeys -clcerts -legacy | openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2',
                    $caminhoCertificado,
                    $senha
                );
                
                $cn = trim(shell_exec($comandoCN) ?? '');
                $validade = trim(shell_exec($comandoValidade) ?? '');
                
                return [
                    'valido' => true,
                    'erro' => null,
                    'cn' => $cn ?: 'Certificado Válido',
                    'validade' => $validade ? Carbon::createFromFormat('M j H:i:s Y T', $validade) : null
                ];
            }

            return [
                'valido' => false,
                'erro' => 'Certificado inválido ou senha incorreta: ' . trim($output ?? ''),
                'cn' => null,
                'validade' => null
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'erro' => 'Senha incorreta ou certificado inválido: ' . $e->getMessage(),
                'cn' => null,
                'validade' => null
            ];
        }
    }
}