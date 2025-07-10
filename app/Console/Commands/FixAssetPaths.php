<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixAssetPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:fix-paths {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix asset paths in Blade templates to use asset() helper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Searching for Blade files with asset path issues...');
        
        $bladeFiles = $this->getBladeFiles();
        $totalChanges = 0;
        
        foreach ($bladeFiles as $file) {
            $changes = $this->fixAssetPathsInFile($file, $dryRun);
            $totalChanges += $changes;
            
            if ($changes > 0) {
                $status = $dryRun ? '[DRY RUN] Would fix' : '✅ Fixed';
                $this->line("{$status} {$changes} asset paths in: {$file}");
            }
        }
        
        if ($totalChanges === 0) {
            $this->info('✨ No asset path issues found!');
        } else {
            $message = $dryRun 
                ? "🔍 Would fix {$totalChanges} asset paths across " . count($bladeFiles) . " files"
                : "✅ Fixed {$totalChanges} asset paths across " . count($bladeFiles) . " files";
            $this->info($message);
            
            if ($dryRun) {
                $this->warn('Run without --dry-run to apply changes');
            }
        }
        
        return 0;
    }
    
    /**
     * Get all Blade template files
     */
    private function getBladeFiles(): array
    {
        $directories = [
            resource_path('views'),
        ];
        
        $files = [];
        
        foreach ($directories as $directory) {
            if (File::exists($directory)) {
                $foundFiles = File::allFiles($directory);
                foreach ($foundFiles as $file) {
                    if ($file->getExtension() === 'php' && str_contains($file->getFilename(), '.blade.')) {
                        $files[] = $file->getPathname();
                    }
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Fix asset paths in a single file
     */
    private function fixAssetPathsInFile(string $filePath, bool $dryRun = false): int
    {
        $content = File::get($filePath);
        $originalContent = $content;
        $changes = 0;
        
        // Pattern to match asset paths that are not using asset() helper
        $patterns = [
            // src="assets/..." -> src="{{ asset('assets/...') }}"
            '/src="(assets\/[^"]+)"/i',
            // href="assets/..." -> href="{{ asset('assets/...') }}"
            '/href="(assets\/[^"]+)"/i',
            // url(assets/...) -> url({{ asset('assets/...') }})
            '/url\((assets\/[^)]+)\)/i',
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function ($matches) use (&$changes) {
                $assetPath = $matches[1];
                $changes++;
                
                // Different replacements based on the attribute
                if (str_contains($matches[0], 'src=')) {
                    return 'src="{{ asset(\'' . $assetPath . '\') }}"';
                } elseif (str_contains($matches[0], 'href=')) {
                    return 'href="{{ asset(\'' . $assetPath . '\') }}"';
                } elseif (str_contains($matches[0], 'url(')) {
                    return 'url({{ asset(\'' . $assetPath . '\') }})';
                }
                
                return $matches[0];
            }, $content);
        }
        
        // Only write if there were changes and not in dry-run mode
        if ($changes > 0 && !$dryRun) {
            File::put($filePath, $content);
        }
        
        return $changes;
    }
} 