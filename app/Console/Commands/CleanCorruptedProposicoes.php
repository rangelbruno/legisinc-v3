<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanCorruptedProposicoes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-corrupted-proposicoes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Iniciando limpeza de proposições corrompidas...');
        
        // Debug simples
        $this->line('Comando executado com sucesso!');
        
        return 0;
    }
}
