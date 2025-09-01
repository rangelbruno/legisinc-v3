<?php

namespace App\Services\OnlyOffice;

use App\Models\TipoProposicaoTemplate;
use App\Models\User;
use App\Services\Template\TemplateParametrosService;
use App\Services\Template\TemplateProcessorService;
use App\Services\Template\TemplateUniversalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnlyOfficeService
{
    private string $serverUrl;

    private string $internalUrl;

    private string $jwtSecret;

    private TemplateParametrosService $templateParametrosService;

    private TemplateProcessorService $templateProcessorService;

    private TemplateUniversalService $templateUniversalService;

    public function __construct(
        TemplateParametrosService $templateParametrosService,
        TemplateProcessorService $templateProcessorService,
        TemplateUniversalService $templateUniversalService
    ) {
        $this->serverUrl = config('onlyoffice.server_url');
        $this->internalUrl = config('onlyoffice.internal_url');
        $this->jwtSecret = config('onlyoffice.jwt_secret');
        $this->templateParametrosService = $templateParametrosService;
        $this->templateProcessorService = $templateProcessorService;
        $this->templateUniversalService = $templateUniversalService;
    }

    /**
     * Obter dados da câmara dos parâmetros
     */
    private function obterDadosCamara(): array
    {
        try {
            $parametros = $this->templateParametrosService->obterParametrosTemplates();
        } catch (\Exception $e) {
            Log::warning('Erro ao obter parâmetros da câmara, usando fallback', [
                'error' => $e->getMessage()
            ]);
            // Fallback com dados padrão
            $parametros = [];
        }

        $endereco = $parametros['Endereço.endereco'] ?? '';
        $numero = $parametros['Endereço.numero'] ?? '';
        $complemento = $parametros['Endereço.complemento'] ?? '';
        $bairro = $parametros['Endereço.bairro'] ?? '';
        $municipio = $parametros['Endereço.cidade'] ?? '';
        $uf = $parametros['Endereço.estado'] ?? '';
        $cep = $parametros['Endereço.cep'] ?? '';

        // Montar endereço completo
        $enderecoCompleto = $endereco;
        if ($numero) {
            $enderecoCompleto .= ", {$numero}";
        }
        if ($complemento) {
            $enderecoCompleto .= ", {$complemento}";
        }

        return [
            'nome_oficial' => $parametros['Identificação.nome_camara'] ??
                            $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL',
            'endereco' => $endereco,
            'numero' => $numero,
            'complemento' => $complemento,
            'bairro' => $bairro,
            'municipio' => $municipio,
            'uf' => $uf,
            'cep' => $cep,
            'endereco_completo' => $enderecoCompleto,
            'endereco_linha' => $enderecoCompleto.($bairro ? " - {$bairro}" : '').($municipio && $uf ? " - {$municipio}/{$uf}" : ''),
            'legislatura' => $parametros['Gestão.legislatura_atual'] ?? '',
            'presidente_nome' => $parametros['Gestão.presidente_nome'] ?? '',
            'presidente_partido' => $parametros['Gestão.presidente_partido'] ?? '',
            'numero_vereadores' => $parametros['Gestão.numero_vereadores'] ?? '',
            'telefone' => $parametros['Contatos.telefone'] ?? '',
            'email' => $parametros['Contatos.email_institucional'] ?? '',
            'website' => $parametros['Contatos.website'] ?? '',
            'cnpj' => $parametros['Identificação.cnpj'] ?? '',
            'horario_funcionamento' => $parametros['Funcionamento.horario_funcionamento'] ?? '',
            'horario_atendimento' => $parametros['Funcionamento.horario_atendimento'] ?? '',
            'sessao' => date('Y'), // Usar ano atual como sessão
        ];
    }

    /**
     * Configuração para edição de template
     */
    public function criarConfiguracaoTemplate(TipoProposicaoTemplate $template): array
    {
        // Garantir que o template tem um arquivo processado com variáveis
        $this->garantirArquivoTemplateProcessado($template);

        // Usar arquivo processado
        $downloadUrl = $template->getUrlDownload();

        // Adicionar timestamp ao document_key para forçar reload após salvar
        $documentKeyWithVersion = $template->document_key.'_v'.$template->updated_at->timestamp;

        // Detectar tipo de arquivo pelo caminho
        $fileExtension = pathinfo($template->arquivo_path, PATHINFO_EXTENSION);

        // Configurar tipo correto para OnlyOffice
        switch ($fileExtension) {
            case 'txt':
                $fileType = 'txt';
                $documentType = 'word'; // Usar "word" para compatibilidade
                break;
            case 'rtf':
                $fileType = 'rtf';
                $documentType = 'word'; // RTF deve usar documentType "word"
                break;
            case 'docx':
                $fileType = 'docx';
                $documentType = 'word';
                break;
            case 'doc':
                $fileType = 'doc';
                $documentType = 'word';
                break;
            default:
                $fileType = 'rtf';
                $documentType = 'word'; // Padrão "word" para todos os documentos
                break;
        }

        // Garantir que documentType é válido e limpo
        $documentType = trim($documentType);
        $validTypes = ['word', 'cell', 'slide', 'text']; // Adicionar 'text' para RTF
        if (! in_array($documentType, $validTypes)) {
            $documentType = 'word'; // Fallback seguro
        }

        // Debug log removido - problema do documentType resolvido

        // Log::info('OnlyOffice editor configuration created', [
        //     'template_id' => $template->id,
        //     'document_key' => $documentKeyWithVersion,
        //     'file_type' => $fileType,
        //     'user_id' => auth()->id()
        // ]);

        $config = [
            'document' => [
                'fileType' => $fileType,
                'key' => $documentKeyWithVersion,
                'title' => $template->getNomeTemplate(),
                'url' => $downloadUrl,
                'info' => [
                    'owner' => Auth::user()->name ?? 'Sistema',
                    'uploaded' => $template->updated_at->format('d/m/Y H:i:s'),
                ],
                'permissions' => [
                    'comment' => true,
                    'copy' => true,
                    'download' => true,
                    'edit' => true,
                    'fillForms' => true,
                    'modifyFilter' => true,
                    'modifyContentControl' => true,
                    'review' => true,
                    'chat' => false,
                ],
            ],
            'documentType' => $documentType,
            'editorConfig' => [
                'mode' => 'edit',
                'callbackUrl' => $this->ajustarCallbackUrl(route('api.onlyoffice.callback', $documentKeyWithVersion)),
                'lang' => 'pt-BR',
                'region' => 'pt-BR',
                'documentLang' => 'pt-BR',
                'user' => [
                    'id' => (string) Auth::id(),
                    'name' => Auth::user()->name ?? 'Sistema',
                    'group' => 'administrators',
                ],
                'customization' => [
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => ['pt-BR'],
                    ],
                    'documentLanguage' => 'pt-BR',
                    'autosave' => true, // Habilitar autosave para garantir salvamento
                    'autosaveTimeout' => 10000, // 10 segundos para resposta mais rápida
                    'autosaveType' => 0, // 0 = strict mode
                    'forcesave' => true, // Permitir salvamento manual
                    'compactHeader' => true,
                    'toolbarNoTabs' => false,
                    'hideRightMenu' => false,
                    'feedback' => [
                        'visible' => false,
                    ],
                    'goback' => [
                        'url' => route('templates.index'),
                    ],
                ],
                'coEditing' => [
                    'mode' => 'strict', // Modo strict para evitar conflitos
                    'change' => false, // Desabilitar coediting para templates
                ],
                'user' => [
                    'id' => (string) (Auth::id() ?? 'user-'.time()),
                    'name' => Auth::user()->name ?? 'Usuário',
                    'group' => 'admin_'.Auth::id(), // Grupo único por usuário
                ],
            ],
        ];

        // Temporariamente desabilitar JWT para debug
        // if ($this->jwtSecret) {
        //     $config['token'] = $this->gerarToken($config);
        // }

        // Debug logs removidos - problema do documentType resolvido

        return $config;
    }

    /**
     * Obter MIME type baseado na extensão do arquivo
     */
    private function getMimeType(string $extensao): string
    {
        $mimeTypes = [
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
        ];

        return $mimeTypes[strtolower($extensao)] ?? 'application/octet-stream';
    }

    /**
     * Atualizar arquivo_path da proposição
     */
    private function atualizarArquivoPathProposicao(\App\Models\Proposicao $proposicao, string $arquivoPath): void
    {
        try {
            $proposicao->update(['arquivo_path' => $arquivoPath]);
            // Log::info('arquivo_path atualizado para proposição', [
            //     'proposicao_id' => $proposicao->id,
            //     'arquivo_path' => $arquivoPath
            // ]);
        } catch (\Exception $e) {
            // Log::warning('Erro ao atualizar arquivo_path da proposição', [
            //     'proposicao_id' => $proposicao->id,
            //     'arquivo_path' => $arquivoPath,
            //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Callback do ONLYOFFICE (auto-save)
     */
    public function processarCallback(string $documentKey, array $data): array
    {
        // Remover versão do document_key se existir
        $originalKey = preg_replace('/_v\d+$/', '', $documentKey);

        $template = TipoProposicaoTemplate::where('document_key', $originalKey)->first();

        if (! $template) {
            return ['error' => 1];
        }

        $status = $data['status'] ?? 0;

        // Log detalhado do callback
        // Log::info('OnlyOffice callback received', [
        //     'document_key' => $documentKey,
        //     'status' => $status,
        //     'status_description' => $this->getStatusDescription($status),
        //     'has_document_url' => isset($data['url']),
        //     'users_count' => count($data['users'] ?? []),
        //     'actions_count' => count($data['actions'] ?? [])
        // ]);

        // Implementar lock para evitar processamento concorrente
        $lockKey = "onlyoffice_save_lock_{$documentKey}";
        $lock = Cache::lock($lockKey, 10); // Lock por 10 segundos

        try {
            // Status 2 = Pronto para salvar (save)
            // Status 6 = Force save (salvamento forçado pelo usuário)
            // Status 4 = Documento fechado sem mudanças (mas vamos tentar salvar se tiver URL)
            if ((in_array($status, [2, 6]) && isset($data['url'])) ||
                ($status === 4 && isset($data['url']) && ! empty($data['url']))) {

                // Verificar se não está processando outro callback
                if ($lock->get()) {
                    // Log::info('Processando salvamento do template', [
                    //     'document_key' => $documentKey,
                    //     'status' => $status,
                    //     'status_type' => match($status) {
                    //         2 => 'auto_save',
                    //         6 => 'force_save',
                    //         4 => 'closing_with_url',
                    //         default => 'unknown'
                    //     },
                    //     'url' => $data['url']
                    // ]);

                    $this->salvarTemplate($template, $data['url']);

                    // Liberar lock após processar
                    $lock->release();
                } else {
                    // Log::warning('Callback ignorado - outro processamento em andamento', [
                    //     'document_key' => $documentKey,
                    //     'status' => $status
                    // ]);
                }
            }

            // Status 1 = Documento sendo editado
            // Status 4 = Documento fechado sem mudanças
            if (in_array($status, [1, 4])) {
                // Log::debug('OnlyOffice editing status update', [
                //     'document_key' => $documentKey,
                //     'status' => $status,
                //     'description' => $status === 1 ? 'Document being edited' : 'Document closed without changes'
                // ]);
            }

        } catch (\Exception $e) {
            // Log::error('Erro no processamento do callback', [
            //     'document_key' => $documentKey,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'data' => $data
            // ]);

            // Garantir liberação do lock em caso de erro
            optional($lock)->release();

            return ['error' => 1];
        }

        return ['error' => 0];
    }

    /**
     * Salvar template diretamente no banco de dados PostgreSQL
     */
    private function salvarTemplate(TipoProposicaoTemplate $template, string $url): void
    {
        try {
            // Corrigir URL do OnlyOffice para usar nome correto do container
            $urlCorrigida = str_replace([
                'onlyoffice-documentserver',
                'localhost:8080',
            ], 'legisinc-onlyoffice', $url);

            // Se a URL ainda contém localhost (qualquer porta), substituir para funcionar entre containers
            if (strpos($urlCorrigida, 'localhost') !== false) {
                // localhost:8001 (aplicação Laravel) → legisinc-app
                $urlCorrigida = str_replace('http://localhost:8001', 'http://legisinc-app', $urlCorrigida);
                // localhost:8080 (OnlyOffice) → legisinc-onlyoffice
                $urlCorrigida = str_replace('http://localhost:8080', 'http://legisinc-onlyoffice', $urlCorrigida);
            }

            // Log::info('OnlyOffice document save: Downloading content', [
            //     'template_id' => $template->id,
            //     'document_key' => $template->document_key,
            //     'url_corrected' => $url !== $urlCorrigida
            // ]);

            // Download do conteúdo com timeout aumentado
            try {
                $response = Http::timeout(60)->get($urlCorrigida);

                // Log::info('OnlyOffice download response', [
                //     'success' => $response->successful(),
                //     'status' => $response->status()
                // ]);
            } catch (\Exception $downloadException) {
                // Log::error('OnlyOffice download exception', [
                //     'url' => $urlCorrigida,
                //     'exception' => $downloadException->getMessage()
                // ]);
                $response = null;
            }

            if (! $response || ! $response->successful()) {
                // Fallback tentando outras variações de URL
                $urlsFallback = [
                    str_replace('http://localhost:8080', 'http://127.0.0.1:8080', $url),
                    $url, // URL original como último recurso
                ];

                foreach ($urlsFallback as $urlFallback) {
                    // Log::warning('Tentando URL fallback para download', [
                    //     'template_id' => $template->id,
                    //     'url_fallback' => $urlFallback,
                    //     'status_anterior' => $response->status()
                    // ]);

                    $response = Http::timeout(60)->get($urlFallback);
                    if ($response->successful()) {
                        break;
                    }
                }
            }

            if (! $response || ! $response->successful()) {
                // Log::error('OnlyOffice document save failed: Download error', [
                //     'template_id' => $template->id,
                //     'document_key' => $template->document_key,
                //     'http_status' => $response ? $response->status() : 'null_response',
                //     'error_type' => 'download_failed'
                // ]);
                return;
            }

            // Obter conteúdo do OnlyOffice
            $conteudo = $response->body();

            // Validar conteúdo
            if (empty($conteudo) || strlen($conteudo) < 50) {
                // Log::error('OnlyOffice document save failed: Invalid content', [
                //     'template_id' => $template->id,
                //     'document_key' => $template->document_key,
                //     'content_size_bytes' => strlen($conteudo),
                //     'error_type' => 'invalid_content'
                // ]);
                return;
            }

            // Detectar formato do conteúdo
            $formato = 'rtf'; // padrão
            if (str_starts_with($conteudo, '{\rtf')) {
                $formato = 'rtf';
            } elseif (str_starts_with($conteudo, 'PK')) {
                $formato = 'docx'; // ZIP-based format
            } elseif (str_contains($conteudo, '<html') || str_contains($conteudo, '<HTML')) {
                $formato = 'html';
            }

            // Log::debug('OnlyOffice content format detected', [
            //     'template_id' => $template->id,
            //     'detected_format' => $formato,
            //     'content_size_bytes' => strlen($conteudo)
            // ]);

            // Processar conteúdo se necessário
            $conteudoProcessado = $this->processarConteudoTemplate($conteudo, $formato, $template);

            // Salvar diretamente no banco de dados PostgreSQL
            DB::transaction(function () use ($template, $conteudoProcessado, $formato) {
                $template->update([
                    'conteudo' => $conteudoProcessado,
                    'formato' => $formato,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

                // Log::info('Template salvo no banco de dados', [
                //     'template_id' => $template->id,
                //     'formato' => $formato,
                //     'content_length' => strlen($conteudoProcessado),
                //     'updated_at' => $template->fresh()->updated_at
                // ]);

                // Limpar cache
                Cache::forget('onlyoffice_template_'.$template->id);
                Cache::forget('template_content_'.$template->id);

                // Extrair variáveis automaticamente
                $this->extrairVariaveis($template);
            });

            // Log::info('Template salvo com sucesso no banco de dados', [
            //     'template_id' => $template->id,
            //     'formato' => $formato,
            //     'content_size' => strlen($conteudoProcessado)
            // ]);

        } catch (\Exception $e) {
            // Log::error('OnlyOffice callback error', [
            //     'template_id' => $template->id,
            //     'document_key' => $template->document_key,
            //     'url' => $url,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);
        }
    }

    /**
     * Processar conteúdo do template para armazenamento no banco
     */
    private function processarConteudoTemplate(string $conteudo, string $formato, TipoProposicaoTemplate $template): string
    {
        try {
            // Para RTF, fazer correções de encoding
            if ($formato === 'rtf') {
                // Garantir que está em UTF-8
                $conteudo = mb_convert_encoding($conteudo, 'UTF-8', 'auto');

                // Corrigir caracteres especiais do RTF
                $conteudo = $this->corrigirEncodingRTF($conteudo);
            }

            // Aplicar processamento de variáveis se necessário
            if (! empty($template->conteudo)) {
                // Se já existe conteúdo no template, preservar variáveis específicas
                $conteudo = $this->preservarVariaveisTemplate($conteudo, $template);
            }

            return $conteudo;

        } catch (\Exception $e) {
            // Log::warning('Erro no processamento do conteúdo do template', [
            //     'template_id' => $template->id,
            //     'formato' => $formato,
            //     'error' => $e->getMessage()
            // ]);

            // Retornar conteúdo original se houver erro
            return $conteudo;
        }
    }

    /**
     * Preservar variáveis específicas do template
     */
    private function preservarVariaveisTemplate(string $conteudo, TipoProposicaoTemplate $template): string
    {
        // Adicionar verificação para manter variáveis importantes do sistema
        $variaveisImportantes = [
            '${cabecalho_nome_camara}',
            '${cabecalho_endereco}',
            '${rodape_texto}',
            '${numero}',
            '${ano}',
            '${ementa}',
        ];

        foreach ($variaveisImportantes as $variavel) {
            if (! str_contains($conteudo, $variavel) && str_contains($template->conteudo, $variavel)) {
                // Log::info('Preservando variável importante no template', [
                //     'template_id' => $template->id,
                //     'variavel' => $variavel
                // ]);
                // Se a variável não está no novo conteúdo mas estava no antigo, avisar
            }
        }

        return $conteudo;
    }

    /**
     * Extrair variáveis do documento
     */
    private function extrairVariaveis(TipoProposicaoTemplate $template): void
    {
        // Usar o conteúdo do banco em vez do arquivo
        if (! $template->conteudo) {
            return;
        }

        // Usar conteúdo do banco de dados
        $conteudo = $template->conteudo;

        // Buscar padrão ${variavel}
        preg_match_all('/\$\{([^}]+)\}/', $conteudo, $matches);

        $variaveis = array_unique($matches[1] ?? []);

        $template->update(['variaveis' => $variaveis]);
    }

    /**
     * Gerar documento final com dados
     */
    public function gerarDocumento(TipoProposicaoTemplate $template, array $dados): string
    {
        if (! $template->arquivo_path) {
            throw new \Exception('Template não possui arquivo');
        }

        // Carregar template
        $conteudo = Storage::get($template->arquivo_path);

        // Mapear dados padrão
        $dadosCompletos = $this->mapearDadosProposicao($dados);

        // Substituir variáveis
        foreach ($dadosCompletos as $variavel => $valor) {
            $conteudo = str_replace('${'.$variavel.'}', $valor, $conteudo);
        }

        // Salvar documento gerado
        $nomeDocumento = 'documento_'.uniqid().'.docx';
        $pathDocumento = "documentos/{$nomeDocumento}";

        Storage::put($pathDocumento, $conteudo);

        return storage_path("app/{$pathDocumento}");
    }

    /**
     * Mapear dados da proposição para variáveis
     */
    private function mapearDadosProposicao(array $dados): array
    {
        $autor = User::find($dados['autor_id']);
        $proposicaoId = $dados['id'] ?? 1;
        $ano = $dados['ano'] ?? date('Y');

        return [
            'numero_proposicao' => $dados['numero_protocolo'] ?? '[AGUARDANDO PROTOCOLO]',
            'ementa' => $dados['ementa'],
            'texto' => $dados['texto'],
            'autor_nome' => $autor->name,
            'autor_cargo' => $autor->cargo ?? 'Vereador',
            'data_atual' => now()->format('d/m/Y'),
            'ano_atual' => now()->year,
            'municipio' => config('app.municipio', 'São Paulo'),
            'camara_nome' => config('app.camara_nome', 'Câmara Municipal'),
        ];
    }

    /**
     * Garantir que o template tem um arquivo processado para visualização admin
     * Apenas processa a imagem do cabeçalho, mantendo outras variáveis como placeholders
     */
    private function garantirArquivoTemplateProcessado(TipoProposicaoTemplate $template): void
    {
        // Primeiro garantir que existe arquivo base
        $this->garantirArquivoTemplate($template);

        // Agora processar apenas a imagem para visualização no admin
        if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
            // Ler conteúdo atual
            $conteudo = Storage::get($template->arquivo_path);

            // Verificar se já foi processado (tem imagem RTF)
            if (strpos($conteudo, '\\pngblip') !== false || strpos($conteudo, '\\jpegblip') !== false) {
                // Já foi processado, não processar novamente
                return;
            }

            // Processar APENAS a imagem do cabeçalho para admin
            $conteudoProcessado = $this->processarImagemCabecalhoAdmin($conteudo);

            // Salvar arquivo processado no mesmo caminho (sobrescrever)
            Storage::put($template->arquivo_path, $conteudoProcessado);

            // Atualizar timestamp para forçar reload
            $template->touch();

            Log::info('Template processado para visualização admin (apenas imagem)', [
                'template_id' => $template->id,
                'path' => $template->arquivo_path,
                'tem_imagem' => strpos($conteudoProcessado, '\\pngblip') !== false,
                'tamanho' => strlen($conteudoProcessado),
            ]);
        }
    }

    /**
     * Processar apenas a imagem do cabeçalho para visualização admin
     * Mantém todas as outras variáveis como placeholders ${variavel}
     */
    private function processarImagemCabecalhoAdmin(string $conteudo): string
    {
        try {
            // Verificar se o conteúdo é RTF
            $isRTF = strpos($conteudo, '{\rtf') !== false;

            // Para admin, usar apenas o caminho fixo da imagem (não usar TemplateVariableService)
            $caminhoImagem = 'template/cabecalho.png';

            // Verificar se a imagem existe
            if ($caminhoImagem && file_exists(public_path($caminhoImagem))) {
                Log::info('Imagem do cabeçalho encontrada para admin', [
                    'path' => $caminhoImagem,
                    'full_path' => public_path($caminhoImagem),
                    'exists' => true,
                ]);

                // Gerar código RTF para a imagem
                $imagemRTF = $this->gerarImagemRTFAdmin(public_path($caminhoImagem));

                // Substituir APENAS a variável ${imagem_cabecalho}
                $formatosImagem = [
                    '${imagem_cabecalho}',
                ];

                if ($isRTF) {
                    // Para RTF, também verificar formato escapado
                    $formatosImagem[] = '$\\{imagem_cabecalho\\}';
                }

                foreach ($formatosImagem as $formato) {
                    if (strpos($conteudo, $formato) !== false) {
                        $conteudo = str_replace($formato, $imagemRTF, $conteudo);
                        Log::info("Variável de imagem $formato substituída por RTF no admin");
                    }
                }
            } else {
                Log::warning('Imagem do cabeçalho não encontrada para admin', [
                    'path' => $caminhoImagem,
                    'full_path' => public_path($caminhoImagem ?? ''),
                    'exists' => false,
                ]);

                // Remover apenas a variável ${imagem_cabecalho} se não existir
                $formatosImagem = ['${imagem_cabecalho}'];
                if ($isRTF) {
                    $formatosImagem[] = '$\\{imagem_cabecalho\\}';
                }
                $conteudo = str_replace($formatosImagem, '', $conteudo);
            }

            // IMPORTANTE: NÃO processar outras variáveis para admin
            // Todas as outras variáveis devem permanecer como ${variavel}

            return $conteudo;

        } catch (\Exception $e) {
            Log::error('Erro ao processar imagem do cabeçalho para admin', [
                'error' => $e->getMessage(),
            ]);

            // Em caso de erro, apenas remover a variável ${imagem_cabecalho}
            return str_replace(['${imagem_cabecalho}', '$\\{imagem_cabecalho\\}'], '', $conteudo);
        }
    }

    /**
     * Gerar código RTF para imagem (versão simplificada para admin)
     */
    private function gerarImagemRTFAdmin(string $caminhoImagem): string
    {
        try {
            // Usar o método do TemplateProcessorService via reflection
            $reflection = new \ReflectionClass($this->templateProcessorService);
            $method = $reflection->getMethod('gerarImagemRTF');
            $method->setAccessible(true);

            return $method->invoke($this->templateProcessorService, $caminhoImagem);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar imagem RTF para admin', [
                'path' => $caminhoImagem,
                'error' => $e->getMessage(),
            ]);

            return '';
        }
    }

    /**
     * Garantir que o template tem um arquivo inicial
     */
    private function garantirArquivoTemplate(TipoProposicaoTemplate $template): void
    {
        if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
            return; // Já tem arquivo
        }

        // Usar o serviço de parâmetros para gerar o template com parâmetros aplicados
        $parametrosService = app(TemplateParametrosService::class);
        $conteudoComParametros = $this->gerarTemplateComParametros($template, $parametrosService);

        // Nome e caminho do arquivo
        $nomeArquivo = "template_{$template->tipo_proposicao_id}.rtf";
        $path = "templates/{$nomeArquivo}";

        // Salvar no storage
        Storage::put($path, $conteudoComParametros);

        // Atualizar template
        $template->update(['arquivo_path' => $path]);

        // Log::info('Template arquivo criado com parâmetros', [
        //     'template_id' => $template->id,
        //     'path' => $path,
        //     'file_exists' => Storage::exists($path)
        // ]);
    }

    /**
     * Gerar template com parâmetros aplicados
     */
    private function gerarTemplateComParametros(TipoProposicaoTemplate $template, TemplateParametrosService $parametrosService): string
    {
        // Obter parâmetros do sistema
        $parametros = $parametrosService->obterParametrosTemplates();

        // Obter tipo de proposição
        $tipo = $template->tipoProposicao;

        // Gerar conteúdo do template baseado no tipo
        $template_content = $this->obterTemplateBase($tipo);

        // Os templates já vêm com as variáveis corretas, não precisa substituir placeholders

        // Verificar se deve usar imagem no cabeçalho
        $usarImagem = ! empty($parametros['Cabeçalho.cabecalho_imagem']);
        if ($usarImagem) {
            // Adicionar variável de imagem no início do template se ainda não existir
            if (strpos($template_content, '${imagem_cabecalho}') === false) {
                $template_content = '${imagem_cabecalho}'."\n\n".$template_content;
            }
        }

        // Garantir que o rodapé seja incluído no final do template
        if (strpos($template_content, '${rodape_texto}') === false) {
            $template_content = $template_content."\n\n".'${rodape_texto}';
        }

        // Aplicar parâmetros de formatação
        $fonte = $parametros['Formatação.format_fonte'] ?? 'Arial';
        $tamanhoFonte = $parametros['Formatação.format_tamanho_fonte'] ?? '12';

        // RTF com formatação básica
        $template_content = $this->aplicarFormatacaoRTF($template_content, $fonte, $tamanhoFonte);

        return $template_content;
    }

    /**
     * Obter template base por tipo de proposição
     */
    private function obterTemplateBase($tipo): string
    {
        // Templates específicos por tipo de proposição
        $templates = [
            'projeto_lei_ordinaria' => $this->getTemplateProjeto('Lei Ordinária'),
            'projeto_lei_complementar' => $this->getTemplateProjeto('Lei Complementar'),
            'indicacao' => $this->getTemplateIndicacao(),
            'mocao' => $this->getTemplateMocao(),
            'requerimento' => $this->getTemplateRequerimento(),
            'projeto_decreto_legislativo' => $this->getTemplateProjeto('Decreto Legislativo'),
            'projeto_resolucao' => $this->getTemplateProjeto('Resolução'),
        ];

        return $templates[$tipo->codigo] ?? $this->getTemplateGenerico($tipo->nome);
    }

    private function getTemplateProjeto(string $tipoNome): string
    {
        return '${cabecalho_nome_camara}
${cabecalho_endereco}
${cabecalho_telefone}

'.strtoupper($tipoNome).' Nº ${numero_proposicao}

EMENTA: ${ementa}

Art. 1º ${texto}

Art. 2º Esta Lei entra em vigor na data de sua publicação.

${municipio}, ${data_atual}.

${var_assinatura_padrao}


${rodape_texto}
';
    }

    private function getTemplateIndicacao(): string
    {
        return '${cabecalho_nome_camara}
${cabecalho_endereco}
${cabecalho_telefone}

INDICAÇÃO Nº ${numero_proposicao}

${autor_nome}

INDICA ${ementa}

Senhor Presidente,

${texto}

Sendo o que se apresenta para a elevada apreciação desta Casa Legislativa.

${municipio}, ${data_atual}.

${var_assinatura_padrao}


${rodape_texto}
';
    }

    private function getTemplateMocao(): string
    {
        return '${cabecalho_nome_camara}
${cabecalho_endereco}
${cabecalho_telefone}

MOÇÃO Nº ${numero_proposicao}

${autor_nome}

${ementa}

Senhor Presidente,

${texto}

É o que se apresenta para a elevada apreciação dos nobres Pares.

${municipio}, ${data_atual}.

${var_assinatura_padrao}


${rodape_texto}
';
    }

    private function getTemplateRequerimento(): string
    {
        return '${cabecalho_nome_camara}
${cabecalho_endereco}
${cabecalho_telefone}

REQUERIMENTO Nº ${numero_proposicao}

${autor_nome}

${ementa}

Senhor Presidente,

${texto}

Termos em que peço deferimento.

${municipio}, ${data_atual}.

${var_assinatura_padrao}


${rodape_texto}
';
    }

    private function getTemplateGenerico(string $tipoNome): string
    {
        return '${cabecalho_nome_camara}
${cabecalho_endereco}
${cabecalho_telefone}

'.strtoupper($tipoNome).' Nº ${numero_proposicao}

EMENTA: ${ementa}

${texto}

${municipio}, ${data_atual}.

${var_assinatura_padrao}


${rodape_texto}
';
    }

    private function aplicarFormatacaoRTF(string $conteudo, string $fonte, string $tamanho): string
    {
        // Converter para RTF com formatação completa e UTF-8 correto
        $rtfContent = '{\\rtf1\\ansi\\ansicpg65001\\deff0\\deflang1046';
        $rtfContent .= '{\\fonttbl{\\f0\\froman\\fcharset0 '.$fonte.';}}';
        $rtfContent .= '{\\colortbl;\\red0\\green0\\blue0;}';
        $rtfContent .= '\\viewkind4\\uc1\\pard\\cf1\\f0\\fs'.($tamanho * 2).' ';

        // Converter conteúdo para RTF com UTF-8 correto
        $conteudoRTF = $this->converterUtf8ParaRtf($conteudo);

        $rtfContent .= $conteudoRTF.'}';

        return $rtfContent;
    }

    /**
     * Converter texto UTF-8 para RTF com sequências Unicode corretas
     * Baseado na solução documentada em docs/SOLUCAO_ACENTUACAO_ONLYOFFICE.md
     */
    private function converterUtf8ParaRtf(string $texto): string
    {
        $textoProcessado = '';

        // Preservar variáveis de template antes de escapar
        $variaveisTemplate = [];
        $placeholderCounter = 0;

        // Encontrar e preservar variáveis ${...}
        $texto = preg_replace_callback('/\$\{([^}]+)\}/', function ($matches) use (&$variaveisTemplate, &$placeholderCounter) {
            $placeholder = '___TEMPLATE_VAR_'.$placeholderCounter.'___';
            $variaveisTemplate[$placeholder] = '${'.$matches[1].'}';  // NÃO escapar as chaves das variáveis
            $placeholderCounter++;

            return $placeholder;
        }, $texto);

        // Escapar caracteres especiais do RTF (depois de preservar as variáveis)
        $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);

        // Processar caractere por caractere usando funções multi-byte
        $length = mb_strlen($texto, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');  // Extrai caractere UTF-8 corretamente
            $codepoint = mb_ord($char, 'UTF-8');        // Obtém codepoint Unicode real

            if ($codepoint > 127) {
                // Gera sequência RTF Unicode correta
                $textoProcessado .= '\\u'.$codepoint.'*';
            } else {
                // Converter quebras de linha para RTF
                if ($char === "\n") {
                    $textoProcessado .= '\\par ';
                } else {
                    $textoProcessado .= $char;
                }
            }
        }

        // Restaurar variáveis de template sem escape das chaves
        foreach ($variaveisTemplate as $placeholder => $variavel) {
            $textoProcessado = str_replace($placeholder, $variavel, $textoProcessado);
        }

        return $textoProcessado;
    }

    /**
     * Gerar estrutura básica de um documento Word com cabeçalho
     */
    private function gerarDocumentoWordComCabecalho(TipoProposicaoTemplate $template): string
    {
        // Tentar usar um arquivo RTF base existente primeiro
        $possiveisArquivos = [
            storage_path('app/public/documentos/modelos/teste-container.rtf'),
            storage_path('app/public/documentos/modelos/modelo-teste.rtf'),
            storage_path('app/public/documentos/modelos/requerimento.rtf'),
            storage_path('app/public/documentos/modelos/teste.docx'),
            storage_path('app/private/documentos/modelos/teste.docx'),
        ];

        foreach ($possiveisArquivos as $arquivo) {
            if (file_exists($arquivo)) {
                // Log::info('Usando arquivo base', ['arquivo' => $arquivo]);
                return file_get_contents($arquivo);
            }
        }

        // Se não encontrar arquivo base, criar um RTF válido com referência à imagem do cabeçalho
        $conteudo = "{\rtf1\ansi\ansicpg1252\deff0\deflang1046";
        $conteudo .= "{\fonttbl{\f0\froman\fcharset0 Times New Roman;}}";
        $conteudo .= "{\colortbl;\red0\green0\blue0;}";
        $conteudo .= "\viewkind4\uc1\pard\cf1\f0\fs24";
        $conteudo .= "\par\par"; // Espaço para a imagem do cabeçalho
        $conteudo .= "{\b Imagem do Cabe\\'e7alho:} \${imagem_cabecalho}\par\par";
        $conteudo .= 'Template: '.$this->converterParaRTF($template->tipoProposicao->nome)."\par\par";
        $conteudo .= "Adicione aqui o conte\\'fado do seu template usando vari\\'e1veis como:\par";
        $conteudo .= "- \${ementa}\par";
        $conteudo .= "- \${autor_nome}\par";
        $conteudo .= "- \${data_atual}\par";
        $conteudo .= "- \${numero_proposicao}\par\par";
        $conteudo .= '}';

        // Log::info('Criando template RTF básico com cabeçalho', ['template_id' => $template->id]);
        return $conteudo;
    }

    /**
     * Converter texto UTF-8 para códigos RTF
     */
    private function converterParaRTF(string $texto): string
    {
        // Mapear caracteres acentuados para códigos RTF
        $mapeamento = [
            'á' => "\\'e1",
            'à' => "\\'e0",
            'â' => "\\'e2",
            'ã' => "\\'e3",
            'é' => "\\'e9",
            'ê' => "\\'ea",
            'í' => "\\'ed",
            'ó' => "\\'f3",
            'ô' => "\\'f4",
            'õ' => "\\'f5",
            'ú' => "\\'fa",
            'ü' => "\\'fc",
            'ç' => "\\'e7",
            'Á' => "\\'c1",
            'À' => "\\'c0",
            'Â' => "\\'c2",
            'Ã' => "\\'c3",
            'É' => "\\'c9",
            'Ê' => "\\'ca",
            'Í' => "\\'cd",
            'Ó' => "\\'d3",
            'Ô' => "\\'d4",
            'Õ' => "\\'d5",
            'Ú' => "\\'da",
            'Ü' => "\\'dc",
            'Ç' => "\\'c7",
        ];

        return str_replace(array_keys($mapeamento), array_values($mapeamento), $texto);
    }

    /**
     * Criar configuração genérica para OnlyOffice
     */
    public function criarConfiguracao(string $documentKey, string $fileName, string $downloadUrl, array $user, string $mode = 'edit'): array
    {
        $config = [
            'document' => [
                'fileType' => pathinfo($fileName, PATHINFO_EXTENSION) ?: 'docx',
                'key' => $documentKey,
                'title' => $fileName,
                'url' => $downloadUrl,
                'permissions' => [
                    'comment' => true,
                    'copy' => true,
                    'download' => true,
                    'edit' => $mode === 'edit',
                    'fillForms' => true,
                    'modifyFilter' => true,
                    'modifyContentControl' => true,
                    'review' => true,
                    'chat' => false,
                ],
            ],
            'documentType' => 'word',
            'editorConfig' => [
                'mode' => $mode,
                'user' => $user,
                'lang' => 'pt-BR',
            ],
        ];

        if ($this->jwtSecret) {
            $config['token'] = $this->gerarToken($config);
        }

        return $config;
    }

    /**
     * Obter grupo do usuário para OnlyOffice
     */
    public function obterGrupoUsuario(User $user): string
    {
        // Determinar grupo baseado nas roles do usuário
        if ($user->hasRole('ADMIN')) {
            return 'administrators';
        } elseif ($user->hasRole('VEREADOR')) {
            return 'vereadores';
        } elseif ($user->hasRole('SERVIDOR')) {
            return 'servidores';
        }

        return 'users';
    }

    /**
     * Preparar conteúdo do template: combinar imagens com variáveis se necessário
     */
    private function prepararConteudoTemplate(string $conteudo, TipoProposicaoTemplate $template): ?string
    {
        // Garantir encoding UTF-8
        if (! mb_check_encoding($conteudo, 'UTF-8')) {
            $conteudo = mb_convert_encoding($conteudo, 'UTF-8', 'auto');
        }

        // Verificar se o conteúdo atual tem variáveis
        $temVariaveis = preg_match('/\$\{[^}]+\}/', $conteudo);

        // Se tem variáveis, usar como está
        if ($temVariaveis) {
            // Log::info('Template mantém variáveis após edição', [
            //     'template_id' => $template->id,
            //     'tamanho_conteudo' => strlen($conteudo)
            // ]);
            return $conteudo;
        }

        // Se não tem variáveis, mas tem conteúdo significativo, PRESERVAR o conteúdo do usuário
        if (strlen($conteudo) > 1000) {
            // Log::info('Template sem variáveis mas com conteúdo significativo - PRESERVANDO conteúdo do usuário', [
            //     'template_id' => $template->id,
            //     'tamanho_conteudo' => strlen($conteudo)
            // ]);

            // PRESERVAR o conteúdo editado pelo usuário, não substituir
            return $conteudo;
        }

        // Conteúdo muito pequeno e sem variáveis - rejeitar
        return null;
    }

    /**
     * Combinar template com imagem + variáveis essenciais
     */
    private function combinarImagemComVariaveis(string $conteudoComImagem, TipoProposicaoTemplate $template): string
    {
        // Primeiro, tentar buscar se existem variáveis textuais óbvias no conteúdo
        // que possam ser reutilizadas ao invés de adicionar novas

        // Variáveis básicas que tentaremos inserir no conteúdo existente
        $variaveisBasicas = [
            'numero_proposicao' => '${numero_proposicao}',
            'data_atual' => '${data_atual}',
            'autor_nome' => '${autor_nome}',
            'municipio' => '${municipio}',
            'ementa' => '${ementa}',
            'texto' => '${texto}',
        ];

        $conteudoModificado = $conteudoComImagem;

        // Tentar substituir padrões comuns de texto por variáveis
        $substituicoes = [
            // Padrões de número
            '/\bN[°º]?\s*\d+\/\d+/iu' => '${numero_proposicao}',
            '/\bmoção\s+n[°º]?\s*\d+/iu' => 'MOÇÃO Nº ${numero_proposicao}',

            // Padrões de data
            '/\b\d{1,2}\/\d{1,2}\/\d{4}\b/' => '${data_atual}',

            // Padrões de município específico (se encontrar "São Paulo", trocar por variável)
            '/\bSão Paulo\b/iu' => '${municipio}',

            // Padrões de ementa e texto (mais complexo, vamos ser conservadores)
            '/\bEmenta:\s*([^\\\\]+?)\\\\par/ius' => 'EMENTA:\\par\\par${ementa}\\par',
            '/\bTexto:\s*([^\\\\]+?)\\\\par/ius' => 'TEXTO:\\par\\par${texto}\\par',
        ];

        $variaveisInseridas = 0;
        foreach ($substituicoes as $padrao => $variavel) {
            if (preg_match($padrao, $conteudoModificado)) {
                $conteudoModificado = preg_replace($padrao, $variavel, $conteudoModificado);
                $variaveisInseridas++;
            }
        }

        // Se conseguimos inserir pelo menos algumas variáveis, usar o conteúdo modificado
        if ($variaveisInseridas > 0) {
            // Log::info('Template com variáveis inseridas no conteúdo original', [
            //     'template_id' => $template->id,
            //     'variaveis_inseridas' => $variaveisInseridas,
            //     'tamanho_original' => strlen($conteudoComImagem),
            //     'tamanho_final' => strlen($conteudoModificado)
            // ]);

            return $conteudoModificado;
        }

        // Se não conseguiu inserir variáveis no conteúdo original,
        // usar a abordagem de template limpo com variáveis
        $templateComVariaveis = '{\rtf1\ansi\ansicpg1252\deff0\deflang1046 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 MOÇÃO Nº ${numero_proposicao}\par}
\par
{\qc Data: ${data_atual}\par}
{\qc Autor: ${autor_nome}\par}
{\qc Município: ${municipio}\par}
\par
\par
{\b EMENTA:\par}
\par
${ementa}
\par
\par
{\b TEXTO:\par}
\par
${texto}
\par
\par
{\qr Câmara Municipal de ${municipio}\par}
{\qr ${data_atual}\par}
}';

        // Log::info('Template substituído por versão limpa com variáveis', [
        //     'template_id' => $template->id,
        //     'tamanho_original' => strlen($conteudoComImagem),
        //     'tamanho_final' => strlen($templateComVariaveis),
        //     'motivo' => 'Não foi possível inserir variáveis no conteúdo original'
        // ]);

        return $templateComVariaveis;
    }

    /**
     * Validar se o conteúdo do template contém variáveis essenciais
     */
    private function validarConteudoTemplate(string $conteudo, TipoProposicaoTemplate $template): bool
    {
        // Verificar se não é um arquivo muito pequeno (possível erro)
        if (strlen($conteudo) < 100) {
            // Log::warning('Template muito pequeno, possível erro', [
            //     'template_id' => $template->id,
            //     'tamanho_conteudo' => strlen($conteudo)
            // ]);
            return false;
        }

        // Verificar se contém pelo menos uma variável (mais flexível)
        $temVariavel = preg_match('/\$\{[^}]+\}/', $conteudo);

        // Se não tem nenhuma variável, verificar se tem conteúdo significativo (imagens, texto, etc)
        if (! $temVariavel) {
            // Arquivo muito grande pode conter imagens e ainda ser válido
            if (strlen($conteudo) > 100000) {
                // Log::info('Template sem variáveis mas com conteúdo significativo (possíveis imagens)', [
                //     'template_id' => $template->id,
                //     'tamanho_conteudo' => strlen($conteudo)
                // ]);
                return true;
            }

            // Log::warning('Template sem variáveis e conteúdo insuficiente', [
            //     'template_id' => $template->id,
            //     'tamanho_conteudo' => strlen($conteudo)
            // ]);
            return false;
        }

        // Log::info('Template validado com sucesso', [
        //     'template_id' => $template->id,
        //     'tem_variavel' => $temVariavel,
        //     'tamanho_conteudo' => strlen($conteudo)
        // ]);

        return true;
    }

    /**
     * Fazer backup do template atual antes de sobrescrever
     */
    private function backupTemplateAtual(TipoProposicaoTemplate $template): void
    {
        try {
            if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
                $conteudoAtual = Storage::get($template->arquivo_path);

                // Criar nome do backup com timestamp
                $backupPath = str_replace('.rtf', '_backup_'.date('Y_m_d_His').'.rtf', $template->arquivo_path);

                // Salvar backup
                Storage::put($backupPath, $conteudoAtual);

                // Log::info('Backup do template criado antes da atualização', [
                //     'template_id' => $template->id,
                //     'backup_path' => $backupPath,
                //     'tamanho_original' => strlen($conteudoAtual)
                // ]);

                // Limpar backups antigos (manter apenas os 5 mais recentes)
                $this->limparBackupsAntigos($template);
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao criar backup do template', [
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Limpar backups antigos, mantendo apenas os 5 mais recentes
     */
    private function limparBackupsAntigos(TipoProposicaoTemplate $template): void
    {
        try {
            $templateBaseName = pathinfo($template->arquivo_path, PATHINFO_FILENAME);
            $templateDir = dirname($template->arquivo_path);

            // Buscar todos os backups existentes
            $arquivos = Storage::files($templateDir);
            $backupsDoTemplate = array_filter($arquivos, function ($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName.'_backup_') === 0;
            });

            // Ordenar por data de modificação (mais recente primeiro)
            usort($backupsDoTemplate, function ($a, $b) {
                return Storage::lastModified($b) - Storage::lastModified($a);
            });

            // Remover backups além dos 5 mais recentes
            if (count($backupsDoTemplate) > 5) {
                $backupsParaRemover = array_slice($backupsDoTemplate, 5);
                foreach ($backupsParaRemover as $backup) {
                    Storage::delete($backup);
                    // Log::info('Backup antigo removido', ['backup_path' => $backup]);
                }
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao limpar backups antigos', [
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Gerar JWT Token
     */
    private function gerarToken(array $data): string
    {
        return \Firebase\JWT\JWT::encode($data, $this->jwtSecret, 'HS256');
    }

    /**
     * Mapear tipo de proposição para código
     */
    private function mapearTipoProposicao(string $tipo): string
    {
        $mapeamento = [
            'Projeto de Lei' => 'projeto_lei_ordinaria',
            'Projeto de Lei Ordinária' => 'projeto_lei_ordinaria',
            'Moção' => 'mocao',
            'Requerimento' => 'requerimento',
            'Projeto de Decreto Legislativo' => 'projeto_decreto_legislativo',
            'Indicação' => 'indicacao',
            'Projeto de Resolução' => 'projeto_resolucao',
            'Projeto de Lei Complementar' => 'projeto_lei_complementar',
        ];

        return $mapeamento[$tipo] ?? $tipo;
    }

    /**
     * Corrigir encoding RTF para UTF-8 correto
     */
    private function corrigirEncodingRTF(string $conteudoRTF): string
    {
        // Detectar se o conteúdo é RTF
        if (strpos($conteudoRTF, '{\rtf') === false) {
            return $conteudoRTF; // Não é RTF, retornar como está
        }

        // Log::info('Corrigindo encoding RTF - OnlyOffice salvou caracteres UTF-8 em RTF', [
        //     'tamanho_original' => strlen($conteudoRTF),
        //     'preview_original' => substr($conteudoRTF, 0, 200)
        // ]);

        // O OnlyOffice está salvando bytes UTF-8 diretamente no RTF
        // Precisamos converter esses bytes para códigos RTF apropriados
        $conteudoCorrigido = $conteudoRTF;

        // Corrigir bytes UTF-8 específicos que aparecem no RTF
        $correcoesBinarias = [
            // í (C3 AD em UTF-8) -> \'ed em RTF
            "\xC3\xAD" => "\\'ed",
            // ã (C3 A3 em UTF-8) -> \'e3 em RTF
            "\xC3\xA3" => "\\'e3",
            // â (C3 A2 em UTF-8) -> \'e2 em RTF
            "\xC3\xA2" => "\\'e2",
            // á (C3 A1 em UTF-8) -> \'e1 em RTF
            "\xC3\xA1" => "\\'e1",
            // à (C3 A0 em UTF-8) -> \'e0 em RTF
            "\xC3\xA0" => "\\'e0",
            // é (C3 A9 em UTF-8) -> \'e9 em RTF
            "\xC3\xA9" => "\\'e9",
            // ê (C3 AA em UTF-8) -> \'ea em RTF
            "\xC3\xAA" => "\\'ea",
            // ó (C3 B3 em UTF-8) -> \'f3 em RTF
            "\xC3\xB3" => "\\'f3",
            // ô (C3 B4 em UTF-8) -> \'f4 em RTF
            "\xC3\xB4" => "\\'f4",
            // õ (C3 B5 em UTF-8) -> \'f5 em RTF
            "\xC3\xB5" => "\\'f5",
            // ú (C3 BA em UTF-8) -> \'fa em RTF
            "\xC3\xBA" => "\\'fa",
            // ç (C3 A7 em UTF-8) -> \'e7 em RTF
            "\xC3\xA7" => "\\'e7",
            // Maiúsculas
            // Í (C3 8D em UTF-8) -> \'cd em RTF
            "\xC3\x8D" => "\\'cd",
            // Ã (C3 83 em UTF-8) -> \'c3 em RTF
            "\xC3\x83" => "\\'c3",
            // Â (C3 82 em UTF-8) -> \'c2 em RTF
            "\xC3\x82" => "\\'c2",
            // Á (C3 81 em UTF-8) -> \'c1 em RTF
            "\xC3\x81" => "\\'c1",
            // À (C3 80 em UTF-8) -> \'c0 em RTF
            "\xC3\x80" => "\\'c0",
            // É (C3 89 em UTF-8) -> \'c9 em RTF
            "\xC3\x89" => "\\'c9",
            // Ê (C3 8A em UTF-8) -> \'ca em RTF
            "\xC3\x8A" => "\\'ca",
            // Ó (C3 93 em UTF-8) -> \'d3 em RTF
            "\xC3\x93" => "\\'d3",
            // Ô (C3 94 em UTF-8) -> \'d4 em RTF
            "\xC3\x94" => "\\'d4",
            // Õ (C3 95 em UTF-8) -> \'d5 em RTF
            "\xC3\x95" => "\\'d5",
            // Ú (C3 9A em UTF-8) -> \'da em RTF
            "\xC3\x9A" => "\\'da",
            // Ç (C3 87 em UTF-8) -> \'c7 em RTF
            "\xC3\x87" => "\\'c7",
        ];

        foreach ($correcoesBinarias as $utf8Bytes => $rtfCode) {
            $antes = substr_count($conteudoCorrigido, $utf8Bytes);
            $conteudoCorrigido = str_replace($utf8Bytes, $rtfCode, $conteudoCorrigido);
            $depois = substr_count($conteudoCorrigido, $utf8Bytes);

            if ($antes > $depois) {
                // Log::info("Corrigido: $antes ocorrências de bytes UTF-8 para códigos RTF", [
                //     'utf8_hex' => bin2hex($utf8Bytes),
                //     'rtf_code' => $rtfCode
                // ]);
            }
        }

        // Log::info('Encoding RTF corrigido - bytes UTF-8 convertidos para códigos RTF', [
        //     'tamanho_final' => strlen($conteudoCorrigido),
        //     'preview_final' => substr($conteudoCorrigido, 0, 200)
        // ]);

        return $conteudoCorrigido;
    }

    /**
     * Codificar caracteres UTF-8 para códigos RTF apropriados
     */
    private function codificarUTF8ParaRTF(string $texto): string
    {
        // Esta função garante que caracteres UTF-8 sejam corretamente codificados no RTF

        // Primeiro, vamos processar caractere por caractere
        $resultado = '';
        $comprimento = mb_strlen($texto, 'UTF-8');

        for ($i = 0; $i < $comprimento; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');

            // Se é ASCII básico, manter como está
            if (ord($char) < 128 && ord($char) > 31) {
                $resultado .= $char;
            }
            // Se é caractere de controle ou não-ASCII, converter para código RTF
            else {
                $codigo = $this->obterCodigoRTF($char);
                if ($codigo) {
                    $resultado .= $codigo;
                } else {
                    // Se não tem mapeamento específico, usar código genérico
                    $resultado .= $char; // Manter original por enquanto
                }
            }
        }

        return $resultado;
    }

    /**
     * Obter código RTF para caractere específico
     */
    private function obterCodigoRTF(string $char): ?string
    {
        $mapeamento = [
            'á' => "\\'e1", 'à' => "\\'e0", 'ã' => "\\'e3", 'â' => "\\'e2", 'ä' => "\\'e4",
            'Á' => "\\'c1", 'À' => "\\'c0", 'Ã' => "\\'c3", 'Â' => "\\'c2", 'Ä' => "\\'c4",
            'é' => "\\'e9", 'è' => "\\'e8", 'ê' => "\\'ea", 'ë' => "\\'eb",
            'É' => "\\'c9", 'È' => "\\'c8", 'Ê' => "\\'ca", 'Ë' => "\\'cb",
            'í' => "\\'ed", 'ì' => "\\'ec", 'î' => "\\'ee", 'ï' => "\\'ef",
            'Í' => "\\'cd", 'Ì' => "\\'cc", 'Î' => "\\'ce", 'Ï' => "\\'cf",
            'ó' => "\\'f3", 'ò' => "\\'f2", 'õ' => "\\'f5", 'ô' => "\\'f4", 'ö' => "\\'f6",
            'Ó' => "\\'d3", 'Ò' => "\\'d2", 'Õ' => "\\'d5", 'Ô' => "\\'d4", 'Ö' => "\\'d6",
            'ú' => "\\'fa", 'ù' => "\\'f9", 'û' => "\\'fb", 'ü' => "\\'fc",
            'Ú' => "\\'da", 'Ù' => "\\'d9", 'Û' => "\\'db", 'Ü' => "\\'dc",
            'ç' => "\\'e7", 'Ç' => "\\'c7",
            'ñ' => "\\'f1", 'Ñ' => "\\'d1",
            '°' => "\\'b0", 'º' => "\\'ba", 'ª' => "\\'aa",
        ];

        return $mapeamento[$char] ?? null;
    }

    /**
     * Corrigir encoding para exibição correta no OnlyOffice
     *
     * O OnlyOffice às vezes interpreta códigos RTF de forma incorreta,
     * causando double encoding. Este método tenta corrigir isso.
     */
    private function corrigirEncodingParaExibicao(string $conteudoRTF): string
    {
        // Log::info('Aplicando correção adicional de encoding para OnlyOffice', [
        //     'tamanho_original' => strlen($conteudoRTF),
        //     'preview' => substr($conteudoRTF, 0, 200)
        // ]);

        // Se não é RTF, não fazer nada
        if (strpos($conteudoRTF, '{\rtf') === false) {
            return $conteudoRTF;
        }

        $conteudoCorrigido = $conteudoRTF;

        // Estratégia: substituir códigos RTF por UTF-8 direto para algumas situações
        // onde o OnlyOffice interpreta melhor UTF-8 do que códigos RTF

        // Primeiro vamos tentar manter apenas os códigos RTF mais essenciais
        // e deixar o OnlyOffice processar o resto como UTF-8

        $substituicoes = [
            // Se encontrarmos padrões problemáticos, vamos corrigi-los
            "\\'ed" => 'í',  // í volta para UTF-8
            "\\'e3" => 'ã',  // ã volta para UTF-8
            "\\'e2" => 'â',  // â volta para UTF-8
            "\\'e7" => 'ç',  // ç volta para UTF-8
            "\\'c7" => 'Ç',  // Ç volta para UTF-8
            "\\'c3" => 'Ã',  // Ã volta para UTF-8
        ];

        foreach ($substituicoes as $rtfCode => $utf8Char) {
            $antes = substr_count($conteudoCorrigido, $rtfCode);
            $conteudoCorrigido = str_replace($rtfCode, $utf8Char, $conteudoCorrigido);
            $depois = substr_count($conteudoCorrigido, $rtfCode);

            if ($antes > $depois) {
                // Log::info("Substituição RTF->UTF8: {$rtfCode} -> {$utf8Char}", [
                //     'ocorrencias' => ($antes - $depois)
                // ]);
            }
        }

        // Log::info('Correção de encoding para OnlyOffice aplicada', [
        //     'tamanho_final' => strlen($conteudoCorrigido)
        // ]);

        return $conteudoCorrigido;
    }

    /**
     * Escapar caracteres especiais para RTF
     */
    private function escapeRtf(string $text): string
    {
        // Log para debug (apenas para textos com acentos)
        if (preg_match('/[áàãâéèêíìîóòõôúùûçÁÀÃÂÉÈÊÍÌÎÓÒÕÔÚÙÛÇ]/', $text)) {
            // Log::info('Codificando texto para Unicode RTF', [
            //     'texto_original' => mb_substr($text, 0, 100),
            //     'length' => mb_strlen($text, 'UTF-8')
            // ]);
        }

        // Converter para UTF-8 se necessário
        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
        }

        // Substituir caracteres especiais RTF
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('{', '\\{', $text);
        $text = str_replace('}', '\\}', $text);

        // Converter caracteres UTF-8 para Unicode RTF usando mb_ functions
        $resultado = '';
        $length = mb_strlen($text, 'UTF-8');
        $caracteresConvertidos = 0;

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');

            if ($codepoint > 127) {
                // Caracteres não-ASCII: converter para \uN*
                $resultado .= '\\u'.$codepoint.'*';
                $caracteresConvertidos++;
            } else {
                // Caracteres ASCII: manter como estão
                $resultado .= $char;
            }
        }

        // Log resultado (apenas para textos com acentos)
        if ($caracteresConvertidos > 0) {
            // Log::info('Texto codificado para Unicode RTF', [
            //     'amostra_resultado' => mb_substr($resultado, 0, 100),
            //     'tamanho_final' => mb_strlen($resultado),
            //     'caracteres_convertidos' => $caracteresConvertidos
            // ]);
        }

        return $resultado;
    }

    /**
     * Gerar documento com template para proposição
     */
    private function gerarDocumentoComTemplate(\App\Models\Proposicao $proposicao)
    {
        try {
            $template = $proposicao->template;

            // Verificar se o template tem arquivo
            // Log::info('Verificando arquivo do template', [
            //     'template_id' => $template->id,
            //     'arquivo_path' => $template->arquivo_path,
            //     'storage_path' => storage_path('app/' . $template->arquivo_path),
            //     'file_exists_storage' => Storage::exists($template->arquivo_path),
            //     'file_exists_direct' => file_exists(storage_path('app/' . $template->arquivo_path))
            // ]);

            if (! $template->arquivo_path) {
                // Log::warning('Template sem caminho de arquivo', [
                //     'proposicao_id' => $proposicao->id,
                //     'template_id' => $template->id
                // ]);
                return $this->gerarDocumentoRTFProposicao($proposicao);
            }

            // Verificar se o arquivo existe usando Storage
            if (! Storage::exists($template->arquivo_path)) {
                Log::warning('Arquivo de template não encontrado no storage', [
                    'proposicao_id' => $proposicao->id,
                    'template_id' => $template->id,
                    'arquivo_path' => $template->arquivo_path,
                ]);

                return $this->gerarDocumentoRTFProposicao($proposicao);
            }

            // Priorizar conteúdo do banco (editado no admin) sobre arquivo do seeder
            if (! empty($template->conteudo)) {
                // Usar conteúdo salvo no banco de dados (editado no admin)
                $conteudoTemplate = $template->conteudo;
                Log::info('Usando conteúdo do banco (template editado no admin)', [
                    'template_id' => $template->id,
                    'tamanho_banco' => strlen($template->conteudo),
                ]);
            } else {
                // Fallback: usar arquivo do seeder
                $conteudoTemplate = Storage::get($template->arquivo_path);
                Log::info('Usando conteúdo do arquivo (template do seeder)', [
                    'template_id' => $template->id,
                    'arquivo_path' => $template->arquivo_path,
                    'tamanho_arquivo' => strlen($conteudoTemplate),
                ]);
            }

            // Log::info('Template carregado', [
            //     'template_id' => $template->id,
            //     'tamanho' => strlen($conteudoTemplate),
            //     'preview' => substr($conteudoTemplate, 0, 200)
            // ]);

            // Obter dados da câmara através do serviço
            $dadosCamara = $this->obterDadosCamara();

            // Usar o TemplateProcessorService para processar o template com todas as variáveis
            Log::info('Processando template com TemplateProcessorService', [
                'proposicao_id' => $proposicao->id,
                'template_id' => $template->id,
            ]);

            // Preparar dados editáveis específicos da proposição
            $dadosEditaveis = [
                'ementa' => $proposicao->ementa ?? '',
                'texto' => $proposicao->conteudo ?? '',
                'justificativa' => $proposicao->justificativa ?? '',
                'numero_proposicao' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            ];

            $conteudoProcessado = $this->templateProcessorService->processarTemplate($template, $proposicao, $dadosEditaveis);

            Log::info('Template processado com sucesso', [
                'template_id' => $template->id,
                'conteudo_length' => strlen($conteudoProcessado),
                'preview' => substr($conteudoProcessado, 0, 300),
            ]);

            // Verificar a extensão do arquivo do template
            $extensao = pathinfo($template->arquivo_path, PATHINFO_EXTENSION);

            if ($extensao === 'rtf') {
                // Para RTF, retornar diretamente
                $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_').'.rtf';
                file_put_contents($tempFile, $conteudoProcessado);

                // Tentar converter para DOCX se possível
                try {
                    // Por enquanto, retornar como RTF mesmo
                    // TODO: Implementar conversão RTF->DOCX preservando imagens
                    return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf")
                        ->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    // Log::error('Erro ao processar documento', ['erro' => $e->getMessage()]);
                    return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf")
                        ->deleteFileAfterSend(true);
                }
            } else {
                // Para outros formatos, usar documento padrão
                return $this->gerarDocumentoRTFProposicao($proposicao);
            }

            /* CÓDIGO DESATIVADO TEMPORARIAMENTE PARA TESTE
            // Substituir variáveis no template
            $conteudoProcessado = $conteudoTemplate;
            foreach ($dados as $variavel => $valor) {
                $conteudoProcessado = str_replace('${' . $variavel . '}', $valor, $conteudoProcessado);
            }

            // Log::info('Variáveis substituídas no template', [
                //     'template_id' => $template->id,
                //     'variaveis' => array_keys($dados)
            // ]);

            // Verificar a extensão do arquivo do template
            $extensao = pathinfo($template->arquivo_path, PATHINFO_EXTENSION);

            if ($extensao === 'rtf') {
                // Para RTF, retornar diretamente
                $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_') . '.rtf';
                file_put_contents($tempFile, $conteudoProcessado);

                return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf")
                    ->deleteFileAfterSend(true);
            } else {
                // Para outros formatos, tentar converter ou usar documento padrão
                // Por enquanto, usar o documento padrão
                return $this->gerarDocumentoRTFProposicao($proposicao);
            }
            */

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar documento com template', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage()
            // ]);

            // Em caso de erro, usar documento padrão
            return $this->gerarDocumentoRTFProposicao($proposicao);
        }
    }

    /**
     * Gerar documento DOCX de uma proposição
     */
    public function gerarDocumentoProposicao(\App\Models\Proposicao $proposicao)
    {
        // Carregar relacionamentos necessários
        $proposicao->load(['template', 'autor']);

        // Log do conteúdo da proposição para debug
        // Log::info('Gerando documento para proposição', [
        //     'proposicao_id' => $proposicao->id,
        //     'tipo' => $proposicao->tipo,
        //     'template_id' => $proposicao->template_id,
        //     'arquivo_path' => $proposicao->arquivo_path,
        //     'ementa_length' => strlen($proposicao->ementa ?? ''),
        //     'conteudo_length' => strlen($proposicao->conteudo ?? ''),
        //     'has_conteudo' => !empty($proposicao->conteudo),
        //     'conteudo_preview' => $proposicao->conteudo ? substr(strip_tags($proposicao->conteudo), 0, 200) : 'VAZIO',
        //     'status' => $proposicao->status,
        //     'autor_nome' => $proposicao->autor->name ?? 'SEM AUTOR'
        // ]);

        // OTIMIZAÇÃO: Cache de verificação de arquivos para evitar múltiplas I/O
        static $cacheArquivos = [];
        $cacheKey = "prop_{$proposicao->id}_".($proposicao->ultima_modificacao ? $proposicao->ultima_modificacao->timestamp : $proposicao->updated_at->timestamp);

        if (isset($cacheArquivos[$cacheKey])) {
            $arquivoInfo = $cacheArquivos[$cacheKey];
            if ($arquivoInfo['caminho']) {
                $extensao = pathinfo($arquivoInfo['arquivo_path'], PATHINFO_EXTENSION);
                $nomeArquivo = "proposicao_{$proposicao->id}.{$extensao}";

                return response()->download($arquivoInfo['caminho'], $nomeArquivo, [
                    'Content-Type' => $this->getMimeType($extensao),
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'Last-Modified' => gmdate('D, d M Y H:i:s').' GMT',
                    'ETag' => '"'.md5($proposicao->id.'_'.($proposicao->ultima_modificacao ? $proposicao->ultima_modificacao->timestamp : time())).'"',
                ]);
            }
        }

        // PRIMEIRO: Verificar arquivo salvo mais eficientemente
        $arquivoSalvo = null;
        $caminhoCompleto = null;

        // Verificar se tem conteúdo de IA
        $temConteudoIA = ! empty($proposicao->conteudo) &&
                        $proposicao->conteudo !== 'Conteúdo a ser definido' &&
                        (str_contains($proposicao->conteudo, 'PODER LEGISLATIVO') ||
                         str_contains($proposicao->conteudo, 'CÂMARA MUNICIPAL') ||
                         str_contains($proposicao->conteudo, 'Art.'));

        // Verificação otimizada de arquivo existente
        if (! empty($proposicao->arquivo_path)) {
            // Array de caminhos possíveis ordenados por prioridade
            $caminhosPossiveis = [
                storage_path('app/'.$proposicao->arquivo_path),
                storage_path('app/private/'.$proposicao->arquivo_path),
                storage_path('app/public/'.$proposicao->arquivo_path),
            ];

            // Busca otimizada - parar na primeira encontrada
            foreach ($caminhosPossiveis as $caminho) {
                if (file_exists($caminho)) {
                    $arquivoSalvo = $proposicao->arquivo_path;
                    $caminhoCompleto = $caminho;
                    break;
                }
            }
        }

        // Cache do resultado
        $cacheArquivos[$cacheKey] = [
            'arquivo_path' => $arquivoSalvo,
            'caminho' => $caminhoCompleto,
            'tem_conteudo_ia' => $temConteudoIA,
        ];

        if ($arquivoSalvo && $caminhoCompleto) {
            Log::info('Usando arquivo salvo da proposição', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $arquivoSalvo,
                'tem_conteudo_ia' => $temConteudoIA,
            ]);

            $extensao = pathinfo($arquivoSalvo, PATHINFO_EXTENSION);
            $nomeArquivo = "proposicao_{$proposicao->id}.{$extensao}";

            return response()->download($caminhoCompleto, $nomeArquivo, [
                'Content-Type' => $this->getMimeType($extensao),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Last-Modified' => gmdate('D, d M Y H:i:s').' GMT',
                'ETag' => '"'.md5($proposicao->id.'_'.($proposicao->ultima_modificacao ? $proposicao->ultima_modificacao->timestamp : time())).'"',
            ]);
        }

        // NOVA LÓGICA: Verificar se deve usar template universal
        try {
            $tipoProposicao = \App\Models\TipoProposicao::where('codigo', $proposicao->tipo)
                ->orWhere('nome', $proposicao->tipo)
                ->orWhere('nome', 'like', '%'.$proposicao->tipo.'%')
                ->first();
        } catch (\Exception $e) {
            Log::warning('Erro ao buscar tipo de proposição, usando fallback', [
                'proposicao_id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'error' => $e->getMessage()
            ]);
            $tipoProposicao = null;
        }

        if ($tipoProposicao && $this->templateUniversalService->deveUsarTemplateUniversal($tipoProposicao)) {
            Log::info('Usando template universal para proposição', [
                'proposicao_id' => $proposicao->id,
                'tipo_proposicao' => $tipoProposicao->nome,
            ]);

            try {
                $conteudoTemplate = $this->templateUniversalService->aplicarTemplateParaProposicao($proposicao);

                // Criar arquivo temporário com o conteúdo do template universal
                $tempFile = tempnam(sys_get_temp_dir(), 'template_universal_').'.rtf';
                file_put_contents($tempFile, $conteudoTemplate);

                $nomeArquivo = "proposicao_{$proposicao->id}.rtf";

                return response()->download($tempFile, $nomeArquivo, [
                    'Content-Type' => 'application/rtf',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ])->deleteFileAfterSend(true);

            } catch (\Exception $e) {
                Log::error('Erro ao aplicar template universal', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage(),
                ]);
                // Continuar para lógica de fallback
            }
        }

        // PULAR a lógica de forçar ABNT - sempre tentar usar template primeiro
        // O template do administrador deve ter precedência
        Log::info('Tentando usar template do tipo da proposição primeiro', [
            'proposicao_id' => $proposicao->id,
            'status' => $proposicao->status,
            'tem_conteudo_ia' => $temConteudoIA,
            'tipo_proposicao' => $proposicao->tipo,
        ]);

        // Verificar se existe PHPWord
        if (! class_exists('\PhpOffice\PhpWord\PhpWord')) {
            // Se não existe, gerar documento RTF simples
            return $this->gerarDocumentoRTFProposicao($proposicao);
        }

        // Se a proposição tem um template associado, usar o documento do template como base
        if ($proposicao->template_id && $proposicao->template) {
            return $this->gerarDocumentoComTemplate($proposicao);
        }

        // Tentar buscar template pelo tipo da proposição
        // Log::info('Buscando template para tipo de proposição', [
        //     'proposicao_id' => $proposicao->id,
        //     'tipo' => $proposicao->tipo
        // ]);

        // Mapear tipos comuns para códigos
        $tipoMapeado = $this->mapearTipoProposicao($proposicao->tipo);

        try {
            $tipoProposicao = \App\Models\TipoProposicao::where('codigo', $tipoMapeado)
                ->orWhere('codigo', $proposicao->tipo)
                ->orWhere('nome', $proposicao->tipo)
                ->orWhere('nome', 'like', '%'.$proposicao->tipo.'%')
                ->first();
        } catch (\Exception $e) {
            Log::warning('Erro ao buscar tipo de proposição para template, usando fallback', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
            $tipoProposicao = null;
        }

        if ($tipoProposicao) {
            // Log::info('Tipo de proposição encontrado', [
            //     'tipo_id' => $tipoProposicao->id,
            //     'codigo' => $tipoProposicao->codigo,
            //     'nome' => $tipoProposicao->nome
            // ]);

            try {
                if ($tipoProposicao->templates()->exists()) {
                    $template = $tipoProposicao->templates()->where('ativo', true)->first();
                } else {
                    $template = null;
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao buscar templates do tipo, usando fallback', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage()
                ]);
                $template = null;
            }
            
            if ($template) {
                Log::info('Template encontrado para o tipo', [
                    'template_id' => $template->id,
                    'arquivo_path' => $template->arquivo_path,
                ]);
                $proposicao->template = $template; // Associar temporariamente para uso

                return $this->gerarDocumentoComTemplate($proposicao);
            } else {
                // Log::warning('Nenhum template ativo encontrado para o tipo', [
                //     'tipo_id' => $tipoProposicao->id
                // ]);
            }
        } else {
            // Log::warning('Tipo de proposição não encontrado', [
            //     'tipo_buscado' => $proposicao->tipo
            // ]);
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord;

        // Configurar documento
        $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::PT_BR));

        // Adicionar seção
        $section = $phpWord->addSection();

        // Adicionar título
        $section->addText(
            'PROPOSIÇÃO: '.strtoupper($proposicao->tipo),
            ['bold' => true, 'size' => 16],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $section->addTextBreak(2);

        // Adicionar informações básicas
        $section->addText('INFORMAÇÕES BÁSICAS', ['bold' => true, 'size' => 14]);
        $section->addText("Autor: {$proposicao->autor->name}");
        $section->addText('Data: '.$proposicao->created_at->format('d/m/Y'));
        $section->addText('Status: '.ucfirst(str_replace('_', ' ', $proposicao->status)));

        $section->addTextBreak(2);

        // Adicionar ementa
        $section->addText('EMENTA', ['bold' => true, 'size' => 14]);
        $section->addText($proposicao->ementa);

        $section->addTextBreak(2);

        // Adicionar conteúdo da proposição
        if (! empty($proposicao->conteudo) && $proposicao->conteudo !== 'Conteúdo a ser definido') {
            $section->addText('CONTEÚDO DA PROPOSIÇÃO', ['bold' => true, 'size' => 14]);

            // Verificar se o conteúdo contém HTML
            if (strip_tags($proposicao->conteudo) != $proposicao->conteudo) {
                // Se contém HTML, tentar converter
                try {
                    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $proposicao->conteudo);
                } catch (\Exception $e) {
                    // Se a conversão HTML falhar, usar texto limpo
                    // Log::warning('Erro ao converter HTML, usando texto limpo', ['error' => $e->getMessage()]);
                    $textoLimpo = strip_tags($proposicao->conteudo);
                    $paragrafos = explode("\n", $textoLimpo);
                    foreach ($paragrafos as $paragrafo) {
                        if (trim($paragrafo)) {
                            $section->addText(trim($paragrafo));
                        } else {
                            $section->addTextBreak();
                        }
                    }
                }
            } else {
                // É texto puro, dividir em parágrafos
                $paragrafos = explode("\n", $proposicao->conteudo);
                foreach ($paragrafos as $paragrafo) {
                    if (trim($paragrafo)) {
                        $section->addText(trim($paragrafo));
                    } else {
                        $section->addTextBreak();
                    }
                }
            }
        } else {
            $section->addText('CONTEÚDO DA PROPOSIÇÃO', ['bold' => true, 'size' => 14]);
            $section->addText('[CONTEÚDO NÃO DISPONÍVEL - Adicione o texto da proposição aqui]', ['italic' => true, 'color' => '999999']);
        }

        // Se houver observações do legislativo, adicionar
        if ($proposicao->observacoes_edicao) {
            $section->addTextBreak(2);
            $section->addText('OBSERVAÇÕES DO LEGISLATIVO', ['bold' => true, 'size' => 14]);
            $section->addText($proposicao->observacoes_edicao);
        }

        // Se a proposição estiver assinada, adicionar informações da assinatura
        if ($proposicao->status === 'assinado' && $proposicao->assinatura_digital) {
            $section->addTextBreak(3);
            $section->addText('ASSINATURA DIGITAL', ['bold' => true, 'size' => 14]);
            $section->addTextBreak(1);

            // Adicionar linha de separação
            $section->addText(str_repeat('_', 80), ['size' => 10]);
            $section->addTextBreak(1);

            // Informações da assinatura
            $section->addText('Documento assinado digitalmente por:', ['bold' => true]);
            $section->addText($proposicao->autor->name);

            if ($proposicao->data_assinatura) {
                $section->addText('Data da Assinatura: '.$proposicao->data_assinatura->format('d/m/Y H:i:s'));
            }

            // Hash da assinatura (primeiros e últimos caracteres para segurança)
            if (strlen($proposicao->assinatura_digital) > 20) {
                $hashDisplay = substr($proposicao->assinatura_digital, 0, 8).'...'.substr($proposicao->assinatura_digital, -8);
                $section->addText('Hash da Assinatura: '.$hashDisplay, ['size' => 10, 'color' => '666666']);
            }

            if ($proposicao->ip_assinatura) {
                $section->addText('IP da Assinatura: '.$proposicao->ip_assinatura, ['size' => 10, 'color' => '666666']);
            }

            $section->addTextBreak(1);
            $section->addText('Este documento foi assinado digitalmente conforme a legislação vigente.',
                ['italic' => true, 'size' => 10, 'color' => '666666']);
        }

        // Salvar documento temporário
        $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        // Retornar arquivo
        return response()->download($tempFile, "proposicao_{$proposicao->id}.docx")
            ->deleteFileAfterSend(true);
    }

    /**
     * Gerar documento DOCX usando Template Padrão ABNT
     */
    private function gerarDocumentoDOCXProposicao(\App\Models\Proposicao $proposicao)
    {
        try {
            Log::info('Gerando documento DOCX com template ABNT', [
                'proposicao_id' => $proposicao->id,
                'conteudo_length' => strlen($proposicao->conteudo ?? ''),
                'ementa' => $proposicao->ementa,
                'conteudo_preview' => $proposicao->conteudo ? substr($proposicao->conteudo, 0, 100) : 'VAZIO',
            ]);

            if (! class_exists('\PhpOffice\PhpWord\PhpWord')) {
                // Fallback para RTF se PhpWord não disponível
                return $this->gerarDocumentoRTFProposicao($proposicao);
            }

            $phpWord = new \PhpOffice\PhpWord\PhpWord;
            $section = $phpWord->addSection([
                'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
                'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
                'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
                'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            ]);

            // Estilos ABNT
            $phpWord->addFontStyle('abntTitle', ['name' => 'Times New Roman', 'size' => 14, 'bold' => true]);
            $phpWord->addFontStyle('abntHeader', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true]);
            $phpWord->addFontStyle('abntNormal', ['name' => 'Times New Roman', 'size' => 12]);
            $phpWord->addFontStyle('abntEmenta', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true]);

            $phpWord->addParagraphStyle('center', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'lineHeight' => 1.5]);
            $phpWord->addParagraphStyle('justified', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH, 'lineHeight' => 1.5]);
            $phpWord->addParagraphStyle('right', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);

            // Cabeçalho institucional
            $dadosCamara = $this->obterDadosCamara();
            $section->addText($dadosCamara['nome_oficial'], 'abntTitle', 'center');
            $section->addText($dadosCamara['endereco_linha'], 'abntNormal', 'center');
            $section->addText('Legislatura: '.$dadosCamara['legislatura'].' - Sessão: '.$dadosCamara['sessao'], 'abntNormal', 'center');
            $section->addTextBreak(2);

            // Epígrafe
            $tipoFormatado = $this->formatarTipoProposicao($proposicao->tipo);
            $numero = $proposicao->numero ?: sprintf('%04d', $proposicao->id);
            $section->addText(strtoupper($tipoFormatado).' Nº '.$numero.', DE '.date('Y'), 'abntHeader', 'center');
            $section->addTextBreak(1);

            // Ementa
            $section->addText('EMENTA:', 'abntEmenta', 'justified');
            $section->addText($proposicao->ementa ?? '', 'abntNormal', 'justified');
            $section->addTextBreak(1);

            // Preâmbulo
            $section->addText('O(A) Vereador(a) que este subscreve, no uso das atribuições que lhe confere o Regimento Interno desta Casa Legislativa, apresenta a presente proposição:', 'abntNormal', 'justified');
            $section->addTextBreak(1);

            // Processar conteúdo da IA
            if (! empty($proposicao->conteudo) && $proposicao->conteudo !== 'Conteúdo a ser definido') {
                Log::info('Processando conteúdo da IA no DOCX', [
                    'proposicao_id' => $proposicao->id,
                    'conteudo_length' => strlen($proposicao->conteudo),
                    'conteudo_preview' => substr($proposicao->conteudo, 0, 200),
                ]);
                $this->processarConteudoDOCX($proposicao->conteudo, $section, $phpWord);
            } else {
                Log::info('Usando conteúdo padrão (sem conteúdo IA)', [
                    'proposicao_id' => $proposicao->id,
                    'conteudo_atual' => $proposicao->conteudo,
                    'eh_placeholder' => $proposicao->conteudo === 'Conteúdo a ser definido',
                ]);
                $section->addText('Art. 1º [INSERIR TEXTO DA PROPOSIÇÃO]', 'abntNormal', 'justified');
                $section->addTextBreak(1);
                $section->addText('Art. 2º Esta proposição entra em vigor na data de sua aprovação.', 'abntNormal', 'justified');
            }

            $section->addTextBreak(2);

            // Data e assinatura
            $municipio = $dadosCamara['municipio'] ?: 'São Paulo';
            $section->addText($municipio.', '.date('d').' de '.$this->obterMesPortugues(date('n')).' de '.date('Y').'.', 'abntNormal', 'right');
            $section->addTextBreak(2);

            $section->addText('__________________________________', 'abntNormal', 'center');
            $section->addTextBreak(1);
            $section->addText($proposicao->autor->name ?? 'AUTOR', 'abntHeader', 'center');
            $section->addText('Vereador(a)', 'abntNormal', 'center');

            // Salvar documento temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_abnt_').'.docx';
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);

            // Log::info('Documento DOCX gerado', [
            //     'proposicao_id' => $proposicao->id,
            //     'arquivo_temp' => $tempFile,
            //     'file_size' => filesize($tempFile)
            // ]);

            // Retornar arquivo DOCX
            return response()->download($tempFile, "proposicao_{$proposicao->id}.docx", [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar DOCX direto', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            // Fallback para RTF
            return $this->gerarDocumentoRTFProposicao($proposicao);
        }
    }

    /**
     * Gerar documento RTF usando Template Padrão ABNT (fallback)
     */
    private function gerarDocumentoRTFProposicao(\App\Models\Proposicao $proposicao)
    {
        try {
            // Log::info('Gerando documento RTF com template ABNT', [
            //     'proposicao_id' => $proposicao->id,
            //     'conteudo_length' => strlen($proposicao->conteudo ?? ''),
            //     'ementa' => $proposicao->ementa
            // ]);

            // Gerar RTF direto com dados da proposição (método simplificado)
            $rtfContent = $this->gerarRTFDireto($proposicao);

            // Salvar documento RTF temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_abnt_').'.rtf';
            file_put_contents($tempFile, $rtfContent);

            // DEBUG: Salvar uma cópia para verificação
            $debugFile = storage_path('app/public/debug_proposicao_'.$proposicao->id.'.rtf');
            file_put_contents($debugFile, $rtfContent);

            // Log::info('Documento RTF direto gerado', [
            //     'proposicao_id' => $proposicao->id,
            //     'arquivo_temp' => $tempFile,
            //     'debug_file' => $debugFile,
            //     'rtf_size' => strlen($rtfContent),
            //     'rtf_preview' => substr($rtfContent, 0, 500)
            // ]);

            // Retornar conteúdo RTF diretamente
            return response($rtfContent)
                ->header('Content-Type', 'application/rtf')
                ->header('Content-Disposition', 'attachment; filename="proposicao_'.$proposicao->id.'.rtf"');

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar RTF direto', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            // Fallback para método antigo
            return $this->gerarDocumentoRTFSimples($proposicao);
        }
    }

    /**
     * Gerar RTF direto com dados da proposição
     */
    private function gerarRTFDireto(\App\Models\Proposicao $proposicao): string
    {
        // Cabeçalho RTF compatível com OnlyOffice DocumentServer
        $rtf = "{\rtf1\ansi\deff0\deflang1046
{\fonttbl{\f0\fnil\fcharset0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\viewkind4\uc1\pard\cf1\f0\fs24
";

        // Cabeçalho institucional
        $dadosCamara = $this->obterDadosCamara();
        $legislaturaCompleta = 'Legislatura: '.$dadosCamara['legislatura'].' - Sessão: '.$dadosCamara['sessao'];

        $rtf .= "{\qc\b\fs28 {$dadosCamara['nome_oficial']}\par}
{\qc {$dadosCamara['endereco_linha']}\par}
{\qc {$legislaturaCompleta}\par}
\par
\par
";

        // Epígrafe
        $tipoFormatado = $this->formatarTipoProposicao($proposicao->tipo);
        $numero = $proposicao->numero ?: sprintf('%04d', $proposicao->id);
        $rtf .= "{\qc\b\fs24 ".strtoupper($tipoFormatado)." Nº {$numero}, DE ".date('Y')."\par}
\par
";

        // Ementa
        $rtf .= "{\b EMENTA:}\par
".$this->escaparRTF($proposicao->ementa)."\par
\par

";

        // Preâmbulo
        $rtf .= "O(A) Vereador(a) que este subscreve, no uso das atribuições que lhe confere o Regimento Interno desta Casa Legislativa, apresenta a presente proposição:\par
\par

";

        // Conteúdo da IA processado
        if (! empty($proposicao->conteudo) && $proposicao->conteudo !== 'Conteúdo a ser definido') {
            $rtf .= $this->processarConteudoIA($proposicao->conteudo);
        } else {
            $rtf .= "{\b Art. 1º} [INSERIR TEXTO DA PROPOSIÇÃO]\par\par
{\b Art. 2º} Esta proposição entra em vigor na data de sua aprovação.\par\par
";
        }

        // Data e local
        $municipio = $dadosCamara['municipio'] ?: 'São Paulo';
        $rtf .= "{\qr {$municipio}, ".date('d').' de '.$this->obterMesPortugues(date('n')).' de '.date('Y').".\par}
\par\par

";

        // Assinatura
        $rtf .= "{\qc __________________________________\par}
\par
{\qc\b ".$this->escaparRTF($proposicao->autor->name ?? 'AUTOR')."\par}
{\qc Vereador(a)\par}

";

        // Justificativa em nova página
        $justificativa = $this->extrairJustificativa($proposicao->conteudo);
        if ($justificativa) {
            $rtf .= "\page

{\qc\b\fs24 JUSTIFICATIVA\par}
\par\par

".$this->escaparRTF($justificativa)."\par
\par\par

{\qr {$municipio}, ".date('d').' de '.$this->obterMesPortugues(date('n')).' de '.date('Y').".\par}
\par\par

{\qc __________________________________\par}
\par
{\qc\b ".$this->escaparRTF($proposicao->autor->name ?? 'AUTOR')."\par}
{\qc Vereador(a)\par}

";
        }

        $rtf .= '}';

        return $rtf;
    }

    /**
     * Processar conteúdo da IA para formato DOCX
     */
    private function processarConteudoDOCX(string $conteudo, $section, $phpWord)
    {
        if (empty($conteudo)) {
            return;
        }

        $linhas = explode("\n", strip_tags($conteudo));

        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if (empty($linha)) {
                continue;
            }

            // Detectar diferentes tipos de estrutura
            if (preg_match('/^\*\*Art\.?\s*(\d+).*?\*\*(.*)/', $linha, $matches)) {
                // Artigo em markdown bold
                $numero = $matches[1];
                $texto = trim($matches[2]);
                $textRun = $section->addTextRun('justified');
                $textRun->addText('Art. '.$numero.'º ', 'abntEmenta');
                $textRun->addText($texto, 'abntNormal');
                $section->addTextBreak(1);
            } elseif (preg_match('/^Art\.?\s*(\d+).*?[\.\-\s](.*)/', $linha, $matches)) {
                // Artigo normal
                $numero = $matches[1];
                $texto = trim($matches[2]);
                $textRun = $section->addTextRun('justified');
                $textRun->addText('Art. '.$numero.'º ', 'abntEmenta');
                $textRun->addText($texto, 'abntNormal');
                $section->addTextBreak(1);
            } elseif (preg_match('/^\*\*(.+?)\*\*(.*)/', $linha, $matches)) {
                // Título em bold
                $titulo = trim($matches[1]);
                $resto = trim($matches[2]);
                if ($resto) {
                    $textRun = $section->addTextRun('justified');
                    $textRun->addText($titulo.' ', 'abntEmenta');
                    $textRun->addText($resto, 'abntNormal');
                } else {
                    $section->addText($titulo, 'abntHeader', 'center');
                }
                $section->addTextBreak(1);
            } elseif (preg_match('/^\*\*?Considerando/', $linha)) {
                // Considerandos
                $texto = str_replace(['**', '*'], '', $linha);
                $textRun = $section->addTextRun('justified');
                $textRun->addText($texto, 'abntEmenta');
                $section->addTextBreak(1);
            } elseif (preg_match('/^\*\*?Resolve/', $linha)) {
                // Resolve
                $texto = str_replace(['**', '*'], '', $linha);
                $section->addText($texto, 'abntHeader', 'center');
                $section->addTextBreak(1);
            } else {
                // Texto normal
                $section->addText($linha, 'abntNormal', 'justified');
                $section->addTextBreak(1);
            }
        }
    }

    /**
     * Gerar documento RTF simples (fallback)
     */
    private function gerarDocumentoRTFSimples(\App\Models\Proposicao $proposicao)
    {
        $conteudo = $proposicao->conteudo ? str_replace("\n", "\par\n", strip_tags($proposicao->conteudo)) : '[CONTEÚDO NÃO DISPONÍVEL - Adicione o texto da proposição aqui]';

        $rtf = "{\rtf1\ansi\deff0 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24
{\qc\b\fs28 PROPOSIÇÃO: ".strtoupper($proposicao->tipo)."\par}
\par
{\b INFORMAÇÕES BÁSICAS:\par}
Autor: {$proposicao->autor->name}\par
Data: ".$proposicao->created_at->format('d/m/Y')."\par
Status: ".ucfirst(str_replace('_', ' ', $proposicao->status))."\par
\par
{\b EMENTA:\par}
{$proposicao->ementa}\par
\par
{\b CONTEÚDO DA PROPOSIÇÃO:\par}
".$conteudo."\par";

        if ($proposicao->observacoes_edicao) {
            $rtf .= "\n\par
{\b OBSERVAÇÕES DO LEGISLATIVO:\par}
{$proposicao->observacoes_edicao}\par";
        }

        // Se a proposição estiver assinada, adicionar informações da assinatura
        if ($proposicao->status === 'assinado' && $proposicao->assinatura_digital) {
            $rtf .= "\n\par\par\par
{\b ASSINATURA DIGITAL:\par}
\par
".str_repeat('_', 80)."\par
\par
{\b Documento assinado digitalmente por:\par}
{$proposicao->autor->name}\par";

            if ($proposicao->data_assinatura) {
                $rtf .= 'Data da Assinatura: '.$proposicao->data_assinatura->format('d/m/Y H:i:s')."\par";
            }

            // Hash da assinatura (primeiros e últimos caracteres para segurança)
            if (strlen($proposicao->assinatura_digital) > 20) {
                $hashDisplay = substr($proposicao->assinatura_digital, 0, 8).'...'.substr($proposicao->assinatura_digital, -8);
                $rtf .= "{\fs20\cf1 Hash da Assinatura: {$hashDisplay}\par}";
            }

            if ($proposicao->ip_assinatura) {
                $rtf .= "{\fs20\cf1 IP da Assinatura: {$proposicao->ip_assinatura}\par}";
            }

            $rtf .= "\par
{\i\fs20\cf1 Este documento foi assinado digitalmente conforme a legislação vigente.\par}";
        }

        $rtf .= "\n}";

        // Salvar arquivo temporário
        $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_').'.rtf';
        file_put_contents($tempFile, $rtf);

        // Retornar arquivo
        return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf")
            ->deleteFileAfterSend(true);
    }

    /**
     * Converter HTML do template ABNT para RTF
     */
    private function converterHtmlParaRTF(string $htmlContent, \App\Models\Proposicao $proposicao): string
    {
        // Se o HTML contém as tags do template ABNT, extrair conteúdo estruturado
        if (str_contains($htmlContent, 'class="epigrafe"')) {
            // Extrair conteúdo das tags HTML principais
            $titulo = $this->extrairConteudoTag($htmlContent, 'epigrafe');
            $ementa = $this->extrairConteudoTag($htmlContent, 'ementa');
            $preambulo = $this->extrairConteudoTag($htmlContent, 'preambulo');
            $articulado = $this->extrairConteudoTag($htmlContent, 'articulado');
            $justificativa = $this->extrairConteudoTag($htmlContent, 'justificativa-texto');
            $assinatura = $this->extrairConteudoTag($htmlContent, 'autor-nome');
            $cargo = $this->extrairConteudoTag($htmlContent, 'autor-cargo');
            $dataLocal = $this->extrairConteudoTag($htmlContent, 'data-local');
        } else {
            // HTML simples ou texto - usar dados da proposição
            $titulo = strtoupper($this->formatarTipoProposicao($proposicao->tipo)).' Nº '.
                     ($proposicao->numero ?: sprintf('%04d', $proposicao->id)).', DE '.date('Y');
            $ementa = $proposicao->ementa;
            $preambulo = 'O(A) Vereador(a) que este subscreve, no uso das atribuições que lhe confere o Regimento Interno desta Casa Legislativa, apresenta a presente proposição:';

            // Processar conteúdo da IA como articulado
            $articulado = $this->processarConteudoIA($proposicao->conteudo);
            $justificativa = $this->extrairJustificativa($proposicao->conteudo);
            $assinatura = $proposicao->autor->name ?? '';
            $cargo = 'Vereador(a)';

            // Obter dados da câmara
            $dadosCamara = $this->obterDadosCamara();
            $municipio = $dadosCamara['municipio'] ?: 'São Paulo';
            $dataLocal = $municipio.', '.date('d').' de '.$this->obterMesPortugues(date('n')).' de '.date('Y').'.';

            // Log do processamento para debug
            // Log::info('Processamento conteúdo IA para RTF', [
            //     'proposicao_id' => $proposicao->id,
            //     'titulo' => $titulo,
            //     'ementa_length' => strlen($ementa),
            //     'articulado_length' => strlen($articulado),
            //     'justificativa_length' => strlen($justificativa),
            //     'assinatura' => $assinatura,
            //     'conteudo_original_preview' => substr($proposicao->conteudo, 0, 200)
            // ]);
        }

        // Cabeçalho institucional
        $cabecalho = $this->obterParametroCabecalho();

        // Construir RTF seguindo normas ABNT
        $rtf = "{\rtf1\ansi\deff0 
{\fonttbl 
{\f0\froman\fcharset0 Times New Roman;}
{\f1\fswiss\fcharset0 Arial;}
}
{\colortbl;\red0\green0\blue0;}
\margl1701\margr1134\margt1701\margb1134
\f0\fs24
";

        // Cabeçalho (centralizado)
        if ($cabecalho) {
            $rtf .= "{\qc\b\fs28 ".$this->escaparRTF($cabecalho)."\par}
\par\par
";
        }

        // Epígrafe (centralizado, caixa alta)
        if ($titulo) {
            $rtf .= "{\qc\b\fs24\caps ".$this->escaparRTF(strip_tags($titulo))."\par}
\par
";
        }

        // Ementa
        if ($ementa) {
            $rtf .= "{\b EMENTA:}\par
".$this->escaparRTF(strip_tags($ementa))."\par
\par
";
        }

        // Preâmbulo
        if ($preambulo) {
            $rtf .= $this->escaparRTF(strip_tags($preambulo))."\par
\par
";
        }

        // Articulado (processar estrutura de artigos)
        if ($articulado) {
            $artigosProcessados = $this->processarArticulado($articulado);
            $rtf .= $artigosProcessados."\par
";
        }

        // Data e local (alinhado à direita)
        if ($dataLocal) {
            $rtf .= "{\qr ".$this->escaparRTF(strip_tags($dataLocal))."\par}
\par\par
";
        }

        // Assinatura (centralizada)
        $rtf .= "{\qc __________________________________\par}
\par
";
        if ($assinatura) {
            $rtf .= "{\qc\b ".$this->escaparRTF(strip_tags($assinatura))."\par}
";
        }
        if ($cargo) {
            $rtf .= "{\qc ".$this->escaparRTF(strip_tags($cargo))."\par}
";
        }

        // Nova página para justificativa
        if ($justificativa) {
            $rtf .= "\page
{\qc\b\fs24 JUSTIFICATIVA\par}
\par\par
".$this->escaparRTF(strip_tags($justificativa))."\par
\par\par
";

            // Data e assinatura da justificativa
            if ($dataLocal) {
                $rtf .= "{\qr ".$this->escaparRTF(strip_tags($dataLocal))."\par}
\par\par
";
            }

            $rtf .= "{\qc __________________________________\par}
\par
";
            if ($assinatura) {
                $rtf .= "{\qc\b ".$this->escaparRTF(strip_tags($assinatura))."\par}
";
            }
            if ($cargo) {
                $rtf .= "{\qc ".$this->escaparRTF(strip_tags($cargo))."\par}
";
            }
        }

        $rtf .= '}';

        return $rtf;
    }

    /**
     * Extrair conteúdo de uma tag HTML por classe
     */
    private function extrairConteudoTag(string $html, string $classe): string
    {
        if (preg_match('/<[^>]*class=["\'][^"\']*'.$classe.'[^"\']*["\'][^>]*>(.*?)<\/[^>]*>/s', $html, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    /**
     * Processar articulado HTML para RTF
     */
    private function processarArticulado(string $articulado): string
    {
        $rtf = '';

        // Encontrar todas as divs de artigo
        if (preg_match_all('/<div[^>]*class=["\'][^"\']*artigo[^"\']*["\'][^>]*>(.*?)<\/div>/s', $articulado, $matches)) {
            foreach ($matches[1] as $artigo) {
                // Extrair número do artigo
                if (preg_match('/<span[^>]*class=["\'][^"\']*artigo-numero[^"\']*["\'][^>]*>(.*?)<\/span>/s', $artigo, $numeroMatch)) {
                    $numero = strip_tags($numeroMatch[1]);
                    $texto = str_replace($numeroMatch[0], '', $artigo);
                    $texto = strip_tags($texto);

                    $rtf .= "{\b ".$this->escaparRTF($numero).'} '.$this->escaparRTF(trim($texto))."\par
\par
";
                } else {
                    // Artigo sem numeração específica
                    $rtf .= $this->escaparRTF(strip_tags($artigo))."\par
\par
";
                }
            }
        }

        return $rtf;
    }

    /**
     * Escapar texto para RTF (versão simplificada)
     */
    private function escaparRTF(string $texto): string
    {
        // Primeiro escapar caracteres especiais RTF
        $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);

        // Remover caracteres problemáticos e manter acentos simples
        $texto = str_replace(['"', "'", "'", '"', '"'], ['"', "'", "'", '"', '"'], $texto);

        // Para OnlyOffice, é melhor manter UTF-8 simples
        return $texto;
    }

    /**
     * Obter parâmetro do cabeçalho
     */
    private function obterParametroCabecalho(): string
    {
        try {
            $parametroService = app(\App\Services\Parametro\ParametroService::class);
            $nomeCamara = $parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'nome_camara');
            $municipio = $parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'municipio');
            $endereco = $parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'endereco_camara');

            $cabecalho = '';
            if ($nomeCamara) {
                $cabecalho .= strtoupper($nomeCamara);
            }
            if ($endereco) {
                $cabecalho .= "\par".$endereco;
            }
            if ($municipio && ! str_contains($cabecalho, $municipio)) {
                $cabecalho .= "\par".$municipio;
            }

            return $cabecalho;

        } catch (\Exception $e) {
            // Log::warning('Erro ao obter parâmetros do cabeçalho', [
            //     'error' => $e->getMessage()
            // ]);

            return 'CÂMARA MUNICIPAL';
        }
    }

    /**
     * Processar conteúdo da IA para RTF estruturado
     */
    private function processarConteudoIA(string $conteudo): string
    {
        if (empty($conteudo)) {
            // Log::warning('Conteúdo IA vazio para processamento RTF');
            return '';
        }

        // Log::info('Iniciando processamento conteúdo IA', [
        //     'conteudo_length' => strlen($conteudo),
        //     'conteudo_preview' => substr($conteudo, 0, 300)
        // ]);

        $rtf = '';
        $linhas = explode("\n", strip_tags($conteudo));
        $numeroArtigo = 1;

        // Log::info('Conteúdo IA dividido em linhas', [
        //     'total_linhas' => count($linhas),
        //     'linhas_nao_vazias' => count(array_filter($linhas, fn($l) => !empty(trim($l))))
        // ]);

        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if (empty($linha)) {
                continue;
            }

            // Detectar diferentes tipos de estrutura
            if (preg_match('/^\*\*Art\.?\s*(\d+).*?\*\*(.*)/', $linha, $matches)) {
                // Artigo em markdown bold
                $numero = $matches[1];
                $texto = trim($matches[2]);
                $rtf .= "{\b Art. {$numero}º} ".$this->escaparRTF($texto)."\par\par";
            } elseif (preg_match('/^Art\.?\s*(\d+).*?[\.\-\s](.*)/', $linha, $matches)) {
                // Artigo normal
                $numero = $matches[1];
                $texto = trim($matches[2]);
                $rtf .= "{\b Art. {$numero}º} ".$this->escaparRTF($texto)."\par\par";
            } elseif (preg_match('/^\*\*(.+?)\*\*(.*)/', $linha, $matches)) {
                // Título em bold
                $titulo = trim($matches[1]);
                $resto = trim($matches[2]);
                if ($resto) {
                    $rtf .= "{\b ".$this->escaparRTF($titulo).'} '.$this->escaparRTF($resto)."\par\par";
                } else {
                    $rtf .= "{\qc\b ".$this->escaparRTF($titulo)."\par}\par";
                }
            } elseif (preg_match('/^\*\*?Considerando/', $linha)) {
                // Considerandos
                $texto = str_replace(['**', '*'], '', $linha);
                $rtf .= "{\b ".$this->escaparRTF($texto)."}\par\par";
            } elseif (preg_match('/^\*\*?Resolve/', $linha)) {
                // Resolve
                $texto = str_replace(['**', '*'], '', $linha);
                $rtf .= "{\b ".$this->escaparRTF($texto)."}\par\par";
            } else {
                // Texto normal - verificar se deve ser tratado como artigo
                if (strlen($linha) > 50 && ! str_starts_with($linha, '**') && $numeroArtigo <= 10) {
                    $rtf .= "{\b Art. {$numeroArtigo}º} ".$this->escaparRTF($linha)."\par\par";
                    $numeroArtigo++;
                } else {
                    $rtf .= $this->escaparRTF($linha)."\par\par";
                }
            }
        }

        // Log::info('Processamento conteúdo IA finalizado', [
        //     'rtf_length' => strlen($rtf),
        //     'rtf_preview' => substr($rtf, 0, 200)
        // ]);

        return $rtf;
    }

    /**
     * Extrair justificativa do conteúdo da IA
     */
    private function extrairJustificativa(string $conteudo): string
    {
        if (empty($conteudo)) {
            return 'A presente proposição tem como objetivo atender às necessidades da população e promover o bem comum, conforme preceitos constitucionais e legais vigentes.';
        }

        // Procurar por seção de justificativa explícita
        if (preg_match('/(?:JUSTIFICATIV|justificativ)[^:]*:?\s*(.+?)(?:\n\s*\n|$)/si', $conteudo, $matches)) {
            return strip_tags(trim($matches[1]));
        }

        // Procurar por considerandos que podem servir como justificativa
        if (preg_match_all('/\*\*?Considerando\*\*?\s*(.+?)(?=\*\*?Considerando|\*\*?Resolve|\*\*?Art\.|\n\s*\n|$)/si', $conteudo, $matches)) {
            $considerandos = array_map('strip_tags', $matches[1]);
            $considerandos = array_map('trim', $considerandos);

            return 'A presente proposição justifica-se pelos seguintes considerandos: '.implode(' ', $considerandos);
        }

        // Fallback: usar as primeiras linhas como justificativa
        $linhas = explode("\n", strip_tags($conteudo));
        $primeirasLinhas = array_slice($linhas, 0, 3);
        $primeirasLinhas = array_filter($primeirasLinhas, fn ($l) => ! empty(trim($l)));

        if (! empty($primeirasLinhas)) {
            return 'A presente proposição visa '.implode(' ', array_map('trim', $primeirasLinhas));
        }

        return 'A presente proposição tem como objetivo atender às necessidades da população e promover o bem comum.';
    }

    /**
     * Obter mês em português
     */
    private function obterMesPortugues(int $mes): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        return $meses[$mes] ?? 'mês inválido';
    }

    /**
     * Formatar tipo de proposição
     */
    private function formatarTipoProposicao(string $tipo): string
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'mocao' => 'Moção',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução',
            'requerimento' => 'Requerimento',
            'emenda' => 'Emenda',
        ];

        return $tipos[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
    }

    /**
     * Processar callback do OnlyOffice para uma proposição
     */
    public function processarCallbackProposicao(\App\Models\Proposicao $proposicao, string $documentKey, array $data): array
    {
        $status = $data['status'] ?? 0;

        // Status 2 = documento salvo e pronto para download
        if ($status == 2 && isset($data['url'])) {
            // AUDITORIA: Capturar estado anterior para histórico
            $estadoAnterior = [
                'arquivo_path' => $proposicao->arquivo_path,
                'conteudo' => $proposicao->conteudo,
                'ultima_modificacao' => $proposicao->ultima_modificacao,
            ];

            try {
                $originalUrl = $data['url'];

                // Converter URL do OnlyOffice para acesso entre containers
                $urlOtimizada = $originalUrl;
                if (config('app.env') === 'local') {
                    // Laravel precisa acessar OnlyOffice usando nome do container
                    $urlOtimizada = str_replace(['http://localhost:8080', 'http://127.0.0.1:8080'], 'http://legisinc-onlyoffice', $originalUrl);
                }

                Log::info('OnlyOffice callback - tentando baixar documento', [
                    'proposicao_id' => $proposicao->id,
                    'original_url' => $originalUrl,
                    'optimized_url' => $urlOtimizada,
                ]);

                // OTIMIZAÇÃO: Download assíncrono com timeout menor
                $downloadStart = microtime(true);
                $response = Http::timeout(30) // Reduzir de 60s para 30s
                    ->withOptions([
                        'stream' => true, // Stream para arquivos grandes
                        'verify' => false, // Disable SSL verification for internal network
                    ])
                    ->get($urlOtimizada);

                $downloadTime = microtime(true) - $downloadStart;

                Log::info('OnlyOffice callback - download concluído', [
                    'proposicao_id' => $proposicao->id,
                    'download_time_seconds' => round($downloadTime, 2),
                    'response_successful' => $response->successful(),
                    'response_status' => $response->status(),
                ]);

                if (! $response->successful()) {
                    Log::error('Erro ao baixar documento do OnlyOffice', [
                        'proposicao_id' => $proposicao->id,
                        'original_url' => $originalUrl,
                        'optimized_url' => $urlOtimizada,
                        'status' => $response->status(),
                    ]);

                    return ['error' => 1];
                }

                // OTIMIZAÇÃO: Detecção de tipo mais eficiente
                $fileType = $data['filetype'] ?? 'rtf';
                if (str_contains($originalUrl, '.docx')) {
                    $fileType = 'docx';
                }

                // IMPORTANTE: Usar timestamp atual para garantir arquivo único
                // Isso permite rastrear todas as edições
                $timestamp = time();
                $nomeArquivo = "proposicoes/proposicao_{$proposicao->id}_{$timestamp}.{$fileType}";

                // OTIMIZAÇÃO: Verificação de diretório apenas uma vez
                static $diretorios_criados = [];
                if (! isset($diretorios_criados['proposicoes'])) {
                    if (! Storage::disk('local')->exists('proposicoes')) {
                        Storage::disk('local')->makeDirectory('proposicoes');
                    }
                    $diretorios_criados['proposicoes'] = true;
                }

                // OTIMIZAÇÃO: Salvar arquivo e extrair conteúdo em paralelo quando possível
                $documentBody = $response->body();
                Storage::disk('local')->put($nomeArquivo, $documentBody);

                // OTIMIZAÇÃO: Sempre extrair conteúdo para manter sincronização
                $conteudoExtraido = '';

                // Sempre extrair conteúdo quando há um novo arquivo do OnlyOffice
                if ($fileType === 'docx') {
                    $conteudoExtraido = $this->extrairConteudoDocumento($documentBody);
                } else {
                    $conteudoExtraido = $this->extrairConteudoRTF($documentBody);
                }

                Log::info('Conteúdo extraído do documento', [
                    'proposicao_id' => $proposicao->id,
                    'file_type' => $fileType,
                    'content_length' => strlen($conteudoExtraido),
                    'content_preview' => substr($conteudoExtraido, 0, 100),
                ]);

                // OTIMIZAÇÃO: Update mais eficiente - apenas campos necessários
                $updateData = [
                    'arquivo_path' => $nomeArquivo,
                    'ultima_modificacao' => now(),
                ];

                // Adicionar modificado_por apenas se usuário autenticado
                if (auth()->id()) {
                    $updateData['modificado_por'] = auth()->id();
                }

                // Se conseguiu extrair conteúdo, sempre atualizar
                if (! empty($conteudoExtraido)) {
                    $updateData['conteudo'] = $conteudoExtraido;
                    Log::info('Conteúdo será atualizado na proposição', [
                        'proposicao_id' => $proposicao->id,
                        'content_length' => strlen($conteudoExtraido),
                    ]);
                }

                // OTIMIZAÇÃO: Update sem recarregar relações desnecessárias
                $proposicao->updateQuietly($updateData); // Sem disparar eventos

                // AUDITORIA: Registrar histórico da alteração
                try {
                    \App\Models\ProposicaoHistorico::registrarCallbackOnlyOffice(
                        $proposicao,
                        $estadoAnterior['arquivo_path'],
                        $nomeArquivo,
                        $estadoAnterior['conteudo'],
                        $conteudoExtraido ?: $proposicao->conteudo,
                        [
                            'document_key' => $documentKey,
                            'callback_status' => $status,
                            'original_url' => $originalUrl,
                            'file_type' => $fileType,
                            'download_time_seconds' => round($downloadTime, 2),
                            'should_extract_content' => true, // Sempre extraindo agora
                            'content_extracted' => ! empty($conteudoExtraido),
                        ]
                    );

                    Log::info('Histórico de alteração registrado', [
                        'proposicao_id' => $proposicao->id,
                        'arquivo_anterior' => $estadoAnterior['arquivo_path'],
                        'arquivo_novo' => $nomeArquivo,
                    ]);
                } catch (\Exception $historicoException) {
                    // Não bloquear o callback por erro no histórico
                    Log::warning('Erro ao registrar histórico de alteração', [
                        'proposicao_id' => $proposicao->id,
                        'error' => $historicoException->getMessage(),
                    ]);
                }

                Log::info('Arquivo e conteúdo atualizados com sucesso', [
                    'proposicao_id' => $proposicao->id,
                    'arquivo_salvo' => $nomeArquivo,
                    'conteudo_atualizado' => ! empty($conteudoExtraido),
                    'conteudo_length' => strlen($conteudoExtraido ?? ''),
                ]);

                // Log::info('Proposição atualizada com sucesso via OnlyOffice', [
                //     'proposicao_id' => $proposicao->id,
                //     'arquivo_path' => $nomeArquivo,
                //     'conteudo_length' => strlen($conteudo)
                // ]);

            } catch (\Exception $e) {
                Log::error('Erro ao processar callback do OnlyOffice para proposição', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return ['error' => 1];
            }
        }

        return ['error' => 0];
    }

    /**
     * Extrair conteúdo de texto de um documento
     */
    private function extrairConteudoDocumento(string $documentContent): string
    {
        // Salvar temporariamente para processar
        $tempFile = tempnam(sys_get_temp_dir(), 'doc_extract_');
        file_put_contents($tempFile, $documentContent);

        try {
            // Verificar se é DOCX
            if (class_exists('\PhpOffice\PhpWord\IOFactory')) {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($tempFile);
                $sections = $phpWord->getSections();

                $extractedContent = '';
                foreach ($sections as $section) {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        if (method_exists($element, 'getText')) {
                            $extractedContent .= $element->getText()."\n";
                        }
                    }
                }

                return $extractedContent;
            }

            // Se não conseguir extrair com PHPWord, tentar como RTF
            $content = file_get_contents($tempFile);

            // Remover tags RTF básicas
            $content = preg_replace('/\{\\[^{}]*\}/', '', $content);
            $content = preg_replace('/[\{\}]/', '', $content);
            $content = str_replace('\par', "\n", $content);

            return trim($content);

        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Ajustar callback URL para comunicação entre containers
     */
    private function ajustarCallbackUrl(string $callbackUrl): string
    {
        if (config('app.env') === 'local') {
            return str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }

        return $callbackUrl;
    }

    /**
     * Inserir número de processo no documento
     */
    public function inserirNumeroProcesso(\App\Models\Proposicao $proposicao, string $posicao = 'cabecalho'): void
    {
        // Log::info('Inserindo número de processo no documento', [
        //     'proposicao_id' => $proposicao->id,
        //     'numero_protocolo' => $proposicao->numero_protocolo,
        //     'posicao' => $posicao
        // ]);

        try {
            // Atualizar o conteúdo da proposição com o número do processo
            $conteudo = $proposicao->conteudo;
            $numeroProcesso = $proposicao->numero_protocolo;

            // Adicionar número baseado na posição configurada
            switch ($posicao) {
                case 'cabecalho':
                    // Adicionar no início do documento
                    $textoNumero = "PROCESSO Nº {$numeroProcesso}\n\n";
                    $conteudo = $textoNumero.$conteudo;
                    break;

                case 'rodape':
                    // Adicionar no final do documento
                    $textoNumero = "\n\nPROCESSO Nº {$numeroProcesso}";
                    $conteudo = $conteudo.$textoNumero;
                    break;

                case 'primeira_pagina':
                    // Adicionar no canto superior direito (simulado com espaços)
                    $textoNumero = str_pad("PROCESSO Nº {$numeroProcesso}", 80, ' ', STR_PAD_LEFT)."\n\n";
                    $conteudo = $textoNumero.$conteudo;
                    break;

                case 'marca_dagua':
                    // Marca d'água não é possível em texto puro, adicionar como cabeçalho
                    $textoNumero = "[PROCESSO Nº {$numeroProcesso}]\n\n";
                    $conteudo = $textoNumero.$conteudo;
                    break;
            }

            // Atualizar conteúdo
            $proposicao->update(['conteudo' => $conteudo]);

            // Regenerar PDF se existir
            if ($proposicao->arquivo_pdf_path) {
                $this->regenerarPDFComProtocolo($proposicao);
            }

            // Log::info('Número de processo inserido com sucesso', [
            //     'proposicao_id' => $proposicao->id,
            //     'numero_protocolo' => $numeroProcesso
            // ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao inserir número de processo no documento', [
            //     'proposicao_id' => $proposicao->id,
            //     'numero_protocolo' => $proposicao->numero_protocolo,
            //     'error' => $e->getMessage()
            // ]);

            throw $e;
        }
    }

    /**
     * Extrair conteúdo de texto limpo de um documento RTF
     */
    private function extrairConteudoRTF(string $rtfContent): string
    {
        try {
            // Log para debug
            Log::info('Iniciando extração de conteúdo RTF', [
                'content_length' => strlen($rtfContent),
                'preview' => substr($rtfContent, 0, 200)
            ]);

            // Primeira etapa: processar caracteres Unicode RTF ANTES de remover controles
            // Padrão mais abrangente para capturar \uN e \uN*
            $content = preg_replace_callback('/\\\\u(-?[0-9]+)\\*?/', function ($matches) {
                $codepoint = intval($matches[1]);
                // Lidar com números negativos (complemento de 2^16)
                if ($codepoint < 0) {
                    $codepoint = 65536 + $codepoint;
                }
                return mb_chr($codepoint);
            }, $rtfContent);

            // Converter quebras de linha RTF para newlines
            $content = str_replace(['\\par ', '\\par', '\\line'], "\n", $content);
            
            // Remover cabeçalho RTF e metadados
            $content = preg_replace('/^{\\\\rtf[^}]*}/', '', $content);
            $content = preg_replace('/{\\\\\\*\\\\[^}]+}/', '', $content); // Remove grupos especiais
            
            // Remover comandos RTF restantes mas preservar o texto
            $content = preg_replace('/\\\\[a-z]+(-?[0-9]+)?[ ]?/', '', $content);
            
            // Remover chaves de agrupamento
            $content = str_replace(['{', '}'], '', $content);
            
            // Remover barras invertidas isoladas
            $content = str_replace('\\', '', $content);
            
            // Processar caracteres especiais RTF
            $replacements = [
                '\~' => ' ',     // Espaço não quebrável
                '\-' => '',      // Hífen opcional
                '\_' => '-',     // Hífen não quebrável
                '\ldblquote' => '"',
                '\rdblquote' => '"',
                '\lquote' => "'",
                '\rquote' => "'",
                '\bullet' => '•',
                '\endash' => '–',
                '\emdash' => '—',
            ];
            
            foreach ($replacements as $rtfChar => $replacement) {
                $content = str_replace($rtfChar, $replacement, $content);
            }
            
            // Limpar múltiplas quebras de linha consecutivas
            $content = preg_replace("/\n{3,}/", "\n\n", $content);
            
            // Limpar espaços extras mas preservar estrutura
            $content = preg_replace('/[ \t]+/', ' ', $content); // Múltiplos espaços para um
            $content = preg_replace('/\n[ \t]+/', "\n", $content); // Remover espaços no início de linhas
            
            // Remover caracteres de controle exceto newlines
            $content = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/', '', $content);
            
            // Trim final
            $content = trim($content);
            
            Log::info('Conteúdo RTF extraído com sucesso', [
                'final_length' => strlen($content),
                'preview' => substr($content, 0, 200)
            ]);

            // Se não conseguiu extrair nada significativo, retornar vazio
            if (strlen($content) < 10) {
                Log::warning('Conteúdo extraído muito curto', [
                    'length' => strlen($content),
                    'content' => $content
                ]);
                return '';
            }

            return $content;

        } catch (\Exception $e) {
            Log::error('Erro ao extrair conteúdo RTF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return '';
        }
    }

    /**
     * Regenerar PDF com número de protocolo após protocolação
     */
    public function regenerarPDFComProtocolo(\App\Models\Proposicao $proposicao): void
    {
        // Log::info('Regenerando PDF com número de protocolo', [
        //     'proposicao_id' => $proposicao->id,
        //     'numero_protocolo' => $proposicao->numero_protocolo,
        //     'numero_protocolo' => $proposicao->numero_protocolo,
        //     'arquivo_pdf_path_atual' => $proposicao->arquivo_pdf_path
        // ]);

        // Recarregar a proposição para ter os dados atualizados
        $proposicao->refresh();
        $proposicao->load(['template', 'autor']);

        // Usar o serviço de conversão já existente do ProposicaoController
        $proposicaoController = app(\App\Http\Controllers\ProposicaoController::class);

        // Utilizar reflexão para chamar o método privado de conversão
        $reflection = new \ReflectionClass($proposicaoController);
        $method = $reflection->getMethod('converterProposicaoParaPDF');
        $method->setAccessible(true);

        try {
            $method->invoke($proposicaoController, $proposicao);

            // Log::info('PDF regenerado com sucesso', [
            //     'proposicao_id' => $proposicao->id,
            //     'numero_protocolo' => $proposicao->numero_protocolo,
            //     'numero_protocolo' => $proposicao->numero_protocolo,
            //     'novo_arquivo_pdf_path' => $proposicao->fresh()->arquivo_pdf_path
            // ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao regenerar PDF com número de protocolo', [
            //     'proposicao_id' => $proposicao->id,
            //     'numero_protocolo' => $proposicao->numero_protocolo,
            //     'numero_protocolo' => $proposicao->numero_protocolo,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            throw $e;
        }
    }

    /**
     * Get human-readable description for OnlyOffice callback status codes
     */
    private function getStatusDescription(int $status): string
    {
        return match ($status) {
            0 => 'Not found',
            1 => 'Document being edited',
            2 => 'Document ready for saving',
            3 => 'Document saving error',
            4 => 'Document closed without changes',
            6 => 'Document being edited (force save)',
            7 => 'Document save/conversion error',
            default => "Unknown status: {$status}"
        };
    }

    /**
     * Extrai texto simples de um arquivo RTF ou DOCX salvo
     * Usado para mostrar conteúdo atualizado após edição no OnlyOffice
     */
    public function extrairTextoDoArquivo(\App\Models\Proposicao $proposicao): ?string
    {
        if (! $proposicao->arquivo_path) {
            return null;
        }

        // Determinar o caminho completo do arquivo
        $caminhoCompleto = null;

        // Tentar encontrar o arquivo em diferentes locais
        $possiveisCaminhos = [
            storage_path('app/'.$proposicao->arquivo_path),
            storage_path('app/private/'.$proposicao->arquivo_path),
            storage_path('app/public/'.$proposicao->arquivo_path),
        ];

        foreach ($possiveisCaminhos as $caminho) {
            if (file_exists($caminho)) {
                $caminhoCompleto = $caminho;
                break;
            }
        }

        if (! $caminhoCompleto) {
            Log::warning('Arquivo não encontrado para extração de texto', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $proposicao->arquivo_path,
            ]);

            return null;
        }

        try {
            $extensao = strtolower(pathinfo($caminhoCompleto, PATHINFO_EXTENSION));

            if ($extensao === 'rtf') {
                // Extrair texto básico de RTF com melhor processamento
                $conteudo = file_get_contents($caminhoCompleto);

                // Remover comandos RTF comuns mais agressivamente
                $texto = $conteudo;

                // Remover cabeçalho RTF completo
                $texto = preg_replace('/^\\{\\\\rtf1[^}]*}/', '', $texto);

                // Remover todos os comandos RTF (backslash seguido de palavra e números opcionais)
                $texto = preg_replace('/\\\\[a-zA-Z]+[0-9]*\s?/', ' ', $texto);

                // Remover caracteres de controle RTF
                $texto = preg_replace('/\\\\[^a-zA-Z]/', '', $texto);

                // Remover chaves
                $texto = preg_replace('/[{}]/', '', $texto);

                // Remover sequências hexadecimais RTF
                $texto = preg_replace('/\\\\\'[0-9a-f]{2}/i', '', $texto);

                // Remover caracteres não imprimíveis e símbolos estranhos
                $texto = preg_replace('/[^\w\s\p{L}\p{P}]/u', ' ', $texto);

                // Limpar múltiplos espaços e quebras de linha
                $texto = preg_replace('/\s+/', ' ', $texto);
                $texto = trim($texto);

                // Se o texto ainda está muito corrompido, tentar extrair apenas texto em português/inglês
                if (preg_match_all('/[a-zA-ZÀ-ÿ\s,.!?;:()]{20,}/u', $texto, $matches)) {
                    $textoLimpo = implode(' ', $matches[0]);
                    if (strlen($textoLimpo) > 50) {
                        $texto = $textoLimpo;
                    }
                }

                // Limitar o tamanho do texto retornado
                if (strlen($texto) > 5000) {
                    $texto = substr($texto, 0, 5000).'...';
                }

                return $texto;

            } elseif ($extensao === 'docx') {
                // Para DOCX, extrair texto do XML
                $zip = new \ZipArchive;
                if ($zip->open($caminhoCompleto) === true) {
                    $content = $zip->getFromName('word/document.xml');
                    $zip->close();

                    if ($content) {
                        // Extrair texto do XML
                        $xml = new \SimpleXMLElement($content);
                        $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                        $paragraphs = $xml->xpath('//w:p');
                        $texto = '';

                        foreach ($paragraphs as $paragraph) {
                            $texts = $paragraph->xpath('.//w:t');
                            foreach ($texts as $text) {
                                $texto .= (string) $text.' ';
                            }
                            $texto .= "\n";
                        }

                        $texto = trim($texto);

                        // Limitar o tamanho
                        if (strlen($texto) > 5000) {
                            $texto = substr($texto, 0, 5000).'...';
                        }

                        return nl2br(htmlspecialchars($texto));
                    }
                }
            }

            // Se não conseguir extrair, retornar indicação
            return '<em>Conteúdo do arquivo não pode ser exibido. Abra no editor para visualizar.</em>';

        } catch (\Exception $e) {
            Log::error('Erro ao extrair texto do arquivo', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $caminhoCompleto,
                'erro' => $e->getMessage(),
            ]);

            return '<em>Erro ao processar conteúdo do arquivo.</em>';
        }
    }

    /**
     * Gerar configuração para editor de proposições
     */
    public function gerarConfiguracaoEditor($template, $proposicao, string $tipo, int $proposicaoId): array
    {
        // Gerar document key único para a proposição
        $documentKey = 'proposicao_'.$proposicaoId.'_'.time();

        // URL para download do documento
        $documentUrl = route('proposicoes.onlyoffice.download', [
            'proposicao' => $proposicaoId,
        ]);

        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            $documentUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $documentUrl);
        }

        // URL de callback para salvar alterações
        $callbackUrl = route('api.onlyoffice.callback', [
            'proposicao' => $proposicaoId,
            'documentKey' => $documentKey,
        ]);

        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }

        // Determinar tipo de arquivo
        $fileType = 'rtf'; // Padrão para proposições
        $documentType = 'word';

        // Se tem template, usar configuração do template
        if ($template && $template->arquivo_path) {
            $extensao = strtolower(pathinfo($template->arquivo_path, PATHINFO_EXTENSION));
            if (in_array($extensao, ['rtf', 'docx', 'doc'])) {
                $fileType = $extensao;
            }
        }

        return [
            'document' => [
                'fileType' => $fileType,
                'key' => $documentKey,
                'title' => 'Proposição #'.$proposicaoId,
                'url' => $documentUrl,
                'permissions' => [
                    'edit' => true,
                    'download' => true,
                    'print' => true,
                    'review' => true,
                    'comment' => true,
                ],
            ],
            'documentType' => $documentType,
            'editorConfig' => [
                'callbackUrl' => $callbackUrl,
                'lang' => 'pt-BR',
                'region' => 'pt-BR',
                'documentLang' => 'pt-BR',
                'mode' => 'edit',
                'user' => [
                    'id' => (string) auth()->id(),
                    'name' => auth()->user()->name ?? 'Usuário',
                    'group' => 'parlamentares',
                ],
                'customization' => [
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => ['pt-BR'],
                    ],
                    'documentLanguage' => 'pt-BR',
                    'autosave' => true,
                    'autosaveTimeout' => 30000, // 30 segundos
                    'autosaveType' => 0, // 0 = strict mode
                    'forcesave' => true,
                    'compactHeader' => false,
                    'toolbarNoTabs' => false,
                    'hideRightMenu' => false,
                    'feedback' => [
                        'visible' => false,
                    ],
                    'goback' => [
                        'url' => route('proposicoes.show', $proposicaoId),
                    ],
                ],
            ],
        ];
    }
}
