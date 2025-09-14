<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemDiagramController extends Controller
{
    /**
     * Display the system architecture diagrams
     */
    public function index()
    {
        // Verificar permissão de admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }

        $diagrams = [];

        // 1. Ler diagramas do project-overview.md
        $markdownPath = base_path('docs/project-overview.md');
        if (File::exists($markdownPath)) {
            $content = File::get($markdownPath);
            $diagrams = array_merge($diagrams, $this->extractDiagrams($content, 'project-overview'));
        }

        // 2. Ler fluxo de proposições
        $fluxoProposicoesPath = base_path('docs/FLUXO-PROPOSICOES-MERMAID.md');
        if (File::exists($fluxoProposicoesPath)) {
            $content = File::get($fluxoProposicoesPath);
            $proposicoesDiagrams = $this->extractDiagrams($content, 'fluxo-proposicoes');

            // Adicionar prefixo ao título para diferenciar
            foreach ($proposicoesDiagrams as &$diagram) {
                $diagram['title'] = '📋 ' . $diagram['title'];
                $diagram['category'] = 'Fluxo de Proposições';
            }
            $diagrams = array_merge($diagrams, $proposicoesDiagrams);
        }

        // 3. Ler fluxo de documentos
        $fluxoDocumentosPath = base_path('docs/FLUXO-DOCUMENTO-COMPLETO.md');
        if (File::exists($fluxoDocumentosPath)) {
            $content = File::get($fluxoDocumentosPath);
            $documentosDiagrams = $this->extractDiagrams($content, 'fluxo-documentos');

            // Adicionar prefixo ao título para diferenciar
            foreach ($documentosDiagrams as &$diagram) {
                $diagram['title'] = '📑 ' . $diagram['title'];
                $diagram['category'] = 'Fluxo de Documentos';
            }
            $diagrams = array_merge($diagrams, $documentosDiagrams);
        }

        // 4. Ler fluxo de assinatura digital
        $fluxoAssinaturaPath = base_path('docs/FLUXO-ASSINATURA-DIGITAL-PYHANKO.md');
        if (File::exists($fluxoAssinaturaPath)) {
            $content = File::get($fluxoAssinaturaPath);
            $assinaturaDiagrams = $this->extractDiagrams($content, 'fluxo-assinatura');

            // Adicionar prefixo ao título para diferenciar
            foreach ($assinaturaDiagrams as &$diagram) {
                $diagram['title'] = '🔏 ' . $diagram['title'];
                $diagram['category'] = 'Assinatura Digital';
            }
            $diagrams = array_merge($diagrams, $assinaturaDiagrams);
        }

        // 5. Ler fluxo completo de proposição
        $fluxoCompletoPath = base_path('docs/FLUXO-COMPLETO-PROPOSICAO.md');
        if (File::exists($fluxoCompletoPath)) {
            $content = File::get($fluxoCompletoPath);
            $completoDiagrams = $this->extractDiagrams($content, 'fluxo-completo');

            // Adicionar prefixo ao título para diferenciar
            foreach ($completoDiagrams as &$diagram) {
                $diagram['title'] = '🔄 ' . $diagram['title'];
                $diagram['category'] = 'Fluxo Completo';
            }
            $diagrams = array_merge($diagrams, $completoDiagrams);
        }

        // Organizar diagramas por categoria
        $diagramsByCategory = [];
        foreach ($diagrams as $diagram) {
            $category = $diagram['category'] ?? 'Arquitetura Geral';
            if (!isset($diagramsByCategory[$category])) {
                $diagramsByCategory[$category] = [];
            }
            $diagramsByCategory[$category][] = $diagram;
        }

        // Estatísticas do sistema
        $stats = [
            'total_controllers' => $this->countFiles('app/Http/Controllers', '*.php'),
            'total_views' => $this->countFiles('resources/views', '*.blade.php'),
            'total_routes' => $this->countRoutes(),
            'total_services' => $this->countFiles('app/Services', '*.php'),
            'total_diagrams' => count($diagrams),
            'total_categories' => count($diagramsByCategory),
        ];

        return view('admin.system-diagram.index', compact('diagrams', 'diagramsByCategory', 'stats'));
    }

    /**
     * Extract Mermaid diagrams from markdown content
     */
    private function extractDiagrams($content, $source = '')
    {
        $diagrams = [];

        // Extrair blocos Mermaid do markdown
        preg_match_all('/```mermaid\n(.*?)\n```/s', $content, $matches);

        if (!empty($matches[1])) {
            // Processar cada diagrama
            foreach ($matches[1] as $index => $diagramContent) {
                // Tentar extrair título do diagrama (linha anterior ao bloco mermaid)
                $titlePattern = '/###?\s*(.*?)\n+```mermaid/s';
                preg_match_all($titlePattern, $content, $titleMatches);

                $title = isset($titleMatches[1][$index])
                    ? trim($titleMatches[1][$index])
                    : 'Diagrama ' . ($index + 1);

                // Limpar título de caracteres especiais
                $title = preg_replace('/^[#\-\*]+\s*/', '', $title);

                $diagrams[] = [
                    'id' => $source . '-diagram-' . ($index + 1),
                    'title' => $title,
                    'content' => trim($diagramContent),
                    'source' => $source,
                    'category' => null // Será definido depois
                ];
            }
        }

        return $diagrams;
    }

    /**
     * Count files in a directory
     */
    private function countFiles($directory, $pattern)
    {
        $path = base_path($directory);
        if (!File::exists($path)) {
            return 0;
        }

        $files = File::allFiles($path);
        $count = 0;

        foreach ($files as $file) {
            if (fnmatch($pattern, $file->getFilename())) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count total routes in the application
     */
    private function countRoutes()
    {
        $routes = app('router')->getRoutes();
        return count($routes);
    }

    /**
     * Get detailed list of controllers
     */
    public function getControllers()
    {
        $path = base_path('app/Http/Controllers');
        $controllers = [];

        if (File::exists($path)) {
            $files = File::allFiles($path);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $relativePath = str_replace(base_path() . '/', '', $file->getPathname());
                    $className = str_replace(['app/Http/Controllers/', '.php'], '', $relativePath);
                    $className = str_replace('/', '\\', $className);

                    $controllers[] = [
                        'name' => $className,
                        'file' => $relativePath,
                        'path' => $file->getPathname()
                    ];
                }
            }
        }

        // Ordenar por nome
        usort($controllers, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json($controllers);
    }

    /**
     * Get detailed list of views
     */
    public function getViews()
    {
        $path = base_path('resources/views');
        $views = [];

        if (File::exists($path)) {
            $files = File::allFiles($path);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $relativePath = str_replace(base_path() . '/resources/views/', '', $file->getPathname());
                    $viewName = str_replace('.blade.php', '', $relativePath);
                    $viewName = str_replace('/', '.', $viewName);

                    $views[] = [
                        'name' => $viewName,
                        'file' => 'resources/views/' . $relativePath,
                        'path' => $file->getPathname()
                    ];
                }
            }
        }

        // Ordenar por nome
        usort($views, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json($views);
    }

    /**
     * Get detailed list of routes
     */
    public function getRoutes()
    {
        $routes = app('router')->getRoutes();
        $routeList = [];

        foreach ($routes as $route) {
            $routeList[] = [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'methods' => implode('|', $route->methods()),
                'action' => $route->getActionName()
            ];
        }

        // Ordenar por URI
        usort($routeList, function($a, $b) {
            return strcmp($a['uri'], $b['uri']);
        });

        return response()->json($routeList);
    }

    /**
     * Get detailed list of services
     */
    public function getServices()
    {
        $path = base_path('app/Services');
        $services = [];

        if (File::exists($path)) {
            $files = File::allFiles($path);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $relativePath = str_replace(base_path() . '/', '', $file->getPathname());
                    $className = str_replace(['app/Services/', '.php'], '', $relativePath);
                    $className = str_replace('/', '\\', $className);

                    $services[] = [
                        'name' => $className,
                        'file' => $relativePath,
                        'path' => $file->getPathname()
                    ];
                }
            }
        }

        // Ordenar por nome
        usort($services, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json($services);
    }

    /**
     * Get detailed list of diagrams
     */
    public function getDiagrams()
    {
        $diagrams = [];

        // Ler diagramas de todos os arquivos de documentação
        $files = [
            'docs/project-overview.md' => 'Arquitetura Geral',
            'docs/FLUXO-PROPOSICOES-MERMAID.md' => 'Fluxo de Proposições',
            'docs/FLUXO-DOCUMENTO-COMPLETO.md' => 'Fluxo de Documentos',
            'docs/FLUXO-ASSINATURA-DIGITAL-PYHANKO.md' => 'Assinatura Digital',
            'docs/FLUXO-COMPLETO-PROPOSICAO.md' => 'Fluxo Completo'
        ];

        foreach ($files as $file => $category) {
            $fullPath = base_path($file);
            if (File::exists($fullPath)) {
                $content = File::get($fullPath);
                $fileDiagrams = $this->extractDiagrams($content, basename($file, '.md'));

                foreach ($fileDiagrams as $diagram) {
                    $diagrams[] = [
                        'title' => $diagram['title'],
                        'category' => $category,
                        'source' => $file
                    ];
                }
            }
        }

        return response()->json($diagrams);
    }

    /**
     * Get detailed list of categories
     */
    public function getCategories()
    {
        $categories = [
            [
                'name' => 'Arquitetura Geral',
                'description' => 'Diagramas da arquitetura geral do sistema',
                'source' => 'docs/project-overview.md'
            ],
            [
                'name' => 'Fluxo de Proposições',
                'description' => 'Fluxos relacionados ao processamento de proposições',
                'source' => 'docs/FLUXO-PROPOSICOES-MERMAID.md'
            ],
            [
                'name' => 'Fluxo de Documentos',
                'description' => 'Fluxos de processamento de documentos',
                'source' => 'docs/FLUXO-DOCUMENTO-COMPLETO.md'
            ],
            [
                'name' => 'Assinatura Digital',
                'description' => 'Fluxos de assinatura digital com PyHanko',
                'source' => 'docs/FLUXO-ASSINATURA-DIGITAL-PYHANKO.md'
            ]
        ];

        return response()->json($categories);
    }

    /**
     * Export diagram as image
     */
    public function export(Request $request)
    {
        // Implementação futura para exportar diagramas como imagem
        return response()->json([
            'message' => 'Funcionalidade de exportação em desenvolvimento'
        ]);
    }
}