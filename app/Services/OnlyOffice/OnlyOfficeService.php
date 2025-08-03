<?php

namespace App\Services\OnlyOffice;

use App\Models\TipoProposicaoTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class OnlyOfficeService
{
    private string $serverUrl;
    private string $internalUrl;
    private string $jwtSecret;

    public function __construct()
    {
        $this->serverUrl = config('onlyoffice.server_url');
        $this->internalUrl = config('onlyoffice.internal_url');
        $this->jwtSecret = config('onlyoffice.jwt_secret');
    }

    /**
     * Configuração para edição de template
     */
    public function criarConfiguracaoTemplate(TipoProposicaoTemplate $template): array
    {
        // Garantir que o template tem um arquivo
        $this->garantirArquivoTemplate($template);
        
        $downloadUrl = $template->getUrlDownload();
        
        // Adicionar timestamp ao document_key para forçar reload após salvar
        $documentKeyWithVersion = $template->document_key . '_v' . $template->updated_at->timestamp;
        
        \Log::info('OnlyOffice config criada', [
            'template_id' => $template->id,
            'download_url' => $downloadUrl,
            'document_key' => $documentKeyWithVersion,
            'original_key' => $template->document_key
        ]);
        
        // Detectar tipo de arquivo pelo caminho
        $fileExtension = pathinfo($template->arquivo_path, PATHINFO_EXTENSION);
        $fileType = $fileExtension === 'txt' ? 'txt' : 'rtf';
        $documentType = $fileExtension === 'txt' ? 'text' : 'word';
        
        $config = [
            'document' => [
                'fileType' => $fileType,
                'key' => $documentKeyWithVersion,
                'title' => $template->getNomeTemplate(),
                'url' => $downloadUrl,
                'info' => [
                    'owner' => auth()->user()->name ?? 'Sistema',
                    'uploaded' => $template->updated_at->format('d/m/Y H:i:s')
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
                ]
            ],
            'documentType' => $documentType,
            'editorConfig' => [
                'mode' => 'edit',
                'callbackUrl' => $this->ajustarCallbackUrl(route('api.onlyoffice.callback', $template->document_key)),
                'lang' => 'pt-BR',
                'region' => 'pt-BR',
                'user' => [
                    'id' => (string) auth()->id(),
                    'name' => auth()->user()->name ?? 'Sistema',
                    'group' => 'administrators'
                ],
                'customization' => [
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => ['pt-BR']
                    ],
                    'autosave' => true, // Habilitar autosave para garantir salvamento
                    'autosaveType' => 0, // 0 = strict mode
                    'forcesave' => true, // Permitir salvamento manual
                    'compactHeader' => true,
                    'toolbarNoTabs' => false,
                    'hideRightMenu' => false,
                    'feedback' => [
                        'visible' => false
                    ],
                    'goback' => [
                        'url' => route('templates.index')
                    ]
                ],
                'coEditing' => [
                    'mode' => 'strict', // Modo strict para evitar conflitos
                    'change' => false // Desabilitar coediting para templates
                ],
                'user' => [
                    'id' => (string)(auth()->id() ?? 'user-' . time()),
                    'name' => auth()->user()->name ?? 'Usuário',
                    'group' => 'admin_' . auth()->id() // Grupo único por usuário
                ]
            ]
        ];

        // Temporariamente desabilitar JWT para debug
        // if ($this->jwtSecret) {
        //     $config['token'] = $this->gerarToken($config);
        // }

        return $config;
    }

    /**
     * Callback do ONLYOFFICE (auto-save)
     */
    public function processarCallback(string $documentKey, array $data): array
    {
        // Remover versão do document_key se existir
        $originalKey = preg_replace('/_v\d+$/', '', $documentKey);
        
        $template = TipoProposicaoTemplate::where('document_key', $originalKey)->first();
        
        if (!$template) {
            return ['error' => 1];
        }

        $status = $data['status'] ?? 0;
        
        // Log detalhado do callback
        \Log::info('OnlyOffice callback status', [
            'document_key' => $documentKey,
            'status' => $status,
            'has_url' => isset($data['url']),
            'url' => $data['url'] ?? null,
            'users' => $data['users'] ?? [],
            'actions' => $data['actions'] ?? [],
            'full_data' => $data
        ]);
        
        // Implementar lock para evitar processamento concorrente
        $lockKey = "onlyoffice_save_lock_{$documentKey}";
        $lock = \Cache::lock($lockKey, 10); // Lock por 10 segundos
        
        try {
            // Status 2 = Pronto para salvar (save)
            // Status 6 = Force save (salvamento forçado pelo usuário)
            if (in_array($status, [2, 6]) && isset($data['url'])) {
                
                // Verificar se não está processando outro callback
                if ($lock->get()) {
                    \Log::info('Processando salvamento do template', [
                        'document_key' => $documentKey,
                        'status' => $status,
                        'status_type' => $status === 2 ? 'auto_save' : 'force_save'
                    ]);
                    
                    $this->salvarTemplate($template, $data['url']);
                    
                    // Liberar lock após processar
                    $lock->release();
                } else {
                    \Log::warning('Callback ignorado - outro processamento em andamento', [
                        'document_key' => $documentKey,
                        'status' => $status
                    ]);
                }
            }
            
            // Status 1 = Documento sendo editado
            // Status 4 = Documento fechado sem mudanças
            if (in_array($status, [1, 4])) {
                \Log::info('Status de edição recebido', [
                    'document_key' => $documentKey,
                    'status' => $status,
                    'description' => $status === 1 ? 'Documento sendo editado' : 'Documento fechado sem mudanças'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro no processamento do callback', [
                'document_key' => $documentKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            
            // Garantir liberação do lock em caso de erro
            optional($lock)->release();
            
            return ['error' => 1];
        }

        return ['error' => 0];
    }

    /**
     * Salvar template automaticamente
     */
    private function salvarTemplate(TipoProposicaoTemplate $template, string $url): void
    {
        try {
            // Corrigir URL do OnlyOffice para usar nome correto do container
            $urlCorrigida = str_replace([
                'onlyoffice-documentserver',
                'localhost:8080'
            ], 'legisinc-onlyoffice', $url);
            
            // Se a URL ainda contém localhost, substituir pela URL interna
            if (strpos($urlCorrigida, 'localhost') !== false) {
                $urlCorrigida = str_replace('http://localhost:8080', 'http://legisinc-onlyoffice', $urlCorrigida);
            }
            
            \Log::info('Tentando baixar arquivo do OnlyOffice', [
                'url_original' => $url,
                'url_corrigida' => $urlCorrigida
            ]);
            
            // Download direto com timeout aumentado
            $response = Http::timeout(30)->get($urlCorrigida);
            
            if (!$response->successful()) {
                // Fallback tentando outras variações de URL
                $urlsFallback = [
                    str_replace('http://localhost:8080', 'http://127.0.0.1:8080', $url),
                    $url // URL original como último recurso
                ];
                
                foreach ($urlsFallback as $urlFallback) {
                    \Log::warning('Tentando URL fallback', [
                        'url_fallback' => $urlFallback,
                        'status_anterior' => $response->status()
                    ]);
                    
                    $response = Http::timeout(30)->get($urlFallback);
                    if ($response->successful()) {
                        break;
                    }
                }
            }
            
            if (!$response || !$response->successful()) {
                \Log::error('OnlyOffice callback - falha no download', [
                    'template_id' => $template->id,
                    'url' => $url,
                    'url_otimizada' => $urlOtimizada,
                    'response_status' => $response ? $response->status() : 'null_response',
                    'response_body' => $response ? $response->body() : 'null_response'
                ]);
                return;
            }

            // Validar e possivelmente combinar conteúdo do template
            $conteudo = $response->body();
            
            // Verificar se o arquivo não está vazio ou corrompido
            if (empty($conteudo) || strlen($conteudo) < 50) {
                \Log::error('OnlyOffice callback - conteúdo inválido ou vazio', [
                    'template_id' => $template->id,
                    'url' => $url,
                    'content_length' => strlen($conteudo),
                    'content_preview' => substr($conteudo, 0, 100)
                ]);
                return;
            }
            
            // Verificar se é realmente um arquivo RTF
            if (!str_starts_with($conteudo, '{\rtf')) {
                \Log::error('OnlyOffice callback - arquivo não é RTF válido', [
                    'template_id' => $template->id,
                    'url' => $url,
                    'content_start' => substr($conteudo, 0, 50)
                ]);
                return;
            }
            
            $conteudoFinal = $this->prepararConteudoTemplate($conteudo, $template);
            
            if (!$conteudoFinal) {
                \Log::warning('Template rejeitado - não foi possível preparar conteúdo válido', [
                    'template_id' => $template->id,
                    'tipo_proposicao_id' => $template->tipo_proposicao_id
                ]);
                return;
            }
            
            // Usar conteúdo preparado em vez do original
            $conteudo = $conteudoFinal;
            
            // Salvar arquivo com extensão RTF
            $nomeArquivo = "template_{$template->tipo_proposicao_id}.rtf";
            $path = "templates/{$nomeArquivo}";
            
            // Fazer backup do template atual antes de sobrescrever
            $this->backupTemplateAtual($template);
            
            // Usar transação para garantir atomicidade e performance
            \DB::transaction(function() use ($template, $path, $conteudo) {
                // Garantir que o conteúdo está em UTF-8 e correto para RTF
                $conteudoCorrigido = $this->corrigirEncodingRTF($conteudo);
                
                // Corrigir encoding adicional para exibição no OnlyOffice
                $conteudoCorrigido = $this->corrigirEncodingParaExibicao($conteudoCorrigido);
                
                // Salvar no storage padrão (público)
                $saved = Storage::put($path, $conteudoCorrigido);
                
                \Log::info('Arquivo salvo no storage com encoding corrigido', [
                    'path' => $path,
                    'saved' => $saved,
                    'size_original' => strlen($conteudo),
                    'size_corrigido' => strlen($conteudoCorrigido)
                ]);
                
                // Forçar refresh do modelo para evitar cache
                $template->refresh();
                
                // Atualizar template
                $updated = $template->update([
                    'arquivo_path' => $path,
                    'updated_by' => auth()->id(),
                    'updated_at' => now() // Forçar atualização do timestamp
                ]);
                
                \Log::info('Template atualizado no banco', [
                    'template_id' => $template->id,
                    'updated' => $updated,
                    'arquivo_path' => $template->arquivo_path,
                    'updated_at' => $template->updated_at
                ]);
                
                // Limpar cache
                \Cache::forget('onlyoffice_template_' . $template->id);
                \Cache::forget('template_content_' . $template->id);
                
                // Extrair variáveis automaticamente
                $this->extrairVariaveis($template);
            });
            
            \Log::info('Template salvo com sucesso', [
                'id' => $template->id,
                'path' => $path,
                'size' => strlen($response->body())
            ]);
            
        } catch (\Exception $e) {
            \Log::error('OnlyOffice callback error', [
                'template_id' => $template->id,
                'document_key' => $template->document_key,
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Extrair variáveis do documento
     */
    private function extrairVariaveis(TipoProposicaoTemplate $template): void
    {
        if (!$template->arquivo_path) {
            return;
        }

        $conteudo = Storage::get($template->arquivo_path);
        
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
        if (!$template->arquivo_path) {
            throw new \Exception('Template não possui arquivo');
        }

        // Carregar template
        $conteudo = Storage::get($template->arquivo_path);

        // Mapear dados padrão
        $dadosCompletos = $this->mapearDadosProposicao($dados);

        // Substituir variáveis
        foreach ($dadosCompletos as $variavel => $valor) {
            $conteudo = str_replace('${' . $variavel . '}', $valor, $conteudo);
        }

        // Salvar documento gerado
        $nomeDocumento = "documento_" . uniqid() . ".docx";
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
        
        return [
            'numero_proposicao' => $dados['numero'] ?? 'A definir',
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
     * Garantir que o template tem um arquivo inicial
     */
    private function garantirArquivoTemplate(TipoProposicaoTemplate $template): void
    {
        if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
            return; // Já tem arquivo
        }

        // Criar documento vazio - usar RTF por compatibilidade
        $nomeArquivo = "template_{$template->tipo_proposicao_id}.rtf";
        $path = "templates/{$nomeArquivo}";
        
        // Conteúdo com imagem padrão do cabeçalho
        $conteudoVazio = $this->criarDocumentoComCabecalho($template);
        
        // Salvar no storage público padrão
        Storage::put($path, $conteudoVazio);
        
        // Atualizar template
        $template->update(['arquivo_path' => $path]);
        
        \Log::info('Template arquivo criado', [
            'template_id' => $template->id,
            'path' => $path,
            'file_exists' => Storage::exists($path)
        ]);
    }

    /**
     * Criar um documento DOCX vazio válido
     */
    private function criarDocumentoComCabecalho(TipoProposicaoTemplate $template): string
    {
        // Usar template básico do storage se existir, senão criar um simples
        $templateBasico = storage_path('app/templates/template_base.docx');
        
        if (file_exists($templateBasico)) {
            return file_get_contents($templateBasico);
        }
        
        // Criar conteúdo básico de um documento Word com cabeçalho
        return $this->gerarDocumentoWordComCabecalho($template);
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
            storage_path('app/private/documentos/modelos/teste.docx')
        ];
        
        foreach ($possiveisArquivos as $arquivo) {
            if (file_exists($arquivo)) {
                \Log::info('Usando arquivo base', ['arquivo' => $arquivo]);
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
        $conteudo .= "Template: " . $this->converterParaRTF($template->tipoProposicao->nome) . "\par\par";
        $conteudo .= "Adicione aqui o conte\\'fado do seu template usando vari\\'e1veis como:\par";
        $conteudo .= "- \${ementa}\par";
        $conteudo .= "- \${autor_nome}\par";
        $conteudo .= "- \${data_atual}\par";
        $conteudo .= "- \${numero_proposicao}\par\par";
        $conteudo .= "}";
        
        \Log::info('Criando template RTF básico com cabeçalho', ['template_id' => $template->id]);
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
                ]
            ],
            'documentType' => 'word',
            'editorConfig' => [
                'mode' => $mode,
                'user' => $user,
                'lang' => 'pt-BR'
            ]
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
        if (!mb_check_encoding($conteudo, 'UTF-8')) {
            $conteudo = mb_convert_encoding($conteudo, 'UTF-8', 'auto');
        }
        
        // Verificar se o conteúdo atual tem variáveis
        $temVariaveis = preg_match('/\$\{[^}]+\}/', $conteudo);
        
        // Se tem variáveis, usar como está
        if ($temVariaveis) {
            \Log::info('Template mantém variáveis após edição', [
                'template_id' => $template->id,
                'tamanho_conteudo' => strlen($conteudo)
            ]);
            return $conteudo;
        }
        
        // Se não tem variáveis, mas tem conteúdo significativo, PRESERVAR o conteúdo do usuário
        if (strlen($conteudo) > 1000) {
            \Log::info('Template sem variáveis mas com conteúdo significativo - PRESERVANDO conteúdo do usuário', [
                'template_id' => $template->id,
                'tamanho_conteudo' => strlen($conteudo)
            ]);
            
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
            'texto' => '${texto}'
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
            \Log::info('Template com variáveis inseridas no conteúdo original', [
                'template_id' => $template->id,
                'variaveis_inseridas' => $variaveisInseridas,
                'tamanho_original' => strlen($conteudoComImagem),
                'tamanho_final' => strlen($conteudoModificado)
            ]);
            
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
        
        \Log::info('Template substituído por versão limpa com variáveis', [
            'template_id' => $template->id,
            'tamanho_original' => strlen($conteudoComImagem),
            'tamanho_final' => strlen($templateComVariaveis),
            'motivo' => 'Não foi possível inserir variáveis no conteúdo original'
        ]);
        
        return $templateComVariaveis;
    }
    
    /**
     * Validar se o conteúdo do template contém variáveis essenciais
     */
    private function validarConteudoTemplate(string $conteudo, TipoProposicaoTemplate $template): bool
    {
        // Verificar se não é um arquivo muito pequeno (possível erro)
        if (strlen($conteudo) < 100) {
            \Log::warning('Template muito pequeno, possível erro', [
                'template_id' => $template->id,
                'tamanho_conteudo' => strlen($conteudo)
            ]);
            return false;
        }
        
        // Verificar se contém pelo menos uma variável (mais flexível)
        $temVariavel = preg_match('/\$\{[^}]+\}/', $conteudo);
        
        // Se não tem nenhuma variável, verificar se tem conteúdo significativo (imagens, texto, etc)
        if (!$temVariavel) {
            // Arquivo muito grande pode conter imagens e ainda ser válido
            if (strlen($conteudo) > 100000) {
                \Log::info('Template sem variáveis mas com conteúdo significativo (possíveis imagens)', [
                    'template_id' => $template->id,
                    'tamanho_conteudo' => strlen($conteudo)
                ]);
                return true;
            }
            
            \Log::warning('Template sem variáveis e conteúdo insuficiente', [
                'template_id' => $template->id,
                'tamanho_conteudo' => strlen($conteudo)
            ]);
            return false;
        }
        
        \Log::info('Template validado com sucesso', [
            'template_id' => $template->id,
            'tem_variavel' => $temVariavel,
            'tamanho_conteudo' => strlen($conteudo)
        ]);
        
        return true;
    }
    
    /**
     * Fazer backup do template atual antes de sobrescrever
     */
    private function backupTemplateAtual(TipoProposicaoTemplate $template): void
    {
        try {
            if ($template->arquivo_path && \Storage::exists($template->arquivo_path)) {
                $conteudoAtual = \Storage::get($template->arquivo_path);
                
                // Criar nome do backup com timestamp
                $backupPath = str_replace('.rtf', '_backup_' . date('Y_m_d_His') . '.rtf', $template->arquivo_path);
                
                // Salvar backup
                \Storage::put($backupPath, $conteudoAtual);
                
                \Log::info('Backup do template criado antes da atualização', [
                    'template_id' => $template->id,
                    'backup_path' => $backupPath,
                    'tamanho_original' => strlen($conteudoAtual)
                ]);
                
                // Limpar backups antigos (manter apenas os 5 mais recentes)
                $this->limparBackupsAntigos($template);
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar backup do template', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
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
            $arquivos = \Storage::files($templateDir);
            $backupsDoTemplate = array_filter($arquivos, function($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName . '_backup_') === 0;
            });
            
            // Ordenar por data de modificação (mais recente primeiro)
            usort($backupsDoTemplate, function($a, $b) {
                return \Storage::lastModified($b) - \Storage::lastModified($a);
            });
            
            // Remover backups além dos 5 mais recentes
            if (count($backupsDoTemplate) > 5) {
                $backupsParaRemover = array_slice($backupsDoTemplate, 5);
                foreach ($backupsParaRemover as $backup) {
                    \Storage::delete($backup);
                    \Log::info('Backup antigo removido', ['backup_path' => $backup]);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao limpar backups antigos', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
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
            'Projeto de Lei Complementar' => 'projeto_lei_complementar'
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
        
        \Log::info('Corrigindo encoding RTF - OnlyOffice salvou caracteres UTF-8 em RTF', [
            'tamanho_original' => strlen($conteudoRTF),
            'preview_original' => substr($conteudoRTF, 0, 200)
        ]);
        
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
                \Log::info("Corrigido: $antes ocorrências de bytes UTF-8 para códigos RTF", [
                    'utf8_hex' => bin2hex($utf8Bytes),
                    'rtf_code' => $rtfCode
                ]);
            }
        }
        
        \Log::info('Encoding RTF corrigido - bytes UTF-8 convertidos para códigos RTF', [
            'tamanho_final' => strlen($conteudoCorrigido),
            'preview_final' => substr($conteudoCorrigido, 0, 200)
        ]);
        
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
            '°' => "\\'b0", 'º' => "\\'ba", 'ª' => "\\'aa"
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
        \Log::info('Aplicando correção adicional de encoding para OnlyOffice', [
            'tamanho_original' => strlen($conteudoRTF),
            'preview' => substr($conteudoRTF, 0, 200)
        ]);
        
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
            "\\'ed" => "í",  // í volta para UTF-8 
            "\\'e3" => "ã",  // ã volta para UTF-8
            "\\'e2" => "â",  // â volta para UTF-8  
            "\\'e7" => "ç",  // ç volta para UTF-8
            "\\'c7" => "Ç",  // Ç volta para UTF-8
            "\\'c3" => "Ã",  // Ã volta para UTF-8
        ];
        
        foreach ($substituicoes as $rtfCode => $utf8Char) {
            $antes = substr_count($conteudoCorrigido, $rtfCode);
            $conteudoCorrigido = str_replace($rtfCode, $utf8Char, $conteudoCorrigido);
            $depois = substr_count($conteudoCorrigido, $rtfCode);
            
            if ($antes > $depois) {
                \Log::info("Substituição RTF->UTF8: {$rtfCode} -> {$utf8Char}", [
                    'ocorrencias' => ($antes - $depois)
                ]);
            }
        }
        
        \Log::info('Correção de encoding para OnlyOffice aplicada', [
            'tamanho_final' => strlen($conteudoCorrigido)
        ]);
        
        return $conteudoCorrigido;
    }

    /**
     * Escapar caracteres especiais para RTF
     */
    private function escapeRtf(string $text): string
    {
        // Converter para UTF-8 se necessário
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
        }
        
        // Substituir caracteres especiais RTF
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('{', '\\{', $text);
        $text = str_replace('}', '\\}', $text);
        
        // Converter caracteres acentuados para códigos RTF
        $replacements = [
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
            '°' => "\\'b0", 'º' => "\\'ba", 'ª' => "\\'aa"
        ];
        
        return strtr($text, $replacements);
    }

    /**
     * Gerar documento com template para proposição
     */
    private function gerarDocumentoComTemplate(\App\Models\Proposicao $proposicao)
    {
        try {
            $template = $proposicao->template;
            
            // Verificar se o template tem arquivo
            \Log::info('Verificando arquivo do template', [
                'template_id' => $template->id,
                'arquivo_path' => $template->arquivo_path,
                'storage_path' => storage_path('app/' . $template->arquivo_path),
                'file_exists_storage' => Storage::exists($template->arquivo_path),
                'file_exists_direct' => file_exists(storage_path('app/' . $template->arquivo_path))
            ]);
            
            if (!$template->arquivo_path) {
                \Log::warning('Template sem caminho de arquivo', [
                    'proposicao_id' => $proposicao->id,
                    'template_id' => $template->id
                ]);
                return $this->gerarDocumentoRTFProposicao($proposicao);
            }
            
            // Verificar diretamente se o arquivo existe
            $caminhoCompleto = storage_path('app/' . $template->arquivo_path);
            if (!file_exists($caminhoCompleto)) {
                \Log::warning('Arquivo de template não encontrado', [
                    'proposicao_id' => $proposicao->id,
                    'template_id' => $template->id,
                    'caminho' => $caminhoCompleto
                ]);
                return $this->gerarDocumentoRTFProposicao($proposicao);
            }

            // Carregar o conteúdo do template diretamente
            $conteudoTemplate = file_get_contents($caminhoCompleto);
            
            \Log::info('Template carregado', [
                'template_id' => $template->id,
                'tamanho' => strlen($conteudoTemplate),
                'preview' => substr($conteudoTemplate, 0, 100)
            ]);
            
            // Preparar dados para substituição - sem processar caracteres especiais por enquanto
            $dados = [
                'numero_proposicao' => $proposicao->numero ?? 'A definir',
                'ementa' => $proposicao->ementa ?? '',
                'texto' => $proposicao->conteudo ?? '',
                'autor_nome' => $proposicao->autor->name ?? '',
                'autor_cargo' => $proposicao->autor->cargo ?? 'Vereador',
                'data_atual' => now()->format('d/m/Y'),
                'ano_atual' => now()->year,
                'municipio' => config('app.municipio', 'São Paulo'),
                'camara_nome' => config('app.camara_nome', 'Câmara Municipal'),
            ];

            // Reativar o código original com melhorias
            // Substituir variáveis no template
            $conteudoProcessado = $conteudoTemplate;
            
            \Log::info('Antes da substituição', [
                'template_id' => $template->id,
                'dados' => $dados,
                'preview_antes' => substr($conteudoProcessado, 0, 500)
            ]);
            
            foreach ($dados as $variavel => $valor) {
                $variavelCompleta = '${' . $variavel . '}';
                $antes = substr_count($conteudoProcessado, $variavelCompleta);
                
                // Escapar caracteres especiais do RTF para valores de texto
                $valorEscapado = $this->escapeRtf($valor);
                
                // Usar as variáveis no formato ${variavel}
                $conteudoProcessado = str_replace($variavelCompleta, $valorEscapado, $conteudoProcessado);
                
                $depois = substr_count($conteudoProcessado, $variavelCompleta);
                
                \Log::info('Substituição de variável', [
                    'variavel' => $variavelCompleta,
                    'valor_original' => $valor,
                    'valor_escapado' => $valorEscapado,
                    'ocorrencias_antes' => $antes,
                    'ocorrencias_depois' => $depois,
                    'substituido' => $antes - $depois
                ]);
            }
            
            \Log::info('Após substituição', [
                'template_id' => $template->id,
                'preview_depois' => substr($conteudoProcessado, 0, 500)
            ]);

            // Verificar a extensão do arquivo do template
            $extensao = pathinfo($template->arquivo_path, PATHINFO_EXTENSION);
            
            if ($extensao === 'rtf') {
                // Para RTF, retornar diretamente
                $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_') . '.rtf';
                file_put_contents($tempFile, $conteudoProcessado);
                
                // Tentar converter para DOCX se possível
                try {
                    // Por enquanto, retornar como RTF mesmo
                    // TODO: Implementar conversão RTF->DOCX preservando imagens
                    return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf")
                        ->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    \Log::error('Erro ao processar documento', ['erro' => $e->getMessage()]);
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
            
            \Log::info('Variáveis substituídas no template', [
                'template_id' => $template->id,
                'variaveis' => array_keys($dados)
            ]);

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
            \Log::error('Erro ao gerar documento com template', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
            
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
        \Log::info('Gerando documento para proposição', [
            'proposicao_id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'template_id' => $proposicao->template_id,
            'ementa_length' => strlen($proposicao->ementa ?? ''),
            'conteudo_length' => strlen($proposicao->conteudo ?? ''),
            'has_conteudo' => !empty($proposicao->conteudo),
            'conteudo_preview' => $proposicao->conteudo ? substr(strip_tags($proposicao->conteudo), 0, 100) : 'VAZIO'
        ]);
        
        // Verificar se existe PHPWord
        if (!class_exists('\PhpOffice\PhpWord\PhpWord')) {
            // Se não existe, gerar documento RTF simples
            return $this->gerarDocumentoRTFProposicao($proposicao);
        }

        // Se a proposição tem um template associado, usar o documento do template como base
        if ($proposicao->template_id && $proposicao->template) {
            return $this->gerarDocumentoComTemplate($proposicao);
        }
        
        // Tentar buscar template pelo tipo da proposição
        \Log::info('Buscando template para tipo de proposição', [
            'proposicao_id' => $proposicao->id,
            'tipo' => $proposicao->tipo
        ]);
        
        // Mapear tipos comuns para códigos
        $tipoMapeado = $this->mapearTipoProposicao($proposicao->tipo);
        
        $tipoProposicao = \App\Models\TipoProposicao::where('codigo', $tipoMapeado)
            ->orWhere('codigo', $proposicao->tipo)
            ->orWhere('nome', $proposicao->tipo)
            ->orWhere('nome', 'like', '%' . $proposicao->tipo . '%')
            ->first();
            
        if ($tipoProposicao) {
            \Log::info('Tipo de proposição encontrado', [
                'tipo_id' => $tipoProposicao->id,
                'codigo' => $tipoProposicao->codigo,
                'nome' => $tipoProposicao->nome
            ]);
            
            if ($tipoProposicao->templates()->exists()) {
                $template = $tipoProposicao->templates()->where('ativo', true)->first();
                if ($template) {
                    \Log::info('Template encontrado para o tipo', [
                        'template_id' => $template->id,
                        'arquivo_path' => $template->arquivo_path
                    ]);
                    $proposicao->template = $template; // Associar temporariamente para uso
                    return $this->gerarDocumentoComTemplate($proposicao);
                } else {
                    \Log::warning('Nenhum template ativo encontrado para o tipo', [
                        'tipo_id' => $tipoProposicao->id
                    ]);
                }
            } else {
                \Log::warning('Tipo não possui templates', [
                    'tipo_id' => $tipoProposicao->id,
                    'tipo_nome' => $tipoProposicao->nome
                ]);
            }
        } else {
            \Log::warning('Tipo de proposição não encontrado', [
                'tipo_buscado' => $proposicao->tipo
            ]);
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Configurar documento
        $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::PT_BR));
        
        // Adicionar seção
        $section = $phpWord->addSection();
        
        // Adicionar título
        $section->addText(
            "PROPOSIÇÃO: " . strtoupper($proposicao->tipo),
            ['bold' => true, 'size' => 16],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $section->addTextBreak(2);
        
        // Adicionar informações básicas
        $section->addText('INFORMAÇÕES BÁSICAS', ['bold' => true, 'size' => 14]);
        $section->addText("Autor: {$proposicao->autor->name}");
        $section->addText("Data: " . $proposicao->created_at->format('d/m/Y'));
        $section->addText("Status: " . ucfirst(str_replace('_', ' ', $proposicao->status)));
        
        $section->addTextBreak(2);
        
        // Adicionar ementa
        $section->addText('EMENTA', ['bold' => true, 'size' => 14]);
        $section->addText($proposicao->ementa);
        
        $section->addTextBreak(2);
        
        // Adicionar conteúdo da proposição
        if (!empty($proposicao->conteudo)) {
            $section->addText('CONTEÚDO DA PROPOSIÇÃO', ['bold' => true, 'size' => 14]);
            
            // Verificar se o conteúdo contém HTML
            if (strip_tags($proposicao->conteudo) != $proposicao->conteudo) {
                // Se contém HTML, tentar converter
                try {
                    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $proposicao->conteudo);
                } catch (\Exception $e) {
                    // Se a conversão HTML falhar, usar texto limpo
                    \Log::warning('Erro ao converter HTML, usando texto limpo', ['error' => $e->getMessage()]);
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
                $section->addText('Data da Assinatura: ' . $proposicao->data_assinatura->format('d/m/Y H:i:s'));
            }
            
            // Hash da assinatura (primeiros e últimos caracteres para segurança)
            if (strlen($proposicao->assinatura_digital) > 20) {
                $hashDisplay = substr($proposicao->assinatura_digital, 0, 8) . '...' . substr($proposicao->assinatura_digital, -8);
                $section->addText('Hash da Assinatura: ' . $hashDisplay, ['size' => 10, 'color' => '666666']);
            }
            
            if ($proposicao->ip_assinatura) {
                $section->addText('IP da Assinatura: ' . $proposicao->ip_assinatura, ['size' => 10, 'color' => '666666']);
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
     * Gerar documento RTF simples de uma proposição
     */
    private function gerarDocumentoRTFProposicao(\App\Models\Proposicao $proposicao)
    {
        $conteudo = $proposicao->conteudo ? str_replace("\n", "\par\n", strip_tags($proposicao->conteudo)) : '[CONTEÚDO NÃO DISPONÍVEL - Adicione o texto da proposição aqui]';
        
        $rtf = "{\rtf1\ansi\deff0 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24
{\qc\b\fs28 PROPOSIÇÃO: " . strtoupper($proposicao->tipo) . "\par}
\par
{\b INFORMAÇÕES BÁSICAS:\par}
Autor: {$proposicao->autor->name}\par
Data: " . $proposicao->created_at->format('d/m/Y') . "\par
Status: " . ucfirst(str_replace('_', ' ', $proposicao->status)) . "\par
\par
{\b EMENTA:\par}
{$proposicao->ementa}\par
\par
{\b CONTEÚDO DA PROPOSIÇÃO:\par}
" . $conteudo . "\par";

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
" . str_repeat('_', 80) . "\par
\par
{\b Documento assinado digitalmente por:\par}
{$proposicao->autor->name}\par";

            if ($proposicao->data_assinatura) {
                $rtf .= "Data da Assinatura: " . $proposicao->data_assinatura->format('d/m/Y H:i:s') . "\par";
            }

            // Hash da assinatura (primeiros e últimos caracteres para segurança)
            if (strlen($proposicao->assinatura_digital) > 20) {
                $hashDisplay = substr($proposicao->assinatura_digital, 0, 8) . '...' . substr($proposicao->assinatura_digital, -8);
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
        $tempFile = tempnam(sys_get_temp_dir(), 'proposicao_') . '.rtf';
        file_put_contents($tempFile, $rtf);
        
        // Retornar arquivo
        return response()->download($tempFile, "proposicao_{$proposicao->id}.rtf")
            ->deleteFileAfterSend(true);
    }

    /**
     * Processar callback do OnlyOffice para uma proposição
     */
    public function processarCallbackProposicao(\App\Models\Proposicao $proposicao, string $documentKey, array $data): array
    {
        $status = $data['status'] ?? 0;

        // Status 2 = documento salvo e pronto para download
        if ($status == 2 && isset($data['url'])) {
            try {
                $originalUrl = $data['url'];
                
                // Converter URL do OnlyOffice para acesso entre containers
                $urlOtimizada = $originalUrl;
                if (config('app.env') === 'local') {
                    // Laravel precisa acessar OnlyOffice usando nome do container
                    $urlOtimizada = str_replace(['http://localhost:8080', 'http://127.0.0.1:8080'], 'http://legisinc-onlyoffice', $originalUrl);
                }
                
                \Log::info('OnlyOffice callback - tentando baixar documento', [
                    'proposicao_id' => $proposicao->id,
                    'original_url' => $originalUrl,
                    'optimized_url' => $urlOtimizada
                ]);
                
                // Baixar o documento atualizado
                $response = Http::timeout(10)->get($urlOtimizada);
                
                if (!$response->successful()) {
                    \Log::error('Erro ao baixar documento do OnlyOffice', [
                        'proposicao_id' => $proposicao->id,
                        'original_url' => $originalUrl,
                        'optimized_url' => $urlOtimizada,
                        'status' => $response->status()
                    ]);
                    return ['error' => 1];
                }

                // Extrair conteúdo do documento
                $conteudo = $this->extrairConteudoDocumento($response->body());
                
                // Atualizar proposição
                $proposicao->update([
                    'conteudo' => $conteudo,
                    'ultima_modificacao' => now(),
                    'modificado_por' => auth()->id()
                ]);
                
                \Log::info('Proposição atualizada com sucesso via OnlyOffice', [
                    'proposicao_id' => $proposicao->id,
                    'conteudo_length' => strlen($conteudo)
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Erro ao processar callback do OnlyOffice para proposição', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage()
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
                            $extractedContent .= $element->getText() . "\n";
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
}