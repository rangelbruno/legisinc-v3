<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArquiteturaController extends Controller
{
    /**
     * Exibe a documentação da arquitetura gateway
     */
    public function index()
    {
        // Verificar se o usuário é admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta página.');
        }
        // Carregar o conteúdo markdown
        $documentoPath = base_path('docs/arquitetura-gateway-visual.md');
        $conteudoMarkdown = File::exists($documentoPath) ? File::get($documentoPath) : '';

        // Carregar a explicação completa
        $explicacaoPath = base_path('docs/arquitetura-gateway-explicacao-completa.md');
        $explicacaoCompleta = File::exists($explicacaoPath) ? File::get($explicacaoPath) : '';

        // Carregar a explicação simples dos containers
        $containersExplicacaoPath = base_path('docs/containers-explicacao-simples.md');
        $containersExplicacao = File::exists($containersExplicacaoPath) ? File::get($containersExplicacaoPath) : '';

        // Carregar a explicação dos containers novos
        $containersNovosPath = base_path('docs/containers-novos-explicacao.md');
        $containersNovos = File::exists($containersNovosPath) ? File::get($containersNovosPath) : '';

        // Informações dos containers
        $containers = $this->getContainerInfo();

        // Status dos serviços
        $servicos = $this->getServiceStatus();

        return view('admin.arquitetura.index', compact('conteudoMarkdown', 'explicacaoCompleta', 'containersExplicacao', 'containersNovos', 'containers', 'servicos'));
    }

    /**
     * API endpoint para verificar status dos containers
     */
    public function statusContainers()
    {
        $containers = $this->getContainerInfo();
        return response()->json($containers);
    }

    /**
     * API endpoint para verificar status dos serviços
     */
    public function statusServicos()
    {
        $servicos = $this->getServiceStatus();
        return response()->json($servicos);
    }

    /**
     * Obter informações dos containers Docker
     */
    private function getContainerInfo()
    {
        try {
            // Verificar se containers estão rodando
            $containers = [
                [
                    'nome' => 'Traefik Gateway',
                    'container' => 'legisinc-gateway-simple',
                    'porta' => '8000',
                    'status' => 'running',
                    'descricao' => 'Gateway principal com load balancing'
                ],
                [
                    'nome' => 'Laravel App',
                    'container' => 'legisinc-app',
                    'porta' => '8001',
                    'status' => 'running',
                    'descricao' => 'Aplicação Laravel (backend atual)'
                ],
                [
                    'nome' => 'Nova API',
                    'container' => 'legisinc-nova-api',
                    'porta' => '3001',
                    'status' => 'running',
                    'descricao' => 'Nova API Node.js (canary deployment)'
                ],
                [
                    'nome' => 'Canary Monitor',
                    'container' => 'legisinc-canary-monitor',
                    'porta' => '3003',
                    'status' => 'running',
                    'descricao' => 'Monitor de canary deployment'
                ],
                [
                    'nome' => 'Prometheus',
                    'container' => 'legisinc-prometheus-simple',
                    'porta' => '9090',
                    'status' => 'running',
                    'descricao' => 'Coleta de métricas'
                ],
                [
                    'nome' => 'Grafana',
                    'container' => 'legisinc-grafana-simple',
                    'porta' => '3000',
                    'status' => 'running',
                    'descricao' => 'Dashboard de métricas'
                ],
                [
                    'nome' => 'Swagger UI',
                    'container' => 'legisinc-swagger-ui',
                    'porta' => '8082',
                    'status' => 'running',
                    'descricao' => 'Documentação interativa da API'
                ]
            ];

            return $containers;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obter status dos serviços
     */
    private function getServiceStatus()
    {
        try {
            $servicos = [
                [
                    'nome' => 'Gateway Principal',
                    'url' => 'http://localhost:8000',
                    'status' => $this->checkUrl('http://localhost:8000'),
                    'tipo' => 'gateway'
                ],
                [
                    'nome' => 'Canary Monitor',
                    'url' => 'http://localhost:3003/status',
                    'status' => $this->checkUrl('http://localhost:3003/status'),
                    'tipo' => 'monitor'
                ],
                [
                    'nome' => 'Traefik Dashboard',
                    'url' => 'http://localhost:8090',
                    'status' => $this->checkUrl('http://localhost:8090'),
                    'tipo' => 'dashboard'
                ],
                [
                    'nome' => 'Grafana',
                    'url' => 'http://localhost:3000',
                    'status' => $this->checkUrl('http://localhost:3000'),
                    'tipo' => 'dashboard'
                ],
                [
                    'nome' => 'Swagger UI',
                    'url' => 'http://localhost:8082',
                    'status' => $this->checkUrl('http://localhost:8082'),
                    'tipo' => 'documentation'
                ]
            ];

            return $servicos;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Verificar se uma URL está acessível
     */
    private function checkUrl($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode >= 200 && $httpCode < 400 ? 'online' : 'offline';
        } catch (\Exception $e) {
            return 'offline';
        }
    }
}