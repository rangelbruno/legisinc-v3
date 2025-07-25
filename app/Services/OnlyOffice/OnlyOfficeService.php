<?php

namespace App\Services\OnlyOffice;

use App\Models\TipoProposicaoTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class OnlyOfficeService
{
    private string $serverUrl;
    private string $jwtSecret;

    public function __construct()
    {
        $this->serverUrl = config('onlyoffice.server_url');
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
        \Log::info('OnlyOffice config criada', [
            'template_id' => $template->id,
            'download_url' => $downloadUrl,
            'document_key' => $template->document_key
        ]);
        
        $config = [
            'document' => [
                'fileType' => 'rtf',
                'key' => $template->document_key,
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
                'lang' => 'pt-BR',
                'autosave' => true,
                'coEditing' => [
                    'mode' => 'fast',
                    'change' => true
                ],
                'user' => [
                    'id' => (string)(auth()->id() ?? 'user-' . time()),
                    'name' => auth()->user()->name ?? 'Usuário'
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
        $template = TipoProposicaoTemplate::where('document_key', $documentKey)->first();
        
        if (!$template) {
            return ['error' => 1];
        }

        $status = $data['status'];

        // Status 2 = Pronto para salvar
        if ($status === 2 && isset($data['url'])) {
            $this->salvarTemplate($template, $data['url']);
        }

        return ['error' => 0];
    }

    /**
     * Salvar template automaticamente
     */
    private function salvarTemplate(TipoProposicaoTemplate $template, string $url): void
    {
        try {
            // Múltiplas opções de URL para tentar acesso ao container OnlyOffice
            $urlsParaTentar = [
                str_replace('http://localhost:8080', 'http://legisinc-onlyoffice:80', $url),
                str_replace('http://localhost:8080', 'http://host.docker.internal:8080', $url),
                str_replace('http://localhost:8080', 'http://onlyoffice-documentserver:80', $url),
                $url // URL original como último recurso
            ];
            
            \Log::info('OnlyOffice salvando template', [
                'template_id' => $template->id,
                'url_original' => $url,
                'urls_para_tentar' => $urlsParaTentar
            ]);
            
            $response = null;
            $urlUtilizada = null;
            
            // Tentar cada URL até conseguir fazer download
            foreach ($urlsParaTentar as $urlTentativa) {
                try {
                    \Log::info('Tentando URL', ['url' => $urlTentativa]);
                    $response = Http::timeout(30)->get($urlTentativa);
                    
                    if ($response->successful()) {
                        $urlUtilizada = $urlTentativa;
                        \Log::info('URL funcionou', ['url' => $urlUtilizada, 'size' => strlen($response->body())]);
                        break;
                    } else {
                        \Log::warning('URL falhou', ['url' => $urlTentativa, 'status' => $response->status()]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erro ao tentar URL', ['url' => $urlTentativa, 'error' => $e->getMessage()]);
                    continue;
                }
            }
            
            if (!$response || !$response->successful()) {
                \Log::error('OnlyOffice callback - falha no download de todas as URLs', [
                    'template_id' => $template->id,
                    'url_original' => $url
                ]);
                return;
            }

            // Salvar arquivo com extensão RTF
            $nomeArquivo = "template_{$template->tipo_proposicao_id}.rtf";
            $path = "templates/{$nomeArquivo}";
            
            // Salvar no storage padrão (público)
            Storage::put($path, $response->body());

            // Atualizar template
            $template->update([
                'arquivo_path' => $path,
                'updated_by' => auth()->id()
            ]);

            // Extrair variáveis automaticamente
            $this->extrairVariaveis($template);
            
            \Log::info('OnlyOffice template salvo com sucesso', [
                'template_id' => $template->id,
                'arquivo_path' => $path,
                'url_utilizada' => $urlUtilizada,
                'tamanho_arquivo' => strlen($response->body())
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