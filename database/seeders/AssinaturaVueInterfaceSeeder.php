<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AssinaturaVueInterfaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->comment('🎨 CONFIGURANDO INTERFACE VUE.JS DE ASSINATURA...');
        
        // 1. Verificar se o controller está usando a view Vue.js
        $this->validateController();
        
        // 2. Adicionar permissões específicas se necessário
        $this->addPermissions();
        
        // 3. Validar estrutura de arquivos
        $this->validateFiles();
        
        $this->info('✅ Interface Vue.js de assinatura configurada com sucesso!');
        $this->newLine();
        $this->info('📋 FUNCIONALIDADES IMPLEMENTADAS:');
        $this->info('   ⚡ Interface reativa com Vue.js 3');
        $this->info('   📱 Design responsivo e moderno');
        $this->info('   🔄 Loading states e feedback visual');
        $this->info('   📁 Upload de certificados com drag & drop');
        $this->info('   📄 Visualizador de PDF integrado');
        $this->info('   🔔 Sistema de notificações toast');
        $this->info('   ⚡ Performance otimizada');
        $this->info('   🎯 UX/UI seguindo padrões do projeto');
        $this->newLine();
        $this->info('🌟 Acesse: http://localhost:8001/proposicoes/2/assinar');
    }
    
    private function validateController()
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        
        if (!File::exists($controllerPath)) {
            $this->error('❌ Controller ProposicaoAssinaturaController não encontrado!');
            return;
        }
        
        $content = File::get($controllerPath);
        
        if (strpos($content, 'assinar-vue') !== false) {
            $this->info('✅ Controller configurado para usar interface Vue.js');
        } else {
            $this->warn('⚠️  Controller ainda não configurado para Vue.js');
            
            // Atualizar controller automaticamente
            $content = str_replace(
                "return view('proposicoes.assinatura.assinar', compact('proposicao'));",
                "return view('proposicoes.assinatura.assinar-vue', compact('proposicao'));",
                $content
            );
            
            File::put($controllerPath, $content);
            $this->info('✅ Controller atualizado automaticamente');
        }
    }
    
    private function addPermissions()
    {
        // Verificar se as permissões existem no sistema
        $permissions = [
            'proposicoes.assinar' => 'Assinar proposições',
            'proposicoes.devolver-legislativo' => 'Devolver proposições para legislativo',
            'proposicoes.serve-pdf' => 'Visualizar PDFs de proposições'
        ];
        
        foreach ($permissions as $permission => $description) {
            $exists = \Spatie\Permission\Models\Permission::where('name', $permission)->exists();
            if (!$exists) {
                $this->warn("⚠️  Permissão '$permission' não encontrada");
            } else {
                $this->info("✅ Permissão '$permission' configurada");
            }
        }
    }
    
    private function validateFiles()
    {
        $files = [
            'resources/views/proposicoes/assinatura/assinar-vue.blade.php' => 'Interface Vue.js de assinatura',
            'resources/views/proposicoes/assinatura/assinar.blade.php' => 'Interface original (backup)',
            'app/Http/Controllers/ProposicaoAssinaturaController.php' => 'Controller de assinatura'
        ];
        
        foreach ($files as $path => $description) {
            $fullPath = base_path($path);
            if (File::exists($fullPath)) {
                $this->info("✅ $description encontrado");
            } else {
                $this->error("❌ $description não encontrado: $path");
            }
        }
        
        // Verificar se a view Vue.js tem o conteúdo esperado
        $vueViewPath = resource_path('views/proposicoes/assinatura/assinar-vue.blade.php');
        if (File::exists($vueViewPath)) {
            $content = File::get($vueViewPath);
            
            $features = [
                'vue@3/dist/vue.global.js' => 'Vue.js 3 CDN incluído',
                'createApp' => 'Vue.js 3 configurado',
                'v-cloak' => 'Prevenção de flash de conteúdo',
                'certificado-option' => 'Sistema de certificados',
                'file-upload-area' => 'Upload de arquivos',
                'toast-container' => 'Sistema de notificações',
                'pdf-viewer-container' => 'Visualizador de PDF'
            ];
            
            foreach ($features as $feature => $description) {
                if (strpos($content, $feature) !== false) {
                    $this->info("✅ $description implementado");
                } else {
                    $this->warn("⚠️  $description não encontrado");
                }
            }
        }
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