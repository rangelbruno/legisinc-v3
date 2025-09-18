<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class TechnicalDocController extends Controller
{
    public function index()
    {
        $modules = $this->getSystemModules();

        return view('admin.technical-doc.index', compact('modules'));
    }

    public function module(Request $request, $module)
    {
        $moduleData = $this->getModuleData($module);

        if (!$moduleData) {
            abort(404, 'Módulo não encontrado');
        }

        return view('admin.technical-doc.module', compact('moduleData', 'module'));
    }

    private function getSystemModules()
    {
        return [
            'proposicoes' => [
                'name' => 'Sistema de Proposições',
                'description' => 'Fluxo completo de criação, edição, revisão e assinatura de proposições legislativas',
                'icon' => 'ki-document',
                'color' => 'primary',
                'controllers' => [
                    'ProposicaoController',
                    'ProposicaoAssinaturaController',
                    'ProposicaoLegislativoController',
                    'ProposicaoProtocoloController'
                ],
                'models' => ['Proposicao', 'TipoProposicao'],
                'key_routes' => [
                    'proposicoes.create' => 'Criar proposição',
                    'proposicoes.show' => 'Visualizar proposição',
                    'proposicoes.assinatura' => 'Assinatura digital',
                    'proposicoes.protocolo' => 'Protocolo'
                ]
            ],
            'onlyoffice' => [
                'name' => 'Integração OnlyOffice',
                'description' => 'Sistema de edição online de documentos RTF/DOC com sincronização em tempo real',
                'icon' => 'ki-file-edit',
                'color' => 'success',
                'controllers' => ['OnlyOfficeController'],
                'models' => ['Proposicao'],
                'key_routes' => [
                    'api.onlyoffice.callback' => 'Callback do OnlyOffice',
                    'api.onlyoffice.realtime' => 'Atualizações em tempo real'
                ]
            ],
            'templates' => [
                'name' => 'Sistema de Templates',
                'description' => 'Processamento e aplicação de templates RTF com variáveis dinâmicas',
                'icon' => 'ki-design',
                'color' => 'warning',
                'controllers' => ['TemplateController', 'TemplateUniversalController'],
                'models' => ['TemplateUniversal', 'TipoProposicao'],
                'key_routes' => [
                    'admin.template-universal.index' => 'Gerenciar templates',
                    'admin.tipo-proposicoes.index' => 'Tipos de proposição'
                ]
            ],
            'assinatura' => [
                'name' => 'Assinatura Digital',
                'description' => 'Sistema de assinatura digital com certificados A1/A3 e validação',
                'icon' => 'ki-security',
                'color' => 'danger',
                'controllers' => ['AssinaturaDigitalController', 'ProposicaoAssinaturaController'],
                'models' => ['CertificadoDigital', 'Proposicao'],
                'key_routes' => [
                    'certificado-digital.index' => 'Gerenciar certificados',
                    'proposicoes.processar-assinatura' => 'Processar assinatura'
                ]
            ]
        ];
    }

    private function getModuleData($module)
    {
        $modules = $this->getSystemModules();

        if (!isset($modules[$module])) {
            return null;
        }

        $moduleInfo = $modules[$module];

        // Buscar arquivos do módulo
        $moduleInfo['files'] = $this->getModuleFiles($module);
        $moduleInfo['routes'] = $this->getModuleRoutes($module);
        $moduleInfo['endpoints'] = $this->getModuleEndpoints($module);
        $moduleInfo['flow'] = $this->getModuleFlow($module);

        return $moduleInfo;
    }

    private function getModuleFiles($module)
    {
        $files = [];

        switch ($module) {
            case 'proposicoes':
                $files = [
                    'controllers' => [
                        'app/Http/Controllers/ProposicaoController.php',
                        'app/Http/Controllers/ProposicaoAssinaturaController.php',
                        'app/Http/Controllers/ProposicaoLegislativoController.php',
                        'app/Http/Controllers/ProposicaoProtocoloController.php'
                    ],
                    'models' => [
                        'app/Models/Proposicao.php',
                        'app/Models/TipoProposicao.php'
                    ],
                    'views' => [
                        'resources/views/proposicoes/criar.blade.php',
                        'resources/views/proposicoes/show.blade.php',
                        'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                        'resources/views/proposicoes/protocolo/index.blade.php'
                    ],
                    'services' => [
                        'app/Services/Template/TemplateProcessorService.php',
                        'app/Services/OnlyOffice/OnlyOfficeService.php'
                    ]
                ];
                break;

            case 'onlyoffice':
                $files = [
                    'controllers' => [
                        'app/Http/Controllers/OnlyOfficeController.php',
                        'app/Http/Controllers/Api/OnlyOfficeRealtimeController.php'
                    ],
                    'services' => [
                        'app/Services/OnlyOffice/OnlyOfficeService.php'
                    ],
                    'views' => [
                        'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                        'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php'
                    ]
                ];
                break;

            case 'templates':
                $files = [
                    'controllers' => [
                        'app/Http/Controllers/Admin/TemplateUniversalController.php',
                        'app/Http/Controllers/TemplateController.php'
                    ],
                    'models' => [
                        'app/Models/TemplateUniversal.php',
                        'app/Models/TipoProposicao.php'
                    ],
                    'services' => [
                        'app/Services/Template/TemplateProcessorService.php'
                    ]
                ];
                break;

            case 'assinatura':
                $files = [
                    'controllers' => [
                        'app/Http/Controllers/AssinaturaDigitalController.php',
                        'app/Http/Controllers/ProposicaoAssinaturaController.php'
                    ],
                    'models' => [
                        'app/Models/CertificadoDigital.php'
                    ],
                    'services' => [
                        'app/Services/AssinaturaDigitalService.php'
                    ],
                    'helpers' => [
                        'app/Helpers/CertificadoHelper.php'
                    ]
                ];
                break;
        }

        return $files;
    }

    private function getModuleRoutes($module)
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) use ($module) {
            $name = $route->getName();

            switch ($module) {
                case 'proposicoes':
                    return strpos($name, 'proposicoes.') === 0;
                case 'onlyoffice':
                    return strpos($name, 'onlyoffice') !== false;
                case 'templates':
                    return strpos($name, 'template') !== false || strpos($name, 'tipo-proposicoes') !== false;
                case 'assinatura':
                    return strpos($name, 'assinatura') !== false || strpos($name, 'certificado') !== false;
            }

            return false;
        })->map(function ($route) {
            return [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'action' => $route->getActionName()
            ];
        })->values()->toArray();

        return $routes;
    }

    private function getModuleEndpoints($module)
    {
        $endpoints = [];

        switch ($module) {
            case 'proposicoes':
                $endpoints = [
                    'GET /proposicoes/minhas-proposicoes' => 'Lista proposições do usuário',
                    'POST /proposicoes' => 'Criar nova proposição',
                    'GET /proposicoes/{id}' => 'Visualizar proposição',
                    'PUT /proposicoes/{id}' => 'Atualizar proposição',
                    'POST /proposicoes/{id}/enviar-legislativo' => 'Enviar para revisão legislativa'
                ];
                break;

            case 'onlyoffice':
                $endpoints = [
                    'POST /api/onlyoffice/callback/proposicao/{id}' => 'Callback de salvamento do OnlyOffice',
                    'GET /api/onlyoffice/realtime/check-changes/{id}' => 'Verificar mudanças em tempo real',
                    'POST /api/onlyoffice/force-save/proposicao/{id}' => 'Forçar salvamento'
                ];
                break;

            case 'templates':
                $endpoints = [
                    'GET /admin/template-universal' => 'Listar templates',
                    'POST /admin/template-universal' => 'Criar template',
                    'PUT /admin/template-universal/{id}' => 'Atualizar template'
                ];
                break;

            case 'assinatura':
                $endpoints = [
                    'GET /certificado-digital' => 'Gerenciar certificados',
                    'POST /proposicoes/{id}/processar-assinatura' => 'Processar assinatura digital',
                    'GET /proposicoes/{id}/verificar-assinatura' => 'Verificar status da assinatura'
                ];
                break;
        }

        return $endpoints;
    }

    private function getModuleFlow($module)
    {
        switch ($module) {
            case 'proposicoes':
                return [
                    'steps' => [
                        '1. Criação' => 'Parlamentar acessa formulário de criação',
                        '2. Template' => 'Sistema aplica template baseado no tipo',
                        '3. Edição' => 'Edição no OnlyOffice com salvamento automático',
                        '4. Envio' => 'Envio para revisão do legislativo',
                        '5. Revisão' => 'Legislativo revisa e aprova/rejeita',
                        '6. Assinatura' => 'Assinatura digital pelo autor',
                        '7. Protocolo' => 'Protocolo oficial numerado'
                    ],
                    'diagram_type' => 'flow'
                ];

            case 'onlyoffice':
                return [
                    'steps' => [
                        '1. Abertura' => 'Sistema gera documento key único',
                        '2. Edição' => 'OnlyOffice carrega documento RTF',
                        '3. Callback' => 'Salvamento automático via callback',
                        '4. Polling' => 'Verificação de mudanças a cada 15s',
                        '5. Sincronização' => 'Atualização do banco de dados'
                    ],
                    'diagram_type' => 'sequence'
                ];

            default:
                return null;
        }
    }
}