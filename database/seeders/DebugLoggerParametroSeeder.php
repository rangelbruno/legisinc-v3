<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use Illuminate\Support\Facades\DB;

class DebugLoggerParametroSeeder extends Seeder
{
    /**
     * Seed para adicionar o parâmetro Debug Logger ao módulo "Dados Gerais"
     * Permite ativar/desativar o sistema de debug de ações do usuário
     */
    public function run(): void
    {
        $this->command->info('🔧 Configurando parâmetro Debug Logger no sistema...');
        
        // 1. Buscar o módulo "Dados Gerais" (ID 1)
        $moduloDadosGerais = ParametroModulo::find(1);
        
        if (!$moduloDadosGerais) {
            $this->command->error('❌ Módulo "Dados Gerais" não encontrado!');
            return;
        }
        
        // 2. Verificar se já existe submódulo "Sistema" ou criar
        $submoduloSistema = ParametroSubmodulo::where('modulo_id', 1)
            ->where('nome', 'Sistema')
            ->first();
            
        if (!$submoduloSistema) {
            $proximaOrdem = ParametroSubmodulo::where('modulo_id', 1)->max('ordem') + 1;
            
            $submoduloSistema = ParametroSubmodulo::create([
                'modulo_id' => 1,
                'nome' => 'Sistema',
                'descricao' => 'Configurações gerais do sistema',
                'tipo' => 'form',
                'ordem' => $proximaOrdem,
                'ativo' => true,
            ]);
            
            $this->command->info("✅ Submódulo 'Sistema' criado com ID {$submoduloSistema->id}");
        } else {
            $this->command->info("ℹ️  Submódulo 'Sistema' já existe (ID {$submoduloSistema->id})");
        }
        
        // 3. Verificar se o campo debug já existe
        $campoDebug = ParametroCampo::where('submodulo_id', $submoduloSistema->id)
            ->where('nome', 'debug_logger_ativo')
            ->first();
            
        if (!$campoDebug) {
            $proximaOrdem = ParametroCampo::where('submodulo_id', $submoduloSistema->id)->max('ordem') + 1;
            
            $campoDebug = ParametroCampo::create([
                'submodulo_id' => $submoduloSistema->id,
                'nome' => 'debug_logger_ativo',
                'label' => '🔧 Debug Logger',
                'descricao' => 'Ativa/desativa o sistema de debug de ações do usuário em tempo real. Quando ativo, exibe um botão flutuante para capturar ações do usuário em tempo real.',
                'tipo_campo' => 'checkbox',
                'obrigatorio' => false,
                'valor_padrao' => 'false',
                'ordem' => $proximaOrdem,
                'ativo' => true,
            ]);
            
            $this->command->info("✅ Campo 'debug_logger_ativo' criado com ID {$campoDebug->id}");
        } else {
            $this->command->info("ℹ️  Campo 'debug_logger_ativo' já existe (ID {$campoDebug->id})");
        }
        
        // 4. Criar valor inicial se não existir
        $valorAtual = DB::table('parametros_valores')
            ->where('campo_id', $campoDebug->id)
            ->first();
            
        if (!$valorAtual) {
            DB::table('parametros_valores')->insert([
                'campo_id' => $campoDebug->id,
                'valor' => 'false',
                'tipo_valor' => 'boolean',
                'user_id' => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info("✅ Valor inicial 'false' definido para debug_logger_ativo");
        } else {
            $this->command->info("ℹ️  Valor para debug_logger_ativo já existe: {$valorAtual->valor}");
        }
        
        // 5. Limpar cache para garantir que as mudanças sejam visíveis
        if (function_exists('cache')) {
            cache()->forget('debug_logger_ativo');
            $this->command->info("🧹 Cache do debug_logger_ativo limpo");
        }
        
        $this->command->info('✅ Parâmetro Debug Logger configurado com sucesso!');
        $this->command->info('   📍 Localização: Dados Gerais > Sistema > 🔧 Debug Logger');
        $this->command->info('   🎯 Acesse /admin/parametros para ativar/desativar');
    }
}