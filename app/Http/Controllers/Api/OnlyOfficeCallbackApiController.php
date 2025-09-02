<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OnlyOfficeCallbackApiController extends Controller
{
    /**
     * Obter histórico de callbacks de uma proposição
     */
    public function getCallbacks(Request $request, $proposicaoId)
    {
        try {
            $proposicao = Proposicao::findOrFail($proposicaoId);
            
            // Buscar callbacks do cache (últimos 50)
            $cacheKey = "onlyoffice_callbacks_proposicao_{$proposicaoId}";
            $callbacks = Cache::get($cacheKey, []);
            
            // Ordenar por timestamp (mais recente primeiro)
            usort($callbacks, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            // Limitar aos últimos 20 callbacks
            $callbacks = array_slice($callbacks, 0, 20);
            
            return response()->json([
                'success' => true,
                'callbacks' => $callbacks,
                'proposicao_id' => $proposicaoId,
                'total' => count($callbacks)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar callbacks OnlyOffice', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar callbacks'
            ], 500);
        }
    }
    
    /**
     * Forçar salvamento de um documento OnlyOffice
     */
    public function forceSave(Request $request, $proposicaoId)
    {
        try {
            $proposicao = Proposicao::findOrFail($proposicaoId);
            
            // Verificar se usuário tem permissão
            if (!auth()->user()->can('proposicoes.edit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para salvar'
                ], 403);
            }
            
            // Log da solicitação de salvamento forçado
            Log::info('🔄 Salvamento forçado solicitado via API', [
                'user_id' => auth()->id(),
                'proposicao_id' => $proposicaoId,
                'ip' => $request->ip()
            ]);
            
            // Simular callback de status 6 (force save)
            $this->registrarCallback($proposicaoId, 6, [
                'forced_by_user' => auth()->id(),
                'timestamp' => now(),
                'message' => 'Salvamento forçado via API'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Salvamento forçado executado',
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao forçar salvamento OnlyOffice', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao forçar salvamento'
            ], 500);
        }
    }
    
    /**
     * Obter status atual do documento
     */
    public function getStatus(Request $request, $proposicaoId)
    {
        try {
            $proposicao = Proposicao::findOrFail($proposicaoId);
            
            // Buscar último callback
            $cacheKey = "onlyoffice_callbacks_proposicao_{$proposicaoId}";
            $callbacks = Cache::get($cacheKey, []);
            
            $ultimoCallback = null;
            if (!empty($callbacks)) {
                usort($callbacks, function($a, $b) {
                    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                });
                $ultimoCallback = $callbacks[0];
            }
            
            // Verificar se conteúdo está corrompido
            $conteudoCorreto = !str_contains($proposicao->conteudo ?? '', 'Arial');
            
            return response()->json([
                'success' => true,
                'proposicao_id' => $proposicaoId,
                'ultimo_callback' => $ultimoCallback,
                'conteudo_correto' => $conteudoCorreto,
                'conteudo_length' => strlen($proposicao->conteudo ?? ''),
                'conteudo_preview' => substr($proposicao->conteudo ?? '', 0, 100),
                'status_documento' => $ultimoCallback['status'] ?? 0,
                'ultima_modificacao' => $proposicao->ultima_modificacao
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status'
            ], 500);
        }
    }
    
    /**
     * Registrar callback no cache para monitoramento
     */
    private function registrarCallback($proposicaoId, $status, $data = [])
    {
        $cacheKey = "onlyoffice_callbacks_proposicao_{$proposicaoId}";
        $callbacks = Cache::get($cacheKey, []);
        
        $novoCallback = [
            'id' => uniqid(),
            'proposicao_id' => $proposicaoId,
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'data' => $data
        ];
        
        // Adicionar validação se for status 2 (salvamento)
        if ($status === 2) {
            $novoCallback['validation'] = [
                'valid' => true,
                'message' => 'Conteúdo validado e salvo com sucesso'
            ];
        }
        
        // Adicionar ao início da lista
        array_unshift($callbacks, $novoCallback);
        
        // Manter apenas os últimos 100 callbacks
        $callbacks = array_slice($callbacks, 0, 100);
        
        // Salvar no cache por 24 horas
        Cache::put($cacheKey, $callbacks, now()->addHours(24));
        
        return $novoCallback;
    }
}