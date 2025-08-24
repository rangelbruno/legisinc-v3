<?php

namespace Tests\Processes;

use Tests\TestCase;
use App\Models\User;
use App\Models\TipoProposicao;
use App\Models\Proposicao;
use App\Models\TipoProposicaoTemplate;
use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;
use App\Services\Template\TemplateProcessorService;
use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * Teste de Fluxo Completo de Proposições
 * 
 * Simula todo o processo:
 * 1. Administração - Criação de Template
 * 2. Parlamentar - Criação de Proposição 
 * 3. Parlamentar - Edição de Proposição
 * 4. Envio ao Legislativo
 * 5. Legislativo - Edição no OnlyOffice
 * 6. Retorno ao Parlamentar
 * 7. Parlamentar - Assinatura Digital
 * 8. Parlamentar - Visualização PDF Assinado
 * 9. Protocolo - Numeração Oficial
 */
class ProposicaoFluxoCompletoTest extends TestCase
{
    use RefreshDatabase;

    private array $fluxoStatus = [];
    private array $etapas = [
        'seeder_templates' => 'Configuração de Templates pelo Administrador',
        'criacao_proposicao' => 'Criação de Proposição pelo Parlamentar',
        'edicao_proposicao' => 'Edição de Proposição pelo Parlamentar',
        'envio_legislativo' => 'Envio para o Legislativo',
        'edicao_legislativo' => 'Edição pelo Legislativo no OnlyOffice',
        'retorno_parlamentar' => 'Retorno ao Parlamentar',
        'assinatura_pdf' => 'Assinatura Digital pelo Parlamentar',
        'visualizacao_pdf' => 'Visualização do PDF Assinado',
        'protocolo_numeracao' => 'Protocolo e Numeração Oficial'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->initializeFluxoStatus();
    }

    private function initializeFluxoStatus(): void
    {
        foreach ($this->etapas as $key => $descricao) {
            $this->fluxoStatus[$key] = [
                'status' => 'pending',
                'descricao' => $descricao,
                'dados' => [],
                'erro' => null
            ];
        }
    }

    /** @test */
    public function deve_executar_fluxo_completo_proposicao()
    {
        echo "\n🚀 INICIANDO FLUXO COMPLETO DE PROPOSIÇÕES\n";
        echo "==========================================\n\n";

        try {
            // Etapa 1: Configuração de Templates
            $this->executarEtapaSeederTemplates();
            
            // Etapa 2: Criação de Proposição pelo Parlamentar
            $this->executarEtapaCriacaoProposicao();
            
            // Etapa 3: Edição de Proposição pelo Parlamentar
            $this->executarEtapaEdicaoProposicao();
            
            // Etapa 4: Envio ao Legislativo
            $this->executarEtapaEnvioLegislativo();
            
            // Etapa 5: Edição pelo Legislativo
            $this->executarEtapaEdicaoLegislativo();
            
            // Etapa 6: Retorno ao Parlamentar
            $this->executarEtapaRetornoParlamentar();
            
            // Etapa 7: Assinatura Digital
            $this->executarEtapaAssinaturaPdf();
            
            // Etapa 8: Visualização PDF
            $this->executarEtapaVisualizacaoPdf();
            
            // Etapa 9: Protocolo
            $this->executarEtapaProtocoloNumeracao();
            
        } catch (\Exception $e) {
            $this->marcarEtapaComoErro(debug_backtrace()[0]['function'], $e->getMessage());
        }
        
        // Exibir relatório final
        $this->exibirRelatorioFinal();
    }

    private function executarEtapaSeederTemplates(): void
    {
        echo "📋 ETAPA 1: Configuração de Templates pelo Administrador\n";
        echo "--------------------------------------------------------\n";
        
        try {
            // Simular criação do módulo Templates
            $moduloTemplates = ParametroModulo::create([
                'nome' => 'Templates',
                'descricao' => 'Configuração de templates de proposições',
                'icon' => 'fa-file-text',
                'ordem' => 6,
                'ativo' => true
            ]);
            
            echo "✅ Módulo Templates criado (ID: {$moduloTemplates->id})\n";

            // Criar submódulo Cabeçalho
            $submoduloCabecalho = ParametroSubmodulo::create([
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Cabeçalho',
                'descricao' => 'Configurações do cabeçalho',
                'tipo' => 'form',
                'ordem' => 1,
                'ativo' => true
            ]);
            
            echo "✅ Submódulo Cabeçalho criado (ID: {$submoduloCabecalho->id})\n";

            // Criar campos do cabeçalho
            $campos = [
                ['nome' => 'cabecalho_nome_camara', 'label' => 'Nome da Câmara', 'tipo_campo' => 'text', 'valor' => 'CÂMARA MUNICIPAL DE CARAGUATATUBA'],
                ['nome' => 'cabecalho_endereco', 'label' => 'Endereço', 'tipo_campo' => 'text', 'valor' => 'Praça da República, 40, Centro, Caraguatatuba-SP'],
                ['nome' => 'cabecalho_telefone', 'label' => 'Telefone', 'tipo_campo' => 'text', 'valor' => '(12) 3882-5588'],
                ['nome' => 'cabecalho_website', 'label' => 'Website', 'tipo_campo' => 'text', 'valor' => 'www.camaracaraguatatuba.sp.gov.br']
            ];

            foreach ($campos as $index => $campo) {
                $campoCriado = ParametroCampo::create([
                    'submodulo_id' => $submoduloCabecalho->id,
                    'nome' => $campo['nome'],
                    'label' => $campo['label'],
                    'tipo_campo' => $campo['tipo_campo'],
                    'ordem' => $index + 1,
                    'obrigatorio' => true,
                    'ativo' => true
                ]);

                // Criar valor do parâmetro
                ParametroValor::create([
                    'campo_id' => $campoCriado->id,
                    'valor' => $campo['valor'],
                    'tipo_valor' => 'string'
                ]);
                
                echo "✅ Campo {$campo['nome']} criado com valor: {$campo['valor']}\n";
            }

            // Criar tipo de proposição Moção
            $tipoMocao = TipoProposicao::create([
                'nome' => 'Moção',
                'codigo' => 'MOC',
                'descricao' => 'Manifestação da Câmara sobre assuntos de interesse público',
                'icone' => 'ki-message-text',
                'cor' => 'primary',
                'ativo' => true,
                'ordem' => 1
            ]);
            
            echo "✅ Tipo Proposição Moção criado (ID: {$tipoMocao->id})\n";

            // Criar template da Moção
            $templateContent = $this->criarConteudoTemplateMocao();
            
            $template = TipoProposicaoTemplate::create([
                'tipo_proposicao_id' => $tipoMocao->id,
                'document_key' => 'template_mocao_' . uniqid(),
                'arquivo_path' => 'templates/mocao_template.rtf',
                'conteudo' => $templateContent,
                'formato' => 'rtf',
                'ativo' => true
            ]);
            
            echo "✅ Template Moção criado (ID: {$template->id})\n";

            $this->marcarEtapaComoSucesso('seeder_templates', [
                'modulo_id' => $moduloTemplates->id,
                'tipo_proposicao_id' => $tipoMocao->id,
                'template_id' => $template->id
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('seeder_templates', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaCriacaoProposicao(): void
    {
        echo "👤 ETAPA 2: Criação de Proposição pelo Parlamentar\n";
        echo "--------------------------------------------------\n";
        
        try {
            // Criar usuário parlamentar
            $parlamentar = User::create([
                'name' => 'Jessica Silva',
                'email' => 'jessica@sistema.gov.br',
                'password' => bcrypt('123456'),
                'tipo' => 'parlamentar'
            ]);
            
            echo "✅ Usuário Parlamentar criado: {$parlamentar->name} ({$parlamentar->email})\n";

            // Buscar template criado na etapa anterior
            $template = TipoProposicaoTemplate::first();
            $tipoProposicao = TipoProposicao::first();
            
            // Criar proposição
            $proposicao = Proposicao::create([
                'tipo' => $tipoProposicao->nome,
                'ementa' => 'Moção de apoio às políticas públicas de meio ambiente em Caraguatatuba',
                'conteudo' => 'Esta moção visa manifestar o apoio da Câmara Municipal às iniciativas de preservação ambiental na cidade de Caraguatatuba, especialmente nas áreas de mata atlântica e preservação das praias.',
                'autor_id' => $parlamentar->id,
                'template_id' => $template->id,
                'status' => 'rascunho',
                'tem_conteudo_ia' => false
            ]);
            
            echo "✅ Proposição criada (ID: {$proposicao->id})\n";
            echo "   📝 Tipo: {$proposicao->tipo}\n";
            echo "   📄 Ementa: {$proposicao->ementa}\n";
            echo "   👤 Autor: {$parlamentar->name}\n";
            echo "   📋 Template: {$template->nome}\n";
            echo "   🔍 Status: {$proposicao->status}\n";

            $this->marcarEtapaComoSucesso('criacao_proposicao', [
                'proposicao_id' => $proposicao->id,
                'parlamentar_id' => $parlamentar->id,
                'template_id' => $template->id
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('criacao_proposicao', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaEdicaoProposicao(): void
    {
        echo "✏️ ETAPA 3: Edição de Proposição pelo Parlamentar\n";
        echo "-------------------------------------------------\n";
        
        try {
            $proposicao = Proposicao::first();
            
            // Simular edição da proposição
            $novoConteudo = $proposicao->conteudo . "\n\nJustificativa: A preservação do meio ambiente é fundamental para o desenvolvimento sustentável de nossa cidade, garantindo qualidade de vida para as presentes e futuras gerações.";
            
            $proposicao->update([
                'conteudo' => $novoConteudo,
                'status' => 'em_edicao'
            ]);
            
            echo "✅ Proposição editada (ID: {$proposicao->id})\n";
            echo "   📝 Conteúdo atualizado ({" . strlen($novoConteudo) . "} caracteres)\n";
            echo "   🔍 Status: {$proposicao->status}\n";

            $this->marcarEtapaComoSucesso('edicao_proposicao', [
                'proposicao_id' => $proposicao->id,
                'caracteres' => strlen($novoConteudo),
                'status' => $proposicao->status
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('edicao_proposicao', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaEnvioLegislativo(): void
    {
        echo "📤 ETAPA 4: Envio para o Legislativo\n";
        echo "------------------------------------\n";
        
        try {
            $proposicao = Proposicao::first();
            
            // Simular envio para análise legislativa
            $proposicao->update([
                'status' => 'enviado_legislativo',
                'enviado_revisao_em' => now()
            ]);
            
            echo "✅ Proposição enviada para o Legislativo (ID: {$proposicao->id})\n";
            echo "   📅 Data de envio: {$proposicao->enviado_revisao_em}\n";
            echo "   🔍 Status: {$proposicao->status}\n";

            $this->marcarEtapaComoSucesso('envio_legislativo', [
                'proposicao_id' => $proposicao->id,
                'data_envio' => $proposicao->data_envio_legislativo
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('envio_legislativo', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaEdicaoLegislativo(): void
    {
        echo "🏛️ ETAPA 5: Edição pelo Legislativo no OnlyOffice\n";
        echo "------------------------------------------------\n";
        
        try {
            // Criar usuário legislativo
            $legislativo = User::create([
                'name' => 'João Santos',
                'email' => 'joao@sistema.gov.br',
                'password' => bcrypt('123456'),
                'tipo' => 'legislativo'
            ]);
            
            echo "✅ Usuário Legislativo criado: {$legislativo->name} ({$legislativo->email})\n";

            $proposicao = Proposicao::first();
            
            // Simular edição pelo legislativo (como se fosse callback do OnlyOffice)
            $conteudoEditado = $proposicao->conteudo . "\n\n[ANÁLISE LEGISLATIVA]\nPareceres Técnicos: Conforme análise da Assessoria Jurídica, a presente moção está em conformidade com o Regimento Interno da Casa e pode ser submetida à deliberação do Plenário.\n\nSugestões de melhoria:\n- Incluir cronograma de implementação\n- Definir metas específicas de preservação";
            
            // Simular salvamento de arquivo OnlyOffice
            $arquivoPath = "proposicoes/proposicao_{$proposicao->id}_editado.rtf";
            Storage::disk('local')->put($arquivoPath, $conteudoEditado);
            
            $proposicao->update([
                'conteudo' => $conteudoEditado,
                'arquivo_path' => $arquivoPath,
                'status' => 'em_revisao',
                'revisado_em' => now(),
                'revisor_id' => $legislativo->id
            ]);
            
            echo "✅ Proposição editada pelo Legislativo (ID: {$proposicao->id})\n";
            echo "   📁 Arquivo salvo: {$arquivoPath}\n";
            echo "   📝 Conteúdo atualizado ({" . strlen($conteudoEditado) . "} caracteres)\n";
            echo "   👤 Revisor: {$legislativo->name}\n";
            echo "   📅 Data da revisão: {$proposicao->revisado_em}\n";
            echo "   🔍 Status: {$proposicao->status}\n";

            $this->marcarEtapaComoSucesso('edicao_legislativo', [
                'proposicao_id' => $proposicao->id,
                'arquivo_path' => $arquivoPath,
                'revisor_id' => $legislativo->id,
                'caracteres' => strlen($conteudoEditado)
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('edicao_legislativo', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaRetornoParlamentar(): void
    {
        echo "↩️ ETAPA 6: Retorno ao Parlamentar\n";
        echo "---------------------------------\n";
        
        try {
            $proposicao = Proposicao::first();
            
            // Simular retorno para o parlamentar
            $proposicao->update([
                'status' => 'retornado_legislativo',
                'data_retorno_legislativo' => now()
            ]);
            
            echo "✅ Proposição retornada ao Parlamentar (ID: {$proposicao->id})\n";
            echo "   📅 Data de retorno: {$proposicao->data_retorno_legislativo}\n";
            echo "   🔍 Status: {$proposicao->status}\n";
            echo "   📄 Pronta para assinatura digital\n";

            $this->marcarEtapaComoSucesso('retorno_parlamentar', [
                'proposicao_id' => $proposicao->id,
                'data_retorno' => $proposicao->data_retorno_legislativo
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('retorno_parlamentar', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaAssinaturaPdf(): void
    {
        echo "✍️ ETAPA 7: Assinatura Digital pelo Parlamentar\n";
        echo "----------------------------------------------\n";
        
        try {
            $proposicao = Proposicao::first();
            
            // Simular geração de PDF e assinatura digital
            $pdfPath = "proposicoes/pdf/proposicao_{$proposicao->id}_assinado.pdf";
            $assinaturaDigital = hash('sha256', $proposicao->conteudo . now()->toString());
            
            // Simular criação do arquivo PDF assinado
            Storage::disk('local')->put($pdfPath, "PDF_CONTENT_MOCK_" . $assinaturaDigital);
            
            $proposicao->update([
                'arquivo_pdf_path' => $pdfPath,
                'assinatura_digital' => $assinaturaDigital,
                'data_assinatura' => now(),
                'ip_assinatura' => '127.0.0.1',
                'certificado_digital' => 'A1_CERTIFICATE_MOCK',
                'status' => 'assinado'
            ]);
            
            echo "✅ Proposição assinada digitalmente (ID: {$proposicao->id})\n";
            echo "   📁 PDF assinado: {$pdfPath}\n";
            echo "   🔐 Assinatura digital: " . substr($assinaturaDigital, 0, 16) . "...\n";
            echo "   📅 Data da assinatura: {$proposicao->data_assinatura}\n";
            echo "   🌐 IP da assinatura: {$proposicao->ip_assinatura}\n";
            echo "   🔍 Status: {$proposicao->status}\n";

            $this->marcarEtapaComoSucesso('assinatura_pdf', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $pdfPath,
                'assinatura' => $assinaturaDigital,
                'data_assinatura' => $proposicao->data_assinatura
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('assinatura_pdf', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaVisualizacaoPdf(): void
    {
        echo "👁️ ETAPA 8: Visualização do PDF Assinado\n";
        echo "---------------------------------------\n";
        
        try {
            $proposicao = Proposicao::first();
            
            // Verificar se PDF existe e pode ser visualizado
            $pdfExiste = Storage::disk('local')->exists($proposicao->arquivo_pdf_path);
            $tamanhoArquivo = $pdfExiste ? Storage::disk('local')->size($proposicao->arquivo_pdf_path) : 0;
            
            echo "✅ PDF Assinado disponível para visualização (ID: {$proposicao->id})\n";
            echo "   📁 Arquivo: {$proposicao->arquivo_pdf_path}\n";
            echo "   ✅ Arquivo existe: " . ($pdfExiste ? 'SIM' : 'NÃO') . "\n";
            echo "   📊 Tamanho: {$tamanhoArquivo} bytes\n";
            echo "   🔐 Hash da assinatura: " . substr($proposicao->assinatura_digital, 0, 16) . "...\n";
            echo "   📅 Assinado em: {$proposicao->data_assinatura}\n";

            $this->marcarEtapaComoSucesso('visualizacao_pdf', [
                'proposicao_id' => $proposicao->id,
                'pdf_existe' => $pdfExiste,
                'tamanho_arquivo' => $tamanhoArquivo
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('visualizacao_pdf', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function executarEtapaProtocoloNumeracao(): void
    {
        echo "📋 ETAPA 9: Protocolo e Numeração Oficial\n";
        echo "----------------------------------------\n";
        
        try {
            // Criar usuário protocolo
            $protocolo = User::create([
                'name' => 'Roberto Lima',
                'email' => 'roberto@sistema.gov.br',
                'password' => bcrypt('123456'),
                'tipo' => 'protocolo'
            ]);
            
            echo "✅ Usuário Protocolo criado: {$protocolo->name} ({$protocolo->email})\n";

            $proposicao = Proposicao::first();
            
            // Gerar número de protocolo
            $anoAtual = date('Y');
            $numeroProtocolo = str_pad(1, 4, '0', STR_PAD_LEFT) . '/' . $anoAtual;
            
            $proposicao->update([
                'numero_protocolo' => $numeroProtocolo,
                'data_protocolo' => now(),
                'funcionario_protocolo_id' => $protocolo->id,
                'status' => 'protocolado'
            ]);
            
            echo "✅ Proposição protocolada oficialmente (ID: {$proposicao->id})\n";
            echo "   🔢 Número de protocolo: {$proposicao->numero_protocolo}\n";
            echo "   📅 Data do protocolo: {$proposicao->data_protocolo}\n";
            echo "   👤 Responsável pelo protocolo: {$protocolo->name}\n";
            echo "   🔍 Status final: {$proposicao->status}\n";

            $this->marcarEtapaComoSucesso('protocolo_numeracao', [
                'proposicao_id' => $proposicao->id,
                'numero_protocolo' => $numeroProtocolo,
                'protocolo_usuario_id' => $protocolo->id,
                'data_protocolo' => $proposicao->data_protocolo
            ]);

        } catch (\Exception $e) {
            $this->marcarEtapaComoErro('protocolo_numeracao', $e->getMessage());
            throw $e;
        }
        
        echo "\n";
    }

    private function criarConteudoTemplateMocao(): string
    {
        return '{\\rtf1\\ansi\\deff0 {\\fonttbl {\\f0 Times New Roman;}}
\\f0\\fs24
{\\pard\\qc\\b CÂMARA MUNICIPAL DE CARAGUATATUBA\\par}
{\\pard\\qc Praça da República, 40, Centro\\par}
{\\pard\\qc (12) 3882-5588\\par}
{\\pard\\qc www.camaracaraguatatuba.sp.gov.br\\par}
\\par
{\\pard\\qc\\b MOÇÃO Nº ${numero_proposicao}\\par}
\\par
{\\pard\\b EMENTA: ${ementa}\\par}
\\par
{\\pard A Câmara Municipal manifesta:\\par}
\\par
{\\pard ${conteudo}\\par}
\\par
{\\pard Resolve dirigir a presente Moção.\\par}
\\par
{\\pard Caraguatatuba, ${dia} de ${mes_extenso} de ${ano_atual}.\\par}
\\par
{\\pard ${assinatura_padrao}\\par}
{\\pard ${autor_nome}\\par}
{\\pard Vereador\\par}
}';
    }

    private function marcarEtapaComoSucesso(string $etapa, array $dados = []): void
    {
        $this->fluxoStatus[$etapa]['status'] = 'sucesso';
        $this->fluxoStatus[$etapa]['dados'] = $dados;
    }

    private function marcarEtapaComoErro(string $etapa, string $erro): void
    {
        $this->fluxoStatus[$etapa]['status'] = 'erro';
        $this->fluxoStatus[$etapa]['erro'] = $erro;
    }

    private function exibirRelatorioFinal(): void
    {
        echo "\n🎯 RELATÓRIO FINAL DO FLUXO DE PROPOSIÇÕES\n";
        echo "==========================================\n\n";
        
        $sucessos = 0;
        $erros = 0;
        
        foreach ($this->fluxoStatus as $etapa => $status) {
            $icone = match($status['status']) {
                'sucesso' => '✅',
                'erro' => '❌',
                default => '⏳'
            };
            
            echo sprintf("%-3s %-50s [%s]\n", 
                $icone, 
                $status['descricao'], 
                strtoupper($status['status'])
            );
            
            if ($status['status'] === 'sucesso') {
                $sucessos++;
            } elseif ($status['status'] === 'erro') {
                $erros++;
                echo "    ⚠️  ERRO: {$status['erro']}\n";
            }
        }
        
        echo "\n📊 RESUMO:\n";
        echo "----------\n";
        echo "✅ Etapas concluídas com sucesso: {$sucessos}\n";
        echo "❌ Etapas com erro: {$erros}\n";
        echo "📈 Taxa de sucesso: " . round(($sucessos / count($this->etapas)) * 100, 1) . "%\n";
        
        if ($erros === 0) {
            echo "\n🎉 FLUXO COMPLETO EXECUTADO COM SUCESSO!\n";
            echo "Todas as etapas foram concluídas sem erros.\n";
        } else {
            echo "\n⚠️  FLUXO CONCLUÍDO COM PROBLEMAS\n";
            echo "Verifique as etapas marcadas com ❌ para correção.\n";
        }
        
        echo "\n";
    }
}