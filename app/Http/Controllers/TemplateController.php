<?php

namespace App\Http\Controllers;

use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class TemplateController extends Controller
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService,
        private TemplateParametrosService $parametrosService
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
        
        // Retornar arquivo com headers corretos
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
            // Executar comando de geração automática
            Artisan::call('templates:gerar-automaticos', ['--force' => true]);
            
            $output = Artisan::output();
            
            \Log::info('Templates regenerados automaticamente', [
                'user_id' => auth()->id(),
                'output' => $output
            ]);

            // Contar templates criados/atualizados
            $totalTemplates = TipoProposicaoTemplate::count();
            
            return redirect()->route('templates.index')
                            ->with('success', "Todos os templates foram regenerados com os parâmetros atualizados! Total: {$totalTemplates} templates.");

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
}