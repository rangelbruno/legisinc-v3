<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primeiro criar tabelas OnlyOffice e roles/permissões básicos
        $this->call([
            OnlyOfficeTablesSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        // Criar usuários do sistema com roles
        $this->call([
            SystemUsersSeeder::class,
        ]);

        // Seeders do sistema de parâmetros
        $this->call([
            TipoParametroSeeder::class,
            GrupoParametroSeeder::class,
            ParametroSeeder::class,
            ParametroPermissionSeeder::class,
            ParametroExemploSeeder::class,
            DadosGeraisParametrosSeeder::class,
            DadosGeraisValoresSeeder::class,
        ]);

        // Seeder de permissões de menu
        $this->call([
            MenuPermissionsSeeder::class,
        ]);

        // Seeder de tipos de proposição
        $this->call([
            TipoProposicaoCompletoSeeder::class,
        ]);

        // Seeders de templates
        $this->call([
            ParametrosTemplatesSeeder::class,
            DocumentoModeloTemplateSeeder::class,
            TipoProposicaoTemplatesSeeder::class,
        ]);

        // Seeder de permissões de tela por tipo de usuário
        $this->call([
            UserTypeScreenPermissionsSeeder::class,
        ]);

        // Seeder de configurações de IA
        $this->call([
            AIProvidersSeeder::class,
            AIConfigurationSeeder::class,
        ]);

        // Seeder de menus otimizados (deve ser executado por último para limpar e otimizar)
        $this->call([
            OptimizedMenuPermissionsSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📄 OnlyOffice DocumentServer: Tabelas inicializadas');
        $this->command->info('🏛️ Dados Gerais da Câmara: Módulos, campos e valores padrão configurados');
        $this->command->info('⚙️ Configure seus dados em: /parametros-dados-gerais-camara');
        $this->command->info('');
        $this->command->info('👥 Usuários Disponíveis:');
        $this->command->info('🔧 Admin: bruno@sistema.gov.br / admin@sistema.gov.br - Senha: 123456/admin123');
        $this->command->info('🏛️ Parlamentar: jessica@sistema.gov.br / parlamentar@camara.gov.br - Senha: 123456/parlamentar123');
        $this->command->info('⚖️ Legislativo: joao@sistema.gov.br / servidor@camara.gov.br - Senha: 123456/servidor123');
        $this->command->info('📋 Protocolo: roberto@sistema.gov.br / protocolo@camara.gov.br - Senha: 123456/protocolo123');
        $this->command->info('📝 Expediente: expediente@sistema.gov.br - Senha: 123456');
        $this->command->info('⚖️ Assessor Jurídico: juridico@sistema.gov.br - Senha: 123456');
        $this->command->info('');
    }
}
