<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProposicaoController;
use App\Models\Proposicao;
use App\Services\AssinaturaDigitalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class AssinaturaDigitalController extends Controller
{
    protected $assinaturaService;

    public function __construct(AssinaturaDigitalService $assinaturaService)
    {
        $this->assinaturaService = $assinaturaService;
    }

    /**
     * Mostrar formulário de assinatura digital
     */
    public function mostrarFormulario(Proposicao $proposicao)
    {
        // Verificação de permissões já é feita pelo middleware check.assinatura.permission
        
        // Verificar se a proposição está disponível para assinatura
        if (!in_array($proposicao->status, ['aprovado', 'aprovado_assinatura'])) {
            abort(403, 'Esta proposição não está disponível para assinatura.');
        }

        // Verificar se já existe PDF para assinatura
        $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
        
        // Se não existe PDF, gerar automaticamente
        if (!$pdfPath || !file_exists($pdfPath)) {
            try {
                $this->gerarPDFParaAssinatura($proposicao);
                $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
                
                if (!$pdfPath || !file_exists($pdfPath)) {
                    return back()->withErrors(['pdf' => 'Erro ao gerar PDF para assinatura.']);
                }
            } catch (\Exception $e) {
                Log::error('Erro ao gerar PDF para assinatura: ' . $e->getMessage());
                return back()->withErrors(['pdf' => 'Erro ao gerar PDF para assinatura: ' . $e->getMessage()]);
            }
        }

        $tiposCertificado = $this->assinaturaService->getTiposCertificado();
        
        // Verificar se o usuário tem certificado digital cadastrado
        $user = Auth::user();
        $certificadoCadastrado = $user->temCertificadoDigital();
        $certificadoValido = $user->certificadoDigitalValido();
        $senhaSalva = $user->certificado_digital_senha_salva ?? false;
        
        
        // Dados do certificado se existir
        $dadosCertificado = null;
        if ($certificadoCadastrado) {
            $dadosCertificado = [
                'cn' => $user->certificado_digital_cn,
                'validade' => $user->certificado_digital_validade,
                'ativo' => $user->certificado_digital_ativo,
                'senha_salva' => $senhaSalva,
                'path' => $user->certificado_digital_path
            ];
        }

        // Usar view Vue.js otimizada
        return view('proposicoes.assinatura.assinar-vue', [
            'proposicaoId' => $proposicao->id,
        ]);
    }

    /**
     * Endpoint de dados para o Vue
     */
    public function dados(Request $request, Proposicao $proposicao)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'proposicaoId' => $proposicao->id,
            'proposicao' => [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'numero_protocolo' => $proposicao->numero_protocolo,
                'titulo' => $proposicao->titulo,
                'ementa' => $proposicao->ementa,
                'status' => $proposicao->status,
                'created_at' => $proposicao->created_at,
                'updated_at' => $proposicao->updated_at,
            ],
            'usuario' => [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
            ],
            'certificado' => $user->certificadoResumo(), // null se não houver
            'temCertificado' => $user->temCertificadoDigital(),
            'certValido' => $user->certificadoDigitalValido(),
        ]);
    }

    /**
     * Processar assinatura digital
     */
    public function processarAssinatura(Request $request, Proposicao $proposicao)
    {
        try {
            // FORÇA SEMPRE JSON - Se não espera JSON, retorna erro
            if (!$request->expectsJson() && !$request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Endpoint aceita apenas requisições JSON'], 406);
            }

            // Log para debug da requisição
            Log::info('AssinaturaDigitalController - processarAssinatura iniciado', [
                'proposicao_id' => $proposicao->id,
                'request_ajax' => $request->ajax(),
                'request_expects_json' => $request->expectsJson(),
                'request_accept' => $request->header('Accept')
            ]);
            
            $user = Auth::user();
            
            // Verificar se usuário tem certificado cadastrado
            if (!$user->temCertificadoDigital()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum certificado digital cadastrado para este usuário.'
                ], 422);
            }
            
            // Verificar se certificado está válido
            if (!$user->certificadoDigitalValido()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O certificado digital está expirado ou inválido.'
                ], 422);
            }
            
            // Validar entrada específica para certificado cadastrado
            $request->validate([
                'usar_certificado_cadastrado' => 'nullable|boolean',
                'senha_certificado' => 'nullable|string|min:1'
            ]);
            
            // Processar assinatura com certificado cadastrado
            return $this->processarAssinaturaCertificadoCadastrado($request, $proposicao, $user);
            
            // Validação customizada para PFX - deve ter pelo menos um dos campos de senha (APENAS PARA UPLOADS)
            if ($request->tipo_certificado === 'PFX' && !$request->input('usar_certificado_cadastrado')) {
                if (empty($request->senha_pfx) && empty($request->senha_certificado)) {
                    return back()->withErrors(['senha_certificado' => 'Senha é obrigatória para certificados PFX.']);
                }
                
                // Retornar JSON para requisições AJAX
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Senha é obrigatória para certificados PFX.'
                    ], 422);
                }
                return back()->withErrors(['senha_certificado' => 'Senha é obrigatória para certificados PFX.']);
            }
            
            // Validar senha do certificado PFX antecipadamente
            if ($request->tipo_certificado === 'PFX') {
                if ($request->hasFile('arquivo_pfx')) {
                    $arquivoPFX = $request->file('arquivo_pfx');
                    $senhaPFX = $request->senha_pfx ?: $request->senha_certificado;
                    
                    // Salvar temporariamente para validação - garantir que diretório temp existe
                    $tempFileName = 'pfx_validation_' . time() . '_' . uniqid() . '.pfx';
                    
                    // Garantir que o diretório temp existe
                    if (!Storage::exists('temp')) {
                        Storage::makeDirectory('temp');
                    }
                    
                    // Debug do arquivo antes do save
                    Log::info('Debug arquivo PFX antes de salvar', [
                        'tempFileName' => $tempFileName,
                        'arquivo_size' => $arquivoPFX->getSize(),
                        'arquivo_mime' => $arquivoPFX->getMimeType(),
                        'arquivo_original' => $arquivoPFX->getClientOriginalName(),
                        'temp_dir_exists' => Storage::exists('temp'),
                        'arquivo_isValid' => $arquivoPFX->isValid(),
                        'arquivo_getRealPath' => $arquivoPFX->getRealPath(),
                        'storage_default_disk' => config('filesystems.default')
                    ]);
                    
                    // Método direto com file_put_contents
                    try {
                        // Diretório de destino
                        $storageDir = storage_path('app/temp');
                        
                        // Garantir que o diretório existe
                        if (!is_dir($storageDir)) {
                            mkdir($storageDir, 0755, true);
                        }
                        
                        // Caminho completo do arquivo temporário
                        $fullTempPath = $storageDir . '/' . $tempFileName;
                        
                        // Ler conteúdo do arquivo temporário do PHP
                        $fileContents = file_get_contents($arquivoPFX->getRealPath());
                        
                        if ($fileContents === false) {
                            throw new \Exception('Erro ao ler conteúdo do arquivo PFX');
                        }
                        
                        // Salvar usando file_put_contents diretamente
                        $bytesWritten = file_put_contents($fullTempPath, $fileContents);
                        
                        if ($bytesWritten === false) {
                            throw new \Exception('Erro ao gravar arquivo PFX diretamente');
                        }
                        
                        Log::info('Debug resultado do file_put_contents - SUCESSO', [
                            'fullTempPath' => $fullTempPath,
                            'file_size' => strlen($fileContents),
                            'bytes_written' => $bytesWritten,
                            'file_exists' => file_exists($fullTempPath),
                            'is_readable' => is_readable($fullTempPath)
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Debug erro no salvamento direto', [
                            'error_message' => $e->getMessage(),
                            'error_code' => $e->getCode(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                            'storage_dir' => $storageDir ?? 'undefined',
                            'temp_filename' => $tempFileName
                        ]);
                        throw new \Exception('Erro ao salvar arquivo temporário para validação PFX');
                    }
                    
                    Log::info('Debug paths para validação PFX', [
                        'tempFileName' => $tempFileName,
                        'fullTempPath' => $fullTempPath,
                        'file_exists' => file_exists($fullTempPath),
                        'is_dir' => is_dir($fullTempPath),
                        'file_permissions' => substr(sprintf('%o', fileperms($fullTempPath)), -4)
                    ]);
                    
                    // Validar se a senha está correta
                    $assinaturaService = app(\App\Services\AssinaturaDigitalService::class);
                    if (!$this->validarSenhaPFX($fullTempPath, $senhaPFX)) {
                        // Remover arquivo temporário
                        @unlink($fullTempPath);
                        return back()->withErrors([
                            'senha_certificado' => 'Senha do certificado PFX está incorreta. Verifique a senha e tente novamente.'
                        ]);
                    }
                    
                    // Remover arquivo temporário após validação
                    @unlink($fullTempPath);
                }
            }
            
            // Validação adicional para tipos que requerem senha
            if (in_array($request->tipo_certificado, ['A1', 'A3']) && empty($request->senha)) {
                return back()->withErrors(['senha' => 'Senha é obrigatória para certificados A1/A3.']);
            }
            
            // Validação específica para arquivo PFX
            if ($request->tipo_certificado === 'PFX') {
                if (!$request->hasFile('arquivo_pfx')) {
                    return back()->withErrors(['arquivo_pfx' => 'Arquivo de certificado é obrigatório para tipo PFX.']);
                }
                
                $arquivo = $request->file('arquivo_pfx');
                if ($arquivo->getSize() > 2048 * 1024) { // 2MB
                    return back()->withErrors(['arquivo_pfx' => 'Arquivo muito grande. Máximo 2MB.']);
                }
                
                // Para demonstração, aceitar qualquer arquivo
                Log::info('Arquivo PFX aceito para demonstração', [
                    'nome' => $arquivo->getClientOriginalName(),
                    'tamanho' => $arquivo->getSize(),
                    'tipo' => $arquivo->getMimeType()
                ]);
            }

            // Obter caminho do PDF para assinatura
            $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                return back()->withErrors(['pdf' => 'PDF para assinatura não encontrado.']);
            }

            // Preparar dados da assinatura
            $dadosAssinatura = [
                'tipo_certificado' => $request->tipo_certificado,
                'nome_assinante' => Auth::user()->name, // Nome do usuário logado
                'senha' => $request->senha,
                'usuario_id' => Auth::id(),
                'ip_assinatura' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Processar arquivo PFX se fornecido
            if ($request->tipo_certificado === 'PFX' && $request->hasFile('arquivo_pfx')) {
                $arquivoPFX = $request->file('arquivo_pfx');
                $caminhoPFX = $this->salvarArquivoPFX($arquivoPFX, $proposicao);
                $dadosAssinatura['arquivo_pfx'] = $caminhoPFX;
                // Aceitar senha de ambos os campos (senha_pfx ou senha_certificado)
                $dadosAssinatura['senha_pfx'] = $request->senha_pfx ?: $request->senha_certificado;
            }
            
            // Gerar identificador e checksum
            $pdfContent = file_get_contents($pdfPath);
            $identificador = $this->assinaturaService->gerarIdentificadorAssinatura();
            $checksum = $this->assinaturaService->gerarChecksum($pdfContent);
            
            $dadosAssinatura['identificador'] = $identificador;
            $dadosAssinatura['checksum'] = $checksum;

            // Processar assinatura (passando usuário para usar certificado se disponível)
            $usuario = Auth::user();
            
            // Se usuário tem certificado e não forneceu arquivo PFX, usar certificado do usuário
            if ($usuario->temCertificadoDigital() && !$request->hasFile('arquivo_pfx')) {
                // Validar que a senha do certificado foi fornecida
                $senhaCertificado = $request->senha_certificado ?? $request->senha_pfx ?? '';
                if (empty($senhaCertificado)) {
                    return back()->withErrors(['senha_certificado' => 'Informe a senha do seu certificado digital.']);
                }
                
                $dadosAssinatura['senha_certificado'] = $senhaCertificado;
                $dadosAssinatura['tipo_certificado'] = 'PFX'; // Forçar PFX quando usar certificado do usuário
                
                Log::info('Usando certificado digital do usuário para assinatura', [
                    'user_id' => $usuario->id,
                    'certificado_nome' => $usuario->certificado_digital_nome
                ]);
            }
            
            $pdfAssinado = $this->assinaturaService->assinarPDF($pdfPath, $dadosAssinatura, $usuario);

            if (!$pdfAssinado) {
                // Retornar JSON para requisições AJAX
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Falha ao processar assinatura digital.'
                    ], 422);
                }
                return back()->withErrors(['assinatura' => 'Falha ao processar assinatura digital.']);
            }

            // Gerar dados compactos para o banco
            $dadosCompactos = [
                'id' => $identificador,
                'tipo' => $dadosAssinatura['tipo_certificado'],
                'nome' => $dadosAssinatura['nome_assinante'],
                'data' => now()->format('d/m/Y H:i')
            ];
            
            // Atualizar proposição com dados otimizados - Status vai para PROTOCOLO
            $statusAnterior = $proposicao->status;
            $proposicao->update([
                'status' => 'enviado_protocolo', // Após assinatura vai para protocolo
                'assinatura_digital' => json_encode($dadosCompactos), // Dados compactos
                'data_assinatura' => now(),
                'ip_assinatura' => $request->ip(),
                'certificado_digital' => $identificador, // ID de 32 caracteres
                'arquivo_pdf_assinado' => $this->obterCaminhoRelativo($pdfAssinado)
            ]);

            // Observer registrará automaticamente no histórico

            // Log da operação
            Log::info('Proposição assinada digitalmente', [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => Auth::id(),
                'tipo_certificado' => $request->tipo_certificado,
                'pdf_assinado' => $pdfAssinado
            ]);

            // Retornar JSON para requisições AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proposição assinada digitalmente com sucesso!',
                    'redirect' => route('proposicoes.show', $proposicao)
                ]);
            }

            return redirect()->route('proposicoes.show', $proposicao)
                ->with('success', 'Proposição assinada digitalmente com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao processar assinatura digital: ' . $e->getMessage(), [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // SEMPRE retornar JSON - nunca HTML
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar assinatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Servir PDF para visualização durante o processo de assinatura
     */
    public function servirPDFParaAssinatura(Proposicao $proposicao)
    {
        // Log detalhado de início
        $user = Auth::user();
        Log::info('📝 ASSINATURA: Servindo PDF para visualização durante assinatura', [
            'proposicao_id' => $proposicao->id,
            'proposicao_status' => $proposicao->status,
            'proposicao_autor_id' => $proposicao->autor_id,
            'proposicao_parlamentar_id' => $proposicao->parlamentar_id ?? 'N/A',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'url_accessed' => request()->url(),
            'pdf_s3_path' => $proposicao->pdf_s3_path,
            'pdf_s3_url_exists' => !empty($proposicao->pdf_s3_url)
        ]);

        // Verificar permissões usando a mesma lógica do servePDF original

        // Debug detalhado das verificações
        Log::info('🔍 ASSINATURA DEBUG: Verificações de permissão detalhadas', [
            'proposicao_id' => $proposicao->id,
            'user_id' => $user->id,
            'user_isLegislativo' => $user->isLegislativo(),
            'proposicao_autor_id_equals_user' => $proposicao->autor_id === $user->id,
            'user_isAssessorJuridico' => $user->isAssessorJuridico(),
            'user_isProtocolo' => $user->isProtocolo(),
            'user_isParlamentar' => $user->isParlamentar()
        ]);

        // Permitir acesso para:
        // 1. Autor da proposição (parlamentar) - especialmente para status 'protocolado'
        // 2. Usuários do legislativo
        // 3. Usuários com perfil jurídico
        // 4. Usuários do protocolo
        if (! $user->isLegislativo() && $proposicao->autor_id !== $user->id && ! $user->isAssessorJuridico() && ! $user->isProtocolo()) {
            Log::warning('🔴 ASSINATURA PDF: Acesso negado por permissões', [
                'proposicao_id' => $proposicao->id,
                'user_id' => Auth::id(),
                'user_roles' => Auth::user()->roles->pluck('name'),
                'proposicao_autor_id' => $proposicao->autor_id
            ]);
            abort(403, 'Acesso negado.');
        }

        // Para parlamentares, permitir apenas em status específicos onde o PDF já está disponível
        if ($user->isParlamentar() && $proposicao->autor_id === $user->id) {
            $statusPermitidos = ['protocolado', 'aprovado', 'assinado', 'enviado_protocolo', 'retornado_legislativo', 'aprovado_assinatura'];
            if (! in_array($proposicao->status, $statusPermitidos)) {
                Log::warning('🔴 ASSINATURA PDF: Status não permitido para parlamentar', [
                    'proposicao_id' => $proposicao->id,
                    'status_atual' => $proposicao->status,
                    'status_permitidos' => $statusPermitidos
                ]);
                abort(403, 'PDF não disponível para download neste status.');
            }
        }

        // Verificação adicional: para assinatura, deve estar em status apropriado
        if (!in_array($proposicao->status, ['aprovado', 'aprovado_assinatura', 'retornado_legislativo'])) {
            Log::warning('🔴 ASSINATURA PDF: Status inadequado para assinatura', [
                'proposicao_id' => $proposicao->id,
                'status_atual' => $proposicao->status
            ]);
            abort(403, 'Esta proposição não está disponível para assinatura no status atual.');
        }

        // 1) PRIORIDADE MÁXIMA: PDF na S3 (mais recente após exportação)
        Log::info('🔍 ASSINATURA: Verificando PDF na S3', [
            'proposicao_id' => $proposicao->id,
            'pdf_s3_path_exists' => !empty($proposicao->pdf_s3_path),
            'pdf_s3_path_value' => $proposicao->pdf_s3_path,
            'pdf_s3_url_exists' => !empty($proposicao->pdf_s3_url)
        ]);

        // 🤖 AUTO-FIX: Se não há pdf_s3_path mas deveria haver (proposição aprovada), tentar fix automático
        if (!$proposicao->pdf_s3_path && $proposicao->status === 'aprovado') {
            Log::info('🤖 ASSINATURA AUTO-FIX: PDF S3 não configurado, tentando correção automática', [
                'proposicao_id' => $proposicao->id,
                'status' => $proposicao->status
            ]);

            try {
                // Usar a lógica do fixProposicaoS3Auto para detectar automaticamente
                $autoFixResult = $this->executeAutoFix($proposicao);

                if ($autoFixResult['success']) {
                    Log::info('✅ ASSINATURA AUTO-FIX: Correção automática bem-sucedida', [
                        'proposicao_id' => $proposicao->id,
                        'pdf_s3_path' => $autoFixResult['pdf_s3_path']
                    ]);

                    // Recarregar a proposição com os dados atualizados
                    $proposicao->refresh();
                } else {
                    Log::warning('⚠️ ASSINATURA AUTO-FIX: Correção automática falhou', [
                        'proposicao_id' => $proposicao->id,
                        'reason' => $autoFixResult['message']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ ASSINATURA AUTO-FIX: Erro durante correção automática', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($proposicao->pdf_s3_path) {
            Log::info('🌐 ASSINATURA: PDF S3 encontrado, verificando disponibilidade', [
                'proposicao_id' => $proposicao->id,
                'pdf_s3_path' => $proposicao->pdf_s3_path
            ]);

            try {
                // Primeiro, verificar se o arquivo existe na S3
                if (Storage::disk('s3')->exists($proposicao->pdf_s3_path)) {
                    Log::info('✅ ASSINATURA: Arquivo confirmado na S3', [
                        'proposicao_id' => $proposicao->id,
                        'pdf_s3_path' => $proposicao->pdf_s3_path
                    ]);

                    // Se existe URL válida, testar
                    if ($proposicao->pdf_s3_url) {
                        $context = stream_context_create([
                            'http' => [
                                'method' => 'HEAD',
                                'timeout' => 5,
                                'ignore_errors' => true
                            ]
                        ]);

                        $headers = @get_headers($proposicao->pdf_s3_url, false, $context);

                        if ($headers && strpos($headers[0], '200') !== false) {
                            Log::info('✅ ASSINATURA: URL S3 válida - redirecionando', [
                                'proposicao_id' => $proposicao->id,
                                's3_status' => $headers[0]
                            ]);

                            return redirect($proposicao->pdf_s3_url);
                        }
                    }

                    // URL não existe ou expirou - gerar nova
                    Log::info('🔄 ASSINATURA: Gerando nova URL S3', [
                        'proposicao_id' => $proposicao->id
                    ]);

                    $newS3Url = Storage::disk('s3')->temporaryUrl($proposicao->pdf_s3_path, now()->addHour());

                    $proposicao->update(['pdf_s3_url' => $newS3Url]);

                    Log::info('✅ ASSINATURA: Nova URL S3 gerada - redirecionando', [
                        'proposicao_id' => $proposicao->id,
                        'new_url_generated' => true
                    ]);

                    return redirect($newS3Url);
                } else {
                    Log::warning('⚠️ ASSINATURA: Arquivo não encontrado na S3', [
                        'proposicao_id' => $proposicao->id,
                        'pdf_s3_path' => $proposicao->pdf_s3_path
                    ]);

                    // Limpar campos S3 se arquivo não existir mais
                    $proposicao->update([
                        'pdf_s3_path' => null,
                        'pdf_s3_url' => null,
                        'pdf_size_bytes' => null
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ ASSINATURA: Erro ao acessar S3', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            Log::info('ℹ️ ASSINATURA: Nenhum PDF na S3 para esta proposição', [
                'proposicao_id' => $proposicao->id
            ]);
        }

        // 2) FALLBACK: Usar o método padrão servePDF
        Log::info('📄 ASSINATURA: Usando fallback para servePDF', [
            'proposicao_id' => $proposicao->id
        ]);

        // Redirecionar para o endpoint padrão de PDF
        return app(ProposicaoController::class)->servePDF($proposicao);
    }

    /**
     * Visualizar PDF assinado
     */
    public function visualizarPDFAssinado(Proposicao $proposicao)
    {
        // Verificar permissões
        if (!Auth::user()->hasPermissionTo('proposicoes.view')) {
            abort(403, 'Você não tem permissão para visualizar esta proposição.');
        }

        // Verificar se existe PDF assinado
        if (!$proposicao->arquivo_pdf_assinado) {
            abort(404, 'PDF assinado não encontrado.');
        }

        $caminhoPDF = storage_path('app/' . $proposicao->arquivo_pdf_assinado);
        
        if (!file_exists($caminhoPDF)) {
            abort(404, 'Arquivo PDF assinado não encontrado.');
        }

        // Retornar PDF para visualização
        return response()->file($caminhoPDF, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="proposicao_' . $proposicao->id . '_assinada.pdf"'
        ]);
    }

    /**
     * Download do PDF assinado
     */
    public function downloadPDFAssinado(Proposicao $proposicao)
    {
        // Verificar permissões
        if (!Auth::user()->hasPermissionTo('proposicoes.view')) {
            abort(403, 'Você não tem permissão para baixar esta proposição.');
        }

        // Verificar se existe PDF assinado
        if (!$proposicao->arquivo_pdf_assinado) {
            abort(404, 'PDF assinado não encontrado.');
        }

        $caminhoPDF = storage_path('app/' . $proposicao->arquivo_pdf_assinado);
        
        if (!file_exists($caminhoPDF)) {
            abort(404, 'Arquivo PDF assinado não encontrado.');
        }

        // Retornar PDF para download
        return response()->download($caminhoPDF, 'proposicao_' . $proposicao->id . '_assinada.pdf');
    }

    /**
     * Teste de endpoint JSON
     */
    public function testeJson(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Endpoint funcionando corretamente',
            'ajax' => $request->ajax(),
            'expects_json' => $request->expectsJson(),
            'headers' => $request->headers->all()
        ]);
    }

    /**
     * Debug: Verificar status S3 de uma proposição
     */
    public function debugS3Status(Proposicao $proposicao)
    {
        $status = [
            'proposicao_id' => $proposicao->id,
            'pdf_s3_path' => $proposicao->pdf_s3_path,
            'pdf_s3_url' => $proposicao->pdf_s3_url,
            'pdf_size_bytes' => $proposicao->pdf_size_bytes,
            's3_file_exists' => false,
            's3_url_valid' => false,
            'error' => null
        ];

        if ($proposicao->pdf_s3_path) {
            try {
                // Verificar se arquivo existe na S3
                $status['s3_file_exists'] = Storage::disk('s3')->exists($proposicao->pdf_s3_path);

                // Se tem URL, verificar se é válida
                if ($proposicao->pdf_s3_url) {
                    $context = stream_context_create([
                        'http' => [
                            'method' => 'HEAD',
                            'timeout' => 5,
                            'ignore_errors' => true
                        ]
                    ]);

                    $headers = @get_headers($proposicao->pdf_s3_url, false, $context);
                    $status['s3_url_valid'] = $headers && strpos($headers[0], '200') !== false;
                    $status['s3_headers'] = $headers[0] ?? 'No response';
                }

                // Se arquivo existe mas URL não é válida, gerar nova
                if ($status['s3_file_exists'] && !$status['s3_url_valid']) {
                    $newUrl = Storage::disk('s3')->temporaryUrl($proposicao->pdf_s3_path, now()->addHour());
                    $status['new_url_generated'] = $newUrl;
                }

            } catch (\Exception $e) {
                $status['error'] = $e->getMessage();
            }
        }

        return response()->json($status);
    }

    /**
     * Executar correção automática interno (reutilizado pelo auto-fix)
     */
    private function executeAutoFix(Proposicao $proposicao): array
    {
        try {
            // Buscar arquivos PDF da proposição na S3 usando padrão automático
            $possiveisCaminhos = [
                "proposicoes/pdfs/2025/09/24/{$proposicao->id}/automatic/proposicao_{$proposicao->id}_auto_" . time() . ".pdf"
            ];

            // Tentar encontrar PDFs existentes na S3
            $s3Files = [];
            try {
                $files = Storage::disk('s3')->files("proposicoes/pdfs/2025/09/24/{$proposicao->id}/automatic/");
                $s3Files = array_filter($files, function($file) use ($proposicao) {
                    return str_contains($file, "proposicao_{$proposicao->id}_auto_") && str_ends_with($file, '.pdf');
                });
            } catch (\Exception $e) {
                // Se não conseguir listar, tentar caminhos fixos
            }

            // Usar o arquivo mais recente ou o caminho fixo
            $s3Path = null;
            if (!empty($s3Files)) {
                // Ordenar por data de modificação (mais recente primeiro)
                rsort($s3Files);
                $s3Path = $s3Files[0];
            } else {
                // Tentar caminhos conhecidos baseados nos logs
                if ($proposicao->id == 4) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/4/automatic/proposicao_4_auto_1758720786.pdf';
                } elseif ($proposicao->id == 5) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/5/automatic/proposicao_5_auto_1758725932.pdf';
                } elseif ($proposicao->id == 6) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/6/automatic/proposicao_6_auto_1758726721.pdf';
                } elseif ($proposicao->id == 7) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/7/automatic/proposicao_7_auto_1758727477.pdf';
                } elseif ($proposicao->id == 9) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/9/automatic/proposicao_9_auto_1758728380.pdf';
                }
            }

            if (!$s3Path) {
                return [
                    'success' => false,
                    'message' => 'Nenhum PDF encontrado na S3 para esta proposição',
                    'proposicao_id' => $proposicao->id
                ];
            }

            // Verificar se arquivo existe na S3
            if (Storage::disk('s3')->exists($s3Path)) {
                // Obter tamanho do arquivo
                $size = Storage::disk('s3')->size($s3Path);

                // Gerar URL temporária
                $tempUrl = Storage::disk('s3')->temporaryUrl($s3Path, now()->addHour());

                // Atualizar usando DB transaction para garantir persistência
                DB::transaction(function () use ($proposicao, $s3Path, $tempUrl, $size) {
                    $proposicao->pdf_s3_path = $s3Path;
                    $proposicao->pdf_s3_url = $tempUrl;
                    $proposicao->pdf_size_bytes = $size;
                    $saved = $proposicao->save();

                    if (!$saved) {
                        throw new \Exception('Falha ao salvar no banco de dados');
                    }
                });

                return [
                    'success' => true,
                    'message' => "Auto-fix executado com sucesso para proposição {$proposicao->id}",
                    'pdf_s3_path' => $s3Path,
                    'pdf_size_bytes' => $size
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Arquivo não encontrado na S3: {$s3Path}",
                    'proposicao_id' => $proposicao->id
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro durante auto-fix: ' . $e->getMessage(),
                'proposicao_id' => $proposicao->id
            ];
        }
    }

    /**
     * Debug: Corrigir S3 path automaticamente para qualquer proposição (temporário)
     */
    public function fixProposicaoS3Auto(Proposicao $proposicao)
    {
        try {
            // Buscar arquivos PDF da proposição na S3 usando padrão automático
            $possiveisCaminhos = [
                "proposicoes/pdfs/2025/09/24/{$proposicao->id}/automatic/proposicao_{$proposicao->id}_auto_" . time() . ".pdf",
                "proposicoes/pdfs/2025/09/24/{$proposicao->id}/automatic/proposicao_{$proposicao->id}_auto_1758720786.pdf",
                "proposicoes/pdfs/2025/09/24/{$proposicao->id}/automatic/proposicao_{$proposicao->id}_auto_1758725932.pdf"
            ];

            // Tentar encontrar PDFs existentes na S3
            $s3Files = [];
            try {
                $files = Storage::disk('s3')->files("proposicoes/pdfs/2025/09/24/{$proposicao->id}/automatic/");
                $s3Files = array_filter($files, function($file) use ($proposicao) {
                    return str_contains($file, "proposicao_{$proposicao->id}_auto_") && str_ends_with($file, '.pdf');
                });
            } catch (\Exception $e) {
                // Se não conseguir listar, tentar caminhos fixos
            }

            // Usar o arquivo mais recente ou o caminho fixo
            $s3Path = null;
            if (!empty($s3Files)) {
                // Ordenar por data de modificação (mais recente primeiro)
                rsort($s3Files);
                $s3Path = $s3Files[0];
            } else {
                // Tentar caminhos conhecidos baseados nos logs
                if ($proposicao->id == 4) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/4/automatic/proposicao_4_auto_1758720786.pdf';
                } elseif ($proposicao->id == 5) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/5/automatic/proposicao_5_auto_1758725932.pdf';
                } elseif ($proposicao->id == 6) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/6/automatic/proposicao_6_auto_1758726721.pdf';
                } elseif ($proposicao->id == 7) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/7/automatic/proposicao_7_auto_1758727477.pdf';
                } elseif ($proposicao->id == 9) {
                    $s3Path = 'proposicoes/pdfs/2025/09/24/9/automatic/proposicao_9_auto_1758728380.pdf';
                }
            }

            if (!$s3Path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum PDF encontrado na S3 para esta proposição',
                    'proposicao_id' => $proposicao->id,
                    'caminhos_testados' => $possiveisCaminhos,
                    'arquivos_encontrados' => $s3Files
                ]);
            }

            // Verificar se arquivo existe na S3
            if (Storage::disk('s3')->exists($s3Path)) {
                // Obter tamanho do arquivo
                $size = Storage::disk('s3')->size($s3Path);

                // Gerar URL temporária
                $tempUrl = Storage::disk('s3')->temporaryUrl($s3Path, now()->addHour());

                // Dados antes da atualização
                $beforeUpdate = [
                    'pdf_s3_path' => $proposicao->pdf_s3_path,
                    'pdf_s3_url' => $proposicao->pdf_s3_url,
                    'pdf_size_bytes' => $proposicao->pdf_size_bytes
                ];

                // Atualizar usando DB transaction para garantir persistência
                DB::transaction(function () use ($proposicao, $s3Path, $tempUrl, $size) {
                    $proposicao->pdf_s3_path = $s3Path;
                    $proposicao->pdf_s3_url = $tempUrl;
                    $proposicao->pdf_size_bytes = $size;
                    $saved = $proposicao->save();

                    if (!$saved) {
                        throw new \Exception('Falha ao salvar no banco de dados');
                    }
                });

                // Forçar refresh dos dados
                $proposicao->refresh();

                // Dados após a atualização
                $afterUpdate = [
                    'pdf_s3_path' => $proposicao->pdf_s3_path,
                    'pdf_s3_url' => $proposicao->pdf_s3_url,
                    'pdf_size_bytes' => $proposicao->pdf_size_bytes
                ];

                return response()->json([
                    'success' => true,
                    'message' => "Proposição {$proposicao->id} atualizada com PDF S3 correto (auto-fix)",
                    'data' => [
                        'proposicao_id' => $proposicao->id,
                        'pdf_s3_path' => $s3Path,
                        'pdf_size_bytes' => $size,
                        'before_update' => $beforeUpdate,
                        'after_update' => $afterUpdate,
                        'persistence_verified' => $proposicao->pdf_s3_path === $s3Path,
                        'new_url_generated' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado na S3',
                    'pdf_s3_path' => $s3Path
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Debug: Corrigir S3 path da proposição 4 (temporário)
     */
    public function fixProposicao4S3(Proposicao $proposicao)
    {
        if ($proposicao->id !== 4) {
            return response()->json(['error' => 'Este endpoint é apenas para proposição 4'], 400);
        }

        $s3Path = 'proposicoes/pdfs/2025/09/24/4/automatic/proposicao_4_auto_1758720786.pdf';

        try {
            // Verificar se arquivo existe na S3
            if (Storage::disk('s3')->exists($s3Path)) {
                // Obter tamanho do arquivo
                $size = Storage::disk('s3')->size($s3Path);

                // Gerar URL temporária
                $tempUrl = Storage::disk('s3')->temporaryUrl($s3Path, now()->addHour());

                // Dados antes da atualização
                $beforeUpdate = [
                    'pdf_s3_path' => $proposicao->pdf_s3_path,
                    'pdf_s3_url' => $proposicao->pdf_s3_url,
                    'pdf_size_bytes' => $proposicao->pdf_size_bytes
                ];

                // Atualizar usando DB transaction para garantir persistência
                \DB::transaction(function () use ($proposicao, $s3Path, $tempUrl, $size) {
                    $proposicao->pdf_s3_path = $s3Path;
                    $proposicao->pdf_s3_url = $tempUrl;
                    $proposicao->pdf_size_bytes = $size;
                    $saved = $proposicao->save();

                    if (!$saved) {
                        throw new \Exception('Falha ao salvar no banco de dados');
                    }
                });

                // Forçar refresh dos dados
                $proposicao->refresh();

                // Dados após a atualização
                $afterUpdate = [
                    'pdf_s3_path' => $proposicao->pdf_s3_path,
                    'pdf_s3_url' => $proposicao->pdf_s3_url,
                    'pdf_size_bytes' => $proposicao->pdf_size_bytes
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Proposição 4 atualizada com PDF S3 correto (com transação)',
                    'data' => [
                        'pdf_s3_path' => $s3Path,
                        'pdf_size_bytes' => $size,
                        'before_update' => $beforeUpdate,
                        'after_update' => $afterUpdate,
                        'persistence_verified' => $proposicao->pdf_s3_path === $s3Path,
                        'new_url_generated' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado na S3',
                    'pdf_s3_path' => $s3Path
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Verificar status da assinatura
     */
    public function verificarStatus(Proposicao $proposicao)
    {
        try {
            $status = [
                'assinada' => false,
                'tipo_certificado' => null,
                'data_assinatura' => null,
                'assinante' => null,
                'pdf_disponivel' => false
            ];

            if ($proposicao->status === 'assinado' && $proposicao->assinatura_digital) {
                $dadosAssinatura = json_decode($proposicao->assinatura_digital, true);
                
                $status['assinada'] = true;
                $status['tipo_certificado'] = $dadosAssinatura['tipo_certificado'] ?? null;
                $status['data_assinatura'] = $proposicao->data_assinatura?->format('d/m/Y H:i:s');
                $status['assinante'] = $dadosAssinatura['nome_assinante'] ?? null;
            }

            if ($proposicao->arquivo_pdf_assinado) {
                $caminhoPDF = storage_path('app/' . $proposicao->arquivo_pdf_assinado);
                $status['pdf_disponivel'] = file_exists($caminhoPDF);
            }

            return response()->json($status);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status da assinatura: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao verificar status'], 500);
        }
    }

    /**
     * Obter caminho do PDF para assinatura
     */
    private function obterCaminhoPDFParaAssinatura(Proposicao $proposicao): ?string
    {
        // CRÍTICO: Verificar se PDF existente está desatualizado comparado ao RTF
        $pdfEncontrado = null;

        // Tentar usar PDF gerado pelo sistema
        if ($proposicao->arquivo_pdf_path) {
            $caminho = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($caminho)) {
                $pdfEncontrado = $caminho;
            }
        }

        // Se não encontrou, tentar usar PDF do diretório de assinatura
        if (!$pdfEncontrado) {
            $diretorioPDFs = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
            if (is_dir($diretorioPDFs)) {
                $pdfs = glob($diretorioPDFs . '/*.pdf');
                if (!empty($pdfs)) {
                    // Retornar o PDF mais recente
                    $pdfEncontrado = array_reduce($pdfs, function($carry, $item) {
                        return (!$carry || filemtime($item) > filemtime($carry)) ? $item : $carry;
                    });
                }
            }
        }

        // VERIFICAÇÃO CRÍTICA: Se PDF existe, verificar se RTF é mais novo
        if ($pdfEncontrado) {
            $pdfModificado = filemtime($pdfEncontrado);

            // Verificar se RTF foi modificado após PDF
            if ($proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
                $caminhoRTF = Storage::path($proposicao->arquivo_path);
                if (file_exists($caminhoRTF)) {
                    $rtfModificado = filemtime($caminhoRTF);

                    if ($rtfModificado > $pdfModificado) {
                        Log::warning('🔴 ASSINATURA: PDF desatualizado detectado - RTF mais novo', [
                            'proposicao_id' => $proposicao->id,
                            'pdf_modificado' => date('Y-m-d H:i:s', $pdfModificado),
                            'rtf_modificado' => date('Y-m-d H:i:s', $rtfModificado),
                            'diferenca_segundos' => $rtfModificado - $pdfModificado
                        ]);

                        // PDF está desatualizado - retornar null para forçar regeneração
                        return null;
                    }
                }
            }

            Log::info('🟢 ASSINATURA: PDF válido encontrado e atualizado', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $pdfEncontrado
            ]);

            return $pdfEncontrado;
        }

        // Tentar usar PDF do OnlyOffice (diretório antigo)
        $diretorioPDFsOnlyOffice = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFsOnlyOffice)) {
            $pdfs = glob($diretorioPDFsOnlyOffice . '/*.pdf');
            if (!empty($pdfs)) {
                $pdfMaisRecente = array_reduce($pdfs, function($carry, $item) {
                    return (!$carry || filemtime($item) > filemtime($carry)) ? $item : $carry;
                });
                return $pdfMaisRecente;
            }
        }

        return null;
    }

    /**
     * Salvar arquivo PFX
     */
    private function salvarArquivoPFX($arquivo, Proposicao $proposicao): string
    {
        $diretorio = storage_path("app/private/certificados/{$proposicao->id}");
        
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $nomeArquivo = 'certificado_' . time() . '.' . $arquivo->getClientOriginalExtension();
        $caminho = $diretorio . '/' . $nomeArquivo;

        $arquivo->move($diretorio, $nomeArquivo);

        return $caminho;
    }

    /**
     * Obter caminho relativo para armazenamento
     */
    private function obterCaminhoRelativo(string $caminhoAbsoluto): string
    {
        $storagePath = storage_path('app/');
        return str_replace($storagePath, '', $caminhoAbsoluto);
    }
    
    /**
     * Gerar identificador único para assinatura
     */
    private function gerarIdentificadorAssinatura(Proposicao $proposicao, $user, string $tipo): string
    {
        return md5($proposicao->id . $user->id . $tipo . time() . uniqid());
    }

    /**
     * Gerar PDF para assinatura automaticamente
     */
    private function gerarPDFParaAssinatura(Proposicao $proposicao): void
    {
        // Se já existe PDF oficial, não regenerar
        if ($proposicao->pdf_oficial_path && Storage::exists($proposicao->pdf_oficial_path)) {
            Log::info('PDF oficial já existe, não regenerando', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $proposicao->pdf_oficial_path
            ]);
            return;
        }

        // Gerar nome único para PDF
        $nomePdf = 'proposicao_' . $proposicao->id . '_assinatura_' . time() . '.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        Log::info('Gerando PDF para assinatura', [
            'proposicao_id' => $proposicao->id,
            'pdf_path' => $caminhoPdfRelativo,
            'arquivo_origem' => $proposicao->arquivo_path
        ]);

        // Verificar se temos arquivo OnlyOffice (RTF/DOCX)
        if ($proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
            $caminhoArquivo = Storage::path($proposicao->arquivo_path);
            $extensao = strtolower(pathinfo($caminhoArquivo, PATHINFO_EXTENSION));
            
            Log::info('Arquivo OnlyOffice encontrado', [
                'arquivo' => $proposicao->arquivo_path,
                'extensao' => $extensao,
                'tamanho' => filesize($caminhoArquivo)
            ]);
            
            // Usar DocumentConversionService para converter corretamente
            try {
                $conversionService = app(\App\Services\DocumentConversionService::class);
                $resultado = $conversionService->convertToPDF(
                    $proposicao->arquivo_path,
                    $caminhoPdfRelativo,
                    $proposicao->status
                );
                
                if ($resultado['success']) {
                    Log::info('PDF gerado com sucesso via DocumentConversionService', [
                        'proposicao_id' => $proposicao->id,
                        'converter' => $resultado['converter'] ?? 'unknown'
                    ]);
                    
                    // Atualizar proposição com caminho do PDF
                    $proposicao->update([
                        'arquivo_pdf_path' => $caminhoPdfRelativo,
                        'pdf_oficial_path' => $caminhoPdfRelativo,
                        'pdf_gerado_em' => now(),
                        'pdf_conversor_usado' => $resultado['converter'] ?? 'libreoffice'
                    ]);
                    
                    return;
                } else {
                    Log::error('Falha na conversão para PDF', [
                        'proposicao_id' => $proposicao->id,
                        'erro' => $resultado['error'] ?? 'desconhecido'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Exceção ao converter para PDF', [
                    'proposicao_id' => $proposicao->id,
                    'erro' => $e->getMessage()
                ]);
            }
        }

        // Fallback: Buscar arquivo DOCX mais recente
        $arquivoDocx = $this->encontrarArquivoDocxMaisRecente($proposicao);
        
        if ($arquivoDocx && file_exists($arquivoDocx)) {
            // Converter DOCX para PDF usando LibreOffice
            $this->converterDocxParaPdf($arquivoDocx, $caminhoPdfAbsoluto);
            
            // Atualizar proposição
            $proposicao->update([
                'arquivo_pdf_path' => $caminhoPdfRelativo,
                'pdf_oficial_path' => $caminhoPdfRelativo,
                'pdf_gerado_em' => now()
            ]);
        } else {
            // Último recurso: Gerar PDF a partir do conteúdo do banco
            $this->gerarPdfDoConteudo($proposicao, $caminhoPdfAbsoluto);
            
            // Atualizar proposição
            $proposicao->update([
                'arquivo_pdf_path' => $caminhoPdfRelativo,
                'pdf_oficial_path' => $caminhoPdfRelativo,
                'pdf_gerado_em' => now()
            ]);
        }
    }

    /**
     * Encontrar arquivo DOCX mais recente da proposição
     */
    private function encontrarArquivoDocxMaisRecente(Proposicao $proposicao): ?string
    {
        // Diretórios para buscar arquivos
        $diretorios = [
            storage_path("app/proposicoes"),
            storage_path("app/private/proposicoes"),
            storage_path("app/public/proposicoes")
        ];

        $arquivos = [];
        
        foreach ($diretorios as $diretorio) {
            if (is_dir($diretorio)) {
                $pattern = $diretorio . "/proposicao_{$proposicao->id}_*.docx";
                $encontrados = glob($pattern);
                foreach ($encontrados as $arquivo) {
                    $arquivos[] = [
                        'path' => $arquivo,
                        'modified' => filemtime($arquivo)
                    ];
                }
            }
        }

        // Verificar arquivo_path do banco
        if ($proposicao->arquivo_path) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_path);
            if (file_exists($caminhoCompleto) && str_ends_with($caminhoCompleto, '.docx')) {
                $arquivos[] = [
                    'path' => $caminhoCompleto,
                    'modified' => filemtime($caminhoCompleto)
                ];
            }
        }

        if (empty($arquivos)) {
            return null;
        }

        // Ordenar por data de modificação (mais recente primeiro)
        usort($arquivos, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $arquivos[0]['path'];
    }

    /**
     * Converter DOCX para PDF usando LibreOffice
     */
    private function converterDocxParaPdf(string $caminhoDocx, string $caminhoPdf): void
    {
        $diretorioDestino = dirname($caminhoPdf);
        
        // Comando LibreOffice para conversão
        $comando = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($diretorioDestino) . " " . escapeshellarg($caminhoDocx) . " 2>&1";
        
        Log::info('Convertendo DOCX para PDF', [
            'comando' => $comando,
            'docx' => $caminhoDocx,
            'pdf_destino' => $caminhoPdf
        ]);

        exec($comando, $output, $returnCode);
        
        if ($returnCode !== 0) {
            Log::error('Erro na conversão DOCX para PDF', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            throw new \Exception('Falha na conversão DOCX para PDF: ' . implode("\n", $output));
        }

        // LibreOffice gera o PDF com o mesmo nome base do DOCX
        $nomeBasePdf = pathinfo($caminhoDocx, PATHINFO_FILENAME) . '.pdf';
        $pdfGerado = $diretorioDestino . '/' . $nomeBasePdf;
        
        // Mover para o nome final desejado
        if (file_exists($pdfGerado) && $pdfGerado !== $caminhoPdf) {
            rename($pdfGerado, $caminhoPdf);
        }
        
        if (!file_exists($caminhoPdf)) {
            throw new \Exception('PDF não foi gerado corretamente');
        }
    }

    /**
     * Gerar PDF a partir do conteúdo do banco
     */
    private function gerarPdfDoConteudo(Proposicao $proposicao, string $caminhoPdf): void
    {
        // Para casos onde não há arquivo DOCX, usar conteúdo do banco
        $conteudo = $proposicao->conteudo ?: 'Conteúdo não disponível';
        
        // Gerar HTML simples
        $html = "<html><body><pre>{$conteudo}</pre></body></html>";
        
        // Salvar como arquivo HTML temporário e converter
        $htmlTemp = tempnam(sys_get_temp_dir(), 'proposicao_') . '.html';
        file_put_contents($htmlTemp, $html);
        
        try {
            $this->converterHtmlParaPdf($htmlTemp, $caminhoPdf);
        } finally {
            unlink($htmlTemp);
        }
    }

    /**
     * Converter HTML para PDF usando LibreOffice
     */
    private function converterHtmlParaPdf(string $caminhoHtml, string $caminhoPdf): void
    {
        $diretorioDestino = dirname($caminhoPdf);
        
        $comando = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($diretorioDestino) . " " . escapeshellarg($caminhoHtml) . " 2>&1";
        
        exec($comando, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Falha na conversão HTML para PDF: ' . implode("\n", $output));
        }

        // LibreOffice gera PDF com nome baseado no HTML
        $nomeBasePdf = pathinfo($caminhoHtml, PATHINFO_FILENAME) . '.pdf';
        $pdfGerado = $diretorioDestino . '/' . $nomeBasePdf;
        
        if (file_exists($pdfGerado) && $pdfGerado !== $caminhoPdf) {
            rename($pdfGerado, $caminhoPdf);
        }
    }

    /**
     * Validar senha do certificado PFX
     */
    private function validarSenhaPFX(string $arquivoPFX, string $senha): bool
    {
        try {
            if (!file_exists($arquivoPFX)) {
                Log::error('Arquivo PFX não encontrado para validação', ['arquivo' => $arquivoPFX]);
                return false;
            }

            // Usar exec com openssl -legacy que funciona com certificados mais antigos
            $command = sprintf(
                'openssl pkcs12 -in %s -passin pass:%s -noout -legacy 2>&1',
                escapeshellarg($arquivoPFX),
                escapeshellarg($senha)
            );
            
            exec($command, $output, $returnCode);
            
            Log::info('Validação de senha PFX via exec', [
                'arquivo' => basename($arquivoPFX),
                'comando' => $command,
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            
            return $returnCode === 0;
            
        } catch (\Exception $e) {
            Log::error('Erro na validação de senha PFX: ' . $e->getMessage(), [
                'arquivo' => basename($arquivoPFX ?? ''),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Processar assinatura digital usando certificado cadastrado no usuário
     */
    private function processarAssinaturaCertificadoCadastrado(Request $request, Proposicao $proposicao, $user)
    {
        Log::info('processarAssinaturaCertificadoCadastrado iniciado', [
            'proposicao_id' => $proposicao->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'request_ajax' => $request->ajax(),
            'request_expects_json' => $request->expectsJson(),
            'usar_certificado_cadastrado' => $request->input('usar_certificado_cadastrado'),
            'senha_certificado_presente' => !empty($request->input('senha_certificado'))
        ]);
        
        try {
            // Verificar se o certificado está válido
            if (!$user->certificadoDigitalValido()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O certificado cadastrado está expirado ou inativo.'
                    ], 422);
                }
                return back()->withErrors(['certificado' => 'O certificado cadastrado está expirado ou inativo.']);
            }
            
            $senhaCertificado = null;
            
            // Obter caminho do certificado primeiro
            $caminhoCompleto = $user->getCaminhoCompletoCertificado();
            
            // Tentar usar senha salva primeiro
            if ($user->certificado_digital_senha_salva && $user->certificado_digital_senha) {
                try {
                    $senhaTestar = $user->getSenhaCertificado();
                    if (!$senhaTestar) {
                        // Senha salva é NULL - remover flag e exigir nova senha
                        $user->removerSenhaCertificado();
                        
                        return response()->json([
                            'success' => false,
                            'code' => 'senha_salva_nula',
                            'message' => 'Sua senha salva foi perdida. Por favor, informe a senha novamente.'
                        ], 422);
                    }
                    
                    // Validar se a senha descriptografada funciona com o certificado
                    $caminhoCompletoCertificado = $user->getCaminhoCompletoCertificado();
                    if (!$this->validarSenhaPFX($caminhoCompletoCertificado, $senhaTestar)) {
                        // Log para debug
                        Log::warning('Senha salva não confere com certificado', [
                            'user_id' => $user->id,
                            'certificado_path' => $caminhoCompletoCertificado
                        ]);
                        
                        // Senha salva não confere - remover e exigir nova
                        $user->removerSenhaCertificado();
                        
                        return response()->json([
                            'success' => false,
                            'code' => 'senha_salva_invalida', 
                            'message' => 'Sua senha salva não confere com o certificado. Por favor, informe a senha correta.'
                        ], 422);
                    }
                    
                    Log::info('Usando senha salva do certificado', ['user_id' => $user->id]);
                    $senhaCertificado = $senhaTestar;
                    
                } catch (\Throwable $e) {
                    // Erro de descriptografia - APP_KEY mudou ou dados corrompidos
                    $user->removerSenhaCertificado();
                    
                    Log::warning('Erro ao descriptografar senha do certificado', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'certificado_path' => $user->certificado_digital_path
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'code' => 'senha_salva_corrompida',
                        'message' => 'Não foi possível recuperar sua senha salva. Por favor, informe a senha novamente.'
                    ], 422);
                }
            }
            
            // Se chegou aqui sem senha, exigir entrada manual
            if (!$senhaCertificado) {
                $senhaInput = (string) $request->input('senha_certificado', '');
                if ($senhaInput === '') {
                    return response()->json([
                        'success' => false,
                        'code' => 'senha_obrigatoria',
                        'message' => 'Por favor, informe a senha do certificado.'
                    ], 422);
                }
                
                // Validar senha fornecida
                $caminhoCompletoCertificado = $user->getCaminhoCompletoCertificado();
                if (!$this->validarSenhaPFX($caminhoCompletoCertificado, $senhaInput)) {
                    return response()->json([
                        'success' => false,
                        'code' => 'senha_invalida',
                        'message' => 'Senha do certificado incorreta.'
                    ], 422);
                }
                
                // Opcionalmente salvar a senha para próximas assinaturas (padrão: sim)
                if ($request->boolean('salvar_senha', true)) {
                    try {
                        $user->salvarSenhaCertificado($senhaInput);
                        Log::info('Senha do certificado salva com sucesso', ['user_id' => $user->id]);
                    } catch (\Exception $e) {
                        Log::warning('Falha ao salvar senha do certificado', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                        // Não falha a assinatura se não conseguir salvar a senha
                    }
                }
                
                $senhaCertificado = $senhaInput;
            }
            
            // Log para debug
            Log::info('Verificando certificado cadastrado', [
                'user_id' => $user->id,
                'certificado_path' => $user->certificado_digital_path,
                'caminho_completo' => $caminhoCompleto,
                'file_exists' => $caminhoCompleto ? file_exists($caminhoCompleto) : false,
                'is_readable' => $caminhoCompleto && file_exists($caminhoCompleto) ? is_readable($caminhoCompleto) : false
            ]);
            
            if (!$caminhoCompleto || !file_exists($caminhoCompleto)) {
                Log::error('Certificado não encontrado', [
                    'caminho_esperado' => $caminhoCompleto,
                    'certificado_path_db' => $user->certificado_digital_path
                ]);
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Arquivo do certificado não encontrado.'
                    ], 422);
                }
                return back()->withErrors(['certificado' => 'Arquivo do certificado não encontrado.']);
            }
            
            
            // Processar assinatura usando o serviço
            $dadosAssinatura = [
                'nome_assinante' => $user->name,
                'email_assinante' => $user->email,
                'tipo_certificado' => 'PFX',
                'ip_assinatura' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'certificado_cn' => $user->certificado_digital_cn,
                'certificado_validade' => $user->certificado_digital_validade
            ];
            
            // Obter PDF para assinatura
            $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
            if (!$pdfPath) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF não encontrado para assinatura.'
                    ], 422);
                }
                return back()->withErrors(['pdf' => 'PDF não encontrado para assinatura.']);
            }
            
            // Adicionar arquivo PFX e senha aos dados da assinatura
            $dadosAssinatura['arquivo_pfx'] = $caminhoCompleto;
            $dadosAssinatura['senha_pfx'] = $senhaCertificado;
            // Adicionar chaves alternativas que o serviço também verifica
            $dadosAssinatura['certificado_path'] = $caminhoCompleto;
            $dadosAssinatura['certificado_senha'] = $senhaCertificado;
            
            // Log para debug dos dados
            Log::info('Dados da assinatura enviados para service', [
                'pfx_path' => $caminhoCompleto,
                'senha_length' => strlen($senhaCertificado ?? ''),
                'dados_assinatura_keys' => array_keys($dadosAssinatura),
                'tipo_certificado' => $dadosAssinatura['tipo_certificado']
            ]);
            
            // Usar serviço de assinatura digital
            $pdfAssinado = $this->assinaturaService->assinarPDF(
                $pdfPath,
                $dadosAssinatura,
                $user
            );
            
            if (!$pdfAssinado) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao processar assinatura digital.'
                    ], 422);
                }
                return back()->withErrors(['assinatura' => 'Erro ao processar assinatura digital.']);
            }
            
            // Gerar identificador da assinatura
            $identificador = $this->gerarIdentificadorAssinatura($proposicao, $user, 'PFX_CADASTRADO');
            
            // Dados compactos para o banco
            $dadosCompactos = [
                'id' => $identificador,
                'tipo' => 'PFX_CADASTRADO', // Para identificar que foi usado certificado cadastrado
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
                'arquivo_pdf_assinado' => $this->obterCaminhoRelativo($pdfAssinado)
            ]);
            
            Log::info('Proposição assinada com certificado cadastrado', [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => $user->id,
                'certificado_cn' => $user->certificado_digital_cn,
                'senha_salva' => $user->certificado_digital_senha_salva
            ]);
            
            // Retornar JSON para requisições AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proposição assinada digitalmente com sucesso usando certificado cadastrado!',
                    'redirect' => route('proposicoes.show', $proposicao)
                ]);
            }
            
            return redirect()->route('proposicoes.show', $proposicao)
                ->with('success', 'Proposição assinada digitalmente com sucesso usando certificado cadastrado!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao processar assinatura com certificado cadastrado: ' . $e->getMessage(), [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retornar JSON para requisições AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar assinatura: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['assinatura' => 'Erro ao processar assinatura: ' . $e->getMessage()]);
        }
    }
}
