<?php

namespace App\Services\Performance;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Proposicao;
use App\Models\TipoProposicaoTemplate;
use App\Models\TipoProposicao;

class CacheService
{
    const CACHE_TTL = 3600; // 1 hora
    const CACHE_TTL_TEMPLATES = 7200; // 2 horas (templates mudam menos)
    const CACHE_TTL_LONG = 86400; // 24 horas para dados estáticos

    /**
     * Cache de proposições com relacionamentos
     */
    public function getProposicaoComRelacionamentos(int $id): ?Proposicao
    {
        $cacheKey = "proposicao_full_{$id}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return Proposicao::with([
                'autor:id,name,email,cargo_atual',
                'revisor:id,name,email',
                'tipoProposicao:id,tipo,nome,codigo',
                'template:id,nome,arquivo_path'
            ])->find($id);
        });
    }

    /**
     * Cache de templates por tipo de proposição
     */
    public function getTemplatesPorTipo(int $tipoProposicaoId): \Illuminate\Support\Collection
    {
        $cacheKey = "templates_tipo_{$tipoProposicaoId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL_TEMPLATES, function () use ($tipoProposicaoId) {
            return TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoProposicaoId)
                ->where('ativo', true)
                ->select('id', 'nome', 'arquivo_path', 'document_key', 'tipo_proposicao_id')
                ->get();
        });
    }

    /**
     * Cache de tipos de proposição ativos
     */
    public function getTiposProposicaoAtivos(): \Illuminate\Support\Collection
    {
        $cacheKey = "tipos_proposicao_ativos";
        
        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () {
            return TipoProposicao::where('ativo', true)
                ->select('id', 'tipo', 'nome', 'codigo', 'slug')
                ->orderBy('nome')
                ->get();
        });
    }

    /**
     * Cache de proposições por usuário (dashboard)
     */
    public function getProposicoesPorUsuario(int $userId, int $limit = 10): \Illuminate\Support\Collection
    {
        $cacheKey = "proposicoes_usuario_{$userId}_limit_{$limit}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $limit) {
            return Proposicao::where('autor_id', $userId)
                ->with(['tipoProposicao:id,tipo,nome'])
                ->select('id', 'tipo', 'ementa', 'status', 'created_at', 'autor_id', 'template_id')
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Cache de estatísticas do sistema
     */
    public function getEstatisticasSistema(): array
    {
        $cacheKey = "estatisticas_sistema";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return [
                'total_proposicoes' => Proposicao::count(),
                'proposicoes_pendentes' => Proposicao::whereIn('status', [
                    'rascunho', 'enviado_legislativo', 'aprovado_assinatura'
                ])->count(),
                'proposicoes_assinadas' => Proposicao::where('status', 'assinado')->count(),
                'proposicoes_protocoladas' => Proposicao::where('status', 'protocolado')->count(),
                'tipos_ativos' => TipoProposicao::where('ativo', true)->count(),
                'templates_ativos' => TipoProposicaoTemplate::where('ativo', true)->count(),
            ];
        });
    }

    /**
     * Invalidar cache relacionado a uma proposição
     */
    public function invalidarCacheProposicao(int $proposicaoId, ?int $autorId = null): void
    {
        $keysToForget = [
            "proposicao_full_{$proposicaoId}",
        ];

        if ($autorId) {
            // Invalidar cache de proposições do usuário
            $pattern = "proposicoes_usuario_{$autorId}_*";
            $this->forgetByPattern($pattern);
        }

        // Invalidar estatísticas
        Cache::forget('estatisticas_sistema');

        foreach ($keysToForget as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalidar cache de templates
     */
    public function invalidarCacheTemplates(?int $tipoProposicaoId = null): void
    {
        if ($tipoProposicaoId) {
            Cache::forget("templates_tipo_{$tipoProposicaoId}");
        } else {
            // Invalidar todos os templates
            $this->forgetByPattern('templates_tipo_*');
        }
        
        Cache::forget('tipos_proposicao_ativos');
    }

    /**
     * Limpar cache por padrão (usando Redis)
     */
    private function forgetByPattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                $keys = $redis->keys($pattern);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (\Exception $e) {
            // Fallback silencioso se Redis não estiver disponível
            \Log::warning('Erro ao limpar cache por padrão: ' . $e->getMessage());
        }
    }

    /**
     * Cache de arquivo PDF (evitar regeneração desnecessária)
     */
    public function getPDFPath(int $proposicaoId): ?string
    {
        $cacheKey = "pdf_path_{$proposicaoId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($proposicaoId) {
            $proposicao = Proposicao::select('arquivo_pdf_path', 'updated_at')
                ->find($proposicaoId);
                
            return $proposicao?->arquivo_pdf_path;
        });
    }

    /**
     * Invalidar cache de PDF
     */
    public function invalidarCachePDF(int $proposicaoId): void
    {
        Cache::forget("pdf_path_{$proposicaoId}");
    }

    /**
     * Warmup de cache (pré-carregar dados importantes)
     */
    public function warmupCache(): void
    {
        // Carregar tipos de proposição ativos
        $this->getTiposProposicaoAtivos();
        
        // Carregar estatísticas
        $this->getEstatisticasSistema();
        
        // Carregar templates dos tipos mais usados
        $tiposPopulares = Proposicao::selectRaw('template_id, COUNT(*) as total')
            ->whereNotNull('template_id')
            ->groupBy('template_id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('template_id');
            
        foreach ($tiposPopulares as $templateId) {
            $template = TipoProposicaoTemplate::find($templateId);
            if ($template) {
                $this->getTemplatesPorTipo($template->tipo_proposicao_id);
            }
        }
    }

    /**
     * Flush completo do cache
     */
    public function flushAll(): void
    {
        Cache::flush();
    }
}