<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LimpezaCodigoDebugSeeder extends Seeder
{
    /**
     * Seeder para remover código de debug e manter apenas versão de produção
     * 
     * Este seeder garante que:
     * 1. Remove rotas de teste/debug temporárias
     * 2. Remove botões de debug das views
     * 3. Mantém apenas código de produção limpo
     * 4. Preserva todas as otimizações funcionais
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpando código de debug e mantendo versão de produção...');
        
        // 1. Limpar rotas de debug
        $this->limparRotasDebug();
        
        // 2. Limpar views de debug
        $this->limparViewsDebug();
        
        // 3. Validar que código de produção está preservado
        $this->validarCodigoProducao();
        
        $this->command->info('✅ Código de produção limpo e otimizado!');
    }
    
    /**
     * Remove rotas de debug temporárias
     */
    private function limparRotasDebug(): void
    {
        $routesFile = base_path('routes/web.php');
        
        if (File::exists($routesFile)) {
            $content = File::get($routesFile);
            
            // Remover rota de teste debug
            $debugRoutePattern = '/\s*\/\/ ROTA DE TESTE - REMOVER APÓS DEBUG.*?->name\(\'test-debug\'\);/s';
            $content = preg_replace($debugRoutePattern, '', $content);
            
            // Remover linhas específicas de debug se existirem
            $linesToRemove = [
                "    // ROTA DE TESTE - REMOVER APÓS DEBUG",
                "    Route::get('/{proposicao}/test-debug', function(\$proposicao) {",
                "        \$prop = App\\Models\\Proposicao::findOrFail(\$proposicao);",
                "        return response('<h1>TESTE DEBUG</h1><p>Proposição ID: ' . \$prop->id . '</p><p>Status: ' . \$prop->status . '</p><p>Usuário atual: ' . (Auth::check() ? Auth::user()->name : 'NÃO LOGADO') . '</p><a href=\"/proposicoes/1\">← Voltar</a>');",
                "    })->name('test-debug');"
            ];
            
            foreach ($linesToRemove as $line) {
                $content = str_replace($line, '', $content);
            }
            
            // Limpar linhas vazias excessivas
            $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
            
            File::put($routesFile, $content);
            $this->command->info('   🗑️ Rotas de debug removidas de routes/web.php');
        }
    }
    
    /**
     * Remove botões de debug das views
     */
    private function limparViewsDebug(): void
    {
        $showViewFile = base_path('resources/views/proposicoes/show.blade.php');
        
        if (File::exists($showViewFile)) {
            $content = File::get($showViewFile);
            
            // Remover bloco de botão de debug
            $debugButtonPattern = '/\s*<!-- TESTE DEBUG - REMOVER APÓS -->.*?<\/a>/s';
            $content = preg_replace($debugButtonPattern, '', $content);
            
            // Remover linhas específicas se existirem
            $linesToRemove = [
                '                            <!-- TESTE DEBUG - REMOVER APÓS -->',
                '                            <a href="{{ route(\'proposicoes.test-debug\', $proposicao->id) }}" class="btn btn-warning btn-sm mt-2">',
                '                                🔍 TESTE DEBUG',
                '                            </a>'
            ];
            
            foreach ($linesToRemove as $line) {
                $content = str_replace($line, '', $content);
            }
            
            File::put($showViewFile, $content);
            $this->command->info('   🗑️ Botões de debug removidos de show.blade.php');
        }
    }
    
    /**
     * Validar que código de produção essencial está preservado
     */
    private function validarCodigoProducao(): void
    {
        $this->command->info('   🔍 Validando código de produção...');
        
        // Verificar ProposicaoAssinaturaController
        $controllerFile = base_path('app/Http/Controllers/ProposicaoAssinaturaController.php');
        if (File::exists($controllerFile)) {
            $content = File::get($controllerFile);
            
            $methodsRequired = [
                'encontrarArquivoMaisRecente',
                'extrairConteudoDOCX',
                'limparPDFsAntigos'
            ];
            
            $allMethodsPresent = true;
            foreach ($methodsRequired as $method) {
                if (!str_contains($content, $method)) {
                    $allMethodsPresent = false;
                    $this->command->warn("      ⚠️ Método $method não encontrado no Controller");
                }
            }
            
            if ($allMethodsPresent) {
                $this->command->info('      ✅ ProposicaoAssinaturaController: Métodos otimizados preservados');
            }
        }
        
        // Verificar OnlyOfficeService
        $serviceFile = base_path('app/Services/OnlyOffice/OnlyOfficeService.php');
        if (File::exists($serviceFile)) {
            $content = File::get($serviceFile);
            
            if (str_contains($content, 'timestamp = time()')) {
                $this->command->info('      ✅ OnlyOfficeService: Callback otimizado preservado');
            } else {
                $this->command->warn('      ⚠️ OnlyOfficeService: Callback pode precisar verificação');
            }
        }
        
        // Verificar rota principal de assinatura
        $routesFile = base_path('routes/web.php');
        if (File::exists($routesFile)) {
            $content = File::get($routesFile);
            
            if (str_contains($content, "Route::get('/{proposicao}/assinar'") && 
                str_contains($content, 'ProposicaoAssinaturaController::class')) {
                $this->command->info('      ✅ Rota proposicoes.assinar preservada');
            } else {
                $this->command->warn('      ⚠️ Rota de assinatura pode estar comprometida');
            }
        }
        
        // Verificar permissões
        $permission = \DB::table('screen_permissions')
            ->where('role_name', 'PARLAMENTAR')
            ->where('screen_route', 'proposicoes.assinar')
            ->where('can_access', true)
            ->first();
            
        if ($permission) {
            $this->command->info('      ✅ Permissão proposicoes.assinar preservada');
        } else {
            $this->command->warn('      ⚠️ Permissão de assinatura pode estar faltando');
        }
    }
}