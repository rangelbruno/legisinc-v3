<?php

namespace Database\Seeders;

use App\Models\TemplateUniversal;
use Illuminate\Database\Seeder;

class TemplateUniversalSeeder extends Seeder
{
    /**
     * Seeder para criar o template universal padrão do sistema
     *
     * VARIÁVEIS DISPONÍVEIS NO TEMPLATE UNIVERSAL:
     *
     * === PROPOSIÇÃO ===
     * ${tipo_proposicao}            - Tipo da proposição (auto-adaptado)
     * ${numero_proposicao}          - Número da proposição
     * ${ementa}                     - Ementa da proposição
     * ${texto}                      - Texto principal
     * ${justificativa}              - Justificativa
     * ${protocolo}                  - Número do protocolo
     *
     * === AUTOR ===
     * ${autor_nome}                 - Nome do autor
     * ${autor_cargo}                - Cargo do autor
     * ${autor_partido}              - Partido do autor
     *
     * === DATAS ===
     * ${data_atual}                 - Data atual (dd/mm/aaaa)
     * ${data_criacao}               - Data de criação
     * ${data_protocolo}             - Data do protocolo
     * ${dia}                        - Dia atual
     * ${mes}                        - Mês atual
     * ${ano_atual}                  - Ano atual
     * ${mes_extenso}                - Mês por extenso
     *
     * === INSTITUIÇÃO ===
     * ${municipio}                  - Nome do município
     * ${nome_camara}                - Nome da câmara
     * ${endereco_camara}            - Endereço da câmara
     * ${telefone_camara}            - Telefone principal
     * ${email_camara}               - E-mail oficial
     * ${cnpj_camara}                - CNPJ da câmara
     *
     * === CABEÇALHO/RODAPÉ ===
     * ${imagem_cabecalho}           - Imagem do cabeçalho (se configurada)
     * ${assinatura_padrao}          - Área de assinatura padrão
     * ${rodape_texto}               - Texto do rodapé institucional
     *
     * === DINÂMICAS (AUTO-ADAPTADAS) ===
     * ${preambulo_dinamico}         - Preâmbulo que muda conforme tipo
     * ${clausula_vigencia}          - Cláusula de vigência apropriada
     * ${categoria_tipo}             - Categoria do template
     */
    public function run(): void
    {
        $this->command->info('🎨 Criando Template Universal para Proposições');
        $this->command->line('========================================================================');

        // Verificar se já existe template universal padrão
        $templateExistente = TemplateUniversal::where('is_default', true)->first();

        if ($templateExistente) {
            $this->command->warn("⚠️  Template universal padrão já existe (ID: {$templateExistente->id})");
            $this->command->ask('Deseja continuar e atualizar o template existente? (s/n)', 's') === 's' ?
                $this->atualizarTemplate($templateExistente) :
                $this->command->info('Operação cancelada.');

            return;
        }

        // Criar novo template universal
        $conteudoTemplate = $this->gerarConteudoTemplateUniversal();

        $template = TemplateUniversal::create([
            'nome' => 'Template Universal de Proposições',
            'descricao' => 'Template único que se adapta a qualquer tipo de proposição legislativa, eliminando a necessidade de manter 23 templates separados.',
            'document_key' => 'template_universal_default_'.time(),
            'conteudo' => $conteudoTemplate,
            'formato' => 'rtf',
            'variaveis' => $this->getVariaveisDisponiveis(),
            'ativo' => true,
            'is_default' => true,
            'updated_by' => 1, // ID do admin padrão
        ]);

        $this->command->info('✅ Template Universal criado com sucesso!');
        $this->command->line("   • ID: {$template->id}");
        $this->command->line("   • Nome: {$template->nome}");
        $this->command->line('   • Variáveis: '.count($template->variaveis).' disponíveis');
        $this->command->line("   • Formato: {$template->formato}");
        $this->command->line('   • Status: '.($template->is_default ? 'Padrão do Sistema' : 'Ativo'));

        $this->command->info('🎉 Template Universal configurado com sucesso!');
        $this->command->line('');
        $this->command->info('💡 Acesse em: /admin/templates/universal');
    }

    /**
     * Atualizar template existente
     */
    private function atualizarTemplate(TemplateUniversal $template): void
    {
        $conteudoAtualizado = $this->gerarConteudoTemplateUniversal();

        $template->update([
            'conteudo' => $conteudoAtualizado,
            'variaveis' => $this->getVariaveisDisponiveis(),
            'updated_by' => 1,
        ]);

        $this->command->info('🔄 Template Universal atualizado com sucesso!');
    }

    /**
     * Gerar conteúdo do template universal com estrutura adaptável
     */
    private function gerarConteudoTemplateUniversal(): string
    {
        // Template RTF universal com seções condicionais
        return <<<'RTF'
{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}
\f0\fs24\sl360\slmult1 

\par
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

\b PREÂMBULO ADAPTÁVEL:\b0\par
${preambulo_dinamico}\par
\par

\b CONTEÚDO PRINCIPAL:\b0\par
${texto}\par
\par

\b JUSTIFICATIVA (quando aplicável):\b0\par
${justificativa}\par
\par

\b SEÇÃO ARTICULADA (Para Projetos de Lei):\b0\par
Art. 1º [Disposição principal da proposição]\par
\par
Parágrafo único. [Detalhamento ou exceção, se necessário]\par
\par
Art. 2º [Disposições complementares ou regulamentares]\par
\par
${clausula_vigencia}\par
\par

\b SEÇÃO ESPECÍFICA (Para Requerimentos):\b0\par
I - [Primeira solicitação ou questionamento];\par
II - [Segunda solicitação ou questionamento];\par
III - [Terceira solicitação ou questionamento].\par
\par

\b SEÇÃO ESPECÍFICA (Para Indicações):\b0\par
a) [Primeira sugestão ao Executivo];\par
b) [Segunda sugestão ao Executivo];\par
c) [Terceira sugestão ao Executivo].\par
\par

\b SEÇÃO ESPECÍFICA (Para Moções):\b0\par
Considerando que [primeira consideração];\par
Considerando que [segunda consideração];\par
Considerando que [terceira consideração];\par
\par
RESOLVE dirigir a presente Moção.\par
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
\par

\b INFORMAÇÕES DO DOCUMENTO:\b0\par
Categoria: ${categoria_tipo}\par
Criado em: ${data_criacao}\par
Protocolo: ${protocolo}\par
Status: ${status}\par

}
RTF;
    }

    /**
     * Lista de variáveis disponíveis no template universal
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

            // Dinâmicas
            'preambulo_dinamico' => 'Preâmbulo que muda conforme tipo',
            'clausula_vigencia' => 'Cláusula de vigência apropriada',
            'categoria_tipo' => 'Categoria do template',

            // Status
            'status' => 'Status da proposição',
        ];
    }
}
