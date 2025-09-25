<?php

namespace App\Services;

use App\Models\Proposicao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

/**
 * Serviço de assinatura integrada com layout determinístico
 * Aplica perfis de carimbo padronizados antes da assinatura PAdES
 */
class PDFAssinaturaIntegradaService
{
    protected ESignMCPIntegrationService $mcpService;
    protected array $profiles;
    protected array $globalSettings;

    public function __construct(ESignMCPIntegrationService $mcpService)
    {
        $this->mcpService = $mcpService;
        $this->profiles = config('legisinc_sign_profiles', []);
        $this->globalSettings = $this->profiles['global_settings'] ?? [];
    }

    /**
     * Aplicar perfil de assinatura automático
     *
     * @param string $pdfPath Caminho do PDF original
     * @param string $profileId ID do perfil a aplicar
     * @param array $bindings Dados para preencher templates
     * @return string Caminho do PDF carimbado
     * @throws \Exception Se falhar no processo
     */
    public function aplicarPerfil(string $pdfPath, string $profileId, array $bindings = []): string
    {
        $startTime = microtime(true);

        try {
            Log::info('🎨 PERFIL: Iniciando aplicação de perfil automático', [
                'pdf_path' => basename($pdfPath),
                'profile_id' => $profileId,
                'bindings_keys' => array_keys($bindings)
            ]);

            // 1. Validar entrada
            if (!file_exists($pdfPath)) {
                throw new \Exception("PDF não encontrado: {$pdfPath}");
            }

            if (!isset($this->profiles[$profileId])) {
                throw new \Exception("Perfil não encontrado: {$profileId}");
            }

            $profile = $this->profiles[$profileId];

            // 2. Verificar cache se habilitado
            if ($this->globalSettings['cache_stamped_pdfs'] ?? false) {
                $cacheKey = $this->generateCacheKey($pdfPath, $profileId, $bindings);
                $cachedPath = Cache::get($cacheKey);

                if ($cachedPath && file_exists($cachedPath)) {
                    Log::info('🎯 PERFIL: Usando PDF carimbado do cache', [
                        'cached_path' => basename($cachedPath),
                        'cache_key' => substr($cacheKey, 0, 16) . '...'
                    ]);
                    return $cachedPath;
                }
            }

            // 3. Obter dimensões da página
            [$pageWidth, $pageHeight] = $this->mcpService->getPageSize($pdfPath, 1);
            if (!$pageWidth || !$pageHeight) {
                throw new \Exception('Não foi possível obter dimensões da página');
            }

            $normalizedPage = $this->normalizePageSize($pageWidth, $pageHeight);

            Log::info('📐 PERFIL: Dimensões da página detectadas', [
                'page_size_original' => "{$pageWidth}×{$pageHeight}pt",
                'page_size_normalized' => "{$normalizedPage['target_width']}×{$normalizedPage['target_height']}pt",
                'orientation' => $normalizedPage['orientation']
            ]);

            // 4. Calcular coordenadas com base nas dimensões normalizadas (mantendo A4)
            $coords = $this->calculateCoordinates($profile, $normalizedPage['target_width'], $normalizedPage['target_height']);

            // 5. Processar bindings (substituir placeholders)
            $processedBindings = $this->processBindings($bindings);

            // 6. Construir elementos visuais
            $elements = $this->buildElements($profile, $coords, $processedBindings);

            // 7. Aplicar carimbo via MCP
            $stampedPath = $this->mcpService->carimbarLateral([
                'pdf_path' => $pdfPath,
                'page' => 1,
                'sidebar' => $coords['sidebar'],
                'padding' => $profile['padding_pt'] ?? 16,
                'elements' => $elements,
                'profile_id' => $profileId,
                'target_page' => $normalizedPage
            ]);

            // 8. Salvar no cache se habilitado
            if (isset($cacheKey)) {
                $cacheTtl = ($this->globalSettings['cache_ttl_hours'] ?? 24) * 3600;
                Cache::put($cacheKey, $stampedPath, $cacheTtl);
            }

            // 9. Gerar thumbnail se habilitado
            if ($this->globalSettings['enable_thumbnails'] ?? false) {
                $this->generateThumbnail($stampedPath, $profileId);
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('✅ PERFIL: Perfil aplicado com sucesso', [
                'profile_id' => $profileId,
                'input' => basename($pdfPath),
                'output' => basename($stampedPath),
                'elements_count' => count($elements),
                'duration_ms' => $duration,
                'coords' => $coords
            ]);

            return $stampedPath;

        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('❌ PERFIL: Erro ao aplicar perfil', [
                'profile_id' => $profileId,
                'error' => $e->getMessage(),
                'pdf_path' => basename($pdfPath),
                'duration_ms' => $duration
            ]);

            throw $e;
        }
    }

    /**
     * Detectar perfil automaticamente baseado no tipo de proposição
     */
    public function detectarPerfil(Proposicao $proposicao): string
    {
        $tipoMapping = $this->profiles['type_mapping'] ?? [];
        $tipoNormalizado = strtolower($proposicao->tipo ?? '');

        $profileId = $tipoMapping[$tipoNormalizado] ?? $tipoMapping['default'] ?? 'legisinc_v2_lateral';

        Log::info('🔍 PERFIL: Perfil detectado automaticamente', [
            'proposicao_id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'tipo_normalizado' => $tipoNormalizado,
            'profile_detected' => $profileId
        ]);

        return $profileId;
    }

    /**
     * Gerar bindings automáticos para uma proposição
     */
    public function gerarBindings(Proposicao $proposicao, $usuario): array
    {
        // URL de verificação
        $urlVerificacao = route('proposicoes.verificar.assinatura', [
            'proposicao' => $proposicao->id,
            'uuid' => $proposicao->uuid ?? uniqid()
        ]);

        // URL encurtada (apenas host + path)
        $urlShort = parse_url($urlVerificacao, PHP_URL_HOST) . parse_url($urlVerificacao, PHP_URL_PATH);

        // Data/hora formatada
        $dataHora = now()->timezone('America/Sao_Paulo')->format('d/m/Y H:i:s');

        return [
            'tipo' => strtoupper($proposicao->tipo ?? 'DOCUMENTO'),
            'numero' => $proposicao->numero ?? 'S/N',
            'ano' => $proposicao->ano ?? date('Y'),
            'protocolo' => $proposicao->protocolo ?? 'N/A',
            'data_hora' => $dataHora,
            'signatario' => $usuario->name ?? 'Não informado',
            'url' => $urlVerificacao,
            'url_short' => $urlShort,
            'url_qr' => $urlVerificacao,
            'codigo' => $proposicao->codigo_validacao ?? strtoupper(substr(md5($proposicao->id . time()), 0, 8)),
            'ementa_short' => $this->truncateText($proposicao->ementa ?? '', 100),
            'timestamp' => time(),
            'signature_hash' => 'calculando...'
        ];
    }

    /**
     * Normalizar dimensões de página para trabalhar sempre com A4 em pontos.
     */
    protected function normalizePageSize(float $pageWidth, float $pageHeight): array
    {
        $convertedWidth = $pageWidth;
        $convertedHeight = $pageHeight;

        // Detecta PDFs gerados em milímetros (muito pequenos) e converte para pontos
        if ($pageWidth < 400 || $pageHeight < 400) {
            $factor = 72 / 25.4; // mm -> pt
            $convertedWidth = $pageWidth * $factor;
            $convertedHeight = $pageHeight * $factor;
        }

        $orientation = $convertedWidth >= $convertedHeight ? 'landscape' : 'portrait';

        // Dimensões A4 em pontos
        $a4Width = 595.28;
        $a4Height = 841.89;

        if ($orientation === 'landscape') {
            $targetWidth = $a4Height;
            $targetHeight = $a4Width;
        } else {
            $targetWidth = $a4Width;
            $targetHeight = $a4Height;
        }

        return [
            'original_width' => $convertedWidth,
            'original_height' => $convertedHeight,
            'target_width' => $targetWidth,
            'target_height' => $targetHeight,
            'orientation' => $orientation
        ];
    }

    /**
     * Calcular coordenadas baseado nas dimensões da página
     */
    protected function calculateCoordinates(array $profile, float $pageWidth, float $pageHeight): array
    {
        $sidebarWidth = $profile['sidebar_width_pt'] ?? 120;
        $padding = $profile['padding_pt'] ?? 16;
        $qrSize = $profile['qr_size_pt'] ?? 88;
        $qrMarginBottom = $profile['qr_margin_bottom_pt'] ?? 16;

        // 🔧 CORREÇÃO: Ajustar largura da sidebar para páginas pequenas
        // Se a página for muito pequena (< 400pt de largura), reduzir proporcionalmente
        if ($pageWidth < 400) {
            // Para páginas pequenas, usar máximo 25% da largura
            $maxSidebarWidth = $pageWidth * 0.25;
            $sidebarWidth = min($sidebarWidth, $maxSidebarWidth);

            // Ajustar QR size proporcionalmente
            $qrSize = min($qrSize, $sidebarWidth - ($padding * 2));

            Log::info('📐 PERFIL: Página pequena detectada - ajustando sidebar', [
                'page_width' => $pageWidth,
                'original_sidebar_width' => $profile['sidebar_width_pt'] ?? 120,
                'adjusted_sidebar_width' => $sidebarWidth,
                'adjusted_qr_size' => $qrSize
            ]);
        } elseif ($pageWidth < 500) {
            // Para páginas médias, usar máximo 35% da largura
            $maxSidebarWidth = $pageWidth * 0.35;
            $sidebarWidth = min($sidebarWidth, $maxSidebarWidth);

            Log::info('📐 PERFIL: Página média detectada - ajustando sidebar', [
                'page_width' => $pageWidth,
                'original_sidebar_width' => $profile['sidebar_width_pt'] ?? 120,
                'adjusted_sidebar_width' => $sidebarWidth
            ]);
        }

        // Coordenadas da sidebar dentro da própria página A4 normalizada
        $sidebar = [
            'x' => $pageWidth - $sidebarWidth,
            'y' => 0,
            'w' => $sidebarWidth,
            'h' => $pageHeight
        ];

        // Área interna (com padding)
        $inner = [
            'x' => $sidebar['x'] + $padding,
            'y' => $sidebar['y'] + $padding,
            'w' => $sidebarWidth - ($padding * 2),
            'h' => $pageHeight - ($padding * 2)
        ];

        // QR Code (no topo da faixa lateral)
        $qr = [
            'x' => $inner['x'],
            'y' => $sidebar['y'] + $qrMarginBottom,
            'w' => $qrSize,
            'h' => $qrSize
        ];

        // Área de texto (abaixo do QR)
        $textArea = [
            'x' => $inner['x'],
            'y' => $qr['y'] + $qr['h'] + ($padding / 2),
            'w' => $inner['w'],
            'h' => $inner['h'] - $qr['h'] - ($padding / 2) - $qrMarginBottom
        ];

        return [
            'sidebar' => $sidebar,
            'inner' => $inner,
            'qr' => $qr,
            'text_area' => $textArea
        ];
    }

    /**
     * Construir elementos visuais baseado no perfil
     */
    protected function buildElements(array $profile, array $coords, array $bindings): array
    {
        $elements = [];

        foreach ($profile['blocks'] ?? [] as $block) {
            if ($block['type'] === 'vertical_text') {
                $text = $this->processTemplate($block['template'] ?? '', $bindings);

                // 🔧 CORREÇÃO: Ajustar fonte para sidebars pequenas
                $fontSize = $block['font_size'] ?? 8;
                $sidebarWidth = $coords['sidebar']['w'];

                if ($sidebarWidth < 80) {
                    // Sidebar muito pequena - reduzir fonte
                    $fontSize = max(6, $fontSize - 2);

                    // Truncar texto se necessário
                    if (strlen($text) > 200) {
                        $text = substr($text, 0, 200) . '...';
                    }

                    Log::info('📝 PERFIL: Ajustando texto para sidebar pequena', [
                        'sidebar_width' => $sidebarWidth,
                        'font_size_adjusted' => $fontSize,
                        'text_length' => strlen($text)
                    ]);
                }

                $elements[] = [
                    'type' => 'text',
                    'x' => $coords[$block['area']]['x'],
                    'y' => $coords[$block['area']]['y'],
                    'width' => $coords[$block['area']]['w'],
                    'height' => $coords[$block['area']]['h'],
                    'text' => $text,
                    'font_size' => $fontSize,
                    'font_weight' => $block['font_weight'] ?? 'normal',
                    'color' => $block['color'] ?? '#333333',
                    'text_align' => $block['align'] ?? 'center',
                    'rotation' => $block['rotation'] ?? 90,
                    'word_wrap' => $block['word_wrap'] ?? true,
                    'line_height' => $block['line_height'] ?? 1.2
                ];

            } elseif ($block['type'] === 'qrcode') {
                $qrData = $this->processTemplate($block['value'] ?? '', $bindings);

                $elements[] = [
                    'type' => 'qrcode',
                    'x' => $coords[$block['area']]['x'],
                    'y' => $coords[$block['area']]['y'],
                    'width' => $coords[$block['area']]['w'],
                    'height' => $coords[$block['area']]['h'],
                    'data' => $qrData,
                    'error_correction' => $block['error_correction'] ?? 'M',
                    'border' => $block['border'] ?? 1,
                    'quiet_zone' => $block['quiet_zone'] ?? 2
                ];
            }
        }

        return $elements;
    }

    /**
     * Processar template substituindo placeholders
     */
    protected function processTemplate(string $template, array $bindings): string
    {
        foreach ($bindings as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }

        return $template;
    }

    /**
     * Processar bindings (sanitização, formatação)
     */
    protected function processBindings(array $bindings): array
    {
        $processed = [];

        foreach ($bindings as $key => $value) {
            // Sanitizar para PDF
            $processed[$key] = $this->sanitizeForPDF($value);
        }

        return $processed;
    }

    /**
     * Sanitizar texto para uso em PDF
     */
    protected function sanitizeForPDF($value): string
    {
        if (!is_string($value)) {
            $value = (string) $value;
        }

        // Remover caracteres problemáticos
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Normalizar espaços
        $value = preg_replace('/\s+/', ' ', $value);

        // Truncar se muito longo
        $maxLength = $this->globalSettings['max_text_length'] ?? 500;
        if (strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength - 3) . '...';
        }

        return trim($value);
    }

    /**
     * Truncar texto preservando palavras
     */
    protected function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        $truncated = substr($text, 0, $length);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }

    /**
     * Gerar chave de cache única
     */
    protected function generateCacheKey(string $pdfPath, string $profileId, array $bindings): string
    {
        $pdfHash = hash('sha256', file_get_contents($pdfPath));
        $bindingsHash = hash('sha256', serialize($bindings));

        return "pdf_stamped:{$profileId}:{$pdfHash}:{$bindingsHash}";
    }

    /**
     * Gerar thumbnail do PDF carimbado (opcional)
     */
    protected function generateThumbnail(string $pdfPath, string $profileId): ?string
    {
        try {
            // TODO: Implementar geração de thumbnail
            // Pode usar ImageMagick ou Poppler (pdftoppm)
            Log::info('📸 PERFIL: Thumbnail gerado', [
                'pdf' => basename($pdfPath),
                'profile' => $profileId
            ]);

            return null; // Por enquanto não implementado

        } catch (\Exception $e) {
            Log::warning('⚠️ PERFIL: Falha ao gerar thumbnail', [
                'error' => $e->getMessage(),
                'pdf' => basename($pdfPath)
            ]);
            return null;
        }
    }

    /**
     * Obter configuração PAdES do perfil
     */
    public function getPadesConfig(string $profileId): array
    {
        $profile = $this->profiles[$profileId] ?? [];
        return $profile['pades'] ?? [
            'visible_widget' => false,
            'reason' => 'Assinatura digital',
            'location' => 'Sistema LegisInc'
        ];
    }

    /**
     * Listar perfis disponíveis
     */
    public function getAvailableProfiles(): array
    {
        $profiles = [];

        foreach ($this->profiles as $key => $profile) {
            if (is_array($profile) && isset($profile['id'])) {
                $profiles[$key] = [
                    'id' => $profile['id'],
                    'name' => $profile['name'] ?? $key,
                    'description' => $profile['description'] ?? 'Sem descrição'
                ];
            }
        }

        return $profiles;
    }
}