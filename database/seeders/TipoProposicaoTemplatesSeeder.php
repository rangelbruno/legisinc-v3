<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;

class TipoProposicaoTemplatesSeeder extends Seeder
{
    /**
     * Seeder para criar todos os templates de proposições seguindo padrões legais LC 95/1998
     * 
     * VARIÁVEIS DISPONÍVEIS NOS TEMPLATES:
     * 
     * === CABEÇALHO ===
     * ${imagem_cabecalho}           - Imagem do cabeçalho (se configurada)
     * ${cabecalho_nome_camara}      - Nome oficial da Câmara
     * ${cabecalho_endereco}         - Endereço completo da Câmara
     * ${cabecalho_telefone}         - Telefone oficial
     * ${cabecalho_website}          - Website oficial
     * 
     * === PROPOSIÇÃO ===
     * ${numero_proposicao}          - Número da proposição
     * ${tipo_proposicao}            - Tipo da proposição
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
     * === RODAPÉ ===
     * ${assinatura_padrao}          - Área de assinatura padrão
     * ${rodape_texto}               - Texto do rodapé institucional
     */
    public function run(): void
    {
        $this->command->info('🏛️  Criando Templates de Proposições com Padrões Legais LC 95/1998');
        $this->command->line('========================================================================');

        // Garantir que o diretório existe
        Storage::makeDirectory('private/templates');

        $templates = $this->getTemplateDefinitions();
        $created = 0;
        $updated = 0;

        foreach ($templates as $template) {
            $tipo = TipoProposicao::where('codigo', $template['codigo'])->first();
            
            if (!$tipo) {
                $this->command->warn("⚠️  Tipo '{$template['codigo']}' não encontrado. Pulando...");
                continue;
            }

            // Criar ou atualizar template
            $templateModel = TipoProposicaoTemplate::updateOrCreate(
                ['tipo_proposicao_id' => $tipo->id],
                [
                    'document_key' => 'template_' . $template['codigo'] . '_seeder_' . time(),
                    'ativo' => true,
                    'updated_by' => null,
                ]
            );

            // Gerar conteúdo RTF
            $conteudoRTF = $this->gerarConteudoRTF($template);
            $nomeArquivo = 'template_' . $template['codigo'] . '_seeder.rtf';
            $caminhoArquivo = 'private/templates/' . $nomeArquivo;

            // Salvar arquivo
            Storage::put($caminhoArquivo, $conteudoRTF);

            // Atualizar caminho do arquivo
            $templateModel->update(['arquivo_path' => $caminhoArquivo]);

            if ($templateModel->wasRecentlyCreated) {
                $created++;
                $this->command->info("✅ Criado: {$template['nome']}");
            } else {
                $updated++;
                $this->command->info("🔄 Atualizado: {$template['nome']}");
            }
        }

        $this->command->line('');
        $this->command->info("📊 Resumo da execução:");
        $this->command->line("   • Criados: $created");
        $this->command->line("   • Atualizados: $updated");
        $this->command->line("   • Total processados: " . ($created + $updated));
        $this->command->info('🎉 Seeder de templates concluído com sucesso!');
    }

    /**
     * Definições dos templates com estruturas específicas
     */
    private function getTemplateDefinitions(): array
    {
        return [
            [
                'codigo' => 'projeto_lei_ordinaria',
                'nome' => 'Projeto de Lei Ordinária',
                'epigrafe' => 'PROJETO DE LEI ORDINÁRIA Nº ${numero_proposicao}/${ano_atual}',
                'ementa' => '${ementa}',
                'preambulo' => 'A CÂMARA MUNICIPAL DECRETA:',
                'articulado' => [
                    'Art. 1º ${texto}',
                    'Parágrafo único. ${detalhamento}',
                    'Art. 2º ${disposicao_complementar}',
                    'Art. 3º Esta lei entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'projeto_lei_complementar',
                'nome' => 'Projeto de Lei Complementar',
                'epigrafe' => 'PROJETO DE LEI COMPLEMENTAR Nº 001/${ano}',
                'ementa' => 'Altera a Lei Orgânica Municipal para ${finalidade} e dá outras providências.',
                'preambulo' => 'A CÂMARA MUNICIPAL DECRETA:',
                'articulado' => [
                    'Art. 1º A Lei Orgânica Municipal passa a vigorar acrescida do seguinte dispositivo:',
                    '"${texto}"',
                    'Art. 2º Esta lei complementar entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'projeto_resolucao',
                'nome' => 'Projeto de Resolução',
                'epigrafe' => 'PROJETO DE RESOLUÇÃO Nº 001/${ano}',
                'ementa' => 'Dispõe sobre matéria de competência da Câmara Municipal e dá outras providências.',
                'preambulo' => 'A CÂMARA MUNICIPAL RESOLVE:',
                'articulado' => [
                    'Art. 1º ${texto}.',
                    'Art. 2º Esta resolução entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'requerimento',
                'nome' => 'Requerimento',
                'epigrafe' => 'REQUERIMENTO Nº ${numero_proposicao}/${ano_atual}',
                'ementa' => '${ementa}',
                'preambulo' => 'Requeiro, nos termos regimentais:',
                'articulado' => [
                    '${texto}',
                    '${justificativa}'
                ]
            ],
            [
                'codigo' => 'indicacao',
                'nome' => 'Indicação',
                'epigrafe' => 'INDICAÇÃO Nº ${numero_proposicao}/${ano_atual}',
                'ementa' => '${ementa}',
                'preambulo' => 'Indico ao Senhor Prefeito Municipal:',
                'articulado' => [
                    '${texto}',
                    '${justificativa}'
                ]
            ],
            [
                'codigo' => 'mocao',
                'nome' => 'Moção',
                'epigrafe' => 'MOÇÃO Nº ${numero_proposicao}/${ano_atual}',
                'ementa' => '${ementa}',
                'preambulo' => 'A Câmara Municipal manifesta:',
                'articulado' => [
                    '${texto}',
                    '${justificativa}',
                    'Resolve dirigir a presente Moção.'
                ]
            ],
            [
                'codigo' => 'emenda',
                'nome' => 'Emenda',
                'epigrafe' => 'EMENDA Nº 001/${ano}',
                'ementa' => 'Emenda ${tipo} ao ${projeto_referencia}.',
                'preambulo' => 'Emenda ao Projeto:',
                'articulado' => [
                    '${texto_emenda}'
                ]
            ],
            [
                'codigo' => 'projeto_decreto_legislativo',
                'nome' => 'Projeto de Decreto Legislativo',
                'epigrafe' => 'PROJETO DE DECRETO LEGISLATIVO Nº 001/${ano}',
                'ementa' => 'Aprova ${assunto} e dá outras providências.',
                'preambulo' => 'A CÂMARA MUNICIPAL DECRETA:',
                'articulado' => [
                    'Art. 1º ${texto}.',
                    'Art. 2º Este decreto legislativo entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'projeto_lei_delegada',
                'nome' => 'Projeto de Lei Delegada',
                'epigrafe' => 'PROJETO DE LEI DELEGADA Nº 001/${ano}',
                'ementa' => 'Dispõe sobre ${assunto} por delegação e dá outras providências.',
                'preambulo' => 'O PREFEITO MUNICIPAL, no uso da delegação conferida pela Lei nº ${lei_delegante}, DECRETA:',
                'articulado' => [
                    'Art. 1º ${texto}.',
                    'Art. 2º Esta lei delegada entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'medida_provisoria',
                'nome' => 'Medida Provisória',
                'epigrafe' => 'MEDIDA PROVISÓRIA Nº 001/${ano}',
                'ementa' => 'Dispõe sobre ${assunto} e dá outras providências.',
                'preambulo' => 'O PREFEITO MUNICIPAL, no uso das atribuições que lhe confere a Lei Orgânica, DECRETA:',
                'articulado' => [
                    'Art. 1º ${texto}.',
                    'Art. 2º Esta medida provisória entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'mensagem_executivo',
                'nome' => 'Mensagem do Executivo',
                'epigrafe' => 'MENSAGEM DO EXECUTIVO Nº 001/${ano}',
                'ementa' => 'Mensagem do Prefeito encaminhando ${assunto}.',
                'preambulo' => 'Senhor Presidente,',
                'articulado' => [
                    'Tenho a honra de encaminhar a Vossa Excelência ${projeto_encaminhado}.',
                    '${justificativa}',
                    'Respeitosamente.'
                ]
            ],
            [
                'codigo' => 'oficio',
                'nome' => 'Ofício',
                'epigrafe' => 'OFÍCIO Nº 001/${ano}',
                'ementa' => 'Ofício sobre ${assunto}.',
                'preambulo' => 'Senhor ${destinatario},',
                'articulado' => [
                    '${texto}',
                    'Atenciosamente.'
                ]
            ],
            [
                'codigo' => 'parecer_comissao',
                'nome' => 'Parecer de Comissão',
                'epigrafe' => 'PARECER DA COMISSÃO DE ${comissao} Nº 001/${ano}',
                'ementa' => 'Parecer sobre o ${projeto_analisado}.',
                'preambulo' => 'RELATÓRIO:',
                'articulado' => [
                    '${relatorio}',
                    'VOTO DO RELATOR:',
                    '${voto}',
                    'DECISÃO DA COMISSÃO:',
                    '${decisao}'
                ]
            ],
            [
                'codigo' => 'projeto_consolidacao_leis',
                'nome' => 'Projeto de Consolidação das Leis',
                'epigrafe' => 'PROJETO DE CONSOLIDAÇÃO DAS LEIS Nº 001/${ano}',
                'ementa' => 'Consolida as leis municipais sobre ${materia}.',
                'preambulo' => 'A CÂMARA MUNICIPAL DECRETA:',
                'articulado' => [
                    'Art. 1º Ficam consolidadas as seguintes leis municipais: ${leis_consolidadas}.',
                    'Art. 2º ${disposicoes_gerais}.',
                    'Art. 3º Esta lei entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'projeto_decreto_congresso',
                'nome' => 'Projeto de Decreto do Congresso',
                'epigrafe' => 'PROJETO DE DECRETO DO CONGRESSO Nº 001/${ano}',
                'ementa' => 'Aprova ${assunto}.',
                'preambulo' => 'O CONGRESSO NACIONAL DECRETA:',
                'articulado' => [
                    'Art. 1º ${texto}.',
                    'Art. 2º Este decreto entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'proposta_emenda_constituicao',
                'nome' => 'Proposta de Emenda à Constituição',
                'epigrafe' => 'PROPOSTA DE EMENDA À CONSTITUIÇÃO Nº 001/${ano}',
                'ementa' => 'Altera a Constituição Federal para ${finalidade}.',
                'preambulo' => 'As Mesas da Câmara dos Deputados e do Senado Federal promulgam a seguinte emenda ao texto constitucional:',
                'articulado' => [
                    'Art. 1º ${alteracao_constitucional}.',
                    'Art. 2º Esta emenda constitucional entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'proposta_emenda_lei_organica',
                'nome' => 'Proposta de Emenda à Lei Orgânica Municipal',
                'epigrafe' => 'PROPOSTA DE EMENDA À LEI ORGÂNICA MUNICIPAL Nº 001/${ano}',
                'ementa' => 'Altera a Lei Orgânica Municipal para ${finalidade}.',
                'preambulo' => 'A CÂMARA MUNICIPAL promulga a seguinte emenda à Lei Orgânica:',
                'articulado' => [
                    'Art. 1º ${alteracao_lei_organica}.',
                    'Art. 2º Esta emenda à Lei Orgânica entra em vigor na data de sua publicação.'
                ]
            ],
            [
                'codigo' => 'recurso',
                'nome' => 'Recurso',
                'epigrafe' => 'RECURSO Nº 001/${ano}',
                'ementa' => 'Recurso contra ${decisao_recorrida}.',
                'preambulo' => 'Venho, respeitosamente, interpor recurso contra ${decisao}.',
                'articulado' => [
                    'DOS FATOS: ${fatos}',
                    'DO DIREITO: ${fundamentacao_juridica}',
                    'DOS PEDIDOS: ${pedidos}'
                ]
            ],
            [
                'codigo' => 'relatorio',
                'nome' => 'Relatório',
                'epigrafe' => 'RELATÓRIO Nº 001/${ano}',
                'ementa' => 'Relatório sobre ${assunto}.',
                'preambulo' => 'RELATÓRIO:',
                'articulado' => [
                    '1. INTRODUÇÃO: ${introducao}',
                    '2. DESENVOLVIMENTO: ${desenvolvimento}',
                    '3. CONCLUSÕES: ${conclusoes}'
                ]
            ],
            [
                'codigo' => 'subemenda',
                'nome' => 'Subemenda',
                'epigrafe' => 'SUBEMENDA Nº 001/${ano}',
                'ementa' => 'Subemenda à Emenda nº ${emenda_referencia}.',
                'preambulo' => 'Subemenda à Emenda:',
                'articulado' => [
                    '${texto_subemenda}'
                ]
            ],
            [
                'codigo' => 'substitutivo',
                'nome' => 'Substitutivo',
                'epigrafe' => 'SUBSTITUTIVO Nº 001/${ano}',
                'ementa' => 'Substitutivo ao ${projeto_referencia}.',
                'preambulo' => 'Substitutivo ao Projeto:',
                'articulado' => [
                    '${texto_substitutivo}'
                ]
            ],
            [
                'codigo' => 'veto',
                'nome' => 'Veto',
                'epigrafe' => 'VETO Nº 001/${ano}',
                'ementa' => 'Veto ${tipo} ao ${projeto_vetado}.',
                'preambulo' => 'Senhor Presidente da Câmara Municipal,',
                'articulado' => [
                    'Comunico a Vossa Excelência que, nos termos da Lei Orgânica, resolvo vetar ${dispositivo_vetado}.',
                    'RAZÕES DO VETO: ${razoes}',
                    'Respeitosamente.'
                ]
            ],
            [
                'codigo' => 'destaque',
                'nome' => 'Destaque',
                'epigrafe' => 'DESTAQUE Nº 001/${ano}',
                'ementa' => 'Destaque para votação em separado.',
                'preambulo' => 'Requer destaque para votação em separado:',
                'articulado' => [
                    '${dispositivo_destacado}'
                ]
            ]
        ];
    }

    /**
     * Gerar conteúdo RTF com formatação LC 95/1998 e todas as variáveis disponíveis
     */
    private function gerarConteudoRTF(array $template): string
    {
        // Cabeçalho RTF com formatação padrão
        $rtf = '{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}\f0\fs24\sl360\slmult1 ';
        
        // CABEÇALHO COMPLETO
        // A variável ${imagem_cabecalho} será processada pelo TemplateProcessorService
        // que irá converter a imagem para o formato RTF correto
        $rtf .= '${imagem_cabecalho}\par ';
        $rtf .= '\qc\b ${cabecalho_nome_camara}\b0\par ';
        $rtf .= '\qc ${cabecalho_endereco}\par ';
        $rtf .= '\qc ${cabecalho_telefone}\par ';
        $rtf .= '\qc ${cabecalho_website}\par \par \ql ';
        
        // Epígrafe com variáveis atualizadas
        $epigrafe = str_replace('${ano}', '${ano_atual}', $template['epigrafe']);
        $epigrafe = $this->converterParaRTF($epigrafe);
        $rtf .= '\b ' . $epigrafe . '\par \b0\par ';
        
        // Ementa
        $ementa = $this->converterParaRTF($template['ementa']);
        $rtf .= '\b EMENTA: \b0 ' . $ementa . '\par \par ';
        
        // Preâmbulo
        $preambulo = $this->converterParaRTF($template['preambulo']);
        $rtf .= '\b ' . $preambulo . '\par \b0\par ';
        
        // Articulado
        foreach ($template['articulado'] as $artigo) {
            $artigoRTF = $this->converterParaRTF($artigo);
            $rtf .= $artigoRTF . '\par \par ';
        }
        
        // RODAPÉ COMPLETO com todas as variáveis disponíveis
        $rtf .= '\par ';
        $rtf .= '\qr ${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}.\par \par ';
        
        // Área de assinatura com dados do autor
        $rtf .= '\qr ${assinatura_padrao}\par ';
        $rtf .= '\qr ${autor_nome}\par ';
        $rtf .= '\qr ${autor_cargo}\par \par ';
        
        // Rodapé institucional
        $rtf .= '\qc\fs18 ${rodape_texto}\fs24\par ';
        
        // Fechar RTF
        $rtf .= '}';
        
        return $rtf;
    }

    /**
     * Converter texto UTF-8 para RTF Unicode
     * Sincronizado com TemplateProcessorService
     */
    private function converterParaRTF(string $texto): string
    {
        // Escapar caracteres especiais do RTF primeiro
        $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);
        
        $resultado = '';
        $length = mb_strlen($texto, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            
            // Converter caracteres não-ASCII para códigos RTF Unicode
            if ($codepoint > 127) {
                $resultado .= '\\u' . $codepoint . '*';
            } else {
                $resultado .= $char;
            }
        }
        
        return $resultado;
    }
}