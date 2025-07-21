<?php

namespace App\Http\Controllers\Documento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento\DocumentoModelo;
use App\Models\TipoProposicao;
use App\Models\Projeto;
use App\Services\Documento\DocumentoService;
use App\Services\Documento\VariavelService;

class DocumentoModeloController extends Controller
{
    public function __construct(
        private DocumentoService $documentoService,
        private VariavelService $variavelService
    ) {}

    public function index()
    {
        $modelos = DocumentoModelo::with(['tipoProposicao', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('documentos.modelos.index', compact('modelos'));
    }

    public function create()
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        
        return view('documentos.modelos.create', compact('tiposProposicao'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'arquivo' => 'required|file|mimes:docx|max:10240' // 10MB
        ]);

        try {
            $arquivo = $request->file('arquivo');
            
            $errorsValidacao = $this->variavelService->validarFormatoDocumento($arquivo);
            if (!empty($errorsValidacao)) {
                return back()->withErrors($errorsValidacao)->withInput();
            }

            $path = $arquivo->store('documentos/modelos');
            
            $variaveis = $this->variavelService->extrairVariaveisDeUpload($arquivo);
            
            $modelo = DocumentoModelo::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo_proposicao_id' => $request->tipo_proposicao_id,
                'arquivo_path' => $path,
                'arquivo_nome' => $arquivo->getClientOriginalName(),
                'arquivo_size' => $arquivo->getSize(),
                'variaveis' => $variaveis,
                'versao' => '1.0',
                'created_by' => auth()->id()
            ]);

            return redirect()->route('documentos.modelos.index')
                           ->with('success', 'Modelo criado com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao criar modelo de documento: ' . $e->getMessage());
            return back()->withErrors(['Erro interno do servidor'])->withInput();
        }
    }

    public function show(DocumentoModelo $modelo)
    {
        $modelo->load(['tipoProposicao', 'creator', 'instancias.projeto']);
        $variaveisFormatadas = $this->variavelService->formatarVariaveisParaExibicao($modelo->variaveis ?? []);
        
        return view('documentos.modelos.show', compact('modelo', 'variaveisFormatadas'));
    }

    public function edit(DocumentoModelo $modelo)
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        $variaveisFormatadas = $this->variavelService->formatarVariaveisParaExibicao($modelo->variaveis ?? []);
        
        return view('documentos.modelos.edit', compact('modelo', 'tiposProposicao', 'variaveisFormatadas'));
    }

    public function update(Request $request, DocumentoModelo $modelo)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'ativo' => 'boolean',
            'arquivo' => 'nullable|file|mimes:docx|max:10240'
        ]);

        try {
            $dadosAtualizacao = [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo_proposicao_id' => $request->tipo_proposicao_id,
                'ativo' => $request->boolean('ativo', true)
            ];

            if ($request->hasFile('arquivo')) {
                $arquivo = $request->file('arquivo');
                
                $errorsValidacao = $this->variavelService->validarFormatoDocumento($arquivo);
                if (!empty($errorsValidacao)) {
                    return back()->withErrors($errorsValidacao)->withInput();
                }

                if ($modelo->arquivo_path && \Storage::exists($modelo->arquivo_path)) {
                    \Storage::delete($modelo->arquivo_path);
                }

                $path = $arquivo->store('documentos/modelos');
                $variaveis = $this->variavelService->extrairVariaveisDeUpload($arquivo);
                
                $dadosAtualizacao = array_merge($dadosAtualizacao, [
                    'arquivo_path' => $path,
                    'arquivo_nome' => $arquivo->getClientOriginalName(),
                    'arquivo_size' => $arquivo->getSize(),
                    'variaveis' => $variaveis,
                    'versao' => $this->incrementarVersao($modelo->versao)
                ]);
            }

            $modelo->update($dadosAtualizacao);

            return redirect()->route('documentos.modelos.show', $modelo)
                           ->with('success', 'Modelo atualizado com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar modelo de documento: ' . $e->getMessage());
            return back()->withErrors(['Erro interno do servidor'])->withInput();
        }
    }

    public function destroy(DocumentoModelo $modelo)
    {
        try {
            if ($modelo->instancias()->exists()) {
                return back()->withErrors(['Este modelo possui documentos associados e não pode ser excluído.']);
            }

            if ($modelo->arquivo_path && \Storage::exists($modelo->arquivo_path)) {
                \Storage::delete($modelo->arquivo_path);
            }

            $modelo->delete();

            return redirect()->route('documentos.modelos.index')
                           ->with('success', 'Modelo excluído com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir modelo de documento: ' . $e->getMessage());
            return back()->withErrors(['Erro interno do servidor']);
        }
    }

    public function downloadModelo(DocumentoModelo $modelo, Projeto $projeto)
    {
        try {
            $instancia = $this->documentoService->criarInstanciaDocumento($projeto->id, $modelo->id);
            $caminhoArquivo = $this->documentoService->gerarDocumentoComVariaveis($instancia);
            
            return response()->download($caminhoArquivo, $instancia->arquivo_nome);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar documento: ' . $e->getMessage());
            return back()->withErrors(['Erro ao gerar documento: ' . $e->getMessage()]);
        }
    }

    public function download(DocumentoModelo $modelo)
    {
        try {
            $caminhoArquivo = storage_path('app/' . $modelo->arquivo_path);
            
            if (!file_exists($caminhoArquivo)) {
                return back()->withErrors(['Arquivo modelo não encontrado.']);
            }

            return response()->download($caminhoArquivo, $modelo->arquivo_nome);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao baixar modelo: ' . $e->getMessage());
            return back()->withErrors(['Erro ao baixar arquivo.']);
        }
    }

    private function incrementarVersao(string $versaoAtual): string
    {
        $partes = explode('.', $versaoAtual);
        $major = (int) ($partes[0] ?? 1);
        $minor = (int) ($partes[1] ?? 0);
        
        $minor++;
        
        return $major . '.' . $minor;
    }
}
