<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Performance\CacheService;
use App\Services\Performance\PDFOptimizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class OptimizePerformance extends Command
{
    protected $signature = 'performance:optimize 
                            {--cache-warmup : Pré-carregar cache importante}
                            {--cleanup-pdfs : Limpar PDFs antigos}
                            {--optimize-db : Otimizar banco de dados}
                            {--all : Executar todas as otimizações}
                            {--report : Gerar relatório de performance}';

    protected $description = 'Otimizar performance do sistema';

    public function handle()
    {
        $this->info('🚀 Iniciando otimização de performance...');
        $startTime = microtime(true);

        if ($this->option('all')) {
            $this->runAllOptimizations();
        } else {
            $this->runSelectedOptimizations();
        }

        if ($this->option('report')) {
            $this->generatePerformanceReport();
        }

        $executionTime = round((microtime(true) - $startTime), 2);
        $this->info("✅ Otimização concluída em {$executionTime}s");
    }

    private function runAllOptimizations(): void
    {
        $this->cacheWarmup();
        $this->cleanupPDFs();
        $this->optimizeDatabase();
        $this->optimizeApplication();
    }

    private function runSelectedOptimizations(): void
    {
        if ($this->option('cache-warmup')) {
            $this->cacheWarmup();
        }

        if ($this->option('cleanup-pdfs')) {
            $this->cleanupPDFs();
        }

        if ($this->option('optimize-db')) {
            $this->optimizeDatabase();
        }
    }

    private function cacheWarmup(): void
    {
        $this->info('🔥 Aquecendo cache...');

        $cacheService = app(CacheService::class);
        
        try {
            $cacheService->warmupCache();
            $this->info('✅ Cache aquecido com sucesso');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao aquecer cache: ' . $e->getMessage());
        }
    }

    private function cleanupPDFs(): void
    {
        $this->info('🗑️ Limpando PDFs antigos...');

        $pdfService = app(PDFOptimizationService::class);
        
        try {
            $cleaned = $pdfService->cleanupOldPDFs(7); // 7 dias
            $this->info("✅ {$cleaned} PDFs antigos removidos");
        } catch (\Exception $e) {
            $this->error('❌ Erro na limpeza de PDFs: ' . $e->getMessage());
        }
    }

    private function optimizeDatabase(): void
    {
        $this->info('🗄️ Otimizando banco de dados...');

        try {
            // Analisar tabelas principais
            $tables = ['proposicoes', 'users', 'tipo_proposicao_templates'];
            
            foreach ($tables as $table) {
                DB::statement("ANALYZE TABLE {$table}");
                $this->line("  ✓ Tabela {$table} analisada");
            }

            // Verificar índices faltantes
            $this->checkMissingIndexes();

            $this->info('✅ Banco de dados otimizado');
        } catch (\Exception $e) {
            $this->error('❌ Erro na otimização do banco: ' . $e->getMessage());
        }
    }

    private function optimizeApplication(): void
    {
        $this->info('⚡ Otimizando aplicação...');

        try {
            // Cache de configuração
            Artisan::call('config:cache');
            $this->line('  ✓ Cache de configuração criado');

            // Cache de rotas
            Artisan::call('route:cache');
            $this->line('  ✓ Cache de rotas criado');

            // Cache de views
            Artisan::call('view:cache');
            $this->line('  ✓ Cache de views criado');

            // Otimizar autoloader
            Artisan::call('optimize');
            $this->line('  ✓ Autoloader otimizado');

            $this->info('✅ Aplicação otimizada');
        } catch (\Exception $e) {
            $this->error('❌ Erro na otimização da aplicação: ' . $e->getMessage());
        }
    }

    private function checkMissingIndexes(): void
    {
        $this->info('🔍 Verificando índices...');

        // Índices recomendados
        $recommendedIndexes = [
            'proposicoes' => [
                ['autor_id', 'status'],
                ['status', 'created_at'],
                ['tipo', 'status'],
                ['numero_protocolo']
            ],
            'tipo_proposicao_templates' => [
                ['tipo_proposicao_id', 'ativo'],
                ['ativo', 'created_at']
            ]
        ];

        foreach ($recommendedIndexes as $table => $indexes) {
            foreach ($indexes as $columns) {
                $indexName = $table . '_' . implode('_', $columns) . '_index';
                
                // Verificar se índice existe
                $exists = DB::select("
                    SELECT COUNT(*) as count 
                    FROM information_schema.statistics 
                    WHERE table_schema = ? 
                    AND table_name = ? 
                    AND index_name = ?
                ", [config('database.connections.pgsql.database'), $table, $indexName]);

                if ($exists[0]->count == 0) {
                    $columnList = implode(', ', $columns);
                    $this->warn("  ⚠️ Índice recomendado ausente: {$table}({$columnList})");
                    
                    if ($this->confirm("Criar índice {$indexName}?", true)) {
                        try {
                            DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
                            $this->info("  ✅ Índice {$indexName} criado");
                        } catch (\Exception $e) {
                            $this->error("  ❌ Erro ao criar índice: " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    private function generatePerformanceReport(): void
    {
        $this->info('📊 Gerando relatório de performance...');

        try {
            // Métricas do banco
            $dbStats = $this->getDatabaseStats();
            
            // Métricas de cache
            $cacheStats = $this->getCacheStats();
            
            // Métricas de arquivos
            $fileStats = $this->getFileStats();

            // Exibir relatório
            $this->displayReport($dbStats, $cacheStats, $fileStats);

        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    private function getDatabaseStats(): array
    {
        return [
            'total_proposicoes' => DB::table('proposicoes')->count(),
            'proposicoes_com_pdf' => DB::table('proposicoes')->whereNotNull('arquivo_pdf_path')->count(),
            'templates_ativos' => DB::table('tipo_proposicao_templates')->where('ativo', true)->count(),
            'usuarios_ativos' => DB::table('users')->whereNotNull('email_verified_at')->count(),
        ];
    }

    private function getCacheStats(): array
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $info = $redis->info('memory');
            
            return [
                'cache_driver' => config('cache.default'),
                'memory_usage' => $info['used_memory_human'] ?? 'N/A',
                'keys_count' => $redis->dbsize(),
                'hit_rate' => $this->calculateCacheHitRate(),
            ];
        } catch (\Exception $e) {
            return [
                'cache_driver' => config('cache.default'),
                'status' => 'Erro ao conectar: ' . $e->getMessage()
            ];
        }
    }

    private function getFileStats(): array
    {
        $pdfPath = storage_path('app/proposicoes/pdfs');
        $proposicoesPath = storage_path('app/proposicoes');

        return [
            'pdf_count' => $this->countFilesInDirectory($pdfPath, '*.pdf'),
            'pdf_size' => $this->getDirectorySize($pdfPath),
            'proposicoes_size' => $this->getDirectorySize($proposicoesPath),
            'storage_total' => disk_total_space(storage_path()),
            'storage_free' => disk_free_space(storage_path()),
        ];
    }

    private function displayReport(array $dbStats, array $cacheStats, array $fileStats): void
    {
        $this->info('');
        $this->info('📈 RELATÓRIO DE PERFORMANCE');
        $this->info('==========================');
        
        $this->info('🗄️ BANCO DE DADOS:');
        foreach ($dbStats as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        $this->info('');
        $this->info('💾 CACHE:');
        foreach ($cacheStats as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        $this->info('');
        $this->info('📁 ARQUIVOS:');
        foreach ($fileStats as $key => $value) {
            if (is_numeric($value) && $value > 1024) {
                $value = $this->formatBytes($value);
            }
            $this->line("  {$key}: {$value}");
        }
    }

    private function calculateCacheHitRate(): string
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $info = $redis->info('stats');
            
            $hits = $info['keyspace_hits'] ?? 0;
            $misses = $info['keyspace_misses'] ?? 0;
            $total = $hits + $misses;
            
            if ($total > 0) {
                $rate = round(($hits / $total) * 100, 2);
                return "{$rate}%";
            }
        } catch (\Exception $e) {
            // Ignorar erro
        }
        
        return 'N/A';
    }

    private function countFilesInDirectory(string $path, string $pattern = '*'): int
    {
        if (!is_dir($path)) {
            return 0;
        }

        return count(glob($path . '/**/' . $pattern, GLOB_BRACE));
    }

    private function getDirectorySize(string $path): int
    {
        if (!is_dir($path)) {
            return 0;
        }

        $size = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}