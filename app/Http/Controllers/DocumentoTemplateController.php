<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTemplate;
use App\Models\TipoProposicao;
use App\Services\Template\TemplateDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoTemplateController extends Controller
{
    public function __construct(
        private TemplateDocumentService $templateDocumentService
    ) {}

    /**
     * Lista todos os templates
     */
    public function index()
    {
        $templates = DocumentoTemplate::with(['tipoProposicao', 'createdBy', 'variaveis'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $tipos = TipoProposicao::orderBy('nome')->get();

        return view('admin.documento-templates.index', compact('templates', 'tipos'));
    }

    /**
     * Exibe formulário para criar novo template
     */
    public function create()
    {
        $tipos = TipoProposicao::orderBy('nome')->get();
        return view('admin.documento-templates.create', compact('tipos'));
    }

    /**
     * Salva novo template
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'required|exists:tipo_proposicoes,id',
            'arquivo' => 'required|file|mimes:docx,doc,rtf|max:10240' // 10MB
        ]);

        try {
            $template = $this->templateDocumentService->criarTemplate(
                $request->only(['nome', 'descricao', 'tipo_proposicao_id']),
                $request->file('arquivo')
            );

            return redirect()->route('documento-templates.show', $template)
                ->with('success', 'Template criado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar template', [
                'erro' => $e->getMessage(),
                'dados' => $request->except('arquivo')
            ]);

            return back()->withErrors('Erro ao criar template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exibe detalhes do template
     */
    public function show(DocumentoTemplate $documentoTemplate)
    {
        $documentoTemplate->load(['tipoProposicao', 'createdBy', 'variaveis', 'instances']);
        
        return view('admin.documento-templates.show', [
            'template' => $documentoTemplate
        ]);
    }

    /**
     * Exibe formulário para editar template
     */
    public function edit(DocumentoTemplate $documentoTemplate)
    {
        $tipos = TipoProposicao::orderBy('nome')->get();
        
        return view('admin.documento-templates.edit', [
            'template' => $documentoTemplate,
            'tipos' => $tipos
        ]);
    }

    /**
     * Atualiza template
     */
    public function update(Request $request, DocumentoTemplate $documentoTemplate)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'arquivo' => 'nullable|file|mimes:docx,doc,rtf|max:10240' // 10MB
        ]);

        try {
            $template = $this->templateDocumentService->atualizarTemplate(
                $documentoTemplate,
                $request->only(['nome', 'descricao']),
                $request->file('arquivo')
            );

            return redirect()->route('documento-templates.show', $template)
                ->with('success', 'Template atualizado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar template', [
                'template_id' => $documentoTemplate->id,
                'erro' => $e->getMessage()
            ]);

            return back()->withErrors('Erro ao atualizar template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove template
     */
    public function destroy(DocumentoTemplate $documentoTemplate)
    {
        try {
            $nomeTemplate = $documentoTemplate->nome;
            
            if ($this->templateDocumentService->excluirTemplate($documentoTemplate)) {
                return redirect()->route('documento-templates.index')
                    ->with('success', "Template '{$nomeTemplate}' foi removido com sucesso.");
            } else {
                return back()->withErrors('Erro ao remover template.');
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao excluir template', [
                'template_id' => $documentoTemplate->id,
                'erro' => $e->getMessage()
            ]);

            return back()->withErrors('Erro ao remover template: ' . $e->getMessage());
        }
    }

    /**
     * Download do arquivo original do template
     */
    public function download(DocumentoTemplate $documentoTemplate)
    {
        if (!$documentoTemplate->arquivo_original_path || !Storage::exists($documentoTemplate->arquivo_original_path)) {
            abort(404, 'Arquivo do template não encontrado');
        }

        $nomeArquivo = \Illuminate\Support\Str::slug($documentoTemplate->nome) . '.docx';
        
        return response()->download(
            Storage::path($documentoTemplate->arquivo_original_path), 
            $nomeArquivo,
            ['Cache-Control' => 'no-cache, no-store, must-revalidate']
        );
    }

    /**
     * Preview do template (para OnlyOffice)
     */
    public function preview(DocumentoTemplate $documentoTemplate)
    {
        if (!$documentoTemplate->arquivo_modelo_path || !Storage::exists($documentoTemplate->arquivo_modelo_path)) {
            abort(404, 'Arquivo modelo do template não encontrado');
        }

        // Configuração básica do OnlyOffice para preview
        $config = [
            "document" => [
                "fileType" => "docx",
                "key" => 'preview_' . $documentoTemplate->id . '_' . time(),
                "title" => "Preview: " . $documentoTemplate->nome,
                "url" => route('documento-templates.serve', $documentoTemplate->id),
            ],
            "editorConfig" => [
                "mode" => "view", // Apenas visualização
                "lang" => "pt-BR"
            ]
        ];

        return view('admin.documento-templates.preview', [
            'template' => $documentoTemplate,
            'config' => $config
        ]);
    }

    /**
     * Serve arquivo para OnlyOffice
     */
    public function serve(DocumentoTemplate $documentoTemplate)
    {
        if (!$documentoTemplate->arquivo_modelo_path || !Storage::exists($documentoTemplate->arquivo_modelo_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        $arquivo = Storage::get($documentoTemplate->arquivo_modelo_path);
        
        return response($arquivo, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline; filename="template.docx"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }

    /**
     * Ativar/Desativar template
     */
    public function toggleStatus(DocumentoTemplate $documentoTemplate)
    {
        $documentoTemplate->update(['ativo' => !$documentoTemplate->ativo]);
        
        $status = $documentoTemplate->ativo ? 'ativado' : 'desativado';
        
        return back()->with('success', "Template {$status} com sucesso.");
    }

    /**
     * Duplicar template
     */
    public function duplicate(DocumentoTemplate $documentoTemplate)
    {
        try {
            if (!$documentoTemplate->arquivo_original_path || !Storage::exists($documentoTemplate->arquivo_original_path)) {
                return back()->withErrors('Arquivo original do template não encontrado.');
            }

            // Criar cópia do arquivo
            $arquivoOriginal = storage_path('app/' . $documentoTemplate->arquivo_original_path);
            $novoNome = 'Cópia de ' . $documentoTemplate->nome;
            
            // Simular UploadedFile
            $tempFile = tempnam(sys_get_temp_dir(), 'template_copy');
            copy($arquivoOriginal, $tempFile);
            
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                basename($documentoTemplate->arquivo_original_path),
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                null,
                true
            );

            $novoTemplate = $this->templateDocumentService->criarTemplate([
                'nome' => $novoNome,
                'descricao' => $documentoTemplate->descricao,
                'tipo_proposicao_id' => $documentoTemplate->tipo_proposicao_id
            ], $uploadedFile);

            return redirect()->route('documento-templates.show', $novoTemplate)
                ->with('success', 'Template duplicado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao duplicar template', [
                'template_id' => $documentoTemplate->id,
                'erro' => $e->getMessage()
            ]);

            return back()->withErrors('Erro ao duplicar template: ' . $e->getMessage());
        }
    }
}