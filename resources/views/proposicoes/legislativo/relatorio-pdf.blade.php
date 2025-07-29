<!DOCTYPE html>
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
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .stats-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        .stats-item .number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            display: block;
        }
        
        .stats-item .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
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
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .page-break {
            page-break-after: always;
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
        <strong>Período:</strong> {{ $dados['data_inicio'] }} até {{ $dados['data_fim'] }}<br>
        <strong>Data de Geração:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
        <strong>Total de Proposições:</strong> {{ $dados['total_geral'] }}
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stats-item">
            <span class="number">{{ array_sum(array_column($dados['dados_por_usuario'], 'aprovadas')) }}</span>
            <span class="label">Aprovadas</span>
        </div>
        <div class="stats-item">
            <span class="number">{{ array_sum(array_column($dados['dados_por_usuario'], 'devolvidas')) }}</span>
            <span class="label">Devolvidas</span>
        </div>
        <div class="stats-item">
            <span class="number">{{ array_sum(array_column($dados['dados_por_usuario'], 'retornadas')) }}</span>
            <span class="label">Retornadas</span>
        </div>
        <div class="stats-item">
            <span class="number">{{ $dados['total_geral'] }}</span>
            <span class="label">Total</span>
        </div>
    </div>

    <!-- User Performance Table -->
    @if(count($dados['dados_por_usuario']) > 0)
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
            @foreach($dados['dados_por_usuario'] as $usuarioId => $dadosUsuario)
            <tr>
                <td>{{ $dadosUsuario['nome'] }}</td>
                <td class="text-center">{{ $dadosUsuario['aprovadas'] }}</td>
                <td class="text-center">{{ $dadosUsuario['devolvidas'] }}</td>
                <td class="text-center">{{ $dadosUsuario['retornadas'] }}</td>
                <td class="text-center">{{ $dadosUsuario['total'] }}</td>
                <td class="text-center">
                    @php
                        $taxaAprovacao = $dadosUsuario['total'] > 0 ? round(($dadosUsuario['aprovadas'] / $dadosUsuario['total']) * 100, 1) : 0;
                    @endphp
                    {{ $taxaAprovacao }}%
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td><strong>TOTAL GERAL</strong></td>
                <td class="text-center">{{ array_sum(array_column($dados['dados_por_usuario'], 'aprovadas')) }}</td>
                <td class="text-center">{{ array_sum(array_column($dados['dados_por_usuario'], 'devolvidas')) }}</td>
                <td class="text-center">{{ array_sum(array_column($dados['dados_por_usuario'], 'retornadas')) }}</td>
                <td class="text-center">{{ $dados['total_geral'] }}</td>
                <td class="text-center">
                    @php
                        $totalAprovadas = array_sum(array_column($dados['dados_por_usuario'], 'aprovadas'));
                        $taxaGeralAprovacao = $dados['total_geral'] > 0 ? round(($totalAprovadas / $dados['total_geral']) * 100, 1) : 0;
                    @endphp
                    {{ $taxaGeralAprovacao }}%
                </td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Detailed List -->
    @if($dados['proposicoes']->count() > 0)
    <div class="page-break"></div>
    <h3>Detalhamento das Proposições</h3>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados['proposicoes'] as $proposicao)
            <tr>
                <td>{{ strtoupper($proposicao->tipo) }}</td>
                <td>{{ $proposicao->titulo ?? 'Proposição #' . $proposicao->id }}</td>
                <td>{{ $proposicao->autor->name }}</td>
                <td>
                    @if($proposicao->status === 'aprovado_assinatura')
                        <span class="badge badge-success">Aprovada</span>
                    @elseif($proposicao->status === 'devolvido_correcao')
                        <span class="badge badge-warning">Devolvida</span>
                    @elseif($proposicao->status === 'retornado_legislativo')
                        <span class="badge badge-info">Retornada</span>
                    @endif
                </td>
                <td>{{ $proposicao->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema de Proposições Legislativas</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>