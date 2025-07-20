<?php

namespace Database\Seeders;

use App\Models\TipoProposicao;
use Illuminate\Database\Seeder;

class TipoProposicaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'codigo' => 'projeto_lei_ordinaria',
                'nome' => 'Projeto de Lei Ordinária',
                'descricao' => 'Proposição destinada a regulamentar matérias de competência da União, que não exijam lei complementar.',
                'icone' => 'ki-document',
                'cor' => 'primary',
                'ativo' => true,
                'ordem' => 1,
                'configuracoes' => [
                    'requer_quorum_especial' => false,
                    'tramitacao_urgente' => false,
                    'areas_permitidas' => ['geral', 'administrativa', 'social']
                ]
            ],
            [
                'codigo' => 'projeto_lei_complementar',
                'nome' => 'Projeto de Lei Complementar',
                'descricao' => 'Proposição destinada a regulamentar matérias reservadas à lei complementar pela Constituição Federal.',
                'icone' => 'ki-file-added',
                'cor' => 'success',
                'ativo' => true,
                'ordem' => 2,
                'configuracoes' => [
                    'requer_quorum_especial' => true,
                    'tramitacao_urgente' => false,
                    'areas_permitidas' => ['constitucional', 'tributaria', 'financeira']
                ]
            ],
            [
                'codigo' => 'proposta_emenda_constitucional',
                'nome' => 'Proposta de Emenda Constitucional',
                'descricao' => 'Proposição destinada a modificar, acrescentar ou suprimir dispositivos da Constituição Federal.',
                'icone' => 'ki-security-user',
                'cor' => 'warning',
                'ativo' => true,
                'ordem' => 3,
                'configuracoes' => [
                    'requer_quorum_especial' => true,
                    'tramitacao_urgente' => false,
                    'votacao_dois_turnos' => true,
                    'areas_permitidas' => ['constitucional']
                ]
            ],
            [
                'codigo' => 'decreto_legislativo',
                'nome' => 'Projeto de Decreto Legislativo',
                'descricao' => 'Proposição destinada a regular matérias de competência exclusiva do Congresso Nacional.',
                'icone' => 'ki-notepad',
                'cor' => 'info',
                'ativo' => true,
                'ordem' => 4,
                'configuracoes' => [
                    'requer_quorum_especial' => false,
                    'tramitacao_urgente' => true,
                    'areas_permitidas' => ['externa', 'administrativa', 'fiscalizacao']
                ]
            ],
            [
                'codigo' => 'resolucao',
                'nome' => 'Projeto de Resolução',
                'descricao' => 'Proposição destinada a regular matérias de competência privativa de cada Casa do Congresso Nacional.',
                'icone' => 'ki-verify',
                'cor' => 'secondary',
                'ativo' => true,
                'ordem' => 5,
                'configuracoes' => [
                    'requer_quorum_especial' => false,
                    'tramitacao_urgente' => false,
                    'areas_permitidas' => ['interna', 'administrativa', 'regimental']
                ]
            ],
            [
                'codigo' => 'indicacao',
                'nome' => 'Indicação',
                'descricao' => 'Proposição pela qual o parlamentar sugere ao Poder Executivo a adoção de providência ou a realização de atos administrativos.',
                'icone' => 'ki-arrow-up-right',
                'cor' => 'light',
                'ativo' => true,
                'ordem' => 6,
                'configuracoes' => [
                    'requer_quorum_especial' => false,
                    'tramitacao_urgente' => false,
                    'sem_votacao' => true,
                    'areas_permitidas' => ['geral', 'administrativa', 'social', 'infraestrutura']
                ]
            ],
            [
                'codigo' => 'requerimento',
                'nome' => 'Requerimento',
                'descricao' => 'Proposição pela qual o parlamentar solicita informações ou documentos a autoridades públicas.',
                'icone' => 'ki-questionnaire-tablet',
                'cor' => 'danger',
                'ativo' => true,
                'ordem' => 7,
                'configuracoes' => [
                    'requer_quorum_especial' => false,
                    'tramitacao_urgente' => true,
                    'prazo_resposta_dias' => 30,
                    'areas_permitidas' => ['fiscalizacao', 'informacao', 'transparencia']
                ]
            ],
            [
                'codigo' => 'mocao',
                'nome' => 'Moção',
                'descricao' => 'Proposição pela qual a Casa manifesta sua opinião sobre determinado assunto ou presta homenagem.',
                'icone' => 'ki-message-text',
                'cor' => 'dark',
                'ativo' => true,
                'ordem' => 8,
                'configuracoes' => [
                    'requer_quorum_especial' => false,
                    'tramitacao_urgente' => false,
                    'tipo_manifestacao' => ['apoio', 'repudio', 'congratulacao', 'pesar'],
                    'areas_permitidas' => ['geral', 'protocolar', 'manifestacao']
                ]
            ]
        ];

        foreach ($tipos as $tipo) {
            TipoProposicao::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                $tipo
            );
        }
    }
}