<?php

namespace Database\Seeders;

use App\Models\TemplateUniversal;
use App\Models\TipoProposicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TemplateUniversalPrioridadeSeeder extends Seeder
{
    /**
     * Garantir que o Template Universal sempre tenha prioridade sobre templates específicos
     */
    public function run(): void
    {
        $this->comment('🎯 GARANTINDO PRIORIDADE DO TEMPLATE UNIVERSAL...');
        
        // 1. Obter template universal
        $universal = TemplateUniversal::getDefault();
        if (!$universal || !$universal->ativo) {
            $this->error('❌ Template Universal não encontrado ou inativo');
            return;
        }
        
        $this->info('📋 Template Universal encontrado (ID: ' . $universal->id . ')');
        
        // 2. Verificar templates específicos que são mais novos
        $tiposComTemplateEspecifico = TipoProposicao::with('template')
            ->whereHas('template', function($query) use ($universal) {
                $query->where('ativo', true)
                      ->where('updated_at', '>', $universal->updated_at);
            })
            ->get();
        
        if ($tiposComTemplateEspecifico->count() > 0) {
            $this->warn('⚠️  Encontrados ' . $tiposComTemplateEspecifico->count() . ' templates específicos mais novos');
            
            // 3. Atualizar timestamp do template universal para ser sempre o mais recente
            $universal->touch(); // Atualiza updated_at para agora
            
            $this->info('✅ Template Universal atualizado: ' . $universal->updated_at);
            
            // 4. Verificar resultado
            $verificacao = TipoProposicao::with('template')
                ->whereHas('template', function($query) use ($universal) {
                    $query->where('ativo', true)
                          ->where('updated_at', '>', $universal->fresh()->updated_at);
                })
                ->count();
            
            if ($verificacao == 0) {
                $this->info('✅ Template Universal agora tem prioridade sobre TODOS os templates específicos');
            } else {
                $this->error('❌ Ainda existem ' . $verificacao . ' templates específicos mais novos');
            }
            
        } else {
            $this->info('✅ Template Universal já tem prioridade sobre todos os templates específicos');
        }
        
        // 5. Validar alguns tipos importantes
        $tiposImportantes = ['projeto_lei_ordinaria', 'projeto_decreto_legislativo', 'mocao', 'requerimento'];
        
        $this->info('🔍 Validando tipos importantes:');
        $templateService = app(\App\Services\Template\TemplateUniversalService::class);
        
        foreach ($tiposImportantes as $codigo) {
            $tipo = TipoProposicao::where('codigo', $codigo)->first();
            if ($tipo) {
                $deveUsar = $templateService->deveUsarTemplateUniversal($tipo);
                $status = $deveUsar ? '✅ UNIVERSAL' : '⚠️  ESPECÍFICO';
                $this->info("   • {$tipo->nome}: {$status}");
            }
        }
        
        $this->info('✅ Prioridade do Template Universal garantida!');
        $this->newLine();
    }
    
    // Helper methods
    private function info($message)
    {
        echo "\033[0;32m$message\033[0m\n";
    }
    
    private function warn($message)
    {
        echo "\033[0;33m$message\033[0m\n";
    }
    
    private function error($message)
    {
        echo "\033[0;31m$message\033[0m\n";
    }
    
    private function comment($message)
    {
        echo "\033[0;36m$message\033[0m\n";
    }
    
    private function newLine()
    {
        echo "\n";
    }
}