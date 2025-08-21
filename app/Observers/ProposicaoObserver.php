<?php

namespace App\Observers;

use App\Models\Proposicao;
use App\Models\TramitacaoLog;
use App\Services\Performance\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProposicaoObserver
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Proposicao "created" event.
     */
    public function created(Proposicao $proposicao): void
    {
        $this->invalidateRelatedCache($proposicao);
        
        Log::info('Proposição criada', [
            'id' => $proposicao->id,
            'autor_id' => $proposicao->autor_id,
            'tipo' => $proposicao->tipo
        ]);

        // Registrar criação no histórico de tramitação
        try {
            TramitacaoLog::criarLog(
                $proposicao->id,
                'CRIADO',
                $proposicao->autor_id,
                null,
                $proposicao->status,
                'Proposição criada pelo parlamentar'
            );
        } catch (\Exception $e) {
            Log::warning('Erro ao registrar criação no histórico: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Proposicao "updated" event.
     */
    public function updated(Proposicao $proposicao): void
    {
        $this->invalidateRelatedCache($proposicao);
        
        // Invalidar cache de PDF se arquivo foi alterado
        if ($proposicao->wasChanged(['arquivo_path', 'conteudo', 'status'])) {
            $this->cacheService->invalidarCachePDF($proposicao->id);
        }

        // Log mudanças importantes
        $changes = $proposicao->getChanges();
        if (isset($changes['status'])) {
            $statusAnterior = $proposicao->getOriginal('status');
            $statusNovo = $changes['status'];
            
            Log::info('Status da proposição alterado', [
                'id' => $proposicao->id,
                'status_anterior' => $statusAnterior,
                'status_novo' => $statusNovo
            ]);

            // Registrar mudança de status no histórico de tramitação
            try {
                $acao = $this->mapearStatusParaAcao($statusAnterior, $statusNovo);
                $observacoes = $this->gerarObservacaoMudancaStatus($statusAnterior, $statusNovo);
                
                if ($acao) {
                    TramitacaoLog::criarLog(
                        $proposicao->id,
                        $acao,
                        Auth::id() ?: $proposicao->autor_id,
                        $statusAnterior,
                        $statusNovo,
                        $observacoes
                    );
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao registrar mudança de status no histórico: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Proposicao "deleted" event.
     */
    public function deleted(Proposicao $proposicao): void
    {
        $this->invalidateRelatedCache($proposicao);
        
        // Limpar arquivos relacionados
        $this->cleanupFiles($proposicao);
        
        Log::info('Proposição excluída', [
            'id' => $proposicao->id,
            'autor_id' => $proposicao->autor_id
        ]);
    }

    /**
     * Invalidar cache relacionado à proposição
     */
    private function invalidateRelatedCache(Proposicao $proposicao): void
    {
        try {
            $this->cacheService->invalidarCacheProposicao(
                $proposicao->id,
                $proposicao->autor_id
            );
        } catch (\Exception $e) {
            Log::warning('Erro ao invalidar cache: ' . $e->getMessage());
        }
    }

    /**
     * Limpar arquivos relacionados à proposição excluída
     */
    private function cleanupFiles(Proposicao $proposicao): void
    {
        try {
            // Limpar PDF
            if ($proposicao->arquivo_pdf_path) {
                $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
            }

            // Limpar diretório de PDFs se vazio
            $pdfDir = storage_path('app/proposicoes/pdfs/' . $proposicao->id);
            if (is_dir($pdfDir) && count(scandir($pdfDir)) === 2) { // apenas . e ..
                rmdir($pdfDir);
            }

            // Limpar arquivo DOCX se for temporário
            if ($proposicao->arquivo_path && str_contains($proposicao->arquivo_path, '_temp_')) {
                $docxPath = storage_path('app/' . $proposicao->arquivo_path);
                if (file_exists($docxPath)) {
                    unlink($docxPath);
                }
            }

        } catch (\Exception $e) {
            Log::warning('Erro ao limpar arquivos da proposição: ' . $e->getMessage());
        }
    }

    /**
     * Mapear mudança de status para ação do histórico
     */
    private function mapearStatusParaAcao(?string $statusAnterior, string $statusNovo): ?string
    {
        // Mapeamento específico baseado no status novo
        return match($statusNovo) {
            'em_edicao' => 'ENVIADO_PARA_REVISAO',
            'enviado_legislativo' => 'ENVIADO_PARA_REVISAO', 
            'aprovado' => 'REVISADO',
            'enviado_protocolo' => 'ASSINADO', // Quando vai para protocolo = foi assinado
            'protocolado' => 'PROTOCOLADO',
            'reprovado', 'rejeitado' => 'REJEITADO',
            default => null
        };
    }

    /**
     * Gerar observação automática para mudança de status
     */
    private function gerarObservacaoMudancaStatus(?string $statusAnterior, string $statusNovo): string
    {
        $user = Auth::user();
        $nomeUsuario = $user ? $user->name : 'Sistema';
        
        return match($statusNovo) {
            'em_edicao' => "Proposição enviada para edição por {$nomeUsuario}",
            'enviado_legislativo' => "Enviado para revisão do Legislativo por {$nomeUsuario}",
            'aprovado' => "Aprovado pelo Legislativo ({$nomeUsuario})",
            'enviado_protocolo' => "Documento assinado digitalmente por {$nomeUsuario} - Aguardando protocolo",
            'protocolado' => "Protocolado pelo setor de Protocolo ({$nomeUsuario})",
            'reprovado' => "Reprovado por {$nomeUsuario}",
            'rejeitado' => "Rejeitado por {$nomeUsuario}",
            default => "Status alterado de '{$statusAnterior}' para '{$statusNovo}' por {$nomeUsuario}"
        };
    }
}