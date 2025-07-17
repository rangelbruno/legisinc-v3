<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class DocumentationService
{
    protected $documentationPath;

    public function __construct()
    {
        $this->documentationPath = base_path('docs');
    }

    /**
     * Get all documentation files
     */
    public function getAllDocuments(): Collection
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
     * Get a specific document by ID
     */
    public function getDocument(string $id): ?array
    {
        $documents = $this->getAllDocuments();
        return $documents->firstWhere('id', $id);
    }

    /**
     * Get processed documentation data for a specific document
     */
    public function getDocumentData(string $id): array
    {
        $document = $this->getDocument($id);
        
        if (!$document) {
            throw new \Exception('Document not found');
        }

        $content = $document['content'];
        
        return [
            'id' => $document['id'],
            'filename' => $document['filename'],
            'title' => $document['title'],
            'overview' => $this->extractOverview($content),
            'sections' => $this->extractSections($content),
            'lastUpdate' => $this->extractLastUpdate($content),
            'version' => $this->extractVersion($content),
            'content' => $this->processMarkdownToHtml($content),
            'last_modified' => date('Y-m-d H:i:s', $document['last_modified']),
            'size' => $this->formatFileSize($document['size'])
        ];
    }

    /**
     * Get sidebar data for all documents
     */
    public function getSidebarData(): array
    {
        $documents = $this->getAllDocuments();
        
        $sidebarData = [];
        foreach ($documents as $doc) {
            $sidebarData[] = [
                'id' => $doc['id'],
                'title' => $doc['title'],
                'filename' => $doc['filename'],
                'icon' => $this->getDocumentIcon($doc['filename']),
                'category' => $this->getDocumentCategory($doc['filename']),
                'last_modified' => date('d/m/Y', $doc['last_modified'])
            ];
        }

        return $this->groupDocumentsByCategory($sidebarData);
    }

    /**
     * Extract title from markdown content
     */
    protected function extractTitle(string $content): ?string
    {
        if (preg_match('/^# (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Format filename to readable title
     */
    protected function formatTitle(string $filename): string
    {
        return Str::title(str_replace(['-', '_'], ' ', $filename));
    }

    /**
     * Extract overview information from content
     */
    protected function extractOverview(string $content): array
    {
        $overview = [];
        
        // Extract version
        if (preg_match('/\*\*Versão\*\*:\s*(.+)$/m', $content, $matches)) {
            $overview['version'] = trim($matches[1]);
        }
        
        // Extract last update
        if (preg_match('/\*\*Última Atualização\*\*:\s*(.+)$/m', $content, $matches)) {
            $overview['lastUpdate'] = trim($matches[1]);
        }
        
        // Extract author
        if (preg_match('/\*\*Autor\*\*:\s*(.+)$/m', $content, $matches)) {
            $overview['author'] = trim($matches[1]);
        }

        return $overview;
    }

    /**
     * Extract sections from content
     */
    protected function extractSections(string $content): array
    {
        $sections = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            if (preg_match('/^(#{2,4})\s+(.+)$/', $line, $matches)) {
                $level = strlen($matches[1]);
                $title = trim($matches[2]);
                $id = Str::slug($title);
                
                $sections[] = [
                    'level' => $level,
                    'title' => $title,
                    'id' => $id,
                    'anchor' => '#' . $id
                ];
            }
        }
        
        return $sections;
    }

    /**
     * Extract last update date
     */
    protected function extractLastUpdate(string $content): ?string
    {
        if (preg_match('/\*\*Última Atualização\*\*:\s*(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Extract version
     */
    protected function extractVersion(string $content): ?string
    {
        if (preg_match('/\*\*Versão\*\*:\s*(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Process markdown content to HTML
     */
    protected function processMarkdownToHtml(string $content): string
    {
        // Similar to ApiDocumentationService but adapted for general markdown
        $lines = explode("\n", $content);
        $processedLines = [];
        $inCodeBlock = false;
        $inList = false;
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
                $inList = false;
                $processedLines[] = '<h1>' . htmlspecialchars($matches[1]) . '</h1>';
                continue;
            }
            
            if (preg_match('/^## (.+)$/', $trimmedLine, $matches)) {
                $inList = false;
                $title = $matches[1];
                $id = Str::slug(strip_tags($title));
                $processedLines[] = '<h2 id="' . $id . '">' . htmlspecialchars($title) . '</h2>';
                continue;
            }
            
            if (preg_match('/^### (.+)$/', $trimmedLine, $matches)) {
                $inList = false;
                $title = $matches[1];
                $id = Str::slug(strip_tags($title));
                $processedLines[] = '<h3 id="' . $id . '">' . htmlspecialchars($title) . '</h3>';
                continue;
            }
            
            if (preg_match('/^#### (.+)$/', $trimmedLine, $matches)) {
                $inList = false;
                $title = $matches[1];
                $id = Str::slug(strip_tags($title));
                $processedLines[] = '<h4 id="' . $id . '">' . htmlspecialchars($title) . '</h4>';
                continue;
            }
            
            // Handle lists
            if (preg_match('/^[-*+]\s+(.+)$/', $trimmedLine, $matches)) {
                if (!$inList) {
                    $processedLines[] = '<ul>';
                    $inList = true;
                }
                $processedLines[] = '<li>' . $this->processInlineMarkdown($matches[1]) . '</li>';
                continue;
            }
            
            if (preg_match('/^\d+\.\s+(.+)$/', $trimmedLine, $matches)) {
                if (!$inList) {
                    $processedLines[] = '<ol>';
                    $inList = true;
                }
                $processedLines[] = '<li>' . $this->processInlineMarkdown($matches[1]) . '</li>';
                continue;
            }
            
            // Close list if we're not in one anymore
            if ($inList && !preg_match('/^[-*+\d]\s+/', $trimmedLine) && !empty($trimmedLine)) {
                $processedLines[] = '</ul>';
                $inList = false;
            }
            
            // Handle paragraphs
            if (!empty($trimmedLine)) {
                $processedLines[] = '<p>' . $this->processInlineMarkdown($trimmedLine) . '</p>';
            } else {
                $processedLines[] = '<br>';
            }
        }
        
        // Close any open lists
        if ($inList) {
            $processedLines[] = '</ul>';
        }
        
        return implode("\n", $processedLines);
    }

    /**
     * Process inline markdown (bold, italic, code, links)
     */
    protected function processInlineMarkdown(string $text): string
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
     * Get document icon based on filename
     */
    protected function getDocumentIcon(string $filename): string
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

        return '📄'; // Default icon
    }

    /**
     * Get document category based on filename
     */
    protected function getDocumentCategory(string $filename): string
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

        return 'Geral'; // Default category
    }

    /**
     * Group documents by category
     */
    protected function groupDocumentsByCategory(array $documents): array
    {
        $grouped = [];
        
        foreach ($documents as $doc) {
            $category = $doc['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $doc;
        }

        // Sort categories
        $categoryOrder = ['API', 'Editor', 'Sistema', 'Projeto', 'Melhorias', 'Geral'];
        $sorted = [];
        
        foreach ($categoryOrder as $category) {
            if (isset($grouped[$category])) {
                $sorted[$category] = $grouped[$category];
            }
        }

        // Add any remaining categories
        foreach ($grouped as $category => $docs) {
            if (!isset($sorted[$category])) {
                $sorted[$category] = $docs;
            }
        }

        return $sorted;
    }

    /**
     * Format file size
     */
    protected function formatFileSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        
        return number_format($size / pow(1024, $power), 2, '.', '') . ' ' . $units[$power];
    }
} 