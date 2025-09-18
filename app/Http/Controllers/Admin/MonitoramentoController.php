<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MonitoramentoController extends Controller
{
    public function index()
    {
        // Verificar se o usuário é admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta página.');
        }

        // Carregar o conteúdo markdown do guia
        $guiaPath = base_path('docs/grafana-prometheus-guia-completo.md');
        $conteudoMarkdown = File::exists($guiaPath) ? File::get($guiaPath) : '';

        return view('admin.monitoramento.index', compact('conteudoMarkdown'));
    }

    public function statusServicos()
    {
        // Verificar se o usuário é admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $servicos = [
            'grafana' => [
                'nome' => 'Grafana',
                'url' => 'http://localhost:3000',
                'porta' => 3000,
                'status' => 'unknown',
                'container' => 'legisinc-grafana-simple'
            ],
            'prometheus' => [
                'nome' => 'Prometheus',
                'url' => 'http://localhost:9090',
                'porta' => 9090,
                'status' => 'unknown',
                'container' => 'legisinc-prometheus-simple'
            ],
            'postgres-exporter' => [
                'nome' => 'PostgreSQL Exporter',
                'url' => 'http://localhost:9187',
                'porta' => 9187,
                'status' => 'unknown',
                'container' => 'legisinc-postgres-exporter'
            ]
        ];

        // Verificar status dos containers
        foreach ($servicos as $key => &$servico) {
            try {
                $output = shell_exec("docker ps --filter name={$servico['container']} --format '{{.Status}}'");
                if ($output && strpos($output, 'Up') !== false) {
                    $servico['status'] = 'running';
                    $servico['uptime'] = trim($output);
                } else {
                    $servico['status'] = 'stopped';
                }
            } catch (Exception $e) {
                $servico['status'] = 'error';
                $servico['error'] = $e->getMessage();
            }
        }

        return response()->json($servicos);
    }

    public function metricas()
    {
        // Verificar se o usuário é admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $metricas = [
            'traefik' => [],
            'postgresql' => [],
            'legisinc' => []
        ];

        try {
            // Verificar se Prometheus está acessível
            $prometheusUrl = 'http://localhost:9090/api/v1/query?query=up';
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'GET'
                ]
            ]);

            $response = @file_get_contents($prometheusUrl, false, $context);

            if ($response) {
                $data = json_decode($response, true);
                if ($data && $data['status'] === 'success') {
                    $metricas['prometheus_conectado'] = true;
                    $metricas['targets_up'] = count($data['data']['result']);
                } else {
                    $metricas['prometheus_conectado'] = false;
                }
            } else {
                $metricas['prometheus_conectado'] = false;
            }
        } catch (Exception $e) {
            $metricas['prometheus_conectado'] = false;
            $metricas['error'] = $e->getMessage();
        }

        return response()->json($metricas);
    }
}