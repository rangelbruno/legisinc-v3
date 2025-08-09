<?php

namespace App\Console\Commands;

use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerarTemplatesComParametros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:regenerar-com-parametros {--force : Forçar regeneração mesmo se já existir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenera todos os templates incluindo os parâmetros de cabeçalho, rodapé, variáveis dinâmicas e dados da câmara';

    private TemplateParametrosService $parametrosService;

    public function __construct(TemplateParametrosService $parametrosService)
    {
        parent::__construct();
        $this->parametrosService = $parametrosService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando regeneração de templates com todos os parâmetros...');
        
        // Obter todos os parâmetros configurados
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $this->info('📋 Parâmetros carregados: ' . count($parametros) . ' configurações');
        
        // Listar parâmetros por categoria
        $this->table(
            ['Categoria', 'Total de Parâmetros'],
            [
                ['Cabeçalho', $this->contarParametrosPorCategoria($parametros, 'Cabeçalho')],
                ['Rodapé', $this->contarParametrosPorCategoria($parametros, 'Rodapé')],
                ['Variáveis Dinâmicas', $this->contarParametrosPorCategoria($parametros, 'Variáveis Dinâmicas')],
                ['Formatação', $this->contarParametrosPorCategoria($parametros, 'Formatação')],
                ['Dados Gerais da Câmara', $this->contarParametrosPorCategoria($parametros, 'Dados Gerais da Câmara')],
            ]
        );

        // Obter todos os tipos de proposição ativos
        $tipos = TipoProposicao::where('ativo', true)->get();
        $this->info('📝 Tipos de proposição encontrados: ' . $tipos->count());

        $templatesRegenerados = 0;
        $templatesCriados = 0;

        foreach ($tipos as $tipo) {
            $this->info("\n🔨 Processando: {$tipo->nome}");
            
            // Verificar se já existe template
            $template = TipoProposicaoTemplate::firstOrNew(['tipo_proposicao_id' => $tipo->id]);
            
            $novoTemplate = !$template->exists;
            
            if (!$novoTemplate && !$this->option('force')) {
                $this->warn("  ⚠️  Template já existe. Use --force para regenerar.");
                continue;
            }

            // Gerar conteúdo do template
            $conteudoTemplate = $this->gerarConteudoTemplate($tipo, $parametros);
            
            // Converter para RTF
            $conteudoRTF = $this->converterParaRTF($conteudoTemplate, $parametros);
            
            // Salvar arquivo
            $nomeArquivo = 'template_' . $tipo->codigo . '_parametrizado.rtf';
            $caminhoArquivo = 'templates/' . $nomeArquivo;
            
            Storage::put($caminhoArquivo, $conteudoRTF);
            
            // Atualizar registro do template
            $template->document_key = 'template_' . $tipo->id . '_' . time() . '_' . uniqid();
            $template->arquivo_path = $caminhoArquivo;
            $template->ativo = true;
            $template->updated_by = null;
            $template->save();
            
            if ($novoTemplate) {
                $templatesCriados++;
                $this->info("  ✅ Template criado: {$caminhoArquivo}");
            } else {
                $templatesRegenerados++;
                $this->info("  ✅ Template regenerado: {$caminhoArquivo}");
            }
        }

        $this->newLine();
        $this->info('🎉 Regeneração concluída!');
        $this->table(
            ['Resultado', 'Total'],
            [
                ['Templates criados', $templatesCriados],
                ['Templates regenerados', $templatesRegenerados],
                ['Total processado', $templatesCriados + $templatesRegenerados],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Contar parâmetros por categoria
     */
    private function contarParametrosPorCategoria(array $parametros, string $categoria): int
    {
        $count = 0;
        foreach ($parametros as $chave => $valor) {
            if (str_starts_with($chave, $categoria . '.')) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Gerar conteúdo do template com todos os parâmetros
     */
    private function gerarConteudoTemplate(TipoProposicao $tipo, array $parametros): string
    {
        $conteudo = "";
        
        // CABEÇALHO COMPLETO
        $conteudo .= $this->gerarCabecalho($parametros);
        
        // IDENTIFICAÇÃO DO DOCUMENTO
        $conteudo .= "\n\n";
        $conteudo .= strtoupper($tipo->nome) . " Nº \${numero_proposicao}/\${ano}\n";
        $conteudo .= str_repeat("=", 60) . "\n\n";
        
        // EMENTA
        $conteudo .= "EMENTA: \${ementa}\n\n";
        $conteudo .= str_repeat("-", 60) . "\n\n";
        
        // CORPO DO DOCUMENTO
        $conteudo .= $this->gerarCorpoPorTipo($tipo);
        
        // JUSTIFICATIVA (se aplicável)
        if ($this->tipoRequerJustificativa($tipo)) {
            $conteudo .= "\n\nJUSTIFICATIVA\n";
            $conteudo .= str_repeat("-", 30) . "\n\n";
            $conteudo .= "\${justificativa}\n\n";
        }
        
        // ÁREA DE ASSINATURA
        $conteudo .= "\n\n";
        $conteudo .= $parametros['Variáveis Dinâmicas.var_assinatura_padrao'] ?? 
                     "Sala das Sessões, em \${data_atual}.\n\n\n_________________________________\n\${autor_nome}\n\${autor_cargo}";
        
        // RODAPÉ COMPLETO
        $conteudo .= $this->gerarRodape($parametros);
        
        return $conteudo;
    }

    /**
     * Gerar cabeçalho com todos os parâmetros
     */
    private function gerarCabecalho(array $parametros): string
    {
        $cabecalho = "";
        
        // Imagem do cabeçalho (placeholder para RTF)
        if (!empty($parametros['Cabeçalho.cabecalho_imagem'])) {
            $cabecalho .= "[IMAGEM: \${cabecalho_imagem}]\n\n";
        }
        
        // Nome da câmara
        $cabecalho .= $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL';
        $cabecalho .= "\n";
        
        // CNPJ se disponível
        if (!empty($parametros['Dados Gerais da Câmara.cnpj'])) {
            $cabecalho .= "CNPJ: " . $parametros['Dados Gerais da Câmara.cnpj'] . "\n";
        }
        
        // Endereço
        if (!empty($parametros['Cabeçalho.cabecalho_endereco'])) {
            $cabecalho .= $parametros['Cabeçalho.cabecalho_endereco'] . "\n";
        } else if (!empty($parametros['Dados Gerais da Câmara.endereco_logradouro'])) {
            $cabecalho .= $parametros['Dados Gerais da Câmara.endereco_logradouro'];
            if (!empty($parametros['Dados Gerais da Câmara.endereco_bairro'])) {
                $cabecalho .= ", " . $parametros['Dados Gerais da Câmara.endereco_bairro'];
            }
            if (!empty($parametros['Dados Gerais da Câmara.endereco_cep'])) {
                $cabecalho .= " - CEP: " . $parametros['Dados Gerais da Câmara.endereco_cep'];
            }
            $cabecalho .= "\n";
        }
        
        // Município/UF
        $municipio = $parametros['Dados Gerais da Câmara.municipio_nome'] ?? 'São Paulo';
        $uf = $parametros['Dados Gerais da Câmara.municipio_uf'] ?? 'SP';
        $cabecalho .= "{$municipio}/{$uf}\n";
        
        // Contatos
        if (!empty($parametros['Cabeçalho.cabecalho_telefone'])) {
            $cabecalho .= "Tel: " . $parametros['Cabeçalho.cabecalho_telefone'];
        }
        if (!empty($parametros['Cabeçalho.cabecalho_website'])) {
            $cabecalho .= " | " . $parametros['Cabeçalho.cabecalho_website'];
        }
        $cabecalho .= "\n";
        
        $cabecalho .= str_repeat("=", 80) . "\n";
        
        return $cabecalho;
    }

    /**
     * Gerar rodapé com todos os parâmetros
     */
    private function gerarRodape(array $parametros): string
    {
        $rodape = "\n\n" . str_repeat("-", 80) . "\n";
        
        // Texto do rodapé
        if (!empty($parametros['Rodapé.rodape_texto'])) {
            $rodape .= $parametros['Rodapé.rodape_texto'] . "\n";
        }
        
        // Informações completas da câmara
        $rodape .= "\n";
        $rodape .= $parametros['Dados Gerais da Câmara.nome_camara_oficial'] ?? 'CÂMARA MUNICIPAL';
        $rodape .= "\n";
        
        // Endereço completo
        if (!empty($parametros['Dados Gerais da Câmara.endereco_logradouro'])) {
            $rodape .= $parametros['Dados Gerais da Câmara.endereco_logradouro'];
            if (!empty($parametros['Dados Gerais da Câmara.endereco_bairro'])) {
                $rodape .= ", " . $parametros['Dados Gerais da Câmara.endereco_bairro'];
            }
            if (!empty($parametros['Dados Gerais da Câmara.endereco_cep'])) {
                $rodape .= " - CEP: " . $parametros['Dados Gerais da Câmara.endereco_cep'];
            }
            $rodape .= "\n";
        }
        
        // Município/UF
        $municipio = $parametros['Dados Gerais da Câmara.municipio_nome'] ?? 'São Paulo';
        $uf = $parametros['Dados Gerais da Câmara.municipio_uf'] ?? 'SP';
        $rodape .= "{$municipio}/{$uf}\n";
        
        // Telefones
        if (!empty($parametros['Dados Gerais da Câmara.telefone_principal'])) {
            $rodape .= "Tel: " . $parametros['Dados Gerais da Câmara.telefone_principal'];
            if (!empty($parametros['Dados Gerais da Câmara.telefone_protocolo'])) {
                $rodape .= " | Protocolo: " . $parametros['Dados Gerais da Câmara.telefone_protocolo'];
            }
            $rodape .= "\n";
        }
        
        // E-mail e website
        if (!empty($parametros['Dados Gerais da Câmara.email_oficial'])) {
            $rodape .= "E-mail: " . $parametros['Dados Gerais da Câmara.email_oficial'];
        }
        if (!empty($parametros['Dados Gerais da Câmara.website'])) {
            $rodape .= " | " . $parametros['Dados Gerais da Câmara.website'];
        }
        $rodape .= "\n";
        
        // Horários
        if (!empty($parametros['Dados Gerais da Câmara.horario_funcionamento'])) {
            $rodape .= "Horário de Funcionamento: " . $parametros['Dados Gerais da Câmara.horario_funcionamento'] . "\n";
        }
        if (!empty($parametros['Dados Gerais da Câmara.horario_protocolo'])) {
            $rodape .= "Horário do Protocolo: " . $parametros['Dados Gerais da Câmara.horario_protocolo'] . "\n";
        }
        
        // Numeração de página (se configurado)
        if (!empty($parametros['Rodapé.rodape_numeracao']) && $parametros['Rodapé.rodape_numeracao']) {
            $rodape .= "\n[Página \${pagina_atual} de \${total_paginas}]";
        }
        
        return $rodape;
    }

    /**
     * Gerar corpo do documento baseado no tipo
     */
    private function gerarCorpoPorTipo(TipoProposicao $tipo): string
    {
        $tipoLower = strtolower($tipo->codigo);
        
        if (str_contains($tipoLower, 'lei')) {
            return $this->gerarCorpoLei();
        } elseif (str_contains($tipoLower, 'indicacao')) {
            return $this->gerarCorpoIndicacao();
        } elseif (str_contains($tipoLower, 'requerimento')) {
            return $this->gerarCorpoRequerimento();
        } elseif (str_contains($tipoLower, 'mocao')) {
            return $this->gerarCorpoMocao();
        } elseif (str_contains($tipoLower, 'resolucao')) {
            return $this->gerarCorpoResolucao();
        } elseif (str_contains($tipoLower, 'decreto')) {
            return $this->gerarCorpoDecreto();
        } else {
            return $this->gerarCorpoPadrao();
        }
    }

    private function gerarCorpoLei(): string
    {
        return "O PRESIDENTE DA CÂMARA MUNICIPAL DE \${municipio}\n\n" .
               "Faço saber que a Câmara Municipal aprovou e eu promulgo a seguinte Lei:\n\n" .
               "Art. 1º \${texto_artigo_1}\n\n" .
               "Parágrafo único. \${texto_paragrafo_unico}\n\n" .
               "Art. 2º \${texto_artigo_2}\n\n" .
               "Art. 3º Esta Lei entra em vigor na data de sua publicação.\n\n" .
               "Art. 4º Revogam-se as disposições em contrário.";
    }

    private function gerarCorpoIndicacao(): string
    {
        return "INDICO ao Excelentíssimo Senhor Prefeito Municipal, nos termos regimentais, que:\n\n" .
               "\${texto}\n\n" .
               "CONSIDERANDOS:\n\n" .
               "- \${considerando_1}\n" .
               "- \${considerando_2}\n" .
               "- \${considerando_3}";
    }

    private function gerarCorpoRequerimento(): string
    {
        return "REQUEIRO à Mesa, ouvido o Plenário e cumpridas as formalidades regimentais, que:\n\n" .
               "\${texto}\n\n" .
               "JUSTIFICATIVA:\n" .
               "\${justificativa_requerimento}";
    }

    private function gerarCorpoMocao(): string
    {
        return "A CÂMARA MUNICIPAL DE \${municipio}, através de seus representantes legais,\n\n" .
               "CONSIDERANDO \${considerando_mocao_1};\n\n" .
               "CONSIDERANDO \${considerando_mocao_2};\n\n" .
               "CONSIDERANDO \${considerando_mocao_3};\n\n" .
               "MANIFESTA \${tipo_manifestacao} a \${destinatario_mocao}\n\n" .
               "\${texto_mocao}";
    }

    private function gerarCorpoResolucao(): string
    {
        return "A MESA DA CÂMARA MUNICIPAL DE \${municipio}, no uso de suas atribuições legais,\n\n" .
               "RESOLVE:\n\n" .
               "Art. 1º \${texto_artigo_1_resolucao}\n\n" .
               "Art. 2º \${texto_artigo_2_resolucao}\n\n" .
               "Art. 3º Esta Resolução entra em vigor na data de sua publicação.";
    }

    private function gerarCorpoDecreto(): string
    {
        return "A MESA DA CÂMARA MUNICIPAL DE \${municipio}, no uso de suas atribuições legais e regimentais,\n\n" .
               "DECRETA:\n\n" .
               "Art. 1º \${texto_artigo_1_decreto}\n\n" .
               "Art. 2º \${texto_artigo_2_decreto}\n\n" .
               "Art. 3º Este Decreto Legislativo entra em vigor na data de sua publicação.";
    }

    private function gerarCorpoPadrao(): string
    {
        return "\${texto}\n\n" .
               "DISPOSIÇÕES GERAIS:\n\n" .
               "\${disposicoes_gerais}";
    }

    /**
     * Verificar se tipo requer justificativa
     */
    private function tipoRequerJustificativa(TipoProposicao $tipo): bool
    {
        $tiposComJustificativa = [
            'projeto_lei_ordinaria',
            'projeto_lei_complementar',
            'indicacao',
            'requerimento'
        ];
        
        return in_array(strtolower($tipo->codigo), $tiposComJustificativa);
    }

    /**
     * Converter texto para RTF com formatação e UTF-8 correto
     */
    private function converterParaRTF(string $texto, array $parametros): string
    {
        $fonte = $parametros['Formatação.format_fonte'] ?? 'Arial';
        $tamanhoFonte = (int)($parametros['Formatação.format_tamanho_fonte'] ?? 12);
        $espacamento = $parametros['Formatação.format_espacamento'] ?? '1.5';
        
        // Converter espaçamento para RTF
        $espacamentoRTF = match($espacamento) {
            '1' => 'sl240',
            '1.5' => 'sl360',
            '2' => 'sl480',
            default => 'sl360'
        };

        // Cabeçalho RTF
        $rtf = "{\\rtf1\\ansi\\ansicpg65001\\deff0 {\\fonttbl {\\f0 {$fonte};}}";
        $rtf .= "\\f0\\fs" . ($tamanhoFonte * 2);
        $rtf .= "\\{$espacamentoRTF}\\slmult1 ";

        // Converter texto para RTF
        $textoConvertido = $this->converterUtf8ParaRtf($texto);
        
        $rtf .= $textoConvertido;
        $rtf .= "}";

        return $rtf;
    }

    /**
     * Converter UTF-8 para RTF
     */
    private function converterUtf8ParaRtf(string $texto): string
    {
        $textoProcessado = '';
        
        // Escapar caracteres especiais do RTF
        $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);
        
        // Processar caractere por caractere
        $length = mb_strlen($texto, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            
            if ($codepoint > 127) {
                $textoProcessado .= '\\u' . $codepoint . '*';
            } else {
                if ($char === "\n") {
                    $textoProcessado .= '\\par ';
                } else {
                    $textoProcessado .= $char;
                }
            }
        }
        
        return $textoProcessado;
    }
}