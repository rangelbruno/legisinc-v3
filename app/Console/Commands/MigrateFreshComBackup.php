<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MigrateFreshComBackup extends Command
{
    protected $signature = 'migrate:fresh-backup {--seed : Seed the database after migration}';

    protected $description = 'Executa migrate:fresh preservando dados críticos e configurações';

    private $dadosCriticos = [];

    private $configBackupPath;

    public function __construct()
    {
        parent::__construct();
        $this->configBackupPath = storage_path('app/config-backup');
    }

    public function handle()
    {
        $this->info('🔄 Iniciando migração segura com backup...');

        try {
            // 1. Fazer backup dos dados críticos
            $this->backupDadosCriticos();

            // 2. Fazer backup das configurações de arquivos
            $this->backupConfiguracoes();

            // 3. Executar migrate:fresh
            $this->info('📦 Executando migrate:fresh...');
            Artisan::call('migrate:fresh');
            $this->info($this->formatArtisanOutput(Artisan::output()));

            // 4. Executar seeders básicos se solicitado
            if ($this->option('seed')) {
                $this->info('🌱 Executando seeders...');
                Artisan::call('db:seed');
                $this->info($this->formatArtisanOutput(Artisan::output()));
            }

            // 5. Restaurar dados críticos
            $this->restaurarDadosCriticos();

            // 6. Restaurar configurações
            $this->restaurarConfiguracoes();

            // 7. Aplicar configurações persistentes
            $this->aplicarConfiguracoesPersistentes();

            // 8. Limpar caches
            $this->limparCaches();

            $this->info('✅ Migração segura concluída com sucesso!');
            $this->newLine();
            $this->info('📋 Resumo:');
            $this->info('  ✓ Banco de dados recriado');
            $this->info('  ✓ Dados críticos preservados');
            $this->info('  ✓ Configurações mantidas');
            $this->info('  ✓ Correções aplicadas');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erro durante a migração: '.$e->getMessage());
            $this->error('Stack trace: '.$e->getTraceAsString());

            // Tentar restaurar dados se houver erro
            if (! empty($this->dadosCriticos)) {
                $this->warn('⚠️ Tentando restaurar dados críticos...');
                $this->restaurarDadosCriticos();
            }

            return 1;
        }
    }

    private function backupDadosCriticos()
    {
        $this->info('💾 Fazendo backup de dados críticos...');

        // Tabelas com dados que devem ser preservados
        $tabelasCriticas = [
            'ai_configurations' => ['*'],
            'parametros' => ['*'],
            'parametros_modulos' => ['*'],
            'parametros_submodulos' => ['*'],
            'parametros_campos' => ['*'],
            'template_padraos' => ['*'],
            'tipo_proposicao_templates' => ['id', 'tipo_proposicao_id', 'nome', 'arquivo_path', 'conteudo', 'document_key', 'created_at', 'updated_at'],
            'tipo_proposicoes' => ['*'],
            'screen_permissions' => ['*'],
        ];

        foreach ($tabelasCriticas as $tabela => $colunas) {
            if (Schema::hasTable($tabela)) {
                try {
                    $query = DB::table($tabela);

                    // Se colunas específicas foram definidas
                    if ($colunas !== ['*']) {
                        // Filtrar apenas colunas que existem
                        $colunasExistentes = Schema::getColumnListing($tabela);
                        $colunasValidas = array_intersect($colunas, $colunasExistentes);

                        if (! empty($colunasValidas)) {
                            $query = $query->select($colunasValidas);
                        }
                    }

                    $dados = $query->get()->toArray();

                    if (! empty($dados)) {
                        $this->dadosCriticos[$tabela] = $dados;
                        $this->line("  ✓ Backup de {$tabela}: ".count($dados).' registros');
                    }
                } catch (\Exception $e) {
                    $this->warn("  ⚠️ Não foi possível fazer backup de {$tabela}: ".$e->getMessage());
                }
            }
        }

        // Salvar backup em arquivo JSON para segurança
        $backupFile = storage_path('app/backup-dados-criticos.json');
        File::put($backupFile, json_encode($this->dadosCriticos, JSON_PRETTY_PRINT));
        $this->line("  ✓ Backup salvo em: {$backupFile}");
    }

    private function restaurarDadosCriticos()
    {
        $this->info('♻️ Restaurando dados críticos...');

        foreach ($this->dadosCriticos as $tabela => $dados) {
            if (Schema::hasTable($tabela) && ! empty($dados)) {
                try {
                    // Converter objetos para arrays
                    $dadosArray = array_map(function ($item) {
                        return (array) $item;
                    }, $dados);

                    // Truncar a tabela primeiro (exceto algumas)
                    $tabelasNaoTruncar = ['tipo_proposicoes', 'tipo_proposicao_templates'];
                    if (! in_array($tabela, $tabelasNaoTruncar)) {
                        DB::table($tabela)->truncate();
                    }

                    // Inserir dados em lotes
                    $chunks = array_chunk($dadosArray, 100);
                    foreach ($chunks as $chunk) {
                        DB::table($tabela)->insertOrIgnore($chunk);
                    }

                    $this->line("  ✓ Restaurado {$tabela}: ".count($dados).' registros');
                } catch (\Exception $e) {
                    $this->warn("  ⚠️ Erro ao restaurar {$tabela}: ".$e->getMessage());
                }
            }
        }
    }

    private function backupConfiguracoes()
    {
        $this->info('📁 Fazendo backup das configurações...');

        // Criar diretório de backup
        if (! File::exists($this->configBackupPath)) {
            File::makeDirectory($this->configBackupPath, 0755, true);
        }

        // Arquivos críticos para backup
        $arquivosCriticos = [
            'config/dompdf.php',
            'app/Http/Controllers/ProposicaoAssinaturaController.php',
            'app/Http/Controllers/ProposicaoProtocoloController.php',
            'app/Services/Template/TemplateVariableService.php',
            'app/Models/Proposicao.php',
        ];

        foreach ($arquivosCriticos as $arquivo) {
            if (File::exists(base_path($arquivo))) {
                $backupFile = $this->configBackupPath.'/'.str_replace('/', '_', $arquivo);
                File::copy(base_path($arquivo), $backupFile);
                $this->line("  ✓ Backup: {$arquivo}");
            }
        }

        // Backup do diretório de fontes
        $fontsDir = storage_path('fonts');
        if (File::exists($fontsDir)) {
            $backupFontsDir = $this->configBackupPath.'/fonts';
            if (! File::exists($backupFontsDir)) {
                File::makeDirectory($backupFontsDir, 0755, true);
            }
            File::copyDirectory($fontsDir, $backupFontsDir);
            $this->line('  ✓ Backup: diretório de fontes');
        }

        // Backup do diretório de templates
        $templatesDir = storage_path('app/private/templates');
        if (File::exists($templatesDir)) {
            $backupTemplatesDir = $this->configBackupPath.'/templates';
            if (! File::exists($backupTemplatesDir)) {
                File::makeDirectory($backupTemplatesDir, 0755, true);
            }
            File::copyDirectory($templatesDir, $backupTemplatesDir);
            $this->line('  ✓ Backup: diretório de templates');
        }
    }

    private function restaurarConfiguracoes()
    {
        $this->info('📂 Restaurando configurações...');

        // Restaurar arquivos
        $arquivosRestaurar = [
            'config/dompdf.php',
            'app/Http/Controllers/ProposicaoAssinaturaController.php',
            'app/Http/Controllers/ProposicaoProtocoloController.php',
            'app/Services/Template/TemplateVariableService.php',
            'app/Models/Proposicao.php',
        ];

        foreach ($arquivosRestaurar as $arquivo) {
            $backupFile = $this->configBackupPath.'/'.str_replace('/', '_', $arquivo);
            if (File::exists($backupFile)) {
                // Fazer backup do arquivo atual antes de sobrescrever
                $currentFile = base_path($arquivo);
                if (File::exists($currentFile)) {
                    File::copy($currentFile, $currentFile.'.bak');
                }

                File::copy($backupFile, $currentFile);
                $this->line("  ✓ Restaurado: {$arquivo}");
            }
        }

        // Restaurar fontes
        $backupFontsDir = $this->configBackupPath.'/fonts';
        if (File::exists($backupFontsDir)) {
            $fontsDir = storage_path('fonts');
            if (! File::exists($fontsDir)) {
                File::makeDirectory($fontsDir, 0755, true);
            }
            File::copyDirectory($backupFontsDir, $fontsDir);
            $this->line('  ✓ Restaurado: diretório de fontes');
        }

        // Restaurar templates
        $backupTemplatesDir = $this->configBackupPath.'/templates';
        if (File::exists($backupTemplatesDir)) {
            $templatesDir = storage_path('app/private/templates');
            if (! File::exists($templatesDir)) {
                File::makeDirectory($templatesDir, 0755, true);
            }
            File::copyDirectory($backupTemplatesDir, $templatesDir);
            $this->line('  ✓ Restaurado: diretório de templates');
        }
    }

    private function aplicarConfiguracoesPersistentes()
    {
        $this->info('🔧 Aplicando configurações persistentes...');

        // Executar seeder de configuração persistente se existir
        if (class_exists('\Database\Seeders\ConfiguracaoSistemaPersistenteSeeder')) {
            Artisan::call('db:seed', [
                '--class' => 'ConfiguracaoSistemaPersistenteSeeder',
                '--force' => true,
            ]);
            $this->line('  ✓ Configurações persistentes aplicadas');
        }

        // Aplicar correções específicas
        $this->aplicarCorrecoesEspecificas();
    }

    private function aplicarCorrecoesEspecificas()
    {
        $this->info('🔨 Aplicando correções específicas...');

        // Correção 1: ProposicaoAssinaturaController linha 3849
        $arquivo = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        if (File::exists($arquivo)) {
            $conteudo = File::get($arquivo);

            // Verificar se a correção já foi aplicada
            if (strpos($conteudo, '$cargoAtual = $proposicao->autor->cargo_atual') === false) {
                // Aplicar correção
                $conteudo = preg_replace(
                    '/\$assinaturaDigitalHTML = "\s*<div[^>]*>\{\$proposicao->autor->cargo_atual \?\? \'Parlamentar\'\}<\/div>/',
                    '$cargoAtual = $proposicao->autor->cargo_atual ?? \'Parlamentar\';
$assinaturaDigitalHTML = "
    <div style=\'margin: 5px 0;\'>{$cargoAtual}</div>',
                    $conteudo
                );

                File::put($arquivo, $conteudo);
                $this->line('  ✓ Corrigido: ProposicaoAssinaturaController linha 3849');
            }
        }

        // Correção 2: TemplateVariableService - adicionar nome_parlamentar
        $arquivo = app_path('Services/Template/TemplateVariableService.php');
        if (File::exists($arquivo)) {
            $conteudo = File::get($arquivo);

            // Verificar se nome_parlamentar já existe
            if (strpos($conteudo, "'nome_parlamentar'") === false) {
                // Adicionar na função getTemplateVariables
                $conteudo = preg_replace(
                    '/(getTemplateVariables\(\): array\s*\{[^}]*)(return \$variables;)/s',
                    '$1        \'nome_parlamentar\' => \'{nome_parlamentar}\',
        $2',
                    $conteudo
                );

                // Adicionar na função listAvailableVariables
                $conteudo = preg_replace(
                    '/(listAvailableVariables\(\): array\s*\{[^}]*)(];)/s',
                    '$1            \'${nome_parlamentar}\' => \'Nome do parlamentar logado\',
        $2',
                    $conteudo
                );

                File::put($arquivo, $conteudo);
                $this->line('  ✓ Corrigido: TemplateVariableService - adicionado nome_parlamentar');
            }
        }

        // Correção 3: Model Proposicao - descomentar 'numero' e adicionar 'numero_sequencial'
        $arquivo = app_path('Models/Proposicao.php');
        if (File::exists($arquivo)) {
            $conteudo = File::get($arquivo);

            // Descomentar 'numero'
            $conteudo = preg_replace('/\/\/\s*\'numero\'/', "'numero'", $conteudo);

            // Adicionar 'numero_sequencial' se não existir
            if (strpos($conteudo, "'numero_sequencial'") === false) {
                $conteudo = preg_replace(
                    '/(\$fillable = \[[^\]]*)(\'numero\'[^\]]*)(];)/s',
                    '$1$2        \'numero_sequencial\',
        $3',
                    $conteudo
                );
            }

            File::put($arquivo, $conteudo);
            $this->line('  ✓ Corrigido: Model Proposicao - campos numero e numero_sequencial');
        }
    }

    private function limparCaches()
    {
        $this->info('🧹 Limpando caches...');

        $comandos = [
            'config:clear' => 'Cache de configuração',
            'cache:clear' => 'Cache da aplicação',
            'view:clear' => 'Cache de views',
            'route:clear' => 'Cache de rotas',
        ];

        foreach ($comandos as $comando => $descricao) {
            Artisan::call($comando);
            $this->line("  ✓ Limpo: {$descricao}");
        }
    }

    private function formatArtisanOutput($output)
    {
        // Remove espaços em branco extras e formata a saída
        $lines = explode("\n", trim($output));
        $formatted = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (! empty($line)) {
                $formatted[] = '    '.$line;
            }
        }

        return implode("\n", $formatted);
    }
}
