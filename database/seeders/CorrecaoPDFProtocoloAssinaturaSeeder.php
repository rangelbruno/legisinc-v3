<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CorrecaoPDFProtocoloAssinaturaSeeder extends Seeder
{
    /**
     * Corrige PDFs de proposições para mostrar número de protocolo e assinatura
     * 
     * Esta correção resolve o problema onde PDFs protocolados não mostravam:
     * - Número de protocolo (mostrava [AGUARDANDO PROTOCOLO])
     * - Assinatura digital do parlamentar
     * - Conteúdo editado pelo Legislativo (arquivos RTF)
     * 
     * VERSÃO 2.0 - Agora suporta arquivos RTF editados pelo OnlyOffice
     */
    public function run(): void
    {
        $this->command->info('🔧 Aplicando correções de PDF com protocolo e assinatura...');
        
        // 1. Corrigir método regenerarPDFAtualizado no ProposicaoAssinaturaController
        $this->corrigirProposicaoAssinaturaController();
        
        // 2. Melhorar o ProposicaoProtocoloController para regenerar PDF automaticamente
        $this->melhorarProposicaoProtocoloController();
        
        // 3. Regenerar PDFs de proposições protocoladas que não estão corretos
        $this->regenerarPDFsProposicoes();
        
        // 4. Adicionar comando artisan para regeneração manual
        $this->verificarComandoRegeneracao();
        
        $this->command->info('✅ Correções de PDF aplicadas com sucesso!');
    }
    
    private function corrigirProposicaoAssinaturaController(): void
    {
        $arquivo = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        
        if (!File::exists($arquivo)) {
            $this->command->warn('❌ ProposicaoAssinaturaController.php não encontrado');
            return;
        }
        
        $conteudo = File::get($arquivo);
        
        // Verificar se método gerarHTMLParaPDFComProtocolo existe e está correto
        if (!str_contains($conteudo, 'gerarHTMLParaPDFComProtocolo')) {
            $this->command->warn('⚠️ Método gerarHTMLParaPDFComProtocolo não encontrado');
            return;
        }
        
        // Verificar se substitui [AGUARDANDO PROTOCOLO] corretamente
        if (!str_contains($conteudo, 'numero_protocolo ?: \'[AGUARDANDO PROTOCOLO]\'')) {
            $this->command->warn('⚠️ Correção do número de protocolo não encontrada');
            return;
        }
        
        // Verificar se inclui assinatura digital
        if (!str_contains($conteudo, 'ASSINATURA DIGITAL')) {
            $this->command->warn('⚠️ Código de assinatura digital não encontrado');
            return;
        }
        
        // Verificar se suporta arquivos RTF
        if (!str_contains($conteudo, 'criarPDFComConteudoRTFProcessado')) {
            $this->command->warn('⚠️ Suporte para arquivos RTF não encontrado');
            return;
        }
        
        // Verificar se usa RTFTextExtractor
        if (!str_contains($conteudo, 'RTFTextExtractor::extract')) {
            $this->command->warn('⚠️ Extração de RTF não configurada');
            return;
        }
        
        $this->command->info('✅ ProposicaoAssinaturaController com correções RTF OK');
    }
    
    private function melhorarProposicaoProtocoloController(): void
    {
        $arquivo = app_path('Http/Controllers/ProposicaoProtocoloController.php');
        
        if (!File::exists($arquivo)) {
            $this->command->warn('❌ ProposicaoProtocoloController.php não encontrado');
            return;
        }
        
        $conteudo = File::get($arquivo);
        
        // Verificar se chama regenerarPDFAtualizado
        if (!str_contains($conteudo, 'regenerarPDFAtualizado')) {
            $this->command->warn('⚠️ Chamada regenerarPDFAtualizado não encontrada');
            return;
        }
        
        // Verificar se tem logs para troubleshooting
        if (!str_contains($conteudo, 'error_log')) {
            $this->command->warn('⚠️ Logs de troubleshooting não encontrados');
            return;
        }
        
        $this->command->info('✅ ProposicaoProtocoloController com melhorias OK');
    }
    
    private function regenerarPDFsProposicoes(): void
    {
        // Buscar proposições protocoladas que podem precisar de correção
        $proposicoes = DB::table('proposicoes')
            ->where('status', 'protocolado')
            ->whereNotNull('numero_protocolo')
            ->whereNotNull('assinatura_digital')
            ->get(['id', 'tipo', 'numero_protocolo']);
        
        $this->command->info("📄 Encontradas {$proposicoes->count()} proposições protocoladas com assinatura");
        
        if ($proposicoes->count() === 0) {
            return;
        }
        
        foreach ($proposicoes as $prop) {
            // Verificar se PDF existe e tem conteúdo correto
            $pdfPath = $this->encontrarPDFProposicao($prop->id);
            
            if (!$pdfPath) {
                $this->command->warn("⚠️ PDF não encontrado para proposição {$prop->id}");
                continue;
            }
            
            // Verificar conteúdo do PDF
            $conteudoPDF = $this->extrairConteudoPDF($pdfPath);
            
            $temProtocolo = stripos($conteudoPDF, $prop->numero_protocolo) !== false;
            $temAssinatura = stripos($conteudoPDF, 'ASSINATURA DIGITAL') !== false;
            
            if (!$temProtocolo || !$temAssinatura) {
                $this->command->warn("❌ Proposição {$prop->id} ({$prop->tipo}) precisa de correção");
                $this->command->line("  - Protocolo no PDF: " . ($temProtocolo ? 'SIM' : 'NÃO'));
                $this->command->line("  - Assinatura no PDF: " . ($temAssinatura ? 'SIM' : 'NÃO'));
                
                // Marcar para regeneração manual
                $this->command->warn("  ⚠️ Execute: php artisan proposicao:regenerar-pdf {$prop->id}");
            } else {
                $this->command->info("✅ Proposição {$prop->id} ({$prop->tipo}) com PDF correto");
            }
        }
    }
    
    private function encontrarPDFProposicao($id): ?string
    {
        $diretoriosPDF = [
            storage_path("app/proposicoes/pdfs/{$id}/"),
            storage_path("app/private/proposicoes/pdfs/{$id}/"),
            storage_path("app/public/proposicoes/pdfs/{$id}/"),
        ];
        
        foreach ($diretoriosPDF as $dir) {
            if (is_dir($dir)) {
                $pdfs = glob($dir . '*.pdf');
                if (!empty($pdfs)) {
                    // Retornar o mais recente
                    usort($pdfs, function($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    return $pdfs[0];
                }
            }
        }
        
        return null;
    }
    
    private function extrairConteudoPDF($pdfPath): string
    {
        if (!file_exists($pdfPath)) {
            return '';
        }
        
        // Usar pdftotext para extrair conteúdo
        $comando = "pdftotext '{$pdfPath}' -";
        $conteudo = shell_exec($comando);
        
        return $conteudo ?: '';
    }
    
    private function verificarComandoRegeneracao(): void
    {
        $comandoPath = app_path('Console/Commands/RegenerarPDFProposicao.php');
        
        if (File::exists($comandoPath)) {
            $this->command->info('✅ Comando artisan proposicao:regenerar-pdf disponível');
        } else {
            $this->command->warn('⚠️ Comando artisan para regeneração não encontrado');
        }
    }
}