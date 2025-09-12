<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProposicaoApiController extends Controller
{
    /**
     * Get proposição with real-time data and optimized cache
     */
    public function show($id): JsonResponse
    {
        try {
            // Cache key baseado no ID da proposição e timestamp da última atualização
            $proposicao = $this->getProposicaoWithCache($id);

            // Verificar permissões
            if (!$this->canViewProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Formatar dados para resposta otimizada
            $data = $this->formatProposicaoResponse($proposicao);

            return response()->json([
                'success' => true,
                'proposicao' => $data,
                'timestamp' => now()->toISOString(),
                'cache_hit' => $this->wasCacheHit($id)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar proposição via API', [
                'proposicao_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar proposição',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Get proposição with intelligent cache
     */
    private function getProposicaoWithCache($id)
    {
        // Primeiro, verificar se existe no cache baseado na última modificação
        $lastModified = Cache::remember("proposicao_last_modified_{$id}", 60, function () use ($id) {
            return Proposicao::where('id', $id)
                ->value('updated_at');
        });

        $cacheKey = "proposicao_api_{$id}_{$lastModified}";
        
        return Cache::remember($cacheKey, 300, function () use ($id) { // Cache por 5 minutos
            return Proposicao::with([
                'autor:id,name,email'
            ])
            ->select([
                'id', 'tipo', 'ementa', 'conteudo', 'status', 
                'autor_id', 'template_id',
                'arquivo_path', 'arquivo_pdf_path', 'numero_protocolo',
                'created_at', 'updated_at', 'ultima_modificacao'
            ])
            ->findOrFail($id);
        });
    }

    /**
     * Format proposição response with optimized data
     */
    private function formatProposicaoResponse($proposicao): array
    {
        // NOVO: Extrair conteúdo do arquivo OnlyOffice se existir (TEMPO REAL)
        $conteudoOnlyOffice = $this->extrairConteudoOnlyOfficeTempoReal($proposicao);
        
        // Validar qualidade do conteúdo extraído
        $conteudoOnlyOfficeValido = $this->isConteudoOnlyOfficeValido($conteudoOnlyOffice);
        
        // Usar conteúdo do OnlyOffice apenas se for de qualidade, senão usar BD
        $conteudo = ($conteudoOnlyOfficeValido && $conteudoOnlyOffice) ? $conteudoOnlyOffice : $proposicao->conteudo;
        $conteudoPreview = null;
        
        if ($conteudo && strlen($conteudo) > 2000) {
            $conteudoPreview = substr($conteudo, 0, 500) . '...';
        }

        return [
            'id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'ementa' => $proposicao->ementa,
            'conteudo' => $conteudo,
            'conteudo_preview' => $conteudoPreview,
            'conteudo_length' => strlen($conteudo ?? ''),
            'conteudo_origem' => ($conteudoOnlyOfficeValido && $conteudoOnlyOffice) ? 'onlyoffice' : 'database',
            'conteudo_timestamp' => $this->obterTimestampArquivoOnlyOffice($proposicao),
            'status' => $proposicao->status,
            'numero_protocolo' => $proposicao->numero_protocolo,
            'created_at' => $proposicao->created_at?->toISOString(),
            'updated_at' => $proposicao->updated_at?->toISOString(),
            'ultima_modificacao' => $proposicao->ultima_modificacao?->toISOString(),
            'autor' => [
                'id' => $proposicao->autor?->id,
                'name' => $proposicao->autor?->name,
                'email' => $proposicao->autor?->email
            ],
            'template_info' => [
                'template_id' => $proposicao->template_id,
                'tipo' => $proposicao->tipo
            ],
            'has_arquivo' => !empty($proposicao->arquivo_path),
            'has_pdf' => $this->verificarExistenciaPDF($proposicao),
            'permissions' => [
                'can_edit' => $this->canEditProposicao($proposicao),
                'can_sign' => $this->canSignProposicao($proposicao),
                'can_view_content' => $this->canViewContent($proposicao),
                'can_send_legislative' => $this->canSendToLegislative($proposicao),
                'can_update_status' => $this->canUpdateStatusAPI($proposicao)
            ],
            'meta' => [
                'word_count' => str_word_count(strip_tags($conteudo ?? '')),
                'char_count' => strlen($conteudo ?? ''),
                'has_content' => !empty($conteudo),
                'is_complete' => !empty($proposicao->ementa) && !empty($conteudo)
            ]
        ];
    }

    /**
     * Check if response came from cache
     */
    private function wasCacheHit($id): bool
    {
        // Verificar se existe no cache baseado na última modificação
        $lastModified = Cache::get("proposicao_last_modified_{$id}");
        if ($lastModified) {
            $cacheKey = "proposicao_api_{$id}_{$lastModified}";
            return Cache::has($cacheKey);
        }
        return false;
    }

    /**
     * Update proposição status
     */
    public function updateStatus($id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|string|in:rascunho,em_edicao,enviado_legislativo,em_revisao,aguardando_aprovacao_autor,devolvido_edicao,retornado_legislativo,aprovado,reprovado'
            ]);

            $proposicao = Proposicao::findOrFail($id);

            // Verificar permissões para alterar status
            if (!$this->canUpdateStatus($proposicao, $request->status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para alterar este status'
                ], 403);
            }

            $oldStatus = $proposicao->status;
            $proposicao->update([
                'status' => $request->status,
                'ultima_modificacao' => now()
            ]);

            // Limpar cache
            Cache::forget("proposicao_api_{$id}");

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get proposição status history/changes
     */
    public function statusHistory($id): JsonResponse
    {
        try {
            $proposicao = Proposicao::findOrFail($id);

            if (!$this->canViewProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Simulação do histórico de status (poderia vir de uma tabela de log)
            $history = [
                [
                    'status' => 'rascunho',
                    'timestamp' => $proposicao->created_at->toISOString(),
                    'user' => $proposicao->autor?->name,
                    'description' => 'Proposição criada'
                ]
            ];

            if ($proposicao->status !== 'rascunho') {
                $history[] = [
                    'status' => $proposicao->status,
                    'timestamp' => $proposicao->updated_at->toISOString(),
                    'user' => Auth::user()->name ?? 'Sistema',
                    'description' => 'Status alterado para ' . $this->getStatusLabel($proposicao->status)
                ];
            }

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar histórico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Real-time updates endpoint
     */
    public function updates($id, Request $request): JsonResponse
    {
        try {
            $lastUpdate = $request->get('last_update');
            $proposicao = Proposicao::findOrFail($id);

            if (!$this->canViewProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Verificar se houve atualizações desde o último check
            $hasUpdates = false;
            if ($lastUpdate) {
                $lastUpdateTime = \Carbon\Carbon::parse($lastUpdate);
                $hasUpdates = $proposicao->updated_at > $lastUpdateTime;
            }

            return response()->json([
                'success' => true,
                'has_updates' => $hasUpdates,
                'current_status' => $proposicao->status,
                'last_modified' => $proposicao->updated_at->toISOString(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar atualizações',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can view proposição
     */
    private function canViewProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Simplificado: usuário logado pode ver suas próprias proposições
        // e também pode ver outras se for admin ou legislativo (baseado no email)
        
        // Autor pode ver sua proposição
        if ($proposicao->autor_id === $user->id) return true;

        // Admin pode ver tudo (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode ver proposições enviadas para análise (baseado no email/nome)
        if ((str_contains($user->email, 'legislativo') || str_contains($user->name, 'legislativo') || str_contains($user->email, 'joao')) && 
            in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado'])) {
            return true;
        }

        // Para desenvolvimento: permitir acesso geral para usuários logados
        return true;
    }

    /**
     * Check if user can edit proposição
     */
    private function canEditProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Admin pode editar tudo (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Autor pode editar se status permitir
        if ($proposicao->autor_id === $user->id && in_array($proposicao->status, [
            'rascunho', 'em_edicao', 'devolvido_edicao'
        ])) {
            return true;
        }

        // Legislativo pode editar durante revisão (baseado no email)
        if ((str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) && 
            $proposicao->status === 'em_revisao') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can sign proposição
     */
    private function canSignProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Apenas proposições aprovadas podem ser assinadas
        if ($proposicao->status !== 'aprovado') return false;

        // Parlamentares e admin podem assinar (baseado no email)
        return str_contains($user->email, 'parlamentar') || 
               str_contains($user->email, 'admin') || 
               str_contains($user->email, 'jessica') ||
               str_contains($user->email, 'bruno') ||
               $proposicao->autor_id === $user->id;
    }

    /**
     * Check if user can view full content
     */
    private function canViewContent(Proposicao $proposicao): bool
    {
        // Por enquanto, mesma regra do canViewProposicao
        return $this->canViewProposicao($proposicao);
    }

    /**
     * Check if user can update status to specific value
     */
    private function canUpdateStatus(Proposicao $proposicao, string $newStatus): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Admin pode alterar qualquer status (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode aprovar/reprovar/devolver (baseado no email)
        if (str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) {
            $allowedStatuses = ['em_revisao', 'aprovado', 'reprovado', 'devolvido_edicao'];
            return in_array($newStatus, $allowedStatuses);
        }

        // Autor pode alterar para em_edicao ou enviado_legislativo
        if ($proposicao->autor_id === $user->id) {
            $allowedStatuses = ['em_edicao', 'enviado_legislativo'];
            return in_array($newStatus, $allowedStatuses);
        }

        return false;
    }

    /**
     * Get status label in Portuguese
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'rascunho' => 'Rascunho',
            'em_edicao' => 'Em Edição',
            'enviado_legislativo' => 'Enviado ao Legislativo',
            'em_revisao' => 'Em Revisão',
            'aguardando_aprovacao_autor' => 'Aguardando Aprovação do Autor',
            'devolvido_edicao' => 'Devolvido para Edição',
            'retornado_legislativo' => 'Retornado do Legislativo',
            'aprovado' => 'Aprovado',
            'reprovado' => 'Reprovado'
        ];

        return $labels[$status] ?? 'Status Desconhecido';
    }

    /**
     * Check if user can send to legislative
     */
    private function canSendToLegislative(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Autor pode enviar se status permitir e tiver conteúdo mínimo
        if ($proposicao->autor_id === $user->id) {
            return in_array($proposicao->status, ['rascunho', 'em_edicao', 'devolvido_edicao']) &&
                   !empty($proposicao->ementa) && !empty($proposicao->conteudo);
        }

        // Admin sempre pode (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can update status via API
     */
    private function canUpdateStatusAPI(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Admin pode alterar qualquer status (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode alterar status durante revisão (baseado no email)
        if ((str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) && 
            $proposicao->status === 'em_revisao') {
            return true;
        }

        return false;
    }

    /**
     * Clear proposição cache when updated
     */
    public static function clearProposicaoCache($proposicaoId)
    {
        $patterns = [
            "proposicao_api_{$proposicaoId}",
            "proposicao_api_{$proposicaoId}_*", 
            "proposicao_last_modified_{$proposicaoId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        // Se tiver implementação de cache com tags, usar aqui
        // Cache::tags(['proposicao', "proposicao_{$proposicaoId}"])->flush();
    }

    /**
     * Verificar se existe PDF para a proposição
     */
    private function verificarExistenciaPDF($proposicao): bool
    {
        // REGRA CRÍTICA: PDF só deve aparecer após aprovação pelo Legislativo
        $statusComPDF = ['aprovado', 'aprovado_assinatura', 'assinado', 'enviado_protocolo', 'protocolado'];
        
        // Se não está em status que deveria ter PDF, não mostrar
        if (!in_array($proposicao->status, $statusComPDF)) {
            return false;
        }

        // 1. Verificar campo arquivo_pdf_path (método rápido)
        if (!empty($proposicao->arquivo_pdf_path)) {
            return true;
        }

        // 2. Verificar fisicamente se existe PDF
        if (in_array($proposicao->status, $statusComPDF)) {
            
            // Verificar múltiplos diretórios onde pode estar o PDF
            $possiveisCaminhos = [
                // Diretório principal de PDFs
                "private/proposicoes/pdfs/{$proposicao->id}/",
                "proposicoes/pdfs/{$proposicao->id}/",
                "pdfs/{$proposicao->id}/",
                // Arquivos individuais mais recentes
                "private/proposicoes/pdfs/{$proposicao->id}/proposicao_{$proposicao->id}_onlyoffice_*_assinado_*.pdf",
                "private/proposicoes/pdfs/{$proposicao->id}/proposicao_{$proposicao->id}_*.pdf",
            ];

            foreach ($possiveisCaminhos as $caminho) {
                try {
                    // Se contém asterisco, usar glob
                    if (strpos($caminho, '*') !== false) {
                        $arquivos = \Storage::glob($caminho);
                        if (!empty($arquivos)) {
                            return true;
                        }
                    } else {
                        // Verificar se o diretório existe e tem arquivos PDF
                        if (\Storage::exists($caminho)) {
                            $arquivos = \Storage::files($caminho);
                            foreach ($arquivos as $arquivo) {
                                if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'pdf') {
                                    return true;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Continuar verificando outros caminhos se um falhar
                    continue;
                }
            }
        }

        return false;
    }

    /**
     * Extrair conteúdo em tempo real do arquivo OnlyOffice
     * Prioriza arquivo salvo sobre conteúdo do banco de dados
     */
    private function extrairConteudoOnlyOfficeTempoReal($proposicao): ?string
    {
        try {
            // Verificar se existe arquivo salvo
            if (empty($proposicao->arquivo_path)) {
                return null;
            }

            // Buscar arquivo nos diretórios possíveis
            $caminhoArquivo = $this->encontrarArquivoOnlyOffice($proposicao);
            
            if (!$caminhoArquivo || !file_exists($caminhoArquivo)) {
                return null;
            }

            // Extrair conteúdo baseado na extensão
            $extensao = strtolower(pathinfo($caminhoArquivo, PATHINFO_EXTENSION));
            
            if ($extensao === 'rtf') {
                return $this->extrairConteudoRTF($caminhoArquivo);
            } elseif (in_array($extensao, ['docx', 'doc'])) {
                return $this->extrairConteudoDOCX($caminhoArquivo);
            }

            return null;

        } catch (\Exception $e) {
            \Log::warning('Erro ao extrair conteúdo OnlyOffice em tempo real', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $proposicao->arquivo_path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Encontrar caminho completo do arquivo OnlyOffice
     */
    private function encontrarArquivoOnlyOffice($proposicao): ?string
    {
        if (empty($proposicao->arquivo_path)) {
            return null;
        }

        // Array de caminhos possíveis ordenados por prioridade
        $caminhosPossiveis = [
            storage_path('app/' . $proposicao->arquivo_path),
            storage_path('app/proposicoes/' . $proposicao->arquivo_path),
            storage_path('app/private/' . $proposicao->arquivo_path),
            storage_path('app/local/' . $proposicao->arquivo_path),
        ];

        // Retornar o primeiro arquivo encontrado
        foreach ($caminhosPossiveis as $caminho) {
            if (file_exists($caminho)) {
                return $caminho;
            }
        }

        return null;
    }

    /**
     * Extrair conteúdo de arquivo RTF
     */
    private function extrairConteudoRTF(string $caminhoArquivo): string
    {
        try {
            $conteudoRTF = file_get_contents($caminhoArquivo);
            
            if (empty($conteudoRTF)) {
                return '';
            }

            // Usar reflexão para chamar método privado do OnlyOfficeService
            $onlyOfficeService = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
            
            // Como o método é privado, vamos implementar nossa própria extração RTF
            return $this->extrairTextoRTFSimples($caminhoArquivo);

        } catch (\Exception $e) {
            \Log::error('Erro ao extrair conteúdo RTF', [
                'arquivo' => $caminhoArquivo,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Extrair conteúdo de arquivo DOCX
     */
    private function extrairConteudoDOCX(string $caminhoArquivo): string
    {
        try {
            // Verificar se arquivo existe e não é muito grande
            if (!file_exists($caminhoArquivo)) {
                return '';
            }

            $fileSize = filesize($caminhoArquivo);
            if ($fileSize > 10 * 1024 * 1024) { // 10MB limite
                return 'Arquivo muito grande para visualização.';
            }

            // Usar ZipArchive para extrair texto do DOCX
            $zip = new \ZipArchive;
            if ($zip->open($caminhoArquivo) !== true) {
                return '';
            }

            $content = $zip->getFromName('word/document.xml');
            $zip->close();

            if (!$content) {
                return '';
            }

            // Limpar XML e extrair texto
            $textoExtraido = $this->extrairTextoDeXML($content);
            
            return $this->limparTextoExtraido($textoExtraido);

        } catch (\Exception $e) {
            \Log::error('Erro ao extrair conteúdo DOCX', [
                'arquivo' => $caminhoArquivo,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Extrair texto de XML do Word
     */
    private function extrairTextoDeXML(string $xmlContent): string
    {
        try {
            // Remover tags XML e deixar apenas o texto
            $texto = preg_replace('/<[^>]+>/', ' ', $xmlContent);
            
            // Limpar espaços múltiplos
            $texto = preg_replace('/\s+/', ' ', $texto);
            
            // Decodificar entidades HTML
            $texto = html_entity_decode($texto, ENT_QUOTES | ENT_XML1, 'UTF-8');
            
            return trim($texto);

        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Limpar texto extraído de caracteres indesejados
     */
    private function limparTextoExtraido(string $texto): string
    {
        // Remover caracteres de controle e especiais
        $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $texto);
        
        // Limpar espaços múltiplos e quebras de linha excessivas
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = preg_replace('/\n\s*\n\s*\n/', "\n\n", $texto);
        
        return trim($texto);
    }

    /**
     * Obter timestamp do arquivo OnlyOffice
     */
    private function obterTimestampArquivoOnlyOffice($proposicao): ?int
    {
        $caminhoArquivo = $this->encontrarArquivoOnlyOffice($proposicao);
        
        if (!$caminhoArquivo || !file_exists($caminhoArquivo)) {
            return null;
        }

        return filemtime($caminhoArquivo);
    }

    /**
     * Extração robusta de texto RTF focada em conteúdo útil
     */
    private function extrairTextoRTFSimples(string $caminhoArquivo): string
    {
        try {
            $conteudoRTF = file_get_contents($caminhoArquivo);
            
            if (empty($conteudoRTF)) {
                return '';
            }

            // ESTRATÉGIA: Procurar por texto entre parágrafos e fora de grupos de comando
            
            // ETAPA 1: Remover cabeçalhos RTF complexos
            $texto = preg_replace('/\{\\\\rtf1.*?\{\\\\colortbl[^}]*\}/s', '', $conteudoRTF);
            $texto = preg_replace('/\{\\\\fonttbl.*?\}/s', '', $texto);
            $texto = preg_replace('/\{\\\\stylesheet.*?\}/s', '', $texto);
            $texto = preg_replace('/\{\\\\\*\\\\defchp.*?\}/', '', $texto);
            
            // ETAPA 2: Decodificar sequências Unicode específicas
            $texto = preg_replace_callback('/\{\\\\uc1\\\\u(\d+)\*[^}]*\}/', function($matches) {
                $code = intval($matches[1]);
                if ($code >= 32 && $code <= 255) {
                    return chr($code);
                }
                return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
            }, $texto);
            
            // ETAPA 3: Buscar parágrafos de texto útil entre comandos \par
            if (preg_match_all('/\\\\par\s*([^\\\\{]*?)(?=\\\\|$)/s', $texto, $matches)) {
                $paragrafos = array_filter($matches[1], function($paragrafo) {
                    $limpo = trim($paragrafo);
                    return strlen($limpo) > 10 && preg_match('/[A-Za-zÀ-ÿ]/', $limpo);
                });
                
                if (!empty($paragrafos)) {
                    $textoFinal = implode("\n\n", array_map('trim', $paragrafos));
                    return $this->limparTextoFinal($textoFinal);
                }
            }
            
            // ETAPA 4: Fallback - remover todos os comandos RTF
            $texto = preg_replace('/\{[^{}]*\}/', '', $texto);
            $texto = preg_replace('/\\\\[a-z]+\d*\s?/', ' ', $texto);
            $texto = preg_replace('/[{}]/', '', $texto);
            
            // ETAPA 5: Extrair apenas sequências de texto válido
            if (preg_match_all('/[A-Za-zÀ-ÿ0-9\s\.\,\:\;\!\?\-\(\)\n]{15,}/', $texto, $matches)) {
                $texto = implode(' ', $matches[0]);
            }
            
            return $this->limparTextoFinal($texto);
            
        } catch (\Exception $e) {
            \Log::error('Erro na extração RTF robusta', [
                'arquivo' => $caminhoArquivo,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Limpeza final do texto extraído
     */
    private function limparTextoFinal(string $texto): string
    {
        // Remover caracteres de controle
        $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $texto);
        
        // Limpar espaços múltiplos mas preservar quebras de linha duplas
        $texto = preg_replace('/[ \t]+/', ' ', $texto);
        $texto = preg_replace('/\n\s*\n/', "\n\n", $texto);
        
        // Remover linhas muito curtas que podem ser lixo
        $linhas = explode("\n", $texto);
        $linhasLimpas = array_filter($linhas, function($linha) {
            $linha = trim($linha);
            return strlen($linha) > 5 && !preg_match('/^[\s\*\;\.\,\-]+$/', $linha);
        });
        
        return trim(implode("\n", $linhasLimpas));
    }

    /**
     * Validar se o conteúdo extraído do OnlyOffice é de qualidade
     */
    private function isConteudoOnlyOfficeValido(?string $conteudo): bool
    {
        if (empty($conteudo)) {
            return false;
        }

        $totalLength = strlen($conteudo);

        // Verificar se começa com dados hexadecimais (provavelmente imagem)
        if (preg_match('/^[0-9a-f]{20,}/', strtolower($conteudo))) {
            return false; // Começa com muitos dados hex
        }

        // Verificar proporção de caracteres hexadecimais
        $hexMatches = preg_match_all('/[0-9a-f]/', strtolower($conteudo));
        if ($hexMatches > 0 && ($hexMatches / $totalLength) > 0.7) {
            return false; // Mais de 70% são caracteres hex
        }

        // Verificar se tem palavras reais em português/inglês
        $palavrasReais = preg_match_all('/\b[A-Za-zÀ-ÿ]{3,}\b/', $conteudo);
        if ($palavrasReais < 5) {
            return false; // Muito poucas palavras reais
        }

        // Verificar se não é só asteriscos e pontos-vírgulas
        $lixoRTF = preg_match_all('/[*;]/', $conteudo);
        if ($lixoRTF > 0 && ($lixoRTF / $totalLength) > 0.3) {
            return false; // Muito lixo RTF
        }

        return true;
    }

    /**
     * Atualizar conteúdo da proposição diretamente
     * Usado quando o conteúdo do OnlyOffice precisa ser salvo manualmente
     */
    public function updateContent($id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'conteudo' => 'required|string|min:10',
                'origem' => 'string|in:onlyoffice,manual,api'
            ]);

            $proposicao = Proposicao::findOrFail($id);

            // Verificar permissões
            if (!$this->canEditProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para editar esta proposição'
                ], 403);
            }

            // Salvar conteúdo anterior para auditoria
            $conteudoAnterior = $proposicao->conteudo;

            // Atualizar conteúdo
            $proposicao->update([
                'conteudo' => $request->conteudo,
                'ultima_modificacao' => now()
            ]);

            // Limpar cache
            self::clearProposicaoCache($id);

            // Log da atualização
            \Log::info('Conteúdo da proposição atualizado via API', [
                'proposicao_id' => $id,
                'user_id' => Auth::id(),
                'origem' => $request->origem ?? 'api',
                'conteudo_anterior_length' => strlen($conteudoAnterior ?? ''),
                'conteudo_novo_length' => strlen($request->conteudo),
                'preview' => substr($request->conteudo, 0, 100)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo atualizado com sucesso',
                'proposicao' => [
                    'id' => $proposicao->id,
                    'conteudo_length' => strlen($proposicao->conteudo),
                    'ultima_modificacao' => $proposicao->ultima_modificacao->toISOString()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar conteúdo da proposição', [
                'proposicao_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar conteúdo',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno'
            ], 500);
        }
    }
}