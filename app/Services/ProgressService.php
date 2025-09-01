<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProgressService
{
    protected $progressFilePath;

    public function __construct()
    {
        $this->progressFilePath = base_path('docs/progress.md');
    }

    public function getProgressData()
    {
        $content = $this->getProgressFileContent();
        
        return [
            'title' => 'Sistema de Tramitação Parlamentar 2.0',
            'subtitle' => 'Cronograma TR - Câmara de Franco da Rocha',
            'overview' => $this->extractOverviewData($content),
            'modules' => $this->extractModulesData($content),
            'statistics' => $this->extractStatisticsData($content),
            'technical' => $this->extractTechnicalData($content),
            'schedule' => $this->extractScheduleData($content),
            'lastUpdate' => $this->extractLastUpdate($content)
        ];
    }

    protected function getProgressFileContent()
    {
        if (!File::exists($this->progressFilePath)) {
            throw new \Exception('Progress file not found');
        }

        return File::get($this->progressFilePath);
    }

    protected function extractOverviewData($content)
    {
        $overview = [];
        
        // Extrair módulos implementados vs total
        if (preg_match('/- \*\*Módulos Implementados:\*\* \*\*(\d+)\/(\d+)\*\* → \*\*(\d+)%\*\*/', $content, $matches)) {
            $overview['implemented_modules'] = (int) $matches[1];
            $overview['total_modules'] = (int) $matches[2];
            $overview['percentage'] = (int) $matches[3];
        }
        
        // Cobertura do TR
        if (preg_match('/- \*\*Cobertura do TR \(módulos obrigatórios\):\*\* \*\*(\d+)\/(\d+)\*\* → \*\*(\d+)%\*\*/', $content, $matches)) {
            $overview['tr_coverage'] = [
                'implemented' => (int) $matches[1],
                'required' => (int) $matches[2],
                'percentage' => (int) $matches[3]
            ];
        }

        // Estrutura base (sempre 100%)
        if (preg_match('/- \*\*Base do Sistema:\*\* mantida como \*\*(\d+)%\*\* concluída/', $content, $matches)) {
            $overview['base_structure'] = [
                'percentage' => (int) $matches[1],
                'status' => 'Concluída'
            ];
        }

        return $overview;
    }

    protected function extractModulesData($content)
    {
        $modules = [];
        
        // Módulos Core Implementados (seção "Módulos Core Implementados")
        preg_match_all('/(\d+)\. \*\*([^*]+)\*\*/', $content, $implementedMatches, PREG_SET_ORDER);
        
        foreach ($implementedMatches as $match) {
            $moduleNumber = (int) $match[1];
            $moduleName = trim($match[2]);
            
            $modules[] = [
                'number' => $moduleNumber,
                'name' => $moduleName,
                'status' => 'completed',
                'category' => 'implemented'
            ];
        }

        // Módulos a Criar (seção "Delta vs. TR")
        preg_match_all('/(\d+)\. \*\*([^*]+)\*\* — ([^\n]+)/', $content, $newModulesMatches, PREG_SET_ORDER);
        
        foreach ($newModulesMatches as $match) {
            $moduleNumber = (int) $match[1] + 9; // Ajustar numeração
            $moduleName = trim($match[2]);
            $description = trim($match[3]);
            
            $modules[] = [
                'number' => $moduleNumber,
                'name' => $moduleName,
                'description' => $description,
                'status' => 'planned',
                'category' => 'new_modules'
            ];
        }

        // Módulos a Finalizar/Adequar
        $finalizeModules = [
            ['name' => 'Proposições - Geração de pauta/ata', 'status' => 'in_development'],
            ['name' => 'Usuários/Institucional - Mesa por legislatura', 'status' => 'in_development'],
            ['name' => 'Templates - Variáveis extras', 'status' => 'in_development'],
            ['name' => 'Parâmetros - Cadastros de tipos', 'status' => 'in_development'],
            ['name' => 'Permissões - Logs detalhados', 'status' => 'in_development'],
            ['name' => 'Relatórios - Presença, votação', 'status' => 'in_development'],
            ['name' => 'Documentos/PDF - PDF/A', 'status' => 'in_development']
        ];
        
        foreach ($finalizeModules as $index => $module) {
            $modules[] = [
                'number' => 18 + $index,
                'name' => $module['name'],
                'status' => $module['status'],
                'category' => 'adjustments'
            ];
        }

        return $modules;
    }

    protected function extractStatisticsData($content)
    {
        $statistics = [];
        
        // Calcular estatísticas baseadas nos dados extraídos
        $statistics['by_status'] = [
            'implemented' => ['count' => 9, 'percentage' => 36],
            'in_development' => ['count' => 7, 'percentage' => 28],
            'planned' => ['count' => 8, 'percentage' => 32],
            'adjustments' => ['count' => 1, 'percentage' => 4]
        ];

        // Categorias baseadas no TR
        $statistics['by_category'] = [
            'implemented' => ['count' => 9, 'total' => 25, 'percentage' => 36],
            'tr_required' => ['count' => 9, 'total' => 17, 'percentage' => 53],
            'new_modules' => ['count' => 0, 'total' => 8, 'percentage' => 0]
        ];
        
        // TR Coverage específico
        $statistics['tr_coverage'] = [
            'completed' => 9,
            'remaining' => 8,
            'total' => 17,
            'percentage' => 53
        ];

        return $statistics;
    }

    protected function extractTechnicalData($content)
    {
        $technical = [];
        
        // Arquitetura técnica baseada na estrutura base (100% concluída)
        $technical['coverage'] = [
            'backend' => 100,  // Laravel 12 + PHP 8.2
            'frontend' => 85,  // Metronic + Blade + Vue.js
            'apis' => 90,      // 31 endpoints + API real
            'database' => 95,  // PostgreSQL + migrations
            'auth' => 100,     // Spatie Permission completo
            'docker' => 100    // Docker + Makefile
        ];
        
        // Stack técnica
        $technical['stack'] = [
            'php' => '8.2',
            'laravel' => '12',
            'database' => 'PostgreSQL',
            'frontend' => 'Metronic + Blade + Vue.js',
            'authentication' => 'JWT/Sanctum + Spatie Permission',
            'containerization' => 'Docker + Makefile'
        ];

        return $technical;
    }

    protected function extractLastUpdate($content)
    {
        // Buscar por data no final do documento ou usar data atual como fallback
        if (preg_match('/\*\*Última atualização\*\*: ([0-9\/]+)/', $content, $matches)) {
            return $matches[1];
        }
        
        // Data do documento baseada no conteúdo
        if (preg_match('/\*\*Versão estável\*\*: ([^\n]+)/', $content, $matches)) {
            return '23/08/2025 - ' . $matches[1];
        }

        return '01/09/2025';
    }

    protected function extractScheduleData($content)
    {
        // Dados estruturados para o gráfico de Gantt
        $ganttTasks = [
            [
                'id' => 'administrativo',
                'name' => 'Módulo Administrativo',
                'start' => '2025-09-02',
                'end' => '2025-09-06',
                'progress' => 0,
                'dependencies' => '',
                'description' => 'Documentos administrativos, peticionamento eletrônico, arquivo digital'
            ],
            [
                'id' => 'protocolo',
                'name' => 'Protocolo Geral',
                'start' => '2025-09-02',
                'end' => '2025-09-06',
                'progress' => 0,
                'dependencies' => '',
                'description' => 'Sistema de numeração, etiquetas automáticas, QR codes'
            ],
            [
                'id' => 'processo_eletronico',
                'name' => 'Processo Eletrônico',
                'start' => '2025-09-09',
                'end' => '2025-09-13',
                'progress' => 0,
                'dependencies' => 'protocolo',
                'description' => 'Assinatura ICP-Brasil/PAdES, conformidade PDF/A'
            ],
            [
                'id' => 'normas_juridicas',
                'name' => 'Normas Jurídicas',
                'start' => '2025-09-09',
                'end' => '2025-09-13',
                'progress' => 0,
                'dependencies' => 'administrativo',
                'description' => 'Integração LexML, catalogação legislativa'
            ],
            [
                'id' => 'plenario_presenca',
                'name' => 'Automação do Plenário',
                'start' => '2025-09-16',
                'end' => '2025-09-20',
                'progress' => 0,
                'dependencies' => 'processo_eletronico',
                'description' => 'Controle de presença, oradores, votações eletrônicas'
            ],
            [
                'id' => 'app_plenario',
                'name' => 'App de Plenário',
                'start' => '2025-09-16',
                'end' => '2025-09-20',
                'progress' => 0,
                'dependencies' => 'normas_juridicas',
                'description' => 'Aplicativo móvel para parlamentares, disseminação de leis'
            ],
            [
                'id' => 'tce_comunicacao',
                'name' => 'Comunicação TCE',
                'start' => '2025-09-23',
                'end' => '2025-09-27',
                'progress' => 0,
                'dependencies' => 'plenario_presenca',
                'description' => 'Validações automáticas, controle de prazos, portal TCE'
            ],
            [
                'id' => 'portal_transparencia',
                'name' => 'Portal Transparência',
                'start' => '2025-09-23',
                'end' => '2025-09-27',
                'progress' => 0,
                'dependencies' => 'app_plenario',
                'description' => 'Portal público, contato com vereadores, dados abertos'
            ],
            [
                'id' => 'testes_integracao',
                'name' => 'Testes & Performance',
                'start' => '2025-09-30',
                'end' => '2025-10-04',
                'progress' => 0,
                'dependencies' => 'tce_comunicacao,portal_transparencia',
                'description' => 'Testes integrados, otimização de performance'
            ],
            [
                'id' => 'homologacao',
                'name' => 'Homologação',
                'start' => '2025-10-02',
                'end' => '2025-10-07',
                'progress' => 0,
                'dependencies' => 'testes_integracao',
                'description' => 'Homologação guiada, treinamento usuários, deploy produção'
            ]
        ];

        // Timeline semanal para compatibilidade
        $weeks = [
            [
                'week' => 'Semana 1 (09/02–09/06)',
                'tasks' => [
                    'Administrativo (doc adm, peticionamento, arquivo)',
                    'Protocolo Geral (numeração, etiquetas, QR)'
                ]
            ],
            [
                'week' => 'Semana 2 (09/09–09/13)',
                'tasks' => [
                    'Processo Eletrônico (ICP/PAdES, PDF/A)',
                    'Normas Jurídicas (+LexML)'
                ]
            ],
            [
                'week' => 'Semana 3 (09/16–09/20)',
                'tasks' => [
                    'Automação do Plenário (presença, oradores, votações)',
                    'Disseminação de Leis & App de Plenário'
                ]
            ],
            [
                'week' => 'Semana 4 (09/23–09/27)',
                'tasks' => [
                    'Comunicação com TCE (validações, prazos, portal)',
                    'Portal Transparência & Contato com Vereadores'
                ]
            ],
            [
                'week' => 'Semana 5 (09/30–10/07)',
                'tasks' => [
                    'Testes Integrados & Performance',
                    'Homologação guiada com usuários-chave',
                    'Deploy Produção + Entrega Formal'
                ]
            ]
        ];
        
        return [
            'weeks' => $weeks,
            'gantt_tasks' => $ganttTasks,
            'milestones' => [
                ['date' => '2025-09-06', 'title' => 'Protótipos Base'],
                ['date' => '2025-09-20', 'title' => 'Módulos Principais'],
                ['date' => '2025-10-07', 'title' => 'Entrega Final']
            ]
        ];
    }
    
    protected function determineModuleCategory($moduleNumber)
    {
        if ($moduleNumber <= 9) {
            return 'implemented';
        } elseif ($moduleNumber <= 17) {
            return 'new_modules';
        } else {
            return 'adjustments';
        }
    }
} 