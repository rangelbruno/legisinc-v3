<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;
use App\Models\User;
use App\Models\Role;

class VueUIImprovementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎨 Configurando melhorias de UI com Vue.js...');

        // Adicionar permissão para nova interface Vue
        $this->addVueInterfacePermission();
        
        // Validar se todas as dependências estão funcionando
        $this->validateDependencies();
        
        $this->command->info('✅ Melhorias de UI com Vue.js configuradas com sucesso!');
        $this->showAccessInstructions();
    }

    /**
     * Adicionar permissão para acessar a nova interface Vue
     */
    private function addVueInterfacePermission(): void
    {
        $roles = ['PARLAMENTAR', 'LEGISLATIVO', 'ADMIN'];
        
        foreach ($roles as $roleName) {
            ScreenPermission::updateOrCreate([
                'role_name' => $roleName,
                'screen_route' => 'proposicoes.criar-vue',
                'screen_module' => 'proposicoes'
            ], [
                'screen_name' => 'Criar Proposição (Interface Vue)',
                'access' => true,
                'can_access' => true
            ]);
        }
        
        $this->command->info('   ✓ Permissões adicionadas para interface Vue.js');
    }

    /**
     * Validar dependências necessárias
     */
    private function validateDependencies(): void
    {
        $validations = [
            'Arquivo Vue criado' => file_exists(resource_path('views/proposicoes/create-vue.blade.php')),
            'Rota Vue configurada' => $this->checkRouteExists(),
            'Templates existem' => $this->checkTemplatesExist(),
            'Usuários existem' => User::count() > 0,
            'Permissões configuradas' => ScreenPermission::where('screen_route', 'proposicoes.criar-vue')->count() > 0
        ];

        foreach ($validations as $description => $valid) {
            $status = $valid ? '✅' : '❌';
            $this->command->info("   {$status} {$description}");
        }
    }

    /**
     * Verificar se a rota existe
     */
    private function checkRouteExists(): bool
    {
        try {
            return \Illuminate\Support\Facades\Route::has('proposicoes.criar-vue');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar se templates existem
     */
    private function checkTemplatesExist(): bool
    {
        return \App\Models\TipoProposicaoTemplate::count() > 0;
    }

    /**
     * Mostrar instruções de acesso
     */
    private function showAccessInstructions(): void
    {
        $this->command->info('');
        $this->command->info('🚀 ===== NOVA INTERFACE VUE.JS CONFIGURADA =====');
        $this->command->info('');
        $this->command->info('📱 ACESSOS DISPONÍVEIS:');
        $this->command->info('   • Interface Atual: http://localhost:8001/proposicoes/criar');
        $this->command->info('   • Interface Vue: http://localhost:8001/proposicoes/criar-vue');
        $this->command->info('');
        $this->command->info('👥 USUÁRIOS CONFIGURADOS:');
        $this->command->info('   • jessica@sistema.gov.br / 123456 (Parlamentar)');
        $this->command->info('   • joao@sistema.gov.br / 123456 (Legislativo)');
        $this->command->info('   • bruno@sistema.gov.br / 123456 (Admin)');
        $this->command->info('');
        $this->command->info('🎯 RECURSOS DA NOVA INTERFACE:');
        $this->command->info('   ✨ Interface moderna e responsiva');
        $this->command->info('   🎨 Animações e transições fluidas');
        $this->command->info('   📱 Design mobile-first');
        $this->command->info('   ⚡ Performance otimizada com Vue 3');
        $this->command->info('   💾 Auto-save em localStorage');
        $this->command->info('   🔄 Wizard em 3 etapas intuitivas');
        $this->command->info('   🎭 Preview em tempo real');
        $this->command->info('   🤖 Integração com IA melhorada');
        $this->command->info('   📝 Editor de texto com formatação');
        $this->command->info('   🔔 Notificações toast elegantes');
        $this->command->info('');
        $this->command->info('💡 MELHORIAS TÉCNICAS:');
        $this->command->info('   • Componente Vue.js isolado');
        $this->command->info('   • Estado reativo com Composition API');
        $this->command->info('   • Validação em tempo real');
        $this->command->info('   • Cacheable e otimizado');
        $this->command->info('   • Integração total com backend Laravel');
        $this->command->info('');
        $this->command->info('🧪 PARA TESTAR:');
        $this->command->info('   1. Acesse http://localhost:8001/proposicoes/criar-vue');
        $this->command->info('   2. Faça login como parlamentar');
        $this->command->info('   3. Teste o wizard de criação');
        $this->command->info('   4. Compare com a interface atual');
        $this->command->info('');
        $this->command->line('💫 A nova interface representa um upgrade significativo na UX/UI!');
    }
}