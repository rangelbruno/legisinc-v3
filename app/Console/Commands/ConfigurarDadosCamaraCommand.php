<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConfigurarDadosCamaraCommand extends Command
{
    protected $signature = 'camara:configurar-dados
                          {--nome= : Nome oficial da Câmara}
                          {--municipio= : Nome do município}
                          {--uf= : UF do município}
                          {--endereco= : Logradouro}
                          {--telefone= : Telefone principal}
                          {--email= : E-mail oficial}
                          {--website= : Website}
                          {--interactive : Modo interativo para configuração}';

    protected $description = 'Configurar dados gerais da Câmara Municipal';

    public function handle(): int
    {
        $this->info('🏛️  Configuração dos Dados da Câmara Municipal');
        $this->info('================================================');

        // Verificar se o submódulo existe
        $submodulo = DB::table('parametros_submodulos')
            ->where('nome', 'Dados Gerais da Câmara')
            ->first();

        if (!$submodulo) {
            $this->error('❌ Submódulo "Dados Gerais da Câmara" não encontrado.');
            $this->comment('Execute: php artisan db:seed --class=DadosGeraisCamaraSeeder');
            return 1;
        }

        if ($this->option('interactive')) {
            return $this->configuracaoInterativa($submodulo->id);
        }

        return $this->configuracaoRapida($submodulo->id);
    }

    private function configuracaoInterativa(int $submoduloId): int
    {
        $this->info('🎯 Modo Interativo - Configure os dados da sua Câmara:');
        $this->newLine();

        $configuracoes = [
            'nome_camara_oficial' => $this->ask('Nome oficial da Câmara', 'CÂMARA MUNICIPAL DE SÃO PAULO'),
            'nome_camara_abreviado' => $this->ask('Nome abreviado', 'CMSP'),
            'municipio_nome' => $this->ask('Nome do município', 'São Paulo'),
            'municipio_uf' => strtoupper($this->ask('UF', 'SP')),
            'endereco_logradouro' => $this->ask('Logradouro (Rua, Av, Praça)', 'Viaduto Jacareí, 100'),
            'endereco_bairro' => $this->ask('Bairro', 'Centro'),
            'endereco_cep' => $this->ask('CEP', '01008-902'),
            'telefone_principal' => $this->ask('Telefone principal', '(11) 3396-4000'),
            'telefone_protocolo' => $this->ask('Telefone do protocolo (opcional)'),
            'email_oficial' => $this->ask('E-mail oficial', 'atendimento@camara.sp.gov.br'),
            'website' => $this->ask('Website', 'www.saopaulo.sp.leg.br'),
            'cnpj' => $this->ask('CNPJ (opcional)'),
            'presidente_nome' => $this->ask('Nome do Presidente atual (opcional)'),
            'horario_funcionamento' => $this->ask('Horário de funcionamento', 'Segunda a Sexta: 8h às 17h'),
            'horario_protocolo' => $this->ask('Horário do protocolo', 'Segunda a Sexta: 9h às 16h'),
        ];

        return $this->salvarConfiguracoes($submoduloId, $configuracoes);
    }

    private function configuracaoRapida(int $submoduloId): int
    {
        $configuracoes = [];

        // Coletar opções fornecidas
        if ($nome = $this->option('nome')) {
            $configuracoes['nome_camara_oficial'] = $nome;
            // Gerar abreviação automática
            $palavras = explode(' ', $nome);
            $configuracoes['nome_camara_abreviado'] = implode('', array_map(fn($p) => strtoupper(substr($p, 0, 1)), array_slice($palavras, -2)));
        }

        if ($municipio = $this->option('municipio')) {
            $configuracoes['municipio_nome'] = $municipio;
        }

        if ($uf = $this->option('uf')) {
            $configuracoes['municipio_uf'] = strtoupper($uf);
        }

        if ($endereco = $this->option('endereco')) {
            $configuracoes['endereco_logradouro'] = $endereco;
        }

        if ($telefone = $this->option('telefone')) {
            $configuracoes['telefone_principal'] = $telefone;
        }

        if ($email = $this->option('email')) {
            $configuracoes['email_oficial'] = $email;
        }

        if ($website = $this->option('website')) {
            $configuracoes['website'] = $website;
        }

        if (empty($configuracoes)) {
            $this->warn('⚠️  Nenhum parâmetro fornecido.');
            $this->comment('Use --interactive para configuração completa ou forneça opções específicas.');
            $this->comment('Exemplo: php artisan camara:configurar-dados --nome="CÂMARA MUNICIPAL DE SANTOS" --municipio="Santos" --uf="SP"');
            return 1;
        }

        return $this->salvarConfiguracoes($submoduloId, $configuracoes);
    }

    private function salvarConfiguracoes(int $submoduloId, array $configuracoes): int
    {
        $salvos = 0;
        $erros = 0;

        $this->info('💾 Salvando configurações...');

        foreach ($configuracoes as $nomeCampo => $valor) {
            if (empty($valor)) continue;

            try {
                // Buscar o campo
                $campo = DB::table('parametros_campos')
                    ->where('submodulo_id', $submoduloId)
                    ->where('nome', $nomeCampo)
                    ->first();

                if (!$campo) {
                    $this->warn("   ⚠️  Campo '{$nomeCampo}' não encontrado");
                    continue;
                }

                // Inserir ou atualizar valor
                DB::table('parametros_valores')->updateOrInsert(
                    ['campo_id' => $campo->id],
                    [
                        'valor' => $valor,
                        'tipo_valor' => 'string',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                $this->info("   ✅ {$campo->label}: {$valor}");
                $salvos++;

            } catch (\Exception $e) {
                $this->error("   ❌ Erro ao salvar {$nomeCampo}: " . $e->getMessage());
                $erros++;
            }
        }

        $this->newLine();
        $this->info("📊 RESUMO:");
        $this->info("   ✅ Salvos: {$salvos}");
        if ($erros > 0) {
            $this->error("   ❌ Erros: {$erros}");
        }

        if ($salvos > 0) {
            $this->newLine();
            $this->comment('🎯 Configurações salvas! Acesse /admin/parametros/6 para ver/editar.');
            $this->comment('🔄 Para aplicar as mudanças nos templates, execute:');
            $this->comment('   php artisan templates:aplicar-padroes-legais --force');
        }

        return $salvos > 0 ? 0 : 1;
    }
}