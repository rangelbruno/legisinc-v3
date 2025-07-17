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
     * Get all documents
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
                
                $documents->push([
                    'id' => Str::slug($fileName),
                    'filename' => $fileName,
                    'title' => $this->extractTitle($content) ?: $this->formatTitle($fileName),
                    'path' => $file->getPathname(),
                    'last_modified' => $file->getMTime(),
                    'size' => $file->getSize(),
                    'content' => $content
                ]);
            }
        }

        return $documents->sortBy('title');
    }

    /**
     * Get sidebar data
     */
    private function getSidebarData($documents)
    {
        $sidebarData = [];
        
        foreach ($documents as $doc) {
            $category = $this->getDocumentCategory($doc['filename']);
            
            if (!isset($sidebarData[$category])) {
                $sidebarData[$category] = [];
            }
            
            $sidebarData[$category][] = [
                'id' => $doc['id'],
                'title' => $doc['title'],
                'filename' => $doc['filename'],
                'icon' => $this->getDocumentIcon($doc['filename']),
                'last_modified' => date('d/m/Y', $doc['last_modified'])
            ];
        }

        // Sort categories
        $categoryOrder = ['API', 'Editor', 'Sistema', 'Projeto', 'Melhorias', 'Geral'];
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
     * Extract overview
     */
    private function extractOverview($content)
    {
        $overview = [];
        
        if (preg_match('/\*\*Versão\*\*:\s*(.+)$/m', $content, $matches)) {
            $overview['version'] = trim($matches[1]);
        }
        
        if (preg_match('/\*\*Última Atualização\*\*:\s*(.+)$/m', $content, $matches)) {
            $overview['lastUpdate'] = trim($matches[1]);
        }

        return $overview;
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
            'api' => '🚀',
            'progress' => '📊',
            'projeto' => '📋',
            'editor' => '📝',
            'integration' => '🔗',
            'session' => '🎯',
            'permission' => '🔐',
            'parametros' => '⚙️',
            'pages' => '📄',
        ];

        foreach ($icons as $keyword => $icon) {
            if (str_contains(strtolower($filename), $keyword)) {
                return $icon;
            }
        }

        return '📄';
    }

    /**
     * Get document category
     */
    private function getDocumentCategory($filename)
    {
        $categories = [
            'api' => 'API',
            'integration' => 'API',
            'quick' => 'API',
            'editor' => 'Editor',
            'progress' => 'Projeto',
            'projeto' => 'Projeto',
            'session' => 'Sistema',
            'permission' => 'Sistema',
            'parametros' => 'Sistema',
            'pages' => 'Sistema',
            'overview' => 'Projeto',
            'flow' => 'Projeto',
            'improvements' => 'Melhorias',
            'create' => 'Melhorias',
            'modelos' => 'Melhorias',
        ];

        foreach ($categories as $keyword => $category) {
            if (str_contains(strtolower($filename), $keyword)) {
                return $category;
            }
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