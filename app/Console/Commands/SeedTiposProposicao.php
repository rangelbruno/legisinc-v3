<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TipoProposicaoCompletoSeeder;

class SeedTiposProposicao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:tipos-proposicao {--fresh : Remove existing types before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed all proposition types from the mapping configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            if ($this->confirm('This will remove all existing proposition types. Continue?')) {
                $this->info('Removing existing proposition types...');
                \App\Models\TipoProposicao::truncate();
                $this->info('Existing types removed.');
            } else {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Seeding proposition types...');
        
        $seeder = new TipoProposicaoCompletoSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $count = \App\Models\TipoProposicao::count();
        $this->info("Seeding completed! Total types: {$count}");
        
        // Show summary
        $this->newLine();
        $this->info('Types by category:');
        
        $categories = [
            'Normativas' => ['pec', 'pelom', 'pl', 'plc', 'pld', 'mp', 'pcl'],
            'Decretos/Resoluções' => ['pdl', 'pdc', 'pr'],
            'Processuais' => ['req', 'ind', 'moc', 'eme', 'sub', 'substitutivo'],
            'Outros' => ['par', 'rel', 'rec', 'veto', 'destaque', 'ofi', 'msg']
        ];
        
        foreach ($categories as $category => $codes) {
            $count = \App\Models\TipoProposicao::whereIn('codigo', 
                collect($codes)->map(function($code) {
                    $mapping = config('tipo_proposicao_mapping.mappings.' . $code);
                    return $mapping['codigo'] ?? $code;
                })->toArray()
            )->count();
            
            $this->line("  {$category}: {$count}");
        }
    }
}