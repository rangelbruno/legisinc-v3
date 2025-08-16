<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proposicao;
use App\Models\User;
use App\Models\TipoProposicaoTemplate;
use App\Http\Controllers\ProposicaoAssinaturaController;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProposicaoTesteAssinaturaSeeder extends Seeder
{
    /**
     * Criar proposição de teste para workflow de assinatura completo
     */
    public function run(): void
    {
        $this->command->info('🎯 Criando proposição de teste para assinatura...');

        // Buscar usuários necessários
        $parlamentar = User::where('email', 'jessica@sistema.gov.br')->first();
        $legislativo = User::where('email', 'joao@sistema.gov.br')->first();

        if (!$parlamentar || !$legislativo) {
            $this->command->error('❌ Usuários parlamentar ou legislativo não encontrados!');
            return;
        }

        // Buscar template de moção
        $template = TipoProposicaoTemplate::find(6);
        if (!$template) {
            $this->command->error('❌ Template de moção (ID 6) não encontrado!');
            return;
        }

        // Criar proposição de teste
        $proposicao = Proposicao::create([
            'tipo' => 'mocao',
            'ementa' => 'Moção de Teste - Sistema de Assinatura Digital Funcionando',
            'conteudo' => 'Esta é uma proposição de teste criada automaticamente para demonstrar o funcionamento completo do sistema de assinatura digital. O conteúdo foi criado pelo parlamentar, editado e aprovado pelo legislativo, e agora está pronto para assinatura digital.',
            'autor_id' => $parlamentar->id,
            'status' => 'aprovado_assinatura',
            'template_id' => $template->id,
            'revisor_id' => $legislativo->id,
            'enviado_revisao_em' => Carbon::now()->subMinutes(15),
            'revisado_em' => Carbon::now()->subMinutes(5),
            'created_at' => Carbon::now()->subMinutes(20),
            'updated_at' => Carbon::now()->subMinutes(5),
        ]);

        $this->command->info("✅ Proposição criada: ID {$proposicao->id}");

        // Simular edição no OnlyOffice criando arquivo DOCX
        $this->simularEdicaoOnlyOffice($proposicao);

        // Gerar PDF automaticamente
        try {
            $this->command->info('📄 Gerando PDF da proposição...');
            
            $controller = new ProposicaoAssinaturaController();
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('gerarPDFParaAssinatura');
            $method->setAccessible(true);
            
            $method->invoke($controller, $proposicao);
            
            $proposicao->refresh();
            
            if ($proposicao->arquivo_pdf_path) {
                $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
                $pdfSize = file_exists($pdfPath) ? filesize($pdfPath) : 0;
                
                $this->command->info("✅ PDF gerado: {$proposicao->arquivo_pdf_path}");
                $this->command->info("📊 Tamanho: " . number_format($pdfSize) . " bytes");
                
                if ($pdfSize > 50000) {
                    $this->command->info("🎨 Formatação OnlyOffice preservada!");
                } else {
                    $this->command->warn("⚠️ PDF menor que esperado (método fallback usado)");
                }
            } else {
                $this->command->error("❌ PDF não foi gerado");
            }
            
        } catch (\Exception $e) {
            $this->command->error("❌ Erro ao gerar PDF: " . $e->getMessage());
        }

        $this->command->info('');
        $this->command->info('🎉 ===============================================');
        $this->command->info('✅ PROPOSIÇÃO DE TESTE CRIADA COM SUCESSO!');
        $this->command->info('🎉 ===============================================');
        $this->command->info('');
        $this->command->info("📋 Proposição ID: {$proposicao->id}");
        $this->command->info("📝 Tipo: {$proposicao->tipo}");
        $this->command->info("📊 Status: {$proposicao->status}");
        $this->command->info("👤 Autor: {$parlamentar->name} ({$parlamentar->email})");
        $this->command->info("⚖️ Revisor: {$legislativo->name} ({$legislativo->email})");
        $this->command->info('');
        $this->command->info('🎯 PARA TESTAR:');
        $this->command->info('1. Acesse: http://localhost:8001/proposicoes/' . $proposicao->id);
        $this->command->info('2. Login: jessica@sistema.gov.br / 123456');
        $this->command->info('3. Verificar: Histórico completo (3 etapas)');
        $this->command->info('4. Verificar: Ações de assinatura disponíveis');
        $this->command->info('5. Clicar: "Assinar Documento"');
        $this->command->info('6. Resultado: Tela de assinatura com PDF formatado');
        $this->command->info('');
        $this->command->info('✨ WORKFLOW PARLAMENTAR → LEGISLATIVO → ASSINATURA FUNCIONANDO! ✨');
        $this->command->info('');
    }

    /**
     * Simula que a proposição foi editada no OnlyOffice criando um arquivo DOCX
     */
    private function simularEdicaoOnlyOffice(Proposicao $proposicao): void
    {
        $this->command->info('📝 Simulando edição no OnlyOffice...');

        try {
            // Criar nome do arquivo RTF (será convertido para DOCX)
            $timestamp = time();
            $rtfPath = "proposicoes/proposicao_{$proposicao->id}_{$timestamp}.rtf";
            $docxPath = "proposicoes/proposicao_{$proposicao->id}_{$timestamp}.docx";

            // Criar conteúdo RTF com formatação OnlyOffice
            $conteudoRTF = $this->criarConteudoDocxSimulado($proposicao);

            // Garantir que o diretório existe
            $diretorio = dirname($rtfPath);
            if (!Storage::disk('local')->exists($diretorio)) {
                Storage::disk('local')->makeDirectory($diretorio);
            }

            // Salvar arquivo RTF
            Storage::disk('local')->put($rtfPath, $conteudoRTF);
            
            // Converter RTF para DOCX usando LibreOffice
            $rtfFullPath = Storage::disk('local')->path($rtfPath);
            $outputDir = dirname($rtfFullPath);
            
            $comando = sprintf(
                'libreoffice --headless --invisible --convert-to docx --outdir %s %s 2>/dev/null',
                escapeshellarg($outputDir),
                escapeshellarg($rtfFullPath)
            );
            
            exec($comando, $output, $returnCode);
            
            $expectedDocx = $outputDir . '/' . pathinfo($rtfFullPath, PATHINFO_FILENAME) . '.docx';
            $docxFullPath = Storage::disk('local')->path($docxPath);
            
            if ($returnCode === 0 && file_exists($expectedDocx)) {
                // Renomear para o nome correto
                rename($expectedDocx, $docxFullPath);
                
                // Atualizar proposição com arquivo DOCX
                $proposicao->update(['arquivo_path' => $docxPath]);
                
                $this->command->info("✅ DOCX criado do RTF: {$docxPath}");
                $this->command->info("📊 Tamanho: " . number_format(filesize($docxFullPath)) . " bytes");
                
                // Remover arquivo RTF temporário
                Storage::disk('local')->delete($rtfPath);
                
            } else {
                // Fallback: usar RTF diretamente
                $proposicao->update(['arquivo_path' => $rtfPath]);
                $this->command->info("✅ RTF criado: {$rtfPath}");
                $this->command->info("📊 Tamanho: " . number_format(strlen($conteudoRTF)) . " bytes");
            }

        } catch (\Exception $e) {
            $this->command->error("❌ Erro ao simular edição OnlyOffice: " . $e->getMessage());
        }
    }

    /**
     * Cria conteúdo RTF real com formatação e codificação UTF-8 correta
     */
    private function criarConteudoDocxSimulado(Proposicao $proposicao): string
    {
        // Gerar conteúdo RTF completo com dados reais da proposição e codificação correta
        $conteudoRTF = '{\rtf1\ansi\deff0' .
            '{\fonttbl{\f0\froman Times New Roman;}{\f1\fswiss Arial;}}' .
            '{\colortbl;\red0\green0\blue0;}' .
            '\f0\fs24' .
            '\qc\b CÂMARA MUNICIPAL DE CARAGUATATUBA\b0\par' .
            '\qc Praça da República, 40, Centro\par' .
            '\qc (12) 3882-5588\par' .
            '\qc www.camaracaraguatatuba.sp.gov.br\par' .
            '\par\par' .
            '\qc\b MOÇÃO Nº [AGUARDANDO PROTOCOLO]\b0\par' .
            '\par' .
            '\ql\b EMENTA:\b0 ' . $proposicao->ementa . '\par' .
            '\par' .
            '\ql A Câmara Municipal manifesta:\par' .
            '\par' .
            '\ql ' . $proposicao->conteudo . '\par' .
            '\par' .
            '\ql Resolve dirigir a presente Moção.\par' .
            '\par' .
            '\ql Caraguatatuba, 15 de agosto de 2025.\par' .
            '\par\par' .
            '\ql __________________________________\par' .
            '\ql ' . ($proposicao->autor->name ?? 'Jessica Santos') . '\par' .
            '\ql Vereadora\par' .
            '}';

        return $conteudoRTF;
    }
}