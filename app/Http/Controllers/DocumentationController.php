<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    protected $documentationPath;

    public function __construct()
    {
        $this->documentationPath = base_path('docs');
    }

    /**
     * Display the documentation index page
     */
    public function index(Request $request)
    {
        try {
            $documents = $this->getAllDocuments();
            
            if ($documents->isEmpty()) {
                return view('documentation.empty', [
                    'sidebarData' => [],
                    'message' => 'Nenhum documento encontrado na pasta /docs'
                ]);
            }
            
            $sidebarData = $this->getSidebarData($documents);
            $selectedDocId = $request->get('doc') ?? $documents->first()['id'];
            $documentData = $this->getDocumentData($selectedDocId, $documents);
            
            if (!$documentData) {
                $documentData = $this->getDocumentData($documents->first()['id'], $documents);
            }
            
            return view('documentation.index', compact('sidebarData', 'documentData'));
            
        } catch (\Exception $e) {
            return view('documentation.error', [
                'error' => $e->getMessage(),
                'sidebarData' => []
            ]);
        }
    }

    /**
     * Display a specific document
     */
    public function show(Request $request, string $docId)
    {
        try {
            $documents = $this->getAllDocuments();
            $sidebarData = $this->getSidebarData($documents);
            $documentData = $this->getDocumentData($docId, $documents);
            
            if (!$documentData) {
                return redirect()->route('documentation.index')
                    ->with('error', 'Documento não encontrado');
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $documentData
                ]);
            }
            
            return view('documentation.index', compact('sidebarData', 'documentData'));
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 404);
            }
            
            return redirect()->route('documentation.index')
                ->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    /**
     * Get all documents with enhanced metadata
     */
    private function getAllDocuments()
    {
        if (!File::isDirectory($this->documentationPath)) {
            return collect();
        }

        $files = File::files($this->documentationPath);
        $documents = collect();

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $fileName = $file->getFilenameWithoutExtension();
                $content = File::get($file->getPathname());
                $metadata = $this->extractMetadata($content);
                
                $documents->push([
                    'id' => Str::slug($fileName),
                    'filename' => $fileName,
                    'title' => $this->extractTitle($content) ?: $this->formatTitle($fileName),
                    'path' => $file->getPathname(),
                    'last_modified' => $file->getMTime(),
                    'size' => $file->getSize(),
                    'content' => $content,
                    'metadata' => $metadata,
                    'category' => $this->getDocumentCategory($fileName),
                    'priority' => $this->getDocumentPriority($fileName, $metadata),
                    'status' => $this->getDocumentStatus($content, $metadata)
                ]);
            }
        }

        // Sort by priority first, then by title
        return $documents->sortBy([
            ['priority', 'asc'],
            ['title', 'asc']
        ]);
    }

    /**
     * Get enhanced sidebar data with metadata
     */
    private function getSidebarData($documents)
    {
        $sidebarData = [];
        
        foreach ($documents as $doc) {
            $category = $doc['category'];
            
            if (!isset($sidebarData[$category])) {
                $sidebarData[$category] = [];
            }
            
            $sidebarData[$category][] = [
                'id' => $doc['id'],
                'title' => $doc['title'],
                'filename' => $doc['filename'],
                'icon' => $this->getDocumentIcon($doc['filename']),
                'last_modified' => date('d/m/Y', $doc['last_modified']),
                'status' => $doc['status'],
                'priority' => $doc['priority'],
                'readingTime' => $doc['metadata']['readingTime'] ?? 1,
                'wordCount' => $doc['metadata']['wordCount'] ?? 0,
                'hasCode' => $doc['metadata']['hasCode'] ?? false,
                'hasImages' => $doc['metadata']['hasImages'] ?? false,
                'sections' => $doc['metadata']['sections'] ?? 0,
                'description' => $doc['metadata']['description'] ?? ''
            ];
        }
        
        // Sort documents within each category by priority
        foreach ($sidebarData as $category => $docs) {
            usort($sidebarData[$category], function($a, $b) {
                if ($a['priority'] === $b['priority']) {
                    return strcmp($a['title'], $b['title']);
                }
                return $a['priority'] <=> $b['priority'];
            });
        }

        // Sort categories
        $categoryOrder = [
            'Projeto', 
            'API', 
            'Sistema', 
            'Legislativo',
            'Editor', 
            'Workflows',
            'Guias',
            'Melhorias', 
            'Configuração',
            'Geral'
        ];
        $sorted = [];
        
        foreach ($categoryOrder as $category) {
            if (isset($sidebarData[$category])) {
                $sorted[$category] = $sidebarData[$category];
            }
        }

        // Add remaining categories
        foreach ($sidebarData as $category => $docs) {
            if (!isset($sorted[$category])) {
                $sorted[$category] = $docs;
            }
        }

        return $sorted;
    }

    /**
     * Get document data
     */
    private function getDocumentData($docId, $documents)
    {
        $document = $documents->firstWhere('id', $docId);
        
        if (!$document) {
            return null;
        }

        $content = $document['content'];
        
        return [
            'id' => $document['id'],
            'filename' => $document['filename'],
            'title' => $document['title'],
            'overview' => $this->extractOverview($content),
            'content' => $this->processMarkdownToHtml($content),
            'last_modified' => date('d/m/Y H:i', $document['last_modified']),
            'size' => $this->formatFileSize($document['size'])
        ];
    }

    /**
     * Extract title from content
     */
    private function extractTitle($content)
    {
        if (preg_match('/^# (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Format filename to title
     */
    private function formatTitle($filename)
    {
        return Str::title(str_replace(['-', '_'], ' ', $filename));
    }

    /**
     * Extract comprehensive metadata from document
     */
    private function extractMetadata($content)
    {
        $metadata = [];
        
        // Basic metadata patterns
        $patterns = [
            'version' => '/\*\*Versão\*\*:\s*(.+)$/m',
            'lastUpdate' => '/\*\*Última [Aa]tualização\*\*:\s*(.+)$/m',
            'status' => '/\*\*Status\*\*:\s*(.+)$/m',
            'author' => '/\*\*Autor\*\*:\s*(.+)$/m',
            'priority' => '/\*\*Prioridade\*\*:\s*(.+)$/m',
            'tags' => '/\*\*Tags\*\*:\s*(.+)$/m'
        ];
        
        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $metadata[$key] = trim($matches[1]);
            }
        }
        
        // Count sections (## headers)
        $metadata['sections'] = preg_match_all('/^## /m', $content);
        
        // Estimate reading time (words per minute)
        $wordCount = str_word_count(strip_tags($content));
        $metadata['readingTime'] = max(1, ceil($wordCount / 200));
        $metadata['wordCount'] = $wordCount;
        
        // Check for code blocks
        $metadata['hasCode'] = preg_match('/```/', $content) > 0;
        
        // Check for images
        $metadata['hasImages'] = preg_match('/!\[.*\]\(.*\)/', $content) > 0;
        
        // Extract description (first paragraph after title)
        if (preg_match('/^# .+\n\n(.+)$/m', $content, $matches)) {
            $metadata['description'] = Str::limit(trim($matches[1]), 150);
        }
        
        return $metadata;
    }
    
    /**
     * Extract overview (backward compatibility)
     */
    private function extractOverview($content)
    {
        return $this->extractMetadata($content);
    }
    
    /**
     * Get document priority for sorting
     */
    private function getDocumentPriority($filename, $metadata = [])
    {
        // Priority based on filename patterns
        $priorities = [
            'projeto' => 1,
            'readme' => 1,
            'progress' => 2,
            'overview' => 2,
            'quick' => 3,
            'api' => 4,
            'setup' => 5,
            'migration' => 6,
            'guia' => 7,
            'parametros' => 8,
            'proposicao' => 9
        ];
        
        // Check explicit priority in metadata
        if (isset($metadata['priority'])) {
            $explicitPriority = strtolower($metadata['priority']);
            if ($explicitPriority === 'high' || $explicitPriority === 'alta') return 1;
            if ($explicitPriority === 'medium' || $explicitPriority === 'média') return 5;
            if ($explicitPriority === 'low' || $explicitPriority === 'baixa') return 10;
        }
        
        $filenameLower = strtolower($filename);
        
        foreach ($priorities as $keyword => $priority) {
            if (str_contains($filenameLower, $keyword)) {
                return $priority;
            }
        }
        
        return 10; // Default priority
    }
    
    /**
     * Get document status
     */
    private function getDocumentStatus($content, $metadata = [])
    {
        // Check explicit status in metadata
        if (isset($metadata['status'])) {
            return $metadata['status'];
        }
        
        // Infer status from content patterns
        if (preg_match('/\b(completo|implementado|finalizado)\b/i', $content)) {
            return 'Completo';
        }
        
        if (preg_match('/\b(em desenvolvimento|wip|work in progress)\b/i', $content)) {
            return 'Em Desenvolvimento';
        }
        
        if (preg_match('/\b(rascunho|draft|preliminar)\b/i', $content)) {
            return 'Rascunho';
        }
        
        return 'Ativo';
    }

    /**
     * Process markdown to HTML
     */
    private function processMarkdownToHtml($content)
    {
        $lines = explode("\n", $content);
        $processedLines = [];
        $inCodeBlock = false;
        $codeBlockContent = '';
        $codeBlockLanguage = '';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Handle code blocks
            if (preg_match('/^```(\w+)?/', $trimmedLine, $matches)) {
                if (!$inCodeBlock) {
                    $inCodeBlock = true;
                    $codeBlockLanguage = $matches[1] ?? '';
                    $codeBlockContent = '';
                    continue;
                } else {
                    $inCodeBlock = false;
                    $languageClass = $codeBlockLanguage ? 'language-' . $codeBlockLanguage : '';
                    $processedLines[] = '<pre><code class="' . $languageClass . '">' . htmlspecialchars($codeBlockContent) . '</code></pre>';
                    $codeBlockContent = '';
                    continue;
                }
            }
            
            if ($inCodeBlock) {
                $codeBlockContent .= ($codeBlockContent ? "\n" : '') . $line;
                continue;
            }
            
            // Handle headers
            if (preg_match('/^# (.+)$/', $trimmedLine, $matches)) {
                $processedLines[] = '<h1>' . htmlspecialchars($matches[1]) . '</h1>';
                continue;
            }
            
            if (preg_match('/^## (.+)$/', $trimmedLine, $matches)) {
                $title = $matches[1];
                $id = Str::slug($title);
                $processedLines[] = '<h2 id="' . $id . '">' . htmlspecialchars($title) . '</h2>';
                continue;
            }
            
            if (preg_match('/^### (.+)$/', $trimmedLine, $matches)) {
                $title = $matches[1];
                $id = Str::slug($title);
                $processedLines[] = '<h3 id="' . $id . '">' . htmlspecialchars($title) . '</h3>';
                continue;
            }
            
            if (preg_match('/^#### (.+)$/', $trimmedLine, $matches)) {
                $title = $matches[1];
                $id = Str::slug($title);
                $processedLines[] = '<h4 id="' . $id . '">' . htmlspecialchars($title) . '</h4>';
                continue;
            }
            
            // Handle paragraphs
            if (!empty($trimmedLine)) {
                $processedLines[] = '<p>' . $this->processInlineMarkdown($trimmedLine) . '</p>';
            }
        }
        
        return implode("\n", $processedLines);
    }

    /**
     * Process inline markdown
     */
    private function processInlineMarkdown($text)
    {
        // Bold
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        
        // Italic
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        
        // Inline code
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);
        
        // Links
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        
        return $text;
    }

    /**
     * Get document icon
     */
    private function getDocumentIcon($filename)
    {
        $icons = [
            // API e Integração
            'api' => '🚀',
            'integration' => '🔗',
            'quick' => '⚡',
            
            // Projeto e Progresso
            'progress' => '📊',
            'projeto' => '📋',
            'overview' => '🔍',
            'flow' => '🌊',
            
            // Editor e Documentos
            'editor' => '📝',
            'pages' => '📄',
            
            // Sistema e Configuração
            'session' => '🎯',
            'permission' => '🔐',
            'parametros' => '⚙️',
            'configuracao' => '🔧',
            'setup' => '🛠️',
            'migration' => '🔄',
            'database' => '🗄️',
            'troubleshooting' => '🚨',
            
            // Melhorias
            'improvements' => '✨',
            'create' => '➕',
            'modelos' => '📐',
            
            // Guias
            'guia' => '📚',
            'readme' => '📖',
            'exemplos' => '💡',
            
            // Legislativo
            'proposicao' => '📜',
            'proposicoes' => '📋',
            
            // Workflows
            'processing' => '⚡',
            'processo' => '🔄'
        ];

        $filenameLower = strtolower($filename);
        
        foreach ($icons as $keyword => $icon) {
            if (str_contains($filenameLower, $keyword)) {
                return $icon;
            }
        }

        // Ícones por padrão de nomenclatura
        if (preg_match('/^[A-Z_]+$/', $filename)) {
            return '🔧';
        }
        
        return '📄';
    }

    /**
     * Get document category
     */
    private function getDocumentCategory($filename)
    {
        $categories = [
            // API e Integração
            'api' => 'API',
            'integration' => 'API',
            'quick' => 'API',
            
            // Editor e Documentos
            'editor' => 'Editor',
            
            // Projeto Principal
            'progress' => 'Projeto',
            'projeto' => 'Projeto',
            'overview' => 'Projeto',
            'flow' => 'Projeto',
            
            // Sistema e Configuração
            'session' => 'Sistema',
            'permission' => 'Sistema',
            'parametros' => 'Sistema',
            'pages' => 'Sistema',
            'configuracao' => 'Sistema',
            'setup' => 'Sistema',
            'troubleshooting' => 'Sistema',
            'migration' => 'Sistema',
            'database' => 'Sistema',
            
            // Melhorias e Desenvolvimento
            'improvements' => 'Melhorias',
            'create' => 'Melhorias',
            'modelos' => 'Melhorias',
            
            // Guias e Documentação
            'guia' => 'Guias',
            'readme' => 'Guias',
            'exemplos' => 'Guias',
            'quick_start' => 'Guias',
            
            // Proposições e Legislativo
            'proposicao' => 'Legislativo',
            'proposicoes' => 'Legislativo',
            
            // Processamento e Workflows
            'processing' => 'Workflows',
            'processo' => 'Workflows'
        ];

        $filenameLower = strtolower($filename);
        
        // Busca por palavras-chave específicas
        foreach ($categories as $keyword => $category) {
            if (str_contains($filenameLower, $keyword)) {
                return $category;
            }
        }
        
        // Categorização baseada em padrões de nomenclatura
        if (preg_match('/^[A-Z_]+$/', $filename)) {
            return 'Configuração';
        }
        
        return 'Geral';
    }

    /**
     * Format file size
     */
    private function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        
        return number_format($size / pow(1024, $power), 2, '.', '') . ' ' . $units[$power];
    }

    /**
     * Get documentation statistics
     */
    public function statistics()
    {
        try {
            $documents = $this->getAllDocuments();
            $categories = [];
            $totalWords = 0;
            $totalReadingTime = 0;
            $statusCounts = [];
            
            foreach ($documents as $doc) {
                $category = $doc['category'];
                if (!isset($categories[$category])) {
                    $categories[$category] = 0;
                }
                $categories[$category]++;
                
                $totalWords += $doc['metadata']['wordCount'] ?? 0;
                $totalReadingTime += $doc['metadata']['readingTime'] ?? 0;
                
                $status = $doc['status'];
                if (!isset($statusCounts[$status])) {
                    $statusCounts[$status] = 0;
                }
                $statusCounts[$status]++;
            }
            
            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_documents' => $documents->count(),
                    'categories' => $categories,
                    'total_words' => $totalWords,
                    'total_reading_time' => $totalReadingTime,
                    'status_distribution' => $statusCounts,
                    'average_words_per_doc' => $documents->count() > 0 ? round($totalWords / $documents->count()) : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search documents
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Query deve ter pelo menos 3 caracteres'
            ]);
        }
        
        try {
            $documents = $this->getAllDocuments();
            $results = [];
            
            foreach ($documents as $doc) {
                $content = strtolower($doc['content']);
                $title = strtolower($doc['title']);
                $queryLower = strtolower($query);
                
                if (str_contains($title, $queryLower) || str_contains($content, $queryLower)) {
                    $results[] = [
                        'id' => $doc['id'],
                        'title' => $doc['title'],
                        'filename' => $doc['filename'],
                        'icon' => $this->getDocumentIcon($doc['filename']),
                        'excerpt' => $this->generateExcerpt($doc['content'], $query)
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'results' => array_slice($results, 0, 10)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate excerpt
     */
    private function generateExcerpt($content, $query)
    {
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/[#*`\[\]()]/m', '', $cleanContent);
        
        $queryLower = strtolower($query);
        $contentLower = strtolower($cleanContent);
        
        $pos = strpos($contentLower, $queryLower);
        
        if ($pos === false) {
            return substr($cleanContent, 0, 200) . '...';
        }
        
        $start = max(0, $pos - 100);
        $excerpt = substr($cleanContent, $start, 200);
        
        // Highlight the search term
        $excerpt = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $excerpt);
        
        return $start > 0 ? '...' . $excerpt . '...' : $excerpt . '...';
    }
} 