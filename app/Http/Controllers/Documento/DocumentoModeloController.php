<?php

namespace App\Http\Controllers\Documento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento\DocumentoModelo;
use App\Models\TipoProposicao;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentoModeloController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentoModelo::with(['tipoProposicao', 'creator']);

        if ($request->has('tipo') && $request->tipo !== '') {
            $query->where('tipo_proposicao_id', $request->tipo);
        }

        if ($request->has('ativo') && $request->ativo !== '') {
            $query->where('ativo', $request->ativo);
        }

        $modelos = $query->orderBy('nome')->paginate(15);
        $tipos = TipoProposicao::orderBy('nome')->get();

        return view('documentos.modelos.index', compact('modelos', 'tipos'));
    }

    public function create()
    {
        $tipos = TipoProposicao::where('ativo', true)->orderBy('nome')->get();
        return view('documentos.modelos.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'required|exists:tipo_proposicoes,id',
            'arquivo' => 'required|file|mimes:docx,doc,pdf',
            'ativo' => 'boolean'
        ]);

        $modelo = new DocumentoModelo();
        $modelo->nome = $request->nome;
        $modelo->descricao = $request->descricao;
        $modelo->tipo_proposicao_id = $request->tipo_proposicao_id;
        $modelo->ativo = $request->has('ativo');
        $modelo->created_by = Auth::id();

        if ($request->hasFile('arquivo')) {
            $file = $request->file('arquivo');
            $path = $file->store('modelos', 'public');
            $modelo->arquivo_path = $path;
            $modelo->arquivo_nome = $file->getClientOriginalName();
            $modelo->arquivo_size = $file->getSize();
        }

        $modelo->save();

        return redirect()->route('documentos.modelos.index')
            ->with('success', 'Modelo criado com sucesso!');
    }

    public function show(DocumentoModelo $modelo)
    {
        $modelo->load(['tipoProposicao', 'creator']);
        return view('documentos.modelos.show', compact('modelo'));
    }

    public function edit(DocumentoModelo $modelo)
    {
        $tipos = TipoProposicao::where('ativo', true)->orderBy('nome')->get();
        return view('documentos.modelos.edit', compact('modelo', 'tipos'));
    }

    public function update(Request $request, DocumentoModelo $modelo)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'required|exists:tipo_proposicoes,id',
            'arquivo' => 'nullable|file|mimes:docx,doc,pdf',
            'ativo' => 'boolean'
        ]);

        $modelo->nome = $request->nome;
        $modelo->descricao = $request->descricao;
        $modelo->tipo_proposicao_id = $request->tipo_proposicao_id;
        $modelo->ativo = $request->has('ativo');

        if ($request->hasFile('arquivo')) {
            // Remove old file if exists
            if ($modelo->arquivo_path && Storage::disk('public')->exists($modelo->arquivo_path)) {
                Storage::disk('public')->delete($modelo->arquivo_path);
            }

            $file = $request->file('arquivo');
            $path = $file->store('modelos', 'public');
            $modelo->arquivo_path = $path;
            $modelo->arquivo_nome = $file->getClientOriginalName();
            $modelo->arquivo_size = $file->getSize();
        }

        $modelo->save();

        return redirect()->route('documentos.modelos.index')
            ->with('success', 'Modelo atualizado com sucesso!');
    }

    public function destroy(DocumentoModelo $modelo)
    {
        // Remove file if exists
        if ($modelo->arquivo_path && Storage::disk('public')->exists($modelo->arquivo_path)) {
            Storage::disk('public')->delete($modelo->arquivo_path);
        }

        $modelo->delete();

        return redirect()->route('documentos.modelos.index')
            ->with('success', 'Modelo removido com sucesso!');
    }

    public function download(DocumentoModelo $modelo)
    {
        if (!$modelo->arquivo_path || !Storage::disk('public')->exists($modelo->arquivo_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return Storage::disk('public')->download($modelo->arquivo_path, $modelo->arquivo_nome);
    }

    public function createOnlyOffice()
    {
        $tipos = TipoProposicao::where('ativo', true)->orderBy('nome')->get();
        return view('documentos.modelos.create-onlyoffice', compact('tipos'));
    }

    public function storeOnlyOffice(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'required|exists:tipo_proposicoes,id',
        ]);

        $modelo = new DocumentoModelo();
        $modelo->nome = $request->nome;
        $modelo->descricao = $request->descricao;
        $modelo->tipo_proposicao_id = $request->tipo_proposicao_id;
        $modelo->ativo = true;
        $modelo->created_by = Auth::id();
        $modelo->document_key = uniqid('modelo_', true);

        $modelo->save();

        return redirect()->route('documentos.modelos.editor-onlyoffice', $modelo)
            ->with('success', 'Modelo criado! Agora você pode editá-lo.');
    }

    public function editorOnlyOffice(DocumentoModelo $modelo)
    {
        return view('documentos.modelos.editor-onlyoffice', compact('modelo'));
    }

    public function lastUpdate(DocumentoModelo $modelo)
    {
        return response()->json([
            'last_update' => $modelo->updated_at->timestamp
        ]);
    }

    // API Methods
    public function apiList(Request $request, $tipoProposicaoId = null)
    {
        $query = DocumentoModelo::where('ativo', true);

        if ($tipoProposicaoId) {
            $query->where('tipo_proposicao_id', $tipoProposicaoId);
        }

        $modelos = $query->orderBy('nome')->get(['id', 'nome', 'descricao', 'tipo_proposicao_id']);

        return response()->json($modelos);
    }
}