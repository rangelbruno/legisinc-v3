<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposicao;
use App\Models\User;
use App\Models\TipoProposicao;
use App\Services\MomentoSessaoService;

class PopularProposicoesExemplo extends Command
{
    protected $signature = 'expediente:popular-exemplos';
    protected $description = 'Popula o banco com proposições de exemplo para testar o sistema do Expediente';

    public function handle()
    {
        $this->info('🌱 Populando proposições de exemplo...');

        // Buscar um usuário autor (primeiro usuário disponível)
        $autor = User::first();
        if (!$autor) {
            $this->error('❌ Nenhum usuário encontrado. Crie pelo menos um usuário primeiro.');
            return 1;
        }

        $funcionarioProtocolo = User::where('id', '!=', $autor->id)->first() ?? $autor;

        // Criar tipos de proposição se não existirem
        $this->criarTiposProposicao();

        // Exemplos de proposições para o Expediente
        $proposicoesExpediente = [
            [
                'tipo' => 'indicacao',
                'ementa' => 'Indica ao Poder Executivo a necessidade de melhorias na iluminação pública do bairro Centro',
                'conteudo' => 'Considerando as constantes reclamações dos moradores sobre a falta de iluminação adequada...',
                'momento_sessao' => 'EXPEDIENTE'
            ],
            [
                'tipo' => 'requerimento_simples',
                'ementa' => 'Requer informações sobre o andamento das obras da praça principal',
                'conteudo' => 'Solicita-se ao Poder Executivo que informe sobre o cronograma e andamento das obras...',
                'momento_sessao' => 'EXPEDIENTE'
            ],
            [
                'tipo' => 'mocao_aplauso',
                'ementa' => 'Moção de Aplauso pelos 50 anos de funcionamento da Escola Municipal João da Silva',
                'conteudo' => 'A Escola Municipal João da Silva completa 50 anos de excelentes serviços prestados...',
                'momento_sessao' => 'EXPEDIENTE'
            ],
            [
                'tipo' => 'voto_pesar',
                'ementa' => 'Voto de Pesar pelo falecimento do ex-vereador José dos Santos',
                'conteudo' => 'Esta Casa de Leis lamenta profundamente o falecimento do ex-vereador José dos Santos...',
                'momento_sessao' => 'EXPEDIENTE'
            ]
        ];

        // Exemplos de proposições para a Ordem do Dia
        $proposicoesOrdemDia = [
            [
                'tipo' => 'projeto_lei_ordinaria',
                'ementa' => 'Dispõe sobre a criação do programa municipal de coleta seletiva de lixo',
                'conteudo' => 'Art. 1º - Fica criado o Programa Municipal de Coleta Seletiva de Lixo...',
                'momento_sessao' => 'ORDEM_DO_DIA',
                'tem_parecer' => true
            ],
            [
                'tipo' => 'projeto_decreto_legislativo',
                'ementa' => 'Concede título de cidadão honorário ao Dr. Paulo Mendes',
                'conteudo' => 'Art. 1º - Fica concedido o título de Cidadão Honorário ao Dr. Paulo Mendes...',
                'momento_sessao' => 'ORDEM_DO_DIA',
                'tem_parecer' => true
            ],
            [
                'tipo' => 'projeto_resolucao',
                'ementa' => 'Altera o Regimento Interno da Câmara Municipal quanto aos prazos de tramitação',
                'conteudo' => 'Art. 1º - O art. 45 do Regimento Interno passa a vigorar com a seguinte redação...',
                'momento_sessao' => 'ORDEM_DO_DIA',
                'tem_parecer' => false
            ]
        ];

        // Proposições não classificadas (para testar classificação)
        $proposicoesNaoClassificadas = [
            [
                'tipo' => 'projeto_lei_complementar',
                'ementa' => 'Institui o Código de Obras do Município',
                'conteudo' => 'Art. 1º - Fica instituído o Código de Obras do Município...',
                'momento_sessao' => 'NAO_CLASSIFICADO'
            ],
            [
                'tipo' => 'requerimento',
                'ementa' => 'Requer a criação de CPI para investigar irregularidades na administração',
                'conteudo' => 'Considerando as denúncias de irregularidades...',
                'momento_sessao' => 'NAO_CLASSIFICADO'
            ]
        ];

        $criadasExpediente = 0;
        $criadasOrdemDia = 0;
        $criadasNaoClassificadas = 0;

        // Criar proposições do Expediente
        foreach ($proposicoesExpediente as $dados) {
            $proposicao = $this->criarProposicao($dados, $autor, $funcionarioProtocolo);
            if ($proposicao) {
                $criadasExpediente++;
                $this->line("✅ Criada (Expediente): {$proposicao->ementa}");
            }
        }

        // Criar proposições da Ordem do Dia
        foreach ($proposicoesOrdemDia as $dados) {
            $proposicao = $this->criarProposicao($dados, $autor, $funcionarioProtocolo);
            if ($proposicao) {
                $criadasOrdemDia++;
                $this->line("⚖️ Criada (Ordem do Dia): {$proposicao->ementa}");
            }
        }

        // Criar proposições não classificadas
        foreach ($proposicoesNaoClassificadas as $dados) {
            $proposicao = $this->criarProposicao($dados, $autor, $funcionarioProtocolo);
            if ($proposicao) {
                $criadasNaoClassificadas++;
                $this->line("❓ Criada (Não Classificada): {$proposicao->ementa}");
            }
        }

        $this->newLine();
        $this->info('📊 Resumo da criação:');
        $this->line("📋 Expediente: {$criadasExpediente} proposições");
        $this->line("⚖️ Ordem do Dia: {$criadasOrdemDia} proposições");
        $this->line("❓ Não Classificadas: {$criadasNaoClassificadas} proposições");
        $this->line("🎯 Total: " . ($criadasExpediente + $criadasOrdemDia + $criadasNaoClassificadas) . " proposições");

        $this->newLine();
        $this->info('✅ Proposições de exemplo criadas com sucesso!');
        $this->warn('💡 Acesse /expediente para visualizar as proposições criadas.');

        return 0;
    }

    private function criarTiposProposicao()
    {
        $tipos = [
            ['codigo' => 'indicacao', 'nome' => 'Indicação'],
            ['codigo' => 'requerimento_simples', 'nome' => 'Requerimento Simples'],
            ['codigo' => 'mocao_aplauso', 'nome' => 'Moção de Aplauso'],
            ['codigo' => 'voto_pesar', 'nome' => 'Voto de Pesar'],
            ['codigo' => 'projeto_lei_ordinaria', 'nome' => 'Projeto de Lei Ordinária'],
            ['codigo' => 'projeto_decreto_legislativo', 'nome' => 'Projeto de Decreto Legislativo'],
            ['codigo' => 'projeto_resolucao', 'nome' => 'Projeto de Resolução'],
            ['codigo' => 'projeto_lei_complementar', 'nome' => 'Projeto de Lei Complementar'],
            ['codigo' => 'requerimento', 'nome' => 'Requerimento'],
        ];

        foreach ($tipos as $tipo) {
            TipoProposicao::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                [
                    'nome' => $tipo['nome'],
                    'descricao' => "Tipo de proposição: {$tipo['nome']}",
                    'ativo' => true,
                    'ordem' => 1
                ]
            );
        }
    }

    private function criarProposicao(array $dados, User $autor, User $funcionarioProtocolo): ?Proposicao
    {
        try {
            $proposicao = Proposicao::create([
                'tipo' => $dados['tipo'],
                'ementa' => $dados['ementa'],
                'conteudo' => $dados['conteudo'],
                'autor_id' => $autor->id,
                'status' => 'protocolado',
                'ano' => date('Y'),
                'momento_sessao' => $dados['momento_sessao'],
                'tem_parecer' => $dados['tem_parecer'] ?? false,
                'numero_protocolo' => 'PROT-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/' . date('Y'),
                'data_protocolo' => now()->subDays(rand(1, 30)),
                'funcionario_protocolo_id' => $funcionarioProtocolo->id,
                'observacoes_protocolo' => 'Proposição criada automaticamente para demonstração do sistema',
            ]);

            return $proposicao;
        } catch (\Exception $e) {
            $this->error("❌ Erro ao criar proposição: {$e->getMessage()}");
            return null;
        }
    }
}