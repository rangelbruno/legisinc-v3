<?php

namespace App\Http\Controllers;

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
}