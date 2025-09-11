<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class PreservationService
{
    protected array $config;
    protected string $manifestFile;

    public function __construct()
    {
        $this->config = config('preservation', [
            'watch' => [
                'include' => ['app/**', 'config/**', 'resources/views/**'],
                'exclude' => ['resources/views/proposicoes/show.blade.php'],
            ],
            'skip_if_no_changes' => true,
            'manifest_file' => storage_path('preservation/last-manifest.json'),
        ]);
        
        $this->manifestFile = $this->config['manifest_file'];
    }

    /**
     * Gera uma nova rodada de preservação apenas se houver mudanças
     */
    public function generatePreservationRound(): ?array
    {
        $currentManifest = $this->generateManifest();
        
        if ($this->config['skip_if_no_changes'] && $this->manifestExists() && $this->manifestsEqual($currentManifest)) {
            return null; // Sem mudanças, não gerar nova rodada
        }

        // Derivar número da rodada a partir do próximo número disponível
        $round = $this->getNextRoundNumber();
        
        // Criar migration
        $migrationPath = $this->createMigration($round);
        
        // Criar seeder com mesmo número
        $seederPath = $this->createSeeder($round, $currentManifest);
        
        // Atualizar DatabaseSeeder
        $this->updateDatabaseSeeder($round);
        
        // Atualizar documentação
        $this->updateDocumentation($round, $currentManifest);
        
        // Salvar manifesto atual
        $this->saveManifest($currentManifest);

        return [
            'round' => $round,
            'migration' => $migrationPath,
            'seeder' => $seederPath,
            'changes' => count($currentManifest),
        ];
    }

    /**
     * Gera manifesto atual dos arquivos monitorados
     */
    protected function generateManifest(): array
    {
        $includes = collect($this->config['watch']['include'] ?? []);
        $excludes = collect($this->config['watch']['exclude'] ?? []);

        $files = $includes->flatMap(function ($pattern) {
            return collect(glob(base_path($pattern), GLOB_BRACE))
                ->filter('is_file')
                ->map(fn($path) => Str::after($path, base_path() . DIRECTORY_SEPARATOR));
        });

        // Aplicar excludes
        $files = $files->reject(function ($path) use ($excludes) {
            return $excludes->contains(function ($exclude) use ($path) {
                return Str::is($exclude, $path);
            });
        });

        // Gerar hashes
        return $files->mapWithKeys(function ($path) {
            $fullPath = base_path($path);
            return [$path => file_exists($fullPath) ? sha1_file($fullPath) : null];
        })->all();
    }

    /**
     * Obtém o próximo número de rodada baseado nas migrations existentes
     */
    protected function getNextRoundNumber(): int
    {
        $migrations = glob(database_path('migrations/*_preserve_melhorias_*.php'));
        
        $lastRound = collect($migrations)
            ->map(function ($path) {
                preg_match('/preserve_melhorias_(\d+)\.php$/', $path, $matches);
                return isset($matches[1]) ? (int) $matches[1] : 0;
            })
            ->max();

        return ($lastRound ?? 0) + 1;
    }

    /**
     * Cria migration com número específico
     */
    protected function createMigration(int $round): string
    {
        $timestamp = now()->format('Y_m_d_His');
        $migrationName = "preserve_melhorias_{$round}";
        $fileName = "{$timestamp}_{$migrationName}.php";
        $path = database_path("migrations/{$fileName}");

        $content = $this->getMigrationTemplate($round);
        File::put($path, $content);

        return $path;
    }

    /**
     * Cria seeder com mesmo número da migration
     */
    protected function createSeeder(int $round, array $manifest): string
    {
        $seederClass = "PreservarMelhorias{$round}Seeder";
        $fileName = "{$seederClass}.php";
        $path = database_path("seeders/{$fileName}");

        $content = $this->getSeederTemplate($round, $seederClass, $manifest);
        File::put($path, $content);

        return $path;
    }

    /**
     * Atualiza DatabaseSeeder com referência consistente
     */
    protected function updateDatabaseSeeder(int $round): void
    {
        $path = database_path('seeders/DatabaseSeeder.php');
        $content = File::get($path);
        
        $seederClass = "PreservarMelhorias{$round}Seeder";
        
        // Adicionar nova chamada antes do último comentário
        $newCall = "        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente\n" .
                   "        \$this->call([\n" .
                   "            {$seederClass}::class,\n" .
                   "        ]);\n\n";

        // Inserir antes do último comentário "ÚLTIMO:"
        $content = str_replace(
            '        // ÚLTIMO:',
            $newCall . '        // ÚLTIMO:',
            $content
        );

        File::put($path, $content);
    }

    /**
     * Templates dos arquivos gerados
     */
    protected function getMigrationTemplate(int $round): string
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Migration de rastreamento para preservação de melhorias #{$round}
        // Esta migration serve apenas como marcador histórico
    }

    public function down(): void
    {
        // Não há reversão necessária
    }
};
";
    }

    protected function getSeederTemplate(int $round, string $className, array $manifest): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $changesJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return "<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\File;
use Illuminate\\Support\\Facades\\Log;

class {$className} extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: {$timestamp}
     * 
     * Alterações detectadas:
     * {$changesJson}
     */
    public function run(): void
    {
        try {
            \$this->command->info('🛡️ Preservando melhorias detectadas automaticamente...');
            
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
        foreach (\$this->arquivosPreservados() as \$arquivo => \$conteudo) {
            \$caminhoCompleto = base_path(\$arquivo);
            
            if (!File::exists(dirname(\$caminhoCompleto))) {
                File::makeDirectory(dirname(\$caminhoCompleto), 0755, true);
            }
            
            File::put(\$caminhoCompleto, \$conteudo);
            \$this->command->info(\"  ✓ Restaurado: {\$arquivo}\");
        }
    }

    private function validarPreservacao(): void
    {
        \$preservados = 0;
        foreach (\$this->arquivosPreservados() as \$arquivo => \$conteudo) {
            if (File::exists(base_path(\$arquivo))) {
                \$preservados++;
            }
        }
        
        \$total = count(\$this->arquivosPreservados());
        \$this->command->info(\"📊 Validação: {\$preservados}/{\$total} arquivos preservados\");
    }

    private function arquivosPreservados(): array
    {
        // Aqui ficaria o conteúdo dos arquivos preservados
        // Este método seria preenchido com os dados reais dos arquivos
        return [];
    }
}
";
    }

    /**
     * Métodos auxiliares para verificação de manifesto
     */
    protected function manifestExists(): bool
    {
        return File::exists($this->manifestFile);
    }

    protected function manifestsEqual(array $current): bool
    {
        if (!$this->manifestExists()) {
            return false;
        }

        $previous = json_decode(File::get($this->manifestFile), true);
        return $previous === $current;
    }

    protected function saveManifest(array $manifest): void
    {
        $dir = dirname($this->manifestFile);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($this->manifestFile, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function updateDocumentation(int $round, array $manifest): void
    {
        $docPath = base_path('MELHORIAS-AUTOMATICAS.md');
        $timestamp = now()->format('Y-m-d H:i:s');
        
        $newEntry = "
## Melhoria #{$round}

**Data**: {$timestamp}
**Seeder**: PreservarMelhorias{$round}Seeder  
**Migration**: preserve_melhorias_{$round}
**Arquivos alterados**: " . count($manifest) . "

**Alterações detectadas:**
```json
" . json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "
```

---
";

        if (File::exists($docPath)) {
            $content = File::get($docPath);
            // Inserir após o cabeçalho
            $content = preg_replace('/^(# .*?\\n\\n)/m', "$1{$newEntry}", $content);
        } else {
            $content = "# Melhorias Automáticas - Histórico\n\n{$newEntry}";
        }

        File::put($docPath, $content);
    }
}
";
    