<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GenerateMelhoriasSeeders extends Command
{
    protected $signature = 'melhorias:generate 
                          {--detect : Detectar automaticamente alterações nos arquivos}
                          {--create-migration : Criar migration para mudanças de banco}
                          {--create-seeder : Criar seeder para preservar alterações}
                          {--auto : Executar detecção e criação automática}';

    protected $description = 'Gera migrations e seeders automaticamente para preservar melhorias no código';

    private $arquivosMonitorados = [
        'app/Http/Controllers/ProposicaoAssinaturaController.php',
        'app/Http/Controllers/ProposicaoProtocoloController.php', 
        'app/Services/OnlyOffice/OnlyOfficeService.php',
        'app/Services/Template/TemplateProcessorService.php',
        'app/Services/Template/TemplateVariableService.php',
        'app/Models/Proposicao.php',
        'config/dompdf.php',
        'resources/views/proposicoes/**/*.blade.php'
    ];

    private $arquivosAlterados = [];
    private $hashesDir;
    private $melhoriaCounter;

    public function __construct()
    {
        parent::__construct();
        $this->hashesDir = storage_path('app/melhorias-hashes');
        $this->melhoriaCounter = storage_path('app/melhoria-counter.txt');
    }

    public function handle()
    {
        $this->info('🔧 Sistema de Preservação Automática de Melhorias v2.0');
        $this->newLine();

        // Criar diretórios necessários
        $this->criarEstruturaDiretorios();

        if ($this->option('auto')) {
            $this->executarProcessoCompleto();
        } else {
            if ($this->option('detect')) {
                $this->detectarAlteracoes();
            }
            if ($this->option('create-seeder')) {
                $this->criarSeederPreservacao();
            }
            if ($this->option('create-migration')) {
                $this->criarMigrationPreservacao();
            }
        }

        return 0;
    }

    private function executarProcessoCompleto()
    {
        $this->info('🚀 Executando processo completo de preservação...');
        
        // 1. Detectar alterações
        $alteracoes = $this->detectarAlteracoes();
        
        if (empty($alteracoes)) {
            $this->info('✅ Nenhuma alteração detectada. Sistema atualizado.');
            return;
        }

        // 2. Criar seeder de preservação
        $seederPath = $this->criarSeederPreservacao();
        
        // 3. Atualizar DatabaseSeeder
        $this->atualizarDatabaseSeeder($seederPath);
        
        // 4. Criar migration se necessário
        if ($this->confirmarCriacaoMigration()) {
            $this->criarMigrationPreservacao();
        }

        // 5. Gerar documentação
        $this->gerarDocumentacaoAlteracoes();

        $this->newLine();
        $this->info('✅ Processo completo concluído!');
        $this->info('📄 Seeder criado: ' . basename($seederPath));
        $this->info('📋 Documentação atualizada');
    }

    private function detectarAlteracoes(): array
    {
        $this->info('🔍 Detectando alterações nos arquivos monitorados...');
        
        $alteracoes = [];
        
        foreach ($this->arquivosMonitorados as $arquivo) {
            $alteracoes = array_merge($alteracoes, $this->verificarArquivo($arquivo));
        }

        if (!empty($alteracoes)) {
            $this->info("📝 Detectadas {count($alteracoes)} alterações:");
            foreach ($alteracoes as $alteracao) {
                $this->line("   • {$alteracao['arquivo']} ({$alteracao['tipo']})");
            }
        }

        $this->arquivosAlterados = $alteracoes;
        return $alteracoes;
    }

    private function verificarArquivo(string $pattern): array
    {
        $alteracoes = [];
        
        // Expandir patterns com wildcards
        if (str_contains($pattern, '**')) {
            $arquivos = $this->expandirPattern($pattern);
        } else {
            $arquivos = [base_path($pattern)];
        }

        foreach ($arquivos as $arquivo) {
            if (!File::exists($arquivo)) continue;

            $hashAtual = $this->calcularHashArquivo($arquivo);
            $hashArmazenado = $this->obterHashArmazenado($arquivo);

            if ($hashAtual !== $hashArmazenado) {
                $alteracoes[] = [
                    'arquivo' => str_replace(base_path() . '/', '', $arquivo),
                    'tipo' => $hashArmazenado ? 'modificado' : 'novo',
                    'hash_anterior' => $hashArmazenado,
                    'hash_atual' => $hashAtual,
                    'tamanho' => File::size($arquivo),
                    'modificado_em' => Carbon::createFromTimestamp(File::lastModified($arquivo))
                ];

                // Atualizar hash armazenado
                $this->armazenarHash($arquivo, $hashAtual);
            }
        }

        return $alteracoes;
    }

    private function expandirPattern(string $pattern): array
    {
        $basePath = base_path();
        $relativePath = str_replace('**/', '', $pattern);
        $directory = dirname($relativePath);
        $filename = basename($relativePath);

        $fullDirectory = $basePath . '/' . $directory;
        
        if (!File::exists($fullDirectory)) {
            return [];
        }

        return File::glob($fullDirectory . '/**/' . $filename);
    }

    private function calcularHashArquivo(string $arquivo): string
    {
        $conteudo = File::get($arquivo);
        $stats = File::lastModified($arquivo) . '|' . File::size($arquivo);
        return hash('sha256', $conteudo . $stats);
    }

    private function obterHashArmazenado(string $arquivo): ?string
    {
        $hashFile = $this->hashesDir . '/' . hash('md5', $arquivo) . '.hash';
        return File::exists($hashFile) ? File::get($hashFile) : null;
    }

    private function armazenarHash(string $arquivo, string $hash): void
    {
        $hashFile = $this->hashesDir . '/' . hash('md5', $arquivo) . '.hash';
        File::put($hashFile, $hash);
    }

    private function criarSeederPreservacao(): string
    {
        $numero = $this->obterProximoNumeroMelhoria();
        $className = "PreservarMelhorias{$numero}Seeder";
        $seederPath = database_path("seeders/{$className}.php");

        $conteudo = $this->gerarConteudoSeeder($className);
        
        File::put($seederPath, $conteudo);
        
        $this->info("✅ Seeder criado: {$className}");
        
        return $seederPath;
    }

    private function gerarConteudoSeeder(string $className): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $alteracoesJson = json_encode($this->arquivosAlterados, JSON_PRETTY_PRINT);
        
        $backupFiles = $this->gerarCodigoBackupFiles();
        $restoreFiles = $this->gerarCodigoRestoreFiles();
        
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class {$className} extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: {$timestamp}
     * 
     * Alterações detectadas:
     * {$alteracoesJson}
     */
    public function run(): void
    {
        \$this->command->info('🛡️ Preservando melhorias detectadas automaticamente...');
        
        try {
            \$this->preservarArquivos();
            \$this->validarPreservacao();
            
            \$this->command->info('✅ Melhorias preservadas com sucesso!');
            
            Log::info('{$className} - Melhorias preservadas', [
                'arquivos_preservados' => count(\$this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\\Exception \$e) {
            \$this->command->error('❌ Erro ao preservar melhorias: ' . \$e->getMessage());
            Log::error('{$className} - Erro', ['error' => \$e->getMessage()]);
        }
    }

    private function preservarArquivos(): void
    {
        \$arquivos = \$this->arquivosPreservados();
        
        foreach (\$arquivos as \$arquivo => \$backupPath) {
            if (File::exists(base_path(\$arquivo))) {
                // Fazer backup do arquivo atual
                \$currentBackup = \$backupPath . '.current.' . time();
                File::copy(base_path(\$arquivo), \$currentBackup);
                
                // Restaurar versão melhorada se o backup existir
                if (File::exists(\$backupPath)) {
                    File::copy(\$backupPath, base_path(\$arquivo));
                    \$this->command->line("  ✓ Restaurado: {\$arquivo}");
                }
            }
        }
    }

    private function validarPreservacao(): void
    {
        \$arquivos = \$this->arquivosPreservados();
        \$sucessos = 0;
        
        foreach (\$arquivos as \$arquivo => \$backupPath) {
            if (File::exists(base_path(\$arquivo))) {
                \$sucessos++;
            }
        }
        
        \$total = count(\$arquivos);
        \$this->command->info("📊 Validação: {\$sucessos}/{\$total} arquivos preservados");
    }

    private function arquivosPreservados(): array
    {
        return [
{$backupFiles}
        ];
    }
}
PHP;
    }

    private function gerarCodigoBackupFiles(): string
    {
        $code = '';
        
        foreach ($this->arquivosAlterados as $alteracao) {
            $arquivo = $alteracao['arquivo'];
            $backupPath = storage_path("app/melhorias-backup/" . str_replace('/', '_', $arquivo));
            
            // Fazer backup do arquivo atual
            if (File::exists(base_path($arquivo))) {
                if (!File::exists(dirname($backupPath))) {
                    File::makeDirectory(dirname($backupPath), 0755, true);
                }
                File::copy(base_path($arquivo), $backupPath);
            }
            
            $code .= "            '{$arquivo}' => '{$backupPath}',\n";
        }
        
        return rtrim($code, ",\n");
    }

    private function atualizarDatabaseSeeder(string $seederPath): void
    {
        $className = basename($seederPath, '.php');
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        
        if (!File::exists($databaseSeederPath)) {
            $this->warn('⚠️ DatabaseSeeder.php não encontrado');
            return;
        }

        $conteudo = File::get($databaseSeederPath);
        
        // Adicionar call para o novo seeder antes do último seeder
        $novaLinha = "        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente\n        \$this->call([\n            {$className}::class,\n        ]);\n\n        // ÚLTIMO:";
        
        $conteudo = str_replace('        // ÚLTIMO:', $novaLinha, $conteudo);
        
        File::put($databaseSeederPath, $conteudo);
        
        $this->info("✅ DatabaseSeeder atualizado com {$className}");
    }

    private function criarMigrationPreservacao(): string
    {
        $timestamp = date('Y_m_d_His');
        $migrationName = "preserve_melhorias_" . $this->obterProximoNumeroMelhoria();
        $migrationPath = database_path("migrations/{$timestamp}_{$migrationName}.php");
        
        $conteudo = $this->gerarConteudoMigration($migrationName);
        
        File::put($migrationPath, $conteudo);
        
        $this->info("✅ Migration criada: {$migrationName}");
        
        return $migrationPath;
    }

    private function gerarConteudoMigration(string $migrationName): string
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
        $timestamp = now()->format('Y-m-d H:i:s');
        
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: {$timestamp}
     */
    public function up(): void
    {
        // Criar tabela para rastrear melhorias se não existir (PostgreSQL compatível)
        if (!Schema::hasTable('melhorias_tracking')) {
            Schema::create('melhorias_tracking', function (Blueprint \$table) {
                \$table->id();
                \$table->string('arquivo', 500)->index();
                \$table->text('hash_anterior')->nullable();
                \$table->text('hash_atual');
                \$table->string('tipo', 20)->default('modificado');
                \$table->json('metadata')->nullable();
                \$table->boolean('preservado')->default(false);
                \$table->timestamps();
                
                \$table->index('preservado');
                \$table->index('created_at');
            });
        }

        // Registrar alterações detectadas
        \$alteracoes = [
PHP . $this->gerarArrayAlteracoesMigration() . <<<PHP
        ];

        foreach (\$alteracoes as \$alteracao) {
            DB::table('melhorias_tracking')->updateOrInsert(
                ['arquivo' => \$alteracao['arquivo']],
                \$alteracao
            );
        }
    }

    public function down(): void
    {
        // Remover registros desta migration
        DB::table('melhorias_tracking')
          ->where('created_at', '>=', now()->subMinute())
          ->delete();
    }
};
PHP;
    }

    private function gerarArrayAlteracoesMigration(): string
    {
        $code = '';
        foreach ($this->arquivosAlterados as $alteracao) {
            $code .= "            [\n";
            $code .= "                'arquivo' => '{$alteracao['arquivo']}',\n";
            $code .= "                'hash_anterior' => " . ($alteracao['hash_anterior'] ? "'{$alteracao['hash_anterior']}'" : 'null') . ",\n";
            $code .= "                'hash_atual' => '{$alteracao['hash_atual']}',\n";
            $code .= "                'tipo' => '{$alteracao['tipo']}',\n";
            $code .= "                'metadata' => json_encode(" . var_export(['tamanho' => $alteracao['tamanho']], true) . "),\n";
            $code .= "                'created_at' => now(),\n";
            $code .= "                'updated_at' => now(),\n";
            $code .= "            ],\n";
        }
        return rtrim($code, ",\n");
    }

    private function confirmarCriacaoMigration(): bool
    {
        return $this->confirm('Criar migration para rastrear essas alterações?', true);
    }

    private function gerarDocumentacaoAlteracoes(): void
    {
        $docPath = base_path('MELHORIAS-AUTOMATICAS.md');
        $timestamp = now()->format('Y-m-d H:i:s');
        $numero = $this->obterNumeroAtualMelhoria();
        
        $conteudo = File::exists($docPath) ? File::get($docPath) : "# Melhorias Automáticas Detectadas\n\n";
        
        $novaSecao = "## Melhoria #{$numero} - {$timestamp}\n\n";
        $novaSecao .= "**Arquivos alterados:** " . count($this->arquivosAlterados) . "\n\n";
        
        foreach ($this->arquivosAlterados as $alteracao) {
            $novaSecao .= "- `{$alteracao['arquivo']}` ({$alteracao['tipo']})\n";
        }
        
        $novaSecao .= "\n**Seeder criado:** `PreservarMelhorias{$numero}Seeder`\n\n";
        $novaSecao .= "---\n\n";
        
        $conteudo = str_replace("# Melhorias Automáticas Detectadas\n\n", "# Melhorias Automáticas Detectadas\n\n" . $novaSecao, $conteudo);
        
        File::put($docPath, $conteudo);
        
        $this->info("📝 Documentação atualizada: MELHORIAS-AUTOMATICAS.md");
    }

    private function obterProximoNumeroMelhoria(): int
    {
        if (!File::exists($this->melhoriaCounter)) {
            File::put($this->melhoriaCounter, '1');
            return 1;
        }
        
        $atual = (int)File::get($this->melhoriaCounter);
        $proximo = $atual + 1;
        File::put($this->melhoriaCounter, (string)$proximo);
        
        return $proximo;
    }

    private function obterNumeroAtualMelhoria(): int
    {
        if (!File::exists($this->melhoriaCounter)) {
            return 1;
        }
        
        return (int)File::get($this->melhoriaCounter);
    }

    private function criarEstruturaDiretorios(): void
    {
        $diretorios = [
            $this->hashesDir,
            storage_path('app/melhorias-backup'),
            dirname($this->melhoriaCounter)
        ];
        
        foreach ($diretorios as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
        }
    }
}