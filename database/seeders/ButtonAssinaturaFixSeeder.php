<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ButtonAssinaturaFixSeeder extends Seeder
{
    /**
     * Fix do botão "Assinar Documento" conforme BUTTON-ASSINATURA-FIX-v2.md
     */
    public function run(): void
    {
        $this->command->info('🔧 Iniciando correção do botão Assinar Documento...');
        
        // 1. Verificar e corrigir permissões
        $this->fixPermissions();
        
        // 2. Adicionar função JavaScript
        $this->addJavaScriptFunction();
        
        // 3. Corrigir botões HTML
        $this->fixButtonsHTML();
        
        $this->command->info('✅ Correção do botão Assinar Documento aplicada com sucesso!');
    }
    
    private function fixPermissions()
    {
        $this->command->info('   🔐 Verificando permissões...');
        
        // Garantir que a permissão existe para PARLAMENTAR
        $permission = DB::table('screen_permissions')
            ->where('role_name', 'PARLAMENTAR')
            ->where('screen_route', 'proposicoes.assinar')
            ->first();
            
        if (!$permission) {
            DB::table('screen_permissions')->insert([
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'proposicoes.assinar',
                'screen_name' => 'Assinar Proposição',
                'screen_module' => 'proposicoes',
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->info('   ✅ Permissão proposicoes.assinar adicionada para PARLAMENTAR');
        } else {
            // Atualizar para garantir que está ativa
            DB::table('screen_permissions')
                ->where('role_name', 'PARLAMENTAR')
                ->where('screen_route', 'proposicoes.assinar')
                ->update(['can_access' => true]);
            $this->command->info('   ✅ Permissão proposicoes.assinar confirmada para PARLAMENTAR');
        }
    }
    
    private function addJavaScriptFunction()
    {
        $this->command->info('   📜 Adicionando função JavaScript...');
        
        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!file_exists($viewPath)) {
            $this->command->warn("   ⚠️  Arquivo view não encontrado: $viewPath");
            return;
        }
        
        $content = file_get_contents($viewPath);
        
        // Verificar se função já existe
        if (strpos($content, 'function verificarAutenticacaoENavegar') !== false) {
            $this->command->info('   ✅ Função verificarAutenticacaoENavegar já existe');
            return;
        }
        
        // Função JavaScript para verificação de autenticação
        $jsFunction = "
// 🔧 Fix v2.0: Verificação de autenticação antes de navegar
function verificarAutenticacaoENavegar(url) {
    console.log('🔍 Verificando autenticação antes de navegar para:', url);
    
    // Mostrar loading
    Swal.fire({
        title: 'Verificando acesso...',
        html: '<div class=\"spinner-border text-primary\" role=\"status\"></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Testar acesso com fetch
    fetch(url, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        Swal.close();
        
        if (response.status === 200 && !response.url.includes('/login')) {
            // ✅ Sucesso - navegar
            window.location.href = url;
        } else if (response.url.includes('/login') || response.status === 302) {
            // 🔐 Sessão expirada
            Swal.fire({
                title: 'Sessão Expirada',
                html: `<div class=\"text-center\">
                    <i class=\"fas fa-exclamation-triangle text-warning fa-3x mb-3\"></i>
                    <p class=\"mb-3\">Sua sessão expirou. Você precisa fazer login novamente.</p>
                    <p class=\"small text-muted\">Você será redirecionado para a página de login.</p>
                </div>`,
                icon: null,
                confirmButtonText: 'Fazer Login',
                confirmButtonColor: '#007bff'
            }).then(() => window.location.href = '/login');
        } else if (response.status === 403) {
            // ❌ Sem permissão
            Swal.fire({
                title: 'Acesso Negado',
                html: `<div class=\"text-center\">
                    <i class=\"fas fa-ban text-danger fa-3x mb-3\"></i>
                    <p>Você não tem permissão para assinar esta proposição.</p>
                </div>`,
                icon: null,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        } else {
            // 🚨 Outro erro
            Swal.fire({
                title: 'Erro de Acesso',
                text: `Erro \${response.status}: Não foi possível acessar a página de assinatura.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('🚨 Erro na requisição:', error);
        // Fallback: tentar navegação direta
        window.location.href = url;
    });
}
";
        
        // Procurar pelo último </script> antes do @endpush
        $lastScriptPos = strrpos($content, '</script>');
        if ($lastScriptPos === false) {
            $this->command->warn('   ⚠️  Tag </script> não encontrada no arquivo');
            return;
        }
        
        // Inserir função antes do último </script>
        $newContent = substr_replace($content, $jsFunction . "\n", $lastScriptPos, 0);
        
        file_put_contents($viewPath, $newContent);
        $this->command->info('   ✅ Função JavaScript adicionada com sucesso');
    }
    
    private function fixButtonsHTML()
    {
        $this->command->info('   🔧 Corrigindo botões HTML...');
        
        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!file_exists($viewPath)) {
            $this->command->warn("   ⚠️  Arquivo view não encontrado: $viewPath");
            return;
        }
        
        $content = file_get_contents($viewPath);
        
        // Verificar se os botões já estão convertidos (procurar por <button onclick>)
        if (strpos($content, '<button type="button" class="btn btn-success btn-lg btn-assinatura" onclick="verificarAutenticacaoENavegar') !== false) {
            $this->command->info('   ✅ Botões já foram convertidos para <button onclick>');
            return;
        }
        
        // Se a função existe mas os botões ainda são <a href>, precisa converter
        if (strpos($content, 'verificarAutenticacaoENavegar') !== false) {
            $this->command->info('   🔄 Função existe, mas botões precisam ser convertidos...');
        }
        
        // Padrões ROBUSTOS para capturar diferentes variações de botões de assinatura
        $patterns = [
            // Padrão 1: href simples
            [
                'search' => '<a href="{{ route(\'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success">',
                'replace' => '<button type="button" class="btn btn-success btn-lg btn-assinatura" onclick="verificarAutenticacaoENavegar(\'{{ route(\'proposicoes.assinar\', $proposicao->id) }}\');">'
            ],
            // Padrão 2: href com btn-lg
            [
                'search' => '<a href="{{ route(\'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-lg">',
                'replace' => '<button type="button" class="btn btn-success btn-lg btn-assinatura" onclick="verificarAutenticacaoENavegar(\'{{ route(\'proposicoes.assinar\', $proposicao->id) }}\');">'
            ],
            // Padrão 3: href com btn-assinatura
            [
                'search' => '<a href="{{ route(\'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-assinatura">',
                'replace' => '<button type="button" class="btn btn-success btn-lg btn-assinatura" onclick="verificarAutenticacaoENavegar(\'{{ route(\'proposicoes.assinar\', $proposicao->id) }}\');">'
            ],
            // Padrão 4: href com btn-lg btn-assinatura
            [
                'search' => '<a href="{{ route(\'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-lg btn-assinatura">',
                'replace' => '<button type="button" class="btn btn-success btn-lg btn-assinatura" onclick="verificarAutenticacaoENavegar(\'{{ route(\'proposicoes.assinar\', $proposicao->id) }}\');">'
            ]
        ];
        
        $changes = 0;
        foreach ($patterns as $pattern) {
            if (strpos($content, $pattern['search']) !== false) {
                $content = str_replace($pattern['search'], $pattern['replace'], $content);
                $changes++;
            }
        }
        
        // Corrigir tags de fechamento </a> para </button>
        $closingPatterns = [
            [
                'search' => '<i class="fas fa-signature me-2"></i>Assinar Documento</a>',
                'replace' => '<i class="fas fa-signature me-2"></i>Assinar Documento</button>'
            ]
        ];
        
        foreach ($closingPatterns as $pattern) {
            if (strpos($content, $pattern['search']) !== false) {
                $content = str_replace($pattern['search'], $pattern['replace'], $content);
                $changes++;
            }
        }
        
        if ($changes > 0) {
            file_put_contents($viewPath, $content);
            $this->command->info("   ✅ $changes correções aplicadas aos botões");
        } else {
            $this->command->info('   ✅ Botões já estão corretos ou não encontrados');
        }
    }
}