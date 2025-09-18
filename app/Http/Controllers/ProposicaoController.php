<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTemplate;
use App\Models\Proposicao;
use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\DocumentConversionService;
use App\Services\OnlyOffice\OnlyOfficeConversionService;
use App\Services\Template\TemplateInstanceService;
use App\Services\Template\TemplateParametrosService;
use App\Services\Template\TemplateProcessorService;
use App\Services\Template\TemplateUniversalService;
use App\Services\TemplateVariablesService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProposicaoController extends Controller
{
    public function __construct(
        private TemplateUniversalService $templateUniversalService,
        private OnlyOfficeConversionService $conversionService
    ) {
        $this->middleware('auth');
        $this->middleware('can:create,App\Models\Proposicao')->only(['create', 'store', 'createModern']);
        $this->middleware('can:update,proposicao')->only(['update', 'edit']);
        $this->middleware('can:delete,proposicao')->only(['destroy']);
        $this->middleware('can:view,proposicao')->only(['show']);
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
        // 📝 LOG: Início da criação de proposição pelo Parlamentar
        \App\Helpers\ComprehensiveLogger::userClick('Parlamentar iniciou criação de nova proposição', [
            'acao' => 'criar_proposicao',
            'tipo_solicitado' => $request->tipo,
            'ementa' => $request->ementa,
            'opcao_preenchimento' => $request->opcao_preenchimento ?? 'modelo',
            'usar_ia' => $request->usar_ia,
            'has_anexos' => $request->hasFile('anexos'),
            'total_anexos' => $request->hasFile('anexos') ? count($request->file('anexos')) : 0
        ]);

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

        // 📝 LOG: Proposição criada com sucesso
        \App\Helpers\ComprehensiveLogger::userClick('Proposição criada com sucesso pelo Parlamentar', [
            'acao' => 'proposicao_criada',
            'proposicao_id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'ementa' => $proposicao->ementa,
            'status_inicial' => $proposicao->status,
            'ano' => $proposicao->ano,
            'opcao_preenchimento' => $opcaoPreenchimento,
            'tem_conteudo_inicial' => !empty($proposicao->conteudo),
            'tamanho_conteudo' => strlen($proposicao->conteudo ?? ''),
            'workflow_stage' => 'criacao_completa'
        ]);

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

            // 📎 LOG: Anexos processados
            \App\Helpers\ComprehensiveLogger::userClick('Anexos adicionados à proposição', [
                'acao' => 'anexos_adicionados',
                'proposicao_id' => $proposicao->id,
                'total_anexos' => $totalAnexos,
                'anexos_info' => array_map(function($anexo) {
                    return [
                        'nome' => $anexo['nome_original'],
                        'tipo' => $anexo['tipo'],
                        'tamanho_mb' => round($anexo['tamanho'] / 1024 / 1024, 2)
                    ];
                }, $anexosData),
                'workflow_stage' => 'anexos_processados'
            ]);
        }

        // 🎉 LOG: Rascunho finalizado com sucesso
        \App\Helpers\ComprehensiveLogger::userClick('Rascunho de proposição finalizado', [
            'acao' => 'rascunho_finalizado',
            'proposicao_id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'status' => $proposicao->status,
            'tem_anexos' => ($proposicao->total_anexos ?? 0) > 0,
            'total_anexos' => $proposicao->total_anexos ?? 0,
            'workflow_stage' => 'rascunho_completo',
            'proxima_etapa' => 'edicao_conteudo'
        ]);

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
        try {
            // Verificar se o tipo existe
            $tipoProposicao = TipoProposicao::buscarPorCodigo($tipo);

            if (! $tipoProposicao || ! $tipoProposicao->ativo) {
                return response()->json([], 404);
            }

            $modelosArray = [];

            // SEMPRE usar apenas template universal - removendo templates específicos
            $templateUniversal = \App\Models\TemplateUniversal::where('ativo', true)->first();
            
            if ($templateUniversal) {
                $modelosArray[] = [
                    'id' => 'template_universal_' . $templateUniversal->id,
                    'nome' => $tipoProposicao->nome . ' - Template Universal',
                    'descricao' => 'Template universal configurado para ' . $tipoProposicao->nome,
                    'is_template' => true,
                    'template_id' => 'universal_' . $templateUniversal->id,
                    'document_key' => $templateUniversal->document_key ?? null,
                    'arquivo_path' => $templateUniversal->arquivo_path ?? null,
                    'is_universal' => true,
                ];
            } else {
                // Se não houver template universal, criar opção básica
                $modelosArray[] = [
                    'id' => 'template_basic',
                    'nome' => $tipoProposicao->nome . ' - Documento Básico',
                    'descricao' => 'Criar ' . $tipoProposicao->nome . ' com formatação básica',
                    'is_template' => false,
                    'template_id' => 'basic',
                    'is_universal' => false,
                ];
            }

            return response()->json($modelosArray);

        } catch (\Exception $e) {
            // Fallback em caso de erro de conexão
            return response()->json([
                [
                    'id' => 'template_fallback',
                    'nome' => 'Documento Básico - ' . ucfirst($tipo),
                    'descricao' => 'Template de fallback devido a problemas de conectividade',
                    'is_template' => false,
                    'template_id' => 'basic',
                    'is_universal' => false,
                    'error' => 'Conexão com banco indisponível'
                ]
            ]);
        }
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
        // 📤 LOG: Parlamentar iniciou envio ao Legislativo
        \App\Helpers\ComprehensiveLogger::userClick('Parlamentar solicitou envio de proposição ao Legislativo', [
            'acao' => 'solicitar_envio_legislativo',
            'proposicao_id' => $proposicao->id,
            'proposicao_tipo' => $proposicao->tipo,
            'proposicao_status_atual' => $proposicao->status,
            'ementa_presente' => !empty($proposicao->ementa),
            'conteudo_presente' => !empty($proposicao->conteudo),
            'arquivo_presente' => !empty($proposicao->arquivo_path),
            'tem_anexos' => ($proposicao->total_anexos ?? 0) > 0,
            'total_anexos' => $proposicao->total_anexos ?? 0,
            'is_author' => $proposicao->autor_id === auth()->id(),
            'workflow_stage' => 'solicitacao_envio'
        ]);

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

            // Capturar status anterior antes da atualização
            $statusAnterior = $proposicao->status;

            // Atualizar status para enviado ao legislativo
            $proposicao->update([
                'status' => 'enviado_legislativo',
            ]);

            // ✅ LOG: Proposição enviada com sucesso ao Legislativo
            \App\Helpers\ComprehensiveLogger::userClick('Proposição enviada com sucesso ao Legislativo', [
                'acao' => 'proposicao_enviada_legislativo',
                'proposicao_id' => $proposicao->id,
                'proposicao_tipo' => $proposicao->tipo,
                'ementa' => $proposicao->ementa,
                'status_anterior' => $statusAnterior,
                'status_atual' => $proposicao->status,
                'conteudo_size' => strlen($proposicao->conteudo ?? ''),
                'arquivo_path' => $proposicao->arquivo_path,
                'tem_anexos' => ($proposicao->total_anexos ?? 0) > 0,
                'workflow_stage' => 'enviado_para_analise',
                'proxima_etapa' => 'analise_legislativa',
                'responsavel_proximo' => 'Legislativo'
            ]);

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
            // ❌ LOG: Erro ao enviar proposição ao Legislativo
            \App\Helpers\ComprehensiveLogger::userClick('Erro ao enviar proposição ao Legislativo', [
                'acao' => 'erro_envio_legislativo',
                'proposicao_id' => $proposicao->id,
                'proposicao_tipo' => $proposicao->tipo,
                'proposicao_status' => $proposicao->status,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'workflow_stage' => 'erro_envio'
            ]);

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

        // Fallback 2: Tentar usar Template Universal
        $templateUniversal = $this->buscarTemplateUniversal();
        if ($templateUniversal) {
            \Log::info('Usando Template Universal como fallback', [
                'template_id' => $template->id,
                'template_universal_id' => $templateUniversal->id ?? 'sem_id'
            ]);

            // Salvar template universal como novo template
            if ($template->arquivo_path) {
                \Storage::disk('local')->put($template->arquivo_path, $templateUniversal);
            }

            return $templateUniversal;
        }

        // Fallback 3: Erro - Nenhum template disponível
        $mensagemErro = "❌ ERRO: Nenhum template válido encontrado para a proposição ID {$template->id}";
        \Log::error($mensagemErro, [
            'template_id' => $template->id,
            'arquivo_path' => $template->arquivo_path,
            'tipo_proposicao' => $template->tipoProposicao->nome ?? 'indefinido'
        ]);

        throw new \Exception($mensagemErro);
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
     * Buscar Template Universal do banco de dados
     */
    private function buscarTemplateUniversal()
    {
        try {
            $templateUniversal = \App\Models\TemplateUniversal::first();
            if ($templateUniversal && $templateUniversal->conteudo) {
                \Log::info('Template Universal encontrado', [
                    'template_id' => $templateUniversal->id,
                    'nome' => $templateUniversal->nome,
                    'tamanho_conteudo' => strlen($templateUniversal->conteudo)
                ]);
                return $templateUniversal->conteudo;
            }

            \Log::warning('Template Universal não encontrado ou sem conteúdo');
            return null;
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar Template Universal', [
                'erro' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine()
            ]);
            return null;
        }
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
            $length = mb_strlen($variavel, 'UTF-8');
            
            for ($i = 0; $i < $length; $i++) {
                $char = mb_substr($variavel, $i, 1, 'UTF-8');
                $codepoint = mb_ord($char, 'UTF-8');
                $sequencia .= '\\u'.$codepoint.'*';
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
            
            // IMPORTANTE: Invalidar PDF antigo para forçar regeneração com últimas alterações
            $proposicao->arquivo_pdf_path = null;
            $proposicao->pdf_gerado_em = null;
            
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

            // NOVA IMPLEMENTAÇÃO: Force Save + Conversão OnlyOffice → PDF
            Log::info('🚀 APROVAÇÃO LEGISLATIVO: Iniciando conversão OnlyOffice → PDF', [
                'proposicao_id' => $proposicao->id,
                'rtf_path' => $proposicao->arquivo_path,
                'onlyoffice_key' => $proposicao->onlyoffice_key,
                'user_id' => auth()->id()
            ]);

            // Verificar se proposição tem dados OnlyOffice válidos
            if (empty($proposicao->onlyoffice_key) || empty($proposicao->arquivo_path)) {
                Log::warning('⚠️ APROVAÇÃO: Proposição sem dados OnlyOffice, usando método tradicional', [
                    'proposicao_id' => $proposicao->id,
                    'onlyoffice_key' => $proposicao->onlyoffice_key,
                    'arquivo_path' => $proposicao->arquivo_path
                ]);

                // Fallback: Invalidar PDF para forçar regeneração tradicional
                $proposicao->update([
                    'status' => 'aprovado_assinatura',
                    'data_aprovacao_autor' => now(),
                    'arquivo_pdf_path' => null,
                    'pdf_gerado_em' => null,
                    'pdf_conversor_usado' => null,
                ]);
            } else {
                // NOVO FLUXO: Force Save + Conversão OnlyOffice
                $conversionResult = $this->conversionService->forceConvertToPdf($proposicao);

                if ($conversionResult['success']) {
                    Log::info('✅ APROVAÇÃO: Conversão OnlyOffice → PDF concluída', [
                        'proposicao_id' => $proposicao->id,
                        'pdf_path' => $conversionResult['pdf_path'],
                        'pdf_size' => $conversionResult['pdf_size']
                    ]);

                    // Atualizar status (PDF já foi atualizado no service)
                    $proposicao->update([
                        'status' => 'aprovado_assinatura',
                        'data_aprovacao_autor' => now(),
                    ]);
                } else {
                    Log::error('❌ APROVAÇÃO: Erro na conversão OnlyOffice → PDF', [
                        'proposicao_id' => $proposicao->id,
                        'error' => $conversionResult['error']
                    ]);

                    // Fallback: Atualizar status e invalidar PDF para regeneração tradicional
                    $proposicao->update([
                        'status' => 'aprovado_assinatura',
                        'data_aprovacao_autor' => now(),
                        'arquivo_pdf_path' => null,
                        'pdf_gerado_em' => null,
                        'pdf_conversor_usado' => null,
                    ]);
                }
            }

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

        // ETAPA 0: Decodificar sequências Unicode RTF primeiro (\u79* = 'O', etc)
        $texto = $this->decodificarUnicodeRTF($rtfContent);

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
    public function viewPDFWithDebug(Proposicao $proposicao)
    {
        // Verificar permissões (mesmas que servePDF)
        $user = Auth::user();
        if (! $user->isLegislativo() && $proposicao->autor_id !== $user->id && ! $user->isAssessorJuridico() && ! $user->isProtocolo()) {
            abort(403, 'Acesso negado.');
        }

        return view('proposicoes.pdf-viewer', [
            'proposicao' => $proposicao
        ]);
    }

    public function debugPDF(Proposicao $proposicao)
    {
        // Verificar permissões (mesmas que servePDF)
        $user = Auth::user();
        if (! $user->isLegislativo() && $proposicao->autor_id !== $user->id && ! $user->isAssessorJuridico() && ! $user->isProtocolo()) {
            abort(403, 'Acesso negado.');
        }

        // Coletar informações de debug sobre o PDF
        $debugInfo = [
            'proposicao_id' => $proposicao->id,
            'status' => $proposicao->status,
            'autor_id' => $proposicao->autor_id,
            'arquivo_path' => $proposicao->arquivo_path,
            'arquivo_pdf_path' => $proposicao->arquivo_pdf_path,
            'pdf_gerado_em' => $proposicao->pdf_gerado_em,
            'has_arquivo' => $proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path),
            'has_pdf' => $proposicao->arquivo_pdf_path && Storage::exists($proposicao->arquivo_pdf_path),
            'pdf_oficial_path' => $this->caminhoPdfOficial($proposicao),
            'storage_files' => []
        ];

        // Verificar arquivos no storage relacionados à proposição
        $proposicaoDir = "proposicoes/{$proposicao->id}";
        if (Storage::exists($proposicaoDir)) {
            $debugInfo['storage_files'] = Storage::files($proposicaoDir);
        }

        return view('proposicoes.debug-pdf', [
            'proposicao' => $proposicao,
            'debugInfo' => $debugInfo
        ]);
    }

    public function servePDF(Proposicao $proposicao)
    {
        // Log de início de requisição PDF
        Log::info('🔴 PDF REQUEST: Iniciando servePDF - PRIORIDADE ONLYOFFICE', [
            'proposicao_id' => $proposicao->id,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $proposicao->status,
            'timestamp' => now()->toISOString()
        ]);

        // Verificar se o usuário tem permissão para ver este PDF
        $user = Auth::user();

        // Permitir acesso para:
        // 1. Autor da proposição (parlamentar) - especialmente para status 'protocolado'
        // 2. Usuários do legislativo
        // 3. Usuários com perfil jurídico
        // 4. Usuários do protocolo
        if (! $user->isLegislativo() && $proposicao->autor_id !== $user->id && ! $user->isAssessorJuridico() && ! $user->isProtocolo()) {
            Log::warning('🔴 PDF REQUEST: Acesso negado por permissões', [
                'proposicao_id' => $proposicao->id,
                'user_id' => Auth::id(),
                'user_roles' => Auth::user()->roles->pluck('name'),
                'proposicao_autor_id' => $proposicao->autor_id
            ]);
            abort(403, 'Acesso negado.');
        }

        // Para parlamentares, permitir apenas em status específicos onde o PDF já está disponível
        if ($user->isParlamentar() && $proposicao->autor_id === $user->id) {
            $statusPermitidos = ['protocolado', 'aprovado', 'assinado', 'enviado_protocolo', 'retornado_legislativo', 'aprovado_assinatura'];
            if (! in_array($proposicao->status, $statusPermitidos)) {
                abort(403, 'PDF não disponível para download neste status.');
            }
        }

        // NOVA ESTRATÉGIA: 1) PDF OnlyOffice Conversion API 2) PDF oficial 3) DomPDF fallback
        Log::info('🔴 PDF REQUEST: Verificando PDF gerado via OnlyOffice Conversion API', [
            'proposicao_id' => $proposicao->id,
            'arquivo_pdf_path' => $proposicao->arquivo_pdf_path,
            'pdf_conversor_usado' => $proposicao->pdf_conversor_usado,
            'pdf_gerado_em' => $proposicao->pdf_gerado_em
        ]);

        // 1) MÁXIMA PRIORIDADE: PDF gerado via OnlyOffice Conversion API
        if ($proposicao->arquivo_pdf_path &&
            $proposicao->pdf_conversor_usado === 'onlyoffice_conversion_api' &&
            Storage::exists($proposicao->arquivo_pdf_path)) {

            $caminhoAbsoluto = Storage::path($proposicao->arquivo_pdf_path);

            Log::info('✅ PDF REQUEST: Servindo PDF OnlyOffice Conversion API', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $proposicao->arquivo_pdf_path,
                'tamanho' => filesize($caminhoAbsoluto),
                'conversor' => $proposicao->pdf_conversor_usado,
                'gerado_em' => $proposicao->pdf_gerado_em
            ]);

            return response()->file($caminhoAbsoluto, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="proposicao_' . $proposicao->id . '_onlyoffice.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '-1',
                'X-PDF-Source' => 'onlyoffice-conversion-api',
                'X-PDF-Generated' => $proposicao->pdf_gerado_em?->toISOString() ?? 'unknown'
            ]);
        }

        // 2) SEGUNDA PRIORIDADE: PDF oficial OnlyOffice (método antigo)
        Log::info('🔴 PDF REQUEST: PDF Conversion API não encontrado, tentando PDF oficial', [
            'proposicao_id' => $proposicao->id
        ]);

        $pdfOficial = $this->encontrarPDFMaisRecenteRobusta($proposicao);

        Log::info('🔴 PDF REQUEST: Resultado da busca por PDF oficial', [
            'proposicao_id' => $proposicao->id,
            'pdf_oficial_encontrado' => $pdfOficial ?: 'NENHUM',
            'pdf_existe' => $pdfOficial ? file_exists(storage_path('app/' . $pdfOficial)) : false
        ]);

        if ($pdfOficial && file_exists(storage_path('app/' . $pdfOficial))) {
            $caminhoAbsoluto = storage_path('app/' . $pdfOficial);

            // Verificar se RTF foi modificado após PDF (forçar regeneração se necessário)
            $pdfModificado = filemtime($caminhoAbsoluto);
            $rtfModificado = null;

            if ($proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
                $caminhoRTF = Storage::path($proposicao->arquivo_path);
                if (file_exists($caminhoRTF)) {
                    $rtfModificado = filemtime($caminhoRTF);
                }
            }

            // Se RTF é mais novo, invalidar PDF e regenerar com DomPDF seguro
            if (!$rtfModificado || $pdfModificado >= $rtfModificado) {
                Log::info('🟢 PDF REQUEST: Servindo PDF existente (OnlyOffice)', [
                    'proposicao_id' => $proposicao->id,
                    'pdf_path' => $pdfOficial,
                    'tamanho' => filesize($caminhoAbsoluto)
                ]);

                return response()->file($caminhoAbsoluto, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="proposicao_' . $proposicao->id . '_oficial.pdf"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '-1',
                    'X-PDF-Source' => 'onlyoffice-oficial'
                ]);
            } else {
                Log::warning('🔴 PDF REQUEST: RTF mais novo que PDF, invalidando para regeneração', [
                    'proposicao_id' => $proposicao->id,
                    'rtf_modificado' => date('Y-m-d H:i:s', $rtfModificado),
                    'pdf_modificado' => date('Y-m-d H:i:s', $pdfModificado)
                ]);

                // Invalidar PDF antigo
                $proposicao->update([
                    'arquivo_pdf_path' => null,
                    'pdf_gerado_em' => null,
                    'pdf_conversor_usado' => null,
                ]);
            }
        }

        // 2) FALLBACK: DomPDF com configurações seguras (subsetting OFF)
        Log::warning('🔴 PDF REQUEST: PDF oficial OnlyOffice não encontrado, usando fallback DomPDF', [
            'proposicao_id' => $proposicao->id
        ]);

        try {
            // CORREÇÃO CRÍTICA: Extrair conteúdo atualizado do RTF editado pelo Legislativo
            $conteudo = null;

            // Se existe arquivo RTF salvo pelo Legislativo, extrair conteúdo dele
            if ($proposicao->arquivo_path) {
                // CORREÇÃO: Usar ordem de prioridade conforme documentação técnica
                $possiveisCaminhos = [
                    storage_path('app/' . $proposicao->arquivo_path),           // PRIORIDADE 1: Onde callbacks salvam
                    storage_path('app/private/' . $proposicao->arquivo_path),   // PRIORIDADE 2: Legacy
                    storage_path('app/local/' . $proposicao->arquivo_path),     // PRIORIDADE 3: Fallback
                ];

                $caminhoRTF = null;
                foreach ($possiveisCaminhos as $caminho) {
                    if (file_exists($caminho)) {
                        $caminhoRTF = $caminho;
                        break;
                    }
                }

                // Se não encontrou pelo arquivo_path, buscar o arquivo mais recente na pasta
                if (!$caminhoRTF) {
                    Log::info('PDF REQUEST: arquivo_path não encontrado, buscando arquivo mais recente', [
                        'proposicao_id' => $proposicao->id,
                        'arquivo_path' => $proposicao->arquivo_path
                    ]);

                    $caminhoRTF = $this->encontrarRTFMaisRecenteRobusta($proposicao);
                }

                if ($caminhoRTF) {
                    Log::info('🔴 PDF REQUEST: Extraindo conteúdo atualizado do RTF para gerar PDF', [
                        'proposicao_id' => $proposicao->id,
                        'rtf_path_banco' => $proposicao->arquivo_path,
                        'rtf_path_encontrado' => $caminhoRTF,
                        'rtf_size' => filesize($caminhoRTF)
                    ]);

                    // Ler o conteúdo RTF do arquivo
                    $rtfContent = file_get_contents($caminhoRTF);

                    // NOVA ESTRATÉGIA: Extração mais agressiva e eficaz
                    $conteudo = $this->extrairTextoRTFMelhorado($rtfContent);

                    Log::info('🔴 PDF REQUEST: Resultado da extração melhorada', [
                        'proposicao_id' => $proposicao->id,
                        'conteudo_length' => strlen($conteudo),
                        'conteudo_preview' => substr($conteudo, 0, 300),
                        'conteudo_empty' => empty($conteudo)
                    ]);

                    // Se ainda está vazio, tentar métodos alternativos
                    if (empty($conteudo) || strlen(trim($conteudo)) < 50) {
                        Log::warning('🔴 PDF REQUEST: Extração melhorada retornou pouco, tentando método inteligente', [
                            'proposicao_id' => $proposicao->id
                        ]);

                        $conteudo = $this->extrairTextoRTFInteligente($rtfContent);

                        if (empty($conteudo) || strlen(trim($conteudo)) < 10) {
                            Log::warning('🔴 PDF REQUEST: Extração inteligente também falhou, tentando método simples', [
                                'proposicao_id' => $proposicao->id
                            ]);

                            $conteudo = $this->extrairTextoRTFSimples($rtfContent);
                        }

                        Log::info('🔴 PDF REQUEST: Resultado final da extração', [
                            'proposicao_id' => $proposicao->id,
                            'conteudo_length' => strlen($conteudo),
                            'conteudo_preview' => substr($conteudo, 0, 200),
                            'conteudo_empty' => empty($conteudo)
                        ]);
                    }
                } else {
                    Log::warning('🔴 PDF REQUEST: Arquivo RTF não encontrado em nenhum caminho', [
                        'proposicao_id' => $proposicao->id,
                        'rtf_path_banco' => $proposicao->arquivo_path,
                        'caminhos_testados' => $possiveisCaminhos
                    ]);
                }
            }

            // Se ainda não tem conteúdo, usar fallback do banco de dados
            if (empty($conteudo)) {
                Log::warning('🔴 PDF REQUEST: Usando conteúdo do banco como fallback', [
                    'proposicao_id' => $proposicao->id,
                    'banco_conteudo_length' => strlen($proposicao->conteudo ?: ''),
                    'banco_ementa_length' => strlen($proposicao->ementa ?: '')
                ]);
                $conteudo = $proposicao->conteudo ?: $proposicao->ementa ?: 'Conteúdo da proposição não disponível.';
            }

            // Limpar cache antes de gerar PDF para forçar conteúdo atualizado
            Cache::forget('proposicao_pdf_' . $proposicao->id);
            Cache::forget('proposicao_conteudo_' . $proposicao->id);

            Log::info('🔴 PDF REQUEST: Gerando HTML para PDF com conteúdo extraído', [
                'proposicao_id' => $proposicao->id,
                'conteudo_final_length' => strlen($conteudo),
                'conteudo_preview' => substr($conteudo, 0, 200)
            ]);

            $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);

            // Sanear encoding RTF→UTF-8 para evitar problemas de codificação
            $html = preg_replace("/\x00/", '', $html); // Remove null bytes
            if (!mb_detect_encoding($html, 'UTF-8', true)) {
                $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            }
            $html = iconv('UTF-8', 'UTF-8//IGNORE', $html); // Remove caracteres inválidos

            // CRÍTICO: Forçar família de fontes segura para evitar mapeamento errado de glifos
            $fonteSegura = "<style>*{font-family:'DejaVu Sans',Arial,sans-serif!important;}</style>";
            $html = $fonteSegura . $html;

            Log::info('🔴 PDF REQUEST: Gerando PDF com DomPDF (configuração segura)', [
                'proposicao_id' => $proposicao->id,
                'html_length' => strlen($html)
            ]);

            // DomPDF com configurações que eliminam o problema "C C C..."
            $pdf = Pdf::setOptions([
                'isRemoteEnabled'        => true,
                'isHtml5ParserEnabled'   => true,
                'enable_font_subsetting' => false,   // <- CHAVE: elimina "C C C..."
                'defaultFont'            => 'DejaVu Sans',
                'dpi'                    => 96,
                'fontCache'              => storage_path('fonts'), // usar cache limpo
                'tempDir'                => sys_get_temp_dir(),
            ])->loadHTML($html)->setPaper('a4', 'portrait');

            Log::info('🟡 PDF REQUEST: DomPDF configurado com subsetting OFF', [
                'proposicao_id' => $proposicao->id
            ]);

            return $pdf->stream("proposicao_{$proposicao->id}.pdf");

        } catch (\Exception $e) {
            Log::error('🔴 PDF REQUEST: Erro ao gerar PDF', [
                'proposicao_id' => $proposicao->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Erro interno ao gerar PDF da proposição.');
        }
    }
    
    /**
     * Método auxiliar para encontrar PDF mais recente para servir
     */
    private function encontrarPDFMaisRecenteParaServir(Proposicao $proposicao): ?string
    {
        // Prioridade 1: arquivo_pdf_path
        if ($proposicao->arquivo_pdf_path) {
            $caminho = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($caminho)) {
                return $proposicao->arquivo_pdf_path;
            }
        }
        
        // Prioridade 2: Diretório de PDFs
        $diretorioPDFs = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFs)) {
            $pdfs = glob($diretorioPDFs . '/*.pdf');
            if (!empty($pdfs)) {
                // Retornar o mais recente
                $pdfMaisRecente = array_reduce($pdfs, function($carry, $item) {
                    return (!$carry || filemtime($item) > filemtime($carry)) ? $item : $carry;
                });
                return str_replace(storage_path('app/'), '', $pdfMaisRecente);
            }
        }
        
        // Prioridade 3: Diretório privado
        $diretorioPrivado = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPrivado)) {
            $pdfs = glob($diretorioPrivado . '/*.pdf');
            if (!empty($pdfs)) {
                $pdfMaisRecente = array_reduce($pdfs, function($carry, $item) {
                    return (!$carry || filemtime($item) > filemtime($carry)) ? $item : $carry;
                });
                return str_replace(storage_path('app/'), '', $pdfMaisRecente);
            }
        }
        
        return null;
    }
    
    /**
     * Converter arquivo para PDF usando estratégia unificada
     */
    private function converterArquivoParaPDFUnificado(string $caminhoOrigem, string $caminhoDestino): bool
    {
        try {
            $diretorioDestino = dirname($caminhoDestino);
            
            // 🎯 SOLUÇÃO DEFINITIVA: SEMPRE APLICAR TEMPLATE UNIVERSAL ANTES DA CONVERSÃO
            $caminhoComTemplate = $this->garantirTemplateUniversal($caminhoOrigem);
            if ($caminhoComTemplate !== $caminhoOrigem) {
                Log::info('🔴 PDF REQUEST: Template universal aplicado', [
                    'original' => $caminhoOrigem,
                    'com_template' => $caminhoComTemplate
                ]);
                $caminhoOrigem = $caminhoComTemplate; // Usar RTF com template
            }
            
            // Comando LibreOffice para conversão
            $comando = sprintf(
                'libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg($diretorioDestino),
                escapeshellarg($caminhoOrigem)
            );
            
            Log::info('🔴 PDF REQUEST: Executando conversão LibreOffice', [
                'comando' => $comando
            ]);
            
            exec($comando, $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::error('🔴 PDF REQUEST: Erro na conversão LibreOffice', [
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                return false;
            }
            
            // LibreOffice gera o PDF com o mesmo nome base do arquivo origem
            $nomeBasePdf = pathinfo($caminhoOrigem, PATHINFO_FILENAME) . '.pdf';
            $pdfGerado = $diretorioDestino . '/' . $nomeBasePdf;
            
            // Mover para o nome final desejado
            if (file_exists($pdfGerado) && $pdfGerado !== $caminhoDestino) {
                rename($pdfGerado, $caminhoDestino);
            }
            
            return file_exists($caminhoDestino);
            
        } catch (\Exception $e) {
            Log::error('🔴 PDF REQUEST: Exceção na conversão', [
                'erro' => $e->getMessage()
            ]);
            return false;
        }
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
            if (!empty($proposicao->conteudo)) {
                $conteudo = $proposicao->conteudo;
            } else {
                $conteudo = $proposicao->ementa ?: 'Conteúdo não disponível';
            }
            
            // Criar HTML para DomPDF como fallback
            $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);
            
            // Sanear encoding RTF→UTF-8
            $html = preg_replace("/\x00/", '', $html);
            if (!mb_detect_encoding($html, 'UTF-8', true)) {
                $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            }
            $html = iconv('UTF-8', 'UTF-8//IGNORE', $html);

            // CRÍTICO: Forçar fonte segura
            $fonteSegura = "<style>*{font-family:'DejaVu Sans',Arial,sans-serif!important}</style>";
            $html = $fonteSegura . $html;

            // DomPDF com configurações anti-subsetting
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions([
                'isRemoteEnabled'        => true,
                'isHtml5ParserEnabled'   => true,
                'enable_font_subsetting' => false,   // <- CHAVE: elimina "C C C..."
                'defaultFont'            => 'DejaVu Sans',
                'dpi'                    => 96,
                'fontCache'              => storage_path('fonts'),
                'tempDir'                => sys_get_temp_dir(),
            ])->loadHTML($html)->setPaper('A4', 'portrait');
            
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
        if (!str_contains($rtfContent, '{\rtf')) {
            return $rtfContent;
        }

        // CORREÇÃO: Processar sequências Unicode RTF (\u123*) antes de extrair texto
        // Converter \u123* e \u-123* em caracteres reais (incluindo valores negativos)
        $rtfContent = preg_replace_callback('/\\\\u(-?\d+)\*/', function($matches) {
            $unicode = intval($matches[1]);
            // Para valores negativos RTF, converter para valor positivo equivalente
            if ($unicode < 0) {
                $unicode = 65536 + $unicode;
            }
            if ($unicode > 0 && $unicode < 65536) {
                return mb_chr($unicode, 'UTF-8');
            }
            return '';
        }, $rtfContent);

        // Para RTF muito complexo como do OnlyOffice, vamos usar uma abordagem mais simples:
        // Buscar por texto real entre códigos RTF usando padrões específicos

        $textosEncontrados = [];
        
        // 1. Buscar texto em português comum (frases)
        preg_match_all('/(?:[A-ZÁÉÍÓÚÂÊÎÔÛÃÕÀÈÌÒÙÇ][a-záéíóúâêîôûãõàèìòùç\s,.-]{15,})/u', $rtfContent, $matches);
        if (!empty($matches[0])) {
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
                if (!empty($matches[0])) {
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
            if (!is_dir($diretorioDestino)) {
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
            $pdfGerado = $diretorioDestino . '/' . $nomeArquivoSemExtensao . '.pdf';
            
            // Mover para o nome final desejado se diferente
            if (file_exists($pdfGerado) && $pdfGerado !== $caminhoPdfDestino) {
                rename($pdfGerado, $caminhoPdfDestino);
            }

            return file_exists($caminhoPdfDestino);

        } catch (\Exception $e) {
            Log::error('Erro na conversão direta para PDF', [
                'erro' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar se a proposição tem PDF disponível
     */
    private function verificarExistenciaPDF($proposicao): bool
    {
        // REGRA CRÍTICA: PDF só deve aparecer após aprovação pelo Legislativo
        // Status onde PDF é disponibilizado:
        $statusComPDF = [
            'aprovado',           // Aprovado pelo Legislativo - PDF gerado para assinatura
            'aprovado_assinatura',// Aprovado para assinatura
            'assinado',           // Assinado digitalmente
            'enviado_protocolo',  // Enviado para protocolo
            'protocolado'         // Protocolado oficialmente
        ];
        
        // Se não está em status que deveria ter PDF, não mostrar
        if (!in_array($proposicao->status, $statusComPDF)) {
            return false;
        }
        
        // Verificar arquivo_pdf_path
        if ($proposicao->arquivo_pdf_path && Storage::exists($proposicao->arquivo_pdf_path)) {
            return true;
        }

        // Verificar diretório de PDFs
        $diretorioPDFs = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFs)) {
            $pdfs = glob($diretorioPDFs . '/*.pdf');
            if (!empty($pdfs)) {
                return true;
            }
        }

        // Verificar diretório privado
        $diretorioPrivado = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPrivado)) {
            $pdfs = glob($diretorioPrivado . '/*.pdf');
            if (!empty($pdfs)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Limpar código LaTeX do conteúdo
     */
    private function limparCodigoLatex($conteudo): string
    {
        if (empty($conteudo)) {
            return $conteudo;
        }

        // Remover comandos LaTeX comuns
        $patterns = [
            '/\\\\[a-zA-Z]+\{[^}]*\}/',  // Comandos com chaves: \comando{conteudo}
            '/\\\\[a-zA-Z]+/',           // Comandos simples: \comando
            '/\{\\\\[^}]*\}/',           // Chaves com comandos: {\comando}
            '/\\\\\\\\/',                // Quebras de linha LaTeX: \\
            '/\$[^$]*\$/',               // Expressões matemáticas: $...$
            '/\$\$[^$]*\$\$/',           // Expressões matemáticas em bloco: $$...$$
        ];

        $conteudoLimpo = $conteudo;
        foreach ($patterns as $pattern) {
            $conteudoLimpo = preg_replace($pattern, '', $conteudoLimpo);
        }

        // Limpar espaços extras
        $conteudoLimpo = preg_replace('/\s+/', ' ', $conteudoLimpo);
        $conteudoLimpo = trim($conteudoLimpo);

        return $conteudoLimpo;
    }

    /**
     * Atualizar status da proposição
     */
    public function updateStatus(Request $request, Proposicao $proposicao)
    {
        try {
            // 🎯 LOG: Início da interação do usuário - clique no botão de aprovação/mudança de status
            $user = Auth::user();
            \App\Helpers\ComprehensiveLogger::userClick('Usuário clicou para alterar status da proposição', [
                'timestamp' => now()->toISOString(),
                'proposicao_id' => $proposicao->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_roles' => $user->roles->pluck('name')->toArray(),
                'request_data' => $request->all(),
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'session_id' => $request->session()->getId(),
                'current_status' => $proposicao->status,
                'requested_status' => $request->input('status'),
                'arquivo_atual' => $proposicao->arquivo_path,
                'pdf_atual' => $proposicao->arquivo_pdf_path,
                'pdf_gerado_em' => $proposicao->pdf_gerado_em,
                'onlyoffice_interactions' => [
                    'document_key' => $proposicao->onlyoffice_document_key ?? 'N/A',
                    'last_modified' => $proposicao->updated_at,
                    'has_rtf_file' => !empty($proposicao->arquivo_path) && Storage::exists($proposicao->arquivo_path),
                    'rtf_size' => !empty($proposicao->arquivo_path) && Storage::exists($proposicao->arquivo_path)
                        ? Storage::size($proposicao->arquivo_path) : 0,
                    'rtf_last_modified' => !empty($proposicao->arquivo_path) && Storage::exists($proposicao->arquivo_path)
                        ? Storage::lastModified($proposicao->arquivo_path) : null
                ]
            ]);

            $request->validate([
                'status' => 'required|string|in:rascunho,em_edicao,enviado_legislativo,aprovado,retornado_parlamentar,enviado_protocolo,protocolado,assinado'
            ]);

            $statusAnterior = $proposicao->status;
            $novoStatus = $request->status;
            
            // Lógica de permissões por perfil
            if ($user->isParlamentar()) {
                // Parlamentar pode apenas criar/editar suas proposições
                if ($proposicao->autor_id !== $user->id) {
                    return response()->json(['error' => 'Você só pode alterar suas próprias proposições.'], 403);
                }
                // Parlamentar só pode usar status: rascunho, em_edicao, enviado_legislativo
                $statusPermitidos = ['rascunho', 'em_edicao', 'enviado_legislativo'];
                if (!in_array($novoStatus, $statusPermitidos)) {
                    return response()->json(['error' => 'Status não permitido para parlamentares.'], 403);
                }
            } elseif ($user->isLegislativo()) {
                // Legislativo pode aprovar/retornar proposições
                $statusPermitidos = ['aprovado', 'retornado_parlamentar'];
                if (!in_array($novoStatus, $statusPermitidos)) {
                    return response()->json(['error' => 'Status não permitido para o legislativo.'], 403);
                }
            } elseif ($user->isProtocolo()) {
                // Protocolo pode protocolar proposições aprovadas
                if ($novoStatus === 'protocolado' && $statusAnterior !== 'enviado_protocolo') {
                    return response()->json(['error' => 'Só é possível protocolar proposições enviadas para o protocolo.'], 403);
                }
            }

            // Preparar dados para atualização
            $updateData = [
                'status' => $novoStatus,
                'updated_at' => now()
            ];

            // CRÍTICO: Se mudando para "aprovado", invalidar PDF para forçar regeneração com conteúdo do Legislativo
            if ($novoStatus === 'aprovado') {
                $updateData['arquivo_pdf_path'] = null;
                $updateData['pdf_gerado_em'] = null;
                $updateData['pdf_conversor_usado'] = null;

                Log::info('🔥 INVALIDAÇÃO PDF: Status mudando para aprovado - PDF será invalidado', [
                    'proposicao_id' => $proposicao->id,
                    'status_anterior' => $statusAnterior,
                    'novo_status' => $novoStatus,
                    'user_role' => $user->roles->pluck('name')->toArray(),
                    'pdf_path_anterior' => $proposicao->arquivo_pdf_path,
                    'invalidacao_aplicada' => true
                ]);
            } else {
                Log::info('📝 STATUS UPDATE: Mudança de status sem invalidação PDF', [
                    'proposicao_id' => $proposicao->id,
                    'status_anterior' => $statusAnterior,
                    'novo_status' => $novoStatus,
                    'user_role' => $user->roles->pluck('name')->toArray(),
                    'invalidacao_aplicada' => false
                ]);
            }

            // Atualizar status
            $proposicao->update($updateData);

            // Log da alteração
            Log::info('Status da proposição alterado', [
                'proposicao_id' => $proposicao->id,
                'status_anterior' => $statusAnterior,
                'novo_status' => $novoStatus,
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso.',
                'status_anterior' => $statusAnterior,
                'novo_status' => $novoStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status da proposição', [
                'proposicao_id' => $proposicao->id,
                'erro' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Erro interno ao atualizar status.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método robusto que replica a lógica do ProposicaoAssinaturaController
     * Busca PDFs por timestamp real de modificação, não por nome
     */
    private function encontrarPDFMaisRecenteRobusta(Proposicao $proposicao): ?string
    {
        // 🚨 CORREÇÃO: APÓS MIGRATE:SAFE, USAR APENAS DADOS DO BANCO ATUAL
        // Não buscar arquivos antigos que podem ter dados de estados anteriores
        
        Log::info('🔴 PDF REQUEST: encontrarPDFMaisRecenteRobusta - usando apenas dados do banco atual', [
            'proposicao_id' => $proposicao->id,
            'status' => $proposicao->status,
            'arquivo_pdf_path' => $proposicao->arquivo_pdf_path
        ]);
        
        // APENAS verificar se há PDF no arquivo_pdf_path do banco atual
        if ($proposicao->arquivo_pdf_path) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($caminhoCompleto)) {
                Log::info('🔴 PDF REQUEST: PDF encontrado no banco atual', [
                    'proposicao_id' => $proposicao->id,
                    'pdf_path' => $proposicao->arquivo_pdf_path,
                    'pdf_size' => filesize($caminhoCompleto)
                ]);
                return $proposicao->arquivo_pdf_path;
            }
        }
        
        Log::info('🔴 PDF REQUEST: Nenhum PDF válido no banco atual - forçará regeneração', [
            'proposicao_id' => $proposicao->id,
            'arquivo_pdf_path_exists' => $proposicao->arquivo_pdf_path ? file_exists(storage_path('app/' . $proposicao->arquivo_pdf_path)) : false
        ]);

        
        return null; // Não encontrou PDF válido no banco atual
    }

    /**
     * Encontrar arquivo RTF mais recente para uma proposição
     * Baseado na lógica de priorização da documentação técnica
     */
    private function encontrarRTFMaisRecenteRobusta(Proposicao $proposicao): ?string
    {
        Log::info('PDF REQUEST: Buscando RTF mais recente para proposição', [
            'proposicao_id' => $proposicao->id,
            'arquivo_path_banco' => $proposicao->arquivo_path
        ]);

        // Buscar arquivos RTF da proposição em todos os diretórios possíveis
        $diretoriosPossiveis = [
            storage_path('app/proposicoes/'),
            storage_path('app/private/proposicoes/'),
            storage_path('app/local/proposicoes/'),
        ];

        $arquivosEncontrados = [];

        foreach ($diretoriosPossiveis as $diretorio) {
            if (is_dir($diretorio)) {
                $pattern = $diretorio . "proposicao_{$proposicao->id}_*.rtf";
                $arquivos = glob($pattern);

                foreach ($arquivos as $arquivo) {
                    if (file_exists($arquivo)) {
                        $arquivosEncontrados[] = [
                            'caminho' => $arquivo,
                            'modificado' => filemtime($arquivo),
                            'tamanho' => filesize($arquivo)
                        ];
                    }
                }
            }
        }

        if (empty($arquivosEncontrados)) {
            Log::warning('PDF REQUEST: Nenhum arquivo RTF encontrado para proposição', [
                'proposicao_id' => $proposicao->id
            ]);
            return null;
        }

        // Ordenar por data de modificação (mais recente primeiro)
        usort($arquivosEncontrados, function($a, $b) {
            return $b['modificado'] - $a['modificado'];
        });

        $arquivoMaisRecente = $arquivosEncontrados[0];

        Log::info('PDF REQUEST: RTF mais recente encontrado', [
            'proposicao_id' => $proposicao->id,
            'arquivo_path' => $arquivoMaisRecente['caminho'],
            'modificado_em' => date('Y-m-d H:i:s', $arquivoMaisRecente['modificado']),
            'tamanho' => $arquivoMaisRecente['tamanho'],
            'total_arquivos' => count($arquivosEncontrados)
        ]);

        return $arquivoMaisRecente['caminho'];
    }

    /**
     * Retornar dados frescos da proposição para Vue.js (polling)
     */
    public function getDadosFrescos(Proposicao $proposicao)
    {
        try {
            // Verificar permissões
            if (!$this->podeVisualizarProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para visualizar esta proposição'
                ], 403);
            }

            // Recarregar dados frescos do banco
            $proposicao->refresh();

            // Verificar se há PDF assinado/mais recente
            $pdfPath = $this->encontrarPDFMaisRecenteRobusta($proposicao);
            
            // Decodificar assinatura digital se existir
            $assinaturaDigital = null;
            if ($proposicao->assinatura_digital) {
                $assinaturaDigital = json_decode($proposicao->assinatura_digital, true);
            }

            return response()->json([
                'success' => true,
                'proposicao' => [
                    'id' => $proposicao->id,
                    'status' => $proposicao->status,
                    'numero_protocolo' => $proposicao->numero_protocolo,
                    'data_protocolo' => $proposicao->data_protocolo?->format('d/m/Y H:i'),
                    'data_criacao' => $proposicao->created_at?->format('d/m/Y H:i'),
                    'ementa' => $proposicao->ementa,
                    'arquivo_pdf_path' => $pdfPath,
                    'has_pdf' => !empty($pdfPath),
                    'assinatura_digital' => $assinaturaDigital,
                    'updated_at' => $proposicao->updated_at?->toISOString(),
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados frescos da proposição', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da proposição',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar se usuário pode visualizar proposição
     */
    private function podeVisualizarProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        // Admin pode ver tudo
        if ($user->hasRole('ADMIN')) {
            return true;
        }
        
        // Parlamentar pode ver suas próprias
        if ($user->hasRole('PARLAMENTAR') && $proposicao->user_id === $user->id) {
            return true;
        }
        
        // Legislativo pode ver proposições em tramitação
        if ($user->hasRole('LEGISLATIVO')) {
            return true;
        }
        
        // Protocolo pode ver proposições protocoladas
        if ($user->hasRole('PROTOCOLO')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 🎯 SOLUÇÃO DEFINITIVA: Garantir que RTF sempre use template universal
     * 
     * Este método verifica se o RTF tem template aplicado, e se não tiver,
     * aplica automaticamente o template universal correto para o tipo da proposição.
     * 
     * @param string $caminhoRTF Caminho do RTF original
     * @return string Caminho do RTF com template aplicado
     */
    private function garantirTemplateUniversal(string $caminhoRTF): string
    {
        try {
            // Extrair ID da proposição do nome do arquivo
            // Exemplo: proposicoes/proposicao_1_1757619341.rtf -> ID = 1
            $nomeArquivo = basename($caminhoRTF);
            if (!preg_match('/proposicao_(\d+)_/', $nomeArquivo, $matches)) {
                Log::warning('🔴 PDF REQUEST: Não conseguiu extrair ID da proposição do RTF', [
                    'caminho' => $caminhoRTF
                ]);
                return $caminhoRTF; // Retornar original se não conseguir extrair ID
            }
            
            $proposicaoId = (int) $matches[1];
            $proposicao = Proposicao::find($proposicaoId);
            
            if (!$proposicao) {
                Log::warning('🔴 PDF REQUEST: Proposição não encontrada', [
                    'id' => $proposicaoId
                ]);
                return $caminhoRTF;
            }
            
            // Verificar se RTF é pequeno demais (indica que não tem template aplicado)
            $caminhoAbsoluto = Storage::path(str_replace(storage_path('app/'), '', $caminhoRTF));
            if (!Storage::exists($caminhoAbsoluto)) {
                // Tentar caminho relativo diretamente
                $caminhoRelativo = str_replace(storage_path('app/'), '', $caminhoRTF);
                if (!Storage::exists($caminhoRelativo)) {
                    Log::warning('🔴 PDF REQUEST: RTF não encontrado no Storage', [
                        'caminho_absoluto' => $caminhoAbsoluto,
                        'caminho_relativo' => $caminhoRelativo
                    ]);
                    return $caminhoRTF;
                }
                $caminhoAbsoluto = $caminhoRelativo;
            }
            
            $rtfContent = Storage::get($caminhoAbsoluto);
            $rtfSize = strlen($rtfContent);
            
            // Se RTF é pequeno (< 10KB) ou não contém elementos de template, aplicar template
            $precisaTemplate = $rtfSize < 10000 || 
                              !str_contains($rtfContent, 'CÂMARA MUNICIPAL') &&
                              !str_contains($rtfContent, 'pict\\pngblip') &&
                              !str_contains($rtfContent, 'SUBEMENDA Nº');
            
            if (!$precisaTemplate) {
                Log::info('🔴 PDF REQUEST: RTF já tem template aplicado', [
                    'proposicao_id' => $proposicaoId,
                    'size' => $rtfSize
                ]);
                return $caminhoRTF;
            }
            
            Log::info('🔴 PDF REQUEST: RTF precisa de template universal', [
                'proposicao_id' => $proposicaoId,
                'size' => $rtfSize,
                'tem_camara' => str_contains($rtfContent, 'CÂMARA MUNICIPAL'),
                'tem_imagem' => str_contains($rtfContent, 'pict\\pngblip')
            ]);
            
            // Buscar template correto para o tipo da proposição
            $template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', $proposicao->tipoProposicao->id)
                ->where('ativo', true)
                ->first();
            
            if (!$template) {
                Log::warning('🔴 PDF REQUEST: Template não encontrado para tipo', [
                    'tipo_id' => $proposicao->tipoProposicao->id,
                    'tipo_nome' => $proposicao->tipoProposicao->nome
                ]);
                return $caminhoRTF;
            }
            
            // Aplicar template usando TemplateProcessorService
            $templateService = app(\App\Services\Template\TemplateProcessorService::class);
            
            $dadosEditaveis = [
                'ementa' => $proposicao->ementa ?: 'Ementa da proposição',
                'texto' => $proposicao->texto ?: $proposicao->ementa ?: 'Conteúdo da proposição',
                'justificativa' => $proposicao->justificativa ?: '',
                'observacoes' => '',
            ];
            
            $rtfComTemplate = $templateService->processarTemplate($template, $proposicao, $dadosEditaveis);
            
            // Salvar RTF processado com nome único
            $novoNome = 'proposicoes/proposicao_' . $proposicaoId . '_template_' . time() . '.rtf';
            Storage::put($novoNome, $rtfComTemplate);
            
            // Atualizar proposição para usar novo RTF (para próximas vezes)
            $proposicao->update([
                'arquivo_path' => $novoNome,
                'arquivo_pdf_path' => null, // Forçar regeneração de PDF futuro
            ]);
            
            Log::info('🔴 PDF REQUEST: Template universal aplicado com sucesso', [
                'proposicao_id' => $proposicaoId,
                'template_id' => $template->id,
                'novo_rtf' => $novoNome,
                'size_original' => $rtfSize,
                'size_com_template' => strlen($rtfComTemplate)
            ]);
            
            // Retornar caminho absoluto do novo RTF
            return Storage::path($novoNome);
            
        } catch (\Exception $e) {
            Log::error('🔴 PDF REQUEST: Erro ao aplicar template universal', [
                'caminho' => $caminhoRTF,
                'erro' => $e->getMessage(),
                'linha' => $e->getLine()
            ]);
            return $caminhoRTF; // Em caso de erro, usar RTF original
        }
    }

    /**
     * Gerar HTML para PDF com formatação adequada
     */
    private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        // Gerar cabeçalho com dados da câmara e número da proposição
        $templateVariableService = app(\App\Services\Template\TemplateVariableService::class);
        $variables = $templateVariableService->getTemplateVariables();

        // Obter número da proposição (prioriza número, depois protocolo)
        $numeroProposicao = $proposicao->numero ?: $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]';

        // Gerar cabeçalho com imagem se disponível
        $headerHTML = '';
        if (! empty($variables['cabecalho_imagem'])) {
            $imagePath = public_path($variables['cabecalho_imagem']);
            if (file_exists($imagePath)) {
                $imageData = base64_encode(file_get_contents($imagePath));
                $mimeType = mime_content_type($imagePath);
                $headerHTML = '<div style="text-align: center; margin-bottom: 20px;">
                    <img src="data:'.$mimeType.';base64,'.$imageData.'"
                         style="max-width: 200px; height: auto;" alt="Cabeçalho" />
                </div>';
            }
        }

        // Cabeçalho da câmara
        $cabeçalhoTexto = "
        <div style='text-align: center; margin-bottom: 20px;'>
            <strong>{$variables['cabecalho_nome_camara']}</strong><br>
            {$variables['cabecalho_endereco']}<br>
            {$variables['cabecalho_telefone']}<br>
            {$variables['cabecalho_website']}
        </div>";

        // Título do documento com número da proposição
        $tipoUppercase = strtoupper($proposicao->tipo);
        $tituloHTML = "
        <div style='text-align: center; margin: 20px 0;'>
            <strong>{$tipoUppercase} Nº {$numeroProposicao}</strong>
        </div>";

        // Ementa se disponível
        $ementaHTML = '';
        if ($proposicao->ementa) {
            $ementaHTML = "
            <div style='margin: 20px 0;'>
                <strong>EMENTA:</strong> {$proposicao->ementa}
            </div>";
        }

        // Separar conteúdo de texto puro
        $conteudoTexto = $conteudo ?: 'Conteúdo não disponível';

        // Limpar restos de placeholders e HTML que possam estar no conteúdo
        $conteudoTexto = $this->limparConteudoParaPDFController($conteudoTexto);

        // Gerar assinatura visual se disponível
        $assinaturaHTML = '';
        if ($proposicao->assinatura_digital && $proposicao->data_assinatura) {
            $assinaturaQRService = app(\App\Services\Template\AssinaturaQRService::class);
            $assinaturaHTML = $assinaturaQRService->gerarHTMLAssinaturaVisualPDF($proposicao);
        }

        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Proposição {$proposicao->id}</title>
            <style>
                body {
                    font-family: 'Times New Roman', serif;
                    margin: 2.5cm 2cm 2cm 2cm;
                    line-height: 1.6;
                    font-size: 12pt;
                    color: #000;
                    text-align: justify;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .document-content {
                    white-space: pre-wrap;
                    margin: 20px 0;
                    padding: 0;
                }
            </style>
        </head>
        <body>
            {$headerHTML}
            {$cabeçalhoTexto}
            {$tituloHTML}
            {$ementaHTML}
            <div class='document-content'>".nl2br(htmlspecialchars($conteudoTexto))."</div>
            {$assinaturaHTML}
        </body>
        </html>";
    }

    /**
     * Método melhorado para extrair texto RTF - mais agressivo para capturar conteúdo real
     */
    private function extrairTextoRTFMelhorado(string $rtfContent): string
    {
        // ETAPA 1: Decodificar Unicode RTF primeiro
        $texto = $this->decodificarUnicodeRTF($rtfContent);

        // ETAPA 2: Extrair texto do corpo do documento RTF
        // Buscar padrões específicos de conteúdo de proposição
        $conteudoExtracted = [];

        // Buscar títulos e texto principal com padrões mais amplos
        $patterns = [
            '/PROJETO DE [A-Z\s]+N[°º\s]*\[[^\]]+\]/i',
            '/DECRETO [A-Z\s]+N[°º\s]*\[[^\]]+\]/i',
            '/EMENTA[:\s]*([^\\\\{}]{20,})/i',
            '/CONSIDERANDO[:\s]*([^\\\\{}]{30,})/i',
            '/Art[.\s]*\d+[^\\\\{}]{20,}/i',
            '/CÂMARA MUNICIPAL[^\\\\{}]{10,}/i',
            '/Dispõe[^\\\\{}]{30,}/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $texto, $matches)) {
                foreach ($matches[0] as $match) {
                    $limpo = trim(preg_replace('/\\\\[a-z]+\d*\s*|[{}]/', ' ', $match));
                    $limpo = preg_replace('/\s+/', ' ', $limpo);
                    if (strlen($limpo) > 15 && !preg_match('/\b(Open Sans|Arial|Calibri|Times New Roman)\b/i', $limpo)) {
                        $conteudoExtracted[] = $limpo;
                    }
                }
            }
        }

        // Buscar parágrafos de texto entre \\par
        if (preg_match_all('/\\\\par[^\\\\]*?([A-Z][^\\\\{}]{20,})/i', $texto, $matches)) {
            foreach ($matches[1] as $match) {
                $limpo = trim(preg_replace('/\\\\[a-z]+\d*\s*|[{}]/', ' ', $match));
                if (strlen($limpo) > 20 && !preg_match('/^[0-9\s\-\*]+$/', $limpo)) {
                    $conteudoExtracted[] = $limpo;
                }
            }
        }

        // Se não encontrou nada específico, fazer extração mais geral
        if (empty($conteudoExtracted)) {
            // Remover códigos RTF e extrair texto
            $textoLimpo = preg_replace('/\\\\[a-z]+\d*\s*/i', ' ', $texto);
            $textoLimpo = str_replace(['{', '}', '\\'], ' ', $textoLimpo);
            $textoLimpo = preg_replace('/\s+/', ' ', $textoLimpo);

            // Buscar sequências de texto de pelo menos 50 caracteres (menos restritivo)
            if (preg_match_all('/([A-ZÁÀÃÂÉÊÍÓÔÕÚÇ][^\\\\{}\d]{50,})/u', $textoLimpo, $matches)) {
                foreach ($matches[1] as $match) {
                    $limpo = trim($match);
                    // Filtrar metadados RTF conhecidos
                    if (strlen($limpo) > 50 &&
                        !preg_match('/\b(Open Sans|Arial|Calibri|Times New Roman|Cambria|Heading|Table Grid|panose)\b/i', $limpo)) {
                        $conteudoExtracted[] = $limpo;
                    }
                }
            }
        }

        // Combinar resultado
        $resultado = implode("\n\n", $conteudoExtracted);
        $resultado = preg_replace('/\s+/', ' ', $resultado);
        $resultado = trim($resultado);

        // Se ainda está muito pequeno, usar extração ultra-agressiva
        if (strlen($resultado) < 100) {
            $textoLimpo = preg_replace('/\\\\[a-z]+\d*\s*/i', ' ', $texto);
            $textoLimpo = str_replace(['{', '}'], '', $textoLimpo);
            $textoLimpo = preg_replace('/\s+/', ' ', $textoLimpo);

            // Pegar primeiras 2000 chars de texto limpo
            $resultado = substr(trim($textoLimpo), 0, 2000);
        }

        return $resultado;
    }

    /**
     * Limpar conteúdo para PDF removendo placeholders e restos de HTML
     */
    private function limparConteudoParaPDFController(string $conteudo): string
    {
        // Remover tags HTML que possam estar como texto
        $conteudo = preg_replace('/<[^>]*>/', '', $conteudo);

        // CORREÇÃO: Remover apenas placeholders de template (${variavel}) sem interferir com códigos RTF
        // Não remover asteriscos isolados que podem ser parte da codificação Unicode RTF (\u123*)
        $conteudo = preg_replace('/\$\{[^}]*\}/', '', $conteudo);

        // Remover espaços extras e quebras de linha desnecessárias
        $conteudo = preg_replace('/\s+/', ' ', $conteudo);
        $conteudo = trim($conteudo);

        return $conteudo;
    }
}
