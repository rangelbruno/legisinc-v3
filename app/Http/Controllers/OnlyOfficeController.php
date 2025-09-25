<?php

namespace App\Http\Controllers;

use App\Models\DocumentWorkflowLog;
use App\Models\Proposicao;
use App\Models\TipoProposicao;
use App\Services\OnlyOffice\OnlyOfficeConversionService;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateUniversalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnlyOfficeController extends Controller
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService,
        private TemplateUniversalService $templateUniversalService,
        private OnlyOfficeConversionService $conversionService
    ) {}

    /**
     * Abrir proposição no editor OnlyOffice para Legislativo
     */
    public function editorLegislativo(Proposicao $proposicao)
    {
        // 📝 LOG: Acesso ao editor OnlyOffice pelo Legislativo
        \App\Helpers\ComprehensiveLogger::userClick('Legislativo acessou editor OnlyOffice', [
            'acao' => 'abrir_editor_onlyoffice',
            'user_type' => 'legislativo',
            'proposicao_id' => $proposicao->id,
            'proposicao_status' => $proposicao->status,
            'proposicao_tipo' => $proposicao->tipo,
            'proposicao_ementa' => $proposicao->ementa,
            'autor_id' => $proposicao->autor_id,
            'workflow_stage' => 'edicao_legislativo',
            'onlyoffice_interaction' => [
                'editor_type' => 'legislativo',
                'document_key_generated' => true,
                'callback_configured' => true,
                'arquivo_existente' => !empty($proposicao->arquivo_path),
                'template_id' => $proposicao->template_id
            ]
        ]);

        // Verificar permissões
        $user = Auth::user();
        
        if (!$user->isLegislativo()) {
            abort(403, 'Acesso negado. Apenas usuários do Legislativo podem editar proposições.');
        }

        // Verificar se a proposição está em status editável pelo Legislativo
        $statusEditaveis = ['enviado_legislativo', 'em_revisao', 'devolvido_correcao', 'protocolado', 'em_analise', 'em_edicao'];
        if (!in_array($proposicao->status, $statusEditaveis)) {
            return redirect()->route('proposicoes.legislativo.index')
                ->with('error', 'Esta proposição não está disponível para edição. Status atual: ' . $proposicao->status);
        }

        // OTIMIZAÇÃO: Carregar relacionamentos de forma mais eficiente
        if (!$proposicao->relationLoaded('autor')) {
            $proposicao->load('autor');
        }
        if (!$proposicao->relationLoaded('template') && $proposicao->template_id) {
            $proposicao->load('template');
        }
        
        // PRIORIDADE PARA LEGISLATIVO: Verificar se já existe arquivo salvo
        // O Legislativo deve editar o arquivo já criado pelo Parlamentar, não usar template
        $temArquivoSalvo = !empty($proposicao->arquivo_path) && 
                          (Storage::disk('local')->exists($proposicao->arquivo_path) || 
                           Storage::disk('public')->exists($proposicao->arquivo_path) ||
                           file_exists(storage_path('app/' . $proposicao->arquivo_path)));
        
        if ($temArquivoSalvo) {
            // Log temporariamente desabilitado
            // Log::info('OnlyOffice Editor Legislativo: Usando arquivo salvo existente', ...);
            
            // Usar configuração padrão que carrega o arquivo salvo (RÁPIDO)
            $config = $this->generateOnlyOfficeConfig($proposicao);
        } else {
            // Se não tem arquivo salvo, usar template como fallback
            if (!$proposicao->relationLoaded('tipoProposicao')) {
                $proposicao->load('tipoProposicao');
            }
            
            $tipoProposicao = $proposicao->tipoProposicao;
            
            // Se a relação não funcionou, buscar por nome
            if (!$tipoProposicao && $proposicao->tipo) {
                $tipoProposicao = TipoProposicao::where('nome', $proposicao->tipo)->first();
            }
            
            $deveUsarUniversal = $tipoProposicao 
                ? $this->templateUniversalService->deveUsarTemplateUniversal($tipoProposicao)
                : false;
            
            if ($deveUsarUniversal) {
                // Log temporariamente desabilitado
                // Log::info('OnlyOffice Editor Legislativo: Usando template universal', ...);
                
                $config = $this->generateOnlyOfficeConfigWithUniversalTemplate($proposicao, 'legislativo');
            } else if ($proposicao->template_id && $proposicao->template) {
                // Log temporariamente desabilitado
                // Log::info('OnlyOffice Editor Legislativo: Usando template específico', ...);
                
                $config = $this->onlyOfficeService->gerarConfiguracaoEditor(
                    $proposicao->template,
                    $proposicao,
                    'proposicao',
                    $proposicao->id
                );
            } else {
                Log::info('OnlyOffice Editor Legislativo: Usando fallback básico', [
                    'proposicao_id' => $proposicao->id
                ]);
                
                $config = $this->generateOnlyOfficeConfig($proposicao);
            }
        }

        return view('proposicoes.legislativo.onlyoffice-editor', compact('proposicao', 'config'));
    }

    /**
     * Gerar configuração para o OnlyOffice
     */
    private function generateOnlyOfficeConfig(Proposicao $proposicao)
    {
        // ✅ LÓGICA INTELIGENTE DE DOCUMENT_KEY (baseada no Template Universal)
        $documentKey = $this->generateIntelligentDocumentKey($proposicao);
        
        $lastModified = $proposicao->ultima_modificacao ? 
                       $proposicao->ultima_modificacao->timestamp : 
                       $proposicao->updated_at->timestamp;

        // OTIMIZAÇÃO: Token mais eficiente
        $version = $lastModified;
        $token = base64_encode($proposicao->id . '|' . $lastModified); // Usar lastModified em vez de time()
        $documentUrl = route('proposicoes.onlyoffice.download', [
            'id' => $proposicao->id,
            'token' => $token,
            'v' => $version,
            '_' => $lastModified // Cache buster baseado em modificação, não time atual
        ]);
        
        // Se estiver em ambiente local/docker, ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            // Usar nome do container da aplicação (porta 80 interna)
            $documentUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $documentUrl);
        }

        // URL de callback para salvar alterações - usando callback específico do legislativo
        $callbackUrl = route('api.onlyoffice.callback.legislativo', [
            'proposicao' => $proposicao,
            'documentKey' => $documentKey
        ]);
        
        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }

        // Configuração padrão para documentos de texto
        $fileType = 'rtf';
        $documentType = 'word'; // SEMPRE 'word' para RTF/DOCX/DOC
        
        Log::info('DEBUG fileType detection - START', [
            'proposicao_id' => $proposicao->id,
            'arquivo_path' => $proposicao->arquivo_path,
            'template_id' => $proposicao->template_id,
            'has_conteudo' => !empty($proposicao->conteudo),
            'template_exists' => $proposicao->template ? true : false
        ]);
        
        // Priorizar arquivo existente da proposição
        if ($proposicao->arquivo_path) {
            Log::info('DEBUG - Usando arquivo_path', ['arquivo_path' => $proposicao->arquivo_path]);
            if (str_ends_with(strtolower($proposicao->arquivo_path), '.rtf')) {
                $fileType = 'rtf';
            } elseif (str_ends_with(strtolower($proposicao->arquivo_path), '.docx')) {
                $fileType = 'docx';
            } elseif (str_ends_with(strtolower($proposicao->arquivo_path), '.doc')) {
                $fileType = 'doc';
            }
        } elseif ($proposicao->template_id && is_numeric($proposicao->template_id) && $proposicao->template && $proposicao->template->arquivo_path) {
            Log::info('DEBUG - Usando template arquivo_path', ['template_path' => $proposicao->template->arquivo_path]);
            // Se não tem arquivo próprio, verificar template (só se template_id for numérico e válido)
            if (str_ends_with(strtolower($proposicao->template->arquivo_path), '.rtf')) {
                $fileType = 'rtf';
            } elseif (str_ends_with(strtolower($proposicao->template->arquivo_path), '.docx')) {
                $fileType = 'docx';
            }
        } else {
            Log::info('DEBUG - Usando lógica fallback', [
                'has_template_id' => !empty($proposicao->template_id),
                'has_conteudo' => !empty($proposicao->conteudo)
            ]);
            // Se tem template_id mas não é numérico válido, usar RTF (templates dinâmicos)
            if ($proposicao->template_id) {
                $fileType = 'rtf';
                Log::info('DEBUG - Definido como RTF por template_id');
            }
            // Se há conteúdo IA e sem template específico, usar RTF (arquivo dinâmico gerado)
            if (!empty($proposicao->conteudo) && $proposicao->template_id === null) {
                $fileType = 'rtf';
                Log::info('DEBUG - Definido como RTF por conteúdo IA (arquivo dinâmico)');
            }
        }
        
        Log::info('DEBUG fileType detection - FINAL', ['fileType' => $fileType]);
        
        Log::info('OnlyOffice file type detection', [
            'proposicao_id' => $proposicao->id,
            'detected_file_type' => $fileType,
            'arquivo_path' => $proposicao->arquivo_path,
            'template_id' => $proposicao->template_id,
            'template_path' => ($proposicao->template_id && is_numeric($proposicao->template_id) && $proposicao->template) ? $proposicao->template->arquivo_path : null,
            'has_ai_content' => !empty($proposicao->conteudo)
        ]);

        $config = [
            'type' => 'desktop',
            'documentType' => $documentType,
            'document' => [
                'fileType' => $fileType,
                'key' => $documentKey,
                'title' => "Proposição {$proposicao->tipo} - {$proposicao->id}",
                'url' => $documentUrl,
                'permissions' => [
                    'edit' => true,
                    'download' => true,
                    'print' => true,
                    'review' => true,
                    'comment' => true
                ]
            ],
            'editorConfig' => [
                'callbackUrl' => $callbackUrl,
                'lang' => 'pt-BR',
                'region' => 'pt-BR',
                'documentLang' => 'pt-BR',
                'mode' => 'edit',
                'user' => [
                    'id' => (string) Auth::id(),
                    'name' => Auth::user()->name
                ],
                'customization' => [
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => ['pt-BR'],
                    ],
                    'documentLanguage' => 'pt-BR',
                    'autosave' => true,
                    'autosaveTimeout' => 30000, // 30 segundos
                    'chat' => false,
                    'comments' => true,
                    'compactHeader' => false,
                    'compactToolbar' => false,
                    'feedback' => false,
                    'forcesave' => true,
                    'help' => true,
                    'hideRightMenu' => false,
                    'toolbarNoTabs' => false,
                    'goback' => [
                        'text' => 'Voltar para Proposição',
                        'url' => route('proposicoes.show', $proposicao)
                    ]
                ]
            ],
            'height' => '100%',
            'width' => '100%'
        ];
        
        // Log da configuração para debug
        Log::info('OnlyOffice Config Generated - Legislativo', [
            'proposicao_id' => $proposicao->id,
            'document_key' => $documentKey,
            'document_url' => $documentUrl,
            'callback_url' => $callbackUrl,
            'file_type' => $fileType
        ]);

        return $config;
    }

    /**
     * Download do documento para o OnlyOffice por ID (sem model binding)
     */
    public function downloadById(Request $request, $id)
    {
        // 🐳 LOG: Container OnlyOffice solicita download de documento
        \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container solicitou download de documento', [
            'proposicao_id' => $id,
            'download_request' => [
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'from_container' => str_contains($request->ip(), '172.') || $request->ip() === 'onlyoffice',
                'headers' => $request->headers->all(),
                'parameters' => $request->all(),
                'is_auth_request' => $request->hasHeader('Authorization'),
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ],
            'workflow_stage' => 'download_documento'
        ]);

        // INTEGRAÇÃO: Priorizar arquivo salvo, depois Template Universal (conforme CLAUDE.md)
        try {
            // Buscar proposição sem falhar por problemas de conexão
            $proposicao = Proposicao::with(['tipoProposicao', 'autor'])->find($id);
            
            if (!$proposicao) {
                throw new \Exception("Proposição não encontrada: {$id}");
            }
            
            // NOVA LÓGICA: Verificar se existe arquivo salvo PRIMEIRO
            if ($proposicao->arquivo_path) {
                $caminhosPossiveis = [
                    storage_path('app/' . $proposicao->arquivo_path),
                    storage_path('app/private/' . $proposicao->arquivo_path),
                    storage_path('app/local/' . $proposicao->arquivo_path),
                ];

                foreach ($caminhosPossiveis as $caminho) {
                    if (file_exists($caminho)) {
                        // 🐳 LOG: Container encontrou arquivo salvo para download
                        \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container baixou arquivo RTF salvo', [
                            'proposicao_id' => $id,
                            'arquivo_info' => [
                                'arquivo_path' => $proposicao->arquivo_path,
                                'caminho_completo' => $caminho,
                                'tamanho_arquivo' => filesize($caminho),
                                'modificado_em' => date('Y-m-d H:i:s', filemtime($caminho)),
                                'tipo_arquivo' => 'arquivo_salvo_existente'
                            ],
                            'workflow_stage' => 'download_arquivo_salvo'
                        ]);
                        
                        // ✅ HEADERS ANTI-CACHE AGRESSIVOS (baseados no Template Universal)
                        $etag = md5(file_get_contents($caminho) . filemtime($caminho));
                        $lastModified = gmdate('D, d M Y H:i:s', filemtime($caminho)) . ' GMT';
                        
                        return response()->download($caminho, "proposicao_{$id}.rtf", [
                            'Content-Type' => 'application/rtf; charset=UTF-8',
                            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0, private',
                            'Pragma' => 'no-cache',
                            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
                            'Last-Modified' => $lastModified,
                            'ETag' => '"' . $etag . '"',
                            'X-Content-Type-Options' => 'nosniff',
                            'X-Frame-Options' => 'SAMEORIGIN',
                            'Vary' => 'Accept-Encoding',
                            // Forçar OnlyOffice a sempre baixar nova versão
                            'X-OnlyOffice-Force-Refresh' => 'true',
                        ]);
                    }
                }
                
                Log::warning('Arquivo salvo não encontrado, usando template universal', [
                    'proposicao_id' => $id,
                    'arquivo_path' => $proposicao->arquivo_path,
                    'caminhos_testados' => $caminhosPossiveis
                ]);
            }
            
            // Usar TemplateUniversalService para determinar se deve usar template universal
            $tipoProposicao = $proposicao->tipoProposicao;
            
            // Se a relação não funcionou, buscar por nome
            if (!$tipoProposicao && $proposicao->tipo) {
                $tipoProposicao = TipoProposicao::where('nome', $proposicao->tipo)->first();
            }
            
            $deveUsarUniversal = $tipoProposicao 
                ? $this->templateUniversalService->deveUsarTemplateUniversal($tipoProposicao)
                : false;
            
            if ($deveUsarUniversal) {
                // 🐳 LOG: Container recebendo template universal
                \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container baixou template universal gerado', [
                    'proposicao_id' => $id,
                    'template_info' => [
                        'tipo_template' => 'template_universal',
                        'tipo_proposicao' => $tipoProposicao ? $tipoProposicao->nome : $proposicao->tipo,
                        'servico_usado' => 'TemplateUniversalService'
                    ],
                    'workflow_stage' => 'download_template_universal'
                ]);

                // Usar TemplateUniversalService para aplicar template
                $rtfContent = $this->templateUniversalService->aplicarTemplateParaProposicao($proposicao);
            } else {
                // 🐳 LOG: Container recebendo RTF básico/específico
                \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container baixou RTF básico/específico', [
                    'proposicao_id' => $id,
                    'template_info' => [
                        'tipo_template' => 'rtf_basico_especifico',
                        'template_id' => $proposicao->template_id,
                        'fallback_usado' => true
                    ],
                    'workflow_stage' => 'download_rtf_basico'
                ]);

                // Fallback para RTF básico
                $rtfContent = $this->gerarRTFTemplateUniversal($id);
            }
            
            $tempFile = tempnam(sys_get_temp_dir(), 'template_universal_') . '.rtf';
            file_put_contents($tempFile, $rtfContent);
            
            // ✅ HEADERS ANTI-CACHE AGRESSIVOS
            $etag = md5($rtfContent . time());
            $lastModified = gmdate('D, d M Y H:i:s') . ' GMT';
            
            return response()->download($tempFile, "proposicao_{$id}.rtf", [
                'Content-Type' => 'application/rtf; charset=UTF-8',
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0, private',
                'Pragma' => 'no-cache',
                'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
                'Last-Modified' => $lastModified,
                'ETag' => '"' . $etag . '"',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Vary' => 'Accept-Encoding',
                'X-OnlyOffice-Force-Refresh' => 'true',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar RTF universal', [
                'proposicao_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback mínimo
            $rtfFallback = $this->gerarRTFSimples($id);
            $tempFile = tempnam(sys_get_temp_dir(), 'fallback_') . '.rtf';
            file_put_contents($tempFile, $rtfFallback);
            
            return response()->download($tempFile, "fallback_{$id}.rtf", [
                'Content-Type' => 'application/rtf; charset=UTF-8'
            ])->deleteFileAfterSend(true);
        }
    }

    /**
     * Gerar RTF com template universal simulado
     */
    private function gerarRTFTemplateUniversal($id)
    {
        $dadosCamara = [
            'nome' => 'CÂMARA MUNICIPAL DE CARAGUATATUBA',
            'endereco' => 'Praça da República, 40, Centro',
            'telefone' => '(12) 3882-5588',
            'website' => 'www.camaracaraguatatuba.sp.gov.br',
            'municipio' => 'Caraguatatuba'
        ];

        $data = now();
        $numeroProposicao = "[AGUARDANDO PROTOCOLO]";

        return '{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;\red255\green255\blue255;}
{\*\generator Laravel Template Universal}
\viewkind4\uc1
\pard\sa200\sl276\slmult1\qc\f0\fs24\b ' . $dadosCamara['nome'] . '\b0\par
\qc ' . $dadosCamara['endereco'] . '\par
\qc ' . $dadosCamara['telefone'] . '\par
\qc ' . $dadosCamara['website'] . '\par
\par\par
\qc\fs28\b MOÇÃO Nº ' . $numeroProposicao . '\b0\fs24\par
\par\par
\ql\b EMENTA:\b0 [Ementa da proposição será definida pelo parlamentar]\par
\par
A Câmara Municipal manifesta:\par
\par
[Texto da proposição será criado pelo parlamentar usando este template universal.]\par
\par
[Este documento foi gerado automaticamente com o Template Universal do Sistema Legisinc.]\par
\par
[Justificativa se houver]\par
\par
Resolve dirigir a presente Moção.\par
\par\par
\qr ' . $dadosCamara['municipio'] . ', ' . $data->format('d') . ' de ' . $this->obterMesPortugues($data->month) . ' de ' . $data->year . '.\par
\par\par
\qc __________________________________\par
\qc [Nome do Parlamentar]\par
\qc Vereador(a)\par
}';
    }

    /**
     * Gerar RTF simples para fallback
     */
    private function gerarRTFSimples($id)
    {
        return '{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Times New Roman;}}
\viewkind4\uc1
\pard\sa200\sl276\slmult1\qc\f0\fs24\b DOCUMENTO BÁSICO\b0\par
\ql Proposição ID: ' . $id . '\par
Data: ' . now()->format('d/m/Y H:i:s') . '\par
\par
Este é um documento básico gerado pelo sistema.\par
}';
    }

    /**
     * Obter mês em português
     */
    private function obterMesPortugues($mes)
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];
        return $meses[$mes] ?? 'janeiro';
    }

    /**
     * Download do documento para o OnlyOffice
     */
    public function download(Request $request, Proposicao $proposicao)
    {
        // 🐳 LOG: Container OnlyOffice solicita download de documento específico
        \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container solicitou download de proposição específica', [
            'proposicao_id' => $proposicao->id,
            'download_request' => [
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'has_token' => $request->has('token'),
                'authenticated' => Auth::check() ? Auth::id() : 'not_authenticated',
                'arquivo_path' => $proposicao->arquivo_path,
                'proposicao_status' => $proposicao->status
            ],
            'workflow_stage' => 'download_proposicao_especifica'
        ]);
        
        // Verificar token para acesso sem autenticação (OnlyOffice)
        $hasValidToken = $request->has('token');
        
        // Permitir acesso sem autenticação se for o OnlyOffice com token
        $userAgent = $request->header('User-Agent', '');
        $isOnlyOffice = str_contains($userAgent, 'ASC.DocService') || 
                       str_contains($userAgent, 'ONLYOFFICE') ||
                       $request->ip() === 'onlyoffice' ||
                       str_contains($request->ip(), '172.') || // Docker network
                       $hasValidToken;
        
        // Verificar permissões apenas se não for o OnlyOffice
        if (!$isOnlyOffice && Auth::check()) {
            $user = Auth::user();
            
            // Permitir acesso se:
            // 1. É usuário do Legislativo (pode editar todas as proposições)
            // 2. É o autor da proposição
            if (!$user->isLegislativo() && $proposicao->autor_id !== $user->id) {
                abort(403, 'Acesso negado. Você só pode editar suas próprias proposições.');
            }
        }

        // Usar o serviço para gerar o documento - com fallback em caso de erro
        try {
            // 🐳 LOG: Container usando OnlyOfficeService para gerar documento
            \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container processando documento via OnlyOfficeService', [
                'proposicao_id' => $proposicao->id,
                'servico_usado' => 'OnlyOfficeService::gerarDocumentoProposicao',
                'workflow_stage' => 'geracao_documento_servico'
            ]);

            return $this->onlyOfficeService->gerarDocumentoProposicao($proposicao);
        } catch (\Exception $e) {
            // 🐳 LOG: Erro no serviço, usando fallback
            \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Erro no OnlyOfficeService, usando fallback RTF', [
                'proposicao_id' => $proposicao->id,
                'error_details' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ],
                'fallback_usado' => true,
                'workflow_stage' => 'fallback_documento'
            ]);
            
            // Fallback: criar documento RTF simples direto
            $rtfContent = '{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Times New Roman;}}
{\*\generator Riched20 10.0.19041}\viewkind4\uc1 
\pard\sa200\sl276\slmult1\qc\f0\fs24\b PROPOSIÇÃO\b0\par
\ql ID: ' . $proposicao->id . '\par
Tipo: ' . ($proposicao->tipo ?? 'Não definido') . '\par
Status: ' . ($proposicao->status ?? 'Em edição') . '\par
Data: ' . now()->format('d/m/Y H:i:s') . '\par
\par
Documento gerado automaticamente devido a erro no sistema.\par
Por favor, contacte o administrador.\par
}';
            
            $tempFile = tempnam(sys_get_temp_dir(), 'fallback_prop_') . '.rtf';
            file_put_contents($tempFile, $rtfContent);
            
            return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf", [
                'Content-Type' => 'application/rtf',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache'
            ])->deleteFileAfterSend(true);
        }
    }

    /**
     * Debug endpoint para testar geração de documento
     */
    public function debugDownload($id)
    {
        try {
            // Teste super simples sem consulta ao banco
            $rtfContent = '{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Times New Roman;}}
{\*\generator Riched20 10.0.19041}\viewkind4\uc1 
\pard\sa200\sl276\slmult1\qc\f0\fs24\b TESTE DE DOCUMENTO RTF SIMPLES\b0\par
\ql Proposicao ID: ' . $id . '\par
Status: Testando\par
Data: ' . date('d/m/Y H:i:s') . '\par
Sistema funcionando!\par
}';
            
            $tempFile = tempnam(sys_get_temp_dir(), 'debug_simple_') . '.rtf';
            file_put_contents($tempFile, $rtfContent);
            
            return response()->download($tempFile, "debug_simple_{$id}.rtf", [
                'Content-Type' => 'application/rtf'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Callback do ONLYOFFICE
     */
    public function callback(Request $request, Proposicao $proposicao, string $documentKey)
    {
        $data = $request->all();

        // 🐳 LOG: Interação detalhada com container OnlyOffice
        \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Callback recebido do container OnlyOffice', [
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
            'proposicao_id' => $proposicao->id,
            'document_key' => $documentKey,
            'callback_status' => $data['status'] ?? null,
            'container_info' => [
                'server_url' => config('onlyoffice.server_url'),
                'internal_url' => config('onlyoffice.internal_url'),
                'callback_from_ip' => $request->ip(),
                'callback_headers' => $request->headers->all(),
                'callback_method' => $request->method(),
                'callback_url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent'),
                'content_type' => $request->header('Content-Type'),
            ],
            'document_info' => [
                'proposicao_status' => $proposicao->status,
                'arquivo_path' => $proposicao->arquivo_path,
                'pdf_path' => $proposicao->arquivo_pdf_path,
                'onlyoffice_key' => $proposicao->onlyoffice_key,
                'ultima_modificacao' => $proposicao->updated_at,
                'rtf_exists' => !empty($proposicao->arquivo_path) && Storage::exists($proposicao->arquivo_path),
                'rtf_size' => !empty($proposicao->arquivo_path) && Storage::exists($proposicao->arquivo_path)
                    ? Storage::size($proposicao->arquivo_path) : 0,
            ],
            'callback_data' => $data,
            'status_meaning' => $this->getStatusMeaning($data['status'] ?? 0)
        ]);

        try {
            // Status 2 = documento salvo e pronto para download
            // Status 6 = force save (força salvamento)
            if (isset($data['status']) && in_array($data['status'], [2, 6])) {
                $statusName = $data['status'] == 6 ? 'Force Save' : 'Document Ready';
                Log::info("🔄 ONLYOFFICE CONTAINER: Iniciando processamento de salvamento (Status {$data['status']} - {$statusName})", [
                    'proposicao_id' => $proposicao->id,
                    'document_key' => $documentKey,
                    'url_download' => $data['url'] ?? 'N/A',
                    'changesurl' => $data['changesurl'] ?? 'N/A',
                    'history' => $data['history'] ?? 'N/A',
                    'users' => $data['users'] ?? 'N/A'
                ]);

                $callbackStart = microtime(true);
                $resultado = $this->onlyOfficeService->processarCallbackProposicao($proposicao, $documentKey, $data);
                $callbackTime = microtime(true) - $callbackStart;

                // ✅ LIMPAR CACHE após salvamento para forçar refresh
                $this->clearProposicaoCache($proposicao);

                // 🔔 Marcar callback de force save se aplicável
                if ($data['status'] == 6) {
                    $this->conversionService->markForceSaveCallbackReceived($proposicao, $data);
                    Log::info('🔔 FORCE SAVE CALLBACK: Marcado no cache para conversão PDF', [
                        'proposicao_id' => $proposicao->id,
                        'callback_status' => $data['status'],
                        'document_key' => $documentKey
                    ]);
                }

                Log::info('✅ ONLYOFFICE CONTAINER: Processamento de salvamento concluído', [
                    'proposicao_id' => $proposicao->id,
                    'callback_time_seconds' => round($callbackTime, 2),
                    'success' => !isset($resultado['error']) || $resultado['error'] == 0,
                    'resultado' => $resultado,
                    'container_response_to_laravel' => [
                        'error_code' => $resultado['error'] ?? 'unknown',
                        'file_downloaded' => isset($data['url']),
                        'file_processed' => $callbackTime < 30, // Considera sucesso se processou em menos de 30s
                        'cache_cleared' => true
                    ]
                ]);
            } else {
                Log::info('🔄 ONLYOFFICE CONTAINER: Status não requer processamento', [
                    'proposicao_id' => $proposicao->id,
                    'status' => $data['status'] ?? 'unknown',
                    'status_meaning' => $this->getStatusMeaning($data['status'] ?? 0),
                    'action_taken' => 'none'
                ]);
                $resultado = ['error' => 0];
            }
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('OnlyOffice callback error', [
                'proposicao_id' => $proposicao->id,
                'document_key' => $documentKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 1]);
        }
    }

    /**
     * Force save document - método simplificado para forçar salvamento
     */
    public function forceSave(Request $request, Proposicao $proposicao)
    {
        try {
            // 🐳 LOG: Container OnlyOffice força salvamento
            \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Container solicitou force save', [
                'proposicao_id' => $proposicao->id,
                'force_save_request' => [
                    'document_key' => $request->input('document_key'),
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'timestamp' => now()->format('Y-m-d H:i:s.u')
                ],
                'proposicao_info' => [
                    'status_antes' => $proposicao->status,
                    'arquivo_path' => $proposicao->arquivo_path,
                    'updated_at_antes' => $proposicao->updated_at
                ],
                'workflow_stage' => 'force_save'
            ]);

            // Marcar a proposição como salva recentemente
            $proposicao->touch(); // Atualiza updated_at

            return response()->json([
                'success' => true,
                'message' => 'Salvamento forçado iniciado',
                'proposicao_id' => $proposicao->id
            ]);
        } catch (\Exception $e) {
            // 🐳 LOG: Erro no force save
            \App\Helpers\ComprehensiveLogger::onlyOfficeContainer('Erro no force save', [
                'proposicao_id' => $proposicao->id,
                'error_details' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ],
                'workflow_stage' => 'force_save_erro'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao forçar salvamento'
            ], 500);
        }
    }

    /**
     * Editor OnlyOffice para Parlamentares editarem suas próprias proposições
     */
    public function editorParlamentar(Proposicao $proposicao, Request $request)
    {
        // 📝 LOG: Acesso ao editor OnlyOffice pelo Parlamentar
        \App\Helpers\ComprehensiveLogger::userClick('Parlamentar acessou editor OnlyOffice', [
            'acao' => 'abrir_editor_onlyoffice',
            'user_type' => 'parlamentar',
            'proposicao_id' => $proposicao->id,
            'proposicao_status' => $proposicao->status,
            'proposicao_tipo' => $proposicao->tipo,
            'proposicao_ementa' => $proposicao->ementa,
            'workflow_stage' => 'edicao_parlamentar',
            'parametros_edicao' => [
                'ai_content' => $request->has('ai_content'),
                'manual_content' => $request->has('manual_content'),
                'conteudo_length' => strlen($proposicao->conteudo ?? ''),
                'tem_conteudo' => !empty($proposicao->conteudo),
                'eh_autor' => $proposicao->autor_id === Auth::id()
            ],
            'onlyoffice_interaction' => [
                'editor_type' => 'parlamentar',
                'arquivo_existente' => !empty($proposicao->arquivo_path),
                'template_id' => $proposicao->template_id,
                'forcar_regeneracao' => $request->has('ai_content') || $request->has('manual_content')
            ]
        ]);
        
        $user = Auth::user();
        
        // Limpar template_id inválido se existir
        if ($proposicao->template_id && !is_numeric($proposicao->template_id)) {
            Log::info('Limpando template_id inválido', [
                'proposicao_id' => $proposicao->id,
                'template_id_invalido' => $proposicao->template_id
            ]);
            
            $proposicao->update(['template_id' => null]);
        }
        
        // Verificar se o usuário é o autor da proposição
        if ($proposicao->autor_id !== $user->id) {
            abort(403, 'Acesso negado. Você só pode editar suas próprias proposições.');
        }

        // Verificar se a proposição está em status editável pelo autor
        $statusEditaveis = ['rascunho', 'em_edicao', 'salvando', 'devolvido_edicao', 'retornado_legislativo'];
        if (!in_array($proposicao->status, $statusEditaveis)) {
            return redirect()->route('proposicoes.minhas-proposicoes')
                ->with('error', 'Esta proposição não está disponível para edição no momento.');
        }

        // OTIMIZAÇÃO: Carregar relacionamentos de forma mais eficiente
        if (!$proposicao->relationLoaded('autor')) {
            $proposicao->load('autor');
        }
        if (!$proposicao->relationLoaded('template') && $proposicao->template_id) {
            $proposicao->load('template');
        }
        
        // Verificar se já existe arquivo salvo do OnlyOffice
        $temArquivoSalvo = !empty($proposicao->arquivo_path) && 
                          Storage::disk('local')->exists($proposicao->arquivo_path);
        
        // SEMPRE usar template universal - não forçar regeneração baseada em conteúdo
        // Só forçar regeneração se explicitamente solicitado via parâmetros
        $forcarRegeneracao = ($request->has('ai_content') || $request->has('manual_content')) && !$temArquivoSalvo;
                           
        if ($forcarRegeneracao) {
            // Limpar arquivo_path para forçar regeneração com conteúdo personalizado
            $proposicao->update([
                'status' => 'em_edicao',
                'arquivo_path' => null
            ]);
            
            Log::info('Forçando regeneração para conteúdo personalizado', [
                'proposicao_id' => $proposicao->id,
                'ai_content_param' => $request->has('ai_content'),
                'manual_content_param' => $request->has('manual_content'),
                'tem_arquivo_salvo' => $temArquivoSalvo,
                'template_id' => $proposicao->template_id,
                'arquivo_path_anterior' => $proposicao->arquivo_path
            ]);
        } else {
            Log::info('Não forçando regeneração - usando arquivo salvo ou template', [
                'proposicao_id' => $proposicao->id,
                'conteudo_atual' => substr($proposicao->conteudo ?? '', 0, 100),
                'eh_placeholder' => $proposicao->conteudo === 'Conteúdo a ser definido',
                'template_id' => $proposicao->template_id,
                'arquivo_path_existente' => $proposicao->arquivo_path,
                'tem_arquivo_salvo' => $temArquivoSalvo
            ]);
        }
        
        // NOVA LÓGICA: Integração com Template Universal (conforme CLAUDE.md)
        // Carregar tipo de proposição se não estiver carregado
        if (!$proposicao->relationLoaded('tipoProposicao')) {
            $proposicao->load('tipoProposicao');
        }
        
        // Verificar se deve usar template universal
        $tipoProposicao = $proposicao->tipoProposicao;
        
        // Se a relação não funcionou, buscar por nome
        if (!$tipoProposicao && $proposicao->tipo) {
            $tipoProposicao = TipoProposicao::where('nome', $proposicao->tipo)->first();
        }
        
        // NOVA LÓGICA: Verificar arquivo salvo PRIMEIRO, antes de template universal
        $temArquivoSalvo = false;
        if ($proposicao->arquivo_path) {
            $caminhosPossiveis = [
                storage_path('app/' . $proposicao->arquivo_path),
                storage_path('app/private/' . $proposicao->arquivo_path),
                storage_path('app/local/' . $proposicao->arquivo_path),
            ];
            
            foreach ($caminhosPossiveis as $caminho) {
                if (file_exists($caminho)) {
                    $temArquivoSalvo = true;
                    Log::info('OnlyOffice Editor: Arquivo salvo encontrado, priorizando sobre template', [
                        'proposicao_id' => $proposicao->id,
                        'arquivo_path' => $proposicao->arquivo_path,
                        'caminho_completo' => $caminho,
                        'tamanho_arquivo' => filesize($caminho)
                    ]);
                    break;
                }
            }
        }
        
        if ($temArquivoSalvo) {
            // PRIORIDADE 1: Usar arquivo salvo existente
            $config = $this->generateOnlyOfficeConfigWithUniversalTemplate($proposicao, 'parlamentar');
        } else {
            $deveUsarUniversal = $tipoProposicao 
                ? $this->templateUniversalService->deveUsarTemplateUniversal($tipoProposicao)
                : false;
            
            if ($deveUsarUniversal) {
                Log::info('OnlyOffice Editor: Usando template universal (sem arquivo salvo)', [
                    'proposicao_id' => $proposicao->id,
                    'tipo_proposicao' => $tipoProposicao ? $tipoProposicao->nome : $proposicao->tipo,
                    'template_id_anterior' => $proposicao->template_id
                ]);
                
                // PRIORIDADE 2: Usar template universal quando não há arquivo salvo
                $config = $this->generateOnlyOfficeConfigWithUniversalTemplate($proposicao, 'parlamentar');
            } else if ($proposicao->template_id && $proposicao->template) {
                Log::info('OnlyOffice Editor: Usando template específico', [
                    'proposicao_id' => $proposicao->id,
                    'template_id' => $proposicao->template_id
                ]);
                
                $config = $this->onlyOfficeService->gerarConfiguracaoEditor(
                    $proposicao->template,
                    $proposicao,
                    'proposicao',
                    $proposicao->id
                );
            } else {
                Log::info('OnlyOffice Editor: Usando fallback básico', [
                    'proposicao_id' => $proposicao->id,
                    'sem_template_universal' => isset($deveUsarUniversal) ? !$deveUsarUniversal : true,
                    'sem_template_especifico' => !$proposicao->template_id
                ]);
                
                // Fallback para proposições sem qualquer template
                $config = $this->generateOnlyOfficeConfig($proposicao);
            }
        }

        // Usar view específica para parlamentares
        return view('proposicoes.parlamentar.onlyoffice-editor', compact('proposicao', 'config'));
    }
    
    /**
     * Verificar status de atualização da proposição
     */
    public function getUpdateStatus(Proposicao $proposicao)
    {
        return response()->json([
            'id' => $proposicao->id,
            'ultima_modificacao' => $proposicao->ultima_modificacao ? 
                                   $proposicao->ultima_modificacao->toISOString() : 
                                   $proposicao->updated_at->toISOString(),
            'arquivo_path' => $proposicao->arquivo_path,
            'conteudo_length' => strlen($proposicao->conteudo ?? ''),
            'status' => $proposicao->status
        ]);
    }

    /**
     * Obter timestamp do arquivo físico do documento
     */
    private function getDocumentFileTimestamp(Proposicao $proposicao): ?int
    {
        if (!$proposicao->arquivo_path) {
            return null;
        }
        
        $caminhosPossiveis = [
            storage_path('app/' . $proposicao->arquivo_path),
            storage_path('app/private/' . $proposicao->arquivo_path),
            storage_path('app/local/' . $proposicao->arquivo_path),
        ];
        
        foreach ($caminhosPossiveis as $caminho) {
            if (file_exists($caminho)) {
                $timestamp = filemtime($caminho);
                return $timestamp ?: null;
            }
        }
        
        return null;
    }
    
    /**
     * Gerar configuração OnlyOffice usando Template Universal
     */
    private function generateOnlyOfficeConfigWithUniversalTemplate(Proposicao $proposicao, $userType = 'legislativo')
    {
        // ✅ LÓGICA INTELIGENTE DE DOCUMENT_KEY (baseada no Template Universal)
        $documentKey = $this->generateIntelligentDocumentKey($proposicao);
        
        $fileTimestamp = $this->getDocumentFileTimestamp($proposicao);
        $lastModified = $fileTimestamp ?: ($proposicao->ultima_modificacao ? 
                       $proposicao->ultima_modificacao->timestamp : 
                       $proposicao->updated_at->timestamp);
        
        // OTIMIZAÇÃO: Token mais eficiente
        $version = $lastModified;
        $token = base64_encode($proposicao->id . '|' . $lastModified);
        
        $documentUrl = route('proposicoes.onlyoffice.download', [
            'id' => $proposicao->id,
            'token' => $token,
            'v' => $version,
            '_' => $lastModified
        ]);
        
        // Se estiver em ambiente local/docker, ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            $documentUrl = str_replace('localhost:8001', 'legisinc-app:80', $documentUrl);
        }

        // Usar callback específico baseado no tipo de usuário
        if ($userType === 'parlamentar') {
            $callbackUrl = route('api.onlyoffice.callback.proposicao', [
                'proposicao' => $proposicao
            ]);
        } else {
            $callbackUrl = route('api.onlyoffice.callback.legislativo', [
                'proposicao' => $proposicao,
                'documentKey' => $documentKey
            ]);
        }

        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }
        
        Log::info('OnlyOffice Config - Template Universal', [
            'proposicao_id' => $proposicao->id,
            'document_key' => $documentKey,
            'document_url' => $documentUrl,
            'callback_url' => $callbackUrl,
            'tipo_proposicao' => $proposicao->tipoProposicao->codigo ?? 'unknown'
        ]);
        
        return [
            'document' => [
                'fileType' => 'rtf',
                'key' => $documentKey,
                'title' => 'Proposição #' . $proposicao->id . ' - ' . $proposicao->ementa,
                'url' => $documentUrl,
                'permissions' => [
                    'edit' => true,
                    'download' => true,
                    'print' => true,
                    'review' => true,
                    'comment' => true,
                ],
                'info' => [
                    'author' => $proposicao->autor->name ?? 'Autor',
                    'created' => $proposicao->created_at->toISOString(),
                    'folder' => 'Proposições'
                ]
            ],
            'documentType' => 'word',
            'editorConfig' => [
                'mode' => 'edit',
                'lang' => 'pt-BR',
                'region' => 'pt-BR',
                'documentLang' => 'pt-BR',
                'callbackUrl' => $callbackUrl,
                'customization' => [
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => ['pt-BR'],
                    ],
                    'documentLanguage' => 'pt-BR',
                    'autosave' => true,
                    'chat' => false,
                    'comments' => true,
                    'help' => false,
                    'hideRightMenu' => false,
                    'logo' => [
                        'image' => asset('template/cabecalho.png'),
                        'imageEmbedded' => asset('template/cabecalho.png'),
                        'url' => config('app.url')
                    ],
                    'compactToolbar' => false,
                    'toolbarNoTabs' => false,
                    'reviewDisplay' => 'original'
                ],
                'user' => [
                    'id' => (string) Auth::id(),
                    'name' => Auth::user()->name,
                    'group' => Auth::user()->isLegislativo() ? 'Legislativo' : 'Parlamentar'
                ],
                'embedded' => [
                    'saveUrl' => $callbackUrl,
                    'embedUrl' => $callbackUrl,
                    'shareUrl' => config('app.url'),
                    'toolbarDocked' => 'top'
                ]
            ],
            'type' => 'desktop',
            'token' => $token,
            'height' => '100%',
            'width' => '100%'
        ];
    }

    /**
     * Gerar document_key inteligente para proposições (versão sem cache por problemas de permissão)
     */
    private function generateIntelligentDocumentKey(Proposicao $proposicao): string
    {
        // ✅ DOCUMENT KEY ESTÁVEL: Baseado apenas no ID e data de criação
        // Isso permite continuidade da sessão OnlyOffice entre Parlamentar e Legislativo
        // A key só muda quando a proposição é recriada, não a cada edição

        // Se já existe uma chave OnlyOffice salva, usar ela para manter sessão
        if (!empty($proposicao->onlyoffice_key)) {
            return $proposicao->onlyoffice_key;
        }

        // Hash baseado em dados imutáveis da proposição
        $stableData = $proposicao->id . '|' .
                     ($proposicao->created_at ? $proposicao->created_at->timestamp : '0') . '|' .
                     ($proposicao->autor_id ?? '0');

        $stableHash = md5($stableData);
        $hashSuffix = substr($stableHash, 0, 8);

        // Key estável que não muda com edições de conteúdo
        $documentKey = "proposicao_{$proposicao->id}_{$hashSuffix}";

        // Salvar a chave na proposição para garantir consistência
        $proposicao->update(['onlyoffice_key' => $documentKey]);

        return $documentKey;
    }

    /**
     * Limpar cache da proposição após salvamento (versão sem cache)
     */
    /**
     * Obter significado dos status do OnlyOffice para logs detalhados
     */
    private function getStatusMeaning(int $status): string
    {
        $meanings = [
            0 => 'NotFound - Documento não encontrado',
            1 => 'Editing - Documento sendo editado',
            2 => 'MustSave - Documento salvo e pronto para download',
            3 => 'Corrupted - Documento corrompido',
            4 => 'Closed - Documento fechado sem alterações',
            5 => 'ForceSave - Salvamento forçado em andamento',
            6 => 'ForceSaveReady - Force save concluído, documento salvo',
            7 => 'MustForceSave - Deve forçar salvamento'
        ];

        return $meanings[$status] ?? "Unknown status: {$status}";
    }

    private function clearProposicaoCache(Proposicao $proposicao): void
    {
        // Versão sem cache - apenas atualizar timestamp para forçar nova document key
        $proposicao->touch();

        // Log removido para evitar problemas de permissão
        // Log::info('Proposição atualizada após salvamento', ...);
    }

    /**
     * Exportar PDF do documento OnlyOffice
     */
    public function exportarPDF(Request $request, Proposicao $proposicao, \App\Services\OnlyOfficeConversionService $converter)
    {
        $startTime = microtime(true);

        // 1) Autorização
        if (\Illuminate\Support\Facades\Gate::denies('edit-onlyoffice', $proposicao)) {
            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'error',
                description: 'Tentativa de exportação PDF negada - usuário sem permissão',
                errorMessage: 'Gate edit-onlyoffice negou acesso',
                metadata: [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? 'Desconhecido',
                    'user_roles' => auth()->user()->getRoleNames()->toArray() ?? [],
                    'ip_address' => request()->ip(),
                ]
            );
            return response()->json(['message' => 'Forbidden'], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
        }

        // 2) Verifica se há arquivo base (rtf) associado
        $arquivoFonteRel = $proposicao->arquivo_path;

        // Se não há arquivo_path definido, tentar encontrar automaticamente
        if (!$arquivoFonteRel) {
            $arquivoEncontrado = $this->buscarArquivoProposicaoAutomaticamente($proposicao);
            if ($arquivoEncontrado) {
                // Atualizar proposição com o arquivo encontrado
                $proposicao->update(['arquivo_path' => $arquivoEncontrado]);
                $arquivoFonteRel = $arquivoEncontrado;

                Log::info('Arquivo RTF encontrado automaticamente e atualizado no banco', [
                    'proposicao_id' => $proposicao->id,
                    'arquivo_path' => $arquivoEncontrado
                ]);
            } else {
                DocumentWorkflowLog::logPdfExport(
                    proposicaoId: $proposicao->id,
                    status: 'error',
                    description: 'Falha na exportação PDF - arquivo fonte não encontrado',
                    errorMessage: 'Proposição não possui arquivo_path definido e nenhum arquivo RTF foi encontrado automaticamente',
                    metadata: [
                        'proposicao_numero' => $proposicao->numero,
                        'proposicao_ano' => $proposicao->ano,
                        'arquivo_path' => $arquivoFonteRel,
                    ]
                );
                return response()->json(['message' => 'Arquivo de origem não disponível para exportação'], 422);
            }
        }

        // Verificar caminhos possíveis para o arquivo RTF
        $caminhosPossiveis = [
            storage_path('app/' . $arquivoFonteRel),
            storage_path('app/private/' . $arquivoFonteRel),
            storage_path('app/local/' . $arquivoFonteRel),
        ];

        $arquivoFonteAbs = null;
        foreach ($caminhosPossiveis as $caminho) {
            if (file_exists($caminho)) {
                $arquivoFonteAbs = $caminho;
                break;
            }
        }

        if (!$arquivoFonteAbs) {
            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'error',
                description: 'Falha na exportação PDF - arquivo físico não encontrado',
                errorMessage: 'Arquivo RTF não encontrado nos caminhos de storage',
                metadata: [
                    'proposicao_numero' => $proposicao->numero,
                    'proposicao_ano' => $proposicao->ano,
                    'arquivo_path' => $arquivoFonteRel,
                    'caminhos_testados' => $caminhosPossiveis,
                ]
            );
            return response()->json(['message' => 'Arquivo de origem não disponível para exportação'], 422);
        }

        // Log início da exportação
        DocumentWorkflowLog::logPdfExport(
            proposicaoId: $proposicao->id,
            status: 'pending',
            description: 'Iniciando processo de exportação de PDF via OnlyOffice',
            metadata: [
                'source_file_path' => $arquivoFonteRel,
                'source_file_absolute' => $arquivoFonteAbs,
                'source_file_exists' => file_exists($arquivoFonteAbs),
                'source_file_size' => file_exists($arquivoFonteAbs) ? filesize($arquivoFonteAbs) : null,
                'proposicao_numero' => $proposicao->numero,
                'proposicao_ano' => $proposicao->ano,
                'converter_service' => get_class($converter),
            ]
        );

        try {
            // 3) Converte para PDF (em tmp)
            $tmpPdfAbs = $converter->convertToPdf($arquivoFonteAbs);

            // 4) Persiste no storage definitivo
            $dir = "proposicoes/pdfs/{$proposicao->id}";
            $fileName = sprintf('proposicao_%d_exported_%d.pdf', $proposicao->id, time());
            $destRel = $dir . '/' . $fileName;

            Storage::disk('local')->makeDirectory($dir);
            copy($tmpPdfAbs, Storage::disk('local')->path($destRel));

            // 5) Calcula informações do arquivo final
            $destAbs = Storage::disk('local')->path($destRel);
            $fileSizeBytes = file_exists($destAbs) ? filesize($destAbs) : null;
            $fileHash = file_exists($destAbs) ? md5_file($destAbs) : null;
            $executionTimeMs = round((microtime(true) - $startTime) * 1000);

            // 6) Atualiza BD
            $proposicao->pdf_exportado_path = $destRel;
            $proposicao->pdf_exportado_em = \Carbon\Carbon::now();
            $proposicao->save();

            // 7) Log de sucesso detalhado
            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'success',
                description: "PDF exportado com sucesso: {$fileName}",
                filePath: $destRel,
                fileSizeBytes: $fileSizeBytes,
                fileHash: $fileHash,
                executionTimeMs: $executionTimeMs,
                metadata: [
                    'source_file' => $arquivoFonteRel,
                    'destination_file' => $destRel,
                    'destination_absolute' => $destAbs,
                    'temp_file_used' => $tmpPdfAbs,
                    'file_size_formatted' => $fileSizeBytes ? $this->formatBytes($fileSizeBytes) : null,
                    'storage_disk' => 'local',
                    'storage_directory_created' => true,
                    'database_updated' => true,
                    'proposicao_numero' => $proposicao->numero,
                    'proposicao_ano' => $proposicao->ano,
                    'export_timestamp' => $proposicao->pdf_exportado_em->toISOString(),
                ]
            );

            Log::info('Exportação PDF concluída', [
                'proposicao_id' => $proposicao->id,
                'user_id' => Auth::id(),
                'path' => $destRel,
                'execution_time_ms' => $executionTimeMs,
                'file_size_bytes' => $fileSizeBytes,
            ]);

            return response()->json([
                'message' => 'PDF exportado com sucesso',
                'path'    => $destRel,
                'exported_at' => $proposicao->pdf_exportado_em->toIso8601String(),
                'file_size' => $fileSizeBytes,
                'execution_time_ms' => $executionTimeMs,
            ]);
        } catch (\Throwable $e) {
            $executionTimeMs = round((microtime(true) - $startTime) * 1000);

            // Log de erro detalhado
            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'error',
                description: 'Falha durante a exportação de PDF',
                executionTimeMs: $executionTimeMs,
                errorMessage: $e->getMessage(),
                metadata: [
                    'source_file' => $arquivoFonteRel,
                    'source_file_absolute' => $arquivoFonteAbs,
                    'source_file_exists' => file_exists($arquivoFonteAbs),
                    'error_class' => get_class($e),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile(),
                    'stack_trace_preview' => substr($e->getTraceAsString(), 0, 500),
                    'proposicao_numero' => $proposicao->numero,
                    'proposicao_ano' => $proposicao->ano,
                ]
            );

            Log::error('Falha ao exportar PDF', [
                'proposicao_id' => $proposicao->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTimeMs,
            ]);
            return response()->json(['message' => 'Falha na exportação do PDF'], 500);
        }
    }

    /**
     * Helper para formatar bytes
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Buscar arquivo RTF da proposição automaticamente quando arquivo_path é NULL
     */
    private function buscarArquivoProposicaoAutomaticamente($proposicao): ?string
    {
        // Diretórios onde buscar arquivos RTF
        $diretorios = [
            'proposicoes',         // Novo padrão do callback OnlyOffice
            'private/proposicoes', // Padrão antigo
            'public/proposicoes',  // Outras variações
            'local/proposicoes'    // Outras variações
        ];

        $arquivosEncontrados = [];

        foreach ($diretorios as $diretorio) {
            $pattern = storage_path("app/{$diretorio}/proposicao_{$proposicao->id}_*.rtf");
            $arquivos = glob($pattern);

            if (!empty($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    // Extrair timestamp do nome do arquivo para ordenar por mais recente
                    if (preg_match('/proposicao_\d+_(\d+)\.rtf$/', $arquivo, $matches)) {
                        $timestamp = (int)$matches[1];
                        $caminhoRelativo = str_replace(storage_path('app/'), '', $arquivo);
                        $arquivosEncontrados[$timestamp] = $caminhoRelativo;
                    }
                }
            }
        }

        if (!empty($arquivosEncontrados)) {
            // Retornar o arquivo mais recente (maior timestamp)
            ksort($arquivosEncontrados);
            $arquivoMaisRecente = end($arquivosEncontrados);

            Log::info('Arquivo RTF encontrado automaticamente', [
                'proposicao_id' => $proposicao->id,
                'arquivo_mais_recente' => $arquivoMaisRecente,
                'total_arquivos_encontrados' => count($arquivosEncontrados)
            ]);

            return $arquivoMaisRecente;
        }

        return null;
    }

    /**
     * Interceptar PDF gerado pelo OnlyOffice e enviar diretamente para S3
     * Método mais eficiente que não baixa no navegador
     */
    public function exportarPDFParaS3(Request $request, Proposicao $proposicao)
    {
        $startTime = microtime(true);

        try {
            // 1. Verificar permissões
            if (\Illuminate\Support\Facades\Gate::denies('edit-onlyoffice', $proposicao)) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            Log::info('🚀 OnlyOffice S3: Iniciando exportação PDF para S3', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'has_pdf_file' => $request->hasFile('pdf_file')
            ]);

            // 2. Verificar se foi enviado um arquivo PDF pelo frontend
            if ($request->hasFile('pdf_file')) {
                return $this->uploadPDFToS3FromFile($request, $proposicao, $startTime);
            }

            // 3. Verificar se foi enviada uma URL do PDF do OnlyOffice
            if ($request->has('pdf_url')) {
                return $this->downloadPDFFromOnlyOfficeAndUploadToS3($request, $proposicao, $startTime);
            }

            // 2. Gerar document key e URL do documento
            $documentKey = $this->generateIntelligentDocumentKey($proposicao);

            // Gerar URL do documento (mesmo padrão usado nos editores)
            $lastModified = $proposicao->updated_at ? $proposicao->updated_at->timestamp : time();
            $token = base64_encode($proposicao->id . '|' . $lastModified);
            $documentUrl = route('proposicoes.onlyoffice.download', [
                'id' => $proposicao->id,
                'token' => $token,
                'v' => $lastModified,
                '_' => $lastModified
            ]);

            // Se estiver em ambiente local/docker, ajustar URL para comunicação entre containers
            if (config('app.env') === 'local') {
                $documentUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $documentUrl);
            }

            Log::info('✅ OnlyOffice S3: URLs geradas', [
                'proposicao_id' => $proposicao->id,
                'document_key' => $documentKey,
                'document_url' => $documentUrl
            ]);

            // 3. Preparar dados para requisição de conversão ao OnlyOffice
            $conversionData = [
                'async' => false,
                'filetype' => 'rtf',
                'key' => $documentKey,
                'outputtype' => 'pdf',
                'title' => "proposicao_{$proposicao->id}.pdf",
                'url' => $documentUrl
            ];

            // 4. Fazer requisição ao OnlyOffice Document Server para conversão
            // Usar URL interna para comunicação Docker-to-Docker
            $onlyofficeUrl = config('onlyoffice.internal_url') . '/ConvertService.ashx';

            $response = \Illuminate\Support\Facades\Http::timeout(60)->post($onlyofficeUrl, $conversionData);

            if (!$response->successful()) {
                throw new \Exception('Falha na conversão OnlyOffice: ' . $response->body());
            }

            // Parse XML response from OnlyOffice
            $responseBody = $response->body();

            Log::info('🔍 OnlyOffice S3: Resposta da conversão', [
                'proposicao_id' => $proposicao->id,
                'status_code' => $response->status(),
                'response_body' => $responseBody
            ]);

            $xml = simplexml_load_string($responseBody);
            if (!$xml) {
                throw new \Exception('Erro ao parsear resposta XML do OnlyOffice');
            }

            // Verificar se a conversão foi bem-sucedida
            if ((string)$xml->EndConvert !== 'True') {
                throw new \Exception('OnlyOffice: Conversão não finalizada');
            }

            $pdfUrl = (string)$xml->FileUrl;

            Log::info('✅ OnlyOffice S3: PDF gerado com sucesso', [
                'proposicao_id' => $proposicao->id,
                'pdf_url' => $pdfUrl
            ]);

            // 5. Baixar PDF da URL temporária do OnlyOffice
            $pdfResponse = \Illuminate\Support\Facades\Http::timeout(30)->get($pdfUrl);

            if (!$pdfResponse->successful()) {
                throw new \Exception('Falha ao baixar PDF do OnlyOffice');
            }

            $pdfContent = $pdfResponse->body();
            $fileSizeBytes = strlen($pdfContent);

            // 6. Gerar estrutura organizada para S3
            $year = now()->year;
            $month = now()->format('m');
            $day = now()->format('d');
            $timestamp = time();

            // Estrutura: proposicoes/pdfs/YYYY/MM/DD/{proposicao_id}/manual/proposicao_{id}_manual_{timestamp}.pdf
            $fileName = "proposicoes/pdfs/{$year}/{$month}/{$day}/{$proposicao->id}/manual/proposicao_{$proposicao->id}_manual_{$timestamp}.pdf";

            Log::info('📤 OnlyOffice S3: Enviando PDF para S3', [
                'proposicao_id' => $proposicao->id,
                'file_name' => $fileName,
                'file_size' => $this->formatBytes($fileSizeBytes)
            ]);

            // 7. Enviar para S3
            $uploaded = \Illuminate\Support\Facades\Storage::disk('s3')->put($fileName, $pdfContent, [
                'ContentType' => 'application/pdf',
                'ACL' => 'private' // Arquivo privado no S3
            ]);

            if (!$uploaded) {
                throw new \Exception('Falha ao enviar PDF para S3');
            }

            // 8. Gerar URL assinada do S3 (válida por 1 hora)
            $s3Url = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl($fileName, now()->addHour());

            // 9. Atualizar proposição com informações do PDF no S3
            $proposicao->update([
                'pdf_s3_path' => $fileName,
                'pdf_s3_url' => $s3Url,
                'pdf_exportado_em' => now(),
                'pdf_size_bytes' => $fileSizeBytes
            ]);

            $executionTimeMs = round((microtime(true) - $startTime) * 1000);

            // 10. Log de sucesso
            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'success',
                description: 'PDF exportado com sucesso para AWS S3',
                executionTimeMs: $executionTimeMs,
                metadata: [
                    's3_path' => $fileName,
                    's3_url_expires_at' => now()->addHour()->toIso8601String(),
                    'file_size_bytes' => $fileSizeBytes,
                    'file_size_formatted' => $this->formatBytes($fileSizeBytes),
                    'onlyoffice_pdf_url' => $pdfUrl,
                    'conversion_data' => $conversionData
                ]
            );

            Log::info('🎉 OnlyOffice S3: Exportação concluída com sucesso', [
                'proposicao_id' => $proposicao->id,
                's3_path' => $fileName,
                'execution_time_ms' => $executionTimeMs,
                'file_size' => $this->formatBytes($fileSizeBytes)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF exportado com sucesso para AWS S3',
                's3_path' => $fileName,
                's3_url' => $s3Url,
                'url_expires_at' => now()->addHour()->toIso8601String(),
                'exported_at' => $proposicao->pdf_exportado_em->toIso8601String(),
                'file_size' => $this->formatBytes($fileSizeBytes),
                'file_size_bytes' => $fileSizeBytes,
                'execution_time_ms' => $executionTimeMs
            ]);

        } catch (\Throwable $e) {
            $executionTimeMs = round((microtime(true) - $startTime) * 1000);

            Log::error('❌ OnlyOffice S3: Falha na exportação', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTimeMs,
                'stack_trace' => $e->getTraceAsString()
            ]);

            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'error',
                description: 'Falha na exportação PDF para S3',
                executionTimeMs: $executionTimeMs,
                errorMessage: $e->getMessage(),
                metadata: [
                    'error_class' => get_class($e),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Falha na exportação do PDF para S3',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica a última exportação para S3
     */
    public function verificarUltimaExportacaoS3(Proposicao $proposicao)
    {
        try {
            // Buscar no S3 o último arquivo exportado para esta proposição
            $s3Disk = \Illuminate\Support\Facades\Storage::disk('s3');

            // Padrões de busca para encontrar PDFs exportados desta proposição
            $searchPaths = [
                "proposicoes/pdf/{$proposicao->id}/",
                "proposicoes/pdfs/"
            ];

            $lastExportedFile = null;
            $lastExportedTime = null;
            $lastExportedUrl = null;

            // Buscar arquivos no S3
            foreach ($searchPaths as $path) {
                try {
                    $files = $s3Disk->allFiles($path);
                    foreach ($files as $file) {
                        if (str_contains($file, "proposicao_{$proposicao->id}_")) {
                            $fileTime = $s3Disk->lastModified($file);
                            if (!$lastExportedTime || $fileTime > $lastExportedTime) {
                                $lastExportedFile = $file;
                                $lastExportedTime = $fileTime;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Continuar buscando em outros paths
                    continue;
                }
            }

            // Se encontrou um arquivo exportado
            if ($lastExportedFile) {
                // Gerar URL temporária (válida por 24 horas)
                $lastExportedUrl = $s3Disk->temporaryUrl($lastExportedFile, now()->addDay());
                $fileSize = $s3Disk->size($lastExportedFile);

                Log::info('✅ Última exportação S3 encontrada', [
                    'proposicao_id' => $proposicao->id,
                    's3_path' => $lastExportedFile,
                    'exported_at' => \Carbon\Carbon::createFromTimestamp($lastExportedTime)->format('d/m/Y H:i:s'),
                    'file_size' => $fileSize
                ]);

                return response()->json([
                    'success' => true,
                    'has_export' => true,
                    's3_path' => $lastExportedFile,
                    's3_url' => $lastExportedUrl,
                    'exported_at' => \Carbon\Carbon::createFromTimestamp($lastExportedTime)->format('d/m/Y H:i:s'),
                    'file_size_kb' => round($fileSize / 1024, 2),
                    'file_name' => basename($lastExportedFile)
                ]);
            }

            Log::info('⚠️ Nenhuma exportação S3 encontrada', [
                'proposicao_id' => $proposicao->id
            ]);

            return response()->json([
                'success' => true,
                'has_export' => false,
                'message' => 'Nenhuma exportação S3 encontrada para esta proposição'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao verificar última exportação S3', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'has_export' => false,
                'message' => 'Erro ao verificar exportação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportação automática para S3 durante aprovação (server-side only)
     */
    public function exportarPDFParaS3Automatico(Request $request, Proposicao $proposicao)
    {
        $startTime = microtime(true);
        $isTestOnly = $request->query('test_only') === '1';

        try {
            // 1. Verificar permissões
            if (\Illuminate\Support\Facades\Gate::denies('edit-onlyoffice', $proposicao)) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            Log::info($isTestOnly ? '🧪 OnlyOffice S3: TESTE de exportação antes da aprovação' : '🤖 OnlyOffice S3: Exportação automática durante aprovação', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'status' => $proposicao->status,
                'test_only' => $isTestOnly
            ]);

            // 2. Buscar a versão mais recente do documento (com edições salvas)
            $documentKey = $this->generateIntelligentDocumentKey($proposicao);

            // Usar arquivo_path que contém a versão mais recente (incluindo edições do legislativo)
            if ($proposicao->arquivo_path) {
                // Verificar existência usando Storage e fallback para file_exists()
                $arquivoExiste = Storage::exists($proposicao->arquivo_path);
                $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_path);

                if (!$arquivoExiste && file_exists($caminhoCompleto)) {
                    // Fallback: arquivo existe fisicamente mas Storage não o encontra
                    $arquivoExiste = true;
                    Log::warning('OnlyOffice S3 Auto: Storage::exists falhou, mas arquivo existe fisicamente', [
                        'proposicao_id' => $proposicao->id,
                        'arquivo_path' => $proposicao->arquivo_path,
                        'caminho_completo' => $caminhoCompleto
                    ]);
                }

                if ($arquivoExiste) {
                    $fileSize = file_exists($caminhoCompleto) ? filesize($caminhoCompleto) : 0;
                    $lastModified = file_exists($caminhoCompleto) ? filemtime($caminhoCompleto) : time();

                    Log::info('📝 OnlyOffice S3 Auto: Usando versão mais recente salva', [
                        'proposicao_id' => $proposicao->id,
                        'arquivo_path' => $proposicao->arquivo_path,
                        'file_size' => $fileSize,
                        'modificado_em' => $proposicao->ultima_modificacao,
                        'metodo_verificacao' => file_exists($caminhoCompleto) ? 'file_exists' : 'Storage'
                    ]);

                    $token = base64_encode($proposicao->id . '|' . $lastModified);
                    $documentUrl = route('proposicoes.onlyoffice.download', [
                        'id' => $proposicao->id,
                        'token' => $token,
                        'v' => $lastModified,
                        '_' => $lastModified
                    ]);
                } else {
                    throw new \Exception("Arquivo da proposição não encontrado: {$proposicao->arquivo_path}. Certifique-se de que o documento foi salvo antes da exportação.");
                }
            } else {
                throw new \Exception('Proposição não possui arquivo definido. Certifique-se de que o documento foi salvo antes da exportação.');
            }

            // Se estiver em ambiente local/docker, ajustar URL para comunicação entre containers
            if (config('app.env') === 'local') {
                $documentUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $documentUrl);
            }

            Log::info('✅ OnlyOffice S3 Auto: URLs geradas', [
                'proposicao_id' => $proposicao->id,
                'document_key' => $documentKey,
                'document_url' => $documentUrl
            ]);

            // 3. Preparar dados de conversão para PDF
            $conversionData = [
                'async' => false,
                'key' => $documentKey,
                'outputtype' => 'pdf',
                'filetype' => 'docx',
                'title' => "Proposição {$proposicao->id} - Exportação Automática",
                'url' => $documentUrl
            ];

            // 4. Fazer requisição ao OnlyOffice Document Server para conversão
            $onlyofficeUrl = config('onlyoffice.internal_url') . '/ConvertService.ashx';

            $response = \Illuminate\Support\Facades\Http::timeout(60)->post($onlyofficeUrl, $conversionData);

            if (!$response->successful()) {
                throw new \Exception('Falha na conversão OnlyOffice: ' . $response->body());
            }

            // 5. Processar resposta do OnlyOffice
            $responseBody = $response->body();

            // Verificar se a resposta é XML (formato usual do OnlyOffice)
            if (str_starts_with(trim($responseBody), '<?xml')) {
                $xml = simplexml_load_string($responseBody);
                if ($xml === false) {
                    throw new \Exception('Falha ao processar resposta XML do OnlyOffice');
                }

                $pdfUrl = (string) $xml->FileUrl;
                $percent = (int) $xml->Percent;

                if (empty($pdfUrl) || $percent !== 100) {
                    throw new \Exception('Conversão PDF falhou - URL vazia ou processo incompleto');
                }
            } else {
                // Tentar como JSON
                $result = json_decode($responseBody, true);
                if (!$result || !isset($result['fileUrl'])) {
                    throw new \Exception('Resposta inválida do OnlyOffice: ' . $responseBody);
                }
                $pdfUrl = $result['fileUrl'];
            }

            // 6. Converter URL externa para interna (para download do container)
            $internalUrl = str_replace('http://localhost:8080', config('onlyoffice.internal_url'), $pdfUrl);

            // 7. Baixar o PDF do OnlyOffice
            $pdfResponse = \Illuminate\Support\Facades\Http::timeout(30)->get($internalUrl);
            if (!$pdfResponse->successful()) {
                throw new \Exception('Falha ao baixar PDF do OnlyOffice: ' . $pdfResponse->status());
            }

            $pdfContent = $pdfResponse->body();
            $fileSizeBytes = strlen($pdfContent);

            // 8. Gerar estrutura organizada para S3
            $year = now()->year;
            $month = now()->format('m');
            $day = now()->format('d');
            $timestamp = time();

            // Estrutura: proposicoes/pdfs/YYYY/MM/DD/{proposicao_id}/automatic/proposicao_{id}_auto_{timestamp}.pdf
            $fileName = "proposicoes/pdfs/{$year}/{$month}/{$day}/{$proposicao->id}/automatic/proposicao_{$proposicao->id}_auto_{$timestamp}.pdf";

            if ($isTestOnly) {
                // MODO TESTE: Apenas testar a conexão com S3, não salvar o arquivo
                Log::info('🧪 OnlyOffice S3: Modo teste - verificando apenas conexão', [
                    'proposicao_id' => $proposicao->id,
                    'test_file_name' => $fileName,
                    'test_content_size' => $fileSizeBytes
                ]);

                // Testar se conseguimos acessar o bucket S3
                try {
                    $s3Disk = \Illuminate\Support\Facades\Storage::disk('s3');
                    $testFileName = "test/connection_test_" . time() . ".txt";
                    $testContent = "Test connection from LegisInc - " . now()->toISOString();

                    Log::info('🧪 OnlyOffice S3: Iniciando teste detalhado', [
                        'proposicao_id' => $proposicao->id,
                        'test_file_name' => $testFileName,
                        'test_content_size' => strlen($testContent),
                        's3_config' => [
                            'bucket' => config('filesystems.disks.s3.bucket'),
                            'region' => config('filesystems.disks.s3.region'),
                            'key_length' => strlen(config('filesystems.disks.s3.key')),
                            'key_first8' => substr(config('filesystems.disks.s3.key'), 0, 8),
                            'endpoint' => config('filesystems.disks.s3.endpoint'),
                            'secret_length' => strlen(config('filesystems.disks.s3.secret'))
                        ]
                    ]);

                    // Tentar fazer upload de teste
                    try {
                        $testUploaded = $s3Disk->put($testFileName, $testContent);

                        Log::info('🧪 OnlyOffice S3: Resultado do upload teste', [
                            'proposicao_id' => $proposicao->id,
                            'test_uploaded' => $testUploaded,
                            'test_file_name' => $testFileName
                        ]);
                    } catch (\Exception $uploadError) {
                        Log::error('🧪 OnlyOffice S3: Erro específico no upload', [
                            'proposicao_id' => $proposicao->id,
                            'error_message' => $uploadError->getMessage(),
                            'error_class' => get_class($uploadError)
                        ]);
                        throw $uploadError;
                    }

                    if (!$testUploaded) {
                        throw new \Exception('Teste de conexão S3 falhou - não foi possível fazer upload');
                    }

                    // Verificar se arquivo existe
                    $exists = $s3Disk->exists($testFileName);
                    Log::info('🧪 OnlyOffice S3: Verificação de existência', [
                        'proposicao_id' => $proposicao->id,
                        'file_exists' => $exists
                    ]);

                    // Tentar deletar o arquivo de teste
                    $deleted = $s3Disk->delete($testFileName);
                    Log::info('🧪 OnlyOffice S3: Limpeza do teste', [
                        'proposicao_id' => $proposicao->id,
                        'file_deleted' => $deleted
                    ]);

                } catch (\Exception $s3TestError) {
                    Log::error('🧪 OnlyOffice S3: Erro detalhado no teste', [
                        'proposicao_id' => $proposicao->id,
                        'error_message' => $s3TestError->getMessage(),
                        'error_class' => get_class($s3TestError),
                        'error_file' => $s3TestError->getFile(),
                        'error_line' => $s3TestError->getLine()
                    ]);

                    throw new \Exception('Teste de conexão S3 falhou: ' . $s3TestError->getMessage());
                }

                $s3Url = null; // Não gerar URL para teste
            } else {
                // MODO NORMAL: Salvar arquivo real no S3
                $uploaded = \Illuminate\Support\Facades\Storage::disk('s3')->put($fileName, $pdfContent);

                if (!$uploaded) {
                    throw new \Exception('Falha ao enviar PDF para S3');
                }

                // 9. Gerar URL assinada do S3 (válida por 1 hora)
                $s3Url = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl($fileName, now()->addHour());

                // 10. Atualizar proposição com informações do PDF no S3
                Log::info('🗂️ OnlyOffice S3: Atualizando banco de dados', [
                    'proposicao_id' => $proposicao->id,
                    'pdf_s3_path' => $fileName,
                    'pdf_size_bytes' => $fileSizeBytes
                ]);

                $updateResult = $proposicao->update([
                    'pdf_s3_path' => $fileName,
                    'pdf_s3_url' => $s3Url,
                    'pdf_exportado_em' => now(),
                    'pdf_size_bytes' => $fileSizeBytes
                ]);

                Log::info('💾 OnlyOffice S3: Resultado da atualização do banco', [
                    'proposicao_id' => $proposicao->id,
                    'update_success' => $updateResult,
                    'pdf_s3_path_after' => $proposicao->fresh()->pdf_s3_path
                ]);
            }

            $executionTimeMs = round((microtime(true) - $startTime) * 1000);

            if ($isTestOnly) {
                // Resposta para modo teste
                Log::info('✅ OnlyOffice S3: Teste de conexão bem-sucedido', [
                    'proposicao_id' => $proposicao->id,
                    'execution_time_ms' => $executionTimeMs
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Teste de conexão S3 bem-sucedido',
                    'test_mode' => true,
                    'execution_time_ms' => $executionTimeMs
                ]);
            }

            // 11. Log de sucesso (modo normal)
            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'success',
                description: 'PDF exportado automaticamente para AWS S3 durante aprovação',
                executionTimeMs: $executionTimeMs,
                metadata: [
                    's3_path' => $fileName,
                    's3_url_expires_at' => now()->addHour()->toIso8601String(),
                    'file_size_bytes' => $fileSizeBytes,
                    'file_size_formatted' => $this->formatBytes($fileSizeBytes),
                    'export_type' => 'automatic_approval'
                ]
            );

            Log::info('🎉 OnlyOffice S3 Auto: Exportação automática concluída', [
                'proposicao_id' => $proposicao->id,
                's3_path' => $fileName,
                'execution_time_ms' => $executionTimeMs,
                'file_size' => $this->formatBytes($fileSizeBytes)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF exportado automaticamente para AWS S3',
                's3_path' => $fileName,
                's3_url' => $s3Url,
                'url_expires_at' => now()->addHour()->toIso8601String(),
                'exported_at' => $proposicao->pdf_exportado_em->toIso8601String(),
                'file_size' => $this->formatBytes($fileSizeBytes),
                'file_size_bytes' => $fileSizeBytes,
                'execution_time_ms' => $executionTimeMs,
                'export_type' => 'automatic'
            ]);

        } catch (\Throwable $e) {
            $executionTimeMs = round((microtime(true) - $startTime) * 1000);

            Log::error('❌ OnlyOffice S3 Auto: Falha na exportação automática', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTimeMs,
                'stack_trace' => $e->getTraceAsString()
            ]);

            DocumentWorkflowLog::logPdfExport(
                proposicaoId: $proposicao->id,
                status: 'error',
                description: 'Falha na exportação automática PDF para S3',
                executionTimeMs: $executionTimeMs,
                errorMessage: $e->getMessage(),
                metadata: [
                    'error_class' => get_class($e),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile(),
                    'export_type' => 'automatic_approval'
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Falha na exportação automática do PDF para S3',
                'error' => $e->getMessage(),
                'export_type' => 'automatic'
            ], 500);
        }
    }

    /**
     * Upload PDF recebido diretamente do frontend para S3
     */
    private function uploadPDFToS3FromFile(Request $request, Proposicao $proposicao, float $startTime)
    {
        try {
            $pdfFile = $request->file('pdf_file');

            // Validar arquivo
            if (!$pdfFile->isValid()) {
                throw new \Exception('Arquivo PDF inválido');
            }

            if ($pdfFile->getMimeType() !== 'application/pdf') {
                throw new \Exception('Arquivo deve ser um PDF válido');
            }

            $fileSize = $pdfFile->getSize();

            Log::info('📄 OnlyOffice S3: Arquivo PDF recebido do frontend', [
                'proposicao_id' => $proposicao->id,
                'file_size' => $fileSize,
                'mime_type' => $pdfFile->getMimeType(),
                'original_name' => $pdfFile->getClientOriginalName()
            ]);

            // 2. Gerar estrutura organizada para S3
            $year = now()->year;
            $month = now()->format('m');
            $day = now()->format('d');
            $timestamp = time();

            // Estrutura: proposicoes/pdfs/YYYY/MM/DD/{proposicao_id}/upload/proposicao_{id}_upload_{timestamp}.pdf
            $s3Path = "proposicoes/pdfs/{$year}/{$month}/{$day}/{$proposicao->id}/upload/proposicao_{$proposicao->id}_upload_{$timestamp}.pdf";

            // 3. Upload para S3
            $s3Disk = Storage::disk('s3');
            $uploaded = $s3Disk->putFileAs(
                dirname($s3Path),
                $pdfFile,
                basename($s3Path),
                [
                    'ContentType' => 'application/pdf',
                    'ContentDisposition' => 'inline; filename="proposicao_' . $proposicao->id . '.pdf"'
                ]
            );

            if (!$uploaded) {
                throw new \Exception('Falha ao enviar arquivo para S3');
            }

            Log::info('✅ OnlyOffice S3: PDF enviado com sucesso', [
                'proposicao_id' => $proposicao->id,
                's3_path' => $s3Path,
                'file_size' => $fileSize
            ]);

            // 4. Gerar URL temporária (válida por 1 hora)
            $s3Url = $s3Disk->temporaryUrl($s3Path, now()->addHour());

            // 5. Atualizar banco de dados
            $proposicao->update([
                'pdf_s3_path' => $s3Path,
                'pdf_s3_url' => $s3Url,
                'pdf_size_bytes' => $fileSize
            ]);

            // 6. Calcular tempo de execução
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('🎉 OnlyOffice S3: Exportação concluída com sucesso', [
                'proposicao_id' => $proposicao->id,
                'execution_time_ms' => $executionTime,
                's3_url' => $s3Url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF exportado com sucesso para AWS S3',
                's3_path' => $s3Path,
                's3_url' => $s3Url,
                'url_expires_at' => now()->addHour()->toISOString(),
                'exported_at' => now()->toISOString(),
                'file_size' => $this->formatBytes($fileSize),
                'file_size_bytes' => $fileSize,
                'execution_time_ms' => $executionTime
            ]);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('❌ OnlyOffice S3: Falha no upload direto', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTime
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Falha na exportação do PDF para S3',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Baixar PDF do OnlyOffice usando URL interna e enviar para S3
     */
    private function downloadPDFFromOnlyOfficeAndUploadToS3(Request $request, Proposicao $proposicao, float $startTime)
    {
        try {
            $pdfUrl = $request->input('pdf_url');

            // Converter URL externa para URL interna se necessário
            $internalUrl = str_replace('http://localhost:8080', config('onlyoffice.internal_url'), $pdfUrl);

            Log::info('📄 OnlyOffice S3: Baixando PDF via URL interna', [
                'proposicao_id' => $proposicao->id,
                'original_url' => $pdfUrl,
                'internal_url' => $internalUrl
            ]);

            // Baixar PDF do OnlyOffice usando URL interna
            $response = \Illuminate\Support\Facades\Http::timeout(30)->get($internalUrl);

            if (!$response->successful()) {
                throw new \Exception('Falha ao baixar PDF do OnlyOffice: ' . $response->status());
            }

            $pdfContent = $response->body();
            $fileSize = strlen($pdfContent);

            // Validar se é um PDF válido
            if (!str_starts_with($pdfContent, '%PDF-')) {
                throw new \Exception('Conteúdo baixado não é um PDF válido');
            }

            Log::info('✅ OnlyOffice S3: PDF baixado com sucesso', [
                'proposicao_id' => $proposicao->id,
                'file_size' => $fileSize
            ]);

            // Definir path no S3
            $timestamp = time();
            $s3Path = "proposicoes/pdf/{$proposicao->id}/proposicao_{$proposicao->id}_exported_{$timestamp}.pdf";

            // Upload para S3
            $s3Disk = Storage::disk('s3');
            $uploaded = $s3Disk->put($s3Path, $pdfContent, [
                'ContentType' => 'application/pdf',
                'ContentDisposition' => 'inline; filename="proposicao_' . $proposicao->id . '.pdf"'
            ]);

            if (!$uploaded) {
                throw new \Exception('Falha ao enviar arquivo para S3');
            }

            Log::info('🎉 OnlyOffice S3: PDF enviado para S3 com sucesso', [
                'proposicao_id' => $proposicao->id,
                's3_path' => $s3Path,
                'file_size' => $fileSize
            ]);

            // Gerar URL temporária (válida por 1 hora)
            $s3Url = $s3Disk->temporaryUrl($s3Path, now()->addHour());

            // Atualizar banco de dados
            $proposicao->update([
                'pdf_s3_path' => $s3Path,
                'pdf_s3_url' => $s3Url,
                'pdf_size_bytes' => $fileSize
            ]);

            // Calcular tempo de execução
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('🎉 OnlyOffice S3: Exportação concluída via URL', [
                'proposicao_id' => $proposicao->id,
                'execution_time_ms' => $executionTime,
                's3_url' => $s3Url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF exportado com sucesso para AWS S3',
                's3_path' => $s3Path,
                's3_url' => $s3Url,
                'url_expires_at' => now()->addHour()->toISOString(),
                'exported_at' => now()->toISOString(),
                'file_size' => $this->formatBytes($fileSize),
                'file_size_bytes' => $fileSize,
                'execution_time_ms' => $executionTime
            ]);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('❌ OnlyOffice S3: Falha no download via URL', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTime
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Falha na exportação do PDF para S3',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Interceptar URL de PDF do evento onDownloadAs e enviar para S3
     * Este método é chamado via webhook quando onDownloadAs é disparado
     */
    public function interceptarPDFOnDownloadAs(Request $request)
    {
        try {
            $pdfUrl = $request->input('pdf_url');
            $proposicaoId = $request->input('proposicao_id');

            if (!$pdfUrl || !$proposicaoId) {
                return response()->json(['error' => 'URL do PDF ou ID da proposição não fornecidos'], 400);
            }

            $proposicao = Proposicao::findOrFail($proposicaoId);

            Log::info('🔗 OnlyOffice S3: Interceptando PDF do onDownloadAs', [
                'proposicao_id' => $proposicaoId,
                'pdf_url' => $pdfUrl
            ]);

            // Baixar PDF da URL fornecida pelo OnlyOffice
            $pdfResponse = \Illuminate\Support\Facades\Http::timeout(30)->get($pdfUrl);

            if (!$pdfResponse->successful()) {
                throw new \Exception('Falha ao baixar PDF do OnlyOffice');
            }

            $pdfContent = $pdfResponse->body();
            $fileSizeBytes = strlen($pdfContent);

            // Gerar estrutura organizada para S3
            $year = now()->year;
            $month = now()->format('m');
            $day = now()->format('d');
            $timestamp = time();

            // Estrutura: proposicoes/pdfs/YYYY/MM/DD/{proposicao_id}/download/proposicao_{id}_download_{timestamp}.pdf
            $fileName = "proposicoes/pdfs/{$year}/{$month}/{$day}/{$proposicaoId}/download/proposicao_{$proposicaoId}_download_{$timestamp}.pdf";

            // Enviar para S3
            $uploaded = \Illuminate\Support\Facades\Storage::disk('s3')->put($fileName, $pdfContent, [
                'ContentType' => 'application/pdf',
                'ACL' => 'private'
            ]);

            if ($uploaded) {
                // Gerar URL assinada
                $s3Url = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl($fileName, now()->addHour());

                // Atualizar proposição
                $proposicao->update([
                    'pdf_s3_path' => $fileName,
                    'pdf_s3_url' => $s3Url,
                    'pdf_exportado_em' => now(),
                    'pdf_size_bytes' => $fileSizeBytes
                ]);

                Log::info('✅ OnlyOffice S3: PDF interceptado e enviado para S3', [
                    'proposicao_id' => $proposicaoId,
                    's3_path' => $fileName,
                    'file_size' => $this->formatBytes($fileSizeBytes)
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'PDF enviado para S3 com sucesso',
                    's3_path' => $fileName,
                    's3_url' => $s3Url
                ]);
            }

            throw new \Exception('Falha ao enviar PDF para S3');

        } catch (\Throwable $e) {
            Log::error('❌ OnlyOffice S3: Falha na interceptação', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}