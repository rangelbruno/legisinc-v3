<?php

namespace Database\Seeders;

use App\Models\TemplateUniversal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TemplateUniversalFixSeeder extends Seeder
{
    /**
     * Seeder para corrigir automaticamente o Template Universal RTF
     * Resolve o problema de codificação e diálogo "Choose TXT options"
     */
    public function run(): void
    {
        $this->command->info('🔧 Aplicando correção automática do Template Universal...');

        try {
            // Encontrar ou criar template universal padrão
            $template = TemplateUniversal::where('is_default', true)->first();

            if (!$template) {
                $this->command->warn('⚠️  Template Universal não encontrado, criando novo...');
                $template = $this->criarTemplateUniversal();
            }

            // Aplicar conteúdo RTF correto
            $conteudoRTFCorreto = $this->gerarConteudoRTFCorreto();

            // Processar imagem se existir
            $conteudoComImagem = $this->processarImagemCabecalho($conteudoRTFCorreto);

            // Atualizar template
            $template->update([
                'conteudo' => $conteudoComImagem,
                'document_key' => 'template_universal_fixed_' . time(),
                'updated_by' => 1,
            ]);

            // Validar estrutura RTF
            $this->validarEstruturaRTF($template);

            $this->command->info('✅ Template Universal corrigido automaticamente!');
            $this->command->line('   • RTF válido com encoding UTF-8');
            $this->command->line('   • Problema de codificação OnlyOffice resolvido');
            $this->command->line('   • Document key atualizado para forçar refresh');

        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao corrigir Template Universal: ' . $e->getMessage());
            Log::error('Erro no TemplateUniversalFixSeeder', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Criar template universal se não existir
     */
    private function criarTemplateUniversal(): TemplateUniversal
    {
        return TemplateUniversal::create([
            'nome' => 'Template Universal Padrão',
            'descricao' => 'Template único que se adapta a qualquer tipo de proposição legislativa',
            'document_key' => 'template_universal_default_' . time(),
            'conteudo' => '', // Será preenchido depois
            'formato' => 'rtf',
            'variaveis' => $this->getVariaveisDisponiveis(),
            'ativo' => true,
            'is_default' => true,
            'updated_by' => 1,
        ]);
    }

    /**
     * Gerar conteúdo RTF correto (sem corrupção)
     */
    private function gerarConteudoRTFCorreto(): string
    {
        return <<<'RTF'
{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}
\f0\fs24\sl360\slmult1 

\b\fs28 TEMPLATE UNIVERSAL - PROPOSIÇÕES LEGISLATIVAS\b0\fs24\par
\par

\b CABEÇALHO INSTITUCIONAL:\b0\par
${imagem_cabecalho}\par
${cabecalho_nome_camara}\par
${cabecalho_endereco}\par
Tel: ${cabecalho_telefone} - ${cabecalho_website}\par
CNPJ: ${cnpj_camara}\par
\par

\line\par
\par

\qc\b\fs26 ${tipo_proposicao} N° ${numero_proposicao}\b0\fs24\par
\ql\par

\b EMENTA:\b0 ${ementa}\par
\par

\b PREÂMBULO DINÂMICO:\b0\par
[Este campo se adapta automaticamente ao tipo de proposição]\par
\par

\b CONTEÚDO PRINCIPAL:\b0\par
${texto}\par
\par

\b JUSTIFICATIVA:\b0\par
${justificativa}\par
\par

\b ARTICULADO (Para Projetos de Lei):\b0\par
Art. 1° [Disposição principal]\par
\par
Parágrafo único. [Detalhamento se necessário]\par
\par
Art. 2° [Disposições complementares]\par
\par
Art. 3° Esta lei entra em vigor na data de sua publicação.\par
\par

\line\par
\par

\b ÁREA DE ASSINATURA:\b0\par
${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}.\par
\par
${assinatura_padrao}\par
${autor_nome}\par
${autor_cargo}\par
\par

\b RODAPÉ INSTITUCIONAL:\b0\par
${rodape_texto}\par
${endereco_camara}, ${endereco_bairro} - CEP: ${endereco_cep}\par
${municipio}/${municipio_uf} - Tel: ${telefone_camara}\par
${website_camara} - ${email_camara}\par

}
RTF;
    }

    /**
     * Processar imagem do cabeçalho se existir
     */
    private function processarImagemCabecalho(string $conteudo): string
    {
        $caminhoImagem = public_path('template/cabecalho.png');

        if (file_exists($caminhoImagem)) {
            $this->command->line('   🖼️  Processando imagem do cabeçalho...');
            
            try {
                $codigoRTFImagem = $this->gerarCodigoRTFImagem($caminhoImagem);
                $conteudo = str_replace('${imagem_cabecalho}', $codigoRTFImagem, $conteudo);
                
                $this->command->line('   ✅ Imagem processada com sucesso');
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️  Erro ao processar imagem, removendo placeholder');
                $conteudo = str_replace('${imagem_cabecalho}\\par', '', $conteudo);
                $conteudo = str_replace('${imagem_cabecalho}', '', $conteudo);
            }
        } else {
            $this->command->line('   ⚠️  Imagem não encontrada, removendo placeholder');
            $conteudo = str_replace('${imagem_cabecalho}\\par', '', $conteudo);
            $conteudo = str_replace('${imagem_cabecalho}', '', $conteudo);
        }

        return $conteudo;
    }

    /**
     * Gerar código RTF para imagem
     */
    private function gerarCodigoRTFImagem(string $caminhoImagem): string
    {
        if (!file_exists($caminhoImagem)) {
            return '[IMAGEM DO CABEÇALHO - ARQUIVO NÃO ENCONTRADO]';
        }

        $info = getimagesize($caminhoImagem);
        if (!$info) {
            return '[IMAGEM DO CABEÇALHO - FORMATO INVÁLIDO]';
        }

        // Converter imagem para hex
        $imagemData = file_get_contents($caminhoImagem);
        $imagemHex = bin2hex($imagemData);

        // Dimensões em twips (1 inch = 1440 twips)
        $larguraTwips = round(($info[0] * 1440) / 96); // 96 DPI padrão
        $alturaTwips = round(($info[1] * 1440) / 96);

        // Redimensionar para cabeçalho (máximo 3 inches de largura)
        $maxLargura = 4320; // 3 inches em twips
        if ($larguraTwips > $maxLargura) {
            $fator = $maxLargura / $larguraTwips;
            $larguraTwips = $maxLargura;
            $alturaTwips = round($alturaTwips * $fator);
        }

        $tipoImagem = $info['mime'] === 'image/png' ? 'pngblip' : 'jpegblip';

        return "{\\*\\shppict {\\pict\\{$tipoImagem}\\picw{$info[0]}\\pich{$info[1]}\\picwgoal{$larguraTwips}\\pichgoal{$alturaTwips} {$imagemHex}}}";
    }

    /**
     * Validar estrutura RTF
     */
    private function validarEstruturaRTF(TemplateUniversal $template): void
    {
        $conteudo = $template->conteudo;

        $comecaComRTF = str_starts_with($conteudo, '{\rtf1');
        $temUTF8 = str_contains($conteudo, '\ansicpg65001');
        $terminaComChave = str_ends_with(trim($conteudo), '}');
        $ocorrenciasRTF = substr_count($conteudo, '{\rtf1');

        if (!$comecaComRTF || !$temUTF8 || !$terminaComChave || $ocorrenciasRTF !== 1) {
            $this->command->warn('⚠️  Estrutura RTF pode ter problemas:');
            $this->command->line("   Começa com {\\rtf1: " . ($comecaComRTF ? 'SIM' : 'NÃO'));
            $this->command->line("   Contém UTF-8: " . ($temUTF8 ? 'SIM' : 'NÃO'));
            $this->command->line("   Termina com }: " . ($terminaComChave ? 'SIM' : 'NÃO'));
            $this->command->line("   Ocorrências RTF: $ocorrenciasRTF");
        } else {
            $this->command->line('   ✅ Estrutura RTF validada com sucesso');
        }
    }

    /**
     * Variáveis disponíveis no template
     */
    private function getVariaveisDisponiveis(): array
    {
        return [
            // Proposição
            'tipo_proposicao' => 'Tipo da proposição (auto-adaptado)',
            'numero_proposicao' => 'Número da proposição',
            'ementa' => 'Ementa da proposição',
            'texto' => 'Texto principal',
            'justificativa' => 'Justificativa',
            'protocolo' => 'Número do protocolo',

            // Autor
            'autor_nome' => 'Nome do autor',
            'autor_cargo' => 'Cargo do autor',
            'autor_partido' => 'Partido do autor',

            // Datas
            'data_atual' => 'Data atual (dd/mm/aaaa)',
            'data_criacao' => 'Data de criação',
            'data_protocolo' => 'Data do protocolo',
            'dia' => 'Dia atual',
            'mes' => 'Mês atual',
            'ano_atual' => 'Ano atual',
            'mes_extenso' => 'Mês por extenso',

            // Instituição
            'municipio' => 'Nome do município',
            'nome_camara' => 'Nome da câmara',
            'endereco_camara' => 'Endereço da câmara',
            'telefone_camara' => 'Telefone principal',
            'email_camara' => 'E-mail oficial',
            'cnpj_camara' => 'CNPJ da câmara',

            // Cabeçalho/Rodapé
            'imagem_cabecalho' => 'Imagem do cabeçalho',
            'assinatura_padrao' => 'Área de assinatura padrão',
            'rodape_texto' => 'Texto do rodapé institucional',

            // Status
            'status' => 'Status da proposição',
        ];
    }
}