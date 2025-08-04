<?php

namespace App\Http\Controllers;

use App\Models\TipoProposicao;
use App\Models\Proposicao;
use App\Models\DocumentoTemplate;
use App\Services\Template\TemplateProcessorService;
use App\Services\Template\TemplateInstanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoController extends Controller
{
    /**
     * Tela inicial para criação de proposição (Parlamentar)
     */
    public function create()
    {
        // Verificar se é usuário do Legislativo - eles não podem criar proposições
        if (auth()->user()->isLegislativo() && !auth()->user()->isParlamentar()) {
            return redirect()->route('proposicoes.legislativo.index')
                ->with('warning', 'Usuários do Legislativo não podem criar proposições. Acesse as proposições enviadas para análise.');
        }

        try {
            // Buscar tipos ativos do banco de dados
            $tipos = TipoProposicao::getParaDropdown();
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar tipos de proposição', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback com tipos padrão
            $tipos = [
                'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
                'projeto_lei_complementar' => 'Projeto de Lei Complementar',
                'indicacao' => 'Indicação',
                'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
                'projeto_resolucao' => 'Projeto de Resolução'
            ];
        }
        
        return view('proposicoes.create', compact('tipos'));
    }

    /**
     * Salvar dados básicos da proposição como rascunho
     */
    public function salvarRascunho(Request $request)
    {
        // Validar se o tipo existe e está ativo
        try {
            $tiposValidos = TipoProposicao::ativos()->pluck('codigo')->toArray();
        } catch (\Exception $e) {
            // Fallback para tipos padrão
            $tiposValidos = array_keys(TipoProposicao::getTiposPadrao());
        }
        
        $request->validate([
            'tipo' => 'required|in:' . implode(',', $tiposValidos),
            'ementa' => 'required|string|max:1000',
        ]);

        // Criar proposição no banco de dados
        $proposicao = Proposicao::create([
            'tipo' => $request->tipo,
            'ementa' => $request->ementa,
            'autor_id' => Auth::id(),
            'status' => 'rascunho',
            'ano' => date('Y'),
        ]);

        return response()->json([
            'success' => true,
            'proposicao_id' => $proposicao->id,
            'message' => 'Rascunho salvo com sucesso!'
        ]);
    }

    /**
     * Buscar modelos baseados no tipo de proposição
     */
    public function buscarModelos($tipo)
    {
        \Log::info('Buscando modelos para tipo: ' . $tipo);
        
        try {
            // Verificar se o tipo existe
            $tipoProposicao = TipoProposicao::buscarPorCodigo($tipo);
            
            if (!$tipoProposicao || !$tipoProposicao->ativo) {
                \Log::warning('Tipo de proposição não encontrado ou inativo: ' . $tipo);
                return response()->json([], 404);
            }
            
            \Log::info('Tipo encontrado, buscando modelos para ID: ' . $tipoProposicao->id);
            
            // Usar o DocumentoModeloService para buscar modelos e templates
            $documentoModeloService = app(\App\Services\Documento\DocumentoModeloService::class);
            $modelos = $documentoModeloService->obterModelosDisponiveis($tipoProposicao->id);
            
            \Log::info('Modelos encontrados: ' . $modelos->count());
            
            // Converter para formato esperado pelo frontend - apenas templates OnlyOffice
            $modelosArray = [];
            foreach ($modelos as $modelo) {
                // Incluir apenas modelos que sejam templates (usam OnlyOffice)
                if ($modelo->is_template ?? false) {
                    $modelosArray[] = [
                        'id' => 'template_' . $modelo->template_id,
                        'nome' => $modelo->nome,
                        'descricao' => $modelo->descricao ?? '',
                        'is_template' => true,
                        'template_id' => $modelo->template_id
                    ];
                }
            }
            
            // Sempre adicionar template em branco como primeira opção
            array_unshift($modelosArray, [
                'id' => 'template_blank',
                'nome' => 'Documento em Branco',
                'descricao' => 'Criar proposição com template em branco usando OnlyOffice',
                'is_template' => true,
                'template_id' => 'blank'
            ]);

            \Log::info('Modelos formatados para retorno:', [
                'count' => count($modelosArray),
                'data' => $modelosArray
            ]);

            return response()->json($modelosArray);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar modelos para tipo: ' . $tipo, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback com modelos mock quando há erro de conexão
            $modelosMock = $this->getModelosMockPorTipo($tipo);
            \Log::info('Usando fallback com ' . count($modelosMock) . ' modelos mock para tipo: ' . $tipo);
            return response()->json($modelosMock);
        }
    }
    
    /**
     * Retornar modelos mock para cada tipo (fallback para problemas de conexão)
     */
    private function getModelosMockPorTipo($tipo)
    {
        $modelos = [
            'projeto_lei_ordinaria' => [
                [
                    'id' => 'mock_1',
                    'nome' => 'Modelo Padrão - Projeto de Lei',
                    'descricao' => 'Template padrão para projetos de lei ordinária',
                    'is_template' => true,
                    'template_id' => 1
                ],
                [
                    'id' => 'mock_2',
                    'nome' => 'Modelo Simplificado - PL',
                    'descricao' => 'Template simplificado para projetos de lei',
                    'is_template' => false,
                    'template_id' => null
                ]
            ],
            'projeto_lei_complementar' => [
                [
                    'id' => 'mock_3',
                    'nome' => 'Modelo Padrão - Lei Complementar',
                    'descricao' => 'Template padrão para projetos de lei complementar',
                    'is_template' => true,
                    'template_id' => 2
                ]
            ],
            'indicacao' => [
                [
                    'id' => 'mock_4',
                    'nome' => 'Modelo Padrão - Indicação',
                    'descricao' => 'Template padrão para indicações',
                    'is_template' => true,
                    'template_id' => 3
                ]
            ],
            'projeto_decreto_legislativo' => [
                [
                    'id' => 'mock_5',
                    'nome' => 'Modelo Padrão - Decreto Legislativo',
                    'descricao' => 'Template padrão para projetos de decreto legislativo',
                    'is_template' => true,
                    'template_id' => 4
                ]
            ],
            'projeto_resolucao' => [
                [
                    'id' => 'mock_6',
                    'nome' => 'Modelo Padrão - Resolução',
                    'descricao' => 'Template padrão para projetos de resolução',
                    'is_template' => true,
                    'template_id' => 5
                ]
            ]
        ];
        
        return $modelos[$tipo] ?? [
            [
                'id' => 'mock_default',
                'nome' => 'Modelo Padrão',
                'descricao' => 'Template padrão para este tipo de proposição',
                'is_template' => true,
                'template_id' => 999
            ]
        ];
    }

    /**
     * Tela de preenchimento do modelo selecionado
     */
    public function preencherModelo($proposicaoId, $modeloId)
    {
        // TODO: Implement proper authorization
        // $this->authorize('update', $proposicao);
        
        $proposicao = Proposicao::findOrFail($proposicaoId);
        
        // Verificar se o usuário é o autor
        if ($proposicao->autor_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar esta proposição.');
        }
        
        $modelo = (object) ['id' => $modeloId, 'nome' => 'Modelo Temporário']; // Temporary
        
        return view('proposicoes.preencher-modelo', compact('proposicao', 'modelo'));
    }

    /**
     * Gerar texto editável baseado no modelo preenchido
     */
    public function gerarTexto(Request $request, $proposicaoId)
    {
        $request->validate([
            'conteudo_modelo' => 'required|array',
            'modelo_id' => 'required'
        ]);

        $proposicao = Proposicao::findOrFail($proposicaoId);
        
        // Verificar autorização
        if ($proposicao->autor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para editar esta proposição.'
            ], 403);
        }

        $modeloId = $request->modelo_id;
        $isTemplate = str_starts_with($modeloId, 'template_');
        
        if ($isTemplate) {
            // Processar com novo sistema de templates
            $templateId = str_replace('template_', '', $modeloId);
            
            // Lidar com template em branco especial
            if ($templateId === 'blank') {
                $template = null; // Template em branco não precisa de template específico
            } else {
                $template = \App\Models\TipoProposicaoTemplate::find($templateId);
                
                if (!$template) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Template não encontrado.'
                    ], 404);
                }
            }

            // Mapear campos do formulário para variáveis do template
            $variaveisTemplate = $request->conteudo_modelo;
            $variaveisTemplate['texto'] = $variaveisTemplate['conteudo'] ?? '';
            
            // Salvar variáveis na sessão temporariamente
            $sessionKey = 'proposicao_' . $proposicaoId . '_variaveis_template';
            session([$sessionKey => $variaveisTemplate]);
            
            // Atualizar proposição apenas com campos existentes
            $proposicao->update([
                'ementa' => $request->conteudo_modelo['ementa'] ?? $proposicao->ementa,
                'conteudo' => $request->conteudo_modelo['conteudo'] ?? $proposicao->conteudo,
                'modelo_id' => $modeloId,
                'template_id' => $templateId,
                'ultima_modificacao' => now()
            ]);
            
            // Processar template
            if ($template) {
                // Template específico existe - usar processamento normal
                $templateProcessor = app(TemplateProcessorService::class);
                $textoGerado = $templateProcessor->processarTemplate(
                    $template,
                    $proposicao,
                    $variaveisTemplate
                );
            } else {
                // Template em branco - criar conteúdo básico
                $textoGerado = $this->criarTextoBasico($proposicao, $variaveisTemplate);
            }
            
            // Salvar texto processado na sessão
            session(['proposicao_' . $proposicaoId . '_conteudo_processado' => $textoGerado]);
            
        } else {
            // Fallback - tratar qualquer modelo não-template como template em branco
            return response()->json([
                'success' => false,
                'message' => 'Tipo de modelo não suportado. Use apenas templates OnlyOffice.'
            ], 400);
        }

        // Armazenar informações adicionais na sessão (backup)
        session([
            'proposicao_' . $proposicaoId . '_modelo_id' => $modeloId,
            'proposicao_' . $proposicaoId . '_template_id' => $isTemplate ? $templateId : null,
            'proposicao_' . $proposicaoId . '_tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'projeto_lei'),
            'proposicao_' . $proposicaoId . '_texto_gerado' => $textoGerado
        ]);

        \Log::info('Texto gerado para proposição', [
            'proposicao_id' => $proposicaoId,
            'modelo_id' => $modeloId,
            'is_template' => $isTemplate,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'texto_gerado' => $textoGerado,
            'message' => 'Texto gerado com sucesso!'
        ]);
    }

    /**
     * Tela de edição final do documento
     */
    public function editarTexto($proposicaoId)
    {
        // TODO: Implement proper authorization and model loading
        // $this->authorize('update', $proposicao);
        
        // Simular busca da proposição com dados sobre o template usado
        $proposicao = (object) [
            'id' => $proposicaoId,
            'conteudo' => 'Conteúdo temporário da proposição...',
            'modelo_id' => session('proposicao_' . $proposicaoId . '_modelo_id'), // Recuperar modelo usado
            'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'mocao') // Recuperar tipo
        ];
        
        // Se foi usado um template, redirecionar para o editor OnlyOffice
        if ($proposicao->modelo_id && str_starts_with($proposicao->modelo_id, 'template_')) {
            $templateId = str_replace('template_', '', $proposicao->modelo_id);
            return redirect()->route('proposicoes.editar-onlyoffice', [
                'proposicao' => $proposicaoId,
                'template' => $templateId
            ]);
        }
        
        // Caso contrário, usar o editor de texto simples
        return view('proposicoes.editar-texto', compact('proposicao'));
    }

    /**
     * Abrir documento no OnlyOffice baseado no template
     */
    public function editarOnlyOffice($proposicaoId, $templateId)
    {
        // TODO: Implement proper authorization
        // $this->authorize('update', $proposicao);
        
        // Buscar proposição no banco de dados primeiro
        $proposicao = Proposicao::find($proposicaoId);
        
        // Se não encontrar no BD, verificar se há dados na sessão (fallback para proposições antigas)
        if (!$proposicao) {
            // Criar objeto mock apenas se há dados na sessão
            if (session()->has('proposicao_' . $proposicaoId . '_tipo')) {
                $proposicao = (object) [
                    'id' => $proposicaoId,
                    'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'mocao'),
                    'modelo_id' => session('proposicao_' . $proposicaoId . '_modelo_id'),
                    'arquivo_path' => null
                ];
            } else {
                // Proposição não existe nem no BD nem na sessão
                return redirect()->route('proposicoes.minhas-proposicoes')
                               ->with('error', 'Proposição não encontrada.');
            }
        }
        
        try {
            // Buscar template do administrador baseado no tipo da proposição
            $template = null;
            
            if ($templateId !== 'blank') {
                // Se templateId é numérico, buscar diretamente pelo ID do template
                if (is_numeric($templateId)) {
                    $template = \App\Models\TipoProposicaoTemplate::where('id', $templateId)
                                                                  ->where('ativo', true)
                                                                  ->first();
                    
                    \Log::info('Buscando template do admin pelo ID', [
                        'proposicao_id' => $proposicaoId,
                        'template_id' => $templateId,
                        'template_encontrado' => $template ? $template->id : 'nenhum'
                    ]);
                } else {
                    // Fallback: buscar pelo tipo de proposição
                    $tipoProposicao = \App\Models\TipoProposicao::buscarPorCodigo($proposicao->tipo);
                    
                    if ($tipoProposicao) {
                        // Buscar template criado pelo admin para este tipo de proposição
                        $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoProposicao->id)
                                                                      ->where('ativo', true)
                                                                      ->first();
                        
                        \Log::info('Buscando template do admin por tipo', [
                            'proposicao_id' => $proposicaoId,
                            'tipo_proposicao' => $proposicao->tipo,
                            'tipo_proposicao_id' => $tipoProposicao->id,
                            'template_encontrado' => $template ? $template->id : 'nenhum'
                        ]);
                    } else {
                        \Log::warning('Tipo de proposição não encontrado', [
                            'tipo' => $proposicao->tipo
                        ]);
                    }
                }
            }
            
            // Criar uma instância do documento baseada no template para esta proposição
            // Usar chave única com timestamp para evitar conflitos de versão
            $documentKey = 'proposicao_' . $proposicaoId . '_' . $templateId . '_' . time() . '_' . substr(md5(uniqid()), 0, 8);
            
            // Verificar se a proposição já tem um arquivo salvo no banco de dados
            if ($proposicao->arquivo_path && \Storage::disk('public')->exists($proposicao->arquivo_path)) {
                $arquivoProposicaoPath = $proposicao->arquivo_path;
                \Log::info('Usando arquivo existente da proposição do banco de dados', [
                    'proposicao_id' => $proposicaoId,
                    'arquivo_path' => $arquivoProposicaoPath
                ]);
            } else {
                // Criar arquivo da proposição (com ou sem template específico)
                $arquivoProposicaoPath = $this->criarArquivoProposicao($proposicaoId, $template);
            }
            
            $arquivoProposicao = basename($arquivoProposicaoPath); // Apenas o nome do arquivo
            
            \Log::info('Abrindo proposição no OnlyOffice', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $templateId,
                'document_key' => $documentKey,
                'arquivo' => $arquivoProposicao
            ]);
            
            return view('proposicoes.editar-onlyoffice', compact('proposicao', 'template', 'documentKey', 'arquivoProposicao'));
            
        } catch (\Exception $e) {
            \Log::error('Erro ao abrir OnlyOffice para proposição', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $templateId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('proposicoes.editar-texto', $proposicaoId)
                            ->with('error', 'Erro ao abrir editor. Usando editor de texto.');
        }
    }

    /**
     * Salvar alterações no texto
     */
    public function salvarTexto(Request $request, $proposicaoId)
    {
        // TODO: Implement proper authorization and model update
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        // TODO: Update proposicao in database
        // $proposicao->update(['conteudo' => $request->conteudo]);

        return response()->json([
            'success' => true,
            'message' => 'Texto salvo com sucesso!'
        ]);
    }

    /**
     * Enviar proposição para análise do legislativo
     */
    public function enviarLegislativo(Proposicao $proposicao)
    {
        \Log::info('Método enviarLegislativo chamado', [
            'proposicao_id' => $proposicao->id,
            'proposicao_status' => $proposicao->status,
            'proposicao_ementa' => $proposicao->ementa ? 'presente' : 'ausente',
            'proposicao_conteudo' => $proposicao->conteudo ? 'presente' : 'ausente',
            'proposicao_arquivo' => $proposicao->arquivo_path ? 'presente' : 'ausente',
            'user_id' => auth()->id(),
            'is_author' => $proposicao->autor_id === auth()->id()
        ]);
        
        try {
            // Verificar se o usuário é o autor da proposição
            if ($proposicao->autor_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para enviar esta proposição.'
                ], 403);
            }

            // Verificar se a proposição está no status correto
            if (!in_array($proposicao->status, ['rascunho', 'em_edicao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser enviada no status atual.'
                ], 400);
            }

            // Validar se tem conteúdo mínimo
            if (empty($proposicao->ementa) || (!$proposicao->conteudo && !$proposicao->arquivo_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposição deve ter ementa e conteúdo antes de ser enviada.'
                ], 400);
            }

            // Atualizar status para enviado ao legislativo
            $proposicao->update([
                'status' => 'enviado_legislativo'
            ]);

            \Log::info('Proposição enviada para legislativo', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'status_anterior' => $proposicao->getOriginal('status')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposição enviada para análise legislativa com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar proposição para legislativo', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Listagem das próprias proposições (Parlamentar)
     */
    public function minhasProposicoes()
    {
        // Verificar se é usuário do Legislativo - eles não podem acessar esta página
        if (auth()->user()->isLegislativo() && !auth()->user()->isParlamentar()) {
            return redirect()->route('proposicoes.legislativo.index')
                ->with('warning', 'Usuários do Legislativo devem acessar as proposições pela área do Legislativo.');
        }

        // Buscar proposições do usuário logado do banco de dados
        $proposicoes = Proposicao::doAutor(Auth::id())
            ->with('autor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('proposicoes.minhas-proposicoes', compact('proposicoes'));
    }

    /**
     * Visualizar proposição
     */
    public function show($proposicaoId)
    {
        // Buscar proposição real do banco de dados
        $proposicao = Proposicao::findOrFail($proposicaoId);
        
        // TODO: Implement proper authorization
        // $this->authorize('view', $proposicao);
        
        return view('proposicoes.show', compact('proposicao'));
    }

    /**
     * Visualizar status de tramitação da proposição
     */
    public function statusTramitacao(Proposicao $proposicao)
    {
        try {
            // Buscar dados atualizados da proposição
            $proposicao->refresh();
            
            // Definir classe do badge baseado no status
            $statusClasses = [
                'rascunho' => 'warning',
                'em_edicao' => 'warning', 
                'enviado_legislativo' => 'secondary',
                'em_revisao' => 'primary',
                'aguardando_aprovacao_autor' => 'primary',
                'devolvido_edicao' => 'warning',
                'retornado_legislativo' => 'info',
                'aprovado' => 'success',
                'rejeitado' => 'danger'
            ];
            
            // Definir descrições do status
            $statusDescricoes = [
                'rascunho' => 'A proposição está em elaboração e ainda não foi enviada.',
                'em_edicao' => 'A proposição está sendo editada pelo autor.',
                'enviado_legislativo' => 'A proposição foi enviada para o Legislativo e está aguardando análise inicial.',
                'em_revisao' => 'O Legislativo está analisando a proposição e verificando sua conformidade.',
                'aguardando_aprovacao_autor' => 'A proposição foi editada pelo Legislativo e aguarda aprovação do autor.',
                'devolvido_edicao' => 'A proposição foi devolvida pelo Legislativo para ajustes do autor.',
                'retornado_legislativo' => 'A proposição foi aprovada pelo Legislativo e retornada para assinatura do autor.',
                'aprovado' => 'A proposição foi aprovada e está pronta para tramitação.',
                'rejeitado' => 'A proposição foi rejeitada pelo Legislativo.'
            ];
            
            // Formatar nome do status
            $statusFormatado = ucfirst(str_replace('_', ' ', $proposicao->status));
            
            return response()->json([
                'success' => true,
                'status' => $proposicao->status,
                'status_formatado' => $statusFormatado,
                'status_class' => $statusClasses[$proposicao->status] ?? 'secondary',
                'status_descricao' => $statusDescricoes[$proposicao->status] ?? 'Status personalizado: ' . $proposicao->status,
                'timeline_updated' => false, // Por enquanto não implementamos atualização da timeline
                'timeline' => null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao obter status da proposição', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status atualizado da proposição.'
            ], 500);
        }
    }

    /**
     * Buscar notificações do usuário atual
     */
    public function buscarNotificacoes()
    {
        try {
            $user = \Auth::user();
            $notificacoes = [];

            // Buscar proposições retornadas do legislativo
            $proposicoesRetornadas = Proposicao::where('autor_id', $user->id)
                ->where('status', 'retornado_legislativo')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($proposicoesRetornadas as $proposicao) {
                $notificacoes[] = [
                    'id' => 'prop_ret_' . $proposicao->id,
                    'tipo' => 'retornado_legislativo',
                    'titulo' => 'Proposição #' . $proposicao->id . ' Retornada',
                    'descricao' => 'Proposição aprovada pelo Legislativo e aguarda sua assinatura',
                    'ementa' => \Str::limit($proposicao->ementa ?? 'Sem ementa', 60),
                    'data' => $proposicao->updated_at,
                    'data_formatada' => $proposicao->updated_at->diffForHumans(),
                    'link' => route('proposicoes.show', $proposicao->id),
                    'link_acao' => route('proposicoes.assinar', $proposicao->id),
                    'acao_texto' => 'Assinar',
                    'icone' => 'ki-duotone ki-check-square',
                    'cor' => 'info',
                    'prioridade' => 'alta'
                ];
            }

            // Buscar proposições aguardando aprovação do autor
            $proposicoesAguardando = Proposicao::where('autor_id', $user->id)
                ->where('status', 'aguardando_aprovacao_autor')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($proposicoesAguardando as $proposicao) {
                $notificacoes[] = [
                    'id' => 'prop_agrd_' . $proposicao->id,
                    'tipo' => 'aguardando_aprovacao_autor',
                    'titulo' => 'Proposição #' . $proposicao->id . ' Editada',
                    'descricao' => 'Proposição editada pelo Legislativo aguarda sua aprovação',
                    'ementa' => \Str::limit($proposicao->ementa ?? 'Sem ementa', 60),
                    'data' => $proposicao->updated_at,
                    'data_formatada' => $proposicao->updated_at->diffForHumans(),
                    'link' => route('proposicoes.show', $proposicao->id),
                    'link_acao' => route('proposicoes.editar-texto', $proposicao->id),
                    'acao_texto' => 'Revisar',
                    'icone' => 'ki-duotone ki-check-circle',
                    'cor' => 'primary',
                    'prioridade' => 'media'
                ];
            }

            // Buscar proposições devolvidas para edição
            $proposicoesDevolvidas = Proposicao::where('autor_id', $user->id)
                ->where('status', 'devolvido_edicao')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($proposicoesDevolvidas as $proposicao) {
                $notificacoes[] = [
                    'id' => 'prop_dev_' . $proposicao->id,
                    'tipo' => 'devolvido_edicao',
                    'titulo' => 'Proposição #' . $proposicao->id . ' Devolvida',
                    'descricao' => 'Proposição devolvida pelo Legislativo para ajustes',
                    'ementa' => \Str::limit($proposicao->ementa ?? 'Sem ementa', 60),
                    'data' => $proposicao->updated_at,
                    'data_formatada' => $proposicao->updated_at->diffForHumans(),
                    'link' => route('proposicoes.show', $proposicao->id),
                    'link_acao' => route('proposicoes.editar-texto', $proposicao->id),
                    'acao_texto' => 'Editar',
                    'icone' => 'ki-duotone ki-arrow-left',
                    'cor' => 'warning',
                    'prioridade' => 'media'
                ];
            }

            // Ordenar por prioridade e data
            usort($notificacoes, function($a, $b) {
                $prioridades = ['alta' => 3, 'media' => 2, 'baixa' => 1];
                $prioridadeA = $prioridades[$a['prioridade']] ?? 1;
                $prioridadeB = $prioridades[$b['prioridade']] ?? 1;
                
                if ($prioridadeA === $prioridadeB) {
                    return $b['data']->timestamp - $a['data']->timestamp;
                }
                
                return $prioridadeB - $prioridadeA;
            });

            return response()->json([
                'success' => true,
                'notificacoes' => $notificacoes,
                'total' => count($notificacoes),
                'nao_lidas' => count(array_filter($notificacoes, function($n) { 
                    return $n['prioridade'] === 'alta'; 
                }))
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar notificações', [
                'user_id' => \Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar notificações.'
            ], 500);
        }
    }

    /**
     * Excluir proposição (apenas rascunhos)
     */
    public function destroy($proposicaoId)
    {
        \Log::info('Iniciando exclusão de proposição', [
            'proposicao_id' => $proposicaoId,
            'user_id' => \Auth::id(),
            'request_method' => request()->method()
        ]);
        
        try {
            // Buscar proposição no banco de dados
            $proposicao = Proposicao::find($proposicaoId);
            
            // Verificar se a proposição existe
            if (!$proposicao) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposição não encontrada.'
                ], 404);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar proposição para exclusão', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            // Fallback: verificar se existem dados na sessão
            $sessionData = session('proposicao_' . $proposicaoId . '_tipo');
            if (!$sessionData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposição não encontrada.'
                ], 404);
            }
            
            // Usar dados da sessão como fallback
            $proposicao = (object) [
                'id' => $proposicaoId,
                'status' => session('proposicao_' . $proposicaoId . '_status', 'rascunho'),
                'autor_id' => \Auth::id(), // Assumir que é do usuário atual
                'arquivo_path' => null
            ];
            
            \Log::info('Usando dados da sessão para exclusão (fallback)', [
                'proposicao_id' => $proposicaoId,
                'user_id' => \Auth::id()
            ]);
        }
        
        // Verificar se é rascunho
        \Log::info('Verificando status da proposição', [
            'proposicao_id' => $proposicaoId,
            'status' => $proposicao->status,
            'autor_id' => $proposicao->autor_id,
            'current_user' => \Auth::id()
        ]);
        
        // Permitir exclusão de rascunhos, proposições em edição, salvando e retornadas do legislativo
        $statusPermitidos = ['rascunho', 'em_edicao', 'salvando', 'retornado_legislativo'];
        if (!in_array($proposicao->status, $statusPermitidos)) {
            \Log::warning('Tentativa de excluir proposição com status não permitido', [
                'proposicao_id' => $proposicaoId,
                'status' => $proposicao->status,
                'user_id' => \Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Apenas rascunhos, proposições em edição, salvando e retornadas do legislativo podem ser excluídas.'
            ], 400);
        }
        
        // Verificar se o usuário é o autor
        if ($proposicao->autor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir esta proposição.'
            ], 403);
        }
        
        try {
            // Excluir arquivos associados se existirem
            if ($proposicao->arquivo_path && \Storage::disk('public')->exists($proposicao->arquivo_path)) {
                \Storage::disk('public')->delete($proposicao->arquivo_path);
            }
            
            // Tentar excluir do banco de dados se for um modelo Eloquent real
            if (is_a($proposicao, \App\Models\Proposicao::class)) {
                $proposicao->delete();
                $method = 'database_deletion';
            } else {
                // Fallback: limpar dados da sessão
                $sessionKeys = [
                    'proposicao_' . $proposicaoId . '_tipo',
                    'proposicao_' . $proposicaoId . '_ementa',
                    'proposicao_' . $proposicaoId . '_conteudo',
                    'proposicao_' . $proposicaoId . '_status',
                    'proposicao_' . $proposicaoId . '_modelo_id',
                    'proposicao_' . $proposicaoId . '_template_id',
                    'proposicao_' . $proposicaoId . '_variaveis_template',
                    'proposicao_' . $proposicaoId . '_conteudo_processado'
                ];
                
                foreach ($sessionKeys as $key) {
                    session()->forget($key);
                }
                $method = 'session_cleanup';
            }
            
            \Log::info('Proposição excluída', [
                'proposicao_id' => $proposicaoId,
                'user_id' => Auth::id(),
                'method' => $method
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Proposição excluída com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir proposição', [
                'proposicao_id' => $proposicaoId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Obter cargo do parlamentar
     */
    private function obterCargoParlamentar($user)
    {
        if (!$user) return '[CARGO DO PARLAMENTAR]';
        
        // Verificar roles
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames();
            
            if ($roles->contains('Vereador')) return 'Vereador';
            if ($roles->contains('Presidente')) return 'Presidente da Câmara';
            if ($roles->contains('Vice-Presidente')) return 'Vice-Presidente da Câmara';
            if ($roles->contains('Secretario')) return 'Secretário da Câmara';
        }
        
        return 'Parlamentar';
    }
    
    /**
     * Obter partido do parlamentar
     */
    private function obterPartidoParlamentar($user)
    {
        if (!$user) return '[PARTIDO]';
        
        // Verificar se existe campo partido no usuário
        if (isset($user->partido)) {
            return $user->partido;
        }
        
        // Verificar se existe relação com modelo Parlamentar
        if (method_exists($user, 'parlamentar') && $user->parlamentar) {
            return $user->parlamentar->partido ?? '[PARTIDO]';
        }
        
        return '[PARTIDO]';
    }
    
    /**
     * Formatar tipo de proposição
     */
    private function formatarTipoProposicao($tipo)
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução',
            'mocao' => 'Moção'
        ];
        
        return $tipos[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
    }
    
    /**
     * Formatar data por extenso
     */
    private function formatarDataExtenso($data)
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];
        
        return $data->day . ' de ' . $meses[$data->month] . ' de ' . $data->year;
    }
    
    /**
     * Gerar número da proposição
     */
    private function gerarNumeroProposicao($proposicao)
    {
        if (isset($proposicao->numero) && $proposicao->numero) {
            return $proposicao->numero;
        }
        
        // Gerar número baseado no ID e ano
        $id = is_object($proposicao) ? $proposicao->id : $proposicao;
        $ano = date('Y');
        
        return sprintf('%04d/%d', $id, $ano);
    }

    /**
     * Processar template do modelo com os dados preenchidos
     */
    private function processarTemplate($modelo, array $dados): string
    {
        // TODO: Implement proper template processing
        $template = $modelo->template ?? 'Template padrão: {{conteudo}}';
        
        // Substituir placeholders pelos dados preenchidos
        foreach ($dados as $campo => $valor) {
            $placeholder = "{{" . $campo . "}}";
            $template = str_replace($placeholder, $valor, $template);
        }
        
        return $template;
    }

    /**
     * Criar arquivo específico da proposição baseado no template
     */
    private function criarArquivoProposicao($proposicaoId, $template)
    {
        try {
            // Definir nome do arquivo da proposição (usar DOCX)
            $templateIdForFile = $template ? $template->id : 'blank';
            $nomeArquivo = "proposicao_{$proposicaoId}_template_{$templateIdForFile}.rtf";
            $pathDestino = "proposicoes/{$nomeArquivo}";
            $pathCompleto = storage_path('app/public/' . $pathDestino);
            
            // IMPORTANTE: Verificar se já existe um arquivo salvo para esta proposição
            // Se existir, usar o arquivo existente ao invés de criar um novo
            if (\Storage::disk('public')->exists($pathDestino)) {
                \Log::info('Arquivo da proposição já existe, usando arquivo salvo', [
                    'proposicao_id' => $proposicaoId,
                    'arquivo_existente' => $pathDestino,
                    'tamanho' => \Storage::disk('public')->size($pathDestino)
                ]);
                return $pathDestino;
            }
            
            // Garantir que o diretório existe
            $diretorio = dirname($pathCompleto);
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            // Se o template tem um arquivo, copiar como base
            \Log::info('Criando novo arquivo para proposição', [
                'proposicao_id' => $proposicaoId,
                'template_exists' => $template ? 'sim' : 'não',
                'template_id' => $template ? $template->id : null,
                'arquivo_path' => $template ? $template->arquivo_path : null
            ]);
            
            if ($template && $template->arquivo_path) {
                // Processar template com variáveis substituídas
                $this->processarTemplateComVariaveis($proposicaoId, $template, $pathDestino);
            } else {
                // Criar arquivo básico com dados preenchidos pelo usuário
                // Buscar dados da proposição do banco de dados ou sessão
                $proposicao = Proposicao::find($proposicaoId);
                $ementa = $proposicao->ementa ?? session('proposicao_' . $proposicaoId . '_ementa', 'Proposição em elaboração');
                $conteudo = $proposicao->conteudo ?? session('proposicao_' . $proposicaoId . '_conteudo', 'Conteúdo da proposição a ser desenvolvido.');
                $tipo = $proposicao->tipo ?? session('proposicao_' . $proposicaoId . '_tipo', 'mocao');
                
                // Usar conteúdo processado se disponível, caso contrário usar texto básico
                $textoCompleto = session('proposicao_' . $proposicaoId . '_conteudo_processado');
                if (!$textoCompleto) {
                    $textoCompleto = "PROPOSIÇÃO - " . strtoupper($tipo) . "\n\n";
                    $textoCompleto .= "EMENTA\n\n";
                    $textoCompleto .= $ementa . "\n\n";
                    $textoCompleto .= "CONTEÚDO\n\n";
                    $textoCompleto .= $conteudo;
                }
                
                // Criar arquivo DOCX usando RTF
                $conteudoDocx = $this->criarArquivoDocx($textoCompleto);
                \Storage::disk('public')->put($pathDestino, $conteudoDocx);
                \Log::info('Arquivo DOCX criado a partir do texto gerado', [
                    'proposicao_path' => $pathDestino,
                    'tamanho_arquivo' => strlen($conteudoDocx),
                    'arquivo_existe_apos_criacao' => \Storage::disk('public')->exists($pathDestino),
                    'ementa' => $ementa,
                    'tipo' => $tipo
                ]);
            }
            
            return $pathDestino;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar arquivo da proposição', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Processar template com substituição de variáveis
     */
    private function processarTemplateComVariaveis($proposicaoId, $template, $pathDestino)
    {
        try {
            // Buscar proposição
            $proposicao = Proposicao::find($proposicaoId);
            
            // Buscar variáveis preenchidas pelo parlamentar da sessão
            $variaveisPreenchidas = session('proposicao_' . $proposicaoId . '_variaveis_template', []);
            
            // DEBUG: Vamos também buscar da proposição diretamente
            $variaveisProposicao = [
                'ementa' => $proposicao->ementa,
                'texto' => $proposicao->conteudo,
                'conteudo' => $proposicao->conteudo
            ];
            
            // Se não há variáveis na sessão, usar as da proposição
            if (empty($variaveisPreenchidas)) {
                $variaveisPreenchidas = $variaveisProposicao;
            }
            
            \Log::info('Processando template com variáveis', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $template->id,
                'variaveis_preenchidas' => $variaveisPreenchidas,
                'variaveis_proposicao' => $variaveisProposicao,
                'session_key' => 'proposicao_' . $proposicaoId . '_variaveis_template'
            ]);
            
            // Verificar se existe arquivo físico do template com sistema de fallback
            $conteudoTemplate = $this->carregarTemplateComFallback($template);
            
            if ($conteudoTemplate) {
                \Log::info('Template carregado com sucesso', [
                    'tamanho_arquivo' => strlen($conteudoTemplate),
                    'contem_variaveis' => strpos($conteudoTemplate, '${') !== false
                ]);
                
                // Verificar se o template tem variáveis válidas
                $this->validarEBackupTemplate($template, $conteudoTemplate);
                
                // Processar variáveis no template
                $templateProcessorService = app(\App\Services\Template\TemplateProcessorService::class);
                
                // Usar o serviço para processar o template (mas ele funciona com texto, não RTF binário)
                // Por isso vamos fazer substituição direta no RTF
                $conteudoProcessado = $this->substituirVariaveisRTF(
                    $conteudoTemplate, 
                    $proposicao, 
                    $variaveisPreenchidas
                );
                
                // Salvar arquivo processado
                \Storage::disk('public')->put($pathDestino, $conteudoProcessado);
                
                // MANTER COMO RTF para preservar formatação - OnlyOffice trabalha bem com RTF
                // A conversão para DOCX estava removendo toda formatação
                $pathCompleto = storage_path('app/public/' . $pathDestino);
                // $this->converterRTFParaDOCX($pathCompleto); // Comentado para preservar formatação
                
                \Log::info('Template processado com variáveis substituídas', [
                    'template_path' => $template->arquivo_path,
                    'proposicao_path' => $pathDestino,
                    'tamanho_original' => strlen($conteudoTemplate),
                    'tamanho_processado' => strlen($conteudoProcessado),
                    'arquivo_existe_apos_processamento' => \Storage::disk('public')->exists($pathDestino)
                ]);
            } else {
                \Log::warning('Arquivo do template não encontrado para processamento', [
                    'template_path' => $template->arquivo_path,
                    'existe_local' => \Storage::disk('local')->exists($template->arquivo_path),
                    'existe_public' => \Storage::disk('public')->exists($template->arquivo_path)
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao processar template com variáveis', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Carregar template com sistema de fallback
     */
    private function carregarTemplateComFallback($template)
    {
        $conteudoTemplate = null;
        
        // Tentar carregar o template principal
        if (\Storage::disk('local')->exists($template->arquivo_path)) {
            $conteudoTemplate = \Storage::disk('local')->get($template->arquivo_path);
        } elseif (\Storage::disk('public')->exists($template->arquivo_path)) {
            $conteudoTemplate = \Storage::disk('public')->get($template->arquivo_path);
        }
        
        // Garantir que o conteúdo está em UTF-8
        if ($conteudoTemplate && !mb_check_encoding($conteudoTemplate, 'UTF-8')) {
            $conteudoTemplate = mb_convert_encoding($conteudoTemplate, 'UTF-8', 'auto');
        }
        
        // Validar se o template contém variáveis essenciais
        if ($conteudoTemplate && $this->validarConteudoTemplateBasico($conteudoTemplate)) {
            return $conteudoTemplate;
        }
        
        // Template principal está corrompido ou não contém variáveis, tentar fallbacks
        \Log::warning('Template principal corrompido, tentando fallbacks', [
            'template_id' => $template->id,
            'arquivo_path' => $template->arquivo_path,
            'conteudo_existe' => $conteudoTemplate !== null,
            'tamanho_conteudo' => $conteudoTemplate ? strlen($conteudoTemplate) : 0
        ]);
        
        // Fallback 1: Tentar backup mais recente
        $conteudoBackup = $this->carregarBackupMaisRecente($template);
        if ($conteudoBackup && $this->validarConteudoTemplateBasico($conteudoBackup)) {
            \Log::info('Template restaurado do backup', [
                'template_id' => $template->id
            ]);
            
            // Restaurar o template principal com o backup
            if ($template->arquivo_path) {
                \Storage::disk('local')->put($template->arquivo_path, $conteudoBackup);
            }
            
            return $conteudoBackup;
        }
        
        // Fallback 2: Usar template padrão baseado no tipo de proposição
        $templatePadrao = $this->obterTemplatePadrao($template->tipoProposicao->nome ?? 'mocao');
        if ($templatePadrao) {
            \Log::info('Usando template padrão como fallback', [
                'template_id' => $template->id,
                'tipo_proposicao' => $template->tipoProposicao->nome ?? 'mocao'
            ]);
            
            // Salvar template padrão como novo template
            if ($template->arquivo_path) {
                \Storage::disk('local')->put($template->arquivo_path, $templatePadrao);
            }
            
            return $templatePadrao;
        }
        
        // Fallback 3: Template mínimo de emergência
        \Log::error('Usando template de emergência', [
            'template_id' => $template->id
        ]);
        
        return $this->obterTemplateEmergencia();
    }
    
    /**
     * Validar se template contém variáveis básicas essenciais
     */
    private function validarConteudoTemplateBasico($conteudo)
    {
        if (!$conteudo || strlen($conteudo) < 50) {
            return false;
        }
        
        // Verificar se contém pelo menos uma variável (mais flexível)
        $temVariavel = preg_match('/\$\{[^}]+\}/', $conteudo);
        
        // Se não tem variáveis, verificar se tem conteúdo significativo (possíveis imagens)
        if (!$temVariavel) {
            // Arquivo grande pode conter imagens e ainda ser válido
            if (strlen($conteudo) > 100000) {
                \Log::info('Template sem variáveis mas com conteúdo significativo aceito no fallback', [
                    'tamanho_conteudo' => strlen($conteudo)
                ]);
                return true;
            }
            return false;
        }
        
        return true; // Tem pelo menos uma variável
    }
    
    /**
     * Carregar backup mais recente do template
     */
    private function carregarBackupMaisRecente($template)
    {
        try {
            if (!$template->arquivo_path) {
                return null;
            }
            
            $templateBaseName = pathinfo($template->arquivo_path, PATHINFO_FILENAME);
            $templateDir = dirname($template->arquivo_path);
            
            // Buscar backups
            $arquivos = \Storage::disk('local')->files($templateDir);
            $backupsDoTemplate = array_filter($arquivos, function($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName . '_backup_') === 0;
            });
            
            if (empty($backupsDoTemplate)) {
                return null;
            }
            
            // Ordenar por data (mais recente primeiro)
            usort($backupsDoTemplate, function($a, $b) {
                return \Storage::disk('local')->lastModified($b) - \Storage::disk('local')->lastModified($a);
            });
            
            return \Storage::disk('local')->get($backupsDoTemplate[0]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar backup', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Obter template padrão baseado no tipo de proposição
     */
    private function obterTemplatePadrao($tipoProposicao)
    {
        $templates = [
            'mocao' => '{\rtf1\ansi\ansicpg1252\deff0\deflang1046 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 MOÇÃO Nº ${numero_proposicao}\par}
\par
{\qc Data: ${data_atual}\par}
{\qc Autor: ${autor_nome}\par}
{\qc Município: ${municipio}\par}
\par
\par
{\b EMENTA:\par}
\par
${ementa}
\par
\par
{\b TEXTO:\par}
\par
${texto}
\par
\par
{\qr Câmara Municipal de ${municipio}\par}
{\qr ${data_atual}\par}
}',
            'projeto_lei_ordinaria' => '{\rtf1\ansi\ansicpg1252\deff0\deflang1046 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 PROJETO DE LEI ORDINÁRIA Nº ${numero_proposicao}\par}
\par
{\qc Data: ${data_atual}\par}
{\qc Autor: ${autor_nome}\par}
{\qc Município: ${municipio}\par}
\par
\par
{\b EMENTA:\par}
\par
${ementa}
\par
\par
{\b TEXTO:\par}
\par
${texto}
\par
\par
{\qr Câmara Municipal de ${municipio}\par}
{\qr ${data_atual}\par}
}'
        ];
        
        return $templates[$tipoProposicao] ?? $templates['mocao'];
    }
    
    /**
     * Template mínimo de emergência
     */
    private function obterTemplateEmergencia()
    {
        return '{\rtf1\ansi\ansicpg1252\deff0\deflang1046 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 ${tipo_proposicao} Nº ${numero_proposicao}\par}
\par
{\qc Data: ${data_atual}\par}
{\qc Autor: ${autor_nome}\par}
\par
\par
{\b EMENTA:\par}
\par
${ementa}
\par
\par
{\b TEXTO:\par}
\par
${texto}
\par
\par
{\qr ${municipio}, ${data_atual}\par}
}';
    }
    
    /**
     * Validar template e fazer backup se estiver funcionando
     */
    private function validarEBackupTemplate($template, $conteudoTemplate)
    {
        try {
            // Usar validação flexível - aceita templates com variáveis OU conteúdo significativo (imagens)
            $temVariaveisEssenciais = preg_match('/\$\{[^}]+\}/', $conteudoTemplate);
            $temConteudoSignificativo = strlen($conteudoTemplate) > 100000; // >100KB indica imagens
            
            if ($temVariaveisEssenciais || $temConteudoSignificativo) {
                // Template está válido, fazer backup
                $backupPath = str_replace('.rtf', '_backup_' . date('Y_m_d_His') . '.rtf', $template->arquivo_path);
                
                // Manter apenas os 5 backups mais recentes
                $this->limparBackupsAntigos($template);
                
                // Salvar backup
                \Storage::disk('local')->put($backupPath, $conteudoTemplate);
                
                \Log::info('Backup do template criado', [
                    'template_id' => $template->id,
                    'backup_path' => $backupPath,
                    'variaveis_encontradas' => $this->extrairVariaveisTemplate($conteudoTemplate)
                ]);
            } else {
                \Log::warning('Template não contém variáveis essenciais', [
                    'template_id' => $template->id,
                    'variaveis_faltando' => ['${ementa}', '${texto}', '${numero_proposicao}']
                ]);
                
                // Tentar restaurar do backup mais recente
                $this->tentarRestaurarTemplate($template);
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao validar template', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Limpar backups antigos, mantendo apenas os 5 mais recentes
     */
    private function limparBackupsAntigos($template)
    {
        try {
            $templateBaseName = pathinfo($template->arquivo_path, PATHINFO_FILENAME);
            $templateDir = dirname($template->arquivo_path);
            
            // Buscar todos os backups existentes
            $backups = \Storage::disk('local')->files($templateDir);
            $backupsDoTemplate = array_filter($backups, function($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName . '_backup_') === 0;
            });
            
            // Ordenar por data (mais recente primeiro)
            usort($backupsDoTemplate, function($a, $b) {
                return \Storage::disk('local')->lastModified($b) - \Storage::disk('local')->lastModified($a);
            });
            
            // Remover backups além dos 5 mais recentes
            if (count($backupsDoTemplate) >= 5) {
                $backupsParaRemover = array_slice($backupsDoTemplate, 4);
                foreach ($backupsParaRemover as $backup) {
                    \Storage::disk('local')->delete($backup);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao limpar backups antigos', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tentar restaurar template do backup mais recente
     */
    private function tentarRestaurarTemplate($template)
    {
        try {
            $templateBaseName = pathinfo($template->arquivo_path, PATHINFO_FILENAME);
            $templateDir = dirname($template->arquivo_path);
            
            // Buscar backup mais recente
            $backups = \Storage::disk('local')->files($templateDir);
            $backupsDoTemplate = array_filter($backups, function($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName . '_backup_') === 0;
            });
            
            if (!empty($backupsDoTemplate)) {
                // Ordenar por data (mais recente primeiro)
                usort($backupsDoTemplate, function($a, $b) {
                    return \Storage::disk('local')->lastModified($b) - \Storage::disk('local')->lastModified($a);
                });
                
                $backupMaisRecente = $backupsDoTemplate[0];
                $conteudoBackup = \Storage::disk('local')->get($backupMaisRecente);
                
                // Restaurar o template
                \Storage::disk('local')->put($template->arquivo_path, $conteudoBackup);
                
                \Log::info('Template restaurado do backup', [
                    'template_id' => $template->id,
                    'backup_usado' => $backupMaisRecente,
                    'data_backup' => \Storage::disk('local')->lastModified($backupMaisRecente)
                ]);
                
                return true;
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao restaurar template do backup', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }
    
    /**
     * Extrair variáveis do template para logging
     */
    private function extrairVariaveisTemplate($conteudo)
    {
        $variaveis = [];
        if (preg_match_all('/\$\{([^}]+)\}/', $conteudo, $matches)) {
            $variaveis = array_unique($matches[1]);
        }
        return $variaveis;
    }
    
    /**
     * Substituir variáveis diretamente no arquivo RTF
     */
    private function substituirVariaveisRTF($conteudoRTF, $proposicao, $variaveisPreenchidas)
    {
        // Obter as proposição real do banco de dados se ela for um objeto simples
        if (!($proposicao instanceof \App\Models\Proposicao)) {
            $proposicaoModel = \App\Models\Proposicao::find($proposicao->id);
            if ($proposicaoModel) {
                $proposicao = $proposicaoModel;
            }
        }
        
        // Não usar o TemplateProcessorService aqui pois causa conflito
        // Vamos definir as variáveis diretamente
        
        // Obter variáveis específicas
        $user = \Auth::user();
        $agora = \Carbon\Carbon::now();
        
        $variaveisSystem = [
            // Datas e horários
            'data' => $agora->format('d/m/Y'),
            'data_atual' => $agora->format('d/m/Y'),
            'data_extenso' => $this->formatarDataExtenso($agora),
            'mes_atual' => $agora->format('m'),
            'ano_atual' => $agora->format('Y'),
            'dia_atual' => $agora->format('d'),
            'hora_atual' => $agora->format('H:i'),
            'data_criacao' => $proposicao->created_at?->format('d/m/Y') ?? $agora->format('d/m/Y'),
            
            // Proposição
            'numero_proposicao' => $this->gerarNumeroProposicao($proposicao),
            'tipo_proposicao' => $proposicao->tipo_formatado ?? $this->formatarTipoProposicao($proposicao->tipo ?? 'mocao'),
            'status_proposicao' => $proposicao->status ?? 'rascunho',
            
            // Parlamentar / Autor
            'nome_parlamentar' => $user->name ?? '[NOME DO PARLAMENTAR]',
            'autor_nome' => $proposicao->autor->name ?? $user->name ?? '[NOME DO AUTOR]',
            'cargo_parlamentar' => $this->obterCargoParlamentar($user),
            'email_parlamentar' => $user->email ?? '[EMAIL DO PARLAMENTAR]',
            'partido_parlamentar' => $this->obterPartidoParlamentar($user),
            
            // Instituição
            'nome_municipio' => config('app.municipio', 'São Paulo'),
            'municipio' => config('app.municipio', 'São Paulo'),
            'nome_camara' => config('app.nome_camara', 'Câmara Municipal'),
            'endereco_camara' => config('app.endereco_camara', 'Endereço da Câmara'),
            'legislatura_atual' => config('app.legislatura', '2021-2024'),
            'sessao_legislativa' => $agora->format('Y')
        ];
        
        // Combinar variáveis do sistema e preenchidas pelo parlamentar
        $todasVariaveis = array_merge($variaveisSystem, $variaveisPreenchidas);
        
        // DEBUG: Log das variáveis encontradas no template
        $variaveisNoTemplate = [];
        
        // Tentar diferentes padrões de regex para encontrar variáveis
        $patterns = [
            '/\$\{([^}]+)\}/',  // Padrão normal: ${variavel}
            '/\$\\\\{([^}]+)\\\\}/', // Padrão com escape de RTF
            '/\\\$\\\{([^}]+)\\\}/', // Padrão com escape duplo
        ];
        
        // Primeiro processar variáveis normais
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $conteudoRTF, $matches)) {
                $variaveisNoTemplate = array_merge($variaveisNoTemplate, $matches[1]);
            }
        }
        
        // Criar lista de variáveis já encontradas no formato normal
        $variaveisNormaisEncontradas = array_unique($variaveisNoTemplate);
        
        // Também buscar variáveis codificadas em Unicode (formato OnlyOffice) 
        // Padrão: sequências que começam com \u36* ($ em Unicode) seguidas de \u123* ({ em Unicode)
        // Exemplo: \u36*\u123*\u116*\u105*\u112*\u111*... = ${tipo_...
        if (preg_match_all('/\\\\u36\*\\\\u123\*(?:\\\\u\d+\*)+\\\\u125\*/', $conteudoRTF, $unicodeMatches)) {
            \Log::info('Variáveis Unicode encontradas', [
                'quantidade' => count($unicodeMatches[0]),
                'sequencias' => array_slice($unicodeMatches[0], 0, 3) // Log primeiras 3
            ]);
            
            foreach ($unicodeMatches[0] as $unicodeSequence) {
                // Decodificar sequência Unicode completa para texto
                $decoded = $this->decodificarUnicodeRTF($unicodeSequence);
                \Log::info('Decodificação Unicode', [
                    'sequencia' => substr($unicodeSequence, 0, 100) . '...',
                    'decodificado' => $decoded
                ]);
                
                if ($decoded && strpos($decoded, '${') === 0 && substr($decoded, -1) === '}') {
                    // Extrair nome da variável (remover ${ e })
                    $nomeVariavel = substr($decoded, 2, -1);
                    if ($nomeVariavel && !in_array($nomeVariavel, $variaveisNormaisEncontradas)) {
                        \Log::info('Variável Unicode adicionada', ['variavel' => $nomeVariavel]);
                        $variaveisNoTemplate[] = $nomeVariavel;
                    }
                }
            }
        }
        
        // Remover duplicatas
        $variaveisNoTemplate = array_unique($variaveisNoTemplate);
        
        \Log::info('DEBUG: Análise de variáveis no template', [
            'variaveis_no_template' => $variaveisNoTemplate,
            'variaveis_disponiveis' => array_keys($todasVariaveis),
            'template_preview' => substr($conteudoRTF, 0, 500) . '...',
            'template_contém_dollar' => strpos($conteudoRTF, '$') !== false,
            'template_contém_chaves' => strpos($conteudoRTF, '{') !== false,
            'template_busca_manual_ementa' => strpos($conteudoRTF, '${ementa}') !== false,
            'template_busca_manual_texto' => strpos($conteudoRTF, '${texto}') !== false
        ]);
        
        // Sempre tentar substituir variáveis primeiro
        $conteudoProcessado = $conteudoRTF;
        $substituicoes = 0;
        $detalhesSubstituicoes = [];
        
        // Ordenar variáveis por tamanho decrescente para evitar substituições parciais
        // Ex: substituir 'data_atual' antes de 'data' para evitar '27/07/2025_atual'
        uksort($todasVariaveis, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        foreach ($todasVariaveis as $variavel => $valor) {
            // Tentar diferentes formatos de placeholder
            $placeholders = [
                '${' . $variavel . '}',  // Formato normal com chaves
                '$' . $variavel,  // Formato simples sem chaves
                '$\\{' . $variavel . '\\}', // Com escape RTF
                '\${' . $variavel . '}', // Com escape simples
            ];
            
            // Adicionar placeholder para formato Unicode apenas se não há versão normal
            $unicodePlaceholder = $this->codificarVariavelParaUnicode('${' . $variavel . '}');
            if ($unicodePlaceholder && strpos($conteudoProcessado, $unicodePlaceholder) !== false) {
                // Verificar se não existe versão normal da mesma variável
                $temVersaoNormal = false;
                foreach (['${' . $variavel . '}', '$' . $variavel, '$\\{' . $variavel . '\\}', '\${' . $variavel . '}'] as $normalPlaceholder) {
                    if (strpos($conteudoProcessado, $normalPlaceholder) !== false) {
                        $temVersaoNormal = true;
                        break;
                    }
                }
                
                if (!$temVersaoNormal) {
                    $placeholders[] = $unicodePlaceholder;
                }
            }
            
            $substituicoesVariavel = 0;
            foreach ($placeholders as $placeholder) {
                $antes = substr_count($conteudoProcessado, $placeholder);
                
                // Para placeholders sem chaves, usar substituição com word boundary
                if ($placeholder === '$' . $variavel) {
                    // Usar regex para garantir que não substitui parcialmente
                    $pattern = '/\$' . preg_quote($variavel, '/') . '(?![a-zA-Z_])/';
                    $conteudoProcessado = preg_replace($pattern, $valor, $conteudoProcessado);
                    $depois = substr_count($conteudoProcessado, $placeholder);
                } else if (strpos($placeholder, '\\u') !== false) {
                    // Se é um placeholder Unicode, usar valor convertido para RTF Unicode
                    $valorRtf = $this->codificarTextoParaUnicode($valor);
                    $conteudoProcessado = str_replace($placeholder, $valorRtf, $conteudoProcessado);
                    $depois = substr_count($conteudoProcessado, $placeholder);
                } else {
                    // Substituição normal - converter valor para RTF para evitar problemas de encoding
                    $valorRtf = $this->converterUtf8ParaRtf($valor);
                    $conteudoProcessado = str_replace($placeholder, $valorRtf, $conteudoProcessado);
                    $depois = substr_count($conteudoProcessado, $placeholder);
                }
                
                $substituicoesVariavel += ($antes - $depois);
            }
            
            $substituicoes += $substituicoesVariavel;
            
            if ($substituicoesVariavel > 0) {
                $detalhesSubstituicoes[$variavel] = [
                    'placeholders' => $placeholders,
                    'valor' => substr($valor, 0, 50) . '...',
                    'substituicoes' => $substituicoesVariavel
                ];
            }
        }
        
        \Log::info('DEBUG: Detalhes das substituições', [
            'detalhes' => $detalhesSubstituicoes,
            'total_substituicoes' => $substituicoes
        ]);
        
        \Log::info('Substituições realizadas', [
            'total_substituicoes' => $substituicoes,
            'tinha_variaveis_antes' => strpos($conteudoRTF, '${') !== false,
            'tem_variaveis_depois' => strpos($conteudoProcessado, '${') !== false
        ]);
        
        // Se não houve substituições e ainda não há conteúdo significativo, adicionar ao final
        // IMPORTANTE: Não adicionar conteúdo se o template já tem conteúdo significativo (imagens, texto longo)
        $temConteudoSignificativo = strlen($conteudoRTF) > 100000; // Templates com imagens são grandes
        
        if ($temConteudoSignificativo && $substituicoes === 0) {
            \Log::info('Template tem conteúdo significativo mas sem variáveis - mantendo conteúdo original sem adicionar conteúdo extra', [
                'tamanho_template' => strlen($conteudoRTF),
                'tem_variaveis' => strpos($conteudoRTF, '${') !== false
            ]);
        }
        
        if ($substituicoes === 0 && strpos($conteudoRTF, '${') === false && !$temConteudoSignificativo) {
            \Log::info('Template não tinha variáveis nem conteúdo significativo, adicionando conteúdo estruturado ao final');
            
            // Encontrar a posição antes do fechamento do RTF
            $posicaoFinal = strrpos($conteudoProcessado, '}');
            if ($posicaoFinal !== false) {
                $conteudoAntes = substr($conteudoProcessado, 0, $posicaoFinal);
                
                // Adicionar conteúdo formatado em RTF
                $conteudoAdicional = '\\par\\par';
                $conteudoAdicional .= '{\\qc\\b\\fs32 MOÇÃO Nº ' . $variaveisSystem['numero_proposicao'] . '\\par}';
                $conteudoAdicional .= '\\par';
                $conteudoAdicional .= '{\\qc Data: ' . $variaveisSystem['data_atual'] . '\\par}';
                $conteudoAdicional .= '{\\qc Autor: ' . $variaveisSystem['autor_nome'] . '\\par}';
                $conteudoAdicional .= '{\\qc Município: ' . $variaveisSystem['municipio'] . '\\par}';
                $conteudoAdicional .= '\\par\\par';
                $conteudoAdicional .= '{\\b\\fs28 EMENTA\\par}';
                $conteudoAdicional .= '\\par';
                $conteudoAdicional .= ($variaveisPreenchidas['ementa'] ?? '[EMENTA NÃO PREENCHIDA]');
                $conteudoAdicional .= '\\par\\par';
                $conteudoAdicional .= '{\\b\\fs28 TEXTO\\par}';
                $conteudoAdicional .= '\\par';
                $conteudoAdicional .= ($variaveisPreenchidas['texto'] ?? '[TEXTO NÃO PREENCHIDO]');
                $conteudoAdicional .= '\\par\\par\\par';
                $conteudoAdicional .= '{\\qr Câmara Municipal de ' . $variaveisSystem['municipio'] . '\\par}';
                $conteudoAdicional .= '{\\qr ' . $variaveisSystem['data_atual'] . '\\par}';
                
                $conteudoProcessado = $conteudoAntes . $conteudoAdicional . '}';
            }
        }
        
        \Log::info('Variáveis processadas no RTF', [
            'tem_variaveis_predefinidas' => strpos($conteudoRTF, '${') !== false,
            'variaveis_disponiveis' => array_keys($todasVariaveis),
            'valores_exemplo' => [
                'ementa' => substr($variaveisPreenchidas['ementa'] ?? '[não definida]', 0, 50) . '...',
                'texto' => substr($variaveisPreenchidas['texto'] ?? '[não definido]', 0, 50) . '...',
                'autor_nome' => $variaveisSystem['autor_nome'],
                'data_atual' => $variaveisSystem['data_atual']
            ]
        ]);
        
        return $conteudoProcessado;
    }
    
    /**
     * Decodificar sequência Unicode do RTF para texto
     */
    private function decodificarSequenciaUnicode($sequenciaUnicode)
    {
        try {
            // Extrair códigos Unicode da sequência
            // Exemplo: \u36*\u116*\u101*\u120*\u116*\u111* 
            preg_match_all('/\\\\u(\d+)\*/', $sequenciaUnicode, $matches);
            
            if (empty($matches[1])) {
                return null;
            }
            
            $texto = '';
            foreach ($matches[1] as $codigo) {
                $texto .= chr((int)$codigo);
            }
            
            return $texto;
            
        } catch (\Exception $e) {
            \Log::warning('Erro ao decodificar sequência Unicode', [
                'sequencia' => $sequenciaUnicode,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Codificar variável para formato Unicode do RTF
     */
    private function codificarVariavelParaUnicode($variavel)
    {
        try {
            $sequencia = '';
            for ($i = 0; $i < strlen($variavel); $i++) {
                $char = $variavel[$i];
                $codigo = ord($char);
                $sequencia .= '\\u' . $codigo . '*';
            }
            return $sequencia;
            
        } catch (\Exception $e) {
            \Log::warning('Erro ao codificar variável para Unicode', [
                'variavel' => $variavel,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Codificar texto para formato Unicode do RTF (para valores)
     */
    private function codificarTextoParaUnicode($texto)
    {
        try {
            \Log::info('Codificando texto para Unicode RTF', [
                'texto_original' => mb_substr($texto, 0, 100),
                'length' => mb_strlen($texto, 'UTF-8')
            ]);
            
            // Para texto longo, usar formato misto: caracteres especiais em Unicode, texto normal como está
            $textoProcessado = '';
            $chunks = explode("\n", $texto); // Preservar quebras de linha
            
            foreach ($chunks as $index => $chunk) {
                if ($index > 0) {
                    $textoProcessado .= '\\par '; // Quebra de linha em RTF
                }
                
                // Para cada chunk, processar caracteres especiais usando mb_* functions para UTF-8
                $length = mb_strlen($chunk, 'UTF-8');
                for ($i = 0; $i < $length; $i++) {
                    $char = mb_substr($chunk, $i, 1, 'UTF-8');
                    $codepoint = mb_ord($char, 'UTF-8');
                    
                    // Converter apenas caracteres especiais para Unicode, manter ASCII normal
                    if ($codepoint > 127) {
                        $textoProcessado .= '\\u' . $codepoint . '*';
                    } else {
                        $textoProcessado .= $char;
                    }
                }
            }
            
            \Log::info('Texto codificado para Unicode RTF', [
                'amostra_resultado' => mb_substr($textoProcessado, 0, 200),
                'tamanho_final' => strlen($textoProcessado)
            ]);
            
            return $textoProcessado;
            
        } catch (\Exception $e) {
            \Log::warning('Erro ao codificar texto para Unicode RTF', [
                'texto_inicio' => mb_substr($texto, 0, 50) . '...',
                'error' => $e->getMessage()
            ]);
            return $texto; // Fallback para texto original
        }
    }
    
    /**
     * Converter texto UTF-8 para RTF com escape sequences
     */
    private function utf8ToRtf($texto)
    {
        // Mapa de caracteres especiais do português para RTF
        $charMap = [
            'À' => '\\\'c0', 'Á' => '\\\'c1', 'Â' => '\\\'c2', 'Ã' => '\\\'c3',
            'Ä' => '\\\'c4', 'Å' => '\\\'c5', 'Æ' => '\\\'c6', 'Ç' => '\\\'c7',
            'È' => '\\\'c8', 'É' => '\\\'c9', 'Ê' => '\\\'ca', 'Ë' => '\\\'cb',
            'Ì' => '\\\'cc', 'Í' => '\\\'cd', 'Î' => '\\\'ce', 'Ï' => '\\\'cf',
            'Ñ' => '\\\'d1', 'Ò' => '\\\'d2', 'Ó' => '\\\'d3', 'Ô' => '\\\'d4',
            'Õ' => '\\\'d5', 'Ö' => '\\\'d6', 'Ù' => '\\\'d9', 'Ú' => '\\\'da',
            'Û' => '\\\'db', 'Ü' => '\\\'dc', 'Ý' => '\\\'dd',
            'à' => '\\\'e0', 'á' => '\\\'e1', 'â' => '\\\'e2', 'ã' => '\\\'e3',
            'ä' => '\\\'e4', 'å' => '\\\'e5', 'æ' => '\\\'e6', 'ç' => '\\\'e7',
            'è' => '\\\'e8', 'é' => '\\\'e9', 'ê' => '\\\'ea', 'ë' => '\\\'eb',
            'ì' => '\\\'ec', 'í' => '\\\'ed', 'î' => '\\\'ee', 'ï' => '\\\'ef',
            'ñ' => '\\\'f1', 'ò' => '\\\'f2', 'ó' => '\\\'f3', 'ô' => '\\\'f4',
            'õ' => '\\\'f5', 'ö' => '\\\'f6', 'ù' => '\\\'f9', 'ú' => '\\\'fa',
            'û' => '\\\'fb', 'ü' => '\\\'fc', 'ý' => '\\\'fd', 'ÿ' => '\\\'ff',
            '\\' => '\\\\', '{' => '\\{', '}' => '\\}'
        ];
        
        // Substituir caracteres especiais usando o mapa
        return strtr($texto, $charMap);
    }

    /**
     * Criar conteúdo RTF básico
     */
    private function criarArquivoRTF($texto)
    {
        // Limpar quebras de linha
        $textoLimpo = str_replace(["\r\n", "\r"], "\n", $texto);
        
        // Converter UTF-8 para RTF
        $textoRTF = $this->utf8ToRtf($textoLimpo);
        $textoRTF = str_replace("\n", "\\par\n", $textoRTF);
        
        // Criar documento RTF compatível com OnlyOffice
        $rtf = '{\\rtf1\\ansi\\ansicpg1252\\deff0\\deflang1046' . "\n";
        $rtf .= '{\\fonttbl{\\f0\\froman\\fcharset0 Times New Roman;}}' . "\n";
        $rtf .= '{\\colortbl ;\\red0\\green0\\blue0;}' . "\n";
        $rtf .= '\\viewkind4\\uc1\\pard\\cf1\\f0\\fs24' . "\n";
        $rtf .= $textoRTF . "\n";
        $rtf .= '\\par}';
        
        return $rtf;
    }

    /**
     * Criar arquivo DOCX usando formato RTF (compatível com OnlyOffice)
     */
    private function criarArquivoDocx($texto)
    {
        // Verificar se o texto já é RTF (já processado pelo TemplateProcessorService)
        if (strpos($texto, '{\rtf') !== false) {
            // Texto já está em formato RTF completo, retornar como está
            return $texto;
        }
        
        // Texto é simples, precisa ser convertido para RTF
        $textoLimpo = str_replace(["\r\n", "\r"], "\n", $texto);
        
        // Converter UTF-8 para RTF
        $textoRTF = $this->utf8ToRtf($textoLimpo);
        $textoRTF = str_replace("\n", "\\par\n", $textoRTF);
        
        // RTF mais simples e compatível
        $rtf = '{\\rtf1\\ansi\\deff0' . "\n";
        $rtf .= '{\\fonttbl {\\f0 Times New Roman;}}' . "\n";
        $rtf .= '\\f0\\fs24' . "\n";
        $rtf .= $textoRTF . "\n";
        $rtf .= '}';
        
        return $rtf;
    }

    /**
     * Limpar dados de teste da sessão (método temporário para desenvolvimento)
     */
    public function limparSessaoTeste()
    {
        session()->forget('proposicoes_excluidas');
        return redirect()->route('proposicoes.minhas-proposicoes')
                        ->with('success', 'Dados de teste da sessão foram limpos.');
    }
    
    /**
     * Servir arquivo para OnlyOffice
     */
    public function serveFile($proposicaoId, $arquivo)
    {
        try {
            $pathArquivo = "proposicoes/{$arquivo}";
            $fullPath = storage_path('app/public/' . $pathArquivo);
            
            \Log::info('OnlyOffice serveFile chamado', [
                'proposicao_id' => $proposicaoId,
                'arquivo' => $arquivo,
                'path_relativo' => $pathArquivo,
                'path_completo' => $fullPath,
                'arquivo_existe' => file_exists($fullPath),
                'storage_exists' => \Storage::disk('public')->exists($pathArquivo)
            ]);
            
            if (!\Storage::disk('public')->exists($pathArquivo)) {
                \Log::error('Arquivo não encontrado para OnlyOffice', [
                    'proposicao_id' => $proposicaoId,
                    'arquivo' => $arquivo,
                    'path' => $pathArquivo,
                    'diretorio_contents' => \Storage::disk('public')->files('proposicoes')
                ]);
                return response('File not found', 404);
            }
            
            $conteudo = \Storage::disk('public')->get($pathArquivo);
            
            // Aplicar correção de encoding se o arquivo contém RTF
            // TEMPORARIAMENTE DESABILITADO - causando triple encoding
            // if (strpos($conteudo, '{\rtf') !== false) {
            //     $conteudo = $this->corrigirEncodingParaOnlyOffice($conteudo);
            // }
            
            // Determinar MIME type baseado na extensão
            $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
            switch (strtolower($extensao)) {
                case 'docx':
                    $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    break;
                case 'rtf':
                    $mimeType = 'application/rtf; charset=utf-8';
                    break;
                case 'txt':
                    $mimeType = 'text/plain; charset=utf-8';
                    break;
                default:
                    $mimeType = 'application/octet-stream';
            }
            
            \Log::info('Arquivo servido com sucesso', [
                'proposicao_id' => $proposicaoId,
                'arquivo' => $arquivo,
                'tamanho' => strlen($conteudo)
            ]);
            
            return response($conteudo)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($conteudo))
                ->header('Content-Disposition', 'inline; filename="' . $arquivo . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao servir arquivo para OnlyOffice', [
                'proposicao_id' => $proposicaoId,
                'arquivo' => $arquivo,
                'error' => $e->getMessage()
            ]);
            
            return response('Internal Server Error', 500);
        }
    }
    
    /**
     * Callback do OnlyOffice para salvar alterações
     */
    public function onlyOfficeCallback($proposicaoId)
    {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            \Log::info('OnlyOffice callback recebido', [
                'proposicao_id' => $proposicaoId,
                'callback_data' => $data
            ]);
            
            if (!$data) {
                return response()->json(['error' => 0]);
            }
            
            $status = $data['status'] ?? 0;
            
            // Status 2 = documento está sendo salvo
            // Status 3 = erro ao salvar
            // Status 6 = documento está sendo editado  
            if ($status == 2) {
                if (isset($data['url'])) {
                    // Substituir localhost pelo nome do container OnlyOffice
                    // Usar nome do container para comunicação entre containers
                    $url = str_replace('http://localhost:8080', 'http://legisinc-onlyoffice', $data['url']);
                    
                    \Log::info('OnlyOffice callback - tentando baixar documento', [
                        'proposicao_id' => $proposicaoId,
                        'original_url' => $data['url'],
                        'converted_url' => $url
                    ]);
                    
                    // Download do arquivo atualizado usando cURL
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
                        // Extrair template_id do arquivo atual da proposição
                        $proposicao = Proposicao::find($proposicaoId);
                        $templateId = 4; // default
                        
                        if ($proposicao && $proposicao->arquivo_path) {
                            // Extrair template_id do nome do arquivo atual
                            if (preg_match('/template_(\d+)\./', $proposicao->arquivo_path, $matches)) {
                                $templateId = $matches[1];
                            }
                        }
                        
                        // Fallback para sessão se não conseguir extrair do arquivo
                        if (!$templateId) {
                            $templateId = session('proposicao_' . $proposicaoId . '_template_id', 4);
                        }
                        
                        // Se o template_id veio como string "template_X", extrair apenas o número
                        if (is_string($templateId) && str_starts_with($templateId, 'template_')) {
                            $templateId = str_replace('template_', '', $templateId);
                        }
                        
                        $nomeArquivo = "proposicao_{$proposicaoId}_template_{$templateId}.rtf";
                        $pathDestino = "proposicoes/{$nomeArquivo}";
                        
                        // Salvar arquivo atualizado
                        \Storage::disk('public')->put($pathDestino, $fileContent);
                        
                        \Log::info('Arquivo da proposição salvo via OnlyOffice', [
                            'proposicao_id' => $proposicaoId,
                            'arquivo' => $nomeArquivo,
                            'size' => strlen($fileContent),
                            'template_id' => $templateId,
                            'path' => $pathDestino
                        ]);
                        
                        // Detectar formato do arquivo e extrair texto adequadamente
                        $textoExtraido = $this->extrairTextoDoArquivo($fileContent);
                        
                        // Atualizar sessão com timestamp da última modificação
                        session(['proposicao_' . $proposicaoId . '_ultima_modificacao' => now()]);
                        
                        // Atualizar registro da proposição no banco de dados
                        if ($proposicao) {
                            // Extrair ementa e conteúdo do texto
                            $dadosExtraidos = \App\Services\RTFTextExtractor::extractEmentaAndConteudo($textoExtraido);
                            
                            $proposicao->update([
                                'arquivo_path' => $pathDestino,
                                'ultima_modificacao' => now(),
                                'status' => 'em_edicao',
                                'ementa' => $dadosExtraidos['ementa'] ?? $proposicao->ementa,
                                'conteudo' => $dadosExtraidos['conteudo'] ?? $proposicao->conteudo
                            ]);
                            
                            \Log::info('Proposição atualizada com texto extraído', [
                                'proposicao_id' => $proposicaoId,
                                'ementa_atualizada' => !empty($dadosExtraidos['ementa']),
                                'conteudo_atualizado' => !empty($dadosExtraidos['conteudo'])
                            ]);
                        }
                    } else {
                        \Log::error('Erro ao baixar arquivo do OnlyOffice', [
                            'proposicao_id' => $proposicaoId,
                            'http_code' => $httpCode,
                            'curl_error' => $curlError,
                            'original_url' => $data['url'],
                            'converted_url' => $url
                        ]);
                    }
                }
            }
            
            return response()->json(['error' => 0]);
            
        } catch (\Exception $e) {
            \Log::error('Erro no callback do OnlyOffice', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 1]);
        }
    }

    /**
     * Salvar dados temporários da proposição
     */
    public function salvarDadosTemporarios(Request $request, $proposicaoId)
    {
        $request->validate([
            'ementa' => 'required|string',
            'conteudo' => 'required|string'
        ]);

        // Salvar na sessão
        session([
            'proposicao_' . $proposicaoId . '_ementa' => $request->ementa,
            'proposicao_' . $proposicaoId . '_conteudo' => $request->conteudo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dados salvos temporariamente'
        ]);
    }

    /**
     * Tela intermediária para preparar edição
     */
    public function prepararEdicao($proposicaoId, $templateId)
    {
        // Buscar proposição no banco de dados primeiro
        $proposicao = Proposicao::find($proposicaoId);
        
        // Se não encontrar no BD, verificar se há dados na sessão (fallback para proposições antigas)
        if (!$proposicao) {
            // Criar objeto mock apenas se há dados na sessão
            if (session()->has('proposicao_' . $proposicaoId . '_tipo')) {
                $proposicao = (object) [
                    'id' => $proposicaoId,
                    'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'projeto_lei'),
                    'ementa' => session('proposicao_' . $proposicaoId . '_ementa', ''),
                    'conteudo' => session('proposicao_' . $proposicaoId . '_conteudo', ''),
                    'status' => session('proposicao_' . $proposicaoId . '_status', 'rascunho')
                ];
            } else {
                // Proposição não existe nem no BD nem na sessão
                return redirect()->route('proposicoes.minhas-proposicoes')
                               ->with('error', 'Proposição não encontrada.');
            }
        }

        // Buscar template do administrador baseado no tipo da proposição
        $template = null;
        
        if ($templateId !== 'blank') {
            // Primeiro, buscar o tipo de proposição
            $tipoProposicao = \App\Models\TipoProposicao::buscarPorCodigo($proposicao->tipo);
            
            if ($tipoProposicao) {
                // Buscar template criado pelo admin para este tipo de proposição
                $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoProposicao->id)
                                                              ->where('ativo', true)
                                                              ->first();
                
                \Log::info('Template do admin encontrado para preparar edição', [
                    'proposicao_id' => $proposicaoId,
                    'tipo_proposicao' => $proposicao->tipo,
                    'template_encontrado' => $template ? $template->id : 'nenhum'
                ]);
            }
        }

        return view('proposicoes.preparar-edicao', compact('proposicao', 'template'));
    }

    /**
     * Tela de edição completa com OnlyOffice e anexos
     */
    public function editorCompleto($proposicaoId, $templateId)
    {
        // Buscar dados da proposição
        $proposicao = (object) [
            'id' => $proposicaoId,
            'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'projeto_lei'),
            'ementa' => session('proposicao_' . $proposicaoId . '_ementa', ''),
            'conteudo' => session('proposicao_' . $proposicaoId . '_conteudo', ''),
            'anexos' => session('proposicao_' . $proposicaoId . '_anexos', [])
        ];

        // Buscar template
        $template = \App\Models\TipoProposicaoTemplate::find($templateId);
        
        if (!$template) {
            return redirect()->route('proposicoes.minhas-proposicoes')
                            ->with('error', 'Template não encontrado.');
        }

        // Criar documento com dados preenchidos
        $documentKey = 'proposicao_' . $proposicaoId . '_editor_' . time();
        
        // Criar arquivo da proposição com dados preenchidos
        $arquivoProposicaoPath = $this->criarArquivoComDados($proposicaoId, $template, $proposicao->ementa, $proposicao->conteudo);
        $arquivoProposicao = basename($arquivoProposicaoPath);

        return view('proposicoes.editor-completo', compact('proposicao', 'template', 'documentKey', 'arquivoProposicao'));
    }

    /**
     * Upload de anexo
     */
    public function uploadAnexo(Request $request, $proposicaoId)
    {
        $request->validate([
            'anexo' => 'required|file|max:10240' // Max 10MB
        ]);

        $arquivo = $request->file('anexo');
        $nomeOriginal = $arquivo->getClientOriginalName();
        $nomeArquivo = time() . '_' . $nomeOriginal;
        $path = $arquivo->storeAs('proposicoes/anexos/' . $proposicaoId, $nomeArquivo, 'public');

        // Salvar na sessão
        $anexos = session('proposicao_' . $proposicaoId . '_anexos', []);
        $anexos[] = [
            'id' => uniqid(),
            'nome' => $nomeOriginal,
            'arquivo' => $nomeArquivo,
            'path' => $path,
            'tamanho' => $arquivo->getSize(),
            'uploaded_at' => now()
        ];
        session(['proposicao_' . $proposicaoId . '_anexos' => $anexos]);

        return response()->json([
            'success' => true,
            'anexo' => end($anexos)
        ]);
    }

    /**
     * Remover anexo
     */
    public function removerAnexo($proposicaoId, $anexoId)
    {
        $anexos = session('proposicao_' . $proposicaoId . '_anexos', []);
        
        foreach ($anexos as $key => $anexo) {
            if ($anexo['id'] == $anexoId) {
                // Remover arquivo físico
                \Storage::disk('public')->delete($anexo['path']);
                
                // Remover da sessão
                unset($anexos[$key]);
                session(['proposicao_' . $proposicaoId . '_anexos' => array_values($anexos)]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Anexo removido com sucesso'
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Anexo não encontrado'
        ], 404);
    }

    /**
     * Criar arquivo com dados preenchidos
     */
    private function criarArquivoComDados($proposicaoId, $template, $ementa, $conteudo)
    {
        try {
            $nomeArquivo = "proposicao_{$proposicaoId}_preenchida_{$template->id}.rtf";
            $pathDestino = "proposicoes/{$nomeArquivo}";
            $pathCompleto = storage_path('app/public/' . $pathDestino);
            
            // Garantir que o diretório existe
            $diretorio = dirname($pathCompleto);
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            // Criar RTF com dados preenchidos
            $rtfContent = $this->gerarRTFComDados($ementa, $conteudo);
            \Storage::disk('public')->put($pathDestino, $rtfContent);
            
            return $pathDestino;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar arquivo com dados', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Gerar conteúdo RTF com dados preenchidos
     */
    private function gerarRTFComDados($ementa, $conteudo)
    {
        // Criar documento RTF com formatação
        $rtf = '{\\rtf1\\ansi\\ansicpg1252\\deff0\\deflang1046' . "\n";
        $rtf .= '{\\fonttbl {\\f0 Times New Roman;}}' . "\n";
        $rtf .= '{\\colortbl;\\red0\\green0\\blue0;}' . "\n";
        $rtf .= '\\f0\\fs24' . "\n";
        
        // Adicionar ementa
        $rtf .= '\\b EMENTA\\b0\\par' . "\n";
        $rtf .= '\\par' . "\n";
        // Converter ementa para RTF com encoding correto
        $ementaRtf = $this->utf8ToRtf($ementa);
        $rtf .= str_replace("\n", "\\par\n", $ementaRtf) . "\n";
        $rtf .= '\\par\\par' . "\n";
        
        // Adicionar conteúdo
        $rtf .= '\\b ' . $this->utf8ToRtf('PROPOSIÇÃO') . '\\b0\\par' . "\n";
        $rtf .= '\\par' . "\n";
        // Converter conteúdo para RTF com encoding correto
        $conteudoRtf = $this->utf8ToRtf($conteudo);
        $rtf .= str_replace("\n", "\\par\n", $conteudoRtf) . "\n";
        
        $rtf .= '}';
        
        return $rtf;
    }

    /**
     * Atualizar status da proposição
     */
    public function atualizarStatus(Request $request, $proposicaoId)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        // Buscar proposição no banco de dados primeiro
        $proposicao = Proposicao::find($proposicaoId);
        
        if ($proposicao) {
            // Atualizar status no banco de dados
            $proposicao->update(['status' => $request->status]);
        } else {
            // Fallback: salvar status na sessão (para proposições antigas)
            session(['proposicao_' . $proposicaoId . '_status' => $request->status]);
        }
        
        // Log da mudança de status
        \Log::info('Status da proposição atualizado', [
            'proposicao_id' => $proposicaoId,
            'novo_status' => $request->status,
            'user_id' => Auth::id(),
            'salvo_em' => $proposicao ? 'banco_dados' : 'sessao'
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Status atualizado com sucesso'
        ]);
    }

    /**
     * Retorno do legislativo (simulado)
     */
    public function retornoLegislativo(Request $request, $proposicaoId)
    {
        // Simular retorno do legislativo
        session([
            'proposicao_' . $proposicaoId . '_status' => 'retornado_legislativo',
            'proposicao_' . $proposicaoId . '_observacoes_legislativo' => $request->observacoes ?? 'Proposição aprovada pelo legislativo'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição retornada do legislativo'
        ]);
    }

    /**
     * Corrigir encoding para exibição correta no OnlyOffice
     */
    private function corrigirEncodingParaOnlyOffice(string $conteudoRTF): string
    {
        \Log::info('Aplicando correção de encoding para OnlyOffice no serveFile', [
            'tamanho_original' => strlen($conteudoRTF),
            'preview' => substr($conteudoRTF, 0, 100)
        ]);
        
        $conteudoCorrigido = $conteudoRTF;
        
        // Substituir códigos RTF e Unicode por UTF-8 para melhor interpretação no OnlyOffice
        $substituicoes = [
            // Códigos RTF tradicionais
            "\\'ed" => "í",  // í
            "\\'e3" => "ã",  // ã
            "\\'e2" => "â",  // â  
            "\\'e7" => "ç",  // ç
            "\\'c7" => "Ç",  // Ç
            "\\'c3" => "Ã",  // Ã
            "\\'e1" => "á",  // á
            "\\'e9" => "é",  // é
            "\\'f3" => "ó",  // ó
            "\\'fa" => "ú",  // ú
            
            // Códigos Unicode do OnlyOffice
            "\\u237*" => "í",   // í em Unicode RTF
            "\\u227*" => "ã",   // ã em Unicode RTF
            "\\u226*" => "â",   // â em Unicode RTF
            "\\u231*" => "ç",   // ç em Unicode RTF
            "\\u199*" => "Ç",   // Ç em Unicode RTF
            "\\u195*" => "Ã",   // Ã em Unicode RTF
            "\\u225*" => "á",   // á em Unicode RTF
            "\\u233*" => "é",   // é em Unicode RTF
            "\\u243*" => "ó",   // ó em Unicode RTF
            "\\u250*" => "ú",   // ú em Unicode RTF
        ];
        
        $totalCorrecoes = 0;
        foreach ($substituicoes as $rtfCode => $utf8Char) {
            $antes = substr_count($conteudoCorrigido, $rtfCode);
            $conteudoCorrigido = str_replace($rtfCode, $utf8Char, $conteudoCorrigido);
            $depois = substr_count($conteudoCorrigido, $rtfCode);
            
            if ($antes > $depois) {
                $correcoes = $antes - $depois;
                $totalCorrecoes += $correcoes;
                \Log::info("Correção RTF->UTF8 aplicada: {$rtfCode} -> {$utf8Char}", [
                    'ocorrencias' => $correcoes
                ]);
            }
        }
        
        \Log::info('Correção de encoding para OnlyOffice concluída', [
            'total_correcoes' => $totalCorrecoes,
            'tamanho_final' => strlen($conteudoCorrigido)
        ]);
        
        return $conteudoCorrigido;
    }

    /**
     * Assinar documento digitalmente
     */
    public function assinarDocumento(Request $request, $proposicaoId)
    {
        // Validar se a proposição está no status correto
        $status = session('proposicao_' . $proposicaoId . '_status', 'rascunho');
        
        if ($status !== 'retornado_legislativo') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar retornada do legislativo para ser assinada'
            ], 400);
        }

        // Simular assinatura digital
        session([
            'proposicao_' . $proposicaoId . '_status' => 'assinado',
            'proposicao_' . $proposicaoId . '_assinatura' => [
                'assinado_por' => Auth::user()->name,
                'assinado_em' => now(),
                'certificado' => 'Certificado Digital A3 - Simulado'
            ]
        ]);

        \Log::info('Documento assinado digitalmente', [
            'proposicao_id' => $proposicaoId,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento assinado com sucesso!'
        ]);
    }

    /**
     * Enviar para protocolo
     */
    public function enviarProtocolo(Request $request, $proposicaoId)
    {
        // Validar se está assinado
        $status = session('proposicao_' . $proposicaoId . '_status', 'rascunho');
        
        if ($status !== 'assinado') {
            return response()->json([
                'success' => false,
                'message' => 'Documento deve estar assinado para ser protocolado'
            ], 400);
        }

        // Gerar número de protocolo
        $numeroProtocolo = 'PROT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        session([
            'proposicao_' . $proposicaoId . '_status' => 'protocolado',
            'proposicao_' . $proposicaoId . '_protocolo' => [
                'numero' => $numeroProtocolo,
                'data' => now(),
                'responsavel' => Auth::user()->name
            ]
        ]);

        \Log::info('Proposição protocolada', [
            'proposicao_id' => $proposicaoId,
            'numero_protocolo' => $numeroProtocolo,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição protocolada com sucesso!',
            'numero_protocolo' => $numeroProtocolo
        ]);
    }

    /**
     * Criar texto básico para template em branco
     */
    private function criarTextoBasico($proposicao, $variaveis)
    {
        $tipoFormatado = $proposicao->tipo_formatado ?? 'Proposição';
        $ementa = $variaveis['ementa'] ?? '';
        $conteudo = $variaveis['texto'] ?? $variaveis['conteudo'] ?? '';
        
        // Criar um documento simples com formatação básica
        $texto = "{$tipoFormatado}\n\n";
        $texto .= "EMENTA\n\n";
        $texto .= "{$ementa}\n\n";
        $texto .= "CONTEÚDO\n\n";
        $texto .= "{$conteudo}\n\n";
        $texto .= "Autor: " . (\Auth::user()->name ?? '[AUTOR]') . "\n";
        $texto .= "Data: " . now()->format('d/m/Y');
        
        return $texto;
    }

    /**
     * Obter status completo da proposição
     */
    public function obterStatus($proposicaoId)
    {
        $status = session('proposicao_' . $proposicaoId . '_status', 'rascunho');
        $assinatura = session('proposicao_' . $proposicaoId . '_assinatura');
        $protocolo = session('proposicao_' . $proposicaoId . '_protocolo');
        $observacoes = session('proposicao_' . $proposicaoId . '_observacoes_legislativo');

        return response()->json([
            'status' => $status,
            'assinatura' => $assinatura,
            'protocolo' => $protocolo,
            'observacoes_legislativo' => $observacoes
        ]);
    }

    /**
     * Extrair texto de qualquer formato de arquivo (DOCX ou RTF)
     */
    private function extrairTextoDoArquivo($fileContent)
    {
        try {
            // Detectar formato do arquivo pelos primeiros bytes
            $header = substr($fileContent, 0, 10);
            
            if (strpos($fileContent, 'PK') === 0 || strpos($fileContent, '<?xml') !== false) {
                // Arquivo DOCX (formato ZIP com XML)
                \Log::info('Detectado arquivo DOCX, extraindo texto do XML');
                return $this->extrairTextoDOCX($fileContent);
            } elseif (strpos($fileContent, '{\\rtf') === 0) {
                // Arquivo RTF - usar o novo extrator
                \Log::info('Detectado arquivo RTF, usando RTFTextExtractor');
                return \App\Services\RTFTextExtractor::extract($fileContent);
            } else {
                // Tentar como texto puro primeiro
                if (mb_check_encoding($fileContent, 'UTF-8')) {
                    \Log::info('Tratando como texto puro UTF-8');
                    return trim($fileContent);
                } else {
                    \Log::warning('Formato de arquivo não reconhecido, tentando como RTF');
                    return \App\Services\RTFTextExtractor::extract($fileContent);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao extrair texto do arquivo', [
                'erro' => $e->getMessage()
            ]);
            return 'Erro ao extrair texto do documento';
        }
    }

    /**
     * Extrair texto de arquivo DOCX
     */
    private function extrairTextoDOCX($docxContent)
    {
        try {
            \Log::info('Iniciando extração de texto DOCX', [
                'tamanho_arquivo' => strlen($docxContent)
            ]);
            
            $texto = '';
            
            // Método 1: Tentar usando ZipArchive se disponível
            if (class_exists('ZipArchive')) {
                $tempFile = tempnam(sys_get_temp_dir(), 'docx_extract_');
                file_put_contents($tempFile, $docxContent);
                
                $zip = new \ZipArchive();
                if ($zip->open($tempFile) === TRUE) {
                    $documentXml = $zip->getFromName('word/document.xml');
                    if ($documentXml !== false) {
                        $texto = $this->extrairTextoDoXML($documentXml);
                        \Log::info('Extração via ZipArchive bem-sucedida', [
                            'tamanho_texto' => strlen($texto)
                        ]);
                    }
                    $zip->close();
                }
                unlink($tempFile);
            }
            
            // Método 2: Se ZipArchive não funcionou, usar stream wrapper
            if (empty($texto)) {
                \Log::info('Tentando extração via stream wrapper');
                $texto = $this->extrairTextoDOCXStream($docxContent);
            }
            
            // Método 3: Se nada funcionou, usar extração direta
            if (empty($texto)) {
                \Log::warning('Usando método de extração direta como fallback');
                $texto = $this->extrairTextoDOCXDireto($docxContent);
            }
            
            \Log::info('Texto extraído do DOCX', [
                'tamanho_resultado' => strlen($texto),
                'preview' => substr($texto, 0, 200)
            ]);
            
            return $texto;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao extrair texto do DOCX', [
                'erro' => $e->getMessage()
            ]);
            return 'Erro ao processar documento DOCX';
        }
    }

    /**
     * Extrair texto DOCX usando stream wrapper
     */
    private function extrairTextoDOCXStream($docxContent)
    {
        try {
            // Criar arquivo temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'docx_stream_');
            file_put_contents($tempFile, $docxContent);
            
            // Tentar usar stream wrapper para ZIP
            $documentContent = @file_get_contents("zip://$tempFile#word/document.xml");
            
            if ($documentContent !== false) {
                \Log::info('Stream wrapper funcionou', [
                    'tamanho_xml' => strlen($documentContent)
                ]);
                
                $texto = $this->extrairTextoDoXML($documentContent);
                unlink($tempFile);
                return $texto;
            }
            
            unlink($tempFile);
            return '';
            
        } catch (\Exception $e) {
            \Log::error('Erro na extração via stream', [
                'erro' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Extrair texto do DOCX diretamente (sem ZipArchive)
     */
    private function extrairTextoDOCXDireto($docxContent)
    {
        try {
            \Log::info('Tentando extração direta de DOCX', [
                'tamanho_arquivo' => strlen($docxContent)
            ]);
            
            // Como último recurso, se nenhum método funcionou, 
            // vamos retornar uma mensagem indicando que o documento precisa ser salvo como texto
            \Log::warning('Extração automática falhou - documento precisa ser salvo como texto simples');
            
            return 'Documento salvo no editor. Para visualizar o texto aqui, salve o documento como texto simples no editor.';
            
        } catch (\Exception $e) {
            \Log::error('Erro na extração direta de DOCX', [
                'erro' => $e->getMessage()
            ]);
            return 'Erro ao extrair texto do documento';
        }
    }

    /**
     * Extrair texto do XML do Word
     */
    private function extrairTextoDoXML($xmlContent)
    {
        try {
            // Usar SimpleXML para extrair texto
            $dom = new \DOMDocument();
            $dom->loadXML($xmlContent);
            
            // Buscar todos os elementos de texto
            $xpath = new \DOMXPath($dom);
            $textNodes = $xpath->query('//w:t');
            
            $textos = [];
            foreach ($textNodes as $node) {
                $textos[] = $node->nodeValue;
            }
            
            $texto = implode(' ', $textos);
            
            // Se não encontrou com namespace, tentar sem
            if (empty($texto)) {
                if (preg_match_all('/<w:t[^>]*>([^<]+)<\/w:t>/i', $xmlContent, $matches)) {
                    $texto = implode(' ', $matches[1]);
                }
            }
            
            // Limpar e formatar
            $texto = html_entity_decode($texto, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $texto = preg_replace('/\s+/', ' ', $texto);
            $texto = trim($texto);
            
            return $texto;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao processar XML do Word', [
                'erro' => $e->getMessage()
            ]);
            
            // Fallback: regex simples
            if (preg_match_all('/<w:t[^>]*>([^<]+)<\/w:t>/i', $xmlContent, $matches)) {
                return implode(' ', $matches[1]);
            }
            
            return '';
        }
    }

    /**
     * Extrair texto puro de um arquivo RTF
     */
    private function extrairTextoDoRTF($rtfContent)
    {
        try {
            // Guardar o conteúdo original
            $text = $rtfContent;
            
            // Remover grupos de controle do cabeçalho completamente (com conteúdo aninhado)
            $text = preg_replace('/\{\\\\fonttbl.*?\}(?:\{.*?\})*\}/s', '', $text);
            $text = preg_replace('/\{\\\\colortbl.*?\}/s', '', $text);
            $text = preg_replace('/\{\\\\stylesheet.*?\}(?:\{.*?\})*\}/s', '', $text);
            $text = preg_replace('/\{\\\\\*\\\\generator.*?\}/s', '', $text);
            
            // Decodificar caracteres especiais hex (\\'XX)
            $text = preg_replace_callback("/\\\\'([0-9a-fA-F]{2})/", function($matches) {
                return chr(hexdec($matches[1]));
            }, $text);
            
            // IMPORTANTE: Decodificar caracteres Unicode ANTES de remover outros comandos
            // Suporta \uXXXX? e \uXXXX*
            $text = preg_replace_callback('/\\\\u(-?\d+)[\?\*]/', function($matches) {
                $code = intval($matches[1]);
                if ($code < 0) {
                    $code = 65536 + $code;
                }
                if ($code < 128) {
                    return chr($code);
                } else if ($code < 256) {
                    // Para caracteres Latin-1
                    return chr($code);
                } else {
                    // Para outros caracteres Unicode
                    return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
                }
            }, $text);
            
            // Converter comandos de formatação em quebras de linha
            $text = str_replace(['\\par', '\\line'], "\n", $text);
            $text = str_replace('\\tab', "\t", $text);
            
            // Remover grupos com \* (grupos de controle especiais)
            $text = preg_replace('/\{\\\\\*[^}]*\}/s', '', $text);
            
            // Remover todos os comandos RTF (começam com \)
            // Mas preservar quebras de linha já convertidas
            $text = preg_replace('/\\\\[a-z]+[-]?\d*\s*/i', '', $text);
            
            // Remover grupos vazios {}
            $text = preg_replace('/\{\s*\}/', '', $text);
            
            // Remover chaves restantes
            $text = str_replace(['{', '}'], '', $text);
            
            // Converter caracteres especiais
            $text = str_replace('\\~', ' ', $text);
            $text = str_replace('\\-', '', $text);
            $text = str_replace('\\*', '', $text);
            
            // Remover múltiplos pontos e vírgulas consecutivos
            $text = preg_replace('/[;]{2,}/', ';', $text);
            $text = preg_replace('/;(\s*;)+/', ';', $text);
            
            // Limpar múltiplos espaços e quebras de linha
            $text = preg_replace('/[ \t]+/', ' ', $text);
            $text = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $text);
            $text = trim($text);
            
            // Garantir UTF-8
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', 'auto');
            }
            
            \Log::info('Texto extraído do RTF', [
                'tamanho_original' => strlen($rtfContent),
                'tamanho_texto' => strlen($text),
                'primeiros_200_chars' => substr($text, 0, 200)
            ]);
            
            return $text;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao extrair texto do RTF', [
                'erro' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Extrair ementa e conteúdo do texto
     */
    private function extrairEmentaEConteudo($texto)
    {
        try {
            $ementa = '';
            $conteudo = '';
            
            // Limpar o texto primeiro
            $texto = trim($texto);
            
            // Dividir o texto em linhas
            $linhas = explode("\n", $texto);
            $linhas = array_filter($linhas, function($linha) {
                return trim($linha) !== '';
            });
            $linhas = array_values($linhas);
            
            // Procurar por padrões conhecidos
            $ementaEncontrada = false;
            $conteudoIniciado = false;
            $linhasEmenta = [];
            $linhasConteudo = [];
            
            foreach ($linhas as $linha) {
                $linhaLimpa = trim($linha);
                
                // Pular linhas com apenas números ou códigos
                if (preg_match('/^[\d\/\-]+$/', $linhaLimpa)) {
                    continue;
                }
                
                // Pular tipo de proposição (primeira linha geralmente)
                if (preg_match('/^(Projeto de Lei|Requerimento|Indicação|Moção)/i', $linhaLimpa)) {
                    continue;
                }
                
                // Detectar início da ementa
                if (!$ementaEncontrada && (
                    stripos($linhaLimpa, 'EMENTA') !== false ||
                    stripos($linhaLimpa, 'Atualizações') !== false ||
                    stripos($linhaLimpa, 'Dispõe sobre') !== false ||
                    stripos($linhaLimpa, 'dívida') !== false ||
                    preg_match('/^[A-Za-zÀ-ÿ].*[a-z]:/', $linhaLimpa) // Linha que termina com :
                )) {
                    $ementaEncontrada = true;
                    // Remover a palavra EMENTA se existir
                    $linhaLimpa = preg_replace('/^EMENTA[:\s]*/i', '', $linhaLimpa);
                    if ($linhaLimpa) {
                        $linhasEmenta[] = $linhaLimpa;
                    }
                    continue;
                }
                
                // Detectar início do conteúdo
                if (stripos($linhaLimpa, 'CONTEÚDO') !== false ||
                    stripos($linhaLimpa, 'TEXTO') !== false ||
                    stripos($linhaLimpa, 'Prezado') !== false ||
                    preg_match('/^Art\.\s*\d+/i', $linhaLimpa)) {
                    $conteudoIniciado = true;
                    // Remover a palavra CONTEÚDO/TEXTO se existir
                    $linhaLimpa = preg_replace('/^(CONTEÚDO|TEXTO)[:\s]*/i', '', $linhaLimpa);
                    if ($linhaLimpa) {
                        $linhasConteudo[] = $linhaLimpa;
                    }
                    continue;
                }
                
                // Adicionar linha ao conteúdo ou ementa apropriado
                if ($conteudoIniciado) {
                    $linhasConteudo[] = $linhaLimpa;
                } elseif ($ementaEncontrada && !$conteudoIniciado) {
                    $linhasEmenta[] = $linhaLimpa;
                } elseif (!$ementaEncontrada && !$conteudoIniciado) {
                    // Se ainda não encontrou marcadores, considerar como ementa as primeiras linhas
                    if (count($linhasEmenta) === 0 && 
                        (strlen($linhaLimpa) > 20 && strlen($linhaLimpa) < 200) &&
                        !preg_match('/^(Prezado|Sua |Na |Art\.|Artigo)/i', $linhaLimpa)) {
                        $linhasEmenta[] = $linhaLimpa;
                        $ementaEncontrada = true; // Marca como encontrada para parar de procurar
                    } elseif (count($linhasEmenta) === 0 && count($linhasConteudo) === 0) {
                        // Se é a primeira linha e parece ser uma ementa curta
                        $linhasEmenta[] = $linhaLimpa;
                    } else {
                        $linhasConteudo[] = $linhaLimpa;
                    }
                }
            }
            
            // Montar ementa e conteúdo
            $ementa = implode(' ', $linhasEmenta);
            $conteudo = implode('<br>', $linhasConteudo);
            
            // Se não encontrou ementa, pegar a primeira linha significativa
            if (empty($ementa) && !empty($linhasConteudo)) {
                $ementa = $linhasConteudo[0];
                array_shift($linhasConteudo);
                $conteudo = implode('<br>', $linhasConteudo);
            }
            
            // Limpar e formatar
            $ementa = $this->limparTexto($ementa);
            $conteudo = $this->limparTexto($conteudo);
            
            // Se a ementa ficar muito grande, truncar
            if (strlen($ementa) > 500) {
                $ementa = substr($ementa, 0, 497) . '...';
            }
            
            \Log::info('Ementa e conteúdo extraídos', [
                'ementa_tamanho' => strlen($ementa),
                'conteudo_tamanho' => strlen($conteudo),
                'ementa_preview' => substr($ementa, 0, 100),
                'conteudo_preview' => substr($conteudo, 0, 100)
            ]);
            
            return [
                'ementa' => $ementa ?: 'Proposição em elaboração',
                'conteudo' => $conteudo ?: 'Conteúdo a ser definido'
            ];
            
        } catch (\Exception $e) {
            \Log::error('Erro ao extrair ementa e conteúdo', [
                'erro' => $e->getMessage()
            ]);
            return [
                'ementa' => 'Erro ao processar ementa',
                'conteudo' => $texto
            ];
        }
    }

    /**
     * Limpar texto removendo caracteres indesejados
     */
    private function limparTexto($texto)
    {
        // Remover múltiplos espaços
        $texto = preg_replace('/\s+/', ' ', $texto);
        
        // Remover espaços no início e fim
        $texto = trim($texto);
        
        // Remover caracteres de controle
        $texto = preg_replace('/[\x00-\x1F\x7F]/', '', $texto);
        
        // Preservar quebras de linha importantes
        $texto = str_replace(["\r\n", "\r", "\n"], '<br>', $texto);
        $texto = preg_replace('/(<br>)+/', '<br>', $texto);
        
        return $texto;
    }

    // ===============================================
    // NOVA ARQUITETURA - DocumentoTemplate
    // ===============================================

    /**
     * Selecionar template da nova arquitetura
     */
    public function selecionarTemplate(Request $request, int $proposicaoId)
    {
        $request->validate([
            'template_id' => 'required|exists:documento_templates,id'
        ]);

        $proposicao = Proposicao::findOrFail($proposicaoId);
        $template = DocumentoTemplate::with('variaveis')->findOrFail($request->template_id);

        // Buscar variáveis editáveis do template
        $variaveisEditaveis = $template->variaveis()
            ->where('tipo', 'editavel')
            ->get();

        return view('proposicoes.preencher-variaveis', [
            'proposicao' => $proposicao,
            'template' => $template,
            'variaveis' => $variaveisEditaveis
        ]);
    }

    /**
     * Processar template da nova arquitetura
     */
    public function processarTemplateNovaArquitetura(Request $request, int $proposicaoId)
    {
        $proposicao = Proposicao::findOrFail($proposicaoId);
        $templateId = $request->input('template_id');

        $templateInstanceService = app(TemplateInstanceService::class);

        try {
            // 1. Criar instância do template
            $instance = $templateInstanceService->criarInstanciaTemplate(
                $proposicaoId, 
                $templateId
            );

            // 2. Processar variáveis
            $variaveisPreenchidas = $request->input('variaveis', []);
            $templateInstanceService->processarVariaveisInstance(
                $instance, 
                $variaveisPreenchidas
            );

            // 3. Redirecionar para OnlyOffice
            return redirect()->route('proposicoes.editar-onlyoffice-nova-arquitetura', [
                'proposicao' => $proposicaoId,
                'instance' => $instance->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao processar template da nova arquitetura', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $templateId,
                'erro' => $e->getMessage()
            ]);

            return back()->withErrors('Erro ao processar template: ' . $e->getMessage());
        }
    }

    /**
     * Editor OnlyOffice com nova arquitetura
     */
    public function editarOnlyOfficeNovaArquitetura(int $proposicaoId, int $instanceId)
    {
        $instance = \App\Models\ProposicaoTemplateInstance::with(['proposicao', 'template'])
            ->findOrFail($instanceId);

        // Verificar se arquivo está pronto
        if ($instance->status !== 'pronto') {
            return back()->withErrors('Template ainda não está pronto para edição.');
        }

        $templateInstanceService = app(TemplateInstanceService::class);

        // Configurar OnlyOffice
        $config = $templateInstanceService->obterConfiguracaoOnlyOffice($instance);

        // Atualizar status para editando
        $instance->update(['status' => 'editando']);

        return view('proposicoes.editar-onlyoffice-nova-arquitetura', [
            'config' => $config,
            'proposicao' => $instance->proposicao,
            'instance' => $instance
        ]);
    }

    /**
     * Servir arquivo de instância para OnlyOffice
     */
    public function serveInstance(int $instanceId)
    {
        $instance = \App\Models\ProposicaoTemplateInstance::findOrFail($instanceId);

        if (!$instance->arquivo_instance_path || !\Storage::exists($instance->arquivo_instance_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        $arquivo = \Storage::get($instance->arquivo_instance_path);
        
        return response($arquivo, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline; filename="document.docx"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }

    /**
     * Callback OnlyOffice para instâncias
     */
    public function onlyOfficeCallbackInstance(int $instanceId)
    {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            \Log::info('OnlyOffice callback recebido para instância', [
                'instance_id' => $instanceId,
                'callback_data' => $data
            ]);
            
            if (!$data) {
                return response()->json(['error' => 0]);
            }

            $instance = \App\Models\ProposicaoTemplateInstance::findOrFail($instanceId);
            $status = $data['status'] ?? 0;
            
            // Status 2 = documento está sendo salvo
            if ($status == 2) {
                if (isset($data['url'])) {
                    // Download do arquivo atualizado
                    $fileContent = file_get_contents($data['url']);
                    
                    if ($fileContent) {
                        // Salvar arquivo atualizado
                        \Storage::put($instance->arquivo_instance_path, $fileContent);
                        
                        \Log::info('Arquivo da instância salvo via OnlyOffice', [
                            'instance_id' => $instanceId,
                            'size' => strlen($fileContent)
                        ]);
                        
                        // Atualizar timestamp
                        $instance->touch();
                    }
                }
            }
            
            return response()->json(['error' => 0]);
            
        } catch (\Exception $e) {
            \Log::error('Erro no callback do OnlyOffice para instância', [
                'instance_id' => $instanceId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 1]);
        }
    }

    /**
     * Finalizar edição de instância
     */
    public function finalizarEdicaoInstance(int $instanceId)
    {
        $instance = \App\Models\ProposicaoTemplateInstance::findOrFail($instanceId);
        
        $templateInstanceService = app(TemplateInstanceService::class);
        $templateInstanceService->finalizarEdicao($instance);

        return redirect()->route('proposicoes.show', $instance->proposicao_id)
            ->with('success', 'Edição finalizada com sucesso!');
    }

    /**
     * Voltar proposição para parlamentar (do legislativo)
     */
    public function voltarParaParlamentar(Proposicao $proposicao)
    {
        try {
            // Verificar se o usuário é do legislativo
            if (!auth()->user()->isLegislativo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários do Legislativo podem executar esta ação.'
                ], 403);
            }

            // Verificar se a proposição está no status correto
            if (!in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'devolvido_correcao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser devolvida no status atual.'
                ], 400);
            }

            // Tentar converter o documento para PDF para facilitar a assinatura
            try {
                \Log::info('Iniciando conversão para PDF', [
                    'proposicao_id' => $proposicao->id,
                    'arquivo_path' => $proposicao->arquivo_path,
                    'arquivo_existe' => $proposicao->arquivo_path ? \Storage::exists($proposicao->arquivo_path) : false,
                    'has_content' => !empty($proposicao->conteudo)
                ]);
                
                // Sempre tentar converter, seja com arquivo físico ou conteúdo do banco
                $this->converterProposicaoParaPDF($proposicao);
                
                \Log::info('Conversão para PDF concluída', [
                    'proposicao_id' => $proposicao->id,
                    'arquivo_pdf_path' => $proposicao->arquivo_pdf_path
                ]);
            } catch (\Exception $e) {
                \Log::error('Erro ao converter proposição para PDF', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continua sem falhar, pois a conversão é opcional
            }

            // Alterar o status para 'retornado_legislativo' - proposição volta para o parlamentar assinar
            $proposicao->status = 'retornado_legislativo';
            $proposicao->save();

            \Log::info('Proposição devolvida para parlamentar', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'status_anterior' => $proposicao->getOriginal('status'),
                'status_novo' => $proposicao->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposição devolvida para o Parlamentar com sucesso! O Legislativo não terá mais acesso.',
                'redirect' => route('proposicoes.legislativo.index')
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao voltar proposição para parlamentar', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Aprovar edições feitas pelo Legislativo
     */
    public function aprovarEdicoesLegislativo(Request $request, Proposicao $proposicao)
    {
        try {
            // Verificar se o usuário é o autor da proposição
            if ($proposicao->autor_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas o autor pode aprovar as edições.'
                ], 403);
            }

            // Verificar se a proposição está no status correto
            if (!in_array($proposicao->status, ['aguardando_aprovacao_autor', 'devolvido_edicao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser aprovada no status atual.'
                ], 400);
            }

            // Atualizar status para 'aprovado_assinatura' - próximo passo é assinar
            $proposicao->update([
                'status' => 'aprovado_assinatura',
                'data_aprovacao_autor' => now()
            ]);

            \Log::info('Edições do legislativo aprovadas pelo parlamentar', [
                'proposicao_id' => $proposicao->id,
                'user_id' => auth()->id(),
                'status_anterior' => $proposicao->getOriginal('status')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Edições aprovadas com sucesso! A proposição está pronta para assinatura.',
                'redirect' => route('proposicoes.show', $proposicao)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao aprovar edições do legislativo', [
                'error' => $e->getMessage(),
                'proposicao_id' => $proposicao->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Converter texto UTF-8 para códigos RTF
     */
    private function converterUtf8ParaRtf($texto)
    {
        \Log::info('Convertendo UTF-8 para RTF Unicode sequences', [
            'texto_original' => $texto,
            'length' => strlen($texto),
            'bytes' => bin2hex($texto)
        ]);

        // Primeiro, limpar qualquer corrupção existente e normalizar para UTF-8 limpo
        $textoLimpo = $this->limparTextoCorrupto($texto);
        
        \Log::info('Texto após limpeza', [
            'texto_limpo' => $textoLimpo,
            'bytes_limpos' => bin2hex($textoLimpo)
        ]);

        // Converter caracteres UTF-8 para sequências Unicode RTF
        $resultado = '';
        $length = mb_strlen($textoLimpo, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($textoLimpo, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            
            if ($codepoint > 127) {
                // Converter para escape Unicode RTF
                $resultado .= '\u' . $codepoint . '*';
            } else {
                $resultado .= $char;
            }
        }
        
        \Log::info('UTF-8 convertido para RTF Unicode', [
            'texto_final' => $resultado,
            'caracteres_convertidos' => $length - strlen(preg_replace('/[^\x00-\x7F]/', '', $textoLimpo))
        ]);

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
            'ProposiÃ§Ã£o' => 'Proposição',
            'C‚mara' => 'Câmara',
            'CÃ¢mara' => 'Câmara',
            'aÃ§Ã£o' => 'ação',
            'oÃ§Ã£o' => 'oção'
        ];

        $textoLimpo = $texto;
        foreach ($correcoes as $corrupto => $correto) {
            $textoLimpo = str_replace($corrupto, $correto, $textoLimpo);
        }

        // Normalizar para UTF-8 NFC (forma canônica)
        if (function_exists('normalizer_normalize')) {
            $textoLimpo = normalizer_normalize($textoLimpo, \Normalizer::FORM_C);
        }

        return $textoLimpo;
    }

    /**
     * Processar template DOCX XML (novo formato sem problemas de encoding)
     */
    private function processarTemplateDOCX($proposicaoId, $template, $pathDestino)
    {
        try {
            $proposicao = Proposicao::find($proposicaoId);
            
            // Buscar variáveis preenchidas pelo parlamentar da sessão
            $variaveisPreenchidas = session('proposicao_' . $proposicaoId . '_variaveis_template', []);
            
            // Se não há variáveis na sessão, usar as da proposição
            if (empty($variaveisPreenchidas)) {
                $variaveisPreenchidas = [
                    'ementa' => $proposicao->ementa,
                    'texto' => $proposicao->conteudo,
                    'conteudo' => $proposicao->conteudo
                ];
            }

            \Log::info('Processando template DOCX XML', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $template->id,
                'variaveis_preenchidas' => $variaveisPreenchidas
            ]);

            // Carregar template XML
            $templatePath = storage_path('app/private/' . $template->arquivo_path);
            if (!file_exists($templatePath)) {
                throw new \Exception("Template DOCX XML não encontrado: " . $templatePath);
            }

            $templateXML = file_get_contents($templatePath);
            
            // Preparar variáveis do sistema
            $user = \Auth::user();
            $agora = \Carbon\Carbon::now();
            
            $variaveis = [
                'data' => $agora->format('d/m/Y'),
                'data_atual' => $agora->format('d/m/Y'),
                'data_extenso' => $this->formatarDataExtenso($agora),
                'numero_proposicao' => $this->gerarNumeroProposicao($proposicao),
                'tipo_proposicao' => $proposicao->tipo_formatado ?? 'Projeto de Lei Ordinária',
                'autor_nome' => $proposicao->autor->name ?? $user->name ?? 'Autor',
                'municipio' => config('app.municipio', 'São Paulo'),
                'nome_camara' => config('app.nome_camara', 'Câmara Municipal'),
                'ementa' => $variaveisPreenchidas['ementa'] ?? 'Ementa da proposição',
                'texto' => $variaveisPreenchidas['texto'] ?? 'Texto da proposição'
            ];

            // Substituir variáveis no XML (sem conversão de encoding!)
            $xmlProcessado = $templateXML;
            foreach ($variaveis as $variavel => $valor) {
                // Escapar caracteres XML mas manter UTF-8
                $valorEscapado = htmlspecialchars($valor, ENT_XML1, 'UTF-8');
                $xmlProcessado = str_replace('${' . $variavel . '}', $valorEscapado, $xmlProcessado);
            }

            // Criar estrutura DOCX
            $this->criarDOCXDeXML($xmlProcessado, storage_path('app/public/' . $pathDestino));

            \Log::info('Template DOCX processado com sucesso', [
                'template_path' => $template->arquivo_path,
                'proposicao_path' => $pathDestino,
                'arquivo_existe' => file_exists(storage_path('app/public/' . $pathDestino))
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao processar template DOCX', [
                'error' => $e->getMessage(),
                'proposicao_id' => $proposicaoId,
                'template_id' => $template->id
            ]);
            throw $e;
        }
    }

    /**
     * Criar arquivo DOCX a partir de XML do documento
     */
    private function criarDOCXDeXML($documentXML, $outputPath)
    {
        // Criar arquivo ZIP temporário
        $zip = new \ZipArchive();
        $tempZip = tempnam(sys_get_temp_dir(), 'docx_');
        
        if ($zip->open($tempZip, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception("Não foi possível criar arquivo DOCX");
        }

        // Estrutura mínima DOCX
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>');

        $zip->addFromString('word/_rels/document.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
</Relationships>');

        // Adicionar o documento principal
        $zip->addFromString('word/document.xml', $documentXML);

        $zip->close();

        // Mover arquivo para destino final
        if (!rename($tempZip, $outputPath)) {
            unlink($tempZip);
            throw new \Exception("Não foi possível mover arquivo DOCX para destino");
        }

        \Log::info('Arquivo DOCX criado com sucesso', [
            'output_path' => $outputPath,
            'file_size' => filesize($outputPath)
        ]);
    }

    /**
     * Converter arquivo RTF para DOCX real usando comando externo
     * Isso resolve problemas de encoding que o OnlyOffice tem com RTF
     */
    private function converterRTFParaDOCX($rtfPath)
    {
        try {
            \Log::info('Iniciando conversão RTF para DOCX', [
                'rtf_path' => $rtfPath,
                'file_exists' => file_exists($rtfPath),
                'file_size' => file_exists($rtfPath) ? filesize($rtfPath) : 0
            ]);

            // Verificar se arquivo RTF existe
            if (!file_exists($rtfPath)) {
                throw new \Exception("Arquivo RTF não encontrado: " . $rtfPath);
            }

            // Criar arquivo temporário para a conversão
            $tempDir = sys_get_temp_dir();
            $tempRtf = $tempDir . '/' . uniqid('rtf_') . '.rtf';
            $tempDocx = $tempDir . '/' . uniqid('docx_') . '.docx';

            // Copiar arquivo original para temporário
            copy($rtfPath, $tempRtf);

            // Tentar conversão usando pandoc (se disponível)
            $pandocCmd = "pandoc '$tempRtf' -o '$tempDocx' 2>&1";
            $pandocOutput = [];
            $pandocReturn = 0;
            exec($pandocCmd, $pandocOutput, $pandocReturn);

            if ($pandocReturn === 0 && file_exists($tempDocx)) {
                // Conversão com pandoc foi bem-sucedida
                copy($tempDocx, $rtfPath);
                unlink($tempRtf);
                unlink($tempDocx);
                
                \Log::info('Conversão RTF para DOCX bem-sucedida usando pandoc', [
                    'new_file_size' => filesize($rtfPath)
                ]);
                return;
            }

            // Se pandoc não funcionou, usar LibreOffice
            $libreofficeCmd = "libreoffice --headless --convert-to docx --outdir '$tempDir' '$tempRtf' 2>&1";
            $libreOutput = [];
            $libreReturn = 0;
            exec($libreofficeCmd, $libreOutput, $libreReturn);

            // O LibreOffice gera arquivo com nome baseado no input
            $expectedDocx = $tempDir . '/' . pathinfo($tempRtf, PATHINFO_FILENAME) . '.docx';

            if (file_exists($expectedDocx)) {
                // Conversão com LibreOffice foi bem-sucedida
                copy($expectedDocx, $rtfPath);
                unlink($tempRtf);
                unlink($expectedDocx);
                
                \Log::info('Conversão RTF para DOCX bem-sucedida usando LibreOffice', [
                    'new_file_size' => filesize($rtfPath)
                ]);
                return;
            }

            // Se ambos falharam, usar conversão manual simples
            $this->converterRTFParaDOCXManual($rtfPath);

        } catch (\Exception $e) {
            \Log::warning('Falha na conversão RTF para DOCX', [
                'error' => $e->getMessage(),
                'rtf_path' => $rtfPath
            ]);
            
            // Se conversão falhar, manter arquivo original
            // O OnlyOffice pode ainda conseguir abrir, mesmo com problemas de encoding
        }
    }

    /**
     * Conversão manual de RTF para formato mais simples
     */
    private function converterRTFParaDOCXManual($rtfPath)
    {
        try {
            $rtfContent = file_get_contents($rtfPath);
            
            // Extrair texto básico do RTF removendo códigos de formatação
            $texto = $this->extrairTextoDoRTF($rtfContent);
            
            // Criar DOCX simples com o texto extraído
            $this->criarDOCXSimples($texto, $rtfPath);
            
            \Log::info('Conversão RTF para DOCX manual bem-sucedida', [
                'new_file_size' => filesize($rtfPath)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Falha na conversão manual RTF para DOCX', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Decodificar sequências Unicode RTF (\u123*) para caracteres
     */
    private function decodificarUnicodeRTF($rtfContent)
    {
        // Decodificar sequências Unicode como \u36*\u123*\u116*... para texto
        return preg_replace_callback('/(?:\\\\u(\d+)\*)+/', function($matches) {
            $fullMatch = $matches[0];
            $texto = '';
            
            // Extrair todos os números Unicode da sequência
            if (preg_match_all('/\\\\u(\d+)\*/', $fullMatch, $unicodeMatches)) {
                foreach ($unicodeMatches[1] as $unicode) {
                    $char = chr((int)$unicode);
                    $texto .= $char;
                }
            }
            
            return $texto;
        }, $rtfContent);
    }

    /**
     * Extrair texto básico do RTF removendo códigos de formatação
     */
    /**
     * Extração inteligente de texto RTF (fallback melhorado)
     */
    private function extrairTextoRTFInteligente($rtfContent)
    {
        \Log::info('Usando extração RTF inteligente focada em conteúdo com escape sequences');
        
        $texto = $rtfContent;
        
        // ETAPA 1: Converter RTF escape sequences para UTF-8 primeiro
        $escapeSequences = [
            "\\'e1" => 'á', "\\'e0" => 'à', "\\'e2" => 'â', "\\'e3" => 'ã',
            "\\'e9" => 'é', "\\'ea" => 'ê', "\\'ed" => 'í',
            "\\'f3" => 'ó', "\\'f4" => 'ô', "\\'f5" => 'õ',
            "\\'fa" => 'ú', "\\'e7" => 'ç',
            "\\'c1" => 'Á', "\\'c0" => 'À', "\\'c2" => 'Â', "\\'c3" => 'Ã',
            "\\'c9" => 'É', "\\'ca" => 'Ê', "\\'cd" => 'Í',
            "\\'d3" => 'Ó', "\\'d4" => 'Ô', "\\'d5" => 'Õ',
            "\\'da" => 'Ú', "\\'c7" => 'Ç'
        ];
        
        foreach ($escapeSequences as $rtf => $utf8) {
            $texto = str_replace($rtf, $utf8, $texto);
        }
        
        // ETAPA 2: Buscar especificamente o corpo do documento
        if (preg_match('/\\\\paperw.*$/s', $texto, $matches)) {
            $corpoDocumento = $matches[0];
            $texto = $corpoDocumento;
            \Log::info('Corpo extraído para fallback inteligente', [
                'tamanho_corpo' => strlen($corpoDocumento)
            ]);
        }
        
        // ETAPA 3: Buscar texto dentro de chaves que contenha palavras reais 
        $fragmentosTexto = [];
        if (preg_match_all('/\{([^{}]*[a-zA-ZÀ-ÿ]{3,}[^{}]*)\}/u', $texto, $matches)) {
            foreach ($matches[1] as $fragmento) {
                // Limpar códigos RTF do fragmento
                $fragmentoLimpo = preg_replace('/\\\\[a-z]+\d*\s*/', ' ', $fragmento);
                $fragmentoLimpo = trim($fragmentoLimpo);
                
                // Manter apenas fragmentos com conteúdo significativo
                if (strlen($fragmentoLimpo) > 10 && 
                    preg_match('/[a-zA-ZÀ-ÿ]{4,}/', $fragmentoLimpo) &&
                    !preg_match('/^[0-9\s\-\*]+$/', $fragmentoLimpo)) {
                    $fragmentosTexto[] = $fragmentoLimpo;
                }
            }
        }
        
        // ETAPA 4: Se não achou nada nas chaves, buscar texto livre no corpo
        if (empty($fragmentosTexto)) {
            // Buscar sequências longas de texto com palavras portuguesas
            if (preg_match_all('/([a-zA-ZÀ-ÿ][^\\\\{}\n\r]{15,})/u', $texto, $matches)) {
                foreach ($matches[1] as $sequencia) {
                    $sequencia = trim($sequencia);
                    if (strlen($sequencia) > 15 && 
                        preg_match('/[a-zA-ZÀ-ÿ]{4,}/', $sequencia)) {
                        $fragmentosTexto[] = $sequencia;
                    }
                }
            }
        }
        
        // Combinar fragmentos encontrados
        if (!empty($fragmentosTexto)) {
            $resultado = implode(' ', $fragmentosTexto);
            $resultado = preg_replace('/\s+/', ' ', $resultado);
            $resultado = trim($resultado);
            
            \Log::info('Extração RTF inteligente finalizada', [
                'fragmentos_encontrados' => count($fragmentosTexto),
                'tamanho_resultado' => strlen($resultado),
                'preview' => substr($resultado, 0, 150)
            ]);
            
            return $resultado;
        }
        
        // Se tudo falhar, usar método simples original
        return $this->extrairTextoRTFSimples($rtfContent);
    }
    
    /**
     * Extração simples de texto RTF (fallback) - APENAS TEXTO LIMPO
     */
    private function extrairTextoRTFSimples($rtfContent)
    {
        \Log::info('Usando extração RTF ultra-simplificada - apenas texto limpo');
        
        // ETAPA 0: Decodificar sequências Unicode RTF primeiro
        $texto = $this->decodificarUnicodeRTF($rtfContent);
        \Log::info('Unicode decodificado na extração simples', [
            'preview' => substr($texto, 0, 200)
        ]);
        
        // ETAPA 1: Converter escape sequences RTF para UTF-8
        $escapeSequences = [
            "\\'e1" => 'á', "\\'e0" => 'à', "\\'e2" => 'â', "\\'e3" => 'ã',
            "\\'e9" => 'é', "\\'ea" => 'ê', "\\'ed" => 'í',
            "\\'f3" => 'ó', "\\'f4" => 'ô', "\\'f5" => 'õ',
            "\\'fa" => 'ú', "\\'e7" => 'ç',
            "\\'c1" => 'Á', "\\'c0" => 'À', "\\'c2" => 'Â', "\\'c3" => 'Ã',
            "\\'c9" => 'É', "\\'ca" => 'Ê', "\\'cd" => 'Í',
            "\\'d3" => 'Ó', "\\'d4" => 'Ô', "\\'d5" => 'Õ',
            "\\'da" => 'Ú', "\\'c7" => 'Ç'
        ];
        
        foreach ($escapeSequences as $rtf => $utf8) {
            $texto = str_replace($rtf, $utf8, $texto);
        }
        
        // ETAPA 2: Buscar APENAS o corpo do documento
        if (preg_match('/\\\\paperw.*$/s', $texto, $matches)) {
            $texto = $matches[0];
        }
        
        // ETAPA 3: REMOVER COMPLETAMENTE todos os códigos RTF (mas preservar variáveis ${...})
        $texto = preg_replace('/\{\\\\[^{}]*\}/', ' ', $texto);  // Remove grupos RTF entre chaves (que começam com \)
        $texto = preg_replace('/\\\\[a-zA-Z]+\d*/', ' ', $texto);  // Remove comandos RTF
        $texto = preg_replace('/\\\\[^a-zA-Z0-9]/', ' ', $texto);  // Remove escapes
        $texto = str_replace(['\\'], ' ', $texto);  // Remove chars especiais (mas manter { } para variáveis)
        
        // ETAPA 4: Extrair variáveis e palavras individuais (mais permissivo)
        $fragmentos = [];
        
        // Primeiro, extrair variáveis do template (${...})
        if (preg_match_all('/\$\{[^}]+\}/', $texto, $variableMatches)) {
            $fragmentos = array_merge($fragmentos, $variableMatches[0]);
        }
        
        // Depois, extrair palavras em português
        if (preg_match_all('/[a-zA-ZÀ-ÿ]+/', $texto, $wordMatches)) {
            // Lista de palavras do sistema para filtrar
            $palavrasProibidas = ['Application', 'Document', 'System', 'Windows', 'File', 'Edit', 'View', 'Insert', 'Format', 'Tools', 'Help'];
            
            $palavrasValidas = array_filter($wordMatches[0], function($palavra) use ($palavrasProibidas) {
                $palavra = trim($palavra);
                return strlen($palavra) >= 3 &&  // Pelo menos 3 caracteres
                       !preg_match('/^[0-9]+$/', $palavra) &&  // Não é só números
                       !preg_match('/^[A-Z]{1,3}$/', $palavra) &&  // Não é só siglas curtas
                       !in_array(ucfirst(strtolower($palavra)), $palavrasProibidas) &&  // Não é palavra do sistema
                       preg_match('/[a-zA-ZÀ-ÿ]/', $palavra);  // Contém letras válidas
            });
            
            $fragmentos = array_merge($fragmentos, $palavrasValidas);
        }
        
        if (!empty($fragmentos)) {
            $palavrasValidas = $fragmentos;
            
            if (!empty($palavrasValidas)) {
                $texto = implode(' ', $palavrasValidas);
                \Log::info('Palavras válidas extraídas', [
                    'quantidade' => count($palavrasValidas),
                    'palavras' => array_slice($palavrasValidas, 0, 10) // Log primeiras 10
                ]);
            }
        }
        
        // ETAPA 5: Se não achou nada com filtro português, usar filtro mais amplo
        if (strlen(trim($texto)) < 10) {
            // Reset e buscar qualquer sequência de letras
            $texto = $rtfContent;
            
            // Aplicar conversões básicas
            foreach ($escapeSequences as $rtf => $utf8) {
                $texto = str_replace($rtf, $utf8, $texto);
            }
            
            // Remover códigos e extrair apenas texto
            $texto = preg_replace('/\\\\[a-zA-Z]+\d*/', ' ', $texto);
            $texto = preg_replace('/[{}\\\\]/', ' ', $texto);
            
            if (preg_match_all('/[a-zA-ZÀ-ÿ\s]{10,}/', $texto, $matches)) {
                $frasesLongas = array_filter($matches[0], function($frase) {
                    return strlen(trim($frase)) > 10;
                });
                
                if (!empty($frasesLongas)) {
                    $texto = implode(' ', $frasesLongas);
                }
            }
        }
        
        // ETAPA 6: Limpeza final
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = trim($texto);
        
        \Log::info('Extração RTF ultra-simplificada finalizada', [
            'tamanho_resultado' => strlen($texto),
            'preview' => substr($texto, 0, 200)
        ]);
        
        return $texto;
    }

    /**
     * Criar DOCX simples com texto limpo
     */
    private function criarDOCXSimples($texto, $outputPath)
    {
        $documentXML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <w:p>
            <w:r>
                <w:t>' . htmlspecialchars($texto, ENT_XML1, 'UTF-8') . '</w:t>
            </w:r>
        </w:p>
    </w:body>
</w:document>';

        $this->criarDOCXDeXML($documentXML, $outputPath);
    }

    /**
     * Converter proposição para PDF (usado quando volta para parlamentar assinar)
     */
    private function converterProposicaoParaPDF(Proposicao $proposicao): void
    {
        // Determinar nome do PDF
        $nomePdf = 'proposicao_' . $proposicao->id . '.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        // Se existe arquivo físico, tentar converter com LibreOffice
        if ($proposicao->arquivo_path && \Storage::exists($proposicao->arquivo_path)) {
            $caminhoArquivo = storage_path('app/' . $proposicao->arquivo_path);
            
            \Log::info('Convertendo proposição para PDF (com arquivo físico)', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $proposicao->arquivo_path,
                'caminho_absoluto' => $caminhoArquivo,
                'arquivo_existe' => file_exists($caminhoArquivo)
            ]);
            
            $this->tentarConversaoComLibreOffice($caminhoArquivo, $caminhoPdfAbsoluto, $proposicao);
        } else {
            \Log::info('Proposição sem arquivo físico, gerando PDF do conteúdo do banco', [
                'proposicao_id' => $proposicao->id,
                'has_content' => !empty($proposicao->conteudo)
            ]);
            
            // Usar DomPDF diretamente para proposições sem arquivo físico
            $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
        }

        // Atualizar proposição com caminho do PDF
        $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
        $proposicao->save();
        
        \Log::info('Proposição convertida para PDF', [
            'proposicao_id' => $proposicao->id,
            'arquivo_original' => $proposicao->arquivo_path,
            'arquivo_pdf' => $caminhoPdfRelativo
        ]);
    }

    /**
     * Tentar conversão com LibreOffice
     */
    private function tentarConversaoComLibreOffice(string $caminhoArquivo, string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        // Verificar se LibreOffice está disponível
        $libreOfficeDisponivel = $this->libreOfficeDisponivel();
        \Log::info('Verificando LibreOffice', [
            'disponivel' => $libreOfficeDisponivel,
            'caminho_pdf' => $caminhoPdfAbsoluto
        ]);
        
        if (!$libreOfficeDisponivel) {
            // Fallback: Criar um PDF usando DomPDF
            \Log::warning('LibreOffice não disponível, usando DomPDF como fallback');
            $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
        } else {
            // Converter para PDF usando LibreOffice
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg(dirname($caminhoPdfAbsoluto)),
                escapeshellarg($caminhoArquivo)
            );
            
            \Log::info('Executando comando LibreOffice', [
                'comando' => $comando
            ]);
            
            exec($comando, $output, $returnCode);
            
            \Log::info('Resultado do comando LibreOffice', [
                'return_code' => $returnCode,
                'output' => $output,
                'pdf_existe' => file_exists($caminhoPdfAbsoluto)
            ]);
            
            if ($returnCode !== 0) {
                \Log::warning('LibreOffice falhou, usando DomPDF como fallback', [
                    'return_code' => $returnCode,
                    'output' => $output
                ]);
                $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
            } else if (!file_exists($caminhoPdfAbsoluto)) {
                \Log::warning('PDF não foi criado pelo LibreOffice, usando DomPDF como fallback');
                $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
            }
        }
    }

    /**
     * Verificar se LibreOffice está disponível
     */
    private function libreOfficeDisponivel(): bool
    {
        exec('which libreoffice', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Servir arquivo PDF da proposição com controle de acesso
     */
    public function servePDF(Proposicao $proposicao)
    {
        // Verificar se o usuário tem permissão para ver este PDF
        $user = Auth::user();
        
        // Permitir acesso para:
        // 1. Autor da proposição (parlamentar) - especialmente para status 'protocolado'
        // 2. Usuários do legislativo
        // 3. Usuários com perfil jurídico
        // 4. Usuários do protocolo
        if (!$user->isLegislativo() && $proposicao->autor_id !== $user->id && !$user->isAssessorJuridico() && !$user->isProtocolo()) {
            abort(403, 'Acesso negado.');
        }

        // Para parlamentares, permitir apenas em status específicos onde o PDF já está disponível
        if ($user->isParlamentar() && $proposicao->autor_id === $user->id) {
            $statusPermitidos = ['protocolado', 'aprovado', 'assinado', 'enviado_protocolo', 'retornado_legislativo'];
            if (!in_array($proposicao->status, $statusPermitidos)) {
                abort(403, 'PDF não disponível para download neste status.');
            }
        }

        // Verificar se o PDF existe
        if (!$proposicao->arquivo_pdf_path) {
            abort(404, 'PDF não encontrado.');
        }

        $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
        
        if (!file_exists($pdfPath)) {
            abort(404, 'Arquivo PDF não encontrado.');
        }

        // Servir o arquivo PDF
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="proposicao_' . $proposicao->id . '.pdf"'
        ]);
    }

    /**
     * Criar PDF usando DomPDF como fallback
     */
    private function criarPDFComDomPDF(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {
            // Ler conteúdo do arquivo original
            $conteudoOriginal = '';
            if ($proposicao->arquivo_path && \Storage::exists($proposicao->arquivo_path)) {
                $conteudoOriginal = \Storage::get($proposicao->arquivo_path);
                // Remover tags RTF básicas se for RTF
                $conteudoOriginal = strip_tags($conteudoOriginal);
                $conteudoOriginal = preg_replace('/\\{[^}]*\\}/', '', $conteudoOriginal);
                $conteudoOriginal = trim($conteudoOriginal);
            }

            // Usar conteúdo do banco se arquivo não existir
            $conteudo = $conteudoOriginal ?: $proposicao->conteudo;

            // Criar HTML simples para o PDF se o template não existir
            $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);

            // Usar DomPDF para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // Salvar PDF
            file_put_contents($caminhoPdfAbsoluto, $pdf->output());

            \Log::info('PDF criado com DomPDF', [
                'proposicao_id' => $proposicao->id,
                'caminho' => $caminhoPdfAbsoluto,
                'tamanho' => filesize($caminhoPdfAbsoluto)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar PDF com DomPDF', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Gerar HTML simples para PDF
     */
    private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Proposição {$proposicao->id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .title { font-size: 18px; font-weight: bold; margin: 10px 0; }
                .info { font-size: 12px; color: #666; margin: 5px 0; }
                .content { margin-top: 30px; text-align: justify; }
                .ementa { background: #f5f5f5; padding: 15px; margin: 20px 0; border-left: 4px solid #007bff; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>CÂMARA MUNICIPAL</h1>
                <div class='title'>" . strtoupper($proposicao->tipo) . " Nº {$proposicao->id}/{$proposicao->ano}</div>
                <div class='info'>Autor: " . ($proposicao->autor->name ?? 'N/A') . "</div>
                <div class='info'>Data: " . $proposicao->created_at->format('d/m/Y') . "</div>
            </div>
            
            <div class='ementa'>
                <strong>EMENTA:</strong><br>
                " . nl2br(htmlspecialchars($proposicao->ementa)) . "
            </div>
            
            <div class='content'>
                " . nl2br(htmlspecialchars($conteudo ?: 'Conteúdo não disponível')) . "
            </div>
        </body>
        </html>";
    }
}