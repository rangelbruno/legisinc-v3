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

            // Validar e possivelmente combinar conteúdo do template
            $conteudo = $response->body();
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
     * Preparar conteúdo do template: combinar imagens com variáveis se necessário
     */
    private function prepararConteudoTemplate(string $conteudo, TipoProposicaoTemplate $template): ?string
    {
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
        
        // Se não tem variáveis, mas tem conteúdo significativo (imagens), tentar combinar
        if (strlen($conteudo) > 100000) {
            \Log::info('Template sem variáveis mas com conteúdo significativo - tentando combinar', [
                'template_id' => $template->id,
                'tamanho_conteudo' => strlen($conteudo)
            ]);
            
            return $this->combinarImagemComVariaveis($conteudo, $template);
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
}