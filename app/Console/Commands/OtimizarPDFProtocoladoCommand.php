<?php

namespace App\Console\Commands;

use App\Models\Proposicao;
use App\Services\Performance\PDFProtocoladoOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OtimizarPDFProtocoladoCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'pdf:otimizar-protocolado 
                            {proposicao_id? : ID da proposição específica}
                            {--all : Otimizar todas as proposições protocoladas}
                            {--compare : Comparar tamanhos antes/depois}
                            {--force : Forçar regeneração mesmo se já otimizado}';

    /**
     * The console command description.
     */
    protected $description = 'Otimizar PDFs protocolados com configurações de alta qualidade';

    /**
     * Execute the console command.
     */
    public function handle(PDFProtocoladoOptimizationService $optimizationService): int
    {
        $this->info('🎯 Iniciando otimização de PDFs protocolados...');
        
        try {
            if ($this->option('all')) {
                $this->otimizarTodasProposicoes($optimizationService);
            } else {
                $proposicaoId = $this->argument('proposicao_id');
                if (!$proposicaoId) {
                    $proposicaoId = $this->ask('Digite o ID da proposição para otimizar:');
                }
                
                $this->otimizarProposicaoEspecifica($optimizationService, $proposicaoId);
            }
            
            $this->info('✅ Otimização concluída com sucesso!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Erro durante a otimização: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Otimizar todas as proposições protocoladas
     */
    private function otimizarTodasProposicoes(PDFProtocoladoOptimizationService $optimizationService): void
    {
        $this->info('🔍 Buscando todas as proposições protocoladas...');
        
        $proposicoes = Proposicao::where('status', 'protocolado')
            ->orWhere('numero_protocolo', '!=', null)
            ->get();
        
        if ($proposicoes->isEmpty()) {
            $this->warn('⚠️ Nenhuma proposição protocolada encontrada');
            return;
        }
        
        $this->info("📋 Encontradas {$proposicoes->count()} proposições protocoladas");
        
        $bar = $this->output->createProgressBar($proposicoes->count());
        $bar->start();
        
        $otimizadas = 0;
        $erros = 0;
        
        foreach ($proposicoes as $proposicao) {
            try {
                $this->otimizarProposicao($optimizationService, $proposicao);
                $otimizadas++;
            } catch (\Exception $e) {
                $erros++;
                $this->newLine();
                $this->error("❌ Erro na proposição {$proposicao->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("📊 Resumo da otimização:");
        $this->info("   ✅ Otimizadas: {$otimizadas}");
        $this->info("   ❌ Erros: {$erros}");
        $this->info("   📋 Total: " . $proposicoes->count());
    }
    
    /**
     * Otimizar proposição específica
     */
    private function otimizarProposicaoEspecifica(PDFProtocoladoOptimizationService $optimizationService, $proposicaoId): void
    {
        $proposicao = Proposicao::find($proposicaoId);
        
        if (!$proposicao) {
            $this->error("❌ Proposição com ID {$proposicaoId} não encontrada");
            return;
        }
        
        $this->info("📋 Otimizando proposição #{$proposicao->id}: {$proposicao->ementa}");
        
        $this->otimizarProposicao($optimizationService, $proposicao);
    }
    
    /**
     * Otimizar uma proposição específica
     */
    private function otimizarProposicao(PDFProtocoladoOptimizationService $optimizationService, Proposicao $proposicao): void
    {
        $this->info("🔧 Processando proposição #{$proposicao->id}...");
        
        // Verificar se já existe PDF otimizado
        if (!$this->option('force')) {
            $pdfExistente = $this->verificarPDFOtimizadoExistente($proposicao);
            if ($pdfExistente) {
                $this->info("ℹ️ PDF já otimizado existe: " . basename($pdfExistente));
                if ($this->option('compare')) {
                    $this->compararTamanhos($proposicao, $pdfExistente);
                }
                return;
            }
        }
        
        // Gerar PDF otimizado
        $startTime = microtime(true);
        $pdfOtimizado = $optimizationService->gerarPDFProtocoladoOtimizado($proposicao);
        $executionTime = microtime(true) - $startTime;
        
        $this->info("✅ PDF otimizado gerado em " . round($executionTime * 1000, 2) . "ms");
        $this->info("📄 Arquivo: " . basename($pdfOtimizado));
        
        // Comparar tamanhos se solicitado
        if ($this->option('compare')) {
            $this->compararTamanhos($proposicao, $pdfOtimizado);
        }
        
        // Exibir informações do PDF otimizado
        $this->exibirInformacoesPDF($pdfOtimizado);
    }
    
    /**
     * Verificar se já existe PDF otimizado
     */
    private function verificarPDFOtimizadoExistente(Proposicao $proposicao): ?string
    {
        $diretorio = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        
        if (!is_dir($diretorio)) {
            return null;
        }
        
        $arquivos = glob($diretorio . '/proposicao_' . $proposicao->id . '_protocolado_otimizado_*.pdf');
        
        if (empty($arquivos)) {
            return null;
        }
        
        // Retornar o mais recente
        usort($arquivos, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        return $arquivos[0];
    }
    
    /**
     * Comparar tamanhos de PDFs
     */
    private function compararTamanhos(Proposicao $proposicao, string $pdfOtimizado): void
    {
        $this->info("📊 Comparando tamanhos de PDFs...");
        
        // Buscar PDF original (não otimizado)
        $diretorio = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        $pdfsOriginais = glob($diretorio . '/proposicao_' . $proposicao->id . '_*.pdf');
        
        // Filtrar apenas PDFs não otimizados
        $pdfsOriginais = array_filter($pdfsOriginais, function($pdf) {
            return !str_contains($pdf, 'otimizado');
        });
        
        if (empty($pdfsOriginais)) {
            $this->warn("⚠️ Nenhum PDF original encontrado para comparação");
            return;
        }
        
        // Usar o PDF original mais recente
        usort($pdfsOriginais, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $pdfOriginal = $pdfsOriginais[0];
        $tamanhoOriginal = filesize($pdfOriginal);
        $tamanhoOtimizado = filesize($pdfOtimizado);
        
        $reducao = round((($tamanhoOriginal - $tamanhoOtimizado) / $tamanhoOriginal) * 100, 2);
        
        $this->info("📋 Comparação de tamanhos:");
        $this->info("   📄 Original: " . $this->formatBytes($tamanhoOriginal) . " (" . basename($pdfOriginal) . ")");
        $this->info("   🎯 Otimizado: " . $this->formatBytes($tamanhoOtimizado) . " (" . basename($pdfOtimizado) . ")");
        
        if ($tamanhoOtimizado < $tamanhoOriginal) {
            $this->info("   📉 Redução: {$reducao}%");
        } elseif ($tamanhoOtimizado > $tamanhoOriginal) {
            $this->warn("   📈 Aumento: " . abs($reducao) . "% (qualidade superior)");
        } else {
            $this->info("   ➡️ Mesmo tamanho (qualidade otimizada)");
        }
    }
    
    /**
     * Exibir informações do PDF otimizado
     */
    private function exibirInformacoesPDF(string $pdfPath): void
    {
        if (!file_exists($pdfPath)) {
            $this->warn("⚠️ Arquivo PDF não encontrado");
            return;
        }
        
        $tamanho = filesize($pdfPath);
        $dataModificacao = date('d/m/Y H:i:s', filemtime($pdfPath));
        
        $this->info("📋 Informações do PDF otimizado:");
        $this->info("   📏 Tamanho: " . $this->formatBytes($tamanho));
        $this->info("   📅 Modificado: {$dataModificacao}");
        $this->info("   📁 Caminho: " . $pdfPath);
        
        // Verificar se é um PDF válido
        $content = file_get_contents($pdfPath, false, null, 0, 4);
        if ($content === '%PDF') {
            $this->info("   ✅ Formato: PDF válido");
        } else {
            $this->error("   ❌ Formato: Não é um PDF válido");
        }
    }
    
    /**
     * Formatar bytes para exibição
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
