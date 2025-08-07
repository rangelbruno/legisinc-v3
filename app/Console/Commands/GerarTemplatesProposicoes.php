<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GerarTemplatesProposicoes extends Command
{
    protected $signature = 'templates:gerar-automaticos 
                          {--tipo=* : Gerar apenas para tipos específicos}
                          {--force : Sobrescrever templates existentes}';

    protected $description = 'Gerar templates automáticos para todos os tipos de proposição usando parâmetros';

    protected TemplateParametrosService $parametrosService;

    public function __construct(TemplateParametrosService $parametrosService)
    {
        parent::__construct();
        $this->parametrosService = $parametrosService;
    }

    public function handle()
    {
        $this->info('🚀 Iniciando geração de templates com padrões legais LC 95/1998...');

        // Obter parâmetros do sistema
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $this->info('📋 Parâmetros carregados: ' . count($parametros));

        // Buscar tipos de proposição
        $query = TipoProposicao::where('ativo', true);
        
        if ($this->option('tipo')) {
            $query->whereIn('codigo', $this->option('tipo'));
        }
        
        $tipos = $query->orderBy('nome')->get();
        $this->info("📄 Tipos encontrados: {$tipos->count()}");

        if ($tipos->isEmpty()) {
            $this->warn('Nenhum tipo de proposição encontrado!');
            return Command::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($tipos as $tipo) {
            $this->line("🔧 Processando: {$tipo->nome} ({$tipo->codigo})");

            // Verificar se já existe template
            $templateExistente = TipoProposicaoTemplate::where('tipo_proposicao_id', $tipo->id)->first();
            
            if ($templateExistente && !$this->option('force')) {
                $this->warn("   ⏭️  Template já existe - use --force para sobrescrever");
                $skipped++;
                continue;
            }

            try {
                if ($templateExistente) {
                    // Atualizar existente
                    $template = $this->atualizarTemplate($templateExistente, $tipo, $parametros);
                    $updated++;
                    $this->info("   ✅ Template atualizado");
                } else {
                    // Criar novo
                    $template = $this->criarTemplate($tipo, $parametros);
                    $created++;
                    $this->info("   ✅ Template criado");
                }

            } catch (\Exception $e) {
                $this->error("   ❌ Erro: {$e->getMessage()}");
                continue;
            }
        }

        $this->newLine();
        $this->info("📊 Resumo da execução:");
        $this->line("   • Criados: {$created}");
        $this->line("   • Atualizados: {$updated}");
        $this->line("   • Ignorados: {$skipped}");
        
        $this->info('🎉 Geração de templates concluída!');
        
        return Command::SUCCESS;
    }

    private function criarTemplate(TipoProposicao $tipo, array $parametros): TipoProposicaoTemplate
    {
        // Gerar conteúdo do template baseado no tipo
        $conteudo = $this->gerarConteudoTemplate($tipo, $parametros);
        
        // Criar arquivo
        $nomeArquivo = $this->gerarNomeArquivo($tipo);
        $caminhoArquivo = "templates/{$nomeArquivo}";
        
        // Salvar no diretório correto onde estão os outros templates
        $pathCompleto = storage_path('app/' . $caminhoArquivo);
        file_put_contents($pathCompleto, $conteudo);

        // Criar registro no banco
        return TipoProposicaoTemplate::create([
            'tipo_proposicao_id' => $tipo->id,
            'nome' => "Template {$tipo->nome}",
            'arquivo_path' => $caminhoArquivo,
            'document_key' => $this->gerarDocumentKey($tipo),
            'variaveis' => $this->obterVariaveisTemplate($tipo),
            'ativo' => true
        ]);
    }

    private function atualizarTemplate(TipoProposicaoTemplate $template, TipoProposicao $tipo, array $parametros): TipoProposicaoTemplate
    {
        // Gerar novo conteúdo
        $conteudo = $this->gerarConteudoTemplate($tipo, $parametros);
        
        // Sempre gerar novo arquivo_path para manter padrão consistente
        $nomeArquivo = $this->gerarNomeArquivo($tipo);
        $caminhoArquivo = "templates/{$nomeArquivo}";
        
        // Backup do arquivo antigo se existir
        if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
            $backupPath = str_replace('.rtf', '_backup_' . date('Y_m_d_His') . '.rtf', $template->arquivo_path);
            Storage::copy($template->arquivo_path, $backupPath);
        }
        
        // Criar novo arquivo usando disco local explícito
        // Salvar no diretório correto onde estão os outros templates
        $pathCompleto = storage_path('app/' . $caminhoArquivo);
        file_put_contents($pathCompleto, $conteudo);
        
        // Atualizar registro
        $template->update([
            'nome' => "Template {$tipo->nome}",
            'arquivo_path' => $caminhoArquivo,
            'variaveis' => $this->obterVariaveisTemplate($tipo),
            'updated_at' => now()
        ]);

        return $template;
    }

    private function gerarConteudoTemplate(TipoProposicao $tipo, array $parametros): string
    {
        // Template base com estrutura padrão
        $template = $this->obterTemplateBase($tipo);
        
        // Aplicar parâmetros de cabeçalho
        // Primeiro, verificar se deve usar imagem ou texto
        $valorImagem = $parametros['Cabeçalho.cabecalho_imagem'] ?? null;
        $usarImagem = !empty($valorImagem);
        
        if ($usarImagem) {
            // Usar placeholder para imagem no cabeçalho
            $cabecalhoImagem = '${imagem_cabecalho}';
            $template = str_replace('{{NOME_CAMARA}}', $cabecalhoImagem, $template);
            $template = str_replace('{{ENDERECO_CAMARA}}', '', $template);
            $template = str_replace('{{TELEFONE_CAMARA}}', '', $template);
        } else {
            // Usar texto dos parâmetros como fallback
            if (!empty($parametros['Cabeçalho.cabecalho_nome_camara'])) {
                $template = str_replace('{{NOME_CAMARA}}', $parametros['Cabeçalho.cabecalho_nome_camara'], $template);
            }
            
            if (!empty($parametros['Cabeçalho.cabecalho_endereco'])) {
                $template = str_replace('{{ENDERECO_CAMARA}}', $parametros['Cabeçalho.cabecalho_endereco'], $template);
            }
            
            if (!empty($parametros['Cabeçalho.cabecalho_telefone'])) {
                $template = str_replace('{{TELEFONE_CAMARA}}', $parametros['Cabeçalho.cabecalho_telefone'], $template);
            }
        }

        // Aplicar parâmetros de formatação
        $fonte = $parametros['Formatação.format_fonte'] ?? 'Arial';
        $tamanhoFonte = $parametros['Formatação.format_tamanho_fonte'] ?? '12';
        
        // RTF com formatação básica
        $template = $this->aplicarFormatacaoRTF($template, $fonte, $tamanhoFonte);
        
        return $template;
    }

    private function obterTemplateBase(TipoProposicao $tipo): string
    {
        // Templates específicos por tipo de proposição
        $templates = [
            'projeto_lei_ordinaria' => $this->getTemplateProjeto('Lei Ordinária'),
            'projeto_lei_complementar' => $this->getTemplateProjeto('Lei Complementar'),
            'indicacao' => $this->getTemplateIndicacao(),
            'mocao' => $this->getTemplateMocao(),
            'requerimento' => $this->getTemplateRequerimento(),
            'projeto_decreto_legislativo' => $this->getTemplateProjeto('Decreto Legislativo'),
            'projeto_resolucao' => $this->getTemplateProjeto('Resolução'),
        ];

        return $templates[$tipo->codigo] ?? $this->getTemplateGenerico($tipo->nome);
    }

    private function getTemplateProjeto(string $tipoNome): string
    {
        return '{{NOME_CAMARA}}
{{ENDERECO_CAMARA}}
{{TELEFONE_CAMARA}}

' . strtoupper($tipoNome) . ' Nº ${numero_proposicao}

EMENTA: ${ementa}

Art. 1º ${texto}

Art. 2º Esta Lei entra em vigor na data de sua publicação.

${municipio}, ${data_atual}.

${assinatura_padrao}
';
    }

    private function getTemplateIndicacao(): string
    {
        return '{{NOME_CAMARA}}
{{ENDERECO_CAMARA}}
{{TELEFONE_CAMARA}}

INDICAÇÃO Nº ${numero_proposicao}

${autor_nome}

INDICA ${ementa}

Senhor Presidente,

${texto}

Sendo o que se apresenta para a elevada apreciação desta Casa Legislativa.

${municipio}, ${data_atual}.

${assinatura_padrao}
';
    }

    private function getTemplateMocao(): string
    {
        return '{{NOME_CAMARA}}
{{ENDERECO_CAMARA}}
{{TELEFONE_CAMARA}}

MOÇÃO Nº ${numero_proposicao}

${autor_nome}

${ementa}

Senhor Presidente,

${texto}

É o que se apresenta para a elevada apreciação dos nobres Pares.

${municipio}, ${data_atual}.

${assinatura_padrao}
';
    }

    private function getTemplateRequerimento(): string
    {
        return '{{NOME_CAMARA}}
{{ENDERECO_CAMARA}}
{{TELEFONE_CAMARA}}

REQUERIMENTO Nº ${numero_proposicao}

${autor_nome}

${ementa}

Senhor Presidente,

${texto}

Termos em que peço deferimento.

${municipio}, ${data_atual}.

${assinatura_padrao}
';
    }

    private function getTemplateGenerico(string $tipoNome): string
    {
        return '{{NOME_CAMARA}}
{{ENDERECO_CAMARA}}
{{TELEFONE_CAMARA}}

' . strtoupper($tipoNome) . ' Nº ${numero_proposicao}

EMENTA: ${ementa}

${texto}

${municipio}, ${data_atual}.

${assinatura_padrao}
';
    }

    private function aplicarFormatacaoRTF(string $conteudo, string $fonte, string $tamanho): string
    {
        // Converter para RTF com formatação completa e UTF-8 correto
        $rtfContent = '{\\rtf1\\ansi\\ansicpg65001\\deff0\\deflang1046';
        $rtfContent .= '{\\fonttbl{\\f0\\froman\\fcharset0 ' . $fonte . ';}}';
        $rtfContent .= '{\\colortbl;\\red0\\green0\\blue0;}';
        $rtfContent .= '\\viewkind4\\uc1\\pard\\cf1\\f0\\fs' . ($tamanho * 2) . ' ';
        
        // Converter conteúdo para RTF com UTF-8 correto
        $conteudoRTF = $this->converterUtf8ParaRtf($conteudo);
        
        $rtfContent .= $conteudoRTF . '}';
        
        return $rtfContent;
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

    private function gerarNomeArquivo(TipoProposicao $tipo): string
    {
        return "template_" . Str::slug($tipo->codigo) . "_" . time() . ".rtf";
    }

    private function gerarDocumentKey(TipoProposicao $tipo): string
    {
        return "template_" . $tipo->id . "_" . time() . "_" . substr(md5($tipo->codigo), 0, 8);
    }

    private function obterVariaveisTemplate(TipoProposicao $tipo): array
    {
        // Variáveis específicas por tipo
        $variaveisBase = [
            'numero_proposicao',
            'ementa', 
            'texto',
            'autor_nome',
            'autor_cargo',
            'data_atual',
            'municipio',
            'assinatura_padrao'
        ];

        // Adicionar variáveis específicas por tipo
        $variaveisExtras = [
            'projeto_lei_ordinaria' => ['justificativa', 'artigos'],
            'projeto_lei_complementar' => ['justificativa', 'artigos'],
            'indicacao' => ['destinatario', 'assunto'],
            'mocao' => ['tipo_mocao', 'destinatario'],
            'requerimento' => ['solicitacao', 'fundamentacao'],
        ];

        $variaveis = $variaveisBase;
        if (isset($variaveisExtras[$tipo->codigo])) {
            $variaveis = array_merge($variaveis, $variaveisExtras[$tipo->codigo]);
        }

        return $variaveis;
    }
}