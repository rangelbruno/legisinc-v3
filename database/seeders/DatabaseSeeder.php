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
            ParametrosModulosFixedSeeder::class, // Criar módulos com IDs fixos
            TipoParametroSeeder::class,
            GrupoParametroSeeder::class,
            ParametroSeeder::class,
            ParametroPermissionSeeder::class,
            ParametroExemploSeeder::class,
            DadosGeraisParametrosSeeder::class,
            DadosGeraisValoresSeeder::class,
            FixDadosGeraisCamposSeeder::class, // CRÍTICO: Criar campos necessários para Dados Gerais
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

        // Processar imagens dos templates admin
        $this->command->info('');
        $this->command->info('🖼️ Processando imagens dos templates admin...');
        \Artisan::call('templates:process-images');
        $this->command->info('✅ Imagens dos templates processadas!');

        $this->command->info('');
        $this->command->info('🎉 ===============================================');
        $this->command->info('✅ SISTEMA LEGISINC CONFIGURADO COM SUCESSO!');
        $this->command->info('🎉 ===============================================');
        $this->command->info('');
        $this->command->info('📄 OnlyOffice DocumentServer: Tabelas inicializadas');
        $this->command->info('🏛️ Dados Gerais da Câmara: Módulos, campos e valores padrão configurados');
        $this->command->info('📝 Templates de Proposições: 23 tipos criados com LC 95/1998');
        $this->command->info('🖼️ Sistema de Imagens RTF: Configurado e funcional');
        $this->command->info('🔤 Codificação UTF-8: Acentuação portuguesa corrigida');
        $this->command->info('');
        $this->command->info('🔧 ===== CONFIGURAÇÕES DISPONÍVEIS =====');
        $this->command->info('📊 Dados Gerais: /parametros-dados-gerais-camara');
        $this->command->info('📝 Templates: /admin/templates');
        $this->command->info('⚙️ Parâmetros Avançados: /parametros');
        $this->command->info('');
        $this->command->info('👥 ===== USUÁRIOS DO SISTEMA =====');
        $this->command->info('🔧 Admin: bruno@sistema.gov.br - Senha: 123456');
        $this->command->info('🏛️ Parlamentar: jessica@sistema.gov.br - Senha: 123456');
        $this->command->info('⚖️ Legislativo: joao@sistema.gov.br - Senha: 123456');
        $this->command->info('📋 Protocolo: roberto@sistema.gov.br - Senha: 123456');
        $this->command->info('📝 Expediente: expediente@sistema.gov.br - Senha: 123456');
        $this->command->info('⚖️ Assessor Jurídico: juridico@sistema.gov.br - Senha: 123456');
        $this->command->info('');
        $this->command->info('🏛️ ===== CÂMARA CONFIGURADA =====');
        $this->command->info('📍 Nome: Câmara Municipal de Caraguatatuba');
        $this->command->info('🏠 Endereço: Praça da República, 40, Centro');
        $this->command->info('📞 Telefone: (12) 3882-5588');
        $this->command->info('🌐 Website: www.camaracaraguatatuba.sp.gov.br');
        $this->command->info('');
        $this->command->info('📋 ===== TEMPLATES EDITÁVEIS FUNCIONAIS =====');
        $this->command->info('✅ Templates editáveis no admin (/admin/templates)');
        $this->command->info('✅ Variáveis substituídas automaticamente');
        $this->command->info('✅ Suporte a RTF Unicode do OnlyOffice');
        $this->command->info('✅ Conteúdo do banco prioritário sobre arquivos');
        $this->command->info('✅ Processamento de ${variavel}, $variavel e $\\{variavel\\}');
        $this->command->info('✅ Decoder Unicode para templates RTF complexos');
        $this->command->info('✅ Cabeçalho com imagem automática');
        $this->command->info('✅ Acentuação portuguesa funcionando');
        $this->command->info('✅ OnlyOffice integrado e operacional');
        $this->command->info('');
        $this->command->info('🚀 Sistema pronto para uso! Acesse: http://localhost:8001');
        $this->command->info('');
    }
}
