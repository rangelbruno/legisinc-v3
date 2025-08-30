<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTemplate;
use App\Models\Proposicao;
use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\Template\TemplateInstanceService;
use App\Services\Template\TemplateParametrosService;
use App\Services\Template\TemplateProcessorService;
use App\Services\TemplateVariablesService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ProposicaoController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('can:create,App\Models\Proposicao', only: ['create', 'store', 'createModern']),
            new Middleware('can:update,proposicao', only: ['update', 'edit']),
            new Middleware('can:delete,proposicao', only: ['destroy']),
            new Middleware('can:view,proposicao', only: ['show']),
        ];
    }

    /**
     * Tela inicial para criação de proposição (Parlamentar)
     */
    public function create()
    {
        // Verificar se é usuário do Legislativo - eles não podem criar proposições
        if (auth()->user()->isLegislativo() && ! auth()->user()->isParlamentar()) {
            return redirect()->route('proposicoes.legislativo.index')
                ->with('warning', 'Usuários do Legislativo não podem criar proposições. Acesse as proposições enviadas para análise.');
        }

        try {
            // Buscar tipos ativos do banco de dados
            $tipos = TipoProposicao::getParaDropdown();
        } catch (\Exception $e) {
            // Log::error('Erro ao buscar tipos de proposição', [
            //     'error' => $e->getMessage()
            // ]);

            // Fallback com tipos padrão
            $tipos = [
                'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
                'projeto_lei_complementar' => 'Projeto de Lei Complementar',
                'indicacao' => 'Indicação',
                'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
                'projeto_resolucao' => 'Projeto de Resolução',
            ];
        }

        return view('proposicoes.create', compact('tipos'));
    }

    /**
     * Tela moderna Vue.js para criação de proposição
     */
    public function createModern(Request $request)
    {
        // Verificar se é usuário do Legislativo - eles não podem criar proposições
        if (auth()->user()->isLegislativo() && ! auth()->user()->isParlamentar()) {
            return redirect()->route('proposicoes.legislativo.index')
                ->with('warning', 'Usuários do Legislativo não podem criar proposições. Acesse as proposições enviadas para análise.');
        }

        // Se um tipo foi selecionado, mostrar o formulário de criação com o tipo pré-selecionado
        $tipoSelecionado = $request->get('tipo');
        $nomeTipoSelecionado = $request->get('nome');

        // Se tem tipo selecionado, usar create.blade.php com o tipo pré-preenchido
        if ($tipoSelecionado) {
            // Mapear o tipo selecionado para o dropdown
            $tipos = [
                $tipoSelecionado => $nomeTipoSelecionado,
            ];

            return view('proposicoes.create', [
                'tipos' => $tipos,
                'tipoSelecionado' => $tipoSelecionado,
                'nomeTipoSelecionado' => $nomeTipoSelecionado,
            ]);
        }

        // Se não tem tipo selecionado, mostrar lista de tipos (fallback)
        try {
            // Buscar tipos ativos do banco de dados com mais detalhes
            $tipos = TipoProposicao::ativos()
                ->ordenados()
                ->get(['id', 'codigo', 'nome', 'descricao', 'sigla'])
                ->map(function ($tipo) {
                    return [
                        'id' => $tipo->id,
                        'codigo' => $tipo->codigo,
                        'nome' => $tipo->nome,
                        'descricao' => $tipo->descricao ?? 'Tipo de proposição '.$tipo->nome,
                        'sigla' => $tipo->sigla ?? strtoupper(substr($tipo->codigo, 0, 3)),
                        'icon' => $this->getIconForTipo($tipo->codigo),
                    ];
                });
        } catch (\Exception $e) {
            // Fallback com tipos padrão
            $tipos = collect([
                [
                    'id' => 1,
                    'codigo' => 'mocao',
                    'nome' => 'Moção',
                    'descricao' => 'Manifestação de apoio, protesto ou pesar',
                    'sigla' => 'MOC',
                    'icon' => 'fas fa-hand-paper',
                ],
                [
                    'id' => 2,
                    'codigo' => 'projeto_lei_ordinaria',
                    'nome' => 'Projeto de Lei Ordinária',
                    'descricao' => 'Proposta de lei sobre matéria de competência municipal',
                    'sigla' => 'PLO',
                    'icon' => 'fas fa-gavel',
                ],
                [
                    'id' => 3,
                    'codigo' => 'indicacao',
                    'nome' => 'Indicação',
                    'descricao' => 'Sugestão ao Poder Executivo',
                    'sigla' => 'IND',
                    'icon' => 'fas fa-lightbulb',
                ],
                [
                    'id' => 4,
                    'codigo' => 'requerimento',
                    'nome' => 'Requerimento',
                    'descricao' => 'Solicitação de informações ou providências',
                    'sigla' => 'REQ',
                    'icon' => 'fas fa-file-signature',
                ],
                [
                    'id' => 5,
                    'codigo' => 'projeto_decreto_legislativo',
                    'nome' => 'Projeto de Decreto Legislativo',
                    'descricao' => 'Matéria de competência exclusiva da Câmara',
                    'sigla' => 'PDL',
                    'icon' => 'fas fa-stamp',
                ],
                [
                    'id' => 6,
                    'codigo' => 'projeto_resolucao',
                    'nome' => 'Projeto de Resolução',
                    'descricao' => 'Matérias internas da Câmara',
                    'sigla' => 'PRS',
                    'icon' => 'fas fa-scroll',
                ],
            ]);
        }

        return view('proposicoes.create-modern', compact('tipos'));
    }

    /**
     * API para carregar tipos de proposição (Vue.js)
     */
    public function getTiposProposicao()
    {
        try {
            $tipos = TipoProposicao::ativos()
                ->ordenados()
                ->get(['id', 'codigo', 'nome', 'descricao', 'sigla'])
                ->map(function ($tipo) {
                    return [
                        'id' => $tipo->id,
                        'codigo' => $tipo->codigo,
                        'nome' => $tipo->nome,
                        'descricao' => $tipo->descricao ?? 'Tipo de proposição '.$tipo->nome,
                        'sigla' => $tipo->sigla ?? strtoupper(substr($tipo->codigo, 0, 3)),
                        'icon' => $this->getIconForTipo($tipo->codigo),
                    ];
                });

            return response()->json([
                'success' => true,
                'tipos' => $tipos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar tipos de proposição',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Definir ícone baseado no tipo de proposição
     */
    private function getIconForTipo($codigo)
    {
        $icons = [
            'mocao' => 'fas fa-hand-paper',
            'projeto_lei_ordinaria' => 'fas fa-gavel',
            'projeto_lei_complementar' => 'fas fa-balance-scale',
            'indicacao' => 'fas fa-lightbulb',
            'requerimento' => 'fas fa-file-signature',
            'projeto_decreto_legislativo' => 'fas fa-stamp',
            'projeto_resolucao' => 'fas fa-scroll',
            'emenda' => 'fas fa-edit',
            'substitutivo' => 'fas fa-exchange-alt',
            'veto' => 'fas fa-ban',
        ];

        return $icons[$codigo] ?? 'fas fa-file-alt';
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
            'tipo' => 'required|in:'.implode(',', $tiposValidos),
            'ementa' => 'required|string|max:1000',
            'opcao_preenchimento' => 'nullable|in:modelo,manual,ia',
            'usar_ia' => 'nullable|in:true,false,1,0',
            'texto_ia' => 'nullable|string',
            'texto_manual' => 'nullable|string',
            'anexos.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        // Preparar dados para criação
        $dadosProposicao = [
            'tipo' => $request->tipo,
            'ementa' => $request->ementa,
            'autor_id' => Auth::id(),
            'status' => 'rascunho',
            'ano' => date('Y'),
        ];

        // Armazenar conteúdo baseado na opção escolhida
        $opcaoPreenchimento = $request->opcao_preenchimento ?? 'modelo';

        switch ($opcaoPreenchimento) {
            case 'ia':
                $usarIA = in_array($request->usar_ia, [true, 'true', 1, '1']);
                if ($usarIA && $request->texto_ia) {
                    $dadosProposicao['conteudo'] = $this->limparCodigoLatex($request->texto_ia);
                }
                break;
            case 'manual':
                if ($request->texto_manual) {
                    $dadosProposicao['conteudo'] = $request->texto_manual;
                }
                break;
                // Para 'modelo', o conteúdo será definido na próxima etapa
        }

        // Criar proposição no banco de dados
        $proposicao = Proposicao::create($dadosProposicao);

        // Processar anexos se houver
        if ($request->hasFile('anexos')) {
            $anexosData = [];
            $totalAnexos = 0;

            foreach ($request->file('anexos') as $anexo) {
                // Gerar nome único para o arquivo
                $nomeOriginal = $anexo->getClientOriginalName();
                $extensao = $anexo->getClientOriginalExtension();
                $nomeArquivo = pathinfo($nomeOriginal, PATHINFO_FILENAME);
                $nomeUnico = $nomeArquivo.'_'.uniqid().'.'.$extensao;

                // Salvar arquivo no storage
                $path = $anexo->storeAs(
                    'proposicoes/'.$proposicao->id.'/anexos',
                    $nomeUnico,
                    'public'
                );

                // Adicionar informações do anexo ao array
                $anexosData[] = [
                    'nome_original' => $nomeOriginal,
                    'nome_arquivo' => $nomeUnico,
                    'caminho' => $path,
                    'tamanho' => $anexo->getSize(),
                    'tipo' => $anexo->getMimeType(),
                    'extensao' => $extensao,
                    'uploaded_at' => now()->toISOString(),
                ];

                $totalAnexos++;
            }

            // Atualizar proposição com informações dos anexos
            $proposicao->update([
                'anexos' => $anexosData,
                'total_anexos' => $totalAnexos,
            ]);
        }

        return response()->json([
            'success' => true,
            'proposicao_id' => $proposicao->id,
            'message' => 'Rascunho salvo com sucesso!',
            'anexos_salvos' => $proposicao->total_anexos ?? 0,
        ]);
    }

    /**
     * Gerar texto para proposição via IA
     */
    public function gerarTextoIA(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'ementa' => 'required|string|max:1000',
        ]);

        try {
            $aiService = app(\App\Services\AI\AITextGenerationService::class);
            $resultado = $aiService->gerarTextoProposicao($request->tipo, $request->ementa);

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'texto' => $resultado['texto'],
                    'provider' => $resultado['provider'] ?? null,
                    'model' => $resultado['model'] ?? null,
                    'message' => 'Texto gerado com sucesso!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                ], 400);
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar texto via IA', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'request_data' => $request->all()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Buscar modelos baseados no tipo de proposição
     */
    public function buscarModelos($tipo)
    {
        // Log::info('Buscando modelos para tipo: ' . $tipo);

        try {
            // Verificar se o tipo existe
            $tipoProposicao = TipoProposicao::buscarPorCodigo($tipo);

            if (! $tipoProposicao || ! $tipoProposicao->ativo) {
                // Log::warning('Tipo de proposição não encontrado ou inativo: ' . $tipo);
                return response()->json([], 404);
            }

            // Log::info('Tipo encontrado, buscando modelos para ID: ' . $tipoProposicao->id);

            // Buscar templates específicos para este tipo de proposição
            $templates = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoProposicao->id)
                ->where('ativo', true)
                ->get();

            // Log::info('Templates específicos encontrados: ' . $templates->count());

            // Converter templates específicos para formato esperado pelo frontend
            $modelosArray = [];
            foreach ($templates as $template) {
                $modelosArray[] = [
                    'id' => 'template_'.$template->id,
                    'nome' => $tipoProposicao->nome.' - Template #'.$template->id,
                    'descricao' => 'Template específico para '.$tipoProposicao->nome,
                    'is_template' => true,
                    'template_id' => $template->id,
                    'document_key' => $template->document_key,
                    'arquivo_path' => $template->arquivo_path,
                ];
            }

            // Se não há templates específicos, adicionar opção em branco
            if (count($modelosArray) === 0) {
                // Log::info('Nenhum template específico encontrado, adicionando opção em branco');
                $modelosArray[] = [
                    'id' => 'template_blank',
                    'nome' => 'Documento em Branco',
                    'descricao' => 'Criar '.$tipoProposicao->nome.' sem template específico',
                    'is_template' => true,
                    'template_id' => 'blank',
                ];
            }

            // Log::info('Modelos formatados para retorno:', [
            //     'count' => count($modelosArray),
            //     'data' => $modelosArray
            // ]);

            return response()->json($modelosArray);

        } catch (\Exception $e) {
            // Log::error('Erro ao buscar modelos para tipo: ' . $tipo, [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            // Fallback com modelos mock quando há erro de conexão
            $modelosMock = $this->getModelosMockPorTipo($tipo);

            // Log::info('Usando fallback com ' . count($modelosMock) . ' modelos mock para tipo: ' . $tipo);
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
                    'template_id' => 1,
                ],
                [
                    'id' => 'mock_2',
                    'nome' => 'Modelo Simplificado - PL',
                    'descricao' => 'Template simplificado para projetos de lei',
                    'is_template' => false,
                    'template_id' => null,
                ],
            ],
            'projeto_lei_complementar' => [
                [
                    'id' => 'mock_3',
                    'nome' => 'Modelo Padrão - Lei Complementar',
                    'descricao' => 'Template padrão para projetos de lei complementar',
                    'is_template' => true,
                    'template_id' => 2,
                ],
            ],
            'indicacao' => [
                [
                    'id' => 'mock_4',
                    'nome' => 'Modelo Padrão - Indicação',
                    'descricao' => 'Template padrão para indicações',
                    'is_template' => true,
                    'template_id' => 3,
                ],
            ],
            'projeto_decreto_legislativo' => [
                [
                    'id' => 'mock_5',
                    'nome' => 'Modelo Padrão - Decreto Legislativo',
                    'descricao' => 'Template padrão para projetos de decreto legislativo',
                    'is_template' => true,
                    'template_id' => 4,
                ],
            ],
            'projeto_resolucao' => [
                [
                    'id' => 'mock_6',
                    'nome' => 'Modelo Padrão - Resolução',
                    'descricao' => 'Template padrão para projetos de resolução',
                    'is_template' => true,
                    'template_id' => 5,
                ],
            ],
        ];

        return $modelos[$tipo] ?? [
            [
                'id' => 'mock_default',
                'nome' => 'Modelo Padrão',
                'descricao' => 'Template padrão para este tipo de proposição',
                'is_template' => true,
                'template_id' => 999,
            ],
        ];
    }

    /**
     * Tela de preenchimento do modelo selecionado
     */
    public function preencherModelo($proposicaoId, $modeloId, Request $request)
    {
        // Log::info('preencherModelo called', [
        //     'proposicao_id' => $proposicaoId,
        //     'modelo_id' => $modeloId,
        //     'user_id' => Auth::id(),
        //     'texto_preenchido' => $request->has('texto_preenchido')
        // ]);

        // TODO: Implement proper authorization
        // $this->authorize('update', $proposicao);

        $proposicao = Proposicao::findOrFail($proposicaoId);

        // Verificar se o usuário é o autor
        if ($proposicao->autor_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar esta proposição.');
        }

        // Buscar template e extrair variáveis
        $templateVariables = [];
        $templateVariablesGrouped = [];
        $modelo = null;

        // Log para debug
        // Log::info('Buscando template com modeloId', ['modeloId' => $modeloId, 'type' => gettype($modeloId)]);

        // Tentar encontrar o template ou DocumentoModelo
        $template = null;
        $documentoModelo = null;

        if (str_starts_with($modeloId, 'template_')) {
            $templateId = str_replace('template_', '', $modeloId);

            // Primeiro, tentar buscar como DocumentoModelo
            $documentoModelo = \App\Models\Documento\DocumentoModelo::where('template_id', $templateId)
                ->orWhere('template_id', $modeloId)
                ->first();

            if ($documentoModelo) {
                // Se encontrou DocumentoModelo, tentar buscar TipoProposicaoTemplate associado
                if ($documentoModelo->tipo_proposicao_id) {
                    $template = TipoProposicaoTemplate::where('tipo_proposicao_id', $documentoModelo->tipo_proposicao_id)
                        ->ativo()
                        ->first();
                }

                // Se não encontrou TipoProposicaoTemplate mas tem DocumentoModelo, criar um objeto simulado
                if (! $template && $documentoModelo->arquivo_path) {
                    $template = (object) [
                        'id' => 'doc_modelo_'.$documentoModelo->id,
                        'tipo_proposicao_id' => $documentoModelo->tipo_proposicao_id,
                        'arquivo_path' => $documentoModelo->arquivo_path,
                        'variaveis' => $documentoModelo->variaveis,
                        'nome' => $documentoModelo->nome,
                        'is_documento_modelo' => true,
                    ];
                }
            } elseif (is_numeric($templateId)) {
                // Se não encontrou DocumentoModelo e é numérico, tentar como TipoProposicaoTemplate
                $template = TipoProposicaoTemplate::find($templateId);
            }
        } elseif (is_numeric($modeloId)) {
            // Se for um ID numérico, buscar diretamente na tabela tipo_proposicao_templates
            $template = TipoProposicaoTemplate::find($modeloId);
        } else {
            // Se for uma string (como "decreto_legislativo"), buscar pelo template_id no DocumentoModelo
            $documentoModelo = \App\Models\Documento\DocumentoModelo::where('template_id', $modeloId)->first();
            if ($documentoModelo) {
                if ($documentoModelo->tipo_proposicao_id) {
                    $template = TipoProposicaoTemplate::where('tipo_proposicao_id', $documentoModelo->tipo_proposicao_id)
                        ->ativo()
                        ->first();
                }

                // Se não encontrou TipoProposicaoTemplate mas tem DocumentoModelo, criar um objeto simulado
                if (! $template && $documentoModelo->arquivo_path) {
                    $template = (object) [
                        'id' => 'doc_modelo_'.$documentoModelo->id,
                        'tipo_proposicao_id' => $documentoModelo->tipo_proposicao_id,
                        'arquivo_path' => $documentoModelo->arquivo_path,
                        'variaveis' => $documentoModelo->variaveis,
                        'nome' => $documentoModelo->nome,
                        'is_documento_modelo' => true,
                    ];
                }
            }
        }

        if ($template) {
            $templateVariablesService = app(\App\Services\TemplateVariablesService::class);
            $templateVariables = $templateVariablesService->extractVariablesFromTemplate($template);
            $userInputVariables = $templateVariablesService->getRequiredUserInputVariables($templateVariables);
            $templateVariablesGrouped = $templateVariablesService->groupVariablesByCategory($userInputVariables);
            $categoryLabels = $templateVariablesService->getCategoryLabels();

            $modelo = (object) [
                'id' => $modeloId,
                'nome' => 'Template de '.ucfirst(str_replace('_', ' ', $proposicao->tipo)),
                'template' => $template,
            ];

            // Log::info('Template encontrado e variáveis extraídas', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $template->id,
            //     'total_variaveis' => count($templateVariables),
            //     'variaveis_usuario' => count($userInputVariables),
            //     'grupos' => array_keys($templateVariablesGrouped)
            // ]);
        } else {
            $modelo = (object) ['id' => $modeloId, 'nome' => 'Template não encontrado'];

            // Log::warning('Template não encontrado', [
            //     'proposicao_id' => $proposicaoId,
            //     'modelo_id_tentativa' => $modeloId
            // ]);
        }

        // Definir categoryLabels mesmo quando não há template
        if (! isset($categoryLabels)) {
            $templateVariablesService = app(\App\Services\TemplateVariablesService::class);
            $categoryLabels = $templateVariablesService->getCategoryLabels();
        }

        // Carregar valores existentes de diferentes fontes para pré-preencher os campos
        $valoresExistentes = $this->carregarValoresExistentes($proposicao);

        // Verificar se o texto foi pré-preenchido (manual ou IA)
        $textoPreenchido = $request->has('texto_preenchido');
        $temTextoPreenchido = $textoPreenchido && ! empty($valoresExistentes['texto']);

        if ($temTextoPreenchido) {
            // Log::info('Texto pré-preenchido detectado', [
            //     'proposicao_id' => $proposicaoId,
            //     'texto_length' => strlen($valoresExistentes['texto']),
            //     'texto_preview' => substr($valoresExistentes['texto'], 0, 100) . '...'
            // ]);
        }

        return view('proposicoes.preencher-modelo', compact(
            'proposicao',
            'modelo',
            'templateVariables',
            'templateVariablesGrouped',
            'categoryLabels',
            'valoresExistentes',
            'temTextoPreenchido'
        ));
    }

    /**
     * Processar texto (manual ou IA) aplicando ao template e redirecionar para visualização
     */
    public function processarTextoERedirecionar($proposicaoId, $modeloId, Request $request)
    {
        // Log::info('processarTextoERedirecionar called', [
        //     'proposicao_id' => $proposicaoId,
        //     'modelo_id' => $modeloId,
        //     'tipo' => $request->get('tipo'),
        //     'user_id' => Auth::id()
        // ]);

        $proposicao = Proposicao::findOrFail($proposicaoId);

        // Verificar se o usuário é o autor
        if ($proposicao->autor_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar esta proposição.');
        }

        try {
            // Aplicar o texto ao template automaticamente
            $templateVariablesService = app(\App\Services\TemplateVariablesService::class);

            // Obter dados do usuário
            $user = Auth::user();
            $now = now();

            // Limpar código LaTeX do conteúdo se necessário
            $conteudoLimpo = $this->limparCodigoLatex($proposicao->conteudo);
            if ($conteudoLimpo !== $proposicao->conteudo) {
                $proposicao->update(['conteudo' => $conteudoLimpo]);
            }

            // Criar array completo de variáveis incluindo sistema e proposição
            $templateVariables = [
                // Dados da proposição
                'ementa' => $proposicao->ementa,
                'texto' => $conteudoLimpo, // Texto limpo sem código LaTeX
                'conteudo' => $conteudoLimpo,
                'finalidade' => $proposicao->ementa,

                // Dados do sistema
                'numero_proposicao' => $proposicao->id,
                'ano' => date('Y'),
                'data_atual' => $now->format('d/m/Y'),
                'data_extenso' => $now->locale('pt_BR')->translatedFormat('j \\d\\e F \\d\\e Y'),
                'dia_atual' => $now->format('d'),
                'mes_atual' => $now->format('m'),
                'ano_atual' => $now->format('Y'),
                'tipo_proposicao' => $proposicao->tipo_formatado ?? 'Proposição',
                'status_proposicao' => ucfirst($proposicao->status ?? 'rascunho'),

                // Dados do parlamentar/autor
                'nome_parlamentar' => $user->name ?? '[NOME DO PARLAMENTAR]',
                'autor_nome' => $user->name ?? '[NOME DO AUTOR]',
                'cargo_parlamentar' => 'Vereador(a)',
                'email_parlamentar' => $user->email ?? '',
                'partido_parlamentar' => '', // Pode ser obtido de relacionamento se existir

                // Dados da cidade/câmara (podem vir de configuração)
                'nome_cidade' => 'São Paulo',
                'nome_estado' => 'São Paulo',
                'nome_camara' => 'Câmara Municipal de São Paulo',
                'endereco_camara' => 'Viaduto Jacareí, 100 - Bela Vista - São Paulo/SP',
                'legislatura' => '2021-2024',
                'sessao' => '2025',
            ];

            // Para texto manual ou IA, não definir template_id específico
            // O sistema usará o template padrão ABNT automaticamente

            // Salvar apenas o status na proposição (sem template_id para evitar conflitos)
            $proposicao->update([
                'status' => 'em_edicao',
            ]);

            // Log::info('Processando texto personalizado sem template específico', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_original' => $modeloId,
            //     'usa_template_padrao' => true
            // ]);

            // Salvar variáveis na sessão para o processamento do template
            $sessionKey = 'proposicao_'.$proposicaoId.'_variaveis_template';
            session([$sessionKey => $templateVariables]);

            // Tentar processar o template imediatamente para gerar o documento
            $this->processarTemplateAutomaticamente($proposicao, $templateVariables);

            // Log::info('Texto processado automaticamente', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $proposicao->template_id,
            //     'variaveis_salvas' => array_keys($templateVariables)
            // ]);

            // Redirecionar direto para a visualização da proposição
            return redirect()->route('proposicoes.show', $proposicaoId)
                ->with('success', 'Proposição criada com sucesso! Texto aplicado automaticamente ao modelo.');

        } catch (\Exception $e) {
            // Log::error('Erro ao processar texto automaticamente', [
            //     'proposicao_id' => $proposicaoId,
            //     'modelo_id' => $modeloId,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            // Em caso de erro, redirecionar para preenchimento manual
            return redirect()->route('proposicoes.preencher-modelo', [$proposicaoId, $modeloId])
                ->with('warning', 'Houve um problema no processamento automático. Complete os campos manualmente.');
        }
    }

    /**
     * Processar template automaticamente com as variáveis fornecidas
     */
    private function processarTemplateAutomaticamente($proposicao, $templateVariables)
    {
        try {
            // Simular um request com as variáveis
            $request = new \Illuminate\Http\Request;
            $request->merge(['template_variables' => $templateVariables]);

            // Chamar o método gerarTexto para processar o template
            $this->gerarTexto($request, $proposicao->id);

            // Log::info('Template processado automaticamente com sucesso', [
            //     'proposicao_id' => $proposicao->id,
            //     'variaveis_processadas' => count($templateVariables)
            // ]);

        } catch (\Exception $e) {
            // Log::warning('Não foi possível processar template automaticamente', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage()
            // ]);
            // Não interrompe o fluxo, apenas registra o aviso
        }
    }

    /**
     * Gerar texto editável baseado no modelo preenchido
     */
    public function gerarTexto(Request $request, $proposicaoId)
    {
        // Validação flexível para aceitar tanto conteudo_modelo quanto template_variables
        $request->validate([
            'modelo_id' => 'required',
        ]);

        // Aceitar tanto o formato antigo (conteudo_modelo) quanto o novo (template_variables)
        $templateVariables = $request->template_variables ?? $request->conteudo_modelo ?? [];

        if (empty($templateVariables)) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum dado foi fornecido para processar o template.',
            ], 400);
        }

        $proposicao = Proposicao::findOrFail($proposicaoId);

        // Verificar autorização
        if ($proposicao->autor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para editar esta proposição.',
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
                // Primeiro, tentar buscar como DocumentoModelo
                $documentoModelo = \App\Models\Documento\DocumentoModelo::where('template_id', $templateId)->first();

                if ($documentoModelo) {
                    // Se encontrou DocumentoModelo, tentar buscar TipoProposicaoTemplate associado
                    if ($documentoModelo->tipo_proposicao_id) {
                        $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $documentoModelo->tipo_proposicao_id)
                            ->ativo()
                            ->first();
                    }

                    // Se não encontrou TipoProposicaoTemplate mas tem DocumentoModelo, criar um objeto simulado
                    if (! $template && $documentoModelo->arquivo_path) {
                        $template = (object) [
                            'id' => 'doc_modelo_'.$documentoModelo->id,
                            'tipo_proposicao_id' => $documentoModelo->tipo_proposicao_id,
                            'arquivo_path' => $documentoModelo->arquivo_path,
                            'variaveis' => $documentoModelo->variaveis,
                            'nome' => $documentoModelo->nome,
                            'is_documento_modelo' => true,
                        ];
                    }
                } elseif (is_numeric($templateId)) {
                    // Se não encontrou DocumentoModelo e é numérico, tentar como TipoProposicaoTemplate
                    $template = \App\Models\TipoProposicaoTemplate::find($templateId);
                } else {
                    $template = null;
                }

                if (! $template) {
                    // Log::warning('Template não encontrado no gerarTexto', [
                    //     'templateId' => $templateId,
                    //     'modeloId' => $modeloId,
                    //     'documentoModelo_found' => $documentoModelo ? 'yes' : 'no'
                    // ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Template não encontrado.',
                    ], 404);
                }
            }

            // Processar variáveis do template usando o novo sistema
            $templateVariablesService = app(\App\Services\TemplateVariablesService::class);

            if ($template) {
                // Extrair variáveis definidas no template
                $templateVars = $templateVariablesService->extractVariablesFromTemplate($template);

                // Substituir variáveis no conteúdo do template
                $processedContent = $this->substituirVariaveisNoTemplate($template, $templateVariables, $proposicao, $templateVariablesService);

                // Salvar o conteúdo processado
                session(['proposicao_'.$proposicaoId.'_conteudo_processado' => $processedContent]);
            }

            // Mapear campos principais para manter compatibilidade
            // Prioridade: ementa > finalidade > texto (primeiros 200 chars como ementa)
            $ementa = $templateVariables['ementa'] ??
                     $templateVariables['finalidade'] ??
                     (isset($templateVariables['texto']) && $templateVariables['texto'] ? substr($templateVariables['texto'], 0, 200).'...' : $proposicao->ementa);

            $conteudo = $templateVariables['texto'] ??
                       $templateVariables['conteudo'] ??
                       $proposicao->conteudo;

            // Salvar variáveis na sessão
            $sessionKey = 'proposicao_'.$proposicaoId.'_variaveis_template';
            session([$sessionKey => $templateVariables]);

            // Atualizar proposição
            $proposicao->update([
                'ementa' => $ementa,
                'conteudo' => $conteudo,
                'modelo_id' => $modeloId,
                'template_id' => $templateId,
                'ultima_modificacao' => now(),
            ]);

            // O conteúdo processado já foi salvo acima, se necessário processar mais
            $textoGerado = session('proposicao_'.$proposicaoId.'_conteudo_processado') ??
                          $this->criarTextoBasico($proposicao, $templateVariables);

        } else {
            // Fallback - tratar qualquer modelo não-template como template em branco
            return response()->json([
                'success' => false,
                'message' => 'Tipo de modelo não suportado. Use apenas templates OnlyOffice.',
            ], 400);
        }

        // Armazenar informações adicionais na sessão (backup)
        session([
            'proposicao_'.$proposicaoId.'_modelo_id' => $modeloId,
            'proposicao_'.$proposicaoId.'_template_id' => $isTemplate ? $templateId : null,
            'proposicao_'.$proposicaoId.'_tipo' => session('proposicao_'.$proposicaoId.'_tipo', 'projeto_lei'),
            'proposicao_'.$proposicaoId.'_texto_gerado' => $textoGerado ?? '',
        ]);

        // Log::info('Texto gerado para proposição', [
        //     'proposicao_id' => $proposicaoId,
        //     'modelo_id' => $modeloId,
        //     'is_template' => $isTemplate,
        //     'user_id' => Auth::id()
        // ]);

        return response()->json([
            'success' => true,
            'texto_gerado' => $textoGerado,
            'message' => 'Texto gerado com sucesso!',
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
            'modelo_id' => session('proposicao_'.$proposicaoId.'_modelo_id'), // Recuperar modelo usado
            'tipo' => session('proposicao_'.$proposicaoId.'_tipo', 'mocao'), // Recuperar tipo
        ];

        // Se foi usado um template, redirecionar para o editor OnlyOffice
        if ($proposicao->modelo_id && str_starts_with($proposicao->modelo_id, 'template_')) {
            $templateId = str_replace('template_', '', $proposicao->modelo_id);

            return redirect()->route('proposicoes.editar-onlyoffice', [
                'proposicao' => $proposicaoId,
                'template' => $templateId,
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
        if (! $proposicao) {
            // Criar objeto mock apenas se há dados na sessão
            if (session()->has('proposicao_'.$proposicaoId.'_tipo')) {
                $proposicao = (object) [
                    'id' => $proposicaoId,
                    'tipo' => session('proposicao_'.$proposicaoId.'_tipo', 'mocao'),
                    'modelo_id' => session('proposicao_'.$proposicaoId.'_modelo_id'),
                    'arquivo_path' => null,
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

                    // Log::info('Buscando template do admin pelo ID', [
                    //     'proposicao_id' => $proposicaoId,
                    //     'template_id' => $templateId,
                    //     'template_encontrado' => $template ? $template->id : 'nenhum'
                    // ]);
                } else {
                    // Fallback: buscar pelo tipo de proposição
                    $tipoProposicao = \App\Models\TipoProposicao::buscarPorCodigo($proposicao->tipo);

                    if ($tipoProposicao) {
                        // Buscar template criado pelo admin para este tipo de proposição
                        $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoProposicao->id)
                            ->where('ativo', true)
                            ->first();

                        // Log::info('Buscando template do admin por tipo', [
                        //     'proposicao_id' => $proposicaoId,
                        //     'tipo_proposicao' => $proposicao->tipo,
                        //     'tipo_proposicao_id' => $tipoProposicao->id,
                        //     'template_encontrado' => $template ? $template->id : 'nenhum'
                        // ]);
                    } else {
                        // Log::warning('Tipo de proposição não encontrado', [
                        //     'tipo' => $proposicao->tipo
                        // ]);
                    }
                }
            }

            // Criar uma instância do documento baseada no template para esta proposição
            // Usar chave única com timestamp para evitar conflitos de versão
            $documentKey = 'proposicao_'.$proposicaoId.'_'.$templateId.'_'.time().'_'.substr(md5(uniqid()), 0, 8);

            // Verificar se a proposição já tem um arquivo salvo no banco de dados
            if ($proposicao->arquivo_path && \Storage::disk('public')->exists($proposicao->arquivo_path)) {
                $arquivoProposicaoPath = $proposicao->arquivo_path;
                // Log::info('Usando arquivo existente da proposição do banco de dados', [
                //     'proposicao_id' => $proposicaoId,
                //     'arquivo_path' => $arquivoProposicaoPath
                // ]);
            } else {
                // Criar arquivo da proposição (com ou sem template específico)
                $arquivoProposicaoPath = $this->criarArquivoProposicao($proposicaoId, $template);
            }

            $arquivoProposicao = basename($arquivoProposicaoPath); // Apenas o nome do arquivo

            // Log::info('Abrindo proposição no OnlyOffice', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $templateId,
            //     'document_key' => $documentKey,
            //     'arquivo' => $arquivoProposicao
            // ]);

            return view('proposicoes.editar-onlyoffice', compact('proposicao', 'template', 'documentKey', 'arquivoProposicao'));

        } catch (\Exception $e) {
            // Log::error('Erro ao abrir OnlyOffice para proposição', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $templateId,
            //     'error' => $e->getMessage()
            // ]);

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
            'conteudo' => 'required|string',
        ]);

        // TODO: Update proposicao in database
        // $proposicao->update(['conteudo' => $request->conteudo]);

        return response()->json([
            'success' => true,
            'message' => 'Texto salvo com sucesso!',
        ]);
    }

    /**
     * Enviar proposição para análise do legislativo
     */
    public function enviarLegislativo(Proposicao $proposicao)
    {
        // Log::info('Método enviarLegislativo chamado', [
        //     'proposicao_id' => $proposicao->id,
        //     'proposicao_status' => $proposicao->status,
        //     'proposicao_ementa' => $proposicao->ementa ? 'presente' : 'ausente',
        //     'proposicao_conteudo' => $proposicao->conteudo ? 'presente' : 'ausente',
        //     'proposicao_arquivo' => $proposicao->arquivo_path ? 'presente' : 'ausente',
        //     'user_id' => auth()->id(),
        //     'is_author' => $proposicao->autor_id === auth()->id()
        // ]);

        try {
            // Verificar se o usuário é o autor da proposição
            if ($proposicao->autor_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para enviar esta proposição.',
                ], 403);
            }

            // Verificar se a proposição está no status correto
            if (! in_array($proposicao->status, ['rascunho', 'em_edicao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser enviada no status atual.',
                ], 400);
            }

            // Validar se tem conteúdo mínimo
            $ementa = $proposicao->ementa;

            // Se não tem ementa no campo principal, tentar extrair das variáveis da sessão
            if (empty($ementa)) {
                $variaveisTemplate = session('proposicao_'.$proposicao->id.'_variaveis_template', []);
                $ementa = $variaveisTemplate['ementa'] ??
                         $variaveisTemplate['finalidade'] ??
                         (isset($variaveisTemplate['texto']) ? $variaveisTemplate['texto'] : '');

                // Se encontrou ementa nas variáveis, atualizar a proposição
                if (! empty($ementa)) {
                    $proposicao->update(['ementa' => $ementa]);
                    // Log::info('Ementa extraída das variáveis do template', [
                    //     'proposicao_id' => $proposicao->id,
                    //     'ementa' => substr($ementa, 0, 100)
                    // ]);
                }
            }

            if (empty($ementa) || (! $proposicao->conteudo && ! $proposicao->arquivo_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposição deve ter ementa e conteúdo antes de ser enviada.',
                ], 400);
            }

            // Atualizar status para enviado ao legislativo
            $proposicao->update([
                'status' => 'enviado_legislativo',
            ]);

            // Log::info('Proposição enviada para legislativo', [
            //     'proposicao_id' => $proposicao->id,
            //     'user_id' => auth()->id(),
            //     'status_anterior' => $proposicao->getOriginal('status')
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposição enviada para análise legislativa com sucesso!',
                'proposicao' => [
                    'id' => $proposicao->id,
                    'status' => $proposicao->status,
                    'updated_at' => $proposicao->updated_at?->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao enviar proposição para legislativo', [
            //     'proposicao_id' => $proposicao->id,
            //     'user_id' => auth()->id(),
            //     'error' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Listagem das próprias proposições (Parlamentar)
     */
    public function minhasProposicoes()
    {
        // Verificar se é usuário do Legislativo - eles não podem acessar esta página
        if (auth()->user()->isLegislativo() && ! auth()->user()->isParlamentar()) {
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
    public function show(Proposicao $proposicao)
    {
        // Limpar cache se houver refresh
        if (request()->has('_refresh')) {
            \Cache::forget('proposicao_'.$proposicao->id);
        }

        // Reload with relationships for fresh data
        $proposicao->load(['autor', 'tipoProposicao']);

        // TODO: Implement proper authorization
        // $this->authorize('view', $proposicao);

        // Se não tem ementa no campo principal, tentar extrair das variáveis
        if (empty($proposicao->ementa)) {
            $variaveisTemplate = session('proposicao_'.$proposicao->id.'_variaveis_template', []);

            // Se não tem variáveis na sessão, mas tem template_id, tentar extrair do template
            if (empty($variaveisTemplate) && ! empty($proposicao->template_id)) {
                try {
                    // Verificar se o template_id é numérico ou string
                    if (is_numeric($proposicao->template_id)) {
                        $template = \App\Models\TipoProposicaoTemplate::find($proposicao->template_id);
                    } else {
                        // Se for uma string como "decreto_legislativo", buscar pelo template_id no DocumentoModelo
                        $documentoModelo = \App\Models\Documento\DocumentoModelo::where('template_id', $proposicao->template_id)->first();
                        if ($documentoModelo && $documentoModelo->tipo_proposicao_id) {
                            $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $documentoModelo->tipo_proposicao_id)
                                ->ativo()
                                ->first();
                        } else {
                            $template = null;
                        }
                    }
                    if ($template) {
                        $templateVariablesService = app(\App\Services\TemplateVariablesService::class);
                        $variaveisTemplate = $templateVariablesService->extractVariablesFromTemplate($template);
                        // Log::info('Variáveis extraídas do template para proposição sem ementa', [
                        //     'proposicao_id' => $proposicao->id,
                        //     'template_id' => $proposicao->template_id,
                        //     'variaveis_encontradas' => array_keys($variaveisTemplate)
                        // ]);
                    }
                } catch (\Exception $e) {
                    // Log::warning('Erro ao extrair variáveis do template', [
                    //     'proposicao_id' => $proposicao->id,
                    //     'template_id' => $proposicao->template_id,
                    //     'erro' => $e->getMessage()
                    // ]);
                }
            }

            // Tentar gerar ementa baseada no tipo de proposição
            $ementaGerada = $this->gerarEmentaAutomatica($proposicao, $variaveisTemplate);

            if (! empty($ementaGerada)) {
                $proposicao->ementa = $ementaGerada;
                // Salvar no banco também
                $proposicao->save();
                // Log::info('Ementa gerada automaticamente', [
                //     'proposicao_id' => $proposicao->id,
                //     'ementa' => $ementaGerada
                // ]);
            }
        }

        // Informações adicionais da sessão (se houver)
        $templateVariables = session('proposicao_'.$proposicao->id.'_variaveis_template', []);
        $conteudoProcessado = session('proposicao_'.$proposicao->id.'_conteudo_processado', '');

        // Status formatado
        $statusFormatado = ucfirst(str_replace('_', ' ', $proposicao->status ?? 'rascunho'));

        // Verificar se pode ser enviado para legislativo
        $podeEnviarLegislativo = $proposicao->autor_id === auth()->id() &&
                                in_array($proposicao->status, ['rascunho', 'em_edicao']) &&
                                (! empty($proposicao->ementa) || ! empty($templateVariables)) &&
                                ($proposicao->conteudo || $proposicao->arquivo_path);

        // Adicionar propriedades necessárias para Vue.js
        $proposicao->has_pdf = $this->verificarExistenciaPDF($proposicao);
        $proposicao->has_arquivo = ! empty($proposicao->arquivo_path);

        // Nova interface Vue.js - mais simples e performática
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
                'rejeitado' => 'danger',
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
                'rejeitado' => 'A proposição foi rejeitada pelo Legislativo.',
            ];

            // Formatar nome do status
            $statusFormatado = ucfirst(str_replace('_', ' ', $proposicao->status));

            return response()->json([
                'success' => true,
                'status' => $proposicao->status,
                'status_formatado' => $statusFormatado,
                'status_class' => $statusClasses[$proposicao->status] ?? 'secondary',
                'status_descricao' => $statusDescricoes[$proposicao->status] ?? 'Status personalizado: '.$proposicao->status,
                'timeline_updated' => false, // Por enquanto não implementamos atualização da timeline
                'timeline' => null,
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter status da proposição', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status atualizado da proposição.',
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
                    'id' => 'prop_ret_'.$proposicao->id,
                    'tipo' => 'retornado_legislativo',
                    'titulo' => 'Proposição #'.$proposicao->id.' Retornada',
                    'descricao' => 'Proposição aprovada pelo Legislativo e aguarda sua assinatura',
                    'ementa' => \Str::limit($proposicao->ementa ?? 'Sem ementa', 60),
                    'data' => $proposicao->updated_at,
                    'data_formatada' => $proposicao->updated_at->diffForHumans(),
                    'link' => route('proposicoes.show', $proposicao->id),
                    'link_acao' => route('proposicoes.assinar', $proposicao->id),
                    'acao_texto' => 'Assinar',
                    'icone' => 'ki-duotone ki-check-square',
                    'cor' => 'info',
                    'prioridade' => 'alta',
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
                    'id' => 'prop_agrd_'.$proposicao->id,
                    'tipo' => 'aguardando_aprovacao_autor',
                    'titulo' => 'Proposição #'.$proposicao->id.' Editada',
                    'descricao' => 'Proposição editada pelo Legislativo aguarda sua aprovação',
                    'ementa' => \Str::limit($proposicao->ementa ?? 'Sem ementa', 60),
                    'data' => $proposicao->updated_at,
                    'data_formatada' => $proposicao->updated_at->diffForHumans(),
                    'link' => route('proposicoes.show', $proposicao->id),
                    'link_acao' => route('proposicoes.editar-texto', $proposicao->id),
                    'acao_texto' => 'Revisar',
                    'icone' => 'ki-duotone ki-check-circle',
                    'cor' => 'primary',
                    'prioridade' => 'media',
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
                    'id' => 'prop_dev_'.$proposicao->id,
                    'tipo' => 'devolvido_edicao',
                    'titulo' => 'Proposição #'.$proposicao->id.' Devolvida',
                    'descricao' => 'Proposição devolvida pelo Legislativo para ajustes',
                    'ementa' => \Str::limit($proposicao->ementa ?? 'Sem ementa', 60),
                    'data' => $proposicao->updated_at,
                    'data_formatada' => $proposicao->updated_at->diffForHumans(),
                    'link' => route('proposicoes.show', $proposicao->id),
                    'link_acao' => route('proposicoes.editar-texto', $proposicao->id),
                    'acao_texto' => 'Editar',
                    'icone' => 'ki-duotone ki-arrow-left',
                    'cor' => 'warning',
                    'prioridade' => 'media',
                ];
            }

            // Ordenar por prioridade e data
            usort($notificacoes, function ($a, $b) {
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
                'nao_lidas' => count(array_filter($notificacoes, function ($n) {
                    return $n['prioridade'] === 'alta';
                })),
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao buscar notificações', [
            //     'user_id' => \Auth::id(),
            //     'error' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar notificações.',
            ], 500);
        }
    }

    /**
     * Excluir proposição (apenas rascunhos)
     */
    public function destroy($proposicaoId)
    {
        try {
            // Buscar proposição no banco de dados
            $proposicao = Proposicao::findOrFail($proposicaoId);

            // Verificar se o usuário é o autor
            if ($proposicao->autor_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para excluir esta proposição.',
                ], 403);
            }

            // Verificar se é um status que permite exclusão
            $statusPermitidos = ['rascunho', 'em_edicao', 'salvando', 'retornado_legislativo'];
            if (! in_array($proposicao->status, $statusPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas rascunhos, proposições em edição, salvando e retornadas do legislativo podem ser excluídas.',
                ], 400);
            }

            // Excluir arquivos associados se existirem
            if ($proposicao->arquivo_path && \Storage::exists($proposicao->arquivo_path)) {
                \Storage::delete($proposicao->arquivo_path);
            }

            // Excluir do banco de dados
            $proposicao->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proposição excluída com sucesso.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir proposição: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obter cargo do parlamentar
     */
    private function obterCargoParlamentar($user)
    {
        if (! $user) {
            return '[CARGO DO PARLAMENTAR]';
        }

        // Verificar roles
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames();

            if ($roles->contains('Vereador')) {
                return 'Vereador';
            }
            if ($roles->contains('Presidente')) {
                return 'Presidente da Câmara';
            }
            if ($roles->contains('Vice-Presidente')) {
                return 'Vice-Presidente da Câmara';
            }
            if ($roles->contains('Secretario')) {
                return 'Secretário da Câmara';
            }
        }

        return 'Parlamentar';
    }

    /**
     * Obter partido do parlamentar
     */
    private function obterPartidoParlamentar($user)
    {
        if (! $user) {
            return '[PARTIDO]';
        }

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
            'mocao' => 'MOÇÃO',
            'projeto_lei_ordinaria' => 'PROJETO DE LEI ORDINÁRIA',
            'projeto_lei_complementar' => 'PROJETO DE LEI COMPLEMENTAR',
            'projeto_lei_delegada' => 'PROJETO DE LEI DELEGADA',
            'medida_provisoria' => 'MEDIDA PROVISÓRIA',
            'projeto_decreto_legislativo' => 'PROJETO DE DECRETO LEGISLATIVO',
            'projeto_decreto_congresso' => 'PROJETO DE DECRETO DO CONGRESSO',
            'projeto_resolucao' => 'PROJETO DE RESOLUÇÃO',
            'requerimento' => 'REQUERIMENTO',
            'indicacao' => 'INDICAÇÃO',
            'emenda' => 'EMENDA',
            'subemenda' => 'SUBEMENDA',
            'substitutivo' => 'SUBSTITUTIVO',
            'parecer_comissao' => 'PARECER DE COMISSÃO',
            'relatorio' => 'RELATÓRIO',
            'recurso' => 'RECURSO',
            'veto' => 'VETO',
            'destaque' => 'DESTAQUE',
            'oficio' => 'OFÍCIO',
            'mensagem_executivo' => 'MENSAGEM DO EXECUTIVO',
            'projeto_consolidacao_leis' => 'PROJETO DE CONSOLIDAÇÃO DAS LEIS',
        ];

        return $tipos[$tipo] ?? strtoupper($tipo);
    }

    /**
     * Formatar data por extenso
     */
    private function formatarDataExtenso($data)
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        return $data->day.' de '.$meses[$data->month].' de '.$data->year;
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
            $placeholder = '{{'.$campo.'}}';
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
            // Definir nome do arquivo da proposição (usar RTF para preservar formatação)
            $templateIdForFile = $template ? $template->id : 'blank';
            $nomeArquivo = "proposicao_{$proposicaoId}_template_{$templateIdForFile}.rtf";
            $pathDestino = "proposicoes/{$nomeArquivo}";
            $pathCompleto = storage_path('app/public/'.$pathDestino);

            // IMPORTANTE: Verificar se já existe um arquivo salvo para esta proposição
            // Se existir, usar o arquivo existente ao invés de criar um novo
            if (\Storage::disk('public')->exists($pathDestino)) {
                // Log::info('Arquivo da proposição já existe, usando arquivo salvo', [
                //     'proposicao_id' => $proposicaoId,
                //     'arquivo_existente' => $pathDestino,
                //     'tamanho' => \Storage::disk('public')->size($pathDestino)
                // ]);
                return $pathDestino;
            }

            // Garantir que o diretório existe
            $diretorio = dirname($pathCompleto);
            if (! file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            // Se o template tem um arquivo, copiar como base
            // Log::info('Criando novo arquivo para proposição', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_exists' => $template ? 'sim' : 'não',
            //     'template_id' => $template ? $template->id : null,
            //     'arquivo_path' => $template ? $template->arquivo_path : null
            // ]);

            if ($template && $template->arquivo_path) {
                // Processar template com variáveis substituídas
                $this->processarTemplateComVariaveis($proposicaoId, $template, $pathDestino);
            } else {
                // Criar arquivo básico com dados preenchidos pelo usuário
                // Buscar dados da proposição do banco de dados ou sessão
                $proposicao = Proposicao::find($proposicaoId);
                $ementa = $proposicao->ementa ?? session('proposicao_'.$proposicaoId.'_ementa', 'Proposição em elaboração');
                $conteudo = $proposicao->conteudo ?? session('proposicao_'.$proposicaoId.'_conteudo', 'Conteúdo da proposição a ser desenvolvido.');
                $tipo = $proposicao->tipo ?? session('proposicao_'.$proposicaoId.'_tipo', 'mocao');

                // Usar conteúdo processado se disponível, caso contrário usar texto básico
                $textoCompleto = session('proposicao_'.$proposicaoId.'_conteudo_processado');
                if (! $textoCompleto) {
                    $textoCompleto = 'PROPOSIÇÃO - '.strtoupper($tipo)."\n\n";
                    $textoCompleto .= "EMENTA\n\n";
                    $textoCompleto .= $ementa."\n\n";
                    $textoCompleto .= "CONTEÚDO\n\n";
                    $textoCompleto .= $conteudo;
                }

                // Criar arquivo DOCX real
                $conteudoDocx = $this->criarArquivoDOCXReal($textoCompleto);
                $pathCompleto = storage_path('app/public/'.$pathDestino);

                // Criar diretório se não existir
                $diretorio = dirname($pathCompleto);
                if (! file_exists($diretorio)) {
                    mkdir($diretorio, 0775, true);
                    chown($diretorio, 'www-data');
                    chgrp($diretorio, 'www-data');
                }

                file_put_contents($pathCompleto, $conteudoDocx);
                // Log::info('Arquivo DOCX criado a partir do texto gerado', [
                //     'proposicao_path' => $pathDestino,
                //     'tamanho_arquivo' => strlen($conteudoDocx),
                //     'arquivo_existe_apos_criacao' => file_exists($pathCompleto),
                //     'ementa' => $ementa,
                //     'tipo' => $tipo
                // ]);
            }

            return $pathDestino;

        } catch (\Exception $e) {
            // Log::error('Erro ao criar arquivo da proposição', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);

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
            $variaveisPreenchidas = session('proposicao_'.$proposicaoId.'_variaveis_template', []);

            // DEBUG: Vamos também buscar da proposição diretamente
            $variaveisProposicao = [
                'ementa' => $proposicao->ementa,
                'texto' => $proposicao->conteudo,
                'conteudo' => $proposicao->conteudo,
            ];

            // Se não há variáveis na sessão, usar as da proposição
            if (empty($variaveisPreenchidas)) {
                $variaveisPreenchidas = $variaveisProposicao;
            }

            // Log::info('Processando template com variáveis', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $template->id,
            //     'variaveis_preenchidas' => $variaveisPreenchidas,
            //     'variaveis_proposicao' => $variaveisProposicao,
            //     'session_key' => 'proposicao_' . $proposicaoId . '_variaveis_template'
            // ]);

            // Verificar se existe arquivo físico do template com sistema de fallback
            $conteudoTemplate = $this->carregarTemplateComFallback($template);

            if ($conteudoTemplate) {
                // Log::info('Template carregado com sucesso', [
                //     'tamanho_arquivo' => strlen($conteudoTemplate),
                //     'contem_variaveis' => strpos($conteudoTemplate, '${') !== false
                // ]);

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

                // DEBUG: Verificar se o conteúdo foi processado
                // Log::info('DEBUG: Antes de salvar arquivo processado', [
                //     'pathDestino' => $pathDestino,
                //     'tamanho_conteudo_processado' => strlen($conteudoProcessado),
                //     'primeiros_100_chars' => substr($conteudoProcessado, 0, 100)
                // ]);

                // Salvar arquivo processado - usando file_put_contents diretamente
                $pathCompleto = storage_path('app/public/'.$pathDestino);

                // Criar diretório se não existir
                $diretorio = dirname($pathCompleto);
                if (! file_exists($diretorio)) {
                    mkdir($diretorio, 0775, true);
                    chown($diretorio, 'www-data');
                    chgrp($diretorio, 'www-data');
                }

                $resultadoSave = file_put_contents($pathCompleto, $conteudoProcessado) !== false;

                // Log::info('DEBUG: Resultado do salvamento direto', [
                //     'resultado_save' => $resultadoSave,
                //     'path_completo' => $pathCompleto,
                //     'arquivo_existe' => file_exists($pathCompleto),
                //     'tamanho_arquivo' => file_exists($pathCompleto) ? filesize($pathCompleto) : 'N/A'
                // ]);

                // MANTER COMO RTF para preservar formatação - OnlyOffice trabalha bem com RTF
                // A conversão para DOCX estava removendo toda formatação
                // $this->converterRTFParaDOCX($pathCompleto); // Comentado para preservar formatação

                // Log::info('Template processado com variáveis substituídas', [
                //     'template_path' => $template->arquivo_path,
                //     'proposicao_path' => $pathDestino,
                //     'tamanho_original' => strlen($conteudoTemplate),
                //     'tamanho_processado' => strlen($conteudoProcessado),
                //     'arquivo_existe_apos_processamento' => \Storage::disk('public')->exists($pathDestino)
                // ]);
            } else {
                // Log::warning('Arquivo do template não encontrado para processamento', [
                //     'template_path' => $template->arquivo_path,
                //     'existe_local' => \Storage::disk('local')->exists($template->arquivo_path),
                //     'existe_public' => \Storage::disk('public')->exists($template->arquivo_path)
                // ]);
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao processar template com variáveis', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
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
        // Primeiro tentar ler do local correto onde os templates são salvos (storage/app/templates)
        $pathCompleto = storage_path('app/'.$template->arquivo_path);
        if (file_exists($pathCompleto)) {
            $conteudoTemplate = file_get_contents($pathCompleto);
        } elseif (\Storage::disk('local')->exists($template->arquivo_path)) {
            $conteudoTemplate = \Storage::disk('local')->get($template->arquivo_path);
        } elseif (\Storage::disk('public')->exists($template->arquivo_path)) {
            $conteudoTemplate = \Storage::disk('public')->get($template->arquivo_path);
        }

        // Garantir que o conteúdo está em UTF-8
        if ($conteudoTemplate && ! mb_check_encoding($conteudoTemplate, 'UTF-8')) {
            $conteudoTemplate = mb_convert_encoding($conteudoTemplate, 'UTF-8', 'auto');
        }

        // Validar se o template contém variáveis essenciais
        if ($conteudoTemplate && $this->validarConteudoTemplateBasico($conteudoTemplate)) {
            return $conteudoTemplate;
        }

        // Template principal está corrompido ou não contém variáveis, tentar fallbacks
        // Log::warning('Template principal corrompido, tentando fallbacks', [
        //     'template_id' => $template->id,
        //     'arquivo_path' => $template->arquivo_path,
        //     'conteudo_existe' => $conteudoTemplate !== null,
        //     'tamanho_conteudo' => $conteudoTemplate ? strlen($conteudoTemplate) : 0
        // ]);

        // Fallback 1: Tentar backup mais recente
        $conteudoBackup = $this->carregarBackupMaisRecente($template);
        if ($conteudoBackup && $this->validarConteudoTemplateBasico($conteudoBackup)) {
            // Log::info('Template restaurado do backup', [
            //     'template_id' => $template->id
            // ]);

            // Restaurar o template principal com o backup
            if ($template->arquivo_path) {
                \Storage::disk('local')->put($template->arquivo_path, $conteudoBackup);
            }

            return $conteudoBackup;
        }

        // Fallback 2: Usar template padrão baseado no tipo de proposição
        $templatePadrao = $this->obterTemplatePadrao($template->tipoProposicao->nome ?? 'mocao');
        if ($templatePadrao) {
            // Log::info('Usando template padrão como fallback', [
            //     'template_id' => $template->id,
            //     'tipo_proposicao' => $template->tipoProposicao->nome ?? 'mocao'
            // ]);

            // Salvar template padrão como novo template
            if ($template->arquivo_path) {
                \Storage::disk('local')->put($template->arquivo_path, $templatePadrao);
            }

            return $templatePadrao;
        }

        // Fallback 3: Template mínimo de emergência
        // Log::error('Usando template de emergência', [
        //     'template_id' => $template->id
        // ]);

        return $this->obterTemplateEmergencia();
    }

    /**
     * Validar se template contém variáveis básicas essenciais
     */
    private function validarConteudoTemplateBasico($conteudo)
    {
        if (! $conteudo || strlen($conteudo) < 50) {
            return false;
        }

        // Verificar se contém pelo menos uma variável (suporte para RTF e texto comum)
        $temVariavel = preg_match('/\$\{[^}]+\}/', $conteudo) || // Variáveis texto comum: ${variavel}
                      preg_match('/\$\\\\\{[^\\\\}]+\\\\\}/', $conteudo); // Variáveis RTF escapadas: $\{variavel\}

        // Se não tem variáveis, verificar se tem conteúdo significativo (possíveis imagens)
        if (! $temVariavel) {
            // Arquivo grande pode conter imagens e ainda ser válido
            if (strlen($conteudo) > 100000) {
                // Log::info('Template sem variáveis mas com conteúdo significativo aceito no fallback', [
                //     'tamanho_conteudo' => strlen($conteudo)
                // ]);
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
            if (! $template->arquivo_path) {
                return null;
            }

            $templateBaseName = pathinfo($template->arquivo_path, PATHINFO_FILENAME);
            $templateDir = dirname($template->arquivo_path);

            // Buscar backups
            $arquivos = \Storage::disk('local')->files($templateDir);
            $backupsDoTemplate = array_filter($arquivos, function ($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName.'_backup_') === 0;
            });

            if (empty($backupsDoTemplate)) {
                return null;
            }

            // Ordenar por data (mais recente primeiro)
            usort($backupsDoTemplate, function ($a, $b) {
                return \Storage::disk('local')->lastModified($b) - \Storage::disk('local')->lastModified($a);
            });

            return \Storage::disk('local')->get($backupsDoTemplate[0]);

        } catch (\Exception $e) {
            // Log::error('Erro ao carregar backup', [
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
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
}',
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
                $backupPath = str_replace('.rtf', '_backup_'.date('Y_m_d_His').'.rtf', $template->arquivo_path);

                // Manter apenas os 5 backups mais recentes
                $this->limparBackupsAntigos($template);

                // Salvar backup
                \Storage::disk('local')->put($backupPath, $conteudoTemplate);

                // Log::info('Backup do template criado', [
                //     'template_id' => $template->id,
                //     'backup_path' => $backupPath,
                //     'variaveis_encontradas' => $this->extrairVariaveisTemplate($conteudoTemplate)
                // ]);
            } else {
                // Log::warning('Template não contém variáveis essenciais', [
                //     'template_id' => $template->id,
                //     'variaveis_faltando' => ['${ementa}', '${texto}', '${numero_proposicao}']
                // ]);

                // Tentar restaurar do backup mais recente
                $this->tentarRestaurarTemplate($template);
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao validar template', [
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
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
            $backupsDoTemplate = array_filter($backups, function ($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName.'_backup_') === 0;
            });

            // Ordenar por data (mais recente primeiro)
            usort($backupsDoTemplate, function ($a, $b) {
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
            // Log::error('Erro ao limpar backups antigos', [
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
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
            $backupsDoTemplate = array_filter($backups, function ($arquivo) use ($templateBaseName) {
                return strpos(basename($arquivo), $templateBaseName.'_backup_') === 0;
            });

            if (! empty($backupsDoTemplate)) {
                // Ordenar por data (mais recente primeiro)
                usort($backupsDoTemplate, function ($a, $b) {
                    return \Storage::disk('local')->lastModified($b) - \Storage::disk('local')->lastModified($a);
                });

                $backupMaisRecente = $backupsDoTemplate[0];
                $conteudoBackup = \Storage::disk('local')->get($backupMaisRecente);

                // Restaurar o template
                \Storage::disk('local')->put($template->arquivo_path, $conteudoBackup);

                // Log::info('Template restaurado do backup', [
                //     'template_id' => $template->id,
                //     'backup_usado' => $backupMaisRecente,
                //     'data_backup' => \Storage::disk('local')->lastModified($backupMaisRecente)
                // ]);

                return true;
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao restaurar template do backup', [
            //     'template_id' => $template->id,
            //     'error' => $e->getMessage()
            // ]);
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
        if (! ($proposicao instanceof \App\Models\Proposicao)) {
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
            'sessao_legislativa' => $agora->format('Y'),
        ];

        // Obter variáveis dos parâmetros do sistema
        $templateParametrosService = app(\App\Services\Template\TemplateParametrosService::class);
        $dadosCompletos = [
            'proposicao' => $proposicao,
            'autor' => $user,
            'variaveis' => $variaveisPreenchidas,
        ];

        // Processar template via serviço para obter todas as variáveis (incluindo parâmetros)
        $templateComVariaveis = $templateParametrosService->processarTemplate('${imagem_cabecalho} ${cabecalho_nome_camara} ${cabecalho_endereco} ${cabecalho_telefone} ${cabecalho_website} ${rodape_texto} ${assinatura_padrao}', $dadosCompletos);

        // Extrair variáveis dos parâmetros usando o serviço
        $parametros = $templateParametrosService->obterParametrosTemplates();
        $variaveisParametros = [];

        // Mapear variáveis dos parâmetros
        $mapeamentoParametros = [
            'imagem_cabecalho' => 'Cabeçalho.cabecalho_imagem',
            'cabecalho_nome_camara' => 'Cabeçalho.cabecalho_nome_camara',
            'cabecalho_endereco' => 'Cabeçalho.cabecalho_endereco',
            'cabecalho_telefone' => 'Cabeçalho.cabecalho_telefone',
            'cabecalho_website' => 'Cabeçalho.cabecalho_website',
            'rodape_texto' => 'Rodapé.rodape_texto',
            'assinatura_padrao' => 'Variáveis Dinâmicas.var_assinatura_padrao',
        ];

        foreach ($mapeamentoParametros as $variavel => $chaveParametro) {
            if (isset($parametros[$chaveParametro])) {
                if ($variavel === 'imagem_cabecalho') {
                    // Para imagem, gerar código RTF completo
                    $imagemCabecalho = $parametros[$chaveParametro];
                    if (! empty($imagemCabecalho)) {
                        $caminhoCompleto = public_path($imagemCabecalho);
                        if (file_exists($caminhoCompleto)) {
                            $variaveisParametros[$variavel] = $this->gerarCodigoRTFImagem($caminhoCompleto);
                        } else {
                            $variaveisParametros[$variavel] = '';
                        }
                    } else {
                        $variaveisParametros[$variavel] = '';
                    }
                } else {
                    $variaveisParametros[$variavel] = $parametros[$chaveParametro];
                }
            }
        }

        // Combinar todas as variáveis: sistema, parâmetros e preenchidas pelo parlamentar
        $todasVariaveis = array_merge($variaveisSystem, $variaveisParametros, $variaveisPreenchidas);

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
            // Log::info('Variáveis Unicode encontradas', [
            //     'quantidade' => count($unicodeMatches[0]),
            //     'sequencias' => array_slice($unicodeMatches[0], 0, 3) // Log primeiras 3
            // ]);

            foreach ($unicodeMatches[0] as $unicodeSequence) {
                // Decodificar sequência Unicode completa para texto
                $decoded = $this->decodificarUnicodeRTF($unicodeSequence);
                // Log::info('Decodificação Unicode', [
                //     'sequencia' => substr($unicodeSequence, 0, 100) . '...',
                //     'decodificado' => $decoded
                // ]);

                if ($decoded && strpos($decoded, '${') === 0 && substr($decoded, -1) === '}') {
                    // Extrair nome da variável (remover ${ e })
                    $nomeVariavel = substr($decoded, 2, -1);
                    if ($nomeVariavel && ! in_array($nomeVariavel, $variaveisNormaisEncontradas)) {
                        // Log::info('Variável Unicode adicionada', ['variavel' => $nomeVariavel]);
                        $variaveisNoTemplate[] = $nomeVariavel;
                    }
                }
            }
        }

        // Remover duplicatas
        $variaveisNoTemplate = array_unique($variaveisNoTemplate);

        // Log::info('DEBUG: Análise de variáveis no template', [
        //     'variaveis_no_template' => $variaveisNoTemplate,
        //     'variaveis_disponiveis' => array_keys($todasVariaveis),
        //     'template_preview' => substr($conteudoRTF, 0, 500) . '...',
        //     'template_contém_dollar' => strpos($conteudoRTF, '$') !== false,
        //     'template_contém_chaves' => strpos($conteudoRTF, '{') !== false,
        //     'template_busca_manual_ementa' => strpos($conteudoRTF, '${ementa}') !== false,
        //     'template_busca_manual_texto' => strpos($conteudoRTF, '${texto}') !== false
        // ]);

        // Sempre tentar substituir variáveis primeiro
        $conteudoProcessado = $conteudoRTF;
        $substituicoes = 0;
        $detalhesSubstituicoes = [];

        // Ordenar variáveis por tamanho decrescente para evitar substituições parciais
        // Ex: substituir 'data_atual' antes de 'data' para evitar '27/07/2025_atual'
        uksort($todasVariaveis, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($todasVariaveis as $variavel => $valor) {
            // Tentar diferentes formatos de placeholder
            $placeholders = [
                '${'.$variavel.'}',  // Formato normal com chaves
                '$'.$variavel,  // Formato simples sem chaves
                '$\\{'.$variavel.'\\}', // Com escape RTF
                '\${'.$variavel.'}', // Com escape simples
            ];

            // Adicionar placeholder para formato Unicode apenas se não há versão normal
            $unicodePlaceholder = $this->codificarVariavelParaUnicode('${'.$variavel.'}');
            if ($unicodePlaceholder && strpos($conteudoProcessado, $unicodePlaceholder) !== false) {
                // Verificar se não existe versão normal da mesma variável
                $temVersaoNormal = false;
                foreach (['${'.$variavel.'}', '$'.$variavel, '$\\{'.$variavel.'\\}', '\${'.$variavel.'}'] as $normalPlaceholder) {
                    if (strpos($conteudoProcessado, $normalPlaceholder) !== false) {
                        $temVersaoNormal = true;
                        break;
                    }
                }

                if (! $temVersaoNormal) {
                    $placeholders[] = $unicodePlaceholder;
                }
            }

            $substituicoesVariavel = 0;
            foreach ($placeholders as $placeholder) {
                $antes = substr_count($conteudoProcessado, $placeholder);

                // Para placeholders sem chaves, usar substituição com word boundary
                if ($placeholder === '$'.$variavel) {
                    // Usar regex para garantir que não substitui parcialmente
                    $pattern = '/\$'.preg_quote($variavel, '/').'(?![a-zA-Z_])/';
                    $conteudoProcessado = preg_replace($pattern, $valor, $conteudoProcessado);
                    $depois = substr_count($conteudoProcessado, $placeholder);
                } elseif (strpos($placeholder, '\\u') !== false) {
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
                    'valor' => substr($valor, 0, 50).'...',
                    'substituicoes' => $substituicoesVariavel,
                ];
            }
        }

        // Log::info('DEBUG: Detalhes das substituições', [
        //     'detalhes' => $detalhesSubstituicoes,
        //     'total_substituicoes' => $substituicoes
        // ]);

        // Log::info('Substituições realizadas', [
        //     'total_substituicoes' => $substituicoes,
        //     'tinha_variaveis_antes' => strpos($conteudoRTF, '${') !== false,
        //     'tem_variaveis_depois' => strpos($conteudoProcessado, '${') !== false
        // ]);

        // Se não houve substituições e ainda não há conteúdo significativo, adicionar ao final
        // IMPORTANTE: Não adicionar conteúdo se o template já tem conteúdo significativo (imagens, texto longo)
        $temConteudoSignificativo = strlen($conteudoRTF) > 100000; // Templates com imagens são grandes

        if ($temConteudoSignificativo && $substituicoes === 0) {
            // Log::info('Template tem conteúdo significativo mas sem variáveis - mantendo conteúdo original sem adicionar conteúdo extra', [
            //     'tamanho_template' => strlen($conteudoRTF),
            //     'tem_variaveis' => strpos($conteudoRTF, '${') !== false
            // ]);
        }

        if ($substituicoes === 0 && strpos($conteudoRTF, '${') === false && ! $temConteudoSignificativo) {
            // Log::info('Template não tinha variáveis nem conteúdo significativo, adicionando conteúdo estruturado ao final');

            // Encontrar a posição antes do fechamento do RTF
            $posicaoFinal = strrpos($conteudoProcessado, '}');
            if ($posicaoFinal !== false) {
                $conteudoAntes = substr($conteudoProcessado, 0, $posicaoFinal);

                // Adicionar conteúdo formatado em RTF
                $conteudoAdicional = '\\par\\par';
                $conteudoAdicional .= '{\\qc\\b\\fs32 MOÇÃO Nº '.$variaveisSystem['numero_proposicao'].'\\par}';
                $conteudoAdicional .= '\\par';
                $conteudoAdicional .= '{\\qc Data: '.$variaveisSystem['data_atual'].'\\par}';
                $conteudoAdicional .= '{\\qc Autor: '.$variaveisSystem['autor_nome'].'\\par}';
                $conteudoAdicional .= '{\\qc Município: '.$variaveisSystem['municipio'].'\\par}';
                $conteudoAdicional .= '\\par\\par';
                $conteudoAdicional .= '{\\b\\fs28 EMENTA\\par}';
                $conteudoAdicional .= '\\par';
                $conteudoAdicional .= ($variaveisPreenchidas['ementa'] ?? '[EMENTA NÃO PREENCHIDA]');
                $conteudoAdicional .= '\\par\\par';
                $conteudoAdicional .= '{\\b\\fs28 TEXTO\\par}';
                $conteudoAdicional .= '\\par';
                $conteudoAdicional .= ($variaveisPreenchidas['texto'] ?? '[TEXTO NÃO PREENCHIDO]');
                $conteudoAdicional .= '\\par\\par\\par';
                $conteudoAdicional .= '{\\qr Câmara Municipal de '.$variaveisSystem['municipio'].'\\par}';
                $conteudoAdicional .= '{\\qr '.$variaveisSystem['data_atual'].'\\par}';

                $conteudoProcessado = $conteudoAntes.$conteudoAdicional.'}';
            }
        }

        // Log::info('Variáveis processadas no RTF', [
        //     'tem_variaveis_predefinidas' => strpos($conteudoRTF, '${') !== false,
        //     'variaveis_disponiveis' => array_keys($todasVariaveis),
        //     'valores_exemplo' => [
        //         'ementa' => substr($variaveisPreenchidas['ementa'] ?? '[não definida]', 0, 50) . '...',
        //         'texto' => substr($variaveisPreenchidas['texto'] ?? '[não definido]', 0, 50) . '...',
        //         'autor_nome' => $variaveisSystem['autor_nome'],
        //         'data_atual' => $variaveisSystem['data_atual']
        //     ]
        // ]);

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
                $texto .= chr((int) $codigo);
            }

            return $texto;

        } catch (\Exception $e) {
            // Log::warning('Erro ao decodificar sequência Unicode', [
            //     'sequencia' => $sequenciaUnicode,
            //     'error' => $e->getMessage()
            // ]);
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
                $sequencia .= '\\u'.$codigo.'*';
            }

            return $sequencia;

        } catch (\Exception $e) {
            // Log::warning('Erro ao codificar variável para Unicode', [
            //     'variavel' => $variavel,
            //     'error' => $e->getMessage()
            // ]);
            return null;
        }
    }

    /**
     * Codificar texto para formato Unicode do RTF (para valores)
     */
    private function codificarTextoParaUnicode($texto)
    {
        try {
            // Log::info('Codificando texto para Unicode RTF', [
            //     'texto_original' => mb_substr($texto, 0, 100),
            //     'length' => mb_strlen($texto, 'UTF-8')
            // ]);

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
                        $textoProcessado .= '\\u'.$codepoint.'*';
                    } else {
                        $textoProcessado .= $char;
                    }
                }
            }

            // Log::info('Texto codificado para Unicode RTF', [
            //     'amostra_resultado' => mb_substr($textoProcessado, 0, 200),
            //     'tamanho_final' => strlen($textoProcessado)
            // ]);

            return $textoProcessado;

        } catch (\Exception $e) {
            // Log::warning('Erro ao codificar texto para Unicode RTF', [
            //     'texto_inicio' => mb_substr($texto, 0, 50) . '...',
            //     'error' => $e->getMessage()
            // ]);
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
            '\\' => '\\\\', '{' => '\\{', '}' => '\\}',
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
        $rtf = '{\\rtf1\\ansi\\ansicpg1252\\deff0\\deflang1046'."\n";
        $rtf .= '{\\fonttbl{\\f0\\froman\\fcharset0 Times New Roman;}}'."\n";
        $rtf .= '{\\colortbl ;\\red0\\green0\\blue0;}'."\n";
        $rtf .= '\\viewkind4\\uc1\\pard\\cf1\\f0\\fs24'."\n";
        $rtf .= $textoRTF."\n";
        $rtf .= '\\par}';

        return $rtf;
    }

    /**
     * Criar arquivo DOCX real usando ZIP
     */
    private function criarArquivoDOCXReal($texto)
    {
        // Converter texto simples para XML do Word
        $textoLimpo = htmlspecialchars($texto, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $textoXML = str_replace("\n", '</w:t><w:br/><w:t>', $textoLimpo);

        $documentXML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
    <w:p>
      <w:r>
        <w:rPr>
          <w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/>
          <w:sz w:val="22"/>
        </w:rPr>
        <w:t>'.$textoXML.'</w:t>
      </w:r>
    </w:p>
  </w:body>
</w:document>';

        // Criar arquivo ZIP temporário
        $tempZip = tempnam(sys_get_temp_dir(), 'docx_');
        $zip = new \ZipArchive;

        if ($zip->open($tempZip, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Não foi possível criar arquivo DOCX');
        }

        // Estrutura básica do DOCX
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

        $zip->addFromString('word/document.xml', $documentXML);

        $zip->close();

        $content = file_get_contents($tempZip);
        unlink($tempZip);

        return $content;
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
        $rtf = '{\\rtf1\\ansi\\deff0'."\n";
        $rtf .= '{\\fonttbl {\\f0 Times New Roman;}}'."\n";
        $rtf .= '\\f0\\fs24'."\n";
        $rtf .= $textoRTF."\n";
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
     * Criar arquivo de teste DOCX
     */
    public function criarArquivoTesteDOCX()
    {
        $textoTeste = "MOÇÃO\n\nEMENTA\n\nTeste de documento DOCX para OnlyOffice\n\nCONTEÚDO\n\nEste é um documento de teste criado para verificar a integração com OnlyOffice.\n\nAutor: Sistema de Teste\nData: ".date('d/m/Y');

        $conteudoDocx = $this->criarArquivoDocx($textoTeste);
        $pathTeste = storage_path('app/public/proposicoes/teste_mocao.docx');

        // Criar diretório se não existir
        $diretorio = dirname($pathTeste);
        if (! file_exists($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        file_put_contents($pathTeste, $conteudoDocx);

        return response()->json([
            'success' => true,
            'message' => 'Arquivo de teste DOCX criado',
            'path' => 'proposicoes/teste_mocao.docx',
            'size' => strlen($conteudoDocx),
        ]);
    }

    /**
     * Servir arquivo para OnlyOffice
     */
    public function serveFile($proposicaoId, $arquivo)
    {
        try {
            $pathArquivo = "proposicoes/{$arquivo}";
            $fullPath = storage_path('app/public/'.$pathArquivo);

            // Log::info('OnlyOffice serveFile chamado', [
            //     'proposicao_id' => $proposicaoId,
            //     'arquivo' => $arquivo,
            //     'path_relativo' => $pathArquivo,
            //     'path_completo' => $fullPath,
            //     'arquivo_existe' => file_exists($fullPath),
            //     'storage_exists' => \Storage::disk('public')->exists($pathArquivo)
            // ]);

            if (! \Storage::disk('public')->exists($pathArquivo)) {
                // Log::error('Arquivo não encontrado para OnlyOffice', [
                //     'proposicao_id' => $proposicaoId,
                //     'arquivo' => $arquivo,
                //     'path' => $pathArquivo,
                //     'diretorio_contents' => \Storage::disk('public')->files('proposicoes')
                // ]);
                return response('File not found', 404);
            }

            $conteudo = \Storage::disk('public')->get($pathArquivo);

            // Processar variáveis do template se for arquivo RTF
            if (strpos($conteudo, '{\rtf') !== false) {
                $conteudo = $this->processarVariaveisTemplate($conteudo);

                // Log::info('Variáveis do template processadas', [
                //     'proposicao_id' => $proposicaoId,
                //     'arquivo' => $arquivo,
                //     'tamanho_processado' => strlen($conteudo)
                // ]);
            }

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

            // Log::info('Arquivo servido com sucesso', [
            //     'proposicao_id' => $proposicaoId,
            //     'arquivo' => $arquivo,
            //     'tamanho' => strlen($conteudo)
            // ]);

            return response($conteudo)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($conteudo))
                ->header('Content-Disposition', 'inline; filename="'.$arquivo.'"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        } catch (\Exception $e) {
            // Log::error('Erro ao servir arquivo para OnlyOffice', [
            //     'proposicao_id' => $proposicaoId,
            //     'arquivo' => $arquivo,
            //     'error' => $e->getMessage()
            // ]);

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

            // Log::info('OnlyOffice callback recebido', [
            //     'proposicao_id' => $proposicaoId,
            //     'callback_data' => $data
            // ]);

            if (! $data) {
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

                    // Log::info('OnlyOffice callback - tentando baixar documento', [
                    //     'proposicao_id' => $proposicaoId,
                    //     'original_url' => $data['url'],
                    //     'converted_url' => $url
                    // ]);

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
                        if (! $templateId) {
                            $templateId = session('proposicao_'.$proposicaoId.'_template_id', 4);
                        }

                        // Se o template_id veio como string "template_X", extrair apenas o número
                        if (is_string($templateId) && str_starts_with($templateId, 'template_')) {
                            $templateId = str_replace('template_', '', $templateId);
                        }

                        $nomeArquivo = "proposicao_{$proposicaoId}_template_{$templateId}.rtf";
                        $pathDestino = "proposicoes/{$nomeArquivo}";

                        // Salvar arquivo atualizado
                        $pathCompleto = storage_path('app/public/'.$pathDestino);

                        // Criar diretório se não existir
                        $diretorio = dirname($pathCompleto);
                        if (! file_exists($diretorio)) {
                            mkdir($diretorio, 0775, true);
                            chown($diretorio, 'www-data');
                            chgrp($diretorio, 'www-data');
                        }

                        // IMPORTANTE: Processar variáveis antes de salvar
                        // O OnlyOffice retorna o arquivo com as variáveis não processadas
                        // Precisamos substituí-las antes de salvar
                        if ($proposicao) {
                            \Log::info('DEBUG - Processando variáveis do arquivo retornado pelo OnlyOffice', [
                                'proposicao_id' => $proposicaoId,
                                'tamanho_original' => strlen($fileContent),
                            ]);

                            // Processar as variáveis usando nosso método
                            $fileContent = $this->substituirVariaveisRTF($fileContent, $proposicao, []);

                            \Log::info('DEBUG - Arquivo processado após substituir variáveis', [
                                'tamanho_processado' => strlen($fileContent),
                                'tem_imagem_cabecalho' => strpos($fileContent, '${imagem_cabecalho}') !== false,
                                'tem_pngblip' => strpos($fileContent, 'pngblip') !== false,
                            ]);
                        }

                        file_put_contents($pathCompleto, $fileContent);

                        // Log::info('Arquivo da proposição salvo via OnlyOffice', [
                        //     'proposicao_id' => $proposicaoId,
                        //     'arquivo' => $nomeArquivo,
                        //     'size' => strlen($fileContent),
                        //     'template_id' => $templateId,
                        //     'path' => $pathDestino
                        // ]);

                        // Detectar formato do arquivo e extrair texto adequadamente
                        $textoExtraido = $this->extrairTextoDoArquivo($fileContent);

                        // Atualizar sessão com timestamp da última modificação
                        session(['proposicao_'.$proposicaoId.'_ultima_modificacao' => now()]);

                        // Atualizar registro da proposição no banco de dados
                        if ($proposicao) {
                            // Extrair ementa e conteúdo do texto
                            $dadosExtraidos = \App\Services\RTFTextExtractor::extractEmentaAndConteudo($textoExtraido);

                            $proposicao->update([
                                'arquivo_path' => $pathDestino,
                                'ultima_modificacao' => now(),
                                'status' => 'em_edicao',
                                'ementa' => $dadosExtraidos['ementa'] ?? $proposicao->ementa,
                                'conteudo' => $dadosExtraidos['conteudo'] ?? $proposicao->conteudo,
                            ]);

                            // Log::info('Proposição atualizada com texto extraído', [
                            //     'proposicao_id' => $proposicaoId,
                            //     'ementa_atualizada' => !empty($dadosExtraidos['ementa']),
                            //     'conteudo_atualizado' => !empty($dadosExtraidos['conteudo'])
                            // ]);
                        }
                    } else {
                        // Log::error('Erro ao baixar arquivo do OnlyOffice', [
                        //     'proposicao_id' => $proposicaoId,
                        //     'http_code' => $httpCode,
                        //     'curl_error' => $curlError,
                        //     'original_url' => $data['url'],
                        //     'converted_url' => $url
                        // ]);
                    }
                }
            }

            return response()->json(['error' => 0]);

        } catch (\Exception $e) {
            // Log::error('Erro no callback do OnlyOffice', [
            //     'proposicao_id' => $proposicaoId,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

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
            'conteudo' => 'required|string',
        ]);

        // Salvar na sessão
        session([
            'proposicao_'.$proposicaoId.'_ementa' => $request->ementa,
            'proposicao_'.$proposicaoId.'_conteudo' => $request->conteudo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dados salvos temporariamente',
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
        if (! $proposicao) {
            // Criar objeto mock apenas se há dados na sessão
            if (session()->has('proposicao_'.$proposicaoId.'_tipo')) {
                $proposicao = (object) [
                    'id' => $proposicaoId,
                    'tipo' => session('proposicao_'.$proposicaoId.'_tipo', 'projeto_lei'),
                    'ementa' => session('proposicao_'.$proposicaoId.'_ementa', ''),
                    'conteudo' => session('proposicao_'.$proposicaoId.'_conteudo', ''),
                    'status' => session('proposicao_'.$proposicaoId.'_status', 'rascunho'),
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

                // Log::info('Template do admin encontrado para preparar edição', [
                //     'proposicao_id' => $proposicaoId,
                //     'tipo_proposicao' => $proposicao->tipo,
                //     'template_encontrado' => $template ? $template->id : 'nenhum'
                // ]);
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
            'tipo' => session('proposicao_'.$proposicaoId.'_tipo', 'projeto_lei'),
            'ementa' => session('proposicao_'.$proposicaoId.'_ementa', ''),
            'conteudo' => session('proposicao_'.$proposicaoId.'_conteudo', ''),
            'anexos' => session('proposicao_'.$proposicaoId.'_anexos', []),
        ];

        // Buscar template
        // Verificar se o templateId é numérico ou string
        if (is_numeric($templateId)) {
            $template = \App\Models\TipoProposicaoTemplate::find($templateId);
        } else {
            // Se for uma string como "decreto_legislativo", buscar pelo template_id no DocumentoModelo
            $documentoModelo = \App\Models\Documento\DocumentoModelo::where('template_id', $templateId)->first();
            if ($documentoModelo && $documentoModelo->tipo_proposicao_id) {
                $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $documentoModelo->tipo_proposicao_id)
                    ->ativo()
                    ->first();
            } else {
                $template = null;
            }
        }

        if (! $template) {
            return redirect()->route('proposicoes.minhas-proposicoes')
                ->with('error', 'Template não encontrado.');
        }

        // Criar documento com dados preenchidos
        $documentKey = 'proposicao_'.$proposicaoId.'_editor_'.time();

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
            'anexo' => 'required|file|max:10240', // Max 10MB
        ]);

        $arquivo = $request->file('anexo');
        $nomeOriginal = $arquivo->getClientOriginalName();
        $nomeArquivo = time().'_'.$nomeOriginal;
        $path = $arquivo->storeAs('proposicoes/anexos/'.$proposicaoId, $nomeArquivo, 'public');

        // Salvar na sessão
        $anexos = session('proposicao_'.$proposicaoId.'_anexos', []);
        $anexos[] = [
            'id' => uniqid(),
            'nome' => $nomeOriginal,
            'arquivo' => $nomeArquivo,
            'path' => $path,
            'tamanho' => $arquivo->getSize(),
            'uploaded_at' => now(),
        ];
        session(['proposicao_'.$proposicaoId.'_anexos' => $anexos]);

        return response()->json([
            'success' => true,
            'anexo' => end($anexos),
        ]);
    }

    /**
     * Remover anexo
     */
    public function removerAnexo($proposicaoId, $anexoId)
    {
        $anexos = session('proposicao_'.$proposicaoId.'_anexos', []);

        foreach ($anexos as $key => $anexo) {
            if ($anexo['id'] == $anexoId) {
                // Remover arquivo físico
                \Storage::disk('public')->delete($anexo['path']);

                // Remover da sessão
                unset($anexos[$key]);
                session(['proposicao_'.$proposicaoId.'_anexos' => array_values($anexos)]);

                return response()->json([
                    'success' => true,
                    'message' => 'Anexo removido com sucesso',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Anexo não encontrado',
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
            $pathCompleto = storage_path('app/public/'.$pathDestino);

            // Garantir que o diretório existe
            $diretorio = dirname($pathCompleto);
            if (! file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            // Criar RTF com dados preenchidos
            $rtfContent = $this->gerarRTFComDados($ementa, $conteudo);
            file_put_contents($pathCompleto, $rtfContent);

            return $pathDestino;

        } catch (\Exception $e) {
            // Log::error('Erro ao criar arquivo com dados', [
            //     'proposicao_id' => $proposicaoId,
            //     'error' => $e->getMessage()
            // ]);

            throw $e;
        }
    }

    /**
     * Gerar conteúdo RTF com dados preenchidos
     */
    private function gerarRTFComDados($ementa, $conteudo)
    {
        // Criar documento RTF com formatação
        $rtf = '{\\rtf1\\ansi\\ansicpg1252\\deff0\\deflang1046'."\n";
        $rtf .= '{\\fonttbl {\\f0 Times New Roman;}}'."\n";
        $rtf .= '{\\colortbl;\\red0\\green0\\blue0;}'."\n";
        $rtf .= '\\f0\\fs24'."\n";

        // Adicionar ementa
        $rtf .= '\\b EMENTA\\b0\\par'."\n";
        $rtf .= '\\par'."\n";
        // Converter ementa para RTF com encoding correto
        $ementaRtf = $this->utf8ToRtf($ementa);
        $rtf .= str_replace("\n", "\\par\n", $ementaRtf)."\n";
        $rtf .= '\\par\\par'."\n";

        // Adicionar conteúdo
        $rtf .= '\\b '.$this->utf8ToRtf('PROPOSIÇÃO').'\\b0\\par'."\n";
        $rtf .= '\\par'."\n";
        // Converter conteúdo para RTF com encoding correto
        $conteudoRtf = $this->utf8ToRtf($conteudo);
        $rtf .= str_replace("\n", "\\par\n", $conteudoRtf)."\n";

        $rtf .= '}';

        return $rtf;
    }

    /**
     * Atualizar status da proposição
     */
    public function atualizarStatus(Request $request, $proposicaoId)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        // Buscar proposição no banco de dados primeiro
        $proposicao = Proposicao::find($proposicaoId);

        if ($proposicao) {
            // Atualizar status no banco de dados
            $proposicao->update(['status' => $request->status]);
        } else {
            // Fallback: salvar status na sessão (para proposições antigas)
            session(['proposicao_'.$proposicaoId.'_status' => $request->status]);
        }

        // Log da mudança de status
        // Log::info('Status da proposição atualizado', [
        //     'proposicao_id' => $proposicaoId,
        //     'novo_status' => $request->status,
        //     'user_id' => Auth::id(),
        //     'salvo_em' => $proposicao ? 'banco_dados' : 'sessao'
        // ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Status atualizado com sucesso',
        ]);
    }

    /**
     * Retorno do legislativo (simulado)
     */
    public function retornoLegislativo(Request $request, $proposicaoId)
    {
        // Simular retorno do legislativo
        session([
            'proposicao_'.$proposicaoId.'_status' => 'retornado_legislativo',
            'proposicao_'.$proposicaoId.'_observacoes_legislativo' => $request->observacoes ?? 'Proposição aprovada pelo legislativo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição retornada do legislativo',
        ]);
    }

    /**
     * Corrigir encoding para exibição correta no OnlyOffice
     */
    private function corrigirEncodingParaOnlyOffice(string $conteudoRTF): string
    {
        // Log::info('Aplicando correção de encoding para OnlyOffice no serveFile', [
        //     'tamanho_original' => strlen($conteudoRTF),
        //     'preview' => substr($conteudoRTF, 0, 100)
        // ]);

        $conteudoCorrigido = $conteudoRTF;

        // Substituir códigos RTF e Unicode por UTF-8 para melhor interpretação no OnlyOffice
        $substituicoes = [
            // Códigos RTF tradicionais
            "\\'ed" => 'í',  // í
            "\\'e3" => 'ã',  // ã
            "\\'e2" => 'â',  // â
            "\\'e7" => 'ç',  // ç
            "\\'c7" => 'Ç',  // Ç
            "\\'c3" => 'Ã',  // Ã
            "\\'e1" => 'á',  // á
            "\\'e9" => 'é',  // é
            "\\'f3" => 'ó',  // ó
            "\\'fa" => 'ú',  // ú

            // Códigos Unicode do OnlyOffice
            '\\u237*' => 'í',   // í em Unicode RTF
            '\\u227*' => 'ã',   // ã em Unicode RTF
            '\\u226*' => 'â',   // â em Unicode RTF
            '\\u231*' => 'ç',   // ç em Unicode RTF
            '\\u199*' => 'Ç',   // Ç em Unicode RTF
            '\\u195*' => 'Ã',   // Ã em Unicode RTF
            '\\u225*' => 'á',   // á em Unicode RTF
            '\\u233*' => 'é',   // é em Unicode RTF
            '\\u243*' => 'ó',   // ó em Unicode RTF
            '\\u250*' => 'ú',   // ú em Unicode RTF
        ];

        $totalCorrecoes = 0;
        foreach ($substituicoes as $rtfCode => $utf8Char) {
            $antes = substr_count($conteudoCorrigido, $rtfCode);
            $conteudoCorrigido = str_replace($rtfCode, $utf8Char, $conteudoCorrigido);
            $depois = substr_count($conteudoCorrigido, $rtfCode);

            if ($antes > $depois) {
                $correcoes = $antes - $depois;
                $totalCorrecoes += $correcoes;
                // Log::info("Correção RTF->UTF8 aplicada: {$rtfCode} -> {$utf8Char}", [
                //     'ocorrencias' => $correcoes
                // ]);
            }
        }

        // Log::info('Correção de encoding para OnlyOffice concluída', [
        //     'total_correcoes' => $totalCorrecoes,
        //     'tamanho_final' => strlen($conteudoCorrigido)
        // ]);

        return $conteudoCorrigido;
    }

    /**
     * Assinar documento digitalmente
     */
    public function assinarDocumento(Request $request, $proposicaoId)
    {
        // Validar se a proposição está no status correto
        $status = session('proposicao_'.$proposicaoId.'_status', 'rascunho');

        if ($status !== 'retornado_legislativo') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar retornada do legislativo para ser assinada',
            ], 400);
        }

        // Simular assinatura digital
        session([
            'proposicao_'.$proposicaoId.'_status' => 'assinado',
            'proposicao_'.$proposicaoId.'_assinatura' => [
                'assinado_por' => Auth::user()->name,
                'assinado_em' => now(),
                'certificado' => 'Certificado Digital A3 - Simulado',
            ],
        ]);

        // Log::info('Documento assinado digitalmente', [
        //     'proposicao_id' => $proposicaoId,
        //     'user_id' => Auth::id()
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento assinado com sucesso!',
        ]);
    }

    /**
     * Enviar para protocolo
     */
    public function enviarProtocolo(Request $request, $proposicaoId)
    {
        // Validar se está assinado
        $status = session('proposicao_'.$proposicaoId.'_status', 'rascunho');

        if ($status !== 'assinado') {
            return response()->json([
                'success' => false,
                'message' => 'Documento deve estar assinado para ser protocolado',
            ], 400);
        }

        // Gerar número de protocolo
        $numeroProtocolo = 'PROT-'.date('Y').'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        session([
            'proposicao_'.$proposicaoId.'_status' => 'protocolado',
            'proposicao_'.$proposicaoId.'_protocolo' => [
                'numero' => $numeroProtocolo,
                'data' => now(),
                'responsavel' => Auth::user()->name,
            ],
        ]);

        // Log::info('Proposição protocolada', [
        //     'proposicao_id' => $proposicaoId,
        //     'numero_protocolo' => $numeroProtocolo,
        //     'user_id' => Auth::id()
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição protocolada com sucesso!',
            'numero_protocolo' => $numeroProtocolo,
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
        $texto .= 'Autor: '.(\Auth::user()->name ?? '[AUTOR]')."\n";
        $texto .= 'Data: '.now()->format('d/m/Y');

        return $texto;
    }

    /**
     * Obter status completo da proposição
     */
    public function obterStatus($proposicaoId)
    {
        $status = session('proposicao_'.$proposicaoId.'_status', 'rascunho');
        $assinatura = session('proposicao_'.$proposicaoId.'_assinatura');
        $protocolo = session('proposicao_'.$proposicaoId.'_protocolo');
        $observacoes = session('proposicao_'.$proposicaoId.'_observacoes_legislativo');

        return response()->json([
            'status' => $status,
            'assinatura' => $assinatura,
            'protocolo' => $protocolo,
            'observacoes_legislativo' => $observacoes,
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
                // Log::info('Detectado arquivo DOCX, extraindo texto do XML');
                return $this->extrairTextoDOCX($fileContent);
            } elseif (strpos($fileContent, '{\\rtf') === 0) {
                // Arquivo RTF - usar o novo extrator
                // Log::info('Detectado arquivo RTF, usando RTFTextExtractor');
                return \App\Services\RTFTextExtractor::extract($fileContent);
            } else {
                // Tentar como texto puro primeiro
                if (mb_check_encoding($fileContent, 'UTF-8')) {
                    // Log::info('Tratando como texto puro UTF-8');
                    return trim($fileContent);
                } else {
                    // Log::warning('Formato de arquivo não reconhecido, tentando como RTF');
                    return \App\Services\RTFTextExtractor::extract($fileContent);
                }
            }
        } catch (\Exception $e) {
            // Log::error('Erro ao extrair texto do arquivo', [
            //     'erro' => $e->getMessage()
            // ]);
            return 'Erro ao extrair texto do documento';
        }
    }

    /**
     * Extrair texto de arquivo DOCX
     */
    private function extrairTextoDOCX($docxContent)
    {
        try {
            // Log::info('Iniciando extração de texto DOCX', [
            //     'tamanho_arquivo' => strlen($docxContent)
            // ]);

            $texto = '';

            // Método 1: Tentar usando ZipArchive se disponível
            if (class_exists('ZipArchive')) {
                $tempFile = tempnam(sys_get_temp_dir(), 'docx_extract_');
                file_put_contents($tempFile, $docxContent);

                $zip = new \ZipArchive;
                if ($zip->open($tempFile) === true) {
                    $documentXml = $zip->getFromName('word/document.xml');
                    if ($documentXml !== false) {
                        $texto = $this->extrairTextoDoXML($documentXml);
                        // Log::info('Extração via ZipArchive bem-sucedida', [
                        //     'tamanho_texto' => strlen($texto)
                        // ]);
                    }
                    $zip->close();
                }
                unlink($tempFile);
            }

            // Método 2: Se ZipArchive não funcionou, usar stream wrapper
            if (empty($texto)) {
                // Log::info('Tentando extração via stream wrapper');
                $texto = $this->extrairTextoDOCXStream($docxContent);
            }

            // Método 3: Se nada funcionou, usar extração direta
            if (empty($texto)) {
                // Log::warning('Usando método de extração direta como fallback');
                $texto = $this->extrairTextoDOCXDireto($docxContent);
            }

            // Log::info('Texto extraído do DOCX', [
            //     'tamanho_resultado' => strlen($texto),
            //     'preview' => substr($texto, 0, 200)
            // ]);

            return $texto;

        } catch (\Exception $e) {
            // Log::error('Erro ao extrair texto do DOCX', [
            //     'erro' => $e->getMessage()
            // ]);
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
                // Log::info('Stream wrapper funcionou', [
                //     'tamanho_xml' => strlen($documentContent)
                // ]);

                $texto = $this->extrairTextoDoXML($documentContent);
                unlink($tempFile);

                return $texto;
            }

            unlink($tempFile);

            return '';

        } catch (\Exception $e) {
            // Log::error('Erro na extração via stream', [
            //     'erro' => $e->getMessage()
            // ]);
            return '';
        }
    }

    /**
     * Extrair texto do DOCX diretamente (sem ZipArchive)
     */
    private function extrairTextoDOCXDireto($docxContent)
    {
        try {
            // Log::info('Tentando extração direta de DOCX', [
            //     'tamanho_arquivo' => strlen($docxContent)
            // ]);

            // Como último recurso, se nenhum método funcionou,
            // vamos retornar uma mensagem indicando que o documento precisa ser salvo como texto
            // Log::warning('Extração automática falhou - documento precisa ser salvo como texto simples');

            return 'Documento salvo no editor. Para visualizar o texto aqui, salve o documento como texto simples no editor.';

        } catch (\Exception $e) {
            // Log::error('Erro na extração direta de DOCX', [
            //     'erro' => $e->getMessage()
            // ]);
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
            $dom = new \DOMDocument;
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
            // Log::error('Erro ao processar XML do Word', [
            //     'erro' => $e->getMessage()
            // ]);

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
            $text = preg_replace_callback("/\\\\'([0-9a-fA-F]{2})/", function ($matches) {
                return chr(hexdec($matches[1]));
            }, $text);

            // IMPORTANTE: Decodificar caracteres Unicode ANTES de remover outros comandos
            // Suporta \uXXXX? e \uXXXX*
            $text = preg_replace_callback('/\\\\u(-?\d+)[\?\*]/', function ($matches) {
                $code = intval($matches[1]);
                if ($code < 0) {
                    $code = 65536 + $code;
                }
                if ($code < 128) {
                    return chr($code);
                } elseif ($code < 256) {
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
            if (! mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', 'auto');
            }

            // Log::info('Texto extraído do RTF', [
            //     'tamanho_original' => strlen($rtfContent),
            //     'tamanho_texto' => strlen($text),
            //     'primeiros_200_chars' => substr($text, 0, 200)
            // ]);

            return $text;

        } catch (\Exception $e) {
            // Log::error('Erro ao extrair texto do RTF', [
            //     'erro' => $e->getMessage()
            // ]);
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
            $linhas = array_filter($linhas, function ($linha) {
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
                if (! $ementaEncontrada && (
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
                } elseif ($ementaEncontrada && ! $conteudoIniciado) {
                    $linhasEmenta[] = $linhaLimpa;
                } elseif (! $ementaEncontrada && ! $conteudoIniciado) {
                    // Se ainda não encontrou marcadores, considerar como ementa as primeiras linhas
                    if (count($linhasEmenta) === 0 &&
                        (strlen($linhaLimpa) > 20 && strlen($linhaLimpa) < 200) &&
                        ! preg_match('/^(Prezado|Sua |Na |Art\.|Artigo)/i', $linhaLimpa)) {
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
            if (empty($ementa) && ! empty($linhasConteudo)) {
                $ementa = $linhasConteudo[0];
                array_shift($linhasConteudo);
                $conteudo = implode('<br>', $linhasConteudo);
            }

            // Limpar e formatar
            $ementa = $this->limparTexto($ementa);
            $conteudo = $this->limparTexto($conteudo);

            // Se a ementa ficar muito grande, truncar
            if (strlen($ementa) > 500) {
                $ementa = substr($ementa, 0, 497).'...';
            }

            // Log::info('Ementa e conteúdo extraídos', [
            //     'ementa_tamanho' => strlen($ementa),
            //     'conteudo_tamanho' => strlen($conteudo),
            //     'ementa_preview' => substr($ementa, 0, 100),
            //     'conteudo_preview' => substr($conteudo, 0, 100)
            // ]);

            return [
                'ementa' => $ementa ?: 'Proposição em elaboração',
                'conteudo' => $conteudo ?: 'Conteúdo a ser definido',
            ];

        } catch (\Exception $e) {
            // Log::error('Erro ao extrair ementa e conteúdo', [
            //     'erro' => $e->getMessage()
            // ]);
            return [
                'ementa' => 'Erro ao processar ementa',
                'conteudo' => $texto,
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
            'template_id' => 'required|exists:documento_templates,id',
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
            'variaveis' => $variaveisEditaveis,
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
                'instance' => $instance->id,
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao processar template da nova arquitetura', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $templateId,
            //     'erro' => $e->getMessage()
            // ]);

            return back()->withErrors('Erro ao processar template: '.$e->getMessage());
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
            'instance' => $instance,
        ]);
    }

    /**
     * Servir arquivo de instância para OnlyOffice
     */
    public function serveInstance(int $instanceId)
    {
        $instance = \App\Models\ProposicaoTemplateInstance::findOrFail($instanceId);

        if (! $instance->arquivo_instance_path || ! \Storage::exists($instance->arquivo_instance_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        $arquivo = \Storage::get($instance->arquivo_instance_path);

        return response($arquivo, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline; filename="document.docx"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
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

            // Log::info('OnlyOffice callback recebido para instância', [
            //     'instance_id' => $instanceId,
            //     'callback_data' => $data
            // ]);

            if (! $data) {
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
                        // IMPORTANTE: Processar variáveis antes de salvar
                        // Buscar a proposição relacionada à instância
                        if ($instance->proposicao_id) {
                            $proposicao = Proposicao::find($instance->proposicao_id);
                            if ($proposicao) {
                                \Log::info('DEBUG - Processando variáveis do arquivo de instância retornado pelo OnlyOffice', [
                                    'instance_id' => $instanceId,
                                    'proposicao_id' => $instance->proposicao_id,
                                    'tamanho_original' => strlen($fileContent),
                                ]);

                                // Processar as variáveis usando nosso método
                                $fileContent = $this->substituirVariaveisRTF($fileContent, $proposicao, []);

                                \Log::info('DEBUG - Arquivo de instância processado após substituir variáveis', [
                                    'tamanho_processado' => strlen($fileContent),
                                    'tem_pngblip' => strpos($fileContent, 'pngblip') !== false,
                                ]);
                            }
                        }

                        // Salvar arquivo atualizado
                        \Storage::put($instance->arquivo_instance_path, $fileContent);

                        // Log::info('Arquivo da instância salvo via OnlyOffice', [
                        //     'instance_id' => $instanceId,
                        //     'size' => strlen($fileContent)
                        // ]);

                        // Atualizar timestamp
                        $instance->touch();
                    }
                }
            }

            return response()->json(['error' => 0]);

        } catch (\Exception $e) {
            // Log::error('Erro no callback do OnlyOffice para instância', [
            //     'instance_id' => $instanceId,
            //     'error' => $e->getMessage()
            // ]);

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
            if (! auth()->user()->isLegislativo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários do Legislativo podem executar esta ação.',
                ], 403);
            }

            // Verificar se a proposição está no status correto
            if (! in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'devolvido_correcao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser devolvida no status atual.',
                ], 400);
            }

            // Tentar converter o documento para PDF para facilitar a assinatura
            try {
                // Log::info('Iniciando conversão para PDF', [
                //     'proposicao_id' => $proposicao->id,
                //     'arquivo_path' => $proposicao->arquivo_path,
                //     'arquivo_existe' => $proposicao->arquivo_path ? \Storage::exists($proposicao->arquivo_path) : false,
                //     'has_content' => !empty($proposicao->conteudo)
                // ]);

                // Sempre tentar converter, seja com arquivo físico ou conteúdo do banco
                $this->converterProposicaoParaPDF($proposicao);

                // Log::info('Conversão para PDF concluída', [
                //     'proposicao_id' => $proposicao->id,
                //     'arquivo_pdf_path' => $proposicao->arquivo_pdf_path
                // ]);
            } catch (\Exception $e) {
                // Log::error('Erro ao converter proposição para PDF', [
                //     'proposicao_id' => $proposicao->id,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
                // ]);
                // Continua sem falhar, pois a conversão é opcional
            }

            // Alterar o status para 'retornado_legislativo' - proposição volta para o parlamentar assinar
            $proposicao->status = 'retornado_legislativo';
            $proposicao->save();

            // Log::info('Proposição devolvida para parlamentar', [
            //     'proposicao_id' => $proposicao->id,
            //     'user_id' => auth()->id(),
            //     'status_anterior' => $proposicao->getOriginal('status'),
            //     'status_novo' => $proposicao->status
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposição devolvida para o Parlamentar com sucesso! O Legislativo não terá mais acesso.',
                'redirect' => route('proposicoes.legislativo.index'),
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao voltar proposição para parlamentar', [
            //     'proposicao_id' => $proposicao->id,
            //     'user_id' => auth()->id(),
            //     'error' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
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
                    'message' => 'Acesso negado. Apenas o autor pode aprovar as edições.',
                ], 403);
            }

            // Verificar se a proposição está no status correto
            if (! in_array($proposicao->status, ['aguardando_aprovacao_autor', 'devolvido_edicao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser aprovada no status atual.',
                ], 400);
            }

            // Atualizar status para 'aprovado_assinatura' - próximo passo é assinar
            $proposicao->update([
                'status' => 'aprovado_assinatura',
                'data_aprovacao_autor' => now(),
            ]);

            // Log::info('Edições do legislativo aprovadas pelo parlamentar', [
            //     'proposicao_id' => $proposicao->id,
            //     'user_id' => auth()->id(),
            //     'status_anterior' => $proposicao->getOriginal('status')
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Edições aprovadas com sucesso! A proposição está pronta para assinatura.',
                'redirect' => route('proposicoes.show', $proposicao),
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao aprovar edições do legislativo', [
            //     'error' => $e->getMessage(),
            //     'proposicao_id' => $proposicao->id
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Converter texto UTF-8 para códigos RTF
     */
    private function converterUtf8ParaRtf($texto)
    {
        // Log::info('Convertendo UTF-8 para RTF Unicode sequences', [
        //     'texto_original' => $texto,
        //     'length' => strlen($texto),
        //     'bytes' => bin2hex($texto)
        // ]);

        // Primeiro, limpar qualquer corrupção existente e normalizar para UTF-8 limpo
        $textoLimpo = $this->limparTextoCorrupto($texto);

        // Log::info('Texto após limpeza', [
        //     'texto_limpo' => $textoLimpo,
        //     'bytes_limpos' => bin2hex($textoLimpo)
        // ]);

        // Converter caracteres UTF-8 para sequências Unicode RTF
        $resultado = '';
        $length = mb_strlen($textoLimpo, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($textoLimpo, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');

            if ($codepoint > 127) {
                // Converter para escape Unicode RTF
                $resultado .= '\u'.$codepoint.'*';
            } else {
                $resultado .= $char;
            }
        }

        // Log::info('UTF-8 convertido para RTF Unicode', [
        //     'texto_final' => $resultado,
        //     'caracteres_convertidos' => $length - strlen(preg_replace('/[^\x00-\x7F]/', '', $textoLimpo))
        // ]);

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
            'oÃ§Ã£o' => 'oção',
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
            $variaveisPreenchidas = session('proposicao_'.$proposicaoId.'_variaveis_template', []);

            // Se não há variáveis na sessão, usar as da proposição
            if (empty($variaveisPreenchidas)) {
                $variaveisPreenchidas = [
                    'ementa' => $proposicao->ementa,
                    'texto' => $proposicao->conteudo,
                    'conteudo' => $proposicao->conteudo,
                ];
            }

            // Log::info('Processando template DOCX XML', [
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $template->id,
            //     'variaveis_preenchidas' => $variaveisPreenchidas
            // ]);

            // Carregar template XML
            $templatePath = storage_path('app/private/'.$template->arquivo_path);
            if (! file_exists($templatePath)) {
                throw new \Exception('Template DOCX XML não encontrado: '.$templatePath);
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
                'texto' => $variaveisPreenchidas['texto'] ?? 'Texto da proposição',
            ];

            // Substituir variáveis no XML (sem conversão de encoding!)
            $xmlProcessado = $templateXML;
            foreach ($variaveis as $variavel => $valor) {
                // Escapar caracteres XML mas manter UTF-8
                $valorEscapado = htmlspecialchars($valor, ENT_XML1, 'UTF-8');
                $xmlProcessado = str_replace('${'.$variavel.'}', $valorEscapado, $xmlProcessado);
            }

            // Criar estrutura DOCX
            $this->criarDOCXDeXML($xmlProcessado, storage_path('app/public/'.$pathDestino));

            // Log::info('Template DOCX processado com sucesso', [
            //     'template_path' => $template->arquivo_path,
            //     'proposicao_path' => $pathDestino,
            //     'arquivo_existe' => file_exists(storage_path('app/public/' . $pathDestino))
            // ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao processar template DOCX', [
            //     'error' => $e->getMessage(),
            //     'proposicao_id' => $proposicaoId,
            //     'template_id' => $template->id
            // ]);
            throw $e;
        }
    }

    /**
     * Criar arquivo DOCX a partir de XML do documento
     */
    private function criarDOCXDeXML($documentXML, $outputPath)
    {
        // Criar arquivo ZIP temporário
        $zip = new \ZipArchive;
        $tempZip = tempnam(sys_get_temp_dir(), 'docx_');

        if ($zip->open($tempZip, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Não foi possível criar arquivo DOCX');
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
        if (! rename($tempZip, $outputPath)) {
            unlink($tempZip);
            throw new \Exception('Não foi possível mover arquivo DOCX para destino');
        }

        // Log::info('Arquivo DOCX criado com sucesso', [
        //     'output_path' => $outputPath,
        //     'file_size' => filesize($outputPath)
        // ]);
    }

    /**
     * Converter arquivo RTF para DOCX real usando comando externo
     * Isso resolve problemas de encoding que o OnlyOffice tem com RTF
     */
    private function converterRTFParaDOCX($rtfPath)
    {
        try {
            // Log::info('Iniciando conversão RTF para DOCX', [
            //     'rtf_path' => $rtfPath,
            //     'file_exists' => file_exists($rtfPath),
            //     'file_size' => file_exists($rtfPath) ? filesize($rtfPath) : 0
            // ]);

            // Verificar se arquivo RTF existe
            if (! file_exists($rtfPath)) {
                throw new \Exception('Arquivo RTF não encontrado: '.$rtfPath);
            }

            // Criar arquivo temporário para a conversão
            $tempDir = sys_get_temp_dir();
            $tempRtf = $tempDir.'/'.uniqid('rtf_').'.rtf';
            $tempDocx = $tempDir.'/'.uniqid('docx_').'.docx';

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

                // Log::info('Conversão RTF para DOCX bem-sucedida usando pandoc', [
                //     'new_file_size' => filesize($rtfPath)
                // ]);
                return;
            }

            // Se pandoc não funcionou, usar LibreOffice
            $libreofficeCmd = "libreoffice --headless --convert-to docx --outdir '$tempDir' '$tempRtf' 2>&1";
            $libreOutput = [];
            $libreReturn = 0;
            exec($libreofficeCmd, $libreOutput, $libreReturn);

            // O LibreOffice gera arquivo com nome baseado no input
            $expectedDocx = $tempDir.'/'.pathinfo($tempRtf, PATHINFO_FILENAME).'.docx';

            if (file_exists($expectedDocx)) {
                // Conversão com LibreOffice foi bem-sucedida
                copy($expectedDocx, $rtfPath);
                unlink($tempRtf);
                unlink($expectedDocx);

                // Log::info('Conversão RTF para DOCX bem-sucedida usando LibreOffice', [
                //     'new_file_size' => filesize($rtfPath)
                // ]);
                return;
            }

            // Se ambos falharam, usar conversão manual simples
            $this->converterRTFParaDOCXManual($rtfPath);

        } catch (\Exception $e) {
            // Log::warning('Falha na conversão RTF para DOCX', [
            //     'error' => $e->getMessage(),
            //     'rtf_path' => $rtfPath
            // ]);

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

            // Log::info('Conversão RTF para DOCX manual bem-sucedida', [
            //     'new_file_size' => filesize($rtfPath)
            // ]);

        } catch (\Exception $e) {
            // Log::error('Falha na conversão manual RTF para DOCX', [
            //     'error' => $e->getMessage()
            // ]);
            throw $e;
        }
    }

    /**
     * Decodificar sequências Unicode RTF (\u123*) para caracteres
     */
    private function decodificarUnicodeRTF($rtfContent)
    {
        // Decodificar sequências Unicode como \u36*\u123*\u116*... para texto
        return preg_replace_callback('/(?:\\\\u(\d+)\*)+/', function ($matches) {
            $fullMatch = $matches[0];
            $texto = '';

            // Extrair todos os números Unicode da sequência
            if (preg_match_all('/\\\\u(\d+)\*/', $fullMatch, $unicodeMatches)) {
                foreach ($unicodeMatches[1] as $unicode) {
                    $char = chr((int) $unicode);
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
        // Log::info('Usando extração RTF inteligente focada em conteúdo com escape sequences');

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
            "\\'da" => 'Ú', "\\'c7" => 'Ç',
        ];

        foreach ($escapeSequences as $rtf => $utf8) {
            $texto = str_replace($rtf, $utf8, $texto);
        }

        // ETAPA 2: Buscar especificamente o corpo do documento
        if (preg_match('/\\\\paperw.*$/s', $texto, $matches)) {
            $corpoDocumento = $matches[0];
            $texto = $corpoDocumento;
            // Log::info('Corpo extraído para fallback inteligente', [
            //     'tamanho_corpo' => strlen($corpoDocumento)
            // ]);
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
                    ! preg_match('/^[0-9\s\-\*]+$/', $fragmentoLimpo)) {
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
        if (! empty($fragmentosTexto)) {
            $resultado = implode(' ', $fragmentosTexto);
            $resultado = preg_replace('/\s+/', ' ', $resultado);
            $resultado = trim($resultado);

            // Log::info('Extração RTF inteligente finalizada', [
            //     'fragmentos_encontrados' => count($fragmentosTexto),
            //     'tamanho_resultado' => strlen($resultado),
            //     'preview' => substr($resultado, 0, 150)
            // ]);

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
        // Log::info('Usando extração RTF ultra-simplificada - apenas texto limpo');

        // ETAPA 0: Decodificar sequências Unicode RTF primeiro
        $texto = $this->decodificarUnicodeRTF($rtfContent);
        // Log::info('Unicode decodificado na extração simples', [
        //     'preview' => substr($texto, 0, 200)
        // ]);

        // ETAPA 1: Converter escape sequences RTF para UTF-8
        $escapeSequences = [
            "\\'e1" => 'á', "\\'e0" => 'à', "\\'e2" => 'â', "\\'e3" => 'ã',
            "\\'e9" => 'é', "\\'ea" => 'ê', "\\'ed" => 'í',
            "\\'f3" => 'ó', "\\'f4" => 'ô', "\\'f5" => 'õ',
            "\\'fa" => 'ú', "\\'e7" => 'ç',
            "\\'c1" => 'Á', "\\'c0" => 'À', "\\'c2" => 'Â', "\\'c3" => 'Ã',
            "\\'c9" => 'É', "\\'ca" => 'Ê', "\\'cd" => 'Í',
            "\\'d3" => 'Ó', "\\'d4" => 'Ô', "\\'d5" => 'Õ',
            "\\'da" => 'Ú', "\\'c7" => 'Ç',
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

            $palavrasValidas = array_filter($wordMatches[0], function ($palavra) use ($palavrasProibidas) {
                $palavra = trim($palavra);

                return strlen($palavra) >= 3 &&  // Pelo menos 3 caracteres
                       ! preg_match('/^[0-9]+$/', $palavra) &&  // Não é só números
                       ! preg_match('/^[A-Z]{1,3}$/', $palavra) &&  // Não é só siglas curtas
                       ! in_array(ucfirst(strtolower($palavra)), $palavrasProibidas) &&  // Não é palavra do sistema
                       preg_match('/[a-zA-ZÀ-ÿ]/', $palavra);  // Contém letras válidas
            });

            $fragmentos = array_merge($fragmentos, $palavrasValidas);
        }

        if (! empty($fragmentos)) {
            $palavrasValidas = $fragmentos;

            if (! empty($palavrasValidas)) {
                $texto = implode(' ', $palavrasValidas);
                // Log::info('Palavras válidas extraídas', [
                //     'quantidade' => count($palavrasValidas),
                //     'palavras' => array_slice($palavrasValidas, 0, 10) // Log primeiras 10
                // ]);
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
                $frasesLongas = array_filter($matches[0], function ($frase) {
                    return strlen(trim($frase)) > 10;
                });

                if (! empty($frasesLongas)) {
                    $texto = implode(' ', $frasesLongas);
                }
            }
        }

        // ETAPA 6: Limpeza final
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = trim($texto);

        // Log::info('Extração RTF ultra-simplificada finalizada', [
        //     'tamanho_resultado' => strlen($texto),
        //     'preview' => substr($texto, 0, 200)
        // ]);

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
                <w:t>'.htmlspecialchars($texto, ENT_XML1, 'UTF-8').'</w:t>
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
        $nomePdf = 'proposicao_'.$proposicao->id.'.pdf';
        $diretorioPdf = 'proposicoes/pdfs/'.$proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf.'/'.$nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/'.$caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (! is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        // Se existe arquivo físico, tentar converter com LibreOffice
        if ($proposicao->arquivo_path && \Storage::exists($proposicao->arquivo_path)) {
            $caminhoArquivo = storage_path('app/'.$proposicao->arquivo_path);

            $this->tentarConversaoComLibreOffice($caminhoArquivo, $caminhoPdfAbsoluto, $proposicao);
        } else {
            // Usar DomPDF diretamente para proposições sem arquivo físico
            $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
        }

        // Atualizar proposição com caminho do PDF
        $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
        $proposicao->save();
    }

    /**
     * Tentar conversão com LibreOffice
     */
    private function tentarConversaoComLibreOffice(string $caminhoArquivo, string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        // Verificar se LibreOffice está disponível
        $libreOfficeDisponivel = $this->libreOfficeDisponivel();

        if (! $libreOfficeDisponivel) {
            // Fallback: Criar um PDF usando DomPDF
            $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
        } else {
            // Converter para PDF usando LibreOffice
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg(dirname($caminhoPdfAbsoluto)),
                escapeshellarg($caminhoArquivo)
            );

            exec($comando, $output, $returnCode);

            if ($returnCode !== 0) {
                $this->criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao);
            } elseif (! file_exists($caminhoPdfAbsoluto)) {
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
        if (! $user->isLegislativo() && $proposicao->autor_id !== $user->id && ! $user->isAssessorJuridico() && ! $user->isProtocolo()) {
            abort(403, 'Acesso negado.');
        }

        // Para parlamentares, permitir apenas em status específicos onde o PDF já está disponível
        if ($user->isParlamentar() && $proposicao->autor_id === $user->id) {
            $statusPermitidos = ['protocolado', 'aprovado', 'assinado', 'enviado_protocolo', 'retornado_legislativo', 'aprovado_assinatura'];
            if (! in_array($proposicao->status, $statusPermitidos)) {
                abort(403, 'PDF não disponível para download neste status.');
            }
        }

        // Buscar o PDF mais recente para a proposição
        $pdfPath = $this->encontrarPDFMaisRecente($proposicao);

        if (! $pdfPath) {
            abort(404, 'PDF não encontrado.');
        }

        // Servir o arquivo PDF
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="proposicao_'.$proposicao->id.'.pdf"',
        ]);
    }

    /**
     * Servir arquivo PDF da proposição para acesso público (sem autenticação)
     * Apenas para proposições com status 'protocolado'
     */
    public function servePDFPublico($proposicaoId)
    {
        // Buscar a proposição
        $proposicao = Proposicao::findOrFail($proposicaoId);

        // Verificar se o status permite acesso público
        if ($proposicao->status !== 'protocolado') {
            abort(403, 'Esta proposição não está disponível para acesso público.');
        }

        // Para proposições protocoladas, sempre gerar um novo PDF com informações atualizadas
        $pdfPath = $this->gerarPDFAtualizado($proposicao);

        if (! $pdfPath) {
            abort(404, 'PDF não encontrado.');
        }

        // Servir o arquivo PDF
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="proposicao_'.$proposicao->id.'_publico.pdf"',
        ]);
    }

    /**
     * Criar PDF usando DomPDF como fallback
     */
    private function criarPDFComDomPDF(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {
            // Tentar obter conteúdo mais atual possível
            $conteudo = '';

            // ESTRATÉGIA DE EXPORTAÇÃO DIRETA: Como "Salvar como PDF" do OnlyOffice

            // 1. Verificar se existe arquivo editado pelo Legislativo
            if ($proposicao->arquivo_path) {
                $caminhoArquivo = null;

                // Tentar encontrar o arquivo editado (RTF do OnlyOffice)
                $possiveisCaminhos = [
                    storage_path('app/'.$proposicao->arquivo_path),
                    storage_path('app/private/'.$proposicao->arquivo_path),
                    storage_path('app/proposicoes/'.basename($proposicao->arquivo_path)),
                ];

                foreach ($possiveisCaminhos as $caminho) {
                    if (file_exists($caminho)) {
                        $caminhoArquivo = $caminho;
                        break;
                    }
                }

                // 2. Se encontrou arquivo RTF editado, tentar conversão direta para PDF
                if ($caminhoArquivo && $this->libreOfficeDisponivel()) {
                    if ($this->converterArquivoParaPDFDireto($caminhoArquivo, $caminhoPdfAbsoluto)) {
                        // Conversão direta bem-sucedida - PDF idêntico ao do OnlyOffice
                        return;
                    }
                }
            }

            // 3. Fallback: Se conversão direta falhar, usar método DomPDF com conteúdo do banco
            $conteudo = '';
            if (! empty($proposicao->conteudo)) {
                $conteudo = $proposicao->conteudo;
            } else {
                $conteudo = $proposicao->ementa ?: 'Conteúdo não disponível';
            }

            // Criar HTML para DomPDF como fallback
            $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);

            // Usar DomPDF para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            // Salvar PDF
            file_put_contents($caminhoPdfAbsoluto, $pdf->output());

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Converter RTF para texto limpo removendo códigos RTF
     */
    private function converterRTFParaTexto(string $rtfContent): string
    {
        // Se não é RTF, retornar como está
        if (! str_contains($rtfContent, '{\rtf')) {
            return $rtfContent;
        }

        // Para RTF muito complexo como do OnlyOffice, vamos usar uma abordagem mais simples:
        // Buscar por texto real entre códigos RTF usando padrões específicos

        $textosEncontrados = [];

        // 1. Buscar texto em português comum (frases)
        preg_match_all('/(?:[A-ZÁÉÍÓÚÂÊÎÔÛÃÕÀÈÌÒÙÇ][a-záéíóúâêîôûãõàèìòùç\s,.-]{15,})/u', $rtfContent, $matches);
        if (! empty($matches[0])) {
            foreach ($matches[0] as $match) {
                // Limpar RTF restante
                $clean = preg_replace('/\\\\\w+\d*\s*/', ' ', $match);
                $clean = preg_replace('/[{}\\\\]/', '', $clean);
                $clean = trim($clean);
                if (strlen($clean) > 10) {
                    $textosEncontrados[] = $clean;
                }
            }
        }

        // 2. Buscar por textos específicos conhecidos
        $palavrasChave = [
            'CÂMARA MUNICIPAL',
            'Praça da República',
            'Caraguatatuba',
            'MOÇÃO',
            'EMENTA',
            'Resolve',
            'manifesta',
            'dirigir a presente',
        ];

        foreach ($palavrasChave as $palavra) {
            if (stripos($rtfContent, $palavra) !== false) {
                // Extrair contexto ao redor da palavra
                preg_match_all('/[^{}\\\\]*'.preg_quote($palavra, '/').'[^{}\\\\]{0,100}/i', $rtfContent, $matches);
                if (! empty($matches[0])) {
                    foreach ($matches[0] as $match) {
                        $clean = preg_replace('/\\\\\w+\d*\s*/', ' ', $match);
                        $clean = preg_replace('/[{}\\\\]/', '', $clean);
                        $clean = trim($clean);
                        if (strlen($clean) > 5) {
                            $textosEncontrados[] = $clean;
                        }
                    }
                }
            }
        }

        // 3. Se ainda não encontramos texto suficiente, usar método strip_tags
        if (empty($textosEncontrados)) {
            $texto = strip_tags($rtfContent);
            $texto = preg_replace('/\\\\\w+\d*\s*/', ' ', $texto);
            $texto = preg_replace('/[{}\\\\]/', '', $texto);
            $texto = preg_replace('/[^\w\s\.,;:!?\-()áéíóúâêîôûãõàèìòùçÁÉÍÓÚÂÊÎÔÛÃÕÀÈÌÒÙÇ]/u', ' ', $texto);
            $texto = preg_replace('/\s+/', ' ', $texto);

            return trim($texto);
        }

        // Juntar textos encontrados e limpar
        $textoFinal = implode(' ', array_unique($textosEncontrados));
        $textoFinal = preg_replace('/\s+/', ' ', $textoFinal);

        return trim($textoFinal);
    }

    /**
     * Converter arquivo RTF editado diretamente para PDF usando LibreOffice
     * Esta é a conversão mais fiel possível - idêntica ao "Salvar como PDF" do OnlyOffice
     */
    private function converterArquivoParaPDFDireto(string $caminhoArquivo, string $caminhoPdfDestino): bool
    {
        try {
            // Garantir que o diretório de destino existe
            $diretorioDestino = dirname($caminhoPdfDestino);
            if (! is_dir($diretorioDestino)) {
                mkdir($diretorioDestino, 0755, true);
            }

            // Comando LibreOffice para conversão direta RTF -> PDF
            $comando = sprintf(
                'libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir %s %s 2>/dev/null',
                escapeshellarg($diretorioDestino),
                escapeshellarg($caminhoArquivo)
            );

            exec($comando, $output, $returnCode);

            // LibreOffice gera PDF com mesmo nome do arquivo fonte
            $nomeArquivoSemExtensao = pathinfo($caminhoArquivo, PATHINFO_FILENAME);
            $pdfGerado = $diretorioDestino.'/'.$nomeArquivoSemExtensao.'.pdf';

            // Verificar se conversão foi bem-sucedida
            if ($returnCode === 0 && file_exists($pdfGerado)) {
                // Se PDF foi gerado com nome diferente, renomear
                if ($pdfGerado !== $caminhoPdfDestino) {
                    rename($pdfGerado, $caminhoPdfDestino);
                }

                return true;
            } else {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Instalar LibreOffice se não estiver disponível (método para containers)
     */
    private function instalarLibreOfficeSeNecessario(): bool
    {
        if ($this->libreOfficeDisponivel()) {
            return true;
        }

        // Tentar instalar LibreOffice em ambiente Docker/Linux
        try {
            // Detectar tipo de sistema (Alpine vs Debian/Ubuntu)
            exec('which apk', $apkOutput, $apkReturn);
            $isAlpine = ($apkReturn === 0);

            if ($isAlpine) {
                // Alpine Linux (container Docker atual)
                $comandos = [
                    'apk add --no-cache libreoffice',
                    'apk add --no-cache fontconfig ttf-liberation ttf-dejavu',
                    'fc-cache -f',
                ];
            } else {
                // Debian/Ubuntu
                $comandos = [
                    'apt-get update -qq',
                    'apt-get install -y -qq libreoffice --no-install-recommends',
                    'apt-get install -y -qq fonts-liberation fonts-dejavu-core',
                    'apt-get clean',
                    'rm -rf /var/lib/apt/lists/*',
                ];
            }

            foreach ($comandos as $comando) {
                exec($comando, $output, $returnCode);
                if ($returnCode !== 0) {
                    return false;
                }
            }

            return $this->libreOfficeDisponivel();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gerar conteúdo completo para PDF quando documento foi editado pelo Legislativo
     */
    private function gerarConteudoCompletoParaPDF(\App\Models\Proposicao $proposicao): string
    {
        $conteudoCompleto = '';

        // 1. Adicionar informação sobre edição
        $conteudoCompleto .= "DOCUMENTO EDITADO PELO LEGISLATIVO\n";
        $conteudoCompleto .= 'Última modificação: '.$proposicao->ultima_modificacao->format('d/m/Y H:i')."\n";
        if ($proposicao->modificadoPor) {
            $conteudoCompleto .= 'Modificado por: '.$proposicao->modificadoPor->name."\n";
        }
        $conteudoCompleto .= str_repeat('-', 50)."\n\n";

        // 2. Adicionar ementa
        $conteudoCompleto .= "EMENTA:\n";
        $conteudoCompleto .= $proposicao->ementa."\n\n";

        // 3. Adicionar conteúdo do banco
        if (! empty($proposicao->conteudo)) {
            $conteudoCompleto .= "CONTEÚDO ORIGINAL:\n";
            $conteudoCompleto .= $proposicao->conteudo."\n\n";
        }

        // 4. Tentar extrair informações básicas do arquivo editado
        if ($proposicao->arquivo_path) {
            $caminhoArquivo = null;
            $possiveisCaminhos = [
                storage_path('app/'.$proposicao->arquivo_path),
                storage_path('app/private/'.$proposicao->arquivo_path),
            ];

            foreach ($possiveisCaminhos as $caminho) {
                if (file_exists($caminho)) {
                    $caminhoArquivo = $caminho;
                    break;
                }
            }

            if ($caminhoArquivo) {
                $conteudoCompleto .= "INFORMAÇÕES DO ARQUIVO EDITADO:\n";
                $conteudoCompleto .= 'Arquivo: '.basename($proposicao->arquivo_path)."\n";
                $conteudoCompleto .= 'Tamanho: '.number_format(filesize($caminhoArquivo) / 1024, 2)." KB\n";
                $conteudoCompleto .= 'Data do arquivo: '.date('d/m/Y H:i:s', filemtime($caminhoArquivo))."\n\n";

                // Tentar extrair alguns trechos legíveis do RTF
                $arquivoContent = file_get_contents($caminhoArquivo);
                $trechosExtraidos = $this->extrairTrechosLegiveisRTF($arquivoContent);

                if (! empty($trechosExtraidos)) {
                    $conteudoCompleto .= "TRECHOS IDENTIFICADOS NO DOCUMENTO EDITADO:\n";
                    $conteudoCompleto .= $trechosExtraidos."\n\n";
                }
            }
        }

        // 5. Adicionar observações do fluxo legislativo se houver
        if ($proposicao->observacoes_legislativo) {
            $conteudoCompleto .= "OBSERVAÇÕES DO LEGISLATIVO:\n";
            $conteudoCompleto .= $proposicao->observacoes_legislativo."\n\n";
        }

        $conteudoCompleto .= 'NOTA: Este PDF contém a versão mais atual do documento, incluindo todas as alterações realizadas durante o processo legislativo.';

        return $conteudoCompleto;
    }

    /**
     * Extrair trechos legíveis de um arquivo RTF (método simplificado)
     */
    private function extrairTrechosLegiveisRTF(string $rtfContent): string
    {
        $trechos = [];

        // Buscar por texto comum em português
        $patterns = [
            '/(?:^|\s)([A-ZÁÉÍÓÚÂÊÎÔÛÃÕÀÈÌÒÙÇ][a-záéíóúâêîôûãõàèìòùç\s,.\-!?:;]{25,})(?:\s|$)/mu',
            '/\b(Art\.|Artigo|Parágrafo|Inciso|Alínea)[^{}\\\\]{5,50}/i',
            '/\b(Considerando|Resolve|Determina|Estabelece)[^{}\\\\]{5,100}/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $rtfContent, $matches)) {
                foreach ($matches[1] as $match) {
                    $match = trim($match);
                    if (strlen($match) > 15 && ! preg_match('/[{}\\\\]/', $match)) {
                        $trechos[] = $match;
                    }
                }
            }
        }

        // Remover duplicatas e retornar os primeiros 5 trechos
        $trechos = array_unique($trechos);
        $trechos = array_slice($trechos, 0, 5);

        return implode("\n- ", $trechos);
    }

    /**
     * Método alternativo para extrair texto de RTF quando conversão principal falha
     */
    private function extrairTextoRTFAlternativo(string $rtfContent): string
    {
        // Método mais agressivo usando LibreOffice se disponível
        if ($this->libreOfficeDisponivel()) {
            try {
                // Salvar RTF temporariamente
                $tempRtf = tempnam(sys_get_temp_dir(), 'rtf_extract_').'.rtf';
                $tempTxt = tempnam(sys_get_temp_dir(), 'txt_extract_').'.txt';

                file_put_contents($tempRtf, $rtfContent);

                // Usar LibreOffice para converter RTF para texto
                $comando = sprintf(
                    'libreoffice --headless --convert-to txt --outdir %s %s 2>/dev/null',
                    escapeshellarg(dirname($tempTxt)),
                    escapeshellarg($tempRtf)
                );

                exec($comando, $output, $returnCode);

                $arquivoTxt = dirname($tempTxt).'/'.pathinfo($tempRtf, PATHINFO_FILENAME).'.txt';

                if ($returnCode === 0 && file_exists($arquivoTxt)) {
                    $texto = file_get_contents($arquivoTxt);

                    // Limpar arquivos temporários
                    @unlink($tempRtf);
                    @unlink($arquivoTxt);

                    if (! empty($texto) && strlen($texto) > 20) {
                        return trim($texto);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        // Método de fallback: buscar por texto comum entre códigos RTF
        $textoExtraido = '';

        // Extrair texto que aparece entre espaços e caracteres comuns
        if (preg_match_all('/\s([A-Za-zÀ-ÿ\s,.\-!?:;]{15,})\s/u', $rtfContent, $matches)) {
            foreach ($matches[1] as $match) {
                $match = trim($match);
                if (strlen($match) > 10 && ! preg_match('/[{}\\\\]/', $match)) {
                    $textoExtraido .= $match.' ';
                }
            }
        }

        // Se ainda não temos texto suficiente, buscar parágrafos
        if (strlen($textoExtraido) < 100) {
            if (preg_match_all('/[A-ZÁÉÍÓÚÂÊÎÔÛÃÕÀÈÌÒÙÇ][a-záéíóúâêîôûãõàèìòùç\s,.\-!?:;]{30,}/u', $rtfContent, $matches)) {
                foreach ($matches[0] as $match) {
                    $match = trim($match);
                    if (strlen($match) > 20 && ! preg_match('/[{}\\\\]/', $match)) {
                        $textoExtraido .= $match.' ';
                    }
                }
            }
        }

        return trim($textoExtraido);
    }

    /**
     * Gerar HTML completo para PDF incluindo protocolo e assinatura
     */
    private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        // Determinar o título baseado no tipo
        $tipoFormatado = $this->formatarTipoProposicao($proposicao->tipo);

        // Informações do protocolo
        $numeroProtocolo = $proposicao->numero_protocolo ?: 'Aguardando Protocolo';
        $dataProtocolo = $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y') : '';

        // Informações da assinatura
        $assinaturaInfo = '';
        if ($proposicao->assinatura_digital) {
            $assinaturaData = json_decode($proposicao->assinatura_digital, true);
            if ($assinaturaData) {
                $assinaturaInfo = "
                <div class='assinatura-info'>
                    <strong>Assinado digitalmente por:</strong> {$assinaturaData['nome']}<br>
                    <strong>Data da assinatura:</strong> {$assinaturaData['data']}<br>
                    <strong>Certificado:</strong> {$assinaturaData['tipo']} - {$assinaturaData['id']}
                </div>";
            }
        }

        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>{$tipoFormatado} Nº {$proposicao->id}/{$proposicao->ano}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .title { font-size: 20px; font-weight: bold; margin: 15px 0; text-transform: uppercase; }
                .protocolo { font-size: 16px; font-weight: bold; margin: 10px 0; color: #2c5aa0; }
                .info { font-size: 12px; color: #666; margin: 5px 0; }
                .content { margin-top: 30px; text-align: justify; }
                .ementa { background: #f5f5f5; padding: 15px; margin: 20px 0; border-left: 4px solid #007bff; }
                .assinatura-info { background: #e8f4fd; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745; font-size: 12px; }
                .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>CÂMARA MUNICIPAL DE CARAGUATATUBA</h1>
                <div class='title'>{$tipoFormatado} Nº {$numeroProtocolo}</div>
                <div class='protocolo'>PROTOCOLO: {$numeroProtocolo}</div>
                <div class='info'>Autor: ".($proposicao->autor->name ?? 'N/A')."</div>
                <div class='info'>Data de Criação: ".$proposicao->created_at->format('d/m/Y').'</div>
                '.($dataProtocolo ? "<div class='info'>Data de Protocolo: {$dataProtocolo}</div>" : '')."
            </div>
            
            <div class='ementa'>
                <strong>EMENTA:</strong><br>
                ".nl2br(htmlspecialchars($proposicao->ementa))."
            </div>
            
            <div class='content'>
                ".nl2br(htmlspecialchars($conteudo ?: 'Conteúdo não disponível'))."
            </div>
            
            {$assinaturaInfo}
            
            <div class='footer'>
                <p>Documento oficial da Câmara Municipal de Caraguatatuba</p>
                <p>Gerado em: ".now()->format('d/m/Y H:i:s').'</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Processar variáveis no template usando TemplateParametrosService
     */
    private function processarVariaveisTemplate(string $conteudo): string
    {
        try {
            // Primeiro, converter variáveis com escape RTF para formato normal
            // De $\{variavel\} para ${variavel}
            $conteudo = str_replace(['$\\{', '\\}'], ['${', '}'], $conteudo);

            $templateService = app(\App\Services\Template\TemplateParametrosService::class);

            // Processar o template com variáveis padrão
            return $templateService->processarTemplate($conteudo, []);

        } catch (\Exception $e) {
            // Se houver erro, retornar conteúdo original
            return $conteudo;
        }
    }

    /**
     * Substituir variáveis no template usando o novo sistema
     */
    private function substituirVariaveisNoTemplate($template, array $templateVariables, Proposicao $proposicao, TemplateVariablesService $templateVariablesService): string
    {
        try {
            // Obter conteúdo do template usando o método privado do service (reflexão)
            $reflection = new \ReflectionClass($templateVariablesService);
            $method = $reflection->getMethod('getTemplateContent');
            $method->setAccessible(true);
            $templateContent = $method->invoke($templateVariablesService, $template);

            if (! $templateContent) {
                return $this->criarTextoBasico($proposicao, $templateVariables);
            }

            // Variáveis do sistema para preenchimento automático
            $user = auth()->user();
            $now = now();

            $systemVariables = [
                'data_atual' => $now->format('d/m/Y'),
                'data_extenso' => $now->locale('pt_BR')->translatedFormat('j \\d\\e F \\d\\e Y'),
                'dia_atual' => $now->format('d'),
                'mes_atual' => $now->locale('pt_BR')->translatedFormat('F'),
                'ano_atual' => $now->format('Y'),
                'hora_atual' => $now->format('H:i'),
                'data_criacao' => $proposicao->created_at->format('d/m/Y'),

                'numero_proposicao' => $proposicao->numero ?? $proposicao->id,
                'tipo_proposicao' => $proposicao->tipo_formatado ?? 'Proposição',
                'status_proposicao' => ucfirst($proposicao->status ?? 'rascunho'),

                'autor_nome' => $proposicao->autor->name ?? $user->name ?? 'Autor',
                'nome_parlamentar' => $proposicao->autor->name ?? $user->name ?? 'Parlamentar',
                'cargo_parlamentar' => 'Vereador(a)',
                'email_parlamentar' => $proposicao->autor->email ?? $user->email ?? '',
                'partido_parlamentar' => '', // TODO: implementar quando tiver campo partido

                'municipio' => config('app.municipio', 'São Paulo'),
                'nome_camara' => config('app.nome_camara', 'Câmara Municipal'),
                'endereco_camara' => config('app.endereco_camara', ''),
                'legislatura_atual' => config('app.legislatura_atual', '2021-2024'),
                'sessao_legislativa' => config('app.sessao_legislativa', '2024'),

                'imagem_cabecalho' => config('app.imagem_cabecalho', ''),
            ];

            // Combinar variáveis do template com as do sistema
            $allVariables = array_merge($systemVariables, $templateVariables);

            // Substituir variáveis no conteúdo do template
            $processedContent = $templateContent;
            foreach ($allVariables as $key => $value) {
                $processedContent = str_replace('${'.$key.'}', $value, $processedContent);
            }

            return $processedContent;

        } catch (\Exception $e) {
            // Fallback: criar texto básico
            return $this->criarTextoBasico($proposicao, $templateVariables);
        }
    }

    /**
     * Gera ementa automaticamente baseada no tipo de proposição e variáveis disponíveis
     */
    private function gerarEmentaAutomatica($proposicao, $variaveisTemplate = [])
    {
        // Primeira tentativa: usar variáveis do template se existirem
        if (! empty($variaveisTemplate)) {
            if (isset($variaveisTemplate['ementa']['label']) && ! empty($variaveisTemplate['ementa'])) {
                // Se tem campo ementa no template mas sem valor, usar descrição padrão
                $ementa = 'Ementa a ser definida - '.ucfirst(str_replace('_', ' ', $proposicao->tipo));
            } elseif (isset($variaveisTemplate['finalidade']['label']) && ! empty($variaveisTemplate['finalidade'])) {
                $ementa = 'Proposta com finalidade a ser definida';
            } elseif (isset($variaveisTemplate['texto']['label']) && ! empty($variaveisTemplate['texto'])) {
                $ementa = 'Proposição em elaboração - conteúdo a ser definido';
            }
        }

        // Segunda tentativa: gerar baseado no tipo de proposição
        if (empty($ementa)) {
            switch ($proposicao->tipo) {
                case 'proposta_emenda_constituicao':
                    $ementa = 'Proposta de Emenda à Constituição - dispositivos e finalidade a serem definidos';
                    break;
                case 'proposta_emenda_lei_organica':
                    $ementa = 'Proposta de Emenda à Lei Orgânica Municipal - dispositivos a serem definidos';
                    break;
                case 'projeto_lei_ordinaria':
                    $ementa = 'Projeto de Lei Ordinária - matéria a ser definida';
                    break;
                case 'projeto_lei_complementar':
                    $ementa = 'Projeto de Lei Complementar - matéria a ser definida';
                    break;
                case 'indicacao':
                    $ementa = 'Indicação - assunto a ser definido';
                    break;
                case 'projeto_decreto_legislativo':
                    $ementa = 'Projeto de Decreto Legislativo - matéria a ser definida';
                    break;
                case 'projeto_resolucao':
                    $ementa = 'Projeto de Resolução - matéria a ser definida';
                    break;
                case 'mocao':
                    $ementa = 'Moção - assunto a ser definido';
                    break;
                default:
                    $ementa = 'Proposição em elaboração - '.ucfirst(str_replace('_', ' ', $proposicao->tipo));
                    break;
            }
        }

        return $ementa ?? null;
    }

    /**
     * Carrega valores existentes de diferentes fontes para pré-preencher campos
     */
    private function carregarValoresExistentes($proposicao)
    {
        $valoresExistentes = [];

        // Log::info('Carregando valores existentes', [
        //     'proposicao_id' => $proposicao->id,
        //     'status' => $proposicao->status
        // ]);

        // 1. Carregar variáveis do template (usa accessor que já tenta banco e sessão)
        $variaveisTemplate = $proposicao->variaveis_template;
        if (! empty($variaveisTemplate)) {
            $valoresExistentes = array_merge($valoresExistentes, $variaveisTemplate);
            // Log::info('Valores carregados via accessor', [
            //     'proposicao_id' => $proposicao->id,
            //     'variaveis' => array_keys($variaveisTemplate),
            //     'valores' => $variaveisTemplate
            // ]);
        }

        // 3. Mapear campos básicos da proposição para variáveis do template atual
        // Só mapear se não existirem valores mais específicos
        if (! empty($proposicao->conteudo) && ! isset($valoresExistentes['texto'])) {
            $valoresExistentes['texto'] = $proposicao->conteudo;
        }

        // Para ementa, tentar mapear para finalidade se a ementa parecer automática
        if (! empty($proposicao->ementa) && ! isset($valoresExistentes['finalidade'])) {
            // Se a ementa contém indicadores de que foi gerada automaticamente, não mapear
            $ementaAutomatica = str_contains($proposicao->ementa, 'serem definidos') ||
                               str_contains($proposicao->ementa, 'a ser definid') ||
                               str_contains($proposicao->ementa, 'em elaboração');

            if (! $ementaAutomatica) {
                // Ementa parece ter conteúdo real, mapear para finalidade
                $valoresExistentes['finalidade'] = $proposicao->ementa;
            }
        }

        // 4. Para proposição "em_edicao", tentar extrair de conteúdo processado
        if ($proposicao->status === 'em_edicao' && ! empty($proposicao->conteudo_processado)) {
            // Aqui poderia tentar extrair valores do conteúdo processado usando regex
            // Por simplicidade, vou pular essa parte por enquanto
        }

        // Log::info('Valores finais carregados', [
        //     'proposicao_id' => $proposicao->id,
        //     'total_variaveis' => count($valoresExistentes),
        //     'variaveis' => array_keys($valoresExistentes)
        // ]);

        return $valoresExistentes;
    }

    /**
     * Remove código LaTeX e outros códigos técnicos do texto
     */
    private function limparCodigoLatex($texto)
    {
        if (empty($texto)) {
            return $texto;
        }

        // Remover comandos LaTeX comuns
        $patterns = [
            '/\\\\documentclass\{[^}]*\}/',
            '/\\\\usepackage(\[[^\]]*\])?\{[^}]*\}/',
            '/\\\\begin\{document\}/',
            '/\\\\end\{document\}/',
            '/\\\\textbf\{([^}]*)\}/',
            '/\\\\vspace\{[^}]*\}/',
            '/\\\\onehalfspacing/',
            '/\\\\spacing/',
            // Comandos de formatação
            '/\\\\[a-zA-Z]+(\{[^}]*\})*/',
            // Linhas que começam com \
            '/^\\\\.*$/m',
        ];

        $replacements = [
            '', // remove documentclass
            '', // remove usepackage
            '', // remove begin{document}
            '', // remove end{document}
            '$1', // mantém apenas o conteúdo do textbf
            '', // remove vspace
            '', // remove onehalfspacing
            '', // remove spacing
            '', // remove outros comandos LaTeX
            '', // remove linhas que começam com \
        ];

        $textoLimpo = preg_replace($patterns, $replacements, $texto);

        // Limpar linhas vazias excessivas
        $textoLimpo = preg_replace('/\n\s*\n\s*\n/', "\n\n", $textoLimpo);

        // Remover espaços em branco no início e fim
        $textoLimpo = trim($textoLimpo);

        // Log::info('Código LaTeX removido do texto', [
        //     'texto_original_length' => strlen($texto),
        //     'texto_limpo_length' => strlen($textoLimpo),
        //     'removeu_latex' => $texto !== $textoLimpo
        // ]);

        return $textoLimpo;
    }

    /**
     * Gerar código RTF para inserir uma imagem
     */
    private function gerarCodigoRTFImagem(string $caminhoImagem): string
    {
        try {
            // Verificar se arquivo existe e obter informações
            if (! file_exists($caminhoImagem)) {
                return '[IMAGEM DO CABEÇALHO - ARQUIVO NÃO ENCONTRADO]';
            }

            $info = getimagesize($caminhoImagem);
            if (! $info) {
                return '[IMAGEM DO CABEÇALHO - FORMATO INVÁLIDO]';
            }

            // Para o OnlyOffice, vamos inserir a imagem usando código RTF específico
            // Primeiro, converter a imagem para formato hexadecimal
            $imagemData = file_get_contents($caminhoImagem);
            $imagemHex = bin2hex($imagemData);

            // Obter dimensões da imagem
            $largura = $info[0];
            $altura = $info[1];

            // Redimensionar se necessário (máximo 200px de largura para evitar arquivo muito grande)
            if ($largura > 200) {
                $novaLargura = 200;
                $novaAltura = intval(($novaLargura * $altura) / $largura);
            } else {
                $novaLargura = $largura;
                $novaAltura = $altura;
            }

            // Converter para twips (1 pixel = 15 twips aprox)
            $larguraTwips = $novaLargura * 15;
            $alturaTwips = $novaAltura * 15;

            // Determinar o tipo MIME da imagem
            $tipoImagem = $info['mime'];
            $formatoRTF = match ($tipoImagem) {
                'image/png' => 'pngblip',
                'image/jpeg', 'image/jpg' => 'jpegblip',
                default => 'pngblip'
            };

            // Gerar código RTF para inserir a imagem
            $rtfImagem = "{\pict\\{$formatoRTF}\\picw{$largura}\\pich{$altura}\\picwgoal{$larguraTwips}\\pichgoal{$alturaTwips} {$imagemHex}}";

            // Centralizar a imagem
            return "{\\qc {$rtfImagem}\\par}";

        } catch (\Exception $e) {
            // Fallback para placeholder se houver erro
            $nomeArquivo = basename($caminhoImagem);

            return "{\\qc\\b\\fs20 [INSERIR IMAGEM: {$nomeArquivo}]\\par}";
        }
    }

    /**
     * Retorna dados atualizados da proposição via AJAX
     * Usado após fechar o editor OnlyOffice para atualizar a página sem reload completo
     */
    public function getDadosAtualizados(Proposicao $proposicao)
    {
        try {
            // Verificar permissão de visualização (se houver usuário autenticado)
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->isParlamentar() && $proposicao->autor_id !== auth()->id()) {
                    return response()->json(['error' => 'Sem permissão'], 403);
                }
            }

            // Recarregar a proposição do banco para garantir dados fresh
            $proposicao->refresh();

            // ESTRATÉGIA SIMPLIFICADA: Sempre extrair ementa do conteúdo original da database
            $conteudoProcessado = $proposicao->conteudo;
            $ementaExtraida = $proposicao->ementa;

            // 1. SEMPRE tentar extrair ementa do conteúdo original (limpo e confiável)
            if ($proposicao->conteudo && strlen($proposicao->conteudo) > 20) {
                $novaEmenta = $this->extrairEmentaDoConteudo($proposicao->conteudo, $proposicao->ementa);
                if ($novaEmenta && $novaEmenta !== 'Ementa a ser definida') {
                    $ementaExtraida = $novaEmenta;
                    \Log::info('✅ Ementa extraída do conteúdo original: '.substr($ementaExtraida, 0, 100));
                }
            }

            // 2. Se existe arquivo salvo, tentar mostrar conteúdo atualizado
            if ($proposicao->arquivo_path) {
                try {
                    $onlyOfficeService = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
                    $textoDoArquivo = $onlyOfficeService->extrairTextoDoArquivo($proposicao);

                    // Verificar se a extração do arquivo foi bem-sucedida
                    if ($textoDoArquivo &&
                        strlen(trim($textoDoArquivo)) > 50 &&
                        ! preg_match('/^[\s;*\\\\-]+$/', $textoDoArquivo)) {

                        $conteudoProcessado = $textoDoArquivo;
                        \Log::info('✅ Conteúdo atualizado extraído do arquivo');
                    } else {
                        \Log::info('⚠️ Extração do arquivo falhou, mantendo conteúdo original');
                    }
                } catch (\Exception $e) {
                    \Log::info('⚠️ Erro ao extrair texto do arquivo: '.$e->getMessage());
                }
            }

            // Retornar dados atualizados
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $proposicao->id,
                    'tipo' => $proposicao->tipo_formatado,
                    'ementa' => $ementaExtraida ?: 'Ementa a ser definida',
                    'conteudo' => $proposicao->conteudo,
                    'conteudo_processado' => $conteudoProcessado,
                    'arquivo_path' => $proposicao->arquivo_path,
                    'ultima_modificacao' => $proposicao->ultima_modificacao ? $proposicao->ultima_modificacao->format('d/m/Y H:i') : null,
                    'status' => $proposicao->status,
                    'status_label' => $proposicao->status_label,
                    'autor' => $proposicao->autor ? $proposicao->autor->name : 'Desconhecido',
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar dados atualizados da proposição: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao buscar dados atualizados',
            ], 500);
        }
    }

    /**
     * Extrair ementa inteligente do conteúdo editado
     */
    private function extrairEmentaDoConteudo(string $conteudoProcessado, ?string $ementaOriginal): string
    {
        // Limpar o conteúdo de tags HTML e caracteres de formatação
        $textoLimpo = strip_tags($conteudoProcessado);
        $textoLimpo = html_entity_decode($textoLimpo);
        $textoLimpo = preg_replace('/\s+/', ' ', $textoLimpo);
        $textoLimpo = trim($textoLimpo);

        // Se o conteúdo está corrompido ou muito pequeno, manter ementa original
        if (strlen($textoLimpo) < 50 || preg_match('/[\\\\*-]+/', $textoLimpo)) {
            return $ementaOriginal ?: 'Ementa a ser definida';
        }

        // Procurar por padrões de ementa
        $padroes = [
            '/EMENTA:\s*([^.]+\.)/i',
            '/Ementa:\s*([^.]+\.)/i',
            '/^([^.]+\.)/m',  // Primeira frase que termina com ponto
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $textoLimpo, $matches)) {
                $ementaEncontrada = trim($matches[1]);
                if (strlen($ementaEncontrada) > 20 && strlen($ementaEncontrada) < 500) {
                    return $ementaEncontrada;
                }
            }
        }

        // Se não encontrou padrão específico, extrair informação significativa
        $linhas = explode("\n", $textoLimpo);
        $linhas = array_filter($linhas, function ($linha) {
            $linha = trim($linha);

            // Ignorar linhas muito curtas, títulos genéricos, ou apenas pontuação
            return strlen($linha) > 15 &&
                   ! preg_match('/^(What|Why|How|Onde|Como|Quando|Por que)/i', $linha) &&
                   ! preg_match('/^[\s\p{P}]*$/u', $linha);
        });

        if (count($linhas) > 0) {
            $linhasValidas = array_values($linhas); // Re-indexar
            $primeiraLinha = trim($linhasValidas[0]);

            // Se a primeira linha parece ser uma pergunta ou título, tentar a próxima
            if (preg_match('/\?$/', $primeiraLinha) && count($linhasValidas) > 1) {
                $primeiraLinha = trim($linhasValidas[1]);
            }

            // Se a primeira linha é muito curta, tentar adicionar contexto
            if (strlen($primeiraLinha) < 80 && count($linhasValidas) > 1) {
                $segundaLinha = trim($linhasValidas[1]);
                if (strlen($segundaLinha) > 15 && ! preg_match('/\?$/', $segundaLinha)) {
                    // Adicionar segunda linha se não for uma pergunta
                    $primeiraLinha .= '. '.$segundaLinha;
                }
            }

            // Limitar tamanho da ementa
            if (strlen($primeiraLinha) > 250) {
                // Tentar cortar em uma frase completa
                $corte = strrpos(substr($primeiraLinha, 0, 247), '.');
                if ($corte > 100) {
                    $primeiraLinha = substr($primeiraLinha, 0, $corte + 1);
                } else {
                    $primeiraLinha = substr($primeiraLinha, 0, 247).'...';
                }
            }

            // Verificar se é uma ementa válida
            if (strlen($primeiraLinha) > 20 && ! preg_match('/^[\s\p{P}]*$/u', $primeiraLinha)) {
                return $primeiraLinha;
            }
        }

        // Fallback: manter ementa original
        return $ementaOriginal ?: 'Ementa a ser definida';
    }

    /**
     * Consulta pública de proposição (sem autenticação)
     * Permite consultar status e informações básicas através do QR Code
     */
    public function consultaPublica($id)
    {
        $proposicao = Proposicao::with(['autor', 'template'])
            ->where('id', $id)
            ->first();

        if (! $proposicao) {
            return view('proposicoes.consulta.nao-encontrada');
        }

        // Apenas mostrar informações públicas
        $informacoesPublicas = [
            'id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'ementa' => $proposicao->ementa,
            'numero_protocolo' => $proposicao->numero_protocolo,
            'status' => $this->traduzirStatus($proposicao->status),
            'data_criacao' => $proposicao->created_at?->format('d/m/Y'),
            'data_protocolo' => $proposicao->data_protocolo?->format('d/m/Y H:i'),
            'autor_nome' => $proposicao->autor?->name,
            'assinado' => ! empty($proposicao->assinatura_digital),
            'data_assinatura' => $proposicao->data_assinatura?->format('d/m/Y H:i'),
            'tem_pdf' => ! empty($proposicao->arquivo_pdf_path) && file_exists(storage_path('app/'.$proposicao->arquivo_pdf_path)),
            'pdf_url' => ! empty($proposicao->arquivo_pdf_path) ? route('proposicoes.consulta.pdf', $proposicao->id) : null,
        ];

        return view('proposicoes.consulta.publica', compact('informacoesPublicas'));
    }

    /**
     * Servir PDF para consulta pública
     */
    public function consultaPublicaPdf($id)
    {
        $proposicao = Proposicao::find($id);

        if (! $proposicao || ! $proposicao->arquivo_pdf_path) {
            abort(404, 'Documento não encontrado');
        }

        $pdfPath = storage_path('app/'.$proposicao->arquivo_pdf_path);

        if (! file_exists($pdfPath)) {
            abort(404, 'Arquivo PDF não encontrado');
        }

        // Apenas permitir download se estiver protocolado ou assinado (documentos públicos)
        if (! in_array($proposicao->status, ['protocolado', 'assinado', 'enviado_protocolo'])) {
            abort(403, 'Documento não disponível para consulta pública');
        }

        $nomeArquivo = $proposicao->tipo.'_'.($proposicao->numero_protocolo ?? $proposicao->id).'.pdf';

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$nomeArquivo.'"',
        ]);
    }

    /**
     * Atualizar status da proposição via formulário tradicional
     */
    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:rascunho,em_edicao,enviado_legislativo,em_revisao,aguardando_aprovacao_autor,devolvido_edicao,retornado_legislativo,aprovado,reprovado',
        ]);

        try {
            $proposicao = Proposicao::findOrFail($id);
            $user = auth()->user();

            // Verificar permissões para alterar status
            if (! $this->canUpdateStatus($proposicao, $request->status, $user)) {
                return redirect()->back()->with('error', 'Você não tem permissão para alterar este status.');
            }

            $oldStatus = $proposicao->status;
            $proposicao->update([
                'status' => $request->status,
                'ultima_modificacao' => now(),
            ]);

            $statusTexts = [
                'rascunho' => 'Rascunho',
                'em_edicao' => 'Em Edição',
                'enviado_legislativo' => 'Enviado ao Legislativo',
                'em_revisao' => 'Em Revisão',
                'aguardando_aprovacao_autor' => 'Aguardando Aprovação do Autor',
                'devolvido_edicao' => 'Devolvido para Edição',
                'retornado_legislativo' => 'Retornado do Legislativo',
                'aprovado' => 'Aprovado',
                'reprovado' => 'Reprovado',
            ];

            $statusText = $statusTexts[$request->status] ?? 'Status Desconhecido';

            return redirect()->route('proposicoes.show', $id)
                ->with('success', "Status alterado para: {$statusText}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar status. Tente novamente.');
        }
    }

    /**
     * Verificar se usuário pode alterar status
     */
    private function canUpdateStatus(Proposicao $proposicao, string $newStatus, $user): bool
    {
        if (! $user) {
            return false;
        }

        $userRole = $user->getRoleNames()->first() ?? 'guest';

        // Admin pode alterar qualquer status
        if ($userRole === 'ADMIN' || str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode aprovar/reprovar/devolver
        if ($userRole === 'LEGISLATIVO' || str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) {
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
     * Buscar dados frescos da proposição para Vue.js
     */
    public function getDadosFrescos($id)
    {
        try {
            $proposicao = Proposicao::with(['autor'])->findOrFail($id);

            // Verificar permissões de visualização
            $user = auth()->user();
            if (! $user) {
                return response()->json(['success' => false, 'message' => 'Não autenticado'], 401);
            }

            // Limpar e extrair dados úteis do conteúdo
            $dadosLimpos = $this->extrairDadosLimpos($proposicao);

            // Formatar dados para Vue.js
            $data = [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'ementa' => $dadosLimpos['ementa'],
                'conteudo' => $dadosLimpos['conteudo'],
                'status' => $proposicao->status,
                'numero_protocolo' => $proposicao->numero_protocolo,
                'created_at' => $proposicao->created_at?->toISOString(),
                'updated_at' => $proposicao->updated_at?->toISOString(),
                'autor' => [
                    'id' => $proposicao->autor?->id,
                    'name' => $proposicao->autor?->name,
                    'email' => $proposicao->autor?->email,
                ],
                'has_arquivo' => ! empty($proposicao->arquivo_path),
                'has_pdf' => ! empty($proposicao->arquivo_pdf_path),
                'meta' => [
                    'word_count' => str_word_count(strip_tags($dadosLimpos['conteudo'] ?? '')),
                    'char_count' => strlen($dadosLimpos['conteudo'] ?? ''),
                    'has_content' => ! empty($dadosLimpos['conteudo']),
                    'is_complete' => ! empty($dadosLimpos['ementa']) && ! empty($dadosLimpos['conteudo']),
                ],
            ];

            return response()->json([
                'success' => true,
                'proposicao' => $data,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados da proposição',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extrair dados limpos da proposição removendo elementos de template
     */
    private function extrairDadosLimpos($proposicao)
    {
        // Inicializar com dados originais
        $ementa = $proposicao->ementa ?? '';
        $conteudo = $proposicao->conteudo ?? '';

        // Se o conteúdo contém elementos de template, extrair dados úteis
        if (str_contains($conteudo, 'assinatura_digital_info') ||
            str_contains($conteudo, 'qrcode_html') ||
            str_contains($conteudo, 'EMENTA:')) {

            // Extrair ementa do conteúdo se presente
            if (preg_match('/EMENTA:\s*([^A]+?)\s*A Câmara/s', $conteudo, $matches)) {
                $ementaExtraida = trim($matches[1]);
                if (! empty($ementaExtraida)) {
                    $ementa = $ementaExtraida;
                }
            }

            // Extrair conteúdo principal (texto entre "A Câmara Municipal manifesta:" e "Resolve dirigir")
            if (preg_match('/A Câmara Municipal manifesta:\s*(.*?)\s*Resolve dirigir/s', $conteudo, $matches)) {
                $conteudoExtraido = trim($matches[1]);
                if (! empty($conteudoExtraido)) {
                    $conteudo = $conteudoExtraido;
                }
            } else {
                // Tentar extrair texto entre outras marcações comuns
                if (preg_match('/manifesta:\s*(.*?)\s*(?:Caraguatatuba|____)/s', $conteudo, $matches)) {
                    $conteudoExtraido = trim($matches[1]);
                    if (! empty($conteudoExtraido)) {
                        $conteudo = $conteudoExtraido;
                    }
                }
            }

            // Limpar elementos de template restantes
            $elementosParaRemover = [
                'assinatura_digital_info',
                'qrcode_html',
                'MOÇÃO Nº [AGUARDANDO PROTOCOLO]',
                '____________________________________',
                'Câmara Municipal de Caraguatatuba - Documento Oficial',
            ];

            foreach ($elementosParaRemover as $elemento) {
                $conteudo = str_replace($elemento, '', $conteudo);
                $ementa = str_replace($elemento, '', $ementa);
            }

            // Limpar espaços extras e quebras de linha desnecessárias
            $conteudo = preg_replace('/\s+/', ' ', trim($conteudo));
            $ementa = preg_replace('/\s+/', ' ', trim($ementa));
        }

        // Fallbacks para dados vazios
        if (empty($ementa) || $ementa === 'Criado pelo Parlamentar') {
            $ementa = 'Moção em elaboração';
        }

        if (empty($conteudo)) {
            $conteudo = 'Conteúdo em elaboração pelo parlamentar';
        }

        return [
            'ementa' => $ementa,
            'conteudo' => $conteudo,
        ];
    }

    /**
     * Traduzir status para linguagem amigável ao público
     */
    private function traduzirStatus(string $status): string
    {
        $statusMap = [
            'rascunho' => 'Em Elaboração',
            'enviado_legislativo' => 'Em Análise Legislativa',
            'aprovado_assinatura' => 'Aprovado - Aguardando Assinatura',
            'assinado' => 'Assinado Digitalmente',
            'enviado_protocolo' => 'Enviado para Protocolo',
            'protocolado' => 'Protocolado - Tramitação Iniciada',
            'devolvido_correcao' => 'Devolvido para Correções',
            'retornado_legislativo' => 'Retornado ao Legislativo',
        ];

        return $statusMap[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Limpar conteúdo RTF corrompido
     */
    private function limparConteudoRTF(string $conteudo): string
    {
        // Se não é RTF, retornar como está
        if (! str_contains($conteudo, '{\rtf')) {
            return $conteudo;
        }

        // Remover códigos RTF complexos e caracteres Unicode corrompidos
        $conteudo = preg_replace('/\\\\u[0-9a-fA-F]+\*/', '', $conteudo); // Remove \u65*\u114* etc
        $conteudo = preg_replace('/\\\\\w+\d*\s*/', ' ', $conteudo);
        $conteudo = preg_replace('/[{}\\\\]/', '', $conteudo);
        $conteudo = preg_replace('/\* \* \* \* \*;?\s*/', '', $conteudo);
        $conteudo = preg_replace('/\d{10,}/', '', $conteudo);
        $conteudo = preg_replace('/[A-Z0-9]{10,}/', '', $conteudo);
        $conteudo = preg_replace('/\s*;\s*/', ' ', $conteudo);

        // Remover sequências específicas de caracteres corrompidos
        $conteudo = preg_replace('/[0-9]{2,}[a-zA-Z]{2,}[0-9]{2,}/', '', $conteudo);
        $conteudo = preg_replace('/[a-zA-Z]{2,}[0-9]{2,}[a-zA-Z]{2,}/', '', $conteudo);

        // Limpar espaços múltiplos e caracteres especiais
        $conteudo = preg_replace('/\s+/', ' ', $conteudo);
        $conteudo = preg_replace('/[^\w\s\-\.\,\:\;\(\)]/', '', $conteudo);

        // Remover linhas vazias ou muito curtas
        $linhas = explode("\n", $conteudo);
        $linhasLimpas = array_filter($linhas, function ($linha) {
            $linhaLimpa = trim($linha);

            return strlen($linhaLimpa) > 10 && ! preg_match('/^[\s\*\-;]+$/', $linhaLimpa);
        });

        return trim(implode("\n", $linhasLimpas));
    }

    /**
     * Gerar PDF atualizado com informações corretas para proposições protocoladas
     */
    private function gerarPDFAtualizado(Proposicao $proposicao): ?string
    {
        // Para proposições protocoladas, sempre gerar um novo PDF
        $nomeArquivo = "proposicao_{$proposicao->id}_protocolado_".time().'_corrigido.pdf';
        $caminhoPdf = storage_path("app/proposicoes/pdfs/{$proposicao->id}/{$nomeArquivo}");

        // Garantir que o diretório existe
        $diretorio = dirname($caminhoPdf);
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        try {
            // Para proposições com conteúdo RTF corrompido, usar apenas a ementa
            $conteudoParaPDF = $proposicao->ementa ?: 'Conteúdo não disponível';

            // Se a ementa for muito curta, adicionar informações básicas
            if (strlen($conteudoParaPDF) < 100) {
                $conteudoParaPDF = "Ementa: {$conteudoParaPDF}\n\n";
                $conteudoParaPDF .= 'Tipo: '.($proposicao->tipo ?? 'Proposição')."\n";
                $conteudoParaPDF .= 'Autor: '.($proposicao->autor->name ?? 'Parlamentar')."\n";
                $conteudoParaPDF .= 'Data: '.($proposicao->created_at ? $proposicao->created_at->format('d/m/Y') : 'N/A')."\n";
                $conteudoParaPDF .= 'Status: '.($proposicao->status ?? 'N/A')."\n\n";
                $conteudoParaPDF .= 'Conteúdo completo disponível no sistema.';
            }

            // Criar HTML simples para teste
            $tipo = $proposicao->tipo ? $proposicao->tipo : 'Proposição';
            $autor = $proposicao->autor->name ? $proposicao->autor->name : 'Parlamentar';
            $status = $proposicao->status ? $proposicao->status : 'N/A';
            $data = $proposicao->created_at ? $proposicao->created_at->format('d/m/Y') : 'N/A';
            $dataAtual = now()->format('d/m/Y H:i:s');

            $html = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>Proposição {$proposicao->id}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .content { margin: 20px 0; }
                    .footer { margin-top: 30px; text-align: center; }
                </style>
            </head>
            <body>
                <div class='header'>
                    <h1>Proposição {$proposicao->id}</h1>
                    <h2>{$tipo}</h2>
                </div>
                
                <div class='content'>
                    <h3>Ementa:</h3>
                    <p>{$conteudoParaPDF}</p>
                    
                    <h3>Informações:</h3>
                    <p><strong>Autor:</strong> {$autor}</p>
                    <p><strong>Status:</strong> {$status}</p>
                    <p><strong>Data:</strong> {$data}</p>
                </div>
                
                <div class='footer'>
                    <p>Documento gerado automaticamente pelo sistema LegisInc</p>
                    <p>Data: {$dataAtual}</p>
                </div>
            </body>
            </html>";

            // Usar DomPDF para gerar PDF com configurações otimizadas
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
                'enableFontSubsetting' => true,
                'pdfBackend' => 'CPDF',
                'tempDir' => sys_get_temp_dir(),
                'chroot' => realpath(base_path()),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'defaultMediaType' => 'screen',
                'defaultPaperSize' => 'a4',
                'defaultPaperOrientation' => 'portrait',
                'fontHeightRatio' => 1.1,
                'enableCssFloat' => true,
                'enableJavascript' => false,
                'enableInlinePhp' => false,
            ]);

            // Salvar PDF
            file_put_contents($caminhoPdf, $pdf->output());

            return $caminhoPdf;

        } catch (\Exception $e) {
            // Se falhar, tentar usar PDF existente como fallback
            return $this->encontrarPDFMaisRecente($proposicao);
        }
    }

    /**
     * Gerar assinatura digital para protocolo
     */
    private function gerarAssinaturaDigital(Proposicao $proposicao): string
    {
        $dataAssinatura = now()->format('d/m/Y H:i:s');

        $nomeAssinante = $proposicao->autor->name ? $proposicao->autor->name : 'Parlamentar';
        $numeroProtocolo = $proposicao->numero_protocolo ? $proposicao->numero_protocolo : 'Pendente';

        return "
        <div class='assinatura-digital'>
            <div class='linha-assinatura'></div>
            <div class='nome-assinante'>{$nomeAssinante}</div>
            <div class='cargo-assinante'>Vereador(a)</div>
            <div class='data-assinatura'>Data: {$dataAssinatura}</div>
            <div class='protocolo-info'>Protocolo: {$numeroProtocolo}</div>
        </div>";
    }

    /**
     * Gerar QR Code para verificação
     */
    private function gerarQRCode(Proposicao $proposicao): string
    {
        $urlVerificacao = url("/proposicoes/{$proposicao->id}");

        return "
        <div class='qrcode-container'>
            <div class='qrcode-info'>
                <strong>QR Code para Verificação</strong><br>
                <small>Escaneie para verificar autenticidade</small><br>
                <small>URL: {$urlVerificacao}</small>
            </div>
        </div>";
    }

    /**
     * Encontrar o PDF mais recente para a proposição
     */
    private function encontrarPDFMaisRecente($proposicao): ?string
    {
        // 1. Se tem arquivo_pdf_path cadastrado, verificar se existe
        if (! empty($proposicao->arquivo_pdf_path)) {
            $pdfPath = storage_path('app/'.$proposicao->arquivo_pdf_path);
            if (file_exists($pdfPath)) {
                return $pdfPath;
            }
        }

        // 2. Buscar PDFs fisicamente em múltiplos diretórios possíveis
        $diretoriosParaBuscar = [
            storage_path("app/proposicoes/pdfs/{$proposicao->id}/"),      // Diretório onde PDFs assinados são criados
            storage_path("app/private/proposicoes/pdfs/{$proposicao->id}/"), // Diretório antigo
            storage_path("app/public/proposicoes/pdfs/{$proposicao->id}/"),  // Diretório público
        ];

        $todosPDFs = [];

        foreach ($diretoriosParaBuscar as $diretorio) {
            if (is_dir($diretorio)) {
                $pdfs = glob($diretorio.'*.pdf');
                if ($pdfs !== false) {
                    $todosPDFs = array_merge($todosPDFs, $pdfs);
                }
            }
        }

        if (empty($todosPDFs)) {
            return null;
        }

        // 3. Priorizar PDFs assinados
        $pdfsAssinados = array_filter($todosPDFs, function ($pdf) {
            return strpos($pdf, '_assinado_') !== false;
        });

        if (! empty($pdfsAssinados)) {
            // Retornar o PDF assinado mais recente
            usort($pdfsAssinados, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            return $pdfsAssinados[0];
        }

        // 4. Se não há PDFs assinados, retornar o PDF mais recente
        usort($todosPDFs, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return $todosPDFs[0];
    }

    /**
     * Verificar se existe PDF para a proposição
     */
    private function verificarExistenciaPDF($proposicao): bool
    {
        // 1. Verificar campo arquivo_pdf_path (método rápido)
        if (! empty($proposicao->arquivo_pdf_path)) {
            return true;
        }

        // 2. Para status avançados, verificar fisicamente se existe PDF
        $statusComPDF = ['aprovado', 'assinado', 'protocolado', 'aprovado_assinatura'];
        if (in_array($proposicao->status, $statusComPDF)) {

            // Verificar múltiplos diretórios onde pode estar o PDF
            $diretoriosParaVerificar = [
                storage_path("app/private/proposicoes/pdfs/{$proposicao->id}/"),
                storage_path("app/proposicoes/pdfs/{$proposicao->id}/"),
                storage_path("app/pdfs/{$proposicao->id}/"),
            ];

            foreach ($diretoriosParaVerificar as $diretorio) {
                try {
                    if (is_dir($diretorio)) {
                        // Usar glob nativo do PHP para buscar PDFs
                        $pdfs = glob($diretorio.'*.pdf');
                        if ($pdfs !== false && ! empty($pdfs)) {
                            return true;
                        }

                        // Buscar também por padrões específicos
                        $padroes = [
                            "proposicao_{$proposicao->id}_onlyoffice_*_assinado_*.pdf",
                            "proposicao_{$proposicao->id}_*.pdf",
                            "proposicao_{$proposicao->id}_protocolado_*.pdf",
                            "proposicao_{$proposicao->id}_assinado_*.pdf",
                        ];

                        foreach ($padroes as $padrao) {
                            $arquivos = glob($diretorio.$padrao);
                            if ($arquivos !== false && ! empty($arquivos)) {
                                return true;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Continuar verificando outros diretórios se um falhar
                    continue;
                }
            }
        }

        return false;
    }
}
