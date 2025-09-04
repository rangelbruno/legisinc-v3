<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SmartPreservationSeeder extends Seeder
{
    /**
     * Seeder inteligente que preserva melhorias automaticamente
     * Executa SEMPRE antes dos seeders que podem sobrescrever código
     */
    
    private $preservationDir;
    private $trackingTable = 'melhorias_tracking';

    public function __construct()
    {
        $this->preservationDir = storage_path('app/smart-preservation');
    }

    public function run(): void
    {
        $this->command->info('🧠 Sistema Inteligente de Preservação v2.0');
        
        $this->criarInfraestrutura();
        $this->detectarEPreservarAlteracoes();
        $this->configurarHooks();
        
        $this->command->info('✅ Sistema inteligente ativado!');
    }

    private function criarInfraestrutura(): void
    {
        // Criar diretórios
        if (!File::exists($this->preservationDir)) {
            File::makeDirectory($this->preservationDir, 0755, true);
        }

        // Criar tabela de rastreamento usando Laravel Schema (compatível com qualquer DB)
        if (!Schema::hasTable($this->trackingTable)) {
            Schema::create($this->trackingTable, function ($table) {
                $table->id();
                $table->string('arquivo', 500)->index();
                $table->text('hash_anterior')->nullable();
                $table->text('hash_atual');
                $table->string('tipo', 20)->default('modificado');
                $table->json('metadata')->nullable();
                $table->boolean('preservado')->default(false);
                $table->timestamps();
                
                $table->index('preservado');
                $table->index('created_at');
            });
            
            $this->command->info('📊 Tabela de rastreamento criada');
        }
    }

    private function detectarEPreservarAlteracoes(): void
    {
        $this->command->info('🔍 Detectando melhorias para preservar...');
        
        $arquivosMonitorados = [
            'app/Http/Controllers/ProposicaoAssinaturaController.php',
            'app/Http/Controllers/ProposicaoProtocoloController.php',
            'app/Services/OnlyOffice/OnlyOfficeService.php', 
            'app/Services/Template/TemplateProcessorService.php',
            'app/Services/Template/TemplateVariableService.php',
            'app/Models/Proposicao.php',
            'config/dompdf.php'
        ];

        $preservados = 0;
        
        foreach ($arquivosMonitorados as $arquivo) {
            if ($this->preservarArquivo($arquivo)) {
                $preservados++;
            }
        }

        $this->command->info("💾 Arquivos preservados: {$preservados}/" . count($arquivosMonitorados));
    }

    private function preservarArquivo(string $arquivo): bool
    {
        $caminhoCompleto = base_path($arquivo);
        
        if (!File::exists($caminhoCompleto)) {
            return false;
        }

        // Calcular hash atual
        $hashAtual = hash('sha256', File::get($caminhoCompleto) . File::lastModified($caminhoCompleto));
        
        // Verificar se já está rastreado
        $registro = DB::table($this->trackingTable)
                      ->where('arquivo', $arquivo)
                      ->orderBy('created_at', 'desc')
                      ->first();

        $precisaPreservar = false;

        if (!$registro) {
            // Arquivo novo no sistema
            $precisaPreservar = true;
            $hashAnterior = null;
        } elseif ($registro->hash_atual !== $hashAtual) {
            // Arquivo foi modificado
            $precisaPreservar = true;
            $hashAnterior = $registro->hash_atual;
        } else {
            // Arquivo não mudou
            return false;
        }

        if ($precisaPreservar) {
            // Fazer backup físico do arquivo
            $backupPath = $this->preservationDir . '/' . str_replace('/', '_', $arquivo) . '.backup';
            File::copy($caminhoCompleto, $backupPath);

            // Registrar no banco
            DB::table($this->trackingTable)->insert([
                'arquivo' => $arquivo,
                'hash_anterior' => $hashAnterior,
                'hash_atual' => $hashAtual,
                'tipo' => $hashAnterior ? 'modificado' : 'novo',
                'metadata' => json_encode([
                    'tamanho' => File::size($caminhoCompleto),
                    'modificado_em' => date('Y-m-d H:i:s', File::lastModified($caminhoCompleto)),
                    'backup_path' => $backupPath
                ]),
                'preservado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->command->line("  ✓ Preservado: {$arquivo}");
            return true;
        }

        return false;
    }

    private function configurarHooks(): void
    {
        // Criar arquivo de configuração para hooks
        $hookConfig = [
            'pre_migrate' => [
                'backup_critical_files' => true,
                'track_changes' => true,
                'preserve_improvements' => true
            ],
            'post_seed' => [
                'restore_preserved_files' => true,
                'validate_preservation' => true,
                'update_tracking' => true
            ],
            'monitored_files' => [
                'app/Http/Controllers/ProposicaoAssinaturaController.php',
                'app/Http/Controllers/ProposicaoProtocoloController.php',
                'app/Services/OnlyOffice/OnlyOfficeService.php',
                'app/Services/Template/TemplateProcessorService.php',
                'app/Services/Template/TemplateVariableService.php',
                'app/Models/Proposicao.php',
                'config/dompdf.php'
            ]
        ];

        File::put(
            storage_path('app/smart-preservation-config.json'),
            json_encode($hookConfig, JSON_PRETTY_PRINT)
        );

        $this->command->info('⚙️ Hooks configurados');
    }

    /**
     * Método para ser chamado APÓS os seeders que podem sobrescrever
     */
    public function restaurarPreservacoes(): void
    {
        $this->command->info('♻️ Restaurando melhorias preservadas...');
        
        $preservados = DB::table($this->trackingTable)
                        ->where('preservado', true)
                        ->where('created_at', '>=', now()->subHour()) // Últimas modificações
                        ->get();

        $restaurados = 0;

        foreach ($preservados as $registro) {
            $metadata = json_decode($registro->metadata, true);
            $backupPath = $metadata['backup_path'] ?? null;

            if ($backupPath && File::exists($backupPath)) {
                $arquivoOriginal = base_path($registro->arquivo);
                
                // Comparar se o arquivo foi realmente sobrescrito
                $hashAtual = hash('sha256', File::get($arquivoOriginal) . File::lastModified($arquivoOriginal));
                
                if ($hashAtual !== $registro->hash_atual) {
                    // Arquivo foi sobrescrito, restaurar backup
                    File::copy($backupPath, $arquivoOriginal);
                    $restaurados++;
                    $this->command->line("  ✓ Restaurado: {$registro->arquivo}");
                }
            }
        }

        $this->command->info("🔄 Arquivos restaurados: {$restaurados}");
    }
}