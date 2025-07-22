<?php

namespace App\Services\OnlyOffice;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class OnlyOfficeService
{
    private string $serverUrl;
    private string $jwtSecret;
    private string $callbackUrl;
    
    public function __construct()
    {
        $this->serverUrl = config('onlyoffice.server_url');
        $this->jwtSecret = config('onlyoffice.jwt_secret');
        $this->callbackUrl = config('onlyoffice.callback_url');
    }
    
    public function criarConfiguracao(string $documentKey, string $fileName, string $fileUrl, array $user, string $mode = 'edit'): array
    {
        $config = [
            'document' => [
                'fileType' => pathinfo($fileName, PATHINFO_EXTENSION),
                'key' => $documentKey,
                'title' => $fileName,
                'url' => $fileUrl,
                'permissions' => $this->obterPermissoes($mode),
                'lang' => config('onlyoffice.locale.lang')
            ],
            'documentType' => $this->determinarTipoDocumento($fileName),
            'editorConfig' => [
                'mode' => $mode,
                'lang' => config('onlyoffice.locale.lang'),
                'region' => config('onlyoffice.locale.region'),
                'callbackUrl' => $this->callbackUrl . '/' . $documentKey,
                'user' => [
                    'id' => (string) $user['id'],
                    'name' => $user['name'],
                    'group' => $user['group'] ?? 'default'
                ],
                'customization' => [
                    'about' => false,
                    'feedback' => false,
                    'forcesave' => true,
                    'submitForm' => true,
                    'autosave' => true,
                    'compactToolbar' => false,
                    'toolbarNoTabs' => false,
                    'reviewDisplay' => 'markup',
                    'trackChanges' => true,
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => config('onlyoffice.locale.spellcheck')
                    ]
                ],
                'plugins' => [
                    'autostart' => [
                        'url' => 'https://example.com/plugin/'
                    ]
                ]
            ],
            'height' => '100%',
            'width' => '100%',
            'type' => 'desktop'
        ];
        
        // Adicionar JWT se configurado
        if ($this->jwtSecret) {
            $config['token'] = JWT::encode($config, $this->jwtSecret, 'HS256');
        }
        
        return $config;
    }
    
    public function processarCallback(string $documentKey, array $data): array
    {
        // Verificar JWT
        if ($this->jwtSecret && isset($data['token'])) {
            try {
                $decoded = JWT::decode($data['token'], new Key($this->jwtSecret, 'HS256'));
                $data = (array) $decoded;
            } catch (\Exception $e) {
                throw new \Exception('Token JWT inválido');
            }
        }
        
        $status = $data['status'];
        $resultado = ['error' => 0];
        
        \Log::info('Processing OnlyOffice callback:', [
            'document_key' => $documentKey,
            'status' => $status,
            'has_url' => isset($data['url']),
            'url' => $data['url'] ?? 'N/A'
        ]);
        
        switch ($status) {
            case 1: // Editando
                \Log::info('Document is being edited', ['document_key' => $documentKey]);
                break;
                
            case 2: // Pronto para salvar
                \Log::info('Document ready to save', ['document_key' => $documentKey]);
                if (isset($data['url'])) {
                    $this->salvarDocumento($documentKey, $data['url']);
                    $resultado['error'] = 0;
                }
                break;
                
            case 3: // Erro ao salvar
                \Log::warning('Error saving document', ['document_key' => $documentKey]);
                if (isset($data['url'])) {
                    $this->salvarDocumento($documentKey, $data['url']);
                    $resultado['error'] = 0;
                }
                break;
                
            case 4: // Documento fechado sem alterações
                \Log::info('Document closed without changes', ['document_key' => $documentKey]);
                break;
                
            case 6: // Editando, mas documento foi salvo
                \Log::info('Document being edited but saved', ['document_key' => $documentKey]);
                if (isset($data['url'])) {
                    $this->salvarDocumento($documentKey, $data['url']);
                }
                break;
                
            case 7: // Erro ao editar
                \Log::error('Error editing document', ['document_key' => $documentKey]);
                if (isset($data['url'])) {
                    $this->salvarDocumento($documentKey, $data['url']);
                }
                break;
                
            default:
                \Log::warning('Unknown callback status', ['document_key' => $documentKey, 'status' => $status]);
                break;
        }
        
        return $resultado;
    }
    
    private function salvarDocumento(string $documentKey, string $url): void
    {
        // Buscar instância do documento
        $instancia = \App\Models\Documento\DocumentoInstancia::where('document_key', $documentKey)->first();
        
        if (!$instancia) {
            // Se não é instância, pode ser modelo
            $modelo = \App\Models\Documento\DocumentoModelo::where('document_key', $documentKey)->first();
            if ($modelo) {
                $this->salvarModelo($modelo, $url);
                return;
            }
            throw new \Exception('Documento não encontrado');
        }
        
        // Download do arquivo atualizado
        $internalUrl = $this->convertToInternalUrl($url);
        \Log::info('Downloading instance file from OnlyOffice:', ['original_url' => $url, 'internal_url' => $internalUrl]);
        
        $response = Http::get($internalUrl);
        
        if ($response->successful()) {
            // Criar nova versão
            $versao = $instancia->versoes()->count() + 1;
            $nomeArquivo = "documento_{$instancia->id}_v{$versao}.rtf";
            $path = "documentos/versoes/{$nomeArquivo}";
            
            // Use public disk to avoid private folder issues
            Storage::disk('public')->put($path, $response->body());
            
            // Registrar nova versão
            \App\Models\Documento\DocumentoVersao::create([
                'instancia_id' => $instancia->id,
                'arquivo_path' => $path,
                'arquivo_nome' => $nomeArquivo,
                'versao' => $versao,
                'modificado_por' => auth()->id(),
                'hash_arquivo' => hash('sha256', $response->body())
            ]);
            
            // Atualizar instância
            $instancia->update([
                'arquivo_path' => $path,
                'arquivo_nome' => $nomeArquivo,
                'versao' => $versao,
                'editado_em' => now(),
                'updated_by' => auth()->id()
            ]);
        }
    }
    
    private function salvarModelo(\App\Models\Documento\DocumentoModelo $modelo, string $url): void
    {
        // Convert URL for internal container access
        $internalUrl = $this->convertToInternalUrl($url);
        \Log::info('Downloading file from OnlyOffice:', ['original_url' => $url, 'internal_url' => $internalUrl]);
        
        $response = Http::get($internalUrl);
        
        if ($response->successful()) {
            $nomeArquivo = $modelo->arquivo_nome;
            $path = "documentos/modelos/{$nomeArquivo}";
            
            // Use public disk to avoid private folder issues
            Storage::disk('public')->put($path, $response->body());
            
            $modelo->update([
                'arquivo_path' => $path,
                'arquivo_size' => strlen($response->body())
            ]);
            
            \Log::info('Model file saved successfully:', [
                'model_id' => $modelo->id,
                'path' => $path,
                'size' => strlen($response->body())
            ]);
        } else {
            \Log::error('Failed to download model file from OnlyOffice:', [
                'model_id' => $modelo->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Failed to download file from OnlyOffice');
        }
    }
    
    private function determinarTipoDocumento(string $fileName): string
    {
        $extensao = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($extensao, config('onlyoffice.document_types.text'))) {
            return 'text';
        } elseif (in_array($extensao, config('onlyoffice.document_types.spreadsheet'))) {
            return 'cell';
        } elseif (in_array($extensao, config('onlyoffice.document_types.presentation'))) {
            return 'slide';
        }
        
        return 'text';
    }
    
    private function obterPermissoes(string $mode): array
    {
        $permissoes = config('onlyoffice.default_permissions');
        
        if ($mode === 'view') {
            $permissoes['edit'] = false;
            $permissoes['comment'] = false;
            $permissoes['fillForms'] = false;
            $permissoes['review'] = false;
        }
        
        return $permissoes;
    }
    
    public function converterParaPDF(string $documentPath): string
    {
        $conversion = [
            'async' => false,
            'filetype' => 'pdf',
            'key' => uniqid(),
            'outputtype' => 'pdf',
            'title' => 'conversion.pdf',
            'url' => route('onlyoffice.download', ['path' => $documentPath])
        ];
        
        if ($this->jwtSecret) {
            $conversion['token'] = JWT::encode($conversion, $this->jwtSecret, 'HS256');
        }
        
        $response = Http::post($this->serverUrl . '/ConvertService.ashx', $conversion);
        
        if ($response->successful()) {
            $result = $response->json();
            if (isset($result['fileUrl'])) {
                return $result['fileUrl'];
            }
        }
        
        throw new \Exception('Erro na conversão para PDF');
    }
    
    public function obterGrupoUsuario(\App\Models\User $user): string
    {
        $grupos = config('onlyoffice.user_groups');
        
        if ($user->hasRole('admin')) return $grupos['admin'];
        if ($user->hasRole('legislativo')) return $grupos['legislativo'];
        if ($user->hasRole('parlamentar')) return $grupos['parlamentar'];
        if ($user->hasRole('assessor')) return $grupos['assessor'];
        
        return 'default';
    }
    
    /**
     * Convert OnlyOffice URLs from external format to internal container format
     */
    private function convertToInternalUrl(string $url): string
    {
        // Replace external localhost:8080 with internal container name
        return str_replace('http://localhost:8080', 'http://legisinc-onlyoffice:80', $url);
    }
}