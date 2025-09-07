<?php

namespace App\Services;

use App\Models\Proposicao;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class PDFServingService
{
    /**
     * Serve PDF baseado no status da proposição (state-aware)
     * 
     * @param Proposicao $proposicao
     * @return Response
     */
    public function servePDF(Proposicao $proposicao): Response
    {
        Log::debug("Servindo PDF state-aware", [
            'proposicao_id' => $proposicao->id,
            'status' => $proposicao->status
        ]);
        
        switch ($proposicao->status) {
            case 'protocolado':
                return $this->serveProtocoladoPDF($proposicao);
                
            case 'assinado':
                return $this->serveAssinadoPDF($proposicao);
                
            case 'aprovado_assinatura':
                return $this->serveParaAssinaturaPDF($proposicao);
                
            case 'em_analise_legislativo':
            case 'rascunho':
            default:
                return $this->servePreviewPDF($proposicao);
        }
    }
    
    /**
     * Serve PDF protocolado (versão final com carimbo)
     */
    private function serveProtocoladoPDF(Proposicao $proposicao): Response
    {
        $path = $proposicao->arquivo_pdf_protocolado;
        
        if (!$path || !Storage::exists($path)) {
            Log::warning("PDF protocolado não encontrado, tentando assinado", [
                'proposicao_id' => $proposicao->id,
                'path' => $path
            ]);
            return $this->serveAssinadoPDF($proposicao);
        }
        
        return $this->serveFile($path, "protocolo-{$proposicao->numero}-{$proposicao->ano}.pdf");
    }
    
    /**
     * Serve PDF assinado
     */
    private function serveAssinadoPDF(Proposicao $proposicao): Response
    {
        // Tentar PDF assinado primeiro, fallback para para_assinatura
        $path = $proposicao->arquivo_pdf_assinado ?: $proposicao->arquivo_pdf_para_assinatura;
        
        if (!$path || !Storage::exists($path)) {
            Log::warning("PDF assinado não encontrado, gerando novo", [
                'proposicao_id' => $proposicao->id,
                'path_assinado' => $proposicao->arquivo_pdf_assinado,
                'path_para_assinatura' => $proposicao->arquivo_pdf_para_assinatura
            ]);
            
            // Tentar gerar PDF para assinatura se não existir
            return $this->serveParaAssinaturaPDF($proposicao);
        }
        
        $filename = $proposicao->arquivo_pdf_assinado 
            ? "assinado-{$proposicao->id}.pdf" 
            : "para-assinatura-{$proposicao->id}.pdf";
            
        return $this->serveFile($path, $filename);
    }
    
    /**
     * Serve PDF para assinatura 
     */
    private function serveParaAssinaturaPDF(Proposicao $proposicao): Response
    {
        $path = $proposicao->arquivo_pdf_para_assinatura;
        
        // Se não existe ou está desatualizado, tentar gerar
        if (!$path || !Storage::exists($path) || $this->pdfDesatualizado($proposicao)) {
            Log::info("PDF para assinatura precisa ser gerado/atualizado", [
                'proposicao_id' => $proposicao->id,
                'path' => $path,
                'desatualizado' => $this->pdfDesatualizado($proposicao)
            ]);
            
            // Dispatch job para gerar PDF (assíncrono)
            dispatch(new \App\Jobs\GerarPDFProposicaoJob($proposicao->id));
            
            // Por enquanto, serve preview
            return $this->servePreviewPDF($proposicao);
        }
        
        return $this->serveFile($path, "para-assinatura-{$proposicao->id}.pdf");
    }
    
    /**
     * Serve PDF de preview com marca d'água
     */
    private function servePreviewPDF(Proposicao $proposicao): Response
    {
        // TODO: Implementar geração de preview com watermark "RASCUNHO"
        // Por enquanto, retorna erro 404 com mensagem explicativa
        
        $message = match($proposicao->status) {
            'rascunho' => 'PDF não disponível para proposições em rascunho. Finalize a edição primeiro.',
            'em_analise_legislativo' => 'PDF será gerado após aprovação do setor legislativo.',
            default => 'PDF não disponível para este status: ' . $proposicao->status
        };
        
        abort(404, $message);
    }
    
    /**
     * Serve arquivo com cabeçalhos corretos e verificações
     */
    private function serveFile(string $path, string $filename): Response
    {
        if (!Storage::exists($path)) {
            Log::error("Arquivo não encontrado no storage", ['path' => $path]);
            abort(404, 'Arquivo PDF não encontrado no sistema');
        }
        
        try {
            $content = Storage::get($path);
            $size = Storage::size($path);
            
            Log::debug("Servindo arquivo PDF", [
                'path' => $path,
                'filename' => $filename,
                'size' => $size
            ]);
            
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => $size,
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN'
            ]);
            
        } catch (Exception $e) {
            Log::error("Erro ao servir arquivo PDF", [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Erro ao carregar arquivo PDF');
        }
    }
    
    /**
     * Verifica se o PDF está desatualizado baseado no hash do conteúdo
     */
    private function pdfDesatualizado(Proposicao $proposicao): bool
    {
        // Se não tem hash do arquivo, assume que está desatualizado
        if (!$proposicao->arquivo_hash) {
            return true;
        }
        
        // Se o hash do PDF é diferente do hash atual do arquivo
        if ($proposicao->arquivo_hash !== $proposicao->pdf_base_hash) {
            return true;
        }
        
        // Se foi modificado após a geração do PDF
        if ($proposicao->conteudo_updated_at && $proposicao->pdf_gerado_em && 
            $proposicao->conteudo_updated_at > $proposicao->pdf_gerado_em) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Obter informações sobre os PDFs da proposição
     */
    public function getStatusPDFs(Proposicao $proposicao): array
    {
        return [
            'para_assinatura' => [
                'path' => $proposicao->arquivo_pdf_para_assinatura,
                'exists' => $proposicao->arquivo_pdf_para_assinatura && Storage::exists($proposicao->arquivo_pdf_para_assinatura),
                'size' => $proposicao->arquivo_pdf_para_assinatura ? Storage::size($proposicao->arquivo_pdf_para_assinatura) : null,
                'desatualizado' => $this->pdfDesatualizado($proposicao)
            ],
            'assinado' => [
                'path' => $proposicao->arquivo_pdf_assinado,
                'exists' => $proposicao->arquivo_pdf_assinado && Storage::exists($proposicao->arquivo_pdf_assinado),
                'size' => $proposicao->arquivo_pdf_assinado ? Storage::size($proposicao->arquivo_pdf_assinado) : null
            ],
            'protocolado' => [
                'path' => $proposicao->arquivo_pdf_protocolado,
                'exists' => $proposicao->arquivo_pdf_protocolado && Storage::exists($proposicao->arquivo_pdf_protocolado),
                'size' => $proposicao->arquivo_pdf_protocolado ? Storage::size($proposicao->arquivo_pdf_protocolado) : null
            ],
            'conversor_usado' => $proposicao->pdf_conversor_usado,
            'gerado_em' => $proposicao->pdf_gerado_em,
            'hash_arquivo' => $proposicao->arquivo_hash,
            'hash_pdf_base' => $proposicao->pdf_base_hash
        ];
    }
}