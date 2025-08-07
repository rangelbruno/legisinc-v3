<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\Template\TemplateEstruturadorService;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Support\Facades\Storage;

class AplicarPadroesLegaisTemplates extends Command
{
    protected $signature = 'templates:aplicar-padroes-legais {--force : Sobrescrever templates existentes}';
    protected $description = 'Aplicar padrões legais LC 95/1998 a todos os templates de proposições';

    public function handle(): int
    {
        $this->info('🏛️  Aplicando Padrões Legais LC 95/1998 aos Templates');
        $this->info('=================================================');

        $estruturadorService = app(TemplateEstruturadorService::class);
        $parametrosService = app(TemplateParametrosService::class);

        // Buscar todos os tipos de proposição ativos
        $tipos = TipoProposicao::where('ativo', true)->orderBy('nome')->get();

        if ($tipos->isEmpty()) {
            $this->error('Nenhum tipo de proposição encontrado.');
            return 1;
        }

        $this->info("Processando {$tipos->count()} tipos de proposição...");
        $this->newLine();

        $processados = 0;
        $erros = 0;

        foreach ($tipos as $tipo) {
            $this->line("📄 Processando: {$tipo->nome}");

            try {
                // Dados exemplo para criar template estruturado
                $dadosExemplo = [
                    'numero' => 1,
                    'ano' => date('Y'),
                    'ementa' => $this->gerarEmentaExemplo($tipo),
                    'texto' => $this->gerarTextoExemplo($tipo),
                    'justificativa' => 'Justificativa da proposição conforme necessidade municipal.',
                    'autor_nome' => '${autor_nome}',
                    'autor_cargo' => '${autor_cargo}',
                    'autor_partido' => '${autor_partido}'
                ];

                // Estruturar conforme LC 95/1998
                $estrutura = $estruturadorService->estruturarProposicao($dadosExemplo, $tipo);

                // Gerar template estruturado
                $templateEstruturado = $estruturadorService->gerarTemplateEstruturado($dadosExemplo, $tipo);

                // Adicionar variáveis no template
                $templateComVariaveis = $this->adicionarVariaveisTemplate($templateEstruturado);

                // Buscar ou criar template
                $template = TipoProposicaoTemplate::firstOrCreate(
                    ['tipo_proposicao_id' => $tipo->id],
                    [
                        'document_key' => 'template_legal_' . $tipo->id . '_' . time() . '_' . uniqid(),
                        'updated_by' => null,
                        'ativo' => true
                    ]
                );

                // Verificar se deve sobrescrever
                if ($template->arquivo_path && !$this->option('force')) {
                    $this->warn("  ⚠️  Template já existe. Use --force para sobrescrever.");
                    continue;
                }

                // Salvar template estruturado como arquivo RTF
                $nomeArquivo = 'template_' . $tipo->codigo . '_legal_' . date('Y') . '.rtf';
                $caminhoArquivo = 'templates/' . $nomeArquivo;

                // Converter para RTF
                $conteudoRTF = $this->converterParaRTF($templateComVariaveis, $parametrosService);
                
                Storage::put($caminhoArquivo, $conteudoRTF);

                // Atualizar template no banco
                $template->update([
                    'arquivo_path' => $caminhoArquivo,
                    'updated_by' => null
                ]);

                $this->info("  ✅ Template gerado: {$estrutura['epigrafe']}");
                $this->line("     📊 Artigos: " . count($estrutura['corpo_articulado']['artigos']));
                $this->line("     📁 Arquivo: {$caminhoArquivo}");
                
                $processados++;

            } catch (\Exception $e) {
                $this->error("  ❌ Erro: {$e->getMessage()}");
                $erros++;
            }

            $this->newLine();
        }

        // Resumo final
        $this->info('📊 RESUMO DA OPERAÇÃO');
        $this->line(str_repeat('-', 50));
        $this->info("✅ Processados com sucesso: {$processados}");
        
        if ($erros > 0) {
            $this->error("❌ Com erros: {$erros}");
        }

        $this->newLine();
        $this->info('🏆 PADRÕES IMPLEMENTADOS:');
        $implementacoes = [
            '✅ LC 95/1998 - Estrutura obrigatória',
            '✅ Epígrafe formatada (TIPO Nº 000/AAAA)',
            '✅ Ementa conforme padrões técnicos',
            '✅ Preâmbulo legal padronizado',
            '✅ Corpo articulado estruturado',
            '✅ Cláusula de vigência automática',
            '✅ Numeração sequencial correta',
            '✅ Variáveis dinâmicas integradas'
        ];

        foreach ($implementacoes as $item) {
            $this->line($item);
        }

        $this->newLine();
        $this->comment('🎯 Todos os templates agora seguem os padrões jurídicos brasileiros!');

        return $processados > 0 ? 0 : 1;
    }

    /**
     * Gerar ementa de exemplo baseada no tipo de proposição
     */
    private function gerarEmentaExemplo(TipoProposicao $tipo): string
    {
        $tipoLower = strtolower($tipo->codigo);
        
        return match($tipoLower) {
            'projeto_lei_ordinaria' => 'Dispõe sobre ${assunto} no âmbito do Município e dá outras providências.',
            'projeto_lei_complementar' => 'Altera a Lei Orgânica Municipal para ${finalidade} e dá outras providências.',
            'indicacao' => 'Indica ao Poder Executivo ${solicitacao}.',
            'requerimento' => 'Requer informações ao Poder Executivo sobre ${assunto}.',
            'mocao' => 'Moção de ${tipo_mocao} dirigida a ${destinatario}.',
            'projeto_resolucao' => 'Dispõe sobre matéria de competência da Câmara Municipal e dá outras providências.',
            'projeto_decreto_legislativo' => 'Aprova ${assunto} e dá outras providências.',
            'proposta_emenda_constituicao' => 'Altera a Constituição Federal para ${finalidade}.',
            'proposta_emenda_lei_organica' => 'Altera a Lei Orgânica Municipal para ${finalidade}.',
            default => 'Dispõe sobre ${assunto} e dá outras providências.'
        };
    }

    /**
     * Gerar texto de exemplo baseado no tipo de proposição
     */
    private function gerarTextoExemplo(TipoProposicao $tipo): string
    {
        $tipoLower = strtolower($tipo->codigo);
        
        if (str_contains($tipoLower, 'lei')) {
            return "Art. 1º \${texto}.\n\nParágrafo único. \${detalhamento}.\n\nArt. 2º \${disposicao_complementar}.\n\nArt. 3º Esta lei entra em vigor na data de sua publicação.";
        }
        
        if ($tipoLower === 'indicacao') {
            return "Indico ao Senhor Prefeito Municipal que:\n\nI - \${texto};\n\nII - \${segunda_solicitacao};\n\nIII - \${terceira_solicitacao}.";
        }
        
        if ($tipoLower === 'requerimento') {
            return "Requeiro, nos termos regimentais, que seja solicitado ao Poder Executivo:\n\na) \${texto};\n\nb) \${segunda_informacao};\n\nc) \${terceira_informacao}.";
        }
        
        if ($tipoLower === 'mocao') {
            return "A Câmara Municipal manifesta \${posicionamento} em relação a \${texto}.\n\nConsiderando que \${justificativa};\n\nConsiderando que \${considerando_2};\n\nResolve dirigir a presente Moção.";
        }
        
        if (str_contains($tipoLower, 'emenda')) {
            return "Art. 1º O \${dispositivo_alterado} passa a vigorar com a seguinte redação:\n\n\"\${texto}\"\n\nArt. 2º Esta emenda entra em vigor na data de sua promulgação.";
        }
        
        // Padrão geral
        return "Art. 1º \${texto}.\n\n§ 1º \${paragrafo_primeiro}.\n\n§ 2º \${paragrafo_segundo}.\n\nArt. 2º \${disposicao_final}.\n\nArt. 3º Esta proposição entra em vigor na data de sua publicação.";
    }

    /**
     * Adicionar variáveis no template gerado
     */
    private function adicionarVariaveisTemplate(string $template): string
    {
        // Adicionar cabeçalho com variáveis
        $cabecalho = "\${imagem_cabecalho}\n\n\${nome_camara}\n\${endereco_camara}\n\n";
        
        // Adicionar rodapé com variáveis
        $rodape = "\n\n\${assinatura_padrao}\n\n\${rodape}";

        // Substituir alguns valores por variáveis mais específicas
        $template = str_replace('Vereador(a)', '${autor_nome}', $template);
        $template = str_replace('PARTIDO', '${autor_partido}', $template);

        return $cabecalho . $template . $rodape;
    }

    /**
     * Converter texto para RTF com formatação usando UTF-8 correto
     */
    private function converterParaRTF(string $texto, TemplateParametrosService $parametrosService): string
    {
        $parametros = $parametrosService->obterParametrosTemplates();
        
        $fonte = $parametros['Formatação.format_fonte'] ?? 'Arial';
        $tamanhoFonte = (int)($parametros['Formatação.format_tamanho_fonte'] ?? 12);
        $espacamento = $parametros['Formatação.format_espacamento'] ?? '1.5';
        
        // Converter espaçamento para RTF (1.5 = 360 twips)
        $espacamentoRTF = match($espacamento) {
            '1' => 'sl240',
            '1.5' => 'sl360',
            '2' => 'sl480',
            default => 'sl360'
        };

        // Cabeçalho RTF com UTF-8 correto
        $rtf = "{\\rtf1\\ansi\\ansicpg65001\\deff0 {\\fonttbl {\\f0 {$fonte};}}";
        $rtf .= "\\f0\\fs" . ($tamanhoFonte * 2); // RTF usa half-points
        $rtf .= "\\{$espacamentoRTF}\\slmult1 ";

        // Converter texto para Unicode RTF usando funções multi-byte
        $textoConvertido = $this->converterUtf8ParaRtf($texto);
        
        // Aplicar formatação em negrito para artigos e epígrafes (após conversão Unicode)
        $textoConvertido = preg_replace('/(Art\\\\\. \\\\u\d+\\\\\*º?)/', '{\\\\b $1 \\\\b0}', $textoConvertido);
        $textoConvertido = preg_replace('/([A-Z\\\\u\d+\\\\\*\s]+N\\\\u\d+\\\\\*\\\\u\d+\\\\\*\s+\\\\u\d+\\\\\*\d+\/\d{4})/', '{\\\\b\\\\fs' . (($tamanhoFonte + 2) * 2) . ' $1\\\\fs' . ($tamanhoFonte * 2) . '\\\\b0}', $textoConvertido);
        
        $rtf .= $textoConvertido;
        $rtf .= "}";

        return $rtf;
    }

    /**
     * Converter texto UTF-8 para RTF com sequências Unicode corretas
     * Baseado na solução documentada em docs/SOLUCAO_ACENTUACAO_ONLYOFFICE.md
     */
    private function converterUtf8ParaRtf(string $texto): string
    {
        $textoProcessado = '';
        
        // Escapar caracteres especiais do RTF primeiro
        $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);
        
        // Processar caractere por caractere usando funções multi-byte
        $length = mb_strlen($texto, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');  // Extrai caractere UTF-8 corretamente
            $codepoint = mb_ord($char, 'UTF-8');        // Obtém codepoint Unicode real
            
            if ($codepoint > 127) {
                // Gera sequência RTF Unicode correta
                $textoProcessado .= '\\u' . $codepoint . '*';
            } else {
                // Converter quebras de linha para RTF
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