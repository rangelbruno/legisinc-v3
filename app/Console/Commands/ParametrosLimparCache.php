<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parametro\ParametroService;

class ParametrosLimparCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parametros:cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpar todo o cache do sistema de parâmetros';

    protected ParametroService $parametroService;

    /**
     * Create a new command instance.
     */
    public function __construct(ParametroService $parametroService)
    {
        parent::__construct();
        $this->parametroService = $parametroService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Limpando cache do sistema de parâmetros...');

        try {
            $this->parametroService->limparTodoCache();
            $this->info('Cache limpo com sucesso!');
            return 0;

        } catch (\Exception $e) {
            $this->error("Erro ao limpar cache: {$e->getMessage()}");
            return 1;
        }
    }
}