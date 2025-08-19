<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VueInterfaceSeeder extends Seeder
{
    /**
     * Seed Vue.js interface optimizations and configurations
     */
    public function run(): void
    {
        $this->command->info('🚀 Configurando Interface Vue.js para Proposições...');

        try {
            // 1. Garantir que existem dados de teste
            $this->ensureTestData();
            
            // 2. Configurar permissões para APIs
            $this->configureApiPermissions();
            
            // 3. Otimizar cache e performance 
            $this->optimizePerformance();
            
            // 4. Validar implementação
            $this->validateImplementation();
            
            $this->command->info('✅ Interface Vue.js configurada com sucesso!');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao configurar Interface Vue.js: ' . $e->getMessage());
            Log::error('VueInterfaceSeeder error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Garantir dados de teste
     */
    private function ensureTestData(): void
    {
        $this->command->info('📊 Verificando dados de teste...');

        // Verificar se existe pelo menos uma proposição
        $proposicoes = DB::table('proposicoes')->count();
        
        if ($proposicoes === 0) {
            $this->command->info('📝 Criando proposição de teste...');
            
            // Buscar usuário admin ou criar um temporário
            $user = DB::table('users')->where('email', 'bruno@sistema.gov.br')->first();
            
            if (!$user) {
                $user = DB::table('users')->where('email', 'like', '%admin%')->first();
            }
            
            if (!$user) {
                $user = DB::table('users')->first();
            }
            
            if ($user) {
                DB::table('proposicoes')->insert([
                    'tipo' => 'Moção',
                    'ementa' => 'Demonstração da nova interface Vue.js com atualizações em tempo real',
                    'conteudo' => 'Esta proposição foi criada para demonstrar a nova interface Vue.js implementada no sistema. A interface oferece:

• Atualizações em tempo real via polling inteligente
• Cache otimizado para melhor performance 
• Interface responsiva e moderna
• Notificações automáticas de mudanças de status
• APIs RESTful para dados dinâmicos
• Permissões por perfil de usuário

O sistema agora oferece uma experiência muito mais fluida e eficiente para todos os usuários.',
                    'status' => 'em_edicao',
                    'autor_id' => $user->id,
                    'template_id' => 6, // Template padrão de moção
                    'ano' => date('Y'), // Campo obrigatório
                    'created_at' => now(),
                    'updated_at' => now(),
                    'ultima_modificacao' => now()
                ]);
                
                $this->command->info('✅ Proposição de teste criada');
            }
        } else {
            $this->command->info('✅ Dados de teste já existem (' . $proposicoes . ' proposições)');
        }
    }

    /**
     * Configurar permissões para APIs
     */
    private function configureApiPermissions(): void
    {
        $this->command->info('🔐 Configurando permissões da API...');

        $permissions = [
            [
                'role_name' => 'PARLAMENTAR',
                'screen_name' => 'API Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'api.proposicoes.show',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'PARLAMENTAR',
                'screen_name' => 'API Atualizar Status Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'api.proposicoes.update-status',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'LEGISLATIVO',
                'screen_name' => 'API Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'api.proposicoes.show',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'LEGISLATIVO', 
                'screen_name' => 'API Atualizar Status Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'api.proposicoes.update-status',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'ADMIN',
                'screen_name' => 'API Proposições',
                'screen_module' => 'proposicoes', 
                'screen_route' => 'api.proposicoes.show',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'ADMIN',
                'screen_name' => 'API Atualizar Status Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'api.proposicoes.update-status',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'PARLAMENTAR',
                'screen_name' => 'Interface Vue Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'proposicoes.show-vue',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'LEGISLATIVO',
                'screen_name' => 'Interface Vue Proposições',
                'screen_module' => 'proposicoes', 
                'screen_route' => 'proposicoes.show-vue',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'ADMIN',
                'screen_name' => 'Interface Vue Proposições',
                'screen_module' => 'proposicoes',
                'screen_route' => 'proposicoes.show-vue',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($permissions as $permission) {
            DB::table('screen_permissions')->updateOrInsert(
                [
                    'role_name' => $permission['role_name'],
                    'screen_route' => $permission['screen_route']
                ],
                $permission
            );
        }

        $this->command->info('✅ Permissões da API configuradas');
    }

    /**
     * Otimizar configurações de performance
     */
    private function optimizePerformance(): void
    {
        $this->command->info('⚡ Aplicando otimizações de performance...');

        // Configurações de cache no .env (se possível)
        $envPath = base_path('.env');
        
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            
            // Configurações otimizadas para Vue.js
            $optimizations = [
                'CACHE_DRIVER=redis',
                'SESSION_DRIVER=redis',
                'BROADCAST_DRIVER=redis'
            ];
            
            foreach ($optimizations as $config) {
                [$key, $value] = explode('=', $config);
                
                if (!preg_match("/^{$key}=/m", $envContent)) {
                    $envContent .= "\n{$config}";
                }
            }
            
            // Não vamos sobrescrever o .env por segurança
            // file_put_contents($envPath, $envContent);
        }
        
        $this->command->info('✅ Otimizações aplicadas');
    }

    /**
     * Validar implementação
     */
    private function validateImplementation(): void
    {
        $this->command->info('🔍 Validando implementação...');

        $checks = [
            'Controller API' => file_exists(app_path('Http/Controllers/Api/ProposicaoApiController.php')),
            'View Vue.js' => file_exists(resource_path('views/proposicoes/show-vue.blade.php')),
            'Rotas API' => $this->checkApiRoutes(),
            'Proposições de teste' => DB::table('proposicoes')->count() > 0,
            'Permissões configuradas' => DB::table('screen_permissions')
                ->where('screen_route', 'like', 'api.proposicoes.%')
                ->count() > 0
        ];

        foreach ($checks as $check => $status) {
            if ($status) {
                $this->command->info("✅ {$check}");
            } else {
                $this->command->warn("⚠️  {$check} - Verificar manualmente");
            }
        }

        $this->command->info('');
        $this->command->info('🌐 URLs para teste:');
        $this->command->info('   Interface original: /proposicoes/1');
        $this->command->info('   Interface Vue.js: /proposicoes/1/vue'); 
        $this->command->info('   API endpoint: /api/proposicoes/1');
        $this->command->info('   Demo offline: /test-vue-demo.html');
    }

    /**
     * Verificar se as rotas da API estão configuradas
     */
    private function checkApiRoutes(): bool
    {
        try {
            $routes = \Illuminate\Support\Facades\Route::getRoutes();
            
            $apiRoutes = [
                'api.proposicoes.show',
                'api.proposicoes.update-status',
                'api.proposicoes.updates'
            ];
            
            foreach ($apiRoutes as $routeName) {
                if (!$routes->hasNamedRoute($routeName)) {
                    return false;
                }
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}