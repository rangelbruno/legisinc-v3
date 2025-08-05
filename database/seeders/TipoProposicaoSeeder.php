<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoProposicao;

class TipoProposicaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $config = config('tipo_proposicao_mapping');
        $mappings = $config['mappings'] ?? [];
        
        // Tipos essenciais para começar
        $tiposEssenciais = ['pl', 'plc', 'pec', 'pdl', 'pr', 'req', 'ind', 'moc', 'eme'];
        
        foreach ($tiposEssenciais as $key) {
            if (isset($mappings[$key])) {
                $tipo = $mappings[$key];
                
                TipoProposicao::firstOrCreate(
                    ['codigo' => $tipo['codigo']],
                    [
                        'nome' => $tipo['nome'],
                        'descricao' => $this->getDescricao($key),
                        'icone' => $tipo['icone'],
                        'cor' => $tipo['cor'],
                        'ordem' => $tipo['ordem'],
                        'ativo' => true,
                        'configuracoes' => $tipo['configuracoes']
                    ]
                );
            }
        }
    }
    
    private function getDescricao($key): string
    {
        $descricoes = [
            'pl' => 'Projeto de Lei Ordinária - Proposição destinada a regular matéria de competência do Poder Legislativo, com sanção do Poder Executivo.',
            'plc' => 'Projeto de Lei Complementar - Destinado a regulamentar matéria constitucional que exige quórum de maioria absoluta.',
            'pec' => 'Proposta de Emenda à Constituição - Visa alterar o texto constitucional, exigindo aprovação por 3/5 dos membros.',
            'pdl' => 'Projeto de Decreto Legislativo - Regula matérias de competência exclusiva do Poder Legislativo, dispensando sanção.',
            'pr' => 'Projeto de Resolução - Destina-se a regular matéria de competência privativa da Casa Legislativa, de caráter político-administrativo.',
            'req' => 'Requerimento - Proposição pela qual o vereador solicita informações, providências ou manifesta posição sobre determinado assunto.',
            'ind' => 'Indicação - Sugestão de medida de interesse público aos Poderes competentes.',
            'moc' => 'Moção - Proposição em que é sugerida a manifestação da Câmara sobre determinado assunto, aplaudindo, apoiando, protestando ou repudiando.',
            'eme' => 'Emenda - Proposição apresentada como acessória a outra, podendo ser supressiva, substitutiva, aditiva ou modificativa.'
        ];
        
        return $descricoes[$key] ?? '';
    }
}