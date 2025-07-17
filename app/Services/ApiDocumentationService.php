<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiDocumentationService
{
    protected $documentationFilePath;

    public function __construct()
    {
        $this->documentationFilePath = base_path('docs/apiDocumentation.md');
    }

    public function getDocumentationData()
    {
        $content = $this->getDocumentationFileContent();
        
        return [
            'title' => $this->extractTitle($content),
            'overview' => $this->extractOverview($content),
            'sections' => $this->extractSections($content),
            'lastUpdate' => $this->extractLastUpdate($content),
            'version' => $this->extractVersion($content),
            'content' => $this->processMarkdownToHtml($content)
        ];
    }

    protected function getDocumentationFileContent()
    {
        if (!File::exists($this->documentationFilePath)) {
            throw new \Exception('API Documentation file not found');
        }

        return File::get($this->documentationFilePath);
    }

    protected function extractTitle($content)
    {
        if (preg_match('/^# (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return 'Documentação da API';
    }

    protected function extractOverview($content)
    {
        $overview = [];
        
        // Extrair versão
        if (preg_match('/\*\*Versão\*\*: (.+)$/m', $content, $matches)) {
            $overview['version'] = trim($matches[1]);
        }
        
        // Extrair última atualização
        if (preg_match('/\*\*Última Atualização\*\*: (.+)$/m', $content, $matches)) {
            $overview['lastUpdate'] = trim($matches[1]);
        }
        
        // Extrair Base URL
        if (preg_match('/\*\*Base URL\*\*: (.+)$/m', $content, $matches)) {
            $overview['baseUrl'] = trim($matches[1]);
        }

        return $overview;
    }

    protected function extractSections($content)
    {
        $sections = [];
        
        // Extrair seções principais (## título)
        preg_match_all('/^## (.+)$/m', $content, $matches);
        
        foreach ($matches[1] as $sectionTitle) {
            $sections[] = [
                'title' => trim($sectionTitle),
                'id' => Str::slug(trim($sectionTitle)),
                'icon' => $this->getSectionIcon(trim($sectionTitle))
            ];
        }

        return $sections;
    }

    protected function extractLastUpdate($content)
    {
        if (preg_match('/\*\*Última Atualização\*\*: (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    protected function extractVersion($content)
    {
        if (preg_match('/\*\*Versão\*\*: (.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return '1.0.0';
    }

    protected function processMarkdownToHtml($content)
    {
        // Split content into lines for better processing
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
                    // Create code block with proper language class
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
                $processedLines[] = '<h1>' . $matches[1] . '</h1>';
                continue;
            }
            
            if (preg_match('/^## (.+)$/', $trimmedLine, $matches)) {
                $inList = false;
                $title = $matches[1];
                $id = Str::slug(strip_tags($title));
                $processedLines[] = '<h2 id="' . $id . '">' . $title . '</h2>';
                continue;
            }
            
            if (preg_match('/^### (.+)$/', $trimmedLine, $matches)) {
                $inList = false;
                $processedLines[] = '<h3>' . $matches[1] . '</h3>';
                continue;
            }
            
            if (preg_match('/^#### (.+)$/', $trimmedLine, $matches)) {
                $inList = false;
                $processedLines[] = '<h4>' . $matches[1] . '</h4>';
                continue;
            }
            
            // Handle lists
            if (preg_match('/^- (.+)$/', $trimmedLine, $matches)) {
                if (!$inList) {
                    $processedLines[] = '<ul>';
                    $inList = true;
                }
                $processedLines[] = '<li>' . $this->processInlineMarkdown($matches[1]) . '</li>';
                continue;
            } else if ($inList) {
                $processedLines[] = '</ul>';
                $inList = false;
            }
            
            // Handle HTTP method indicators
            if (preg_match('/^(GET|POST|PUT|DELETE|PATCH)\s+(.+)$/', $trimmedLine, $matches)) {
                $method = strtolower($matches[1]);
                $endpoint = $matches[2];
                $processedLines[] = '<div class="endpoint-method">';
                $processedLines[] = '<span class="endpoint-badge ' . $method . '">' . $matches[1] . '</span>';
                $processedLines[] = '<code class="endpoint-path">' . $endpoint . '</code>';
                $processedLines[] = '</div>';
                continue;
            }
            
            // Handle empty lines
            if (empty($trimmedLine)) {
                $processedLines[] = '';
                continue;
            }
            
            // Handle regular paragraphs
            if (!preg_match('/^<[h|u|d]/', $trimmedLine)) {
                $processedLines[] = '<p>' . $this->processInlineMarkdown($trimmedLine) . '</p>';
            } else {
                $processedLines[] = $trimmedLine;
            }
        }
        
        // Close any open lists
        if ($inList) {
            $processedLines[] = '</ul>';
        }
        
        return implode("\n", $processedLines);
    }
    
    protected function processInlineMarkdown($text)
    {
        // Process inline code first to avoid conflicts
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        
        // Process bold text
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        
        // Process italic text
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        
        // Process links
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        
        return $text;
    }

    protected function getSectionIcon($sectionTitle)
    {
        $icons = [
            'Visão Geral' => 'fas fa-info-circle',
            'Configuração Inicial' => 'fas fa-cog',
            'Autenticação' => 'fas fa-lock',
            'Gestão de Usuários' => 'fas fa-users',
            'Gestão de Parlamentares' => 'fas fa-user-tie',
            'Gestão de Projetos' => 'fas fa-file-alt',
            'Tramitação' => 'fas fa-route',
            'Anexos' => 'fas fa-paperclip',
            'Relatórios' => 'fas fa-chart-bar',
            'Busca e Filtros' => 'fas fa-search',
            'Métricas' => 'fas fa-chart-line',
            'Permissões' => 'fas fa-shield-alt',
            'Testes' => 'fas fa-flask',
            'Versionamento' => 'fas fa-code-branch',
            'Próximos Passos' => 'fas fa-arrow-right'
        ];

        foreach ($icons as $keyword => $icon) {
            if (str_contains($sectionTitle, $keyword)) {
                return $icon;
            }
        }

        return 'fas fa-file-alt';
    }
} 