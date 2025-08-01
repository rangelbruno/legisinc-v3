<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixTemplateEncoding extends Command
{
    protected $signature = 'templates:fix-encoding {--dry-run : Apenas mostrar o que seria corrigido sem fazer mudanças}';
    protected $description = 'Corrigir encoding de templates RTF salvos pelo OnlyOffice';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Corrigindo encoding de templates RTF...');
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: Nenhuma alteração será feita.');
        }
        
        // Encontrar todos os arquivos RTF de templates
        $templatePaths = [
            'templates',
            'app/templates', 
            'app/private/templates'
        ];
        
        $arquivosCorrigidos = 0;
        $totalCorrecoes = 0;
        
        foreach ($templatePaths as $path) {
            if (Storage::exists($path)) {
                $arquivos = Storage::files($path);
                
                foreach ($arquivos as $arquivo) {
                    if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'rtf') {
                        $resultado = $this->corrigirArquivo($arquivo, $dryRun);
                        if ($resultado['correcoes'] > 0) {
                            $arquivosCorrigidos++;
                            $totalCorrecoes += $resultado['correcoes'];
                            
                            $this->line("✅ {$arquivo}: {$resultado['correcoes']} correções");
                        } else {
                            $this->line("ℹ️  {$arquivo}: já correto");
                        }
                    }
                }
            }
        }
        
        if ($totalCorrecoes > 0) {
            if (!$dryRun) {
                $this->info("✅ Correção concluída!");
                $this->line("   Arquivos corrigidos: {$arquivosCorrigidos}");
                $this->line("   Total de correções: {$totalCorrecoes}");
            } else {
                $this->warn("Seriam corrigidos {$arquivosCorrigidos} arquivos com {$totalCorrecoes} correções.");
                $this->line("Execute sem --dry-run para aplicar as correções.");
            }
        } else {
            $this->info("✅ Todos os templates já estão com encoding correto!");
        }
        
        return 0;
    }
    
    private function corrigirArquivo(string $arquivo, bool $dryRun): array
    {
        $conteudoOriginal = Storage::get($arquivo);
        
        // Corrigir bytes UTF-8 específicos que aparecem no RTF
        $correcoesBinarias = [
            // Minúsculas
            "\xC3\xAD" => "\\'ed", // í
            "\xC3\xA3" => "\\'e3", // ã
            "\xC3\xA2" => "\\'e2", // â
            "\xC3\xA1" => "\\'e1", // á
            "\xC3\xA0" => "\\'e0", // à
            "\xC3\xA9" => "\\'e9", // é
            "\xC3\xAA" => "\\'ea", // ê
            "\xC3\xB3" => "\\'f3", // ó
            "\xC3\xB4" => "\\'f4", // ô
            "\xC3\xB5" => "\\'f5", // õ
            "\xC3\xBA" => "\\'fa", // ú
            "\xC3\xA7" => "\\'e7", // ç
            
            // Maiúsculas
            "\xC3\x8D" => "\\'cd", // Í
            "\xC3\x83" => "\\'c3", // Ã
            "\xC3\x82" => "\\'c2", // Â
            "\xC3\x81" => "\\'c1", // Á
            "\xC3\x80" => "\\'c0", // À
            "\xC3\x89" => "\\'c9", // É
            "\xC3\x8A" => "\\'ca", // Ê
            "\xC3\x93" => "\\'d3", // Ó
            "\xC3\x94" => "\\'d4", // Ô
            "\xC3\x95" => "\\'d5", // Õ
            "\xC3\x9A" => "\\'da", // Ú
            "\xC3\x87" => "\\'c7", // Ç
        ];
        
        $conteudoCorrigido = $conteudoOriginal;
        $totalCorrecoes = 0;
        
        foreach ($correcoesBinarias as $utf8Bytes => $rtfCode) {
            $antes = substr_count($conteudoCorrigido, $utf8Bytes);
            $conteudoCorrigido = str_replace($utf8Bytes, $rtfCode, $conteudoCorrigido);
            $depois = substr_count($conteudoCorrigido, $utf8Bytes);
            
            if ($antes > $depois) {
                $totalCorrecoes += ($antes - $depois);
            }
        }
        
        // Salvar apenas se houver correções e não for dry-run
        if ($totalCorrecoes > 0 && !$dryRun) {
            // Criar backup
            $backupPath = $arquivo . '.backup.' . date('Y_m_d_His');
            Storage::put($backupPath, $conteudoOriginal);
            
            // Salvar arquivo corrigido
            Storage::put($arquivo, $conteudoCorrigido);
        }
        
        return [
            'correcoes' => $totalCorrecoes,
            'tamanho_original' => strlen($conteudoOriginal),
            'tamanho_final' => strlen($conteudoCorrigido)
        ];
    }
}