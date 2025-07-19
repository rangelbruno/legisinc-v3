<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Parametro\ConfiguracaoParametroService;

class ParametroModularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuracaoService = app(ConfiguracaoParametroService::class);
        
        // Configura os parâmetros iniciais do sistema
        $configuracaoService->configurarParametrosIniciais();
        
        $this->command->info('Parâmetros modulares criados com sucesso!');
    }
}