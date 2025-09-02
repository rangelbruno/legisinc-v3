<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OnlyOfficeRealtimeController extends Controller
{
    /**
     * Verificar se houve mudanças no documento desde último acesso
     */
    public function checkDocumentChanges(Request $request, $proposicaoId): JsonResponse
    {
        try {
            $proposicao = Proposicao::find($proposicaoId);
            
            if (!$proposicao) {
                return response()->json(['error' => 'Proposição não encontrada'], 404);
            }
            
            // Obter timestamp atual do arquivo salvo
            $currentTimestamp = $this->getDocumentTimestamp($proposicao);
            
            // Obter timestamp da última verificação do cliente
            $clientTimestamp = $request->input('last_check', 0);
            
            $hasChanges = $currentTimestamp > $clientTimestamp;
            
            if ($hasChanges) {
                Log::info('OnlyOffice Realtime: Mudanças detectadas', [
                    'proposicao_id' => $proposicaoId,
                    'current_timestamp' => $currentTimestamp,
                    'client_timestamp' => $clientTimestamp,
                    'arquivo_path' => $proposicao->arquivo_path
                ]);
            }
            
            return response()->json([
                'has_changes' => $hasChanges,
                'current_timestamp' => $currentTimestamp,
                'last_modified' => $currentTimestamp ? date('Y-m-d H:i:s', $currentTimestamp) : null,
                'arquivo_path' => $proposicao->arquivo_path,
                'needs_refresh' => $hasChanges
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao verificar mudanças no documento', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Erro interno',
                'has_changes' => false,
                'current_timestamp' => time()
            ], 500);
        }
    }
    
    /**
     * Forçar invalidação de cache e regeneração do documento
     */
    public function invalidateDocumentCache(Request $request, $proposicaoId): JsonResponse
    {
        try {
            $proposicao = Proposicao::find($proposicaoId);
            
            if (!$proposicao) {
                return response()->json(['error' => 'Proposição não encontrada'], 404);
            }
            
            // Limpar cache relacionado ao documento
            $cacheKeys = [
                "documento_timestamp_{$proposicaoId}",
                "documento_config_{$proposicaoId}",
                "onlyoffice_key_{$proposicaoId}"
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            
            // Forçar update do timestamp da proposição
            $proposicao->touch(); // Atualiza updated_at
            
            Log::info('OnlyOffice Realtime: Cache invalidado', [
                'proposicao_id' => $proposicaoId,
                'cache_keys_cleared' => $cacheKeys,
                'new_timestamp' => $proposicao->updated_at->timestamp
            ]);
            
            return response()->json([
                'cache_invalidated' => true,
                'new_timestamp' => $proposicao->updated_at->timestamp,
                'message' => 'Cache invalidado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao invalidar cache do documento', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Erro ao invalidar cache'
            ], 500);
        }
    }
    
    /**
     * Obter novo document key após mudanças
     */
    public function getNewDocumentKey(Request $request, $proposicaoId): JsonResponse
    {
        try {
            $proposicao = Proposicao::find($proposicaoId);
            
            if (!$proposicao) {
                return response()->json(['error' => 'Proposição não encontrada'], 404);
            }
            
            // Gerar novo document key baseado no timestamp atual
            $timestamp = time();
            $documentKey = 'realtime_' . $proposicaoId . '_' . $timestamp . '_' . substr(md5($proposicaoId . $timestamp), 0, 8);
            
            // Obter URL atualizada do documento
            $documentUrl = route('proposicoes.onlyoffice.download', [
                'id' => $proposicaoId,
                'token' => base64_encode($proposicaoId . '|' . $timestamp),
                'v' => $timestamp,
                '_' => $timestamp,
                'realtime' => 1 // Flag para identificar requisições realtime
            ]);
            
            // Ajustar para ambiente Docker
            if (config('app.env') === 'local') {
                $documentUrl = str_replace('localhost:8001', 'legisinc-app:80', $documentUrl);
            }
            
            Log::info('OnlyOffice Realtime: Novo document key gerado', [
                'proposicao_id' => $proposicaoId,
                'document_key' => $documentKey,
                'timestamp' => $timestamp
            ]);
            
            return response()->json([
                'document_key' => $documentKey,
                'document_url' => $documentUrl,
                'timestamp' => $timestamp,
                'callback_url' => $this->getCallbackUrl($proposicao, $documentKey)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar novo document key', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Erro ao gerar document key'
            ], 500);
        }
    }
    
    /**
     * Obter timestamp do documento baseado no arquivo físico
     */
    private function getDocumentTimestamp(Proposicao $proposicao): ?int
    {
        if (!$proposicao->arquivo_path) {
            return null;
        }
        
        $caminhosPossiveis = [
            storage_path('app/' . $proposicao->arquivo_path),
            storage_path('app/private/' . $proposicao->arquivo_path),
            storage_path('app/local/' . $proposicao->arquivo_path),
        ];
        
        foreach ($caminhosPossiveis as $caminho) {
            if (file_exists($caminho)) {
                return filemtime($caminho);
            }
        }
        
        return null;
    }
    
    /**
     * Gerar callback URL
     */
    private function getCallbackUrl(Proposicao $proposicao, string $documentKey): string
    {
        $callbackUrl = route('api.onlyoffice.callback.legislativo', [
            'proposicao' => $proposicao,
            'documentKey' => $documentKey
        ]);
        
        if (config('app.env') === 'local') {
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }
        
        return $callbackUrl;
    }
}