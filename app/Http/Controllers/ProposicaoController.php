<?php

namespace App\Http\Controllers;

use App\Models\TipoProposicao;
use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoController extends Controller
{
    /**
     * Tela inicial para criação de proposição (Parlamentar)
     */
    public function create()
    {
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
            
            // Converter para formato esperado pelo frontend
            $modelosArray = [];
            foreach ($modelos as $modelo) {
                $modelosArray[] = [
                    'id' => $modelo->id,
                    'nome' => $modelo->nome,
                    'descricao' => $modelo->descricao ?? '',
                    'is_template' => $modelo->is_template ?? false,
                    'template_id' => $modelo->template_id ?? null
                ];
            }

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
        // TODO: Implement proper authorization
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo_modelo' => 'required|array',
            'modelo_id' => 'required' // Aceitar tanto integer quanto string (para templates)
        ]);

        // TODO: Implement proper template processing
        $modeloId = $request->modelo_id;
        $isTemplate = str_starts_with($modeloId, 'template_');
        
        // Extrair campos principais
        $ementa = $request->conteudo_modelo['ementa'] ?? '';
        $conteudo = $request->conteudo_modelo['conteudo'] ?? '';
        
        if ($isTemplate) {
            $templateId = str_replace('template_', '', $modeloId);
            // Para templates, o texto gerado será o conteúdo principal formatado
            $textoGerado = "EMENTA: {$ementa}\n\n";
            $textoGerado .= $conteudo;
        } else {
            // Para modelos normais, formatar o texto de forma estruturada
            $textoGerado = "PROPOSIÇÃO LEGISLATIVA\n\n";
            $textoGerado .= "EMENTA: {$ementa}\n\n";
            $textoGerado .= "CONTEÚDO:\n\n";
            $textoGerado .= $conteudo;
        }

        // Atualizar proposição no banco de dados
        $proposicao = Proposicao::find($proposicaoId);
        if ($proposicao) {
            $proposicao->update([
                'ementa' => $ementa,
                'conteudo' => $conteudo,
                'modelo_id' => $modeloId,
                'template_id' => $isTemplate ? $templateId : null,
                'ultima_modificacao' => now()
            ]);
        }
        
        // Armazenar informações da proposição na sessão para uso posterior (backup)
        session([
            'proposicao_' . $proposicaoId . '_modelo_id' => $modeloId,
            'proposicao_' . $proposicaoId . '_template_id' => $isTemplate ? $templateId : null,
            'proposicao_' . $proposicaoId . '_tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'projeto_lei'),
            'proposicao_' . $proposicaoId . '_texto_gerado' => $textoGerado,
            'proposicao_' . $proposicaoId . '_ementa' => $ementa,
            'proposicao_' . $proposicaoId . '_conteudo' => $conteudo
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
        
        // Simular busca da proposição
        $proposicao = (object) [
            'id' => $proposicaoId,
            'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'mocao'),
            'modelo_id' => session('proposicao_' . $proposicaoId . '_modelo_id')
        ];
        
        try {
            // Buscar o template
            $template = \App\Models\TipoProposicaoTemplate::find($templateId);
            
            if (!$template) {
                return redirect()->route('proposicoes.editar-texto', $proposicaoId)
                                ->with('error', 'Template não encontrado. Usando editor de texto.');
            }
            
            // Criar uma instância do documento baseada no template para esta proposição
            $documentKey = 'proposicao_' . $proposicaoId . '_template_' . $templateId . '_' . time();
            
            // Copiar o arquivo do template para um arquivo específico da proposição
            $arquivoProposicaoPath = $this->criarArquivoProposicao($proposicaoId, $template);
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
    public function enviarLegislativo($proposicaoId)
    {
        // TODO: Implement proper authorization and validation
        // $this->authorize('update', $proposicao);
        
        // Validar se tem conteúdo
        $ementa = session('proposicao_' . $proposicaoId . '_ementa');
        $conteudo = session('proposicao_' . $proposicaoId . '_conteudo');
        
        if (!$ementa || !$conteudo) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve ter ementa e conteúdo antes de ser enviada.'
            ], 400);
        }

        // Atualizar status
        session([
            'proposicao_' . $proposicaoId . '_status' => 'enviado_legislativo',
            'proposicao_' . $proposicaoId . '_enviado_em' => now()
        ]);

        \Log::info('Proposição enviada para legislativo', [
            'proposicao_id' => $proposicaoId,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição enviada para análise legislativa!'
        ]);
    }

    /**
     * Listagem das próprias proposições (Parlamentar)
     */
    public function minhasProposicoes()
    {
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
        // TODO: Implement proper authorization and model loading
        // $this->authorize('view', $proposicao);
        
        // Usar os mesmos dados mock consistentes
        $proposicoesBase = [
            1 => [
                'tipo' => 'projeto_lei_ordinaria',
                'ementa' => 'Dispõe sobre a regulamentação do uso de bicicletas em vias públicas',
                'status' => 'rascunho'
            ],
            2 => [
                'tipo' => 'projeto_lei_complementar',
                'ementa' => 'Altera a Lei Orgânica do Município para incluir dispositivos sobre transparência',
                'status' => 'rascunho'
            ],
            3 => [
                'tipo' => 'indicacao',
                'ementa' => 'Indica ao Poder Executivo a necessidade de melhorias na iluminação pública',
                'status' => 'rascunho'
            ],
            4 => [
                'tipo' => 'projeto_lei_ordinaria',
                'ementa' => 'Institui o Programa Municipal de Compostagem de Resíduos Orgânicos',
                'status' => 'aprovada'
            ]
        ];
        
        $dados = $proposicoesBase[$proposicaoId] ?? [
            'tipo' => 'projeto_lei_ordinaria',
            'ementa' => 'Exemplo de proposição',
            'status' => 'rascunho'
        ];
        
        $proposicao = (object) array_merge([
            'id' => $proposicaoId,
            'conteudo' => 'Conteúdo da proposição...',
            'autor' => Auth::user(),
            'created_at' => now()
        ], $dados);
        
        return view('proposicoes.show', compact('proposicao'));
    }

    /**
     * Visualizar status de tramitação da proposição
     */
    public function statusTramitacao($proposicaoId)
    {
        // Buscar dados completos da proposição
        $status = session('proposicao_' . $proposicaoId . '_status', 'rascunho');
        $assinatura = session('proposicao_' . $proposicaoId . '_assinatura');
        $protocolo = session('proposicao_' . $proposicaoId . '_protocolo');
        $observacoes = session('proposicao_' . $proposicaoId . '_observacoes_legislativo');
        $enviadoEm = session('proposicao_' . $proposicaoId . '_enviado_em');

        // Definir ordem dos status para o progress bar
        $statusOrder = [
            'rascunho' => 1,
            'enviado_legislativo' => 2,
            'retornado_legislativo' => 3,
            'assinado' => 4,
            'protocolado' => 5
        ];

        $proposicao = (object) [
            'id' => $proposicaoId,
            'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'projeto_lei'),
            'ementa' => session('proposicao_' . $proposicaoId . '_ementa', ''),
            'conteudo' => session('proposicao_' . $proposicaoId . '_conteudo', ''),
            'status' => $status,
            'status_order' => $statusOrder[$status] ?? 1,
            'assinatura' => $assinatura,
            'protocolo' => $protocolo,
            'observacoes_legislativo' => $observacoes,
            'enviado_em' => $enviadoEm,
            'template_id' => session('proposicao_' . $proposicaoId . '_modelo_id')
        ];

        return view('proposicoes.status-tramitacao', compact('proposicao'));
    }

    /**
     * Excluir proposição (apenas rascunhos)
     */
    public function destroy($proposicaoId)
    {
        // TODO: Implement proper authorization and model loading
        // $this->authorize('delete', $proposicao);
        
        // Simular busca da proposição usando os mesmos dados mock
        $proposicoesBase = [
            1 => ['status' => 'rascunho', 'autor_id' => Auth::id()],
            2 => ['status' => 'rascunho', 'autor_id' => Auth::id()],
            3 => ['status' => 'rascunho', 'autor_id' => Auth::id()],
            4 => ['status' => 'aprovada', 'autor_id' => Auth::id()],
        ];
        
        // Verificar se a proposição existe
        if (!isset($proposicoesBase[$proposicaoId])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não encontrada.'
            ], 404);
        }
        
        $proposicaoData = $proposicoesBase[$proposicaoId];
        
        // Verificar se é rascunho
        if ($proposicaoData['status'] !== 'rascunho') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas rascunhos podem ser excluídos.'
            ], 400);
        }
        
        // Verificar se o usuário é o autor
        if ($proposicaoData['autor_id'] !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir esta proposição.'
            ], 403);
        }
        
        try {
            // TODO: Implement actual deletion when model is ready
            // $proposicao->delete();
            
            // Por enquanto, usar sessão para simular exclusão
            $excluidas = session('proposicoes_excluidas', []);
            $excluidas[] = (int) $proposicaoId;
            session(['proposicoes_excluidas' => array_unique($excluidas)]);
            
            \Log::info('Proposição excluída', [
                'proposicao_id' => $proposicaoId,
                'user_id' => Auth::id(),
                'method' => 'session_simulation'
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
            $nomeArquivo = "proposicao_{$proposicaoId}_template_{$template->id}.docx";
            $pathDestino = "proposicoes/{$nomeArquivo}";
            $pathCompleto = storage_path('app/public/' . $pathDestino);
            
            // Garantir que o diretório existe
            $diretorio = dirname($pathCompleto);
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            // Se o template tem um arquivo, copiar como base
            if ($template->arquivo_path && \Storage::disk('public')->exists($template->arquivo_path)) {
                \Storage::disk('public')->copy($template->arquivo_path, $pathDestino);
                \Log::info('Arquivo do template copiado', [
                    'template_path' => $template->arquivo_path,
                    'proposicao_path' => $pathDestino,
                    'arquivo_existe_apos_copia' => \Storage::disk('public')->exists($pathDestino)
                ]);
            } else {
                // Criar um arquivo RTF básico com o texto gerado
                $ementa = session('proposicao_' . $proposicaoId . '_ementa', 'Proposição em elaboração');
                $conteudo = session('proposicao_' . $proposicaoId . '_conteudo', 'Conteúdo da proposição a ser desenvolvido.');
                $tipo = session('proposicao_' . $proposicaoId . '_tipo', 'mocao');
                
                $textoCompleto = "PROPOSIÇÃO - " . strtoupper($tipo) . "\n\n";
                $textoCompleto .= "EMENTA: " . $ementa . "\n\n";
                $textoCompleto .= "CONTEÚDO:\n" . $conteudo;
                
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
        // Limpar quebras de linha
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
            
            // Determinar MIME type baseado na extensão
            $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
            switch (strtolower($extensao)) {
                case 'docx':
                    $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    break;
                case 'rtf':
                    $mimeType = 'application/rtf';
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
                    // Substituir localhost pelo IP do container OnlyOffice
                    // Usando IP direto porque os containers estão em redes diferentes
                    $url = str_replace('http://localhost:8080', 'http://172.24.0.3', $data['url']);
                    
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
                        // Extrair template_id do nome do arquivo atual ou da sessão
                        $templateId = session('proposicao_' . $proposicaoId . '_template_id', 11);
                        
                        // Se o template_id veio como string "template_X", extrair apenas o número
                        if (is_string($templateId) && str_starts_with($templateId, 'template_')) {
                            $templateId = str_replace('template_', '', $templateId);
                        }
                        
                        $nomeArquivo = "proposicao_{$proposicaoId}_template_{$templateId}.docx";
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
                        
                        // Atualizar sessão com timestamp da última modificação
                        session(['proposicao_' . $proposicaoId . '_ultima_modificacao' => now()]);
                        
                        // Atualizar registro da proposição no banco de dados
                        $proposicao = Proposicao::find($proposicaoId);
                        if ($proposicao) {
                            $proposicao->update([
                                'arquivo_path' => $pathDestino,
                                'ultima_modificacao' => now(),
                                'status' => 'em_edicao'
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
        // Buscar dados da proposição
        $proposicao = (object) [
            'id' => $proposicaoId,
            'tipo' => session('proposicao_' . $proposicaoId . '_tipo', 'projeto_lei'),
            'ementa' => session('proposicao_' . $proposicaoId . '_ementa', ''),
            'conteudo' => session('proposicao_' . $proposicaoId . '_conteudo', ''),
            'status' => session('proposicao_' . $proposicaoId . '_status', 'rascunho')
        ];

        // Buscar template
        $template = \App\Models\TipoProposicaoTemplate::find($templateId);
        
        if (!$template) {
            return redirect()->route('proposicoes.minhas-proposicoes')
                            ->with('error', 'Template não encontrado.');
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

        // Salvar status na sessão
        session(['proposicao_' . $proposicaoId . '_status' => $request->status]);
        
        // Log da mudança de status
        \Log::info('Status da proposição atualizado', [
            'proposicao_id' => $proposicaoId,
            'novo_status' => $request->status,
            'user_id' => Auth::id()
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
}