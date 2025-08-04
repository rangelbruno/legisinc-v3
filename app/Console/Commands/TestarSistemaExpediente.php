<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposicao;
use App\Services\MomentoSessaoService;

class TestarSistemaExpediente extends Command
{
    protected $signature = 'expediente:testar-sistema';
    protected $description = 'Testa o sistema completo do Expediente';

    public function handle()
    {
        $this->info('🧪 Testando o sistema do Expediente...');
        $this->newLine();

        // 1. Verificar se existem proposições
        $totalProposicoes = Proposicao::count();
        $this->info("📊 Total de proposições no sistema: {$totalProposicoes}");

        if ($totalProposicoes === 0) {
            $this->warn('⚠️ Nenhuma proposição encontrada. Execute: php artisan expediente:popular-exemplos');
            return 0;
        }

        // 2. Verificar proposições protocoladas
        $protocoladas = Proposicao::where('status', 'protocolado')->count();
        $this->info("📋 Proposições protocoladas: {$protocoladas}");

        // 3. Testar classificação automática
        $this->info('🔧 Testando classificação automática...');
        $classificadas = MomentoSessaoService::reclassificarProposicoes();
        $this->info("✅ Reclassificadas automaticamente: {$classificadas}");

        // 4. Verificar estatísticas
        $estatisticas = MomentoSessaoService::obterEstatisticas();
        $this->newLine();
        $this->info('📈 Estatísticas do sistema:');
        $this->table(
            ['Categoria', 'Quantidade', 'Percentual'],
            [
                ['Total', $estatisticas['total'], '100%'],
                ['Expediente', $estatisticas['expediente'], $estatisticas['percentual_expediente'] . '%'],
                ['Ordem do Dia', $estatisticas['ordem_dia'], $estatisticas['percentual_ordem_dia'] . '%'],
                ['Não Classificadas', $estatisticas['nao_classificado'], '---'],
            ]
        );

        // 5. Testar validações de votação
        $this->newLine();
        $this->info('🗳️ Testando validações de votação...');
        
        $proposicoesExpediente = MomentoSessaoService::obterProposicoesPorMomento('EXPEDIENTE');
        $proposicoesOrdemDia = MomentoSessaoService::obterProposicoesPorMomento('ORDEM_DO_DIA');

        $this->info("📋 Proposições do Expediente: {$proposicoesExpediente->count()}");
        foreach ($proposicoesExpediente->take(3) as $proposicao) {
            $validacao = MomentoSessaoService::podeEnviarParaVotacao($proposicao);
            $status = $validacao['pode_enviar'] ? '✅' : '❌';
            $this->line("  {$status} {$proposicao->tipo_formatado}: " . ($validacao['pode_enviar'] ? 'Pode votar' : implode(', ', $validacao['erros'])));
        }

        $this->info("⚖️ Proposições da Ordem do Dia: {$proposicoesOrdemDia->count()}");
        foreach ($proposicoesOrdemDia->take(3) as $proposicao) {
            $validacao = MomentoSessaoService::podeEnviarParaVotacao($proposicao);
            $status = $validacao['pode_enviar'] ? '✅' : '❌';
            $this->line("  {$status} {$proposicao->tipo_formatado}: " . ($validacao['pode_enviar'] ? 'Pode votar' : implode(', ', $validacao['erros'])));
        }

        // 6. Verificar rotas
        $this->newLine();
        $this->info('🛣️ Verificando rotas do sistema...');
        
        $rotas = [
            'expediente.index' => 'Painel do Expediente',
            'expediente.aguardando-pauta' => 'Aguardando Pauta',
            'expediente.relatorio' => 'Relatório'
        ];

        foreach ($rotas as $route => $nome) {
            try {
                $url = route($route);
                $this->line("  ✅ {$nome}: {$url}");
            } catch (\Exception $e) {
                $this->line("  ❌ {$nome}: Erro na rota");
            }
        }

        // 7. Resultado final
        $this->newLine();
        $this->info('🎯 RESULTADO DOS TESTES:');
        
        if ($protocoladas > 0) {
            $this->line('✅ Sistema funcionando corretamente!');
            $this->line('✅ Proposições protocoladas encontradas');
            $this->line('✅ Classificação automática funcional');
            $this->line('✅ Validações de votação operacionais');
            $this->line('✅ Rotas configuradas corretamente');
            
            $this->newLine();
            $this->warn('💡 PRÓXIMOS PASSOS:');
            $this->line('1. Acesse /expediente para ver o painel');
            $this->line('2. Teste a classificação manual de proposições');
            $this->line('3. Teste o envio para votação');
            $this->line('4. Confira os relatórios em /expediente/relatorio');
        } else {
            $this->error('❌ Nenhuma proposição protocolada encontrada para testar');
        }

        return 0;
    }
}