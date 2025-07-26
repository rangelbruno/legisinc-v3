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
        
        $config = [
            'document' => [
                'fileType' => 'rtf',
                'key' => $documentKeyWithVersion,
                'title' => $template->getNomeTemplate(),
                'url' => $downloadUrl,
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
            'documentType' => 'word',
            'editorConfig' => [
                'mode' => 'edit',
                'callbackUrl' => str_replace('http://localhost:8001', 'http://host.docker.internal:8001', route('api.onlyoffice.callback', $template->document_key)),
                'createUrl' => route('api.templates.download', $template->id),
                'lang' => 'pt-BR',
                'customization' => [
                    'autosave' => true, // Habilitar autosave para garantir salvamento
                    'autosaveType' => 0, // 0 = strict mode
                    'forcesave' => true, // Permitir salvamento manual
                    'compactHeader' => true,
                    'toolbarNoTabs' => false,
                    'hideRightMenu' => false,
                    'feedback' => [
                        'visible' => false
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
            'users' => $data['users'] ?? [],
            'actions' => $data['actions'] ?? []
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
                'error' => $e->getMessage()
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
            // Usar URL interna entre containers Docker (mais rápido)
            $urlOtimizada = str_replace($this->serverUrl, $this->internalUrl, $url);
            
            // Download direto com timeout reduzido
            $response = Http::timeout(5)->get($urlOtimizada);
            
            if (!$response->successful()) {
                // Fallback rápido apenas se necessário
                $response = Http::timeout(5)->get($url);
            }
            
            if (!$response || !$response->successful()) {
                \Log::error('OnlyOffice callback - falha no download', [
                    'template_id' => $template->id,
                    'url' => $url
                ]);
                return;
            }

            // Salvar arquivo com extensão RTF
            $nomeArquivo = "template_{$template->tipo_proposicao_id}.rtf";
            $path = "templates/{$nomeArquivo}";
            
            // Usar transação para garantir atomicidade e performance
            \DB::transaction(function() use ($template, $path, $response) {
                // Salvar no storage padrão (público)
                $saved = Storage::put($path, $response->body());
                
                \Log::info('Arquivo salvo no storage', [
                    'path' => $path,
                    'saved' => $saved,
                    'size' => strlen($response->body())
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
                'document_key' => $template->document_key,
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
        
        // Conteúdo mínimo de um RTF (documento vazio válido)
        $conteudoVazio = $this->criarDocumentoVazio($template);
        
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
    private function criarDocumentoVazio(TipoProposicaoTemplate $template): string
    {
        // Usar template básico do storage se existir, senão criar um simples
        $templateBasico = storage_path('app/templates/template_base.docx');
        
        if (file_exists($templateBasico)) {
            return file_get_contents($templateBasico);
        }
        
        // Criar conteúdo básico de um documento Word
        return $this->gerarDocumentoWordVazio($template);
    }

    /**
     * Gerar estrutura básica de um documento Word
     */
    private function gerarDocumentoWordVazio(TipoProposicaoTemplate $template): string
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
        
        // Se não encontrar arquivo base, criar um RTF válido
        $conteudo = "{\rtf1\ansi\deff0";
        $conteudo .= "{\fonttbl{\f0 Times New Roman;}}";
        $conteudo .= "\f0\fs24";
        $conteudo .= "Template: " . $template->tipoProposicao->nome . "\par\par";
        $conteudo .= "Adicione aqui o conte\'fado do seu template usando vari\'e1veis como:\par";
        $conteudo .= "- \${ementa}\par";
        $conteudo .= "- \${autor_nome}\par";
        $conteudo .= "- \${data_atual}\par";
        $conteudo .= "- \${numero_proposicao}\par\par";
        $conteudo .= "}";
        
        \Log::info('Criando template RTF básico', ['template_id' => $template->id]);
        return $conteudo;
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
     * Gerar JWT Token
     */
    private function gerarToken(array $data): string
    {
        return \Firebase\JWT\JWT::encode($data, $this->jwtSecret, 'HS256');
    }
}