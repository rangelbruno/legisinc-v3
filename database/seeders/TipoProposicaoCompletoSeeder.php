<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoProposicao;
use Illuminate\Support\Facades\DB;

class TipoProposicaoCompletoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desabilitar verificação de chaves estrangeiras temporariamente
        DB::statement('SET session_replication_role = replica;');
        
        try {
            $config = config('tipo_proposicao_mapping');
            $mappings = $config['mappings'] ?? [];
            
            // Processar todos os tipos do mapeamento
            foreach ($mappings as $key => $tipo) {
                $this->command->info("Criando tipo: {$tipo['nome']}");
                
                TipoProposicao::updateOrCreate(
                    ['codigo' => $tipo['codigo']],
                    [
                        'nome' => $tipo['nome'],
                        'descricao' => $this->getDescricaoCompleta($key),
                        'icone' => $tipo['icone'],
                        'cor' => $tipo['cor'],
                        'ordem' => $tipo['ordem'],
                        'ativo' => true,
                        'configuracoes' => $tipo['configuracoes']
                    ]
                );
            }
            
            $this->command->info('Todos os tipos de proposição foram criados com sucesso!');
            
        } finally {
            // Reabilitar verificação de chaves estrangeiras
            DB::statement('SET session_replication_role = DEFAULT;');
        }
    }
    
    /**
     * Retorna descrições detalhadas para cada tipo
     */
    private function getDescricaoCompleta($key): string
    {
        $descricoes = [
            'pec' => 'Proposta de Emenda à Constituição - Proposição destinada a alterar o texto constitucional. Requer aprovação por 3/5 dos membros em dois turnos de votação.',
            
            'pelom' => 'Proposta de Emenda à Lei Orgânica Municipal - Proposição destinada a alterar a Lei Orgânica do Município. Requer aprovação por 2/3 dos membros em dois turnos.',
            
            'pl' => 'Projeto de Lei Ordinária - Proposição destinada a regular matéria de competência do Poder Legislativo, com sanção do Poder Executivo. Tramita com maioria simples.',
            
            'plc' => 'Projeto de Lei Complementar - Destinado a regulamentar matéria constitucional que exige quórum de maioria absoluta para aprovação.',
            
            'plp' => 'Projeto de Lei Complementar - Destinado a regulamentar matéria constitucional que exige quórum de maioria absoluta para aprovação.',
            
            'pld' => 'Projeto de Lei Delegada - Proposição de iniciativa do Poder Executivo, mediante delegação do Poder Legislativo. Aplicável apenas na esfera federal.',
            
            'mp' => 'Medida Provisória - Ato normativo de iniciativa exclusiva do Chefe do Executivo, com força de lei por até 120 dias. Deve atender requisitos de urgência e relevância.',
            
            'pdl' => 'Projeto de Decreto Legislativo - Regula matérias de competência exclusiva do Poder Legislativo, dispensando sanção. Produz efeitos externos.',
            
            'pdc' => 'Projeto de Decreto do Congresso - Similar ao PDL, usado em matérias de competência exclusiva do Congresso Nacional.',
            
            'pr' => 'Projeto de Resolução - Destina-se a regular matéria de competência privativa da Casa Legislativa, de caráter político-administrativo. Produz efeitos internos.',
            
            'req' => 'Requerimento - Proposição pela qual o parlamentar solicita informações, providências ou manifesta posição. Possui mais de 20 subespécies diferentes.',
            
            'ind' => 'Indicação - Sugestão de medida de interesse público aos Poderes competentes, especialmente ao Executivo. Não possui caráter vinculante.',
            
            'moc' => 'Moção - Proposição em que é sugerida a manifestação da Câmara sobre determinado assunto, podendo ser de aplauso, apoio, protesto, repúdio, pesar ou congratulação.',
            
            'eme' => 'Emenda - Proposição acessória apresentada a outra, visando alterar seu texto. Pode ser supressiva, aditiva, substitutiva, modificativa ou aglutinativa.',
            
            'sub' => 'Subemenda - Proposição acessória que visa alterar uma emenda já apresentada.',
            
            'substitutivo' => 'Substitutivo - Texto integral alternativo apresentado ao projeto original, substituindo-o completamente.',
            
            'par' => 'Parecer de Comissão - Opinião técnica emitida por comissão sobre aspectos de constitucionalidade, mérito, impacto financeiro ou redação final.',
            
            'rel' => 'Relatório - Documento que precede o parecer, usado em CPIs, comissões especiais e comissões mistas.',
            
            'rec' => 'Recurso - Proposição que questiona ato da Mesa Diretora ou de comissão, solicitando revisão de decisão.',
            
            'veto' => 'Veto - Recusa do Executivo em sancionar projeto aprovado pelo Legislativo. Pode ser total ou parcial. Deve ser apreciado em sessão conjunta.',
            
            'destaque' => 'Destaque - Pedido para que parte específica de um texto seja votada separadamente do conjunto da proposição.',
            
            'ofi' => 'Ofício - Comunicação oficial entre órgãos ou poderes, podendo conter solicitações, informações ou encaminhamentos.',
            
            'msg' => 'Mensagem do Executivo - Comunicação do Chefe do Executivo ao Legislativo para envio de projetos, comunicação de sanções, vetos ou prestação de informações.',
            
            'pcl' => 'Projeto de Consolidação das Leis - Proposição que reúne diplomas legais esparsos sobre determinada matéria em um texto único, sem alteração de mérito.'
        ];
        
        return $descricoes[$key] ?? 'Tipo de proposição legislativa.';
    }
}