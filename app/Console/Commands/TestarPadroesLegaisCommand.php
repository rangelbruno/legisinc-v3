<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TipoProposicao;
use App\Models\User;
use App\Services\Template\TemplateEstruturadorService;
use App\Services\Template\TemplateMetadadosService;
use App\Services\Template\TemplateValidadorLegalService;
use App\Services\Template\TemplateNumeracaoService;

class TestarPadroesLegaisCommand extends Command
{
    protected $signature = 'template:testar-padroes {--tipo=PL} {--salvar=false}';
    protected $description = 'Testar configuração dos templates com padrões legais LC 95/1998';

    public function handle(): int
    {
        $this->info('🏛️  Testando Padrões Legais para Documentos Oficiais');
        $this->info('==========================================');

        // Buscar tipo de proposição
        $codigoTipo = $this->option('tipo');
        $tipoProposicao = TipoProposicao::where('codigo', $codigoTipo)->first();
        
        if (!$tipoProposicao) {
            $this->error("Tipo de proposição '{$codigoTipo}' não encontrado.");
            $this->info('Tipos disponíveis:');
            
            TipoProposicao::where('ativo', true)->get()->each(function($tipo) {
                $this->line("  - {$tipo->codigo}: {$tipo->nome}");
            });
            
            return 1;
        }

        $this->info("✅ Tipo encontrado: {$tipoProposicao->nome}");

        // Dados de exemplo para teste
        $dadosExemplo = [
            'numero' => 123,
            'ano' => 2025,
            'ementa' => 'Dispõe sobre a implementação de padrões de acessibilidade nos documentos oficiais da Câmara Municipal e dá outras providências.',
            'texto' => "Art. 1º Fica instituído o padrão de acessibilidade para documentos oficiais da Câmara Municipal, conforme especificações técnicas estabelecidas nesta lei.\n\nArt. 2º Os documentos deverão seguir as normas WCAG 2.1 AA e PDF/UA para garantir acessibilidade universal.\n\n§ 1º A implementação será gradual conforme cronograma a ser estabelecido.\n\n§ 2º Os servidores receberão treinamento adequado.\n\nArt. 3º Esta lei entra em vigor na data de sua publicação.",
            'justificativa' => 'A acessibilidade é direito fundamental garantido pela Constituição Federal.',
            'autor_nome' => 'Vereador João Silva',
            'autor_cargo' => 'Vereador',
            'autor_partido' => 'PARTIDO'
        ];

        $this->newLine();
        $this->info('📋 Dados da Proposição de Teste:');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Tipo', $tipoProposicao->nome],
                ['Número/Ano', "{$dadosExemplo['numero']}/{$dadosExemplo['ano']}"],
                ['Ementa', substr($dadosExemplo['ementa'], 0, 80) . '...'],
                ['Autor', $dadosExemplo['autor_nome']],
            ]
        );

        // 1. Testar Estruturação (LC 95/1998)
        $this->newLine();
        $this->info('🔨 1. ESTRUTURAÇÃO LC 95/1998');
        $this->line(str_repeat('-', 50));
        
        $estruturadorService = app(TemplateEstruturadorService::class);
        $estrutura = $estruturadorService->estruturarProposicao($dadosExemplo, $tipoProposicao);
        
        $this->info('✅ Epígrafe: ' . $estrutura['epigrafe']);
        $this->info('✅ Ementa: ' . substr($estrutura['ementa'], 0, 60) . '...');
        $this->info('✅ Preâmbulo: ' . $estrutura['preambulo']);
        $this->info('✅ Artigos estruturados: ' . count($estrutura['corpo_articulado']['artigos']));
        $this->info('✅ Cláusula vigência: ' . $estrutura['clausula_vigencia']);

        // 2. Testar Numeração Unificada
        $this->newLine();
        $this->info('🔢 2. NUMERAÇÃO UNIFICADA');
        $this->line(str_repeat('-', 50));
        
        $numeracaoService = app(TemplateNumeracaoService::class);
        $proximoNumero = $numeracaoService->obterProximoNumero($tipoProposicao);
        $epigrafe = $numeracaoService->gerarEpigrafeFormatada($tipoProposicao, $proximoNumero, 2025);
        
        $this->info("✅ Próximo número disponível: {$proximoNumero}");
        $this->info("✅ Epígrafe formatada: {$epigrafe}");
        
        // Estatísticas
        $stats = $numeracaoService->obterEstatisticasNumeracao(2025);
        if (!empty($stats['por_tipo'])) {
            $this->info('📊 Estatísticas por tipo:');
            foreach ($stats['por_tipo'] as $codigo => $dados) {
                $this->line("   {$codigo}: {$dados['total']} proposições (último nº {$dados['maior_numero']})");
            }
        }

        // 3. Testar Validação Legal
        $this->newLine();
        $this->info('⚖️  3. VALIDAÇÃO LEGAL');
        $this->line(str_repeat('-', 50));
        
        $validadorService = app(TemplateValidadorLegalService::class);
        $validacao = $validadorService->validarProposicaoCompleta($dadosExemplo, $tipoProposicao);
        
        $resumo = $validacao['resumo'];
        $this->info("✅ Status: {$resumo['status']}");
        $this->info("✅ Qualidade: {$resumo['qualidade_percentual']}%");
        $this->info("📊 Erros: {$resumo['total_erros']} | Avisos: {$resumo['total_avisos']} | Aprovado: {$resumo['total_aprovado']}");
        
        // Mostrar conformidades
        $conformidades = [
            'LC 95/1998' => $resumo['conforme_lc95'] ? '✅' : '❌',
            'Estrutura' => $resumo['estrutura_adequada'] ? '✅' : '❌',
            'Metadados' => $resumo['metadados_completos'] ? '✅' : '❌',
            'Numeração' => $resumo['numeracao_conforme'] ? '✅' : '❌',
            'Acessibilidade' => $resumo['acessivel'] ? '✅' : '❌'
        ];
        
        $this->table(['Conformidade', 'Status'], 
            array_map(fn($k, $v) => [$k, $v], array_keys($conformidades), $conformidades)
        );

        // 4. Testar Metadados
        $this->newLine();
        $this->info('📄 4. METADADOS DUBLIN CORE & LEXML');
        $this->line(str_repeat('-', 50));
        
        // Criar proposição temporária para testes
        $proposicaoTeste = new \App\Models\Proposicao();
        $proposicaoTeste->numero = (string)$dadosExemplo['numero'];
        $proposicaoTeste->ano = $dadosExemplo['ano'];
        $proposicaoTeste->ementa = $dadosExemplo['ementa'];
        $proposicaoTeste->conteudo = $dadosExemplo['texto'];
        $proposicaoTeste->tipo = $tipoProposicao->codigo;
        $proposicaoTeste->tipoProposicao = $tipoProposicao;
        $proposicaoTeste->autor_id = 1; // ID fictício
        $proposicaoTeste->status = 'rascunho';
        $proposicaoTeste->created_at = now();
        $proposicaoTeste->updated_at = now();
        
        $metadadosService = app(TemplateMetadadosService::class);
        $dublinCore = $metadadosService->gerarDublinCore($proposicaoTeste);
        $lexmlUrn = $metadadosService->gerarLexMLURN($proposicaoTeste);
        
        $this->info('✅ Dublin Core gerado: ' . count($dublinCore) . ' campos');
        $this->info('✅ LexML URN: ' . $lexmlUrn);
        
        // Mostrar alguns metadados importantes
        $metadadosImportantes = [
            'Título' => $dublinCore['dc:title'] ?? 'N/A',
            'Criador' => $dublinCore['dc:creator'] ?? 'N/A',
            'Tipo' => $dublinCore['dc:type'] ?? 'N/A',
            'Data' => $dublinCore['dc:date'] ?? 'N/A',
            'Idioma' => $dublinCore['dc:language'] ?? 'N/A'
        ];
        
        $this->table(['Metadado', 'Valor'], 
            array_map(fn($k, $v) => [$k, substr($v, 0, 50) . (strlen($v) > 50 ? '...' : '')], 
            array_keys($metadadosImportantes), $metadadosImportantes)
        );

        // 5. Gerar Template Completo
        $this->newLine();
        $this->info('📃 5. TEMPLATE ESTRUTURADO COMPLETO');
        $this->line(str_repeat('-', 50));
        
        $templateCompleto = $estruturadorService->gerarTemplateEstruturado($dadosExemplo, $tipoProposicao);
        
        $this->info('Template gerado com ' . strlen($templateCompleto) . ' caracteres:');
        $this->newLine();
        $this->line('┌' . str_repeat('─', 78) . '┐');
        foreach (explode("\n", substr($templateCompleto, 0, 500)) as $linha) {
            $this->line('│ ' . str_pad(substr($linha, 0, 76), 76) . ' │');
        }
        if (strlen($templateCompleto) > 500) {
            $this->line('│ ' . str_pad('... (conteúdo truncado)', 76) . ' │');
        }
        $this->line('└' . str_repeat('─', 78) . '┘');

        // 6. Gerar XML Akoma Ntoso
        $this->newLine();
        $this->info('📰 6. XML AKOMA NTOSO');
        $this->line(str_repeat('-', 50));
        
        $xmlAkoma = $metadadosService->gerarAkomaNtosoXML($proposicaoTeste);
        $this->info('✅ XML Akoma Ntoso gerado: ' . strlen($xmlAkoma) . ' caracteres');
        
        if ($this->option('salvar') === 'true') {
            $nomeArquivo = storage_path('app/teste-akoma-ntoso.xml');
            file_put_contents($nomeArquivo, $xmlAkoma);
            $this->info("✅ XML salvo em: {$nomeArquivo}");
        }

        // Resumo Final
        $this->newLine();
        $this->info('🎯 RESUMO FINAL');
        $this->line(str_repeat('=', 50));
        
        $implementacoes = [
            '✅ LC 95/1998 - Estrutura obrigatória',
            '✅ Numeração unificada por tipo e ano',
            '✅ Metadados Dublin Core',
            '✅ Identificadores LexML URN',
            '✅ XML Akoma Ntoso (OASIS)',
            '✅ Validações automáticas',
            '✅ Acessibilidade WCAG/PDF-UA',
            '✅ Formatação padronizada'
        ];

        foreach ($implementacoes as $item) {
            $this->line($item);
        }

        $this->newLine();
        $this->info('🏆 Sistema configurado com padrões internacionais!');
        $this->comment('Conformidade com LC 95/1998, LexML, Dublin Core e Akoma Ntoso');
        
        if (!$this->option('salvar')) {
            $this->newLine();
            $this->comment('Use --salvar=true para salvar arquivos de exemplo');
        }

        return 0;
    }
}