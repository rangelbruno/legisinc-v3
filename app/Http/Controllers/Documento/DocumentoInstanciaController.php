<?php

namespace App\Http\Controllers\Documento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento\DocumentoInstancia;
use App\Models\Documento\DocumentoVersao;
use App\Services\Documento\DocumentoService;
use App\Services\Documento\VariavelService;

class DocumentoInstanciaController extends Controller
{
    public function __construct(
        private DocumentoService $documentoService,
        private VariavelService $variavelService
    ) {}

    public function index(Request $request)
    {
        $query = DocumentoInstancia::with(['projeto', 'modelo', 'creator']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('modelo_id') && $request->modelo_id !== '') {
            $query->where('modelo_id', $request->modelo_id);
        }

        $instancias = $query->orderBy('updated_at', 'desc')->paginate(15);

        return view('documentos.instancias.index', compact('instancias'));
    }

    public function show(DocumentoInstancia $instancia)
    {
        $instancia->load([
            'projeto.tipoProposicao',
            'modelo',
            'versoes.modificador',
            'creator',
            'updater'
        ]);

        $versoes = $instancia->versoes()->ordenadaPorVersao()->get();

        return view('documentos.instancias.show', compact('instancia', 'versoes'));
    }

    public function uploadVersao(Request $request, DocumentoInstancia $instancia)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:docx|max:10240',
            'comentarios' => 'nullable|string|max:1000'
        ]);

        try {
            $arquivo = $request->file('arquivo');
            
            $errorsValidacao = $this->variavelService->validarFormatoDocumento($arquivo);
            if (!empty($errorsValidacao)) {
                return response()->json(['errors' => $errorsValidacao], 422);
            }

            $versao = $this->documentoService->uploadVersaoDocumento(
                $instancia,
                $arquivo,
                $request->comentarios
            );

            return response()->json([
                'success' => true,
                'message' => 'Nova versão enviada com sucesso!',
                'versao' => [
                    'numero' => $versao->versao,
                    'arquivo_nome' => $versao->arquivo_nome,
                    'modificado_por' => $versao->modificador->name,
                    'data' => $versao->data_modificacao_formatada,
                    'comentarios' => $versao->comentarios
                ]
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao fazer upload de versão: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function downloadVersao(DocumentoVersao $versao)
    {
        try {
            $caminhoArquivo = storage_path('app/' . $versao->arquivo_path);
            
            if (!file_exists($caminhoArquivo)) {
                return back()->withErrors(['Arquivo não encontrado.']);
            }

            return response()->download($caminhoArquivo, $versao->arquivo_nome);
            
        } catch (\Exception $e) {
            // Log::error('Erro ao baixar versão: ' . $e->getMessage());
            return back()->withErrors(['Erro ao baixar arquivo.']);
        }
    }

    public function download(DocumentoInstancia $instancia)
    {
        try {
            if (!$instancia->arquivo_path) {
                return back()->withErrors(['Nenhum arquivo associado a esta instância.']);
            }

            $caminhoArquivo = storage_path('app/' . $instancia->arquivo_path);
            
            if (!file_exists($caminhoArquivo)) {
                return back()->withErrors(['Arquivo não encontrado.']);
            }

            return response()->download($caminhoArquivo, $instancia->arquivo_nome);
            
        } catch (\Exception $e) {
            // Log::error('Erro ao baixar instância: ' . $e->getMessage());
            return back()->withErrors(['Erro ao baixar arquivo.']);
        }
    }

    public function gerarPDF(DocumentoInstancia $instancia)
    {
        try {
            $caminhoPdf = $this->documentoService->converterParaPDF($instancia);
            
            $instancia->update(['status' => DocumentoInstancia::STATUS_FINALIZADO]);
            
            return response()->download($caminhoPdf);
            
        } catch (\Exception $e) {
            // Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            return back()->withErrors(['Erro ao gerar PDF: ' . $e->getMessage()]);
        }
    }

    public function finalizar(DocumentoInstancia $instancia)
    {
        try {
            $sucesso = $this->documentoService->finalizarDocumento($instancia);
            
            if ($sucesso) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento finalizado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao finalizar documento'
                ], 500);
            }
            
        } catch (\Exception $e) {
            // Log::error('Erro ao finalizar documento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function alterarStatus(Request $request, DocumentoInstancia $instancia)
    {
        $request->validate([
            'status' => 'required|in:rascunho,parlamentar,legislativo,finalizado'
        ]);

        try {
            $statusAnterior = $instancia->status;
            
            $instancia->update([
                'status' => $request->status,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status alterado de '{$statusAnterior}' para '{$request->status}'",
                'status_formatado' => $instancia->status_formatado
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao alterar status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function destroy(DocumentoInstancia $instancia)
    {
        try {
            $sucesso = $this->documentoService->excluirInstancia($instancia);
            
            if ($sucesso) {
                return redirect()->route('documentos.instancias.index')
                               ->with('success', 'Documento excluído com sucesso!');
            } else {
                return back()->withErrors(['Erro ao excluir documento.']);
            }
            
        } catch (\Exception $e) {
            // Log::error('Erro ao excluir instância: ' . $e->getMessage());
            return back()->withErrors(['Erro interno do servidor.']);
        }
    }

    public function versoes(DocumentoInstancia $instancia)
    {
        $versoes = $instancia->versoes()
            ->with('modificador')
            ->ordenadaPorVersao()
            ->paginate(10);

        return view('documentos.instancias.versoes', compact('instancia', 'versoes'));
    }

    public function compararVersoes(DocumentoVersao $versao1, DocumentoVersao $versao2)
    {
        if ($versao1->instancia_id !== $versao2->instancia_id) {
            return back()->withErrors(['As versões devem ser da mesma instância.']);
        }

        return view('documentos.instancias.comparar', compact('versao1', 'versao2'));
    }
}
