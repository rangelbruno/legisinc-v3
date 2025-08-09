<?php

namespace App\Http\Controllers;

use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateParametrosService;
use App\Services\Template\TemplateEstruturadorService;
use App\Services\Template\TemplateValidadorLegalService;
use App\Services\Template\TemplateNumeracaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class TemplateController extends Controller
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService,
        private TemplateParametrosService $parametrosService,
        private TemplateEstruturadorService $estruturadorService,
        private TemplateValidadorLegalService $validadorService,
        private TemplateNumeracaoService $numeracaoService
    ) {}

    /**
     * Lista todos os tipos com seus templates
     */
    public function index()
    {
        $tipos = TipoProposicao::with('template')
                               ->orderBy('nome')
                               ->get();

        return view('admin.templates.index', compact('tipos'));
    }

    /**
     * Exibe formulário para criar novo template
     */
    public function create()
    {
        $tipos = TipoProposicao::orderBy('nome')->get();
        return view('admin.templates.create', compact('tipos'));
    }

    /**
     * Salva novo template
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_proposicao_id' => 'required|exists:tipo_proposicoes,id',
            'substituir_existente' => 'sometimes|boolean'
        ]);

        $tipo = TipoProposicao::findOrFail($request->tipo_proposicao_id);
        
        // Verificar se já existe template
        $templateExistente = $tipo->template;
        if ($templateExistente && !$request->boolean('substituir_existente')) {
            return back()->withErrors([
                'tipo_proposicao_id' => 'Este tipo já possui um template. Marque a opção "Substituir template existente" se desejar criar um novo.'
            ])->withInput();
        }

        // Remover template existente se for substituição
        if ($templateExistente && $request->boolean('substituir_existente')) {
            // Remover arquivo do template anterior se existir
            if ($templateExistente->arquivo_path && Storage::exists($templateExistente->arquivo_path)) {
                Storage::delete($templateExistente->arquivo_path);
            }
            $templateExistente->delete();
        }

        // Criar novo template
        $template = TipoProposicaoTemplate::create([
            'tipo_proposicao_id' => $tipo->id,
            'document_key' => 'template_' . $tipo->id . '_' . time(),
            'ativo' => true,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('templates.editor', $tipo)
                        ->with('success', 'Template criado com sucesso! Agora você pode editá-lo.');
    }

    /**
     * Criar ou editar template (mesmo método!)
     */
    public function editor(TipoProposicao $tipo)
    {
        // Buscar ou criar template
        $template = TipoProposicaoTemplate::firstOrCreate(
            ['tipo_proposicao_id' => $tipo->id],
            [
                'document_key' => 'template_' . $tipo->id . '_' . time() . '_' . uniqid(),
                'updated_by' => auth()->id()
            ]
        );

        // Verificar se há callback em processamento
        $callbackEmProcessamento = \Cache::has('onlyoffice_callback_' . $template->document_key) ||
                                   \Cache::has('onlyoffice_save_lock_' . $template->document_key);
        
        // Verificar se é uma nova sessão (possível logout/login)
        $sessionKey = 'template_session_' . $template->id . '_' . auth()->id();
        $ultimaSessao = \Cache::get($sessionKey);
        $novaSessao = $ultimaSessao !== session()->getId();
        
        // Sempre gerar novo document_key se:
        // 1. Não houver um key
        // 2. Passou mais de 2 minutos desde a última modificação
        // 3. Não há callback em processamento
        // 4. Nova sessão detectada (logout/login)
        $tempoDesdeUltimaModificacao = $template->updated_at->diffInMinutes(now());
        
        if (empty($template->document_key) || 
            ($tempoDesdeUltimaModificacao > 2 && !$callbackEmProcessamento) ||
            $novaSessao) {
            
            $novoDocumentKey = 'template_' . $tipo->id . '_' . time() . '_' . uniqid();
            
            \Log::info('Gerando novo document_key para template', [
                'template_id' => $template->id,
                'tipo_id' => $tipo->id,
                'old_key' => $template->document_key,
                'new_key' => $novoDocumentKey,
                'tempo_desde_modificacao' => $tempoDesdeUltimaModificacao,
                'callback_em_processamento' => $callbackEmProcessamento,
                'nova_sessao' => $novaSessao,
                'session_id' => session()->getId()
            ]);
            
            $template->update([
                'document_key' => $novoDocumentKey,
                'updated_by' => auth()->id()
            ]);
            
            // Limpar caches relacionados
            \Cache::forget('onlyoffice_template_' . $template->id);
            \Cache::forget('onlyoffice_callback_' . $template->document_key);
            \Cache::forget('onlyoffice_save_lock_' . $template->document_key);
            
            // Registrar nova sessão para evitar conflitos futuros
            \Cache::put($sessionKey, session()->getId(), 3600); // Cache por 1 hora
        } else {
            \Log::info('Mantendo document_key existente', [
                'template_id' => $template->id,
                'document_key' => $template->document_key,
                'tempo_desde_modificacao' => $tempoDesdeUltimaModificacao,
                'callback_em_processamento' => $callbackEmProcessamento,
                'nova_sessao' => $novaSessao
            ]);
            
            // Ainda assim, registrar sessão atual para tracking
            \Cache::put($sessionKey, session()->getId(), 3600);
        }

        // Configuração do ONLYOFFICE
        $config = $this->onlyOfficeService->criarConfiguracaoTemplate($template);

        // Adicionar informação de sessão para evitar refresh desnecessário
        $config['editorConfig']['customization'] = $config['editorConfig']['customization'] ?? [];
        $config['editorConfig']['customization']['forcesave'] = true;
        $config['editorConfig']['customization']['autosave'] = true; // Manter autosave habilitado
        
        // Adicionar callback URL correta com document_key versionado
        $callbackUrl = route('api.onlyoffice.callback', $config['document']['key']);
        
        // Ajustar URL para comunicação entre containers (igual ao OnlyOfficeController)
        if (config('app.env') === 'local') {
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }
        
        $config['editorConfig']['callbackUrl'] = $callbackUrl;

        return view('admin.templates.editor', [
            'tipo' => $tipo,
            'template' => $template,
            'config' => $config
        ]);
    }

    /**
     * Download do template para uso
     */
    public function download(TipoProposicaoTemplate $template)
    {
        // Forçar refresh do modelo para pegar dados mais recentes
        $template->refresh();
        
        \Log::info('Template download requested', [
            'template_id' => $template->id,
            'ativo' => $template->ativo,
            'arquivo_path' => $template->arquivo_path,
            'file_exists' => $template->arquivo_path ? Storage::exists($template->arquivo_path) : false
        ]);

        if (!$template->ativo) {
            \Log::warning('Template não ativo', ['template_id' => $template->id]);
            abort(404, 'Template não está ativo');
        }

        if (!$template->arquivo_path) {
            \Log::warning('Template sem arquivo', ['template_id' => $template->id]);
            abort(404, 'Template não possui arquivo');
        }

        // Limpar cache de arquivo antes de verificar
        clearstatcache();
        
        if (!Storage::exists($template->arquivo_path)) {
            \Log::error('Arquivo do template não encontrado', [
                'template_id' => $template->id,
                'path' => $template->arquivo_path
            ]);
            abort(404, 'Arquivo do template não encontrado');
        }

        // Gerar nome do arquivo baseado no tipo de proposição
        $nomeArquivo = \Illuminate\Support\Str::slug($template->tipoProposicao->nome) . '.rtf';
        
        // Verificar se o arquivo precisa de correção de encoding antes do download
        $conteudoArquivo = Storage::get($template->arquivo_path);
        
        // Processar variáveis do template se for arquivo RTF
        if (pathinfo($template->arquivo_path, PATHINFO_EXTENSION) === 'rtf') {
            $conteudoArquivo = $this->processarVariaveisTemplate($conteudoArquivo);
            
            \Log::info('Variáveis do template processadas', [
                'template_id' => $template->id,
                'arquivo_path' => $template->arquivo_path,
                'tamanho_processado' => strlen($conteudoArquivo)
            ]);
        }
        
        // Se contém caracteres mal codificados comuns, tentar corrigir
        if (strpos($conteudoArquivo, 'MunicÃ­pio') !== false || 
            strpos($conteudoArquivo, 'SÃ£o Paulo') !== false ||
            strpos($conteudoArquivo, 'relaÃ§Ã£o') !== false) {
            
            \Log::info('Arquivo contém encoding incorreto, corrigindo antes do download', [
                'template_id' => $template->id,
                'path' => $template->arquivo_path
            ]);
            
            // Aplicar correções básicas
            $correcoes = [
                'MunicÃ­pio' => 'Município',
                'SÃ£o Paulo' => 'São Paulo',
                'relaÃ§Ã£o' => 'relação',
                'posiÃ§Ã£o' => 'posição',
                'funÃ§Ã£o' => 'função',
                'criaÃ§Ã£o' => 'criação',
                'legislaÃ§Ã£o' => 'legislação',
                'aprovaÃ§Ã£o' => 'aprovação'
            ];
            
            $conteudoCorrigido = str_replace(array_keys($correcoes), array_values($correcoes), $conteudoArquivo);
            
            // Criar arquivo temporário com conteúdo corrigido
            $tempFile = tempnam(sys_get_temp_dir(), 'template_fixed_') . '.rtf';
            file_put_contents($tempFile, $conteudoCorrigido);
            
            return response()->download(
                $tempFile,
                $nomeArquivo,
                [
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Content-Type' => 'application/rtf; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename*=UTF-8\'\'' . rawurlencode($nomeArquivo)
                ]
            )->deleteFileAfterSend(true);
        }
        
        // Detectar tipo de arquivo e definir Content-Type apropriado
        $extension = pathinfo($template->arquivo_path, PATHINFO_EXTENSION);
        $contentType = match($extension) {
            'txt' => 'text/plain; charset=utf-8',
            'rtf' => 'application/rtf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream'
        };
        
        // Se o conteúdo foi processado, criar arquivo temporário
        if (isset($conteudoArquivo)) {
            $tempFile = tempnam(sys_get_temp_dir(), 'template_processed_') . '.rtf';
            file_put_contents($tempFile, $conteudoArquivo);
            
            return response()->download(
                $tempFile,
                $nomeArquivo,
                [
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"'
                ]
            )->deleteFileAfterSend(true);
        }
        
        // Fallback para arquivo original (caso não tenha sido processado)
        return response()->download(
            Storage::path($template->arquivo_path), 
            $nomeArquivo,
            [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"'
            ]
        );
    }

    /**
     * Gerar documento a partir do template
     */
    public function gerar(Request $request, TipoProposicao $tipo)
    {
        $template = $tipo->template;
        
        if (!$template || !$template->ativo) {
            return response()->json([
                'error' => 'Template não disponível para este tipo'
            ], 404);
        }

        // Dados da proposição (vem do request)
        $dados = $request->validate([
            'numero' => 'nullable|string',
            'ementa' => 'required|string',
            'texto' => 'required|string',
            'autor_id' => 'required|exists:users,id'
        ]);

        // Gerar documento com substituição de variáveis
        $documentoPath = $this->onlyOfficeService->gerarDocumento($template, $dados);

        return response()->download($documentoPath);
    }

    /**
     * Salvar template manualmente
     */
    public function salvarTemplate(TipoProposicao $tipo)
    {
        try {
            $template = $tipo->template;
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template não encontrado'
                ], 404);
            }

            // Atualizar timestamp do template para indicar que foi modificado
            $template->touch();

            \Log::info('Template salvo manualmente', [
                'template_id' => $template->id,
                'tipo_id' => $tipo->id,
                'user_id' => auth()->id(),
                'document_key' => $template->document_key
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template salvo com sucesso!',
                'timestamp' => $template->updated_at->format('d/m/Y H:i:s')
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar template manualmente', [
                'tipo_id' => $tipo->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove template
     */
    public function destroy(TipoProposicaoTemplate $template)
    {
        try {
            // Remover arquivo do template se existir
            if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
                Storage::delete($template->arquivo_path);
            }

            // Obter nome do tipo para a mensagem
            $tipoNome = $template->tipoProposicao->nome;

            // Remover template do banco
            $template->delete();

            return redirect()->route('templates.index')
                            ->with('success', "Template do tipo '{$tipoNome}' foi removido com sucesso.");

        } catch (\Exception $e) {
            \Log::error('Erro ao excluir template', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('templates.index')
                            ->with('error', 'Erro ao remover template. Tente novamente.');
        }
    }

    /**
     * Regenerar todos os templates usando parâmetros atualizados
     */
    public function regenerarTodos()
    {
        try {
            // Executar comando para aplicar padrões legais
            Artisan::call('templates:aplicar-padroes-legais', ['--force' => true]);
            
            $output = Artisan::output();
            
            \Log::info('Templates regenerados com padrões legais', [
                'user_id' => auth()->id(),
                'output' => $output
            ]);

            // Contar templates criados/atualizados
            $totalTemplates = TipoProposicaoTemplate::count();
            
            return redirect()->route('templates.index')
                            ->with('success', "Todos os templates foram regenerados seguindo LC 95/1998 e padrões jurídicos! Total: {$totalTemplates} templates conformes.");

        } catch (\Exception $e) {
            \Log::error('Erro ao regenerar templates', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('templates.index')
                            ->with('error', 'Erro ao regenerar templates: ' . $e->getMessage());
        }
    }

    /**
     * Gerar template com padrões legais LC 95/1998
     */
    public function gerarComPadroesLegais(TipoProposicao $tipo)
    {
        try {
            // Dados exemplo para criar template estruturado
            $dadosExemplo = [
                'numero' => 1,
                'ano' => date('Y'),
                'ementa' => $this->gerarEmentaExemplo($tipo),
                'texto' => $this->gerarTextoExemplo($tipo),
                'justificativa' => 'Justificativa para a proposição.',
                'autor_nome' => 'Vereador(a)',
                'autor_cargo' => 'Vereador(a)',
                'autor_partido' => 'PARTIDO'
            ];

            // Estruturar conforme LC 95/1998
            $estrutura = $this->estruturadorService->estruturarProposicao($dadosExemplo, $tipo);

            // Gerar template estruturado
            $templateEstruturado = $this->estruturadorService->gerarTemplateEstruturado($dadosExemplo, $tipo);

            // Adicionar variáveis no template
            $templateComVariaveis = $this->adicionarVariaveisTemplate($templateEstruturado);

            // Buscar ou criar template
            $template = TipoProposicaoTemplate::firstOrCreate(
                ['tipo_proposicao_id' => $tipo->id],
                [
                    'document_key' => 'template_legal_' . $tipo->id . '_' . time() . '_' . uniqid(),
                    'updated_by' => auth()->id(),
                    'ativo' => true
                ]
            );

            // Salvar template estruturado como arquivo RTF
            $nomeArquivo = 'template_' . $tipo->codigo . '_legal.rtf';
            $caminhoArquivo = 'templates/' . $nomeArquivo;

            // Converter para RTF
            $conteudoRTF = $this->converterParaRTF($templateComVariaveis, $tipo);
            
            Storage::put($caminhoArquivo, $conteudoRTF);

            // Atualizar template no banco
            $template->update([
                'arquivo_path' => $caminhoArquivo,
                'updated_by' => auth()->id()
            ]);

            \Log::info('Template com padrões legais gerado', [
                'tipo_id' => $tipo->id,
                'template_id' => $template->id,
                'arquivo_path' => $caminhoArquivo,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Template estruturado conforme LC 95/1998 gerado com sucesso!",
                'estrutura' => [
                    'epigrafe' => $estrutura['epigrafe'],
                    'ementa' => substr($estrutura['ementa'], 0, 80) . '...',
                    'artigos' => count($estrutura['corpo_articulado']['artigos']),
                    'validacoes' => $estrutura['validacoes']['valida'] ? 'Conforme' : 'Com alertas'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar template com padrões legais', [
                'tipo_id' => $tipo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar template conforme padrões legais
     */
    public function validarTemplate(TipoProposicao $tipo)
    {
        try {
            $template = $tipo->template;
            
            if (!$template || !$template->arquivo_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template não encontrado'
                ], 404);
            }

            // Ler conteúdo do template
            $conteudo = Storage::get($template->arquivo_path);
            
            // Extrair texto do RTF (simplificado)
            $textoLimpo = $this->extrairTextoRTF($conteudo);

            // Dados simulados para validação
            $dadosValidacao = [
                'ementa' => 'Dispõe sobre exemplo e dá outras providências.',
                'texto' => $textoLimpo,
                'numero' => 1,
                'ano' => date('Y')
            ];

            // Executar validação
            $resultadoValidacao = $this->validadorService->validarProposicaoCompleta($dadosValidacao, $tipo);

            return response()->json([
                'success' => true,
                'validacao' => $resultadoValidacao['resumo'],
                'detalhes' => [
                    'lc95_conforme' => $resultadoValidacao['lc95_1998']['conforme'],
                    'estrutura_adequada' => $resultadoValidacao['estrutura_textual']['adequada'],
                    'total_erros' => $resultadoValidacao['resumo']['total_erros'],
                    'total_avisos' => $resultadoValidacao['resumo']['total_avisos'],
                    'qualidade' => $resultadoValidacao['resumo']['qualidade_percentual']
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao validar template', [
                'tipo_id' => $tipo->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar status dos templates (quantos foram criados automaticamente)
     */
    public function status()
    {
        $tiposTotal = TipoProposicao::where('ativo', true)->count();
        $templatesTotal = TipoProposicaoTemplate::count();
        $templatesSemArquivo = TipoProposicaoTemplate::whereNull('arquivo_path')->count();
        
        return response()->json([
            'tipos_total' => $tiposTotal,
            'templates_total' => $templatesTotal,
            'templates_sem_arquivo' => $templatesSemArquivo,
            'cobertura_percentual' => $tiposTotal > 0 ? round(($templatesTotal / $tiposTotal) * 100, 1) : 0,
            'parametros_count' => count($this->parametrosService->obterParametrosTemplates())
        ]);
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
            
            // Processar o template com variáveis padrão
            return $this->parametrosService->processarTemplate($conteudo, []);
            
        } catch (\Exception $e) {
            \Log::warning('Erro ao processar variáveis do template:', [
                'error' => $e->getMessage()
            ]);
            
            // Se houver erro, retornar conteúdo original
            return $conteudo;
        }
    }

    /**
     * Gerar ementa de exemplo baseada no tipo de proposição
     */
    private function gerarEmentaExemplo(TipoProposicao $tipo): string
    {
        $tipoLower = strtolower($tipo->codigo);
        
        return match($tipoLower) {
            'projeto_lei_ordinaria' => 'Dispõe sobre [ASSUNTO] no âmbito do Município e dá outras providências.',
            'projeto_lei_complementar' => 'Altera a Lei Orgânica Municipal para [FINALIDADE] e dá outras providências.',
            'indicacao' => 'Indica ao Poder Executivo [SOLICITAÇÃO].',
            'requerimento' => 'Requer informações ao Poder Executivo sobre [ASSUNTO].',
            'mocao' => 'Moção de [TIPO] dirigida a [DESTINATÁRIO].',
            'projeto_resolucao' => 'Dispõe sobre matéria de competência da Câmara Municipal e dá outras providências.',
            'projeto_decreto_legislativo' => 'Aprova [ASSUNTO] e dá outras providências.',
            default => 'Dispõe sobre [ASSUNTO] e dá outras providências.'
        };
    }

    /**
     * Gerar texto de exemplo baseado no tipo de proposição
     */
    private function gerarTextoExemplo(TipoProposicao $tipo): string
    {
        $tipoLower = strtolower($tipo->codigo);
        
        $textoBase = "Art. 1º [Disposição principal da proposição].\n\n";
        
        if (str_contains($tipoLower, 'lei')) {
            $textoBase .= "Parágrafo único. [Detalhamento ou exceção].\n\n";
            $textoBase .= "Art. 2º [Disposição complementar].\n\n";
            $textoBase .= "Art. 3º Esta lei entra em vigor na data de sua publicação.";
        } elseif ($tipoLower === 'indicacao') {
            $textoBase = "Indico ao Senhor Prefeito Municipal que:\n\n";
            $textoBase .= "I - [Primeira solicitação];\n\n";
            $textoBase .= "II - [Segunda solicitação];\n\n";
            $textoBase .= "III - [Terceira solicitação].";
        } elseif ($tipoLower === 'requerimento') {
            $textoBase = "Requeiro, nos termos regimentais, que seja solicitado ao Poder Executivo:\n\n";
            $textoBase .= "a) [Primeira informação];\n\n";
            $textoBase .= "b) [Segunda informação];\n\n";
            $textoBase .= "c) [Terceira informação].";
        } elseif ($tipoLower === 'mocao') {
            $textoBase = "A Câmara Municipal manifesta [POSICIONAMENTO] em relação a [ASSUNTO].\n\n";
            $textoBase .= "Considerando que [CONSIDERANDO 1];\n\n";
            $textoBase .= "Considerando que [CONSIDERANDO 2];\n\n";
            $textoBase .= "Resolve dirigir a presente Moção.";
        }
        
        return $textoBase;
    }

    /**
     * Adicionar variáveis no template gerado
     */
    private function adicionarVariaveisTemplate(string $template): string
    {
        // Substituir valores fixos por variáveis
        $substituicoes = [
            '/\b\d+\/\d{4}\b/' => '${numero_proposicao}/${ano}',
            '/Vereador\(a\)/' => '${autor_nome}',
            '/\[ASSUNTO\]/' => '${ementa}',
            '/\[OBJETO\]/' => '${texto}',
            '/\[FINALIDADE\]/' => '${justificativa}',
            '/\[SOLICITAÇÃO\]/' => '${texto}',
            '/\[TIPO\]/' => '${tipo_mocao}',
            '/\[DESTINATÁRIO\]/' => '${destinatario}',
            '/\[POSICIONAMENTO\]/' => '${posicionamento}',
            '/\[CONSIDERANDO \d+\]/' => '${considerandos}'
        ];

        foreach ($substituicoes as $padrao => $variavel) {
            $template = preg_replace($padrao, $variavel, $template);
        }

        // Adicionar cabeçalho com variáveis
        $cabecalho = "\n${imagem_cabecalho}\n\n${nome_camara}\n${endereco_camara}\n\n";
        
        // Adicionar rodapé com variáveis
        $rodape = "\n\n${assinatura_padrao}\n\n${rodape}";

        return $cabecalho . $template . $rodape;
    }

    /**
     * Converter texto para RTF com formatação usando UTF-8 correto
     */
    private function converterParaRTF(string $texto, TipoProposicao $tipo): string
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        
        $fonte = $parametros['Formatação.format_fonte'] ?? 'Arial';
        $tamanhoFonte = (int)($parametros['Formatação.format_tamanho_fonte'] ?? 12);
        $espacamento = $parametros['Formatação.format_espacamento'] ?? '1.5';
        
        // Converter espaçamento para RTF (1.5 = 360 twips)
        $espacamentoRTF = match($espacamento) {
            '1' => 'sl240',
            '1.5' => 'sl360',
            '2' => 'sl480',
            default => 'sl360'
        };

        // Cabeçalho RTF com UTF-8 correto
        $rtf = "{\\rtf1\\ansi\\ansicpg65001\\deff0 {\\fonttbl {\\f0 {$fonte};}}";
        $rtf .= "\\f0\\fs" . ($tamanhoFonte * 2); // RTF usa half-points
        $rtf .= "\\{$espacamentoRTF}\\slmult1 ";

        // Converter texto para Unicode RTF usando funções multi-byte
        $textoConvertido = $this->converterUtf8ParaRtf($texto);
        
        // Aplicar formatação em negrito para artigos
        $textoConvertido = preg_replace('/(Art\\\\\. \\\\u\d+\\\\\*º?)/', '{\\\\b $1 \\\\b0}', $textoConvertido);
        
        $rtf .= $textoConvertido;
        $rtf .= "}";

        return $rtf;
    }

    /**
     * Converter texto UTF-8 para RTF com sequências Unicode corretas
     * Baseado na solução documentada em docs/SOLUCAO_ACENTUACAO_ONLYOFFICE.md
     */
    private function converterUtf8ParaRtf(string $texto): string
    {
        $textoProcessado = '';
        
        // Escapar caracteres especiais do RTF primeiro
        $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);
        
        // Processar caractere por caractere usando funções multi-byte
        $length = mb_strlen($texto, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');  // Extrai caractere UTF-8 corretamente
            $codepoint = mb_ord($char, 'UTF-8');        // Obtém codepoint Unicode real
            
            if ($codepoint > 127) {
                // Gera sequência RTF Unicode correta
                $textoProcessado .= '\\u' . $codepoint . '*';
            } else {
                // Converter quebras de linha para RTF
                if ($char === "\n") {
                    $textoProcessado .= '\\par ';
                } else {
                    $textoProcessado .= $char;
                }
            }
        }
        
        return $textoProcessado;
    }

    /**
     * Extrair texto limpo de RTF (simplificado)
     */
    private function extrairTextoRTF(string $rtfContent): string
    {
        // Remove códigos RTF básicos
        $texto = preg_replace('/\{\\\\[^}]*\}/', '', $rtfContent);
        $texto = preg_replace('/\\\\[a-zA-Z]+\d*\s?/', '', $texto);
        $texto = preg_replace('/\{|\}/', '', $texto);
        $texto = str_replace('\\par', "\n", $texto);
        $texto = trim($texto);
        
        return $texto;
    }
    
    /**
     * Gerar preview do template com dados de exemplo
     */
    public function previewTemplate(TipoProposicao $tipo)
    {
        try {
            $template = $tipo->template;
            
            if (!$template || !$template->arquivo_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template não encontrado'
                ], 404);
            }

            // Ler conteúdo do template
            $conteudoTemplate = Storage::get($template->arquivo_path);
            
            // Dados de exemplo para preview
            $dadosExemplo = [
                'numero_proposicao' => '001/' . date('Y'),
                'tipo_proposicao' => $tipo->nome,
                'ementa' => $this->gerarEmentaExemplo($tipo),
                'texto' => $this->gerarTextoExemplo($tipo),
                'justificativa' => 'Esta é uma justificativa de exemplo para demonstrar como a variável será substituída no template.',
                'autor_nome' => 'João Silva',
                'autor_cargo' => 'Vereador',
                'autor_partido' => 'PARTIDO',
                'data_atual' => date('d/m/Y'),
                'ano' => date('Y'),
                'nome_camara' => 'Câmara Municipal de São Paulo',
                'municipio' => 'São Paulo',
                'endereco_camara' => 'Viaduto Jacareí, 100 - Bela Vista, São Paulo - SP',
                'telefone_camara' => '(11) 3396-4000',
                'website_camara' => 'www.camara.sp.gov.br',
                'imagem_cabecalho' => '[LOGO DA CÂMARA]',
                'assinatura_padrao' => "\n_____________________________\nJoão Silva\nVereador",
                'rodape' => 'Câmara Municipal de São Paulo'
            ];

            // Processar template com dados de exemplo
            $conteudoProcessado = $this->parametrosService->processarTemplate($conteudoTemplate, $dadosExemplo);
            
            // Se for RTF, extrair texto para preview
            if (pathinfo($template->arquivo_path, PATHINFO_EXTENSION) === 'rtf') {
                $textoLimpo = $this->extrairTextoRTF($conteudoProcessado);
            } else {
                $textoLimpo = $conteudoProcessado;
            }

            return response()->json([
                'success' => true,
                'preview' => [
                    'conteudo' => $textoLimpo,
                    'dados_exemplo' => $dadosExemplo,
                    'tipo' => $tipo->nome,
                    'arquivo_tipo' => pathinfo($template->arquivo_path, PATHINFO_EXTENSION)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar preview do template', [
                'tipo_id' => $tipo->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar preview: ' . $e->getMessage()
            ], 500);
        }
    }
}