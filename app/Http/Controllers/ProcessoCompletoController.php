<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Models\TipoProposicaoTemplate;
use App\Models\User;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\NumeroProcessoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessoCompletoController extends Controller
{
    private OnlyOfficeService $onlyOfficeService;
    private NumeroProcessoService $numeroProcessoService;

    public function __construct(
        OnlyOfficeService $onlyOfficeService,
        NumeroProcessoService $numeroProcessoService
    ) {
        $this->onlyOfficeService = $onlyOfficeService;
        $this->numeroProcessoService = $numeroProcessoService;
    }

    /**
     * Exibe a página de análise do processo completo
     */
    public function index()
    {
        return view('tests.processo-completo');
    }

    /**
     * API: Verificar se templates existem
     */
    public function checkTemplates()
    {
        try {
            // Verificar se existe template para tipo_proposicao_id = 3 (que normalmente é Moção)
            $templateMocao = TipoProposicaoTemplate::where('tipo_proposicao_id', 3)
                ->where('ativo', true)
                ->first();

            // Se não encontrou no ID 3, tentar buscar qualquer template ativo
            if (!$templateMocao) {
                $templateMocao = TipoProposicaoTemplate::where('ativo', true)->first();
            }

            return response()->json([
                'success' => true,
                'template_mocao_exists' => $templateMocao !== null,
                'template_id' => $templateMocao?->id,
                'tipo_proposicao_id' => $templateMocao?->tipo_proposicao_id,
                'total_templates' => TipoProposicaoTemplate::count(),
                'templates_ativos' => TipoProposicaoTemplate::where('ativo', true)->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Criar proposição de teste
     */
    public function createTestProposicao(Request $request)
    {
        try {
            $request->validate([
                'tipo' => 'required|string',
                'ementa' => 'required|string',
                'template_id' => 'nullable|integer'
            ]);

            // Buscar um usuário parlamentar para ser o autor
            $parlamentar = User::whereHas('roles', function($query) {
                $query->where('name', 'PARLAMENTAR');
            })->first();

            if (!$parlamentar) {
                // Se não há parlamentar, usar o primeiro usuário disponível
                $parlamentar = User::first();
                if (!$parlamentar) {
                    throw new \Exception('Nenhum usuário encontrado no sistema');
                }
            }

            // Se não foi fornecido template_id, buscar qualquer template ativo
            $templateId = $request->template_id;
            if (!$templateId) {
                $template = TipoProposicaoTemplate::where('ativo', true)->first();
                $templateId = $template?->id;
            }

            $proposicao = Proposicao::create([
                'tipo' => $request->tipo,
                'ementa' => $request->ementa,
                'conteudo' => 'Conteúdo de teste para análise do processo legislativo.',
                'autor_id' => $parlamentar->id,
                'status' => 'rascunho',
                'ano' => date('Y'),
                'template_id' => $templateId,
                'variaveis_template' => [
                    'numero_proposicao' => '[AGUARDANDO PROTOCOLO]',
                    'ementa' => $request->ementa,
                    'autor_nome' => $parlamentar->name,
                    'municipio' => 'Caraguatatuba'
                ]
            ]);

            Log::info('Proposição de teste criada', [
                'proposicao_id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'status' => $proposicao->status,
                'template_id' => $proposicao->template_id
            ]);

            return response()->json([
                'success' => true,
                'proposicao_id' => $proposicao->id,
                'status' => $proposicao->status,
                'template_aplicado' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar proposição de teste', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Simular edição no OnlyOffice
     */
    public function simulateEdit(Proposicao $proposicao)
    {
        try {
            // Atualizar status para em_edicao
            $proposicao->update([
                'status' => 'em_edicao',
                'ultima_modificacao' => now()
            ]);

            // Simular callback do OnlyOffice
            $arquivoPath = "proposicoes/proposicao_{$proposicao->id}_" . time() . ".docx";
            
            // Criar um arquivo de teste
            $conteudoTeste = "Documento editado via OnlyOffice - Proposição {$proposicao->id}";
            Storage::disk('local')->put($arquivoPath, $conteudoTeste);

            $proposicao->update([
                'arquivo_path' => $arquivoPath,
                'conteudo_processado' => $conteudoTeste
            ]);

            Log::info('Edição no OnlyOffice simulada', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $arquivoPath,
                'status' => $proposicao->status
            ]);

            return response()->json([
                'success' => true,
                'arquivo_salvo' => true,
                'arquivo_path' => $arquivoPath,
                'status' => $proposicao->status
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao simular edição OnlyOffice', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Enviar para o Legislativo
     */
    public function enviarLegislativo(Proposicao $proposicao)
    {
        try {
            $proposicao->update([
                'status' => 'enviado_legislativo',
                'enviado_revisao_em' => now()
            ]);

            // Buscar usuário do Legislativo
            $legislativo = User::whereHas('roles', function($query) {
                $query->where('name', 'LEGISLATIVO');
            })->first();

            if ($legislativo) {
                $proposicao->update(['revisor_id' => $legislativo->id]);
            }

            Log::info('Proposição enviada para o Legislativo', [
                'proposicao_id' => $proposicao->id,
                'status' => $proposicao->status,
                'revisor_id' => $proposicao->revisor_id
            ]);

            return response()->json([
                'success' => true,
                'status' => $proposicao->status,
                'enviado_em' => $proposicao->enviado_revisao_em,
                'revisor_id' => $proposicao->revisor_id
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar para Legislativo', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Simular edição do Legislativo
     */
    public function simulateLegislativoEdit(Proposicao $proposicao)
    {
        try {
            // Simular que o Legislativo editou o documento
            $conteudoEditado = $proposicao->conteudo_processado . "\n\nObservações do Legislativo: Documento revisado e aprovado para prosseguimento.";
            
            // Atualizar arquivo
            $novoArquivoPath = "proposicoes/proposicao_{$proposicao->id}_legislativo_" . time() . ".docx";
            Storage::disk('local')->put($novoArquivoPath, $conteudoEditado);

            $proposicao->update([
                'arquivo_path' => $novoArquivoPath,
                'conteudo_processado' => $conteudoEditado,
                'observacoes_legislativo' => 'Documento revisado conforme normas técnicas.',
                'revisado_em' => now()
            ]);

            Log::info('Edição do Legislativo simulada', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $novoArquivoPath,
                'revisado_em' => $proposicao->revisado_em
            ]);

            return response()->json([
                'success' => true,
                'edicao_salva' => true,
                'arquivo_path' => $novoArquivoPath,
                'observacoes' => $proposicao->observacoes_legislativo
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao simular edição do Legislativo', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Retornar para Parlamentar
     */
    public function retornarParlamentar(Proposicao $proposicao)
    {
        try {
            // Gerar PDF para o parlamentar
            $pdfPath = "proposicoes/pdf/proposicao_{$proposicao->id}_" . time() . ".pdf";
            
            // Simular geração de PDF
            $conteudoPdf = "PDF da proposição {$proposicao->id} com alterações do Legislativo";
            Storage::disk('local')->put($pdfPath, $conteudoPdf);

            $proposicao->update([
                'status' => 'retornado_legislativo',
                'data_retorno_legislativo' => now(),
                'arquivo_pdf_path' => $pdfPath
            ]);

            Log::info('Proposição retornada para Parlamentar', [
                'proposicao_id' => $proposicao->id,
                'status' => $proposicao->status,
                'pdf_path' => $pdfPath
            ]);

            return response()->json([
                'success' => true,
                'pdf_gerado' => true,
                'pdf_path' => $pdfPath,
                'status' => $proposicao->status
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao retornar para Parlamentar', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Simular assinatura digital
     */
    public function simulateAssinatura(Proposicao $proposicao)
    {
        try {
            // Gerar assinatura digital simulada
            $assinaturaDigital = hash('sha256', $proposicao->id . now() . 'teste_assinatura');
            
            // Gerar PDF assinado
            $pdfAssinadoPath = "proposicoes/pdf/proposicao_{$proposicao->id}_assinado_" . time() . ".pdf";
            $conteudoPdfAssinado = "PDF assinado da proposição {$proposicao->id} com QR Code e certificado digital";
            Storage::disk('local')->put($pdfAssinadoPath, $conteudoPdfAssinado);

            $proposicao->update([
                'status' => 'assinado',
                'confirmacao_leitura' => true,
                'assinatura_digital' => $assinaturaDigital,
                'data_assinatura' => now(),
                'ip_assinatura' => request()->ip(),
                'pdf_assinado_path' => $pdfAssinadoPath,
                'assinado' => true
            ]);

            Log::info('Assinatura digital simulada', [
                'proposicao_id' => $proposicao->id,
                'assinatura_digital' => $assinaturaDigital,
                'pdf_assinado_path' => $pdfAssinadoPath
            ]);

            return response()->json([
                'success' => true,
                'assinatura_valida' => true,
                'assinatura_digital' => $assinaturaDigital,
                'pdf_assinado_path' => $pdfAssinadoPath,
                'data_assinatura' => $proposicao->data_assinatura
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao simular assinatura', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Simular protocolo
     */
    public function simulateProtocolo(Proposicao $proposicao)
    {
        try {
            // Gerar número de protocolo
            $numeroProtocolo = $this->numeroProcessoService->atribuirNumeroProcesso($proposicao);
            
            // Buscar funcionário do protocolo
            $funcionarioProtocolo = User::whereHas('roles', function($query) {
                $query->where('name', 'PROTOCOLO');
            })->first();

            $proposicao->update([
                'status' => 'protocolado',
                'numero_protocolo' => $numeroProtocolo,
                'data_protocolo' => now(),
                'funcionario_protocolo_id' => $funcionarioProtocolo?->id,
                'comissoes_destino' => ['Comissão de Justiça', 'Comissão de Finanças'],
                'observacoes_protocolo' => 'Documento protocolado e encaminhado às comissões competentes.',
                'verificacoes_realizadas' => [
                    'assinatura_valida' => true,
                    'formato_correto' => true,
                    'documentos_anexos' => true,
                    'todas_aprovadas' => true
                ]
            ]);

            // Atualizar variáveis do template com número oficial
            $variaveis = $proposicao->variaveis_template ?? [];
            $variaveis['numero_proposicao'] = $numeroProtocolo;
            $proposicao->update(['variaveis_template' => $variaveis]);

            Log::info('Protocolo simulado', [
                'proposicao_id' => $proposicao->id,
                'numero_protocolo' => $numeroProtocolo,
                'status' => $proposicao->status
            ]);

            return response()->json([
                'success' => true,
                'numero_protocolo' => $numeroProtocolo,
                'status' => $proposicao->status,
                'data_protocolo' => $proposicao->data_protocolo,
                'funcionario_protocolo_id' => $proposicao->funcionario_protocolo_id
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao simular protocolo', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obter status da proposição
     */
    public function getProposicaoStatus(Proposicao $proposicao)
    {
        try {
            return response()->json([
                'success' => true,
                'proposicao' => [
                    'id' => $proposicao->id,
                    'tipo' => $proposicao->tipo,
                    'ementa' => $proposicao->ementa,
                    'status' => $proposicao->status,
                    'numero_protocolo' => $proposicao->numero_protocolo,
                    'arquivo_path' => $proposicao->arquivo_path,
                    'arquivo_pdf_path' => $proposicao->arquivo_pdf_path,
                    'pdf_assinado_path' => $proposicao->pdf_assinado_path,
                    'assinatura_digital' => $proposicao->assinatura_digital,
                    'data_assinatura' => $proposicao->data_assinatura,
                    'data_protocolo' => $proposicao->data_protocolo,
                    'observacoes_legislativo' => $proposicao->observacoes_legislativo,
                    'created_at' => $proposicao->created_at,
                    'updated_at' => $proposicao->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Limpar dados de teste
     */
    public function limparDadosTeste()
    {
        try {
            // Remover proposições de teste
            $proposicoesTeste = Proposicao::where('ementa', 'like', '%teste%')
                ->orWhere('ementa', 'like', '%análise do processo%')
                ->get();

            $removidas = 0;
            foreach ($proposicoesTeste as $proposicao) {
                // Remover arquivos associados
                if ($proposicao->arquivo_path && Storage::disk('local')->exists($proposicao->arquivo_path)) {
                    Storage::disk('local')->delete($proposicao->arquivo_path);
                }
                if ($proposicao->arquivo_pdf_path && Storage::disk('local')->exists($proposicao->arquivo_pdf_path)) {
                    Storage::disk('local')->delete($proposicao->arquivo_pdf_path);
                }
                if ($proposicao->pdf_assinado_path && Storage::disk('local')->exists($proposicao->pdf_assinado_path)) {
                    Storage::disk('local')->delete($proposicao->pdf_assinado_path);
                }
                
                $proposicao->delete();
                $removidas++;
            }

            Log::info('Dados de teste limpos', [
                'proposicoes_removidas' => $removidas
            ]);

            return response()->json([
                'success' => true,
                'proposicoes_removidas' => $removidas,
                'message' => "Removidas {$removidas} proposições de teste"
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao limpar dados de teste', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}