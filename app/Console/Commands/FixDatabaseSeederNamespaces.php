<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixDatabaseSeederNamespaces extends Command
{
    protected $signature = 'fix:seeder-namespaces';
    protected $description = 'Fix malformed namespace references in DatabaseSeeder';

    public function handle()
    {
        $seederPath = database_path('seeders/DatabaseSeeder.php');
        
        if (!File::exists($seederPath)) {
            $this->error('DatabaseSeeder.php not found');
            return 1;
        }

        $content = File::get($seederPath);
        $originalContent = $content;
        
        // Fix malformed namespace patterns
        $fixes = [
            // Fix Database\Seeders\Database\Seeders\ClassName pattern
            '/Database\\\\Seeders\\\\Database\\\\Seeders\\\\([A-Za-z0-9_]+)::class/' => '$1::class',
            // Fix Database\Seeders\ClassName pattern when it should be just ClassName
            '/Database\\\\Seeders\\\\([A-Za-z0-9_]+Seeder)::class/' => '$1::class',
            // Fix any other malformed Database\Seeders patterns
            '/Database\\\\Seeders\\\\([A-Za-z0-9_]+)::class/' => '$1::class',
        ];
        
        $fixedCount = 0;
        foreach ($fixes as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $fixedCount += substr_count($content, $pattern) - substr_count($newContent, $pattern);
                $content = $newContent;
            }
        }
        
        if ($content !== $originalContent) {
            File::put($seederPath, $content);
            $this->info("✅ Fixed {$fixedCount} namespace issues in DatabaseSeeder");
            return 0;
        }
        
        $this->info('✅ No namespace issues found in DatabaseSeeder');
        return 0;
    }
}