<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TemplateRelatorioController extends Controller
{
    /**
     * Mostrar interface de edição do template PDF
     */
    public function editarTemplatePdf()
    {
        $templatePath = resource_path('views/proposicoes/legislativo/relatorio-pdf.blade.php');
        $conteudoTemplate = File::exists($templatePath) ? File::get($templatePath) : $this->getTemplateDefault();

        return view('admin.templates.relatorio-pdf', compact('conteudoTemplate'));
    }

    /**
     * Salvar alterações no template PDF
     */
    public function salvarTemplatePdf(Request $request)
    {
        $request->validate([
            'template_content' => 'required|string'
        ]);

        try {
            $templatePath = resource_path('views/proposicoes/legislativo/relatorio-pdf.blade.php');
            
            // Fazer backup do template atual
            $backupPath = resource_path('views/proposicoes/legislativo/relatorio-pdf-backup-' . date('Y-m-d-H-i-s') . '.blade.php');
            if (File::exists($templatePath)) {
                File::copy($templatePath, $backupPath);
            }

            // Salvar novo template
            File::put($templatePath, $request->template_content);

            // Limpar cache de views
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Template salvo com sucesso!',
                'backup_created' => basename($backupPath)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview do template com dados de exemplo
     */
    public function previewTemplate(Request $request)
    {
        $templateContent = $request->input('template_content');
        
        try {
            // Criar arquivo temporário
            $tempPath = resource_path('views/proposicoes/legislativo/relatorio-pdf-temp.blade.php');
            File::put($tempPath, $templateContent);

            // Dados de exemplo para preview
            $dados = $this->getDadosExemplo();

            // Renderizar template
            $html = view('proposicoes.legislativo.relatorio-pdf-temp', compact('dados'))->render();

            // Remover arquivo temporário
            File::delete($tempPath);

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            // Limpar arquivo temporário em caso de erro
            $tempPath = resource_path('views/proposicoes/legislativo/relatorio-pdf-temp.blade.php');
            if (File::exists($tempPath)) {
                File::delete($tempPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro no preview: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Listar backups disponíveis
     */
    public function listarBackups()
    {
        $viewsPath = resource_path('views/proposicoes/legislativo/');
        $backups = collect(File::glob($viewsPath . 'relatorio-pdf-backup-*.blade.php'))
            ->map(function ($file) {
                return [
                    'filename' => basename($file),
                    'path' => $file,
                    'created_at' => date('d/m/Y H:i:s', filemtime($file)),
                    'size' => $this->formatBytes(filesize($file))
                ];
            })
            ->sortByDesc('created_at')
            ->values();

        return response()->json($backups);
    }

    /**
     * Restaurar backup
     */
    public function restaurarBackup(Request $request)
    {
        $backupFile = $request->input('backup_file');
        $backupPath = resource_path('views/proposicoes/legislativo/' . $backupFile);

        if (!File::exists($backupPath) || !str_contains($backupFile, 'relatorio-pdf-backup-')) {
            return response()->json([
                'success' => false,
                'message' => 'Backup não encontrado ou inválido'
            ], 404);
        }

        try {
            $templatePath = resource_path('views/proposicoes/legislativo/relatorio-pdf.blade.php');
            File::copy($backupPath, $templatePath);
            
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Backup restaurado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resetar para template padrão
     */
    public function resetarTemplate()
    {
        try {
            $templatePath = resource_path('views/proposicoes/legislativo/relatorio-pdf.blade.php');
            
            // Fazer backup antes de resetar
            $backupPath = resource_path('views/proposicoes/legislativo/relatorio-pdf-backup-before-reset-' . date('Y-m-d-H-i-s') . '.blade.php');
            if (File::exists($templatePath)) {
                File::copy($templatePath, $backupPath);
            }

            // Restaurar template padrão
            File::put($templatePath, $this->getTemplateDefault());
            
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Template resetado para o padrão!',
                'backup_created' => basename($backupPath)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter dados de exemplo para preview
     */
    private function getDadosExemplo()
    {
        return [
            'periodo' => 'mes_atual',
            'data_inicio' => '01/07/2025',
            'data_fim' => '31/07/2025',
            'dados_por_usuario' => [
                '1' => [
                    'nome' => 'João Silva',
                    'aprovadas' => 15,
                    'devolvidas' => 3,
                    'retornadas' => 2,
                    'total' => 20
                ],
                '2' => [
                    'nome' => 'Maria Santos',
                    'aprovadas' => 12,
                    'devolvidas' => 1,
                    'retornadas' => 1,
                    'total' => 14
                ]
            ],
            'total_geral' => 34,
            'proposicoes' => collect([
                (object) [
                    'id' => 1,
                    'tipo' => 'projeto_lei',
                    'titulo' => 'Projeto de Lei Exemplo',
                    'status' => 'aprovado_assinatura',
                    'updated_at' => now(),
                    'autor' => (object) ['name' => 'João Silva']
                ],
                (object) [
                    'id' => 2,
                    'tipo' => 'mocao',
                    'titulo' => 'Moção de Exemplo',
                    'status' => 'devolvido_correcao',
                    'updated_at' => now()->subDays(1),
                    'autor' => (object) ['name' => 'Maria Santos']
                ]
            ])
        ];
    }

    /**
     * Obter template padrão
     */
    private function getTemplateDefault()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Produtividade Legislativa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Relatório de Produtividade Legislativa</h1>
        <div class="subtitle">Sistema de Proposições Legislativas</div>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <strong>Período:</strong> {{ $dados[\'data_inicio\'] }} até {{ $dados[\'data_fim\'] }}<br>
        <strong>Data de Geração:</strong> {{ now()->format(\'d/m/Y H:i:s\') }}<br>
        <strong>Total de Proposições:</strong> {{ $dados[\'total_geral\'] }}
    </div>

    <!-- User Performance Table -->
    @if(count($dados[\'dados_por_usuario\']) > 0)
    <h3>Produtividade por Usuário</h3>
    <table>
        <thead>
            <tr>
                <th>Usuário</th>
                <th class="text-center">Aprovadas</th>
                <th class="text-center">Devolvidas</th>
                <th class="text-center">Retornadas</th>
                <th class="text-center">Total</th>
                <th class="text-center">Taxa de Aprovação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados[\'dados_por_usuario\'] as $usuarioId => $dadosUsuario)
            <tr>
                <td>{{ $dadosUsuario[\'nome\'] }}</td>
                <td class="text-center">{{ $dadosUsuario[\'aprovadas\'] }}</td>
                <td class="text-center">{{ $dadosUsuario[\'devolvidas\'] }}</td>
                <td class="text-center">{{ $dadosUsuario[\'retornadas\'] }}</td>
                <td class="text-center">{{ $dadosUsuario[\'total\'] }}</td>
                <td class="text-center">
                    @php
                        $taxaAprovacao = $dadosUsuario[\'total\'] > 0 ? round(($dadosUsuario[\'aprovadas\'] / $dadosUsuario[\'total\']) * 100, 1) : 0;
                    @endphp
                    {{ $taxaAprovacao }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema de Proposições Legislativas</p>
        <p>{{ now()->format(\'d/m/Y H:i:s\') }}</p>
    </div>
</body>
</html>';
    }

    /**
     * Formatar tamanho de arquivo
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}