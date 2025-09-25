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

            Log::info('📐 PERFIL: Dimensões da página detectadas', [
                'page_size' => "{$pageWidth}×{$pageHeight}pt",
                'orientation' => $pageWidth > $pageHeight ? 'landscape' : 'portrait'
            ]);

            // 4. Calcular coordenadas baseado no tamanho da página
            $coords = $this->calculateCoordinates($profile, $pageWidth, $pageHeight);

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
                'profile_id' => $profileId
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
     * Obter o PDF original mais recente
     */
    private function obterPDFOriginal(Proposicao $proposicao): ?string
    {
        // Tentar diferentes fontes de PDF em ordem de prioridade

        // 1. PDF gerado pelo OnlyOffice (mais recente)
        $pdfOnlyOffice = $this->buscarPDFOnlyOffice($proposicao);
        if ($pdfOnlyOffice && file_exists($pdfOnlyOffice)) {
            Log::info('PDFAssinaturaIntegrada: Usando PDF do OnlyOffice', ['path' => $pdfOnlyOffice]);
            return $pdfOnlyOffice;
        }

        // 2. PDF de assinatura existente
        $pdfAssinatura = $this->buscarPDFAssinatura($proposicao);
        if ($pdfAssinatura && file_exists($pdfAssinatura)) {
            Log::info('PDFAssinaturaIntegrada: Usando PDF de assinatura existente', ['path' => $pdfAssinatura]);
            return $pdfAssinatura;
        }

        // 3. PDF armazenado no campo arquivo_pdf_path
        if ($proposicao->arquivo_pdf_path) {
            $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($pdfPath)) {
                Log::info('PDFAssinaturaIntegrada: Usando PDF do campo arquivo_pdf_path', ['path' => $pdfPath]);
                return $pdfPath;
            }
        }

        Log::warning('PDFAssinaturaIntegrada: Nenhum PDF original encontrado', [
            'proposicao_id' => $proposicao->id
        ]);
        return null;
    }

    /**
     * Buscar PDF mais recente gerado pelo OnlyOffice
     */
    private function buscarPDFOnlyOffice(Proposicao $proposicao): ?string
    {
        $directories = [
            storage_path("app/private/proposicoes/pdfs/{$proposicao->id}"),
            storage_path("app/proposicoes/pdfs/{$proposicao->id}"),
            storage_path("app/private/proposicoes/{$proposicao->id}"),
            storage_path("app/public/proposicoes"),
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) continue;

            $pdfs = glob($dir . '/*.pdf');
            if (empty($pdfs)) continue;

            // Ordenar por data de modificação (mais recente primeiro)
            usort($pdfs, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            foreach ($pdfs as $pdf) {
                $filesize = filesize($pdf);
                if ($filesize > 1000) { // PDF válido (> 1KB)
                    return $pdf;
                }
            }
        }

        return null;
    }

    /**
     * Buscar PDF de assinatura existente
     */
    private function buscarPDFAssinatura(Proposicao $proposicao): ?string
    {
        $pattern = storage_path("app/proposicoes/pdfs/{$proposicao->id}/proposicao_{$proposicao->id}_assinatura_*.pdf");
        $pdfs = glob($pattern);

        if (!empty($pdfs)) {
            // Retornar o mais recente
            usort($pdfs, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            return $pdfs[0];
        }

        return null;
    }

    /**
     * Criar PDF modificado com assinatura integrada
     */
    private function criarPDFModificado(string $pdfOriginal, array $opcoes = []): string
    {
        // Configurar FPDI
        $this->fpdi->setSourceFile($pdfOriginal);
        $totalPaginas = $this->fpdi->setSourceFile($pdfOriginal);

        Log::info('PDFAssinaturaIntegrada: Processando PDF', [
            'total_paginas' => $totalPaginas,
            'tamanho_original' => filesize($pdfOriginal)
        ]);

        // Copiar todas as páginas do PDF original E adicionar assinatura na última página
        for ($i = 1; $i <= $totalPaginas; $i++) {
            // Importar página original
            $templateId = $this->fpdi->importPage($i);
            $size = $this->fpdi->getTemplateSize($templateId);

            // Adicionar nova página com mesmo tamanho
            $this->fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);

            // Usar template da página original
            $this->fpdi->useTemplate($templateId);

            // Se é a última página E tem assinatura digital, adicionar elementos embebidos
            if ($i === $totalPaginas && $this->proposicao->assinatura_digital) {
                $this->adicionarAssinaturaEmbebidaNaPagina($size, $opcoes);
            }
        }

        // Salvar PDF modificado
        $nomeArquivo = "proposicao_{$this->proposicao->id}_integrado_" . time() . '.pdf';
        $caminhoDestino = storage_path("app/private/proposicoes/pdfs/{$this->proposicao->id}/{$nomeArquivo}");

        // Criar diretório se não existir
        $diretorio = dirname($caminhoDestino);
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $this->fpdi->Output('F', $caminhoDestino);

        return $caminhoDestino;
    }

    /**
     * Adicionar assinatura embebida diretamente na página existente
     */
    private function adicionarAssinaturaEmbebidaNaPagina(array $size, array $opcoes = []): void
    {
        $incluirQR = $opcoes['incluir_qr'] ?? true;
        $incluirCertificado = $opcoes['incluir_certificado'] ?? true;

        // Obter dimensões da página
        $pageWidth = $size['width'];
        $pageHeight = $size['height'];

        // Definir área para assinatura
        $boxWidth = 80;  // mm
        $boxHeight = 50; // mm
        $margin = 10;    // mm

        // Usar posição customizada se fornecida, senão usar canto inferior direito
        if (isset($opcoes['custom_position']) && $opcoes['custom_position']['use_custom_position']) {
            $customPos = $opcoes['custom_position'];

            // Converter percentuais para coordenadas reais do PDF
            $boxX = ($customPos['x_percent'] / 100.0) * $pageWidth;
            $boxY = ($customPos['y_percent'] / 100.0) * $pageHeight;

            // Ajustar para não sair das bordas da página
            $boxX = max($margin, min($boxX, $pageWidth - $boxWidth - $margin));
            $boxY = max($margin, min($boxY, $pageHeight - $boxHeight - $margin));

            Log::info('PDFAssinaturaIntegrada: Usando posição customizada', [
                'x_percent' => $customPos['x_percent'],
                'y_percent' => $customPos['y_percent'],
                'boxX' => $boxX,
                'boxY' => $boxY,
                'pageWidth' => $pageWidth,
                'pageHeight' => $pageHeight
            ]);
        } else {
            // Posição padrão: canto inferior direito
            $boxX = $pageWidth - $boxWidth - $margin;
            $boxY = $pageHeight - $boxHeight - $margin;

            Log::info('PDFAssinaturaIntegrada: Usando posição padrão', [
                'boxX' => $boxX,
                'boxY' => $boxY
            ]);
        }

        // Desenhar caixa de fundo semitransparente
        $this->fpdi->SetFillColor(248, 249, 250); // Cinza muito claro
        $this->fpdi->SetDrawColor(40, 167, 69);   // Verde
        $this->fpdi->SetLineWidth(0.5);
        $this->fpdi->Rect($boxX, $boxY, $boxWidth, $boxHeight, 'DF');

        // Título da assinatura
        $this->fpdi->SetFont('Courier', 'B', 8);
        $this->fpdi->SetTextColor(40, 167, 69); // Verde
        $this->fpdi->SetXY($boxX + 2, $boxY + 2);
        $this->fpdi->Cell($boxWidth - 4, 5, 'ASSINATURA DIGITAL', 0, 1, 'C');

        // Linha separadora
        $y = $boxY + 8;
        $this->fpdi->Line($boxX + 5, $y, $boxX + $boxWidth - 5, $y);

        // Informações da assinatura (compactas)
        $y += 3;
        $this->fpdi->SetFont('Courier', '', 7);
        $this->fpdi->SetTextColor(0, 0, 0);

        // Nome do assinante
        $y += 5;
        $this->fpdi->SetXY($boxX + 2, $y);
        $this->fpdi->SetFont('Courier', 'B', 7);
        $this->fpdi->Cell(20, 4, 'Assinado:', 0, 0);
        $this->fpdi->SetFont('Courier', '', 7);
        $nomeAutor = $this->proposicao->autor->name ?? 'N/A';
        $this->fpdi->Cell($boxWidth - 22, 4, substr($nomeAutor, 0, 25), 0, 1);

        // Data da assinatura
        $y += 5;
        $this->fpdi->SetXY($boxX + 2, $y);
        $this->fpdi->SetFont('Courier', 'B', 7);
        $this->fpdi->Cell(20, 4, 'Data:', 0, 0);
        $this->fpdi->SetFont('Courier', '', 7);
        $dataAssinatura = $this->proposicao->data_assinatura
            ? $this->proposicao->data_assinatura->format('d/m/Y H:i')
            : date('d/m/Y H:i');
        $this->fpdi->Cell($boxWidth - 22, 4, $dataAssinatura, 0, 1);

        // Identificador compacto
        $y += 5;
        $this->fpdi->SetXY($boxX + 2, $y);
        $this->fpdi->SetFont('Courier', 'B', 7);
        $this->fpdi->Cell(20, 4, 'ID:', 0, 0);
        $this->fpdi->SetFont('Courier', '', 7);
        $identificador = $this->gerarIdentificadorCompacto();
        $this->fpdi->Cell($boxWidth - 22, 4, $identificador, 0, 1);

        // QR Code pequeno (se solicitado)
        if ($incluirQR) {
            $this->adicionarQRCodeEmbebido($boxX + $boxWidth - 20, $boxY + 15);
        }

        // Texto legal compacto
        $y += 8;
        $this->fpdi->SetXY($boxX + 2, $y);
        $this->fpdi->SetFont('Courier', 'I', 6);
        $this->fpdi->SetTextColor(100, 100, 100);
        $this->fpdi->Cell($boxWidth - 4, 3, 'Assinado conforme Lei 14.063/2020', 0, 1, 'C');

        // URL de verificação compacta
        $y += 4;
        $this->fpdi->SetXY($boxX + 2, $y);
        $this->fpdi->SetFont('Courier', '', 6);
        $urlVerificacao = parse_url(url("/conferir_assinatura"), PHP_URL_HOST);
        $this->fpdi->Cell($boxWidth - 4, 3, "Verify: {$urlVerificacao}", 0, 1, 'C');
    }

    /**
     * Gerar identificador único para a assinatura
     */
    private function gerarIdentificadorAssinatura(): string
    {
        $id = $this->proposicao->id;
        $timestamp = $this->proposicao->data_assinatura
            ? $this->proposicao->data_assinatura->timestamp
            : time();

        $hash = base64_encode($id . '-' . $timestamp);
        $identificador = strtoupper(substr($hash, 0, 24));

        // Formatar como XXXX-XXXX-XXXX-XXXX-XXXX-XXXX
        return chunk_split($identificador, 4, '-');
    }

    /**
     * Gerar identificador compacto para a assinatura
     */
    private function gerarIdentificadorCompacto(): string
    {
        $id = $this->proposicao->id;
        $timestamp = $this->proposicao->data_assinatura
            ? $this->proposicao->data_assinatura->timestamp
            : time();

        // Gerar hash mais compacto (primeiros 12 caracteres)
        $hash = base64_encode($id . '-' . $timestamp);
        $identificador = strtoupper(substr($hash, 0, 12));

        // Formatar como XXXX-XXXX-XXXX
        return substr($identificador, 0, 4) . '-' . substr($identificador, 4, 4) . '-' . substr($identificador, 8, 4);
    }

    /**
     * Adicionar QR Code embebido pequeno
     */
    private function adicionarQRCodeEmbebido(float $x, float $y): void
    {
        // Desenhar placeholder para QR Code (15x15mm)
        $this->fpdi->SetDrawColor(0, 0, 0);
        $this->fpdi->SetLineWidth(0.3);
        $this->fpdi->Rect($x, $y, 15, 15);

        // Grid interno para simular QR Code
        $this->fpdi->SetDrawColor(200, 200, 200);
        $this->fpdi->SetLineWidth(0.1);

        // Linhas verticais
        for ($i = 1; $i < 15; $i++) {
            $this->fpdi->Line($x + $i, $y, $x + $i, $y + 15);
        }

        // Linhas horizontais
        for ($i = 1; $i < 15; $i++) {
            $this->fpdi->Line($x, $y + $i, $x + 15, $y + $i);
        }

        // Texto "QR" no centro
        $this->fpdi->SetFont('Courier', 'B', 6);
        $this->fpdi->SetTextColor(100, 100, 100);
        $this->fpdi->SetXY($x + 3, $y + 7);
        $this->fpdi->Cell(9, 3, 'QR', 0, 0, 'C');
    }

    /**
     * MÉTODO LEGADO - Adicionar QR Code ao PDF (simulado por enquanto)
     */
    private function adicionarQRCode(int $y): void
    {
        // Por enquanto, adicionar um placeholder para o QR Code
        // TODO: Integrar com biblioteca de QR Code para gerar imagem real

        $this->fpdi->SetXY(140, $y);
        $this->fpdi->SetDrawColor(0, 0, 0);
        $this->fpdi->Rect(140, $y, 25, 25); // Placeholder retângulo

        $this->fpdi->SetXY(140, $y + 27);
        $this->fpdi->SetFont('Courier', 'I', 7);
        $this->fpdi->SetTextColor(100, 100, 100);
        $this->fpdi->Cell(25, 3, 'QR Code', 0, 0, 'C');
        $this->fpdi->SetXY(140, $y + 30);
        $this->fpdi->Cell(25, 3, 'Verificacao', 0, 0, 'C');
    }

    /**
     * Verificar se é possível modificar o PDF
     */
    public function podeModificarPDF(Proposicao $proposicao): bool
    {
        return $this->obterPDFOriginal($proposicao) !== null;
    }

    /**
     * Limpar arquivos PDF antigos para economizar espaço
     */
    public function limparPDFsAntigos(Proposicao $proposicao, int $manterUltimos = 3): void
    {
        $diretorio = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (!is_dir($diretorio)) return;

        $pdfs = glob($diretorio . '/proposicao_*_integrado_*.pdf');
        if (count($pdfs) <= $manterUltimos) return;

        // Ordenar por data (mais antigos primeiro)
        usort($pdfs, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Remover arquivos mais antigos, mantendo apenas os últimos N
        $paraRemover = array_slice($pdfs, 0, count($pdfs) - $manterUltimos);
        foreach ($paraRemover as $arquivo) {
            unlink($arquivo);
            Log::info('PDFAssinaturaIntegrada: PDF antigo removido', ['arquivo' => $arquivo]);
        }
    }
}