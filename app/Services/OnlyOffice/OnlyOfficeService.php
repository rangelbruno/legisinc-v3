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
        // Adicionar timestamp e document key para evitar cache
        $separator = strpos($fileUrl, '?') !== false ? '&' : '?';
        $fileUrl .= $separator . 't=' . time() . '&key=' . $documentKey;
        
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
                'coEditing' => [
                    'mode' => 'fast',   // Use fast mode for better performance
                    'change' => false   // Disable change notifications
                ],
                'embedded' => [
                    'saveUrl' => $this->callbackUrl . '/' . $documentKey,
                    'toolbarDocked' => 'top'
                ],
                'user' => [
                    'id' => (string) $user['id'],
                    'name' => $user['name'],
                    'group' => $user['group'] ?? 'default'
                ],
                'customization' => [
                    'about' => false,
                    'feedback' => false,
                    'forcesave' => true,  // Enable forcesave for better synchronization
                    'submitForm' => false, // Disable submit form to prevent auto closure
                    'autosave' => true,   // Enable autosave for continuous saving
                    'compactToolbar' => false,
                    'toolbarNoTabs' => false,
                    'reviewDisplay' => 'markup',
                    'trackChanges' => false,
                    'goback' => false,    // Disable go back button
                    'chat' => false,      // Disable chat
                    'comments' => true,   // Keep comments enabled
                    'help' => false,      // Disable help
                    'plugins' => false,   // Disable plugins
                    'spellcheck' => [
                        'mode' => true,
                        'lang' => config('onlyoffice.locale.spellcheck')
                    ],
                    'close' => false      // Disable close button to prevent accidental closure
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
                if (isset($data['url']) && $data['url'] === 'force_save_request') {
                    // For force save requests, just acknowledge - no actual save needed
                    // The content is still being edited in the browser
                    \Log::info('Force save request acknowledged (no action needed)', ['document_key' => $documentKey]);
                } else if (isset($data['url'])) {
                    // Only save if there's a real URL from OnlyOffice
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
            
            // Document key not found - likely an orphaned callback from an old session
            // Try to find the most recently updated model and save there
            $modeloRecente = \App\Models\Documento\DocumentoModelo::orderBy('updated_at', 'desc')->first();
            if ($modeloRecente) {
                \Log::warning('Orphaned callback - using most recent model:', [
                    'orphaned_key' => $documentKey,
                    'using_model_id' => $modeloRecente->id,
                    'current_key' => $modeloRecente->document_key
                ]);
                $this->salvarModelo($modeloRecente, $url);
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
            // Use existing file name to maintain consistency, or create new one if doesn't exist
            if ($modelo->arquivo_nome && $modelo->arquivo_path) {
                $nomeArquivo = $modelo->arquivo_nome;
                $path = $modelo->arquivo_path;
                \Log::info('Using existing file path:', ['arquivo_nome' => $nomeArquivo, 'arquivo_path' => $path]);
            } else {
                // Generate new file name only if no existing file
                $timestamp = time();
                $nomeArquivo = "modelo_{$modelo->id}_{$timestamp}.rtf";
                $path = "documentos/modelos/{$nomeArquivo}";
                \Log::info('Creating new file path:', ['arquivo_nome' => $nomeArquivo, 'arquivo_path' => $path]);
            }
            
            \Log::info('About to save file:', [
                'model_id' => $modelo->id,
                'path' => $path,
                'file_size' => strlen($response->body()),
                'arquivo_nome' => $nomeArquivo,
                'old_arquivo_nome' => $modelo->arquivo_nome
            ]);
            
            // Get full paths for debugging
            $fullPath = Storage::disk('public')->path($path);
            $directory = dirname($fullPath);
            
            \Log::info('File save paths:', [
                'path' => $path,
                'full_path' => $fullPath,
                'directory' => $directory,
                'directory_exists' => is_dir($directory),
                'directory_writable' => is_writable($directory),
                'response_body_size' => strlen($response->body())
            ]);
            
            // Ensure directory exists
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
                \Log::info('Created directory:', ['directory' => $directory]);
            }
            
            // Try Laravel Storage put method first
            try {
                $saved = Storage::disk('public')->put($path, $response->body());
                
                // Verify the file was actually saved correctly
                if ($saved && Storage::disk('public')->exists($path)) {
                    $savedSize = Storage::disk('public')->size($path);
                    $expectedSize = strlen($response->body());
                    
                    if ($savedSize === $expectedSize) {
                        \Log::info('File saved via Storage facade:', [
                            'path' => $path,
                            'full_path' => $fullPath,
                            'file_exists' => true,
                            'file_size' => $savedSize,
                            'expected_size' => $expectedSize
                        ]);
                    } else {
                        // File saved but with wrong size - try fallback
                        \Log::warning('File saved with incorrect size, trying fallback:', [
                            'path' => $path,
                            'saved_size' => $savedSize,
                            'expected_size' => $expectedSize
                        ]);
                        $saved = false;
                    }
                } else {
                    \Log::warning('Storage put returned success but file not found/created');
                    $saved = false;
                }
            } catch (\Exception $e) {
                \Log::warning('Storage put failed with exception:', [
                    'error' => $e->getMessage()
                ]);
                $saved = false;
            }
            
            // If Laravel Storage failed, try direct file_put_contents
            if (!$saved) {
                \Log::info('Trying direct file_put_contents as fallback');
                
                // For RTF files, make sure we handle binary content correctly
                $content = $response->body();
                \Log::info('Content analysis before save:', [
                    'content_length' => strlen($content),
                    'is_binary' => !mb_check_encoding($content, 'UTF-8'),
                    'first_100_chars' => substr($content, 0, 100),
                    'last_100_chars' => substr($content, -100)
                ]);
                
                $bytesWritten = file_put_contents($fullPath, $content, LOCK_EX);
                
                if ($bytesWritten !== false && $bytesWritten > 0) {
                    $actualSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                    
                    \Log::info('File saved via file_put_contents:', [
                        'full_path' => $fullPath,
                        'bytes_written' => $bytesWritten,
                        'file_exists' => file_exists($fullPath),
                        'file_size' => $actualSize,
                        'expected_size' => strlen($content)
                    ]);
                    
                    if ($actualSize === strlen($content)) {
                        $saved = true;
                        // Verify content integrity by reading back
                        $savedContent = file_get_contents($fullPath);
                        if (strlen($savedContent) !== strlen($content)) {
                            \Log::error('Content verification failed:', [
                                'original_size' => strlen($content),
                                'saved_size' => strlen($savedContent)
                            ]);
                            $saved = false;
                        } else {
                            \Log::info('Content verification passed');
                        }
                    } else {
                        \Log::error('File saved but with incorrect size via file_put_contents:', [
                            'expected' => strlen($content),
                            'actual' => $actualSize
                        ]);
                        $saved = false;
                    }
                } else {
                    \Log::error('file_put_contents failed:', [
                        'full_path' => $fullPath,
                        'bytes_written' => $bytesWritten,
                        'directory_writable' => is_writable($directory),
                        'directory_permissions' => substr(sprintf('%o', fileperms($directory)), -4),
                        'disk_space' => disk_free_space($directory),
                        'current_user' => posix_getpwuid(posix_geteuid())['name'] ?? 'unknown'
                    ]);
                    $saved = false;
                }
            }
            
            \Log::info('File save result:', [
                'model_id' => $modelo->id,
                'path' => $path,
                'saved' => $saved,
                'exists_after_save' => Storage::disk('public')->exists($path),
                'size_after_save' => Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0
            ]);
            
            if ($saved) {
                // Delete old file if it exists and is different
                if ($modelo->arquivo_path && $modelo->arquivo_path !== $path && Storage::disk('public')->exists($modelo->arquivo_path)) {
                    try {
                        Storage::disk('public')->delete($modelo->arquivo_path);
                        \Log::info('Old file deleted:', ['old_path' => $modelo->arquivo_path]);
                    } catch (\Exception $e) {
                        \Log::warning('Could not delete old file:', ['old_path' => $modelo->arquivo_path, 'error' => $e->getMessage()]);
                    }
                }
                
                // Generate new document key to prevent version conflicts
                $newDocumentKey = 'modelo_' . $modelo->id . '_' . time() . '_' . uniqid();
                
                // Update modelo to point to the new file with new document key
                $modelo->update([
                    'arquivo_path' => $path,
                    'arquivo_nome' => $nomeArquivo,
                    'arquivo_size' => strlen($response->body()),
                    'document_key' => $newDocumentKey
                ]);
                
                \Log::info('Modelo updated after OnlyOffice save:', [
                    'modelo_id' => $modelo->id,
                    'new_document_key' => $newDocumentKey,
                    'updated_at' => $modelo->fresh()->updated_at->toISOString(),
                    'timestamp' => $modelo->fresh()->updated_at->timestamp
                ]);
                
                \Log::info('Updated modelo database record:', [
                    'model_id' => $modelo->id,
                    'new_arquivo_path' => $path,
                    'new_arquivo_nome' => $nomeArquivo
                ]);
            } else {
                \Log::error('Failed to save file to storage', [
                    'model_id' => $modelo->id,
                    'path' => $path,
                    'disk_info' => [
                        'disk' => 'public',
                        'root' => Storage::disk('public')->path('')
                    ]
                ]);
                throw new \Exception('Failed to save file to storage');
            }
            
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