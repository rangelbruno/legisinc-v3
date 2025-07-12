<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoProjeto;

class TipoProjetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nome' => 'Lei Ordinária',
                'descricao' => 'Projeto de lei ordinária municipal',
                'template_conteudo' => '
LEI Nº _____, DE ___ DE _______ DE ____

Dispõe sobre ____________ e dá outras providências.

O PREFEITO MUNICIPAL DE _______, Estado de _______, no uso de suas atribuições legais,

FAZ SABER que a Câmara Municipal aprovou e eu sanciono a seguinte Lei:

Art. 1º - [Descrever o objetivo da lei]

Art. 2º - [Definições e conceitos, se aplicável]

Art. 3º - [Disposições específicas]

Art. 4º - As despesas decorrentes da execução desta Lei correrão por conta das dotações orçamentárias próprias.

Art. 5º - Esta Lei entra em vigor na data de sua publicação.

Art. 6º - Revogam-se as disposições em contrário.

Prefeitura Municipal de _______, ___ de _______ de ____.

_________________________
[Nome do Prefeito]
Prefeito Municipal
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'maioria_simples',
                    'tramitacao_especial' => false,
                    'prazo_analise_dias' => 30,
                ]
            ],
            [
                'nome' => 'Lei Complementar',
                'descricao' => 'Projeto de lei complementar municipal',
                'template_conteudo' => '
LEI COMPLEMENTAR Nº _____, DE ___ DE _______ DE ____

Dispõe sobre ____________ e dá outras providências.

O PREFEITO MUNICIPAL DE _______, Estado de _______, no uso de suas atribuições legais,

FAZ SABER que a Câmara Municipal aprovou e eu sanciono a seguinte Lei Complementar:

Art. 1º - [Descrever o objetivo da lei complementar]

Art. 2º - [Definições e conceitos, se aplicável]

Art. 3º - [Disposições específicas]

Art. 4º - As despesas decorrentes da execução desta Lei Complementar correrão por conta das dotações orçamentárias próprias.

Art. 5º - Esta Lei Complementar entra em vigor na data de sua publicação.

Art. 6º - Revogam-se as disposições em contrário.

Prefeitura Municipal de _______, ___ de _______ de ____.

_________________________
[Nome do Prefeito]
Prefeito Municipal
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'maioria_absoluta',
                    'tramitacao_especial' => true,
                    'prazo_analise_dias' => 45,
                ]
            ],
            [
                'nome' => 'Emenda Constitucional',
                'descricao' => 'Proposta de emenda à Lei Orgânica Municipal',
                'template_conteudo' => '
EMENDA À LEI ORGÂNICA MUNICIPAL Nº _____, DE ___ DE _______ DE ____

Altera [dispositivo] da Lei Orgânica do Município de _______.

A CÂMARA MUNICIPAL DE _______, Estado de _______, aprova a seguinte Emenda à Lei Orgânica Municipal:

Art. 1º - O [artigo/parágrafo/inciso] da Lei Orgânica Municipal passa a vigorar com a seguinte redação:

"[Nova redação]"

Art. 2º - Esta Emenda entra em vigor na data de sua promulgação.

Câmara Municipal de _______, ___ de _______ de ____.

_________________________
[Nome do Presidente]
Presidente da Câmara Municipal
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'dois_tercos',
                    'tramitacao_especial' => true,
                    'prazo_analise_dias' => 60,
                    'votacao_duplo_turno' => true,
                ]
            ],
            [
                'nome' => 'Decreto Legislativo',
                'descricao' => 'Decreto legislativo da Câmara Municipal',
                'template_conteudo' => '
DECRETO LEGISLATIVO Nº _____, DE ___ DE _______ DE ____

Dispõe sobre ____________.

A CÂMARA MUNICIPAL DE _______, Estado de _______, no uso de suas atribuições constitucionais e legais,

DECRETA:

Art. 1º - [Objeto do decreto]

Art. 2º - [Disposições específicas]

Art. 3º - Este Decreto Legislativo entra em vigor na data de sua publicação.

Câmara Municipal de _______, ___ de _______ de ____.

_________________________
[Nome do Presidente]
Presidente da Câmara Municipal
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'maioria_simples',
                    'tramitacao_especial' => false,
                    'prazo_analise_dias' => 20,
                ]
            ],
            [
                'nome' => 'Resolução',
                'descricao' => 'Resolução da Câmara Municipal',
                'template_conteudo' => '
RESOLUÇÃO Nº _____, DE ___ DE _______ DE ____

Dispõe sobre ____________.

A CÂMARA MUNICIPAL DE _______, Estado de _______, no uso de suas atribuições regimentais,

RESOLVE:

Art. 1º - [Objeto da resolução]

Art. 2º - [Disposições específicas]

Art. 3º - Esta Resolução entra em vigor na data de sua publicação.

Câmara Municipal de _______, ___ de _______ de ____.

_________________________
[Nome do Presidente]
Presidente da Câmara Municipal
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'maioria_simples',
                    'tramitacao_especial' => false,
                    'prazo_analise_dias' => 15,
                ]
            ],
            [
                'nome' => 'Indicação',
                'descricao' => 'Indicação parlamentar',
                'template_conteudo' => '
INDICAÇÃO Nº _____ / ____

Indica ao Executivo Municipal ____________.

Senhor Presidente,

Considerando ____________;

Considerando ____________;

INDICO ao Executivo Municipal que:

[Texto da indicação]

Sala das Sessões, ___ de _______ de ____.

_________________________
[Nome do Vereador]
Vereador
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'sem_votacao',
                    'tramitacao_especial' => false,
                    'prazo_analise_dias' => 10,
                ]
            ],
            [
                'nome' => 'Requerimento',
                'descricao' => 'Requerimento parlamentar',
                'template_conteudo' => '
REQUERIMENTO Nº _____ / ____

Requer ____________.

Senhor Presidente,

Nos termos regimentais, REQUEIRO que:

[Texto do requerimento]

Sala das Sessões, ___ de _______ de ____.

_________________________
[Nome do Vereador]
Vereador
                ',
                'ativo' => true,
                'metadados' => [
                    'quorum_aprovacao' => 'maioria_simples',
                    'tramitacao_especial' => false,
                    'prazo_analise_dias' => 5,
                ]
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoProjeto::create($tipo);
        }

        $this->command->info('Tipos de projetos criados com sucesso!');
    }
}
