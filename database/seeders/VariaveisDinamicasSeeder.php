<?php

namespace Database\Seeders;

use App\Models\VariavelDinamica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariaveisDinamicasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Populando variáveis dinâmicas padrão...');
        
        $variaveisPadrao = VariavelDinamica::getVariaveisPadrao();
        
        foreach ($variaveisPadrao as $index => $variavel) {
            VariavelDinamica::updateOrCreate(
                ['nome' => $variavel['nome']],
                [
                    'valor' => $variavel['valor'],
                    'descricao' => $variavel['descricao'],
                    'tipo' => $variavel['tipo'],
                    'escopo' => $variavel['escopo'],
                    'formato' => $variavel['formato'],
                    'validacao' => $variavel['validacao'],
                    'sistema' => $variavel['sistema'],
                    'ativo' => true,
                    'ordem' => $index + 1,
                    'created_by' => 1, // Admin user
                    'updated_by' => 1
                ]
            );
        }
        
        $this->command->info('✅ Variáveis dinâmicas padrão criadas com sucesso!');
    }
}
