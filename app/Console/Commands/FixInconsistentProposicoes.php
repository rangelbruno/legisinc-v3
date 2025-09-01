<?php

namespace App\Console\Commands;

use App\Models\Proposicao;
use App\Services\Template\TemplateUniversalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixInconsistentProposicoes extends Command
{
    protected $signature = 'proposicoes:fix-inconsistent
                            {--dry-run : Show what would be done without making changes}
                            {--force : Force update even if arquivo_path exists}';

    protected $description = 'Fix proposições with inconsistent arquivo_path vs physical files';

    protected TemplateUniversalService $templateUniversalService;

    public function __construct(TemplateUniversalService $templateUniversalService)
    {
        parent::__construct();
        $this->templateUniversalService = $templateUniversalService;
    }

    public function handle()
    {
        $this->info('🔍 Verificando proposições com estados inconsistentes...');

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // 1. Encontrar proposições sem arquivo_path mas com arquivos físicos
        $proposicoesSemPath = Proposicao::whereNull('arquivo_path')->get();
        
        $inconsistencias = [];

        foreach ($proposicoesSemPath as $proposicao) {
            $arquivosEncontrados = $this->findPhysicalFiles($proposicao->id);
            
            if (!empty($arquivosEncontrados)) {
                $inconsistencias[] = [
                    'proposicao' => $proposicao,
                    'arquivos' => $arquivosEncontrados
                ];
            }
        }

        // 2. Encontrar proposições com arquivo_path mas sem arquivo físico
        $proposicoesComPath = Proposicao::whereNotNull('arquivo_path')->get();
        
        foreach ($proposicoesComPath as $proposicao) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_path);
            
            if (!file_exists($caminhoCompleto)) {
                $inconsistencias[] = [
                    'proposicao' => $proposicao,
                    'problema' => 'arquivo_path_sem_arquivo_fisico'
                ];
            }
        }

        if (empty($inconsistencias)) {
            $this->info('✅ Nenhuma inconsistência encontrada!');
            return 0;
        }

        $this->warn("🔍 Encontradas " . count($inconsistencias) . " inconsistência(s):");

        foreach ($inconsistencias as $i => $inconsistencia) {
            $proposicao = $inconsistencia['proposicao'];
            
            $this->line("");
            $this->info("📋 Proposição {$proposicao->id}:");
            $this->line("   Tipo: {$proposicao->tipo}");
            $this->line("   Status: {$proposicao->status}");
            $this->line("   arquivo_path atual: " . ($proposicao->arquivo_path ?: 'null'));

            if (isset($inconsistencia['arquivos'])) {
                $this->line("   Arquivos encontrados:");
                foreach ($inconsistencia['arquivos'] as $arquivo) {
                    $this->line("   - {$arquivo}");
                }

                if (!$dryRun) {
                    $this->fixProposicaoWithPhysicalFiles($proposicao, $inconsistencia['arquivos']);
                }
            } elseif (isset($inconsistencia['problema']) && $inconsistencia['problema'] === 'arquivo_path_sem_arquivo_fisico') {
                $this->line("   ❌ arquivo_path aponta para arquivo inexistente");
                
                if (!$dryRun) {
                    $this->fixProposicaoWithoutPhysicalFile($proposicao);
                }
            }
        }

        if ($dryRun) {
            $this->warn("\n⚠️  Executando em modo DRY-RUN. Use sem --dry-run para aplicar correções.");
        } else {
            $this->info("\n✅ Correções aplicadas!");
        }

        return 0;
    }

    private function findPhysicalFiles(int $proposicaoId): array
    {
        $padroes = [
            "proposicoes/proposicao_{$proposicaoId}_*.rtf",
            "proposicoes/proposicao_{$proposicaoId}_*.docx",
            "private/proposicoes/proposicao_{$proposicaoId}_*.rtf", 
            "private/proposicoes/proposicao_{$proposicaoId}_*.docx",
        ];

        $arquivosEncontrados = [];
        
        foreach ($padroes as $padrao) {
            $arquivos = glob(storage_path('app/' . $padrao));
            
            foreach ($arquivos as $arquivo) {
                $path = str_replace(storage_path('app/'), '', $arquivo);
                $arquivosEncontrados[] = $path;
            }
        }

        return array_unique($arquivosEncontrados);
    }

    private function fixProposicaoWithPhysicalFiles(Proposicao $proposicao, array $arquivos): void
    {
        // Escolher o arquivo mais recente
        $arquivoMaisRecente = null;
        $timestampMaisRecente = 0;

        foreach ($arquivos as $arquivo) {
            $caminhoCompleto = storage_path('app/' . $arquivo);
            $timestamp = filemtime($caminhoCompleto);
            
            if ($timestamp > $timestampMaisRecente) {
                $timestampMaisRecente = $timestamp;
                $arquivoMaisRecente = $arquivo;
            }
        }

        if ($arquivoMaisRecente) {
            // Verificar se o arquivo contém variáveis não substituídas
            $caminhoCompleto = storage_path('app/' . $arquivoMaisRecente);
            $temVariaveisNaoSubstituidas = $this->checkForUnreplacedVariables($caminhoCompleto);

            if ($temVariaveisNaoSubstituidas) {
                $this->line("   🔧 Arquivo contém variáveis não substituídas, reprocessando...");
                $this->reprocessProposicaoWithTemplate($proposicao);
            } else {
                $this->line("   ✅ Arquivo OK, apenas atualizando arquivo_path...");
                $proposicao->arquivo_path = $arquivoMaisRecente;
                $proposicao->save();
            }
        }
    }

    private function fixProposicaoWithoutPhysicalFile(Proposicao $proposicao): void
    {
        $this->line("   🔧 Gerando novo arquivo com template universal...");
        $this->reprocessProposicaoWithTemplate($proposicao);
    }

    private function reprocessProposicaoWithTemplate(Proposicao $proposicao): void
    {
        try {
            $conteudo = $this->templateUniversalService->aplicarTemplateParaProposicao($proposicao);
            
            $novoArquivo = "proposicoes/proposicao_{$proposicao->id}_" . time() . '.rtf';
            $caminhoCompleto = storage_path('app/' . $novoArquivo);
            
            $diretorio = dirname($caminhoCompleto);
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            file_put_contents($caminhoCompleto, $conteudo);
            
            $proposicao->arquivo_path = $novoArquivo;
            $proposicao->save();
            
            $this->line("   ✅ Novo arquivo criado: {$novoArquivo}");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Erro ao reprocessar proposição {$proposicao->id}: " . $e->getMessage());
        }
    }

    private function checkForUnreplacedVariables(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if ($extension === 'docx') {
            return $this->checkDocxForVariables($filePath);
        } elseif ($extension === 'rtf') {
            return $this->checkRtfForVariables($filePath);
        }
        
        return false;
    }

    private function checkDocxForVariables(string $filePath): bool
    {
        if (!class_exists('ZipArchive')) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return false;
        }

        $xmlContent = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xmlContent) {
            return false;
        }

        $texto = strip_tags($xmlContent);
        $texto = html_entity_decode($texto);
        
        return preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $texto) === 1;
    }

    private function checkRtfForVariables(string $filePath): bool
    {
        $conteudo = file_get_contents($filePath);
        return preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $conteudo) === 1;
    }
}