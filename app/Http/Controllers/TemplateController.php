<?php

namespace App\Http\Controllers;

use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService
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
                'document_key' => 'template_' . $tipo->id . '_' . time(),
                'updated_by' => auth()->id()
            ]
        );

        // Sempre gerar novo document_key para nova sessão de edição
        $template->update([
            'document_key' => 'template_' . $tipo->id . '_' . time() . '_' . auth()->id()
        ]);

        // Configuração do ONLYOFFICE
        $config = $this->onlyOfficeService->criarConfiguracaoTemplate($template);

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

        if (!Storage::exists($template->arquivo_path)) {
            \Log::error('Arquivo do template não encontrado', [
                'template_id' => $template->id,
                'path' => $template->arquivo_path
            ]);
            abort(404, 'Arquivo do template não encontrado');
        }

        // Gerar nome do arquivo baseado no tipo de proposição
        $nomeArquivo = \Illuminate\Support\Str::slug($template->tipoProposicao->nome) . '.rtf';
        
        return Storage::download($template->arquivo_path, $nomeArquivo);
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
}