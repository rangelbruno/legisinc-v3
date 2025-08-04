<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProposicaoAssinaturaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Lista proposições aguardando assinatura do parlamentar
     */
    public function index()
    {
        $proposicoes = Proposicao::where('autor_id', Auth::id())
            ->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('proposicoes.assinatura.index', compact('proposicoes'));
    }

    /**
     * Tela para assinatura da proposição aprovada
     */
    public function assinar(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
            abort(403, 'Proposição não está disponível para assinatura.');
        }

        // Verificar se precisa gerar PDF
        $pdfPath = $proposicao->arquivo_pdf_path ? storage_path('app/' . $proposicao->arquivo_pdf_path) : null;
        if (!$proposicao->arquivo_pdf_path || !file_exists($pdfPath)) {
            try {
                $this->gerarPDFParaAssinatura($proposicao);
            } catch (\Exception $e) {
                \Log::warning('Não foi possível gerar PDF para assinatura', [
                    'proposicao_id' => $proposicao->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('proposicoes.assinatura.assinar', compact('proposicao'));
    }

    /**
     * Tela para correção da proposição devolvida
     */
    public function corrigir(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'devolvido_correcao') {
            abort(403, 'Proposição não está disponível para correção.');
        }

        return view('proposicoes.assinatura.corrigir', compact('proposicao'));
    }

    /**
     * Confirmar leitura da proposição
     */
    public function confirmarLeitura(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        $proposicao->update([
            'confirmacao_leitura' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leitura confirmada!'
        ]);
    }

    /**
     * Processar assinatura digital
     */
    public function processarAssinatura(Request $request, Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'assinatura_digital' => 'required|string',
            'certificado_digital' => 'nullable|string',
        ]);

        if (!$proposicao->confirmacao_leitura) {
            return response()->json([
                'success' => false,
                'message' => 'É necessário confirmar a leitura do documento antes de assinar.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'assinado',
            'assinatura_digital' => $request->assinatura_digital,
            'certificado_digital' => $request->certificado_digital,
            'data_assinatura' => now(),
            'ip_assinatura' => $request->ip(),
        ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Proposição assinada digitalmente',
        //     'aprovado_assinatura',
        //     'assinado'
        // );

        // Enviar automaticamente para protocolo após assinatura
        $proposicao->update([
            'status' => 'enviado_protocolo'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição assinada e enviada para protocolo com sucesso!'
        ]);
    }

    /**
     * Reenviar proposição para protocolo (caso necessário)
     */
    public function enviarProtocolo(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if (!in_array($proposicao->status, ['assinado', 'protocolado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar assinada para ser enviada ao protocolo.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'enviado_protocolo'
        ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Reenviado para protocolo',
        //     $proposicao->status,
        //     'enviado_protocolo'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Proposição reenviada para protocolo!'
        ]);
    }

    /**
     * Salvar correções na proposição devolvida
     */
    public function salvarCorrecoes(Request $request, Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        if ($proposicao->status !== 'devolvido_correcao') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para correção.'
            ], 400);
        }

        // Criar nova versão com as correções
        $proposicao->criarNovaVersao(
            $request->conteudo,
            'Correções baseadas no parecer técnico',
            'correcao'
        );

        return response()->json([
            'success' => true,
            'message' => 'Correções salvas com sucesso!'
        ]);
    }

    /**
     * Reenviar proposição para legislativo após correções
     */
    public function reenviarLegislativo(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'devolvido_correcao') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para reenvio.'
            ], 400);
        }

        // Limpar dados da revisão anterior
        $proposicao->update([
            'status' => 'enviado_legislativo',
            'revisor_id' => null,
            'analise_constitucionalidade' => null,
            'analise_juridicidade' => null,
            'analise_regimentalidade' => null,
            'analise_tecnica_legislativa' => null,
            'parecer_tecnico' => null,
            'tipo_retorno' => null,
            'observacoes_internas' => null,
            'data_revisao' => null,
        ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Reenviado para análise legislativa após correções',
        //     'devolvido_correcao',
        //     'enviado_legislativo'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Proposição reenviada para análise legislativa!'
        ]);
    }

    /**
     * Devolver proposição para legislativo com observações
     */
    public function devolverLegislativo(Request $request, Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'observacoes' => 'required|string|min:10',
        ], [
            'observacoes.required' => 'É obrigatório informar o motivo da devolução.',
            'observacoes.min' => 'As observações devem ter pelo menos 10 caracteres.'
        ]);

        if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para devolução.'
            ], 400);
        }

        // Atualizar status e salvar observações
        $proposicao->update([
            'status' => 'devolvido_correcao',
            'observacoes_retorno' => $request->observacoes,
            'data_retorno_legislativo' => now(),
        ]);

        \Log::info('Proposição devolvida para legislativo', [
            'proposicao_id' => $proposicao->id,
            'autor_devolucao' => Auth::user()->name,
            'observacoes' => $request->observacoes
        ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Devolvido pelo parlamentar para correções',
        //     $proposicao->status,
        //     'devolvido_correcao'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Proposição devolvida para o Legislativo com sucesso!',
            'redirect' => route('proposicoes.assinatura')
        ]);
    }

    /**
     * Histórico de assinaturas do parlamentar
     */
    public function historico()
    {
        $proposicoes = Proposicao::where('autor_id', Auth::id())
            ->whereNotNull('data_assinatura')
            ->orderBy('data_assinatura', 'desc')
            ->paginate(15);

        return view('proposicoes.assinatura.historico', compact('proposicoes'));
    }

    /**
     * Gerar PDF para assinatura se não existir
     */
    private function gerarPDFParaAssinatura(Proposicao $proposicao): void
    {
        // Determinar nome do PDF
        $nomePdf = 'proposicao_' . $proposicao->id . '.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        // Se não existe arquivo físico, usar conteúdo do banco de dados
        if (!$proposicao->arquivo_path || !\Storage::exists($proposicao->arquivo_path)) {
            \Log::info('Arquivo original não encontrado, gerando PDF do conteúdo do banco', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $proposicao->arquivo_path,
                'has_content' => !empty($proposicao->conteudo)
            ]);
            
            // Usar DomPDF diretamente
            $this->criarPDFExemplo($caminhoPdfAbsoluto, $proposicao);
            
            // Atualizar proposição com caminho do PDF
            $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
            $proposicao->save();
            
            return;
        }

        $caminhoArquivo = storage_path('app/' . $proposicao->arquivo_path);
        
        \Log::info('Gerando PDF para assinatura a partir de arquivo físico', [
            'proposicao_id' => $proposicao->id,
            'arquivo_path' => $proposicao->arquivo_path,
            'caminho_absoluto' => $caminhoArquivo
        ]);

        // Verificar se LibreOffice está disponível
        if (!$this->libreOfficeDisponivel()) {
            // Fallback: Criar um PDF de exemplo para demonstração
            \Log::warning('LibreOffice não disponível, criando PDF de exemplo');
            $this->criarPDFExemplo($caminhoPdfAbsoluto, $proposicao);
        } else {
            // Converter para PDF usando LibreOffice
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg(dirname($caminhoPdfAbsoluto)),
                escapeshellarg($caminhoArquivo)
            );

            \Log::info('Executando comando LibreOffice para assinatura', [
                'comando' => $comando
            ]);

            exec($comando, $output, $returnCode);

            \Log::info('Resultado comando LibreOffice para assinatura', [
                'return_code' => $returnCode,
                'output' => $output,
                'pdf_existe' => file_exists($caminhoPdfAbsoluto)
            ]);

            if ($returnCode !== 0) {
                throw new \Exception('Erro na conversão para PDF. Código: ' . $returnCode . '. Output: ' . implode(', ', $output));
            }

            // Verificar se o PDF foi criado
            if (!file_exists($caminhoPdfAbsoluto)) {
                throw new \Exception('PDF não foi criado após conversão');
            }
        }

        // Atualizar proposição com caminho do PDF
        $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
        $proposicao->save();

        \Log::info('PDF gerado com sucesso para assinatura', [
            'proposicao_id' => $proposicao->id,
            'arquivo_pdf_path' => $caminhoPdfRelativo,
            'tamanho_arquivo' => filesize($caminhoPdfAbsoluto)
        ]);
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
     * Criar PDF de exemplo quando LibreOffice não está disponível
     */
    private function criarPDFExemplo(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {
            // Ler conteúdo do arquivo original
            $conteudoOriginal = '';
            if ($proposicao->arquivo_path && \Storage::exists($proposicao->arquivo_path)) {
                $conteudoOriginal = \Storage::get($proposicao->arquivo_path);
                // Remover tags RTF básicas se for RTF
                $conteudoOriginal = strip_tags($conteudoOriginal);
                $conteudoOriginal = preg_replace('/\{[^}]*\}/', '', $conteudoOriginal);
                $conteudoOriginal = trim($conteudoOriginal);
            }

            // Criar HTML para o PDF
            $html = view('proposicoes.pdf.template', [
                'proposicao' => $proposicao,
                'conteudo' => $conteudoOriginal ?: $proposicao->conteudo
            ])->render();

            // Usar Dompdf para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // Salvar PDF
            file_put_contents($caminhoPdfAbsoluto, $pdf->output());

            \Log::info('PDF de exemplo criado com sucesso', [
                'proposicao_id' => $proposicao->id,
                'caminho' => $caminhoPdfAbsoluto,
                'tamanho' => filesize($caminhoPdfAbsoluto)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar PDF de exemplo', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}