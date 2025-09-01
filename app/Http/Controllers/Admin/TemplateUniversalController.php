<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TemplateUniversalResource;
use App\Models\TemplateUniversal;
use App\Models\TipoProposicao;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateProcessorService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TemplateUniversalController extends Controller implements HasMiddleware
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService,
        private TemplateProcessorService $templateProcessor
    ) {}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:ADMIN', only: ['index', 'create', 'store', 'destroy']),
            new Middleware('role:ADMIN|LEGISLATIVO', only: ['show', 'editor']),
            new Middleware('can:update,template', only: ['update']),
        ];
    }

    /**
     * Exibir interface do template universal
     */
    public function index()
    {
        $template = TemplateUniversal::getOrCreateDefault();

        // Otimização com eager loading para evitar N+1 queries
        $template->loadMissing(['updatedBy']);

        $tiposProposicao = TipoProposicao::where('ativo', true)->orderBy('nome')->get();

        return view('admin.templates.universal.index', compact('template', 'tiposProposicao'));
    }

    /**
     * Editor do template universal
     */
    public function editor(?TemplateUniversal $template = null)
    {
        // Editor do template universal

        if (! $template) {
            $template = TemplateUniversal::getOrCreateDefault();
        }

        // Otimização com eager loading para evitar N+1 queries
        $template->loadMissing(['updatedBy']);

        // ✅ LÓGICA INTELIGENTE DE DOCUMENT_KEY (baseada no sistema de proposições)
        $documentKey = $this->generateIntelligentDocumentKey($template);

        // Atualizar o document_key apenas se mudou
        if ($template->document_key !== $documentKey) {
            $template->updateQuietly([
                'document_key' => $documentKey,
                'updated_by' => auth()->id(),
            ]);

            Log::info('Template universal document_key atualizado', [
                'template_id' => $template->id,
                'old_key' => $template->document_key,
                'new_key' => $documentKey,
                'user_id' => auth()->id(),
            ]);
        }

        // Processar imagens antes de gerar configuração
        if (empty($template->conteudo)) {
            $conteudoBase = $this->criarTemplateBase();
            $template->update(['conteudo' => $conteudoBase]);
        }

        $conteudoProcessado = $this->processarImagensParaEditor($template->conteudo);
        if ($conteudoProcessado !== $template->conteudo) {
            $template->update(['conteudo' => $conteudoProcessado]);
        }

        // Criar configuração específica para Template Universal
        $config = $this->criarConfiguracaoOnlyOffice($template);

        return view('admin.templates.universal.editor', [
            'template' => $template,
            'config' => $config,
        ]);
    }

    /**
     * Gerar document_key inteligente (Laravel 12 best practices)
     */
    private function generateIntelligentDocumentKey(TemplateUniversal $template): string
    {
        // ✅ ANTI-CACHE: Forçar nova key apenas quando conteúdo muda
        $cacheKey = 'template_universal_doc_key_'.$template->id;
        $currentContentHash = md5($template->conteudo ?? '');

        // Verificar se existe key cacheada com mesmo conteúdo
        $cachedData = Cache::get($cacheKey);
        if ($cachedData && $cachedData['content_hash'] === $currentContentHash) {
            return $cachedData['document_key'];
        }

        // Gerar nova key apenas quando conteúdo mudou
        $timestamp = time();
        $hashSuffix = substr($currentContentHash, 0, 8);
        $newKey = "template_universal_{$template->id}_{$timestamp}_{$hashSuffix}";

        // Cache por 2 horas
        Cache::put($cacheKey, [
            'document_key' => $newKey,
            'content_hash' => $currentContentHash,
            'timestamp' => $timestamp,
        ], 7200);

        return $newKey;
    }

    /**
     * Criar configuração do OnlyOffice para Template Universal
     */
    private function criarConfiguracaoOnlyOffice(TemplateUniversal $template): array
    {
        // ✅ USAR O DOCUMENT_KEY JÁ GERADO (não regenerar)
        $documentKey = $template->document_key;

        // URL para download do documento (usar rota de template universal)
        $documentUrl = route('api.templates.universal.download', ['template' => $template->id]);

        // URL de callback para salvar alterações (usar rota de template universal)
        $callbackUrl = route('api.onlyoffice.template-universal.callback', [
            'template' => $template->id,
            'documentKey' => $documentKey,
        ]);

        // Ajustar URLs para comunicação entre containers
        if (config('app.env') === 'local') {
            $documentUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $documentUrl);
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);

            // URLs ajustadas para comunicação entre containers
        }

        return [
            'document' => [
                'fileType' => 'rtf',
                'key' => $documentKey,
                'title' => $template->nome,
                'url' => $documentUrl,
                'permissions' => [
                    'comment' => true,
                    'copy' => true,
                    'download' => true,
                    'edit' => true,
                    'fillForms' => true,
                    'modifyFilter' => true,
                    'modifyContentControl' => true,
                    'review' => true,
                    'print' => true,
                ],
            ],
            'documentType' => 'word',
            'editorConfig' => [
                'mode' => 'edit',
                'lang' => 'pt-BR',
                'callbackUrl' => $callbackUrl,
                'user' => [
                    'id' => (string) Auth::id(),
                    'name' => Auth::user()->name,
                    'group' => 'Administradores',
                ],
                'customization' => [
                    'autosave' => true,
                    'forcesave' => true,
                    'compactHeader' => true,
                    'feedback' => false,
                    'hideRightMenu' => false,
                    'hideRulers' => false,
                    'toolbarNoTabs' => false,
                    'zoom' => 100,
                    'goback' => false,
                    'chat' => false,
                    'comments' => true,
                    'help' => false,
                    'plugins' => false,
                ],
            ],
        ];
    }

    /**
     * Criar template base com todas as variáveis disponíveis
     */
    private function criarTemplateBase(): string
    {
        // Primeiro, criar o template com variável de imagem
        $conteudoBase = <<<RTF
{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}
\f0\fs24\sl360\slmult1 

\b\fs28 TEMPLATE UNIVERSAL - PROPOSIÇÕES LEGISLATIVAS\b0\fs24\par
\par

\b CABEÇALHO INSTITUCIONAL:\b0\par
$\{imagem_cabecalho\}\par
$\{cabecalho_nome_camara\}\par
$\{cabecalho_endereco\}\par
Tel: $\{cabecalho_telefone\} - $\{cabecalho_website\}\par
CNPJ: $\{cnpj_camara\}\par
\par

\line\par
\par

\qc\b\fs26 $\{tipo_proposicao\} N° $\{numero_proposicao\}\b0\fs24\par
\ql\par

\b EMENTA:\b0 $\{ementa\}\par
\par

\b PREÂMBULO DINÂMICO:\b0\par
[Este campo se adapta automaticamente ao tipo de proposição]\par
\par

\b CONTEÚDO PRINCIPAL:\b0\par
$\{texto\}\par
\par

\b JUSTIFICATIVA:\b0\par
$\{justificativa\}\par
\par

\b ARTICULADO (Para Projetos de Lei):\b0\par
Art. 1° [Disposição principal]\par
\par
Parágrafo único. [Detalhamento se necessário]\par
\par
Art. 2° [Disposições complementares]\par
\par
Art. 3° Esta lei entra em vigor na data de sua publicação.\par
\par

\line\par
\par

\b ÁREA DE ASSINATURA:\b0\par
$\{municipio\}, $\{dia\} de $\{mes_extenso\} de $\{ano_atual\}.\par
\par
$\{assinatura_padrao\}\par
$\{autor_nome\}\par
$\{autor_cargo\}\par
\par

\b RODAPÉ INSTITUCIONAL:\b0\par
$\{rodape_texto\}\par
$\{endereco_camara\}, $\{endereco_bairro\} - CEP: $\{endereco_cep\}\par
$\{municipio\}/$\{municipio_uf\} - Tel: $\{telefone_camara\}\par
$\{website_camara\} - $\{email_camara\}\par

}
RTF;

        // Processar a imagem imediatamente
        return $this->processarImagensParaEditor($conteudoBase);
    }

    /**
     * Download do template universal
     */
    public function download(TemplateUniversal $template)
    {
        $template->refresh();

        if (! $template->ativo) {
            abort(404, 'Template não está ativo');
        }

        $conteudoArquivo = $template->conteudo;

        if (empty($conteudoArquivo)) {
            // Criar conteúdo base se não existir
            $conteudoArquivo = $this->criarTemplateBase();
            $conteudoArquivo = $this->processarImagensParaEditor($conteudoArquivo);
            $template->update(['conteudo' => $conteudoArquivo]);
        }

        // Garantir que o conteúdo seja RTF válido
        $conteudoArquivo = $this->garantirRTFValido($conteudoArquivo);

        // NÃO aplicar converterUtf8ParaRtf em arquivos RTF!
        // RTF já tem seu próprio sistema de codificação

        $formato = 'rtf';
        $contentType = 'application/rtf; charset=utf-8';
        $nomeArquivo = \Illuminate\Support\Str::slug($template->nome).'.rtf';

        // Log para debug
        Log::info('Template universal download', [
            'template_id' => $template->id,
            'formato' => $formato,
            'content_type' => $contentType,
            'nome_arquivo' => $nomeArquivo,
            'tamanho_conteudo' => strlen($conteudoArquivo),
            'primeiros_50_chars' => substr($conteudoArquivo, 0, 50),
        ]);

        // ✅ HEADERS ANTI-CACHE AGRESSIVOS + FORCE REFRESH
        $etag = md5($conteudoArquivo.$template->updated_at);
        $lastModified = gmdate('D, d M Y H:i:s', strtotime($template->updated_at)).' GMT';

        return response($conteudoArquivo, 200, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0, private',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            'Last-Modified' => $lastModified,
            'ETag' => '"'.$etag.'"',
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$nomeArquivo.'"',
            'Content-Length' => strlen($conteudoArquivo),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Vary' => 'Accept-Encoding',
            // Forçar OnlyOffice a sempre baixar nova versão
            'X-OnlyOffice-Force-Refresh' => 'true',
        ]);
    }

    /**
     * Callback do OnlyOffice para template universal
     */
    public function callback(Request $request, string $templateId, string $documentKey)
    {
        try {
            // Ler input raw (padrão OnlyOffice)
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (! $data) {
                return response()->json(['error' => 0]);
            }

            // ✅ BUSCA MAIS ROBUSTA: tenta por document_key E por ID como fallback
            $template = TemplateUniversal::with(['updatedBy'])
                ->where('document_key', $documentKey)
                ->first();

            // Fallback: buscar por ID se não encontrou por document_key
            if (! $template && is_numeric($templateId)) {
                $template = TemplateUniversal::with(['updatedBy'])->find($templateId);
                if ($template) {
                    Log::warning('Template encontrado por ID fallback', [
                        'template_id' => $templateId,
                        'document_key_recebido' => $documentKey,
                        'document_key_atual' => $template->document_key,
                    ]);
                }
            }

            if (! $template) {
                Log::warning('Template não encontrado no callback', [
                    'template_id' => $templateId,
                    'document_key' => $documentKey,
                ]);

                return response()->json(['error' => 0]);
            }

            $status = $data['status'] ?? 0;

            Log::info('Template Universal Callback recebido', [
                'template_id' => $template->id,
                'document_key' => $documentKey,
                'status' => $status,
                'status_description' => $this->getOnlyOfficeStatusDescription($status),
                'has_url' => isset($data['url']),
            ]);

            // ✅ PROCESSAR MÚLTIPLOS STATUS (não apenas 2)
            if ($status == 2) {
                if (isset($data['url'])) {
                    // Substituir localhost pelo nome do container OnlyOffice
                    $url = str_replace('http://localhost:8080', 'http://legisinc-onlyoffice', $data['url']);

                    Log::info('Template Universal - Baixando documento editado', [
                        'template_id' => $template->id,
                        'original_url' => $data['url'],
                        'converted_url' => $url,
                    ]);

                    // Download usando cURL (padrão do sistema)
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                    $fileContent = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    curl_close($ch);

                    if ($fileContent && $httpCode == 200) {
                        // Validação robusta do conteúdo RTF
                        if (! str_starts_with($fileContent, '{\\rtf1')) {
                            Log::warning('Conteúdo recebido não é RTF válido', [
                                'template_id' => $template->id,
                                'first_100_chars' => substr($fileContent, 0, 100),
                            ]);

                            return response()->json(['error' => 0]);
                        }

                        // Sanitizar conteúdo antes de salvar
                        $fileContent = $this->sanitizeRtfContent($fileContent);

                        // Salvar usando updateQuietly para melhor performance (Laravel 12 best practice)
                        $template->updateQuietly([
                            'conteudo' => $fileContent,
                            'updated_by' => auth()->id() ?? 1, // Fallback para sistema
                            'updated_at' => now(),
                            'document_key' => $documentKey, // Manter sincronia
                        ]);

                        // ✅ LIMPAR CACHE após salvamento para forçar refresh
                        Cache::forget('template_universal_doc_key_'.$template->id);
                        Cache::forget('onlyoffice_template_universal_'.$template->id);

                        Log::info('Template universal salvo com sucesso', [
                            'template_id' => $template->id,
                            'document_key' => $documentKey,
                            'file_size' => strlen($fileContent),
                            'is_rtf_valid' => str_starts_with($fileContent, '{\\rtf1'),
                            'preview' => substr($fileContent, 0, 100).'...',
                        ]);

                    } else {
                        Log::error('Erro ao baixar arquivo OnlyOffice', [
                            'template_id' => $template->id,
                            'http_code' => $httpCode,
                            'curl_error' => $curlError,
                            'url' => $url,
                        ]);
                    }
                }
            } elseif (in_array($status, [1, 4])) {
                // Status 1 = Carregando, Status 4 = Fechando sem mudanças
                Log::info('Template Universal - Status informativo', [
                    'template_id' => $template->id,
                    'status' => $status,
                    'description' => $status === 1 ? 'Carregando documento' : 'Fechando sem alterações',
                ]);
            } else {
                Log::info('Template Universal - Status não processado', [
                    'template_id' => $template->id,
                    'status' => $status,
                    'description' => $this->getOnlyOfficeStatusDescription($status),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro geral callback Template Universal', [
                'document_key' => $documentKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['error' => 0]);
    }

    /**
     * Force save para salvamento imediato (baseado em melhores práticas Laravel 12)
     */
    public function forceSave(Request $request, TemplateUniversal $template)
    {
        try {
            // Validação básica sem FormRequest (já que não há auth)
            $request->validate([
                'document_key' => 'sometimes|nullable|string|max:100',
            ]);

            // Usar updateQuietly para performance (Laravel 12 best practice)
            $template->updateQuietly([
                'updated_by' => auth()->id() ?? 1, // Fallback para ID 1 quando não autenticado
                'updated_at' => now(),
                // Atualizar document_key se fornecido
                'document_key' => $request->input('document_key', $template->document_key),
            ]);

            Log::info('Template universal force save executado', [
                'template_id' => $template->id,
                'document_key' => $template->document_key,
                'user_id' => auth()->id() ?? 'anonymous',
                'timestamp' => now()->format('Y-m-d H:i:s.u'),
            ]);

            // Retornar usando API Resource (Laravel 12 best practice)
            return new TemplateUniversalResource($template->fresh());

        } catch (\Exception $e) {
            Log::error('Erro no force save do template universal', [
                'template_id' => $template->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Aplicar template universal para um tipo específico
     */
    public function aplicarParaTipo(Request $request, TemplateUniversal $template, TipoProposicao $tipo)
    {
        $validated = $request->validate([
            'dados_personalizados' => 'array',
            'preview' => 'boolean',
        ]);

        $dadosPersonalizados = $validated['dados_personalizados'] ?? [];
        $preview = $validated['preview'] ?? false;

        // Aplicar template ao tipo
        $conteudoAplicado = $template->aplicarParaTipo($tipo, $dadosPersonalizados);

        if ($preview) {
            return response()->json([
                'success' => true,
                'conteudo' => $conteudoAplicado,
                'tipo' => $tipo->nome,
            ]);
        }

        // Salvar como arquivo temporário para download
        $tempFile = tempnam(sys_get_temp_dir(), 'template_aplicado_');
        file_put_contents($tempFile, $conteudoAplicado);

        $nomeArquivo = \Illuminate\Support\Str::slug($tipo->nome).'_template.'.$template->formato;

        return response()->download($tempFile, $nomeArquivo)->deleteFileAfterSend(true);
    }

    /**
     * Definir como template padrão
     */
    public function setDefault(TemplateUniversal $template)
    {
        $template->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Template definido como padrão do sistema!',
        ]);
    }

    /**
     * Criar novo template universal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $template = TemplateUniversal::create([
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'document_key' => 'template_universal_'.time().'_'.uniqid(),
            'ativo' => true,
            'is_default' => $validated['is_default'] ?? false,
            'updated_by' => auth()->id(),
            'formato' => 'rtf',
        ]);

        if ($template->is_default) {
            $template->setAsDefault();
        }

        return redirect()->route('admin.templates.universal.editor', $template)
            ->with('success', 'Template universal criado com sucesso!');
    }

    /**
     * Processar variáveis de imagem para o editor OnlyOffice
     */
    private function processarImagensParaEditor(string $conteudo): string
    {
        // Verificar se precisa processar imagem
        if (! str_contains($conteudo, '${imagem_cabecalho}')) {
            return $conteudo;
        }

        // Caminho da imagem padrão
        $caminhoImagem = public_path('template/cabecalho.png');

        if (file_exists($caminhoImagem)) {
            // Gerar código RTF para a imagem
            $codigoRTFImagem = $this->gerarCodigoRTFImagem($caminhoImagem);

            // Substituir a variável pela imagem (sem escape no RTF)
            $conteudo = str_replace('${imagem_cabecalho}', $codigoRTFImagem, $conteudo);

            Log::info('Imagem do cabeçalho processada para template universal', [
                'caminho' => $caminhoImagem,
                'tamanho_codigo_rtf' => strlen($codigoRTFImagem),
            ]);
        } else {
            // Se não existe a imagem, remover a variável
            $conteudo = str_replace('${imagem_cabecalho}\\par', '', $conteudo);
            $conteudo = str_replace('${imagem_cabecalho}', '', $conteudo);

            Log::warning('Imagem do cabeçalho não encontrada para template universal', [
                'caminho_esperado' => $caminhoImagem,
            ]);
        }

        return $conteudo;
    }

    /**
     * Obter descrição do status OnlyOffice (Laravel 12 best practices)
     */
    private function getOnlyOfficeStatusDescription(int $status): string
    {
        return match ($status) {
            0 => 'Não definido',
            1 => 'Documento sendo editado',
            2 => 'Documento pronto para salvar',
            3 => 'Erro no salvamento',
            4 => 'Documento fechado sem mudanças',
            6 => 'Documento sendo editado, mas salvo no momento',
            7 => 'Erro ao forçar salvamento',
            default => "Status desconhecido: {$status}",
        };
    }

    /**
     * Gerar código RTF para incorporar imagem
     */
    private function gerarCodigoRTFImagem(string $caminhoImagem): string
    {
        try {
            // Usar o método do TemplateParametrosService que já funciona
            $templateParametros = app(\App\Services\Template\TemplateParametrosService::class);

            // Usar reflection para acessar método privado
            $reflection = new \ReflectionClass($templateParametros);
            $method = $reflection->getMethod('gerarCodigoRTFImagem');
            $method->setAccessible(true);

            return $method->invoke($templateParametros, $caminhoImagem);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar código RTF da imagem', [
                'caminho' => $caminhoImagem,
                'erro' => $e->getMessage(),
            ]);

            // Fallback simples - retornar código RTF básico
            return $this->gerarCodigoRTFImagemSimples($caminhoImagem);
        }
    }

    /**
     * Gerar código RTF simples para imagem (fallback)
     */
    private function gerarCodigoRTFImagemSimples(string $caminhoImagem): string
    {
        try {
            if (! file_exists($caminhoImagem)) {
                return '[IMAGEM DO CABEÇALHO - ARQUIVO NÃO ENCONTRADO]';
            }

            $info = getimagesize($caminhoImagem);
            if (! $info) {
                return '[IMAGEM DO CABEÇALHO - FORMATO INVÁLIDO]';
            }

            // Converter imagem para hex
            $imagemData = file_get_contents($caminhoImagem);
            $imagemHex = bin2hex($imagemData);

            // Dimensões em twips (1 inch = 1440 twips)
            $larguraTwips = round(($info[0] * 1440) / 96); // 96 DPI padrão
            $alturaTwips = round(($info[1] * 1440) / 96);

            // Redimensionar para cabeçalho (máximo 3 inches de largura)
            $maxLargura = 4320; // 3 inches em twips
            if ($larguraTwips > $maxLargura) {
                $fator = $maxLargura / $larguraTwips;
                $larguraTwips = $maxLargura;
                $alturaTwips = round($alturaTwips * $fator);
            }

            $tipoImagem = $info['mime'] === 'image/png' ? 'pngblip' : 'jpegblip';

            return "{\\*\\shppict {\\pict\\{$tipoImagem}\\picw{$info[0]}\\pich{$info[1]}\\picwgoal{$larguraTwips}\\pichgoal{$alturaTwips} {$imagemHex}}}";

        } catch (\Exception $e) {
            Log::error('Erro ao gerar código RTF simples da imagem', [
                'caminho' => $caminhoImagem,
                'erro' => $e->getMessage(),
            ]);

            return '[ERRO: Não foi possível processar a imagem]';
        }
    }

    /**
     * Garantir que o conteúdo seja RTF válido
     */
    private function garantirRTFValido(string $conteudo): string
    {
        $conteudo = trim($conteudo);

        // ✅ CORREÇÃO ESPECÍFICA: Detectar e corrigir RTF corrompido
        $conteudo = $this->corrigirRTFCorrompido($conteudo);

        // Se já começa com {\rtf1, assumir que é válido
        if (str_starts_with($conteudo, '{\rtf1')) {
            return $conteudo;
        }

        // Se contém elementos RTF mas está corrompido, tentar extrair conteúdo útil
        if (str_contains($conteudo, '\\par') || str_contains($conteudo, '\\f0') || str_contains($conteudo, 'rtf1')) {
            // Extrair conteúdo após o cabeçalho RTF
            if (preg_match('/\\\\f0.*?(\\\\par.*?)(?:}\\s*$|$)/s', $conteudo, $matches)) {
                $conteudoExtraido = $matches[1];
            } else {
                // Fallback: usar todo o conteúdo depois dos headers
                $conteudoExtraido = preg_replace('/^.*?\\\\f0\\\\fs24\\\\sl360\\\\slmult1\\s*/', '', $conteudo);
                $conteudoExtraido = rtrim($conteudoExtraido, '}');
            }

            // Criar RTF válido com conteúdo extraído
            return "{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}\f0\fs24 ".trim($conteudoExtraido).'}';
        }

        // Se não é RTF, criar RTF básico
        return $this->criarRTFBasico($conteudo);
    }

    /**
     * Corrigir RTF corrompido (caracteres perdidos)
     */
    private function corrigirRTFCorrompido(string $conteudo): string
    {
        // Dicionário de correções para RTF corrompido
        $correcoes = [
            // Cabeçalho corrompido
            '{\tf1' => '{\rtf1',
            '{\rtf\ansi' => '{\rtf1\ansi',
            
            // Font table corrompida
            '\onttbl' => '\fonttbl',
            '{onttbl' => '{\fonttbl',
            
            // Font commands corrompidos
            '\0' => '\f0',
            '0s24' => '\f0\fs24',
            
            // Bold commands corrompidos
            '\bs' => '\b\fs',
            '\b0s' => '\b0\fs',
        ];

        $conteudoCorrigido = $conteudo;
        
        foreach ($correcoes as $corrompido => $correto) {
            $conteudoCorrigido = str_replace($corrompido, $correto, $conteudoCorrigido);
        }

        // Log da correção se houver mudanças
        if ($conteudoCorrigido !== $conteudo) {
            Log::info('RTF corrompido corrigido', [
                'original_inicio' => substr($conteudo, 0, 100),
                'corrigido_inicio' => substr($conteudoCorrigido, 0, 100),
                'correcoes_aplicadas' => array_keys(array_filter($correcoes, function($correto, $corrompido) use ($conteudo) {
                    return str_contains($conteudo, $corrompido);
                }, ARRAY_FILTER_USE_BOTH))
            ]);
        }

        return $conteudoCorrigido;
    }

    /**
     * Criar RTF básico válido quando conteúdo não é RTF
     */
    private function criarRTFBasico(string $conteudo): string
    {
        // Escapar caracteres especiais do RTF
        $conteudoEscapado = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $conteudo);

        // Converter quebras de linha para \par
        $conteudoEscapado = str_replace(["\r\n", "\n", "\r"], '\\par ', $conteudoEscapado);

        return "{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}\f0\fs24 ".$conteudoEscapado.'}';
    }

    /**
     * Converter RTF para TXT simples para OnlyOffice
     */
    private function converterRTFParaTXT(string $rtfContent): string
    {
        $conteudo = $rtfContent;

        // Remover COMPLETAMENTE toda formatação RTF
        // 1. Remover cabeçalho RTF completo
        $conteudo = preg_replace('/^{\\\\rtf1[^}]*}/', '', $conteudo);
        $conteudo = preg_replace('/^{\\\\rtf1.*?\\\\f0\\\\fs24/', '', $conteudo);

        // 2. Remover todos os comandos RTF (qualquer coisa com \)
        $conteudo = preg_replace('/\\\\[a-zA-Z]+\d*\\s*/', ' ', $conteudo);
        $conteudo = preg_replace('/\\\\[^a-zA-Z\s]/', '', $conteudo);

        // 3. Remover todas as chaves { }
        $conteudo = str_replace(['{', '}'], '', $conteudo);

        // 4. Converter \par para quebras de linha
        $conteudo = str_replace('\\par', "\n\n", $conteudo);
        $conteudo = str_replace('par', "\n\n", $conteudo);

        // 5. Limpar espaços e normalizar
        $conteudo = preg_replace('/\s+/', ' ', $conteudo); // Múltiplos espaços -> um espaço
        $conteudo = preg_replace('/\n\s+/', "\n", $conteudo); // Espaços após quebras
        $conteudo = preg_replace('/\n{3,}/', "\n\n", $conteudo); // Múltiplas quebras -> duas

        // 6. Garantir que variáveis estejam em linhas separadas
        $conteudo = preg_replace('/(\$\{[^}]+\})/', "\n$1\n", $conteudo);
        $conteudo = preg_replace('/\n{3,}/', "\n\n", $conteudo);

        return trim($conteudo);
    }

    /**
     * Criar template TXT simples sem RTF
     */
    private function criarTXTTemplate(): string
    {
        return <<<'TXT'
TEMPLATE UNIVERSAL - PROPOSIÇÕES LEGISLATIVAS

CABEÇALHO INSTITUCIONAL:
[Imagem do cabeçalho será inserida aqui]
${cabecalho_nome_camara}
${cabecalho_endereco}
Tel: ${cabecalho_telefone} - ${cabecalho_website}
CNPJ: ${cnpj_camara}

--------------------------------------------------

TIPO DA PROPOSIÇÃO: ${tipo_proposicao} N° ${numero_proposicao}

EMENTA: ${ementa}

PREÂMBULO DINÂMICO:
[Este campo se adapta automaticamente ao tipo de proposição]

CONTEÚDO PRINCIPAL:
${texto}

JUSTIFICATIVA:
${justificativa}

ARTICULADO (Para Projetos de Lei):
Art. 1° [Disposição principal]

Parágrafo único. [Detalhamento se necessário]

Art. 2° [Disposições complementares]

Art. 3° Esta lei entra em vigor na data de sua publicação.

--------------------------------------------------

ÁREA DE ASSINATURA:
${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}.

${assinatura_padrao}
${autor_nome}
${autor_cargo}

RODAPÉ INSTITUCIONAL:
${rodape_texto}
${endereco_camara}, ${endereco_bairro} - CEP: ${endereco_cep}
${municipio}/${municipio_uf} - Tel: ${telefone_camara}
${website_camara} - ${email_camara}
TXT;
    }

    /**
     * Converter UTF-8 para RTF com codificação Unicode correta
     */
    private function converterUtf8ParaRtf($texto)
    {
        // Primeiro, limpar qualquer corrupção existente e normalizar para UTF-8 limpo
        $textoLimpo = $this->limparTextoCorrupto($texto);

        // Proteger variáveis do template antes da conversão
        $variaveis = [];
        $contador = 0;

        // Substituir variáveis por marcadores temporários
        $textoProtegido = preg_replace_callback('/\$\{[^}]+\}/', function ($match) use (&$variaveis, &$contador) {
            $placeholder = "###VAR_{$contador}###";
            $variaveis[$placeholder] = $match[0];
            $contador++;

            return $placeholder;
        }, $textoLimpo);

        // Converter caracteres UTF-8 para sequências Unicode RTF
        $resultado = '';
        $length = mb_strlen($textoProtegido, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($textoProtegido, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');

            if ($codepoint > 127) {
                // Converter para escape Unicode RTF
                $resultado .= '\u'.$codepoint.'*';
            } else {
                $resultado .= $char;
            }
        }

        // Restaurar variáveis originais
        foreach ($variaveis as $placeholder => $variavel) {
            $resultado = str_replace($placeholder, $variavel, $resultado);
        }

        return $resultado;
    }

    /**
     * Limpar texto corrupto e normalizar para UTF-8
     */
    private function limparTextoCorrupto($texto)
    {
        // Detectar e corrigir sequências corruptas conhecidas
        $correcoes = [
            'SÃ£o' => 'São',
            'SÃ£' => 'Sã',
            'Municí­pio' => 'Município',
            'Munic­pio' => 'Município',
            'JosÃ©' => 'José',
            'EndereÃ§o' => 'Endereço',
            'ProposiÃ§Ã£o' => 'Proposição',
            'ProposiÃ§' => 'Proposiç',
            'CÃ¢mara' => 'Câmara',
            'DescriÃ§Ã£o' => 'Descrição',
            'ApresentaÃ§Ã£o' => 'Apresentação',
            'DisposiÃ§Ãµes' => 'Disposições',
            'CoordenaÃ§Ã£o' => 'Coordenação',
            'OrganizaÃ§Ã£o' => 'Organização',
            'ParticipaÃ§Ã£o' => 'Participação',
            'â€™' => "'",
            'â€œ' => '"',
            'â€�' => '"',
            'â€"' => '–',
            'â€"' => '—',
            'â€¢' => '•',
            'â€¦' => '…',
            'Ã¡' => 'á',
            'Ã©' => 'é',
            'Ã­' => 'í',
            'Ã³' => 'ó',
            'Ãº' => 'ú',
            'Ã¢' => 'â',
            'Ãª' => 'ê',
            'Ã´' => 'ô',
            'Ã£' => 'ã',
            'Ãµ' => 'õ',
            'Ã§' => 'ç',
            'Ã€' => 'À',
            'Ã‰' => 'É',
            'Ã' => 'Í',
            'Ã"' => 'Ó',
            'Ãš' => 'Ú',
            'Ã‚' => 'Â',
            'ÃŠ' => 'Ê',
            'Ã"' => 'Ô',
            'Ãƒ' => 'Ã',
            'Ã•' => 'Õ',
            'Ã‡' => 'Ç',
        ];

        $textoCorrigido = str_replace(array_keys($correcoes), array_values($correcoes), $texto);

        // Tentar detectar e corrigir double-encoding UTF-8
        if (mb_check_encoding($textoCorrigido, 'UTF-8')) {
            // Se já é UTF-8 válido mas tem caracteres estranhos, pode ser double-encoded
            $tentativaDecodificada = mb_convert_encoding(
                mb_convert_encoding($textoCorrigido, 'ISO-8859-1', 'UTF-8'),
                'UTF-8',
                'ISO-8859-1'
            );

            // Verificar se a decodificação melhorou (tem menos caracteres estranhos)
            if (substr_count($tentativaDecodificada, 'Ã') < substr_count($textoCorrigido, 'Ã')) {
                $textoCorrigido = $tentativaDecodificada;
            }
        }

        // Garantir que o resultado final é UTF-8 válido
        if (! mb_check_encoding($textoCorrigido, 'UTF-8')) {
            $textoCorrigido = mb_convert_encoding($textoCorrigido, 'UTF-8', 'auto');
        }

        // Normalizar quebras de linha
        $textoCorrigido = str_replace(["\r\n", "\r"], "\n", $textoCorrigido);

        return $textoCorrigido;
    }

    /**
     * Sanitizar conteúdo RTF (Laravel 12 best practice).
     */
    private function sanitizeRtfContent(string $content): string
    {
        // Remove caracteres de controle problemáticos mas preserva RTF
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        // Normalizar quebras de linha RTF
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Garantir que o RTF termine corretamente
        $content = trim($content);
        if (! str_ends_with($content, '}')) {
            $content .= '}';
        }

        return $content;
    }
}
