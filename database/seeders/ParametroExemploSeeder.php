<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro;
use App\Models\GrupoParametro;
use App\Models\TipoParametro;

class ParametroExemploSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buscar grupos e tipos existentes
        $grupoSistema = GrupoParametro::where('codigo', 'sistema')->first();
        $grupoLegislativo = GrupoParametro::where('codigo', 'legislativo')->first();
        $grupoNotificacoes = GrupoParametro::where('codigo', 'notificacoes')->first();
        $grupoSeguranca = GrupoParametro::where('codigo', 'seguranca')->first();
        
        $tipoString = TipoParametro::where('codigo', 'string')->first();
        $tipoInteger = TipoParametro::where('codigo', 'integer')->first();
        $tipoBoolean = TipoParametro::where('codigo', 'boolean')->first();
        $tipoEmail = TipoParametro::where('codigo', 'email')->first();
        $tipoUrl = TipoParametro::where('codigo', 'url')->first();
        $tipoTime = TipoParametro::where('codigo', 'time')->first();
        $tipoJson = TipoParametro::where('codigo', 'json')->first();
        $tipoColor = TipoParametro::where('codigo', 'color')->first();

        // Parâmetros de exemplo para demonstração
        $parametrosExemplo = [
            // Configurações de Sistema
            [
                'nome' => 'Mensagem de Boas-vindas',
                'codigo' => 'sistema.mensagem_boas_vindas',
                'descricao' => 'Mensagem exibida na tela inicial do sistema para usuários logados',
                'valor' => 'Bem-vindo ao Sistema LegisInc! Gerencie seus projetos legislativos com eficiência.',
                'valor_padrao' => 'Bem-vindo ao Sistema Legislativo!',
                'grupo_parametro_id' => $grupoSistema->id,
                'tipo_parametro_id' => $tipoString->id,
                'help_text' => 'Esta mensagem aparece no dashboard após o login',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 100
            ],
            [
                'nome' => 'Limite de Projetos por Parlamentar',
                'codigo' => 'sistema.limite_projetos_parlamentar',
                'descricao' => 'Número máximo de projetos que um parlamentar pode ter em tramitação simultaneamente',
                'valor' => '50',
                'valor_padrao' => '25',
                'grupo_parametro_id' => $grupoSistema->id,
                'tipo_parametro_id' => $tipoInteger->id,
                'help_text' => 'Limite para evitar sobrecarga do sistema. 0 = ilimitado',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 101
            ],
            [
                'nome' => 'Permitir Edição de Projetos Protocolados',
                'codigo' => 'sistema.permitir_edicao_protocolados',
                'descricao' => 'Define se projetos já protocolados podem ser editados pelos autores',
                'valor' => 'false',
                'valor_padrao' => 'false',
                'grupo_parametro_id' => $grupoSistema->id,
                'tipo_parametro_id' => $tipoBoolean->id,
                'help_text' => 'Ativado: permite edição. Desativado: projetos protocolados ficam bloqueados',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 102
            ],

            // Configurações Legislativas
            [
                'nome' => 'Horário de Início das Sessões',
                'codigo' => 'legislativo.horario_inicio_sessao',
                'descricao' => 'Horário padrão para início das sessões legislativas',
                'valor' => '14:00',
                'valor_padrao' => '14:00',
                'grupo_parametro_id' => $grupoLegislativo->id,
                'tipo_parametro_id' => $tipoTime->id,
                'help_text' => 'Formato: HH:MM (24 horas)',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 200
            ],
            [
                'nome' => 'Quórum Mínimo para Votação',
                'codigo' => 'legislativo.quorum_minimo_votacao',
                'descricao' => 'Número mínimo de parlamentares presentes para realizar votações',
                'valor' => '15',
                'valor_padrao' => '10',
                'grupo_parametro_id' => $grupoLegislativo->id,
                'tipo_parametro_id' => $tipoInteger->id,
                'help_text' => 'Baseado no regimento interno da casa legislativa',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 201
            ],
            [
                'nome' => 'Tipos de Projeto Permitidos',
                'codigo' => 'legislativo.tipos_projeto_permitidos',
                'descricao' => 'Lista de tipos de projetos que podem ser criados no sistema',
                'valor' => '["Lei Ordinária", "Lei Complementar", "Resolução", "Decreto Legislativo", "Emenda", "Requerimento"]',
                'valor_padrao' => '["Lei Ordinária", "Resolução", "Requerimento"]',
                'grupo_parametro_id' => $grupoLegislativo->id,
                'tipo_parametro_id' => $tipoJson->id,
                'help_text' => 'Array JSON com os tipos permitidos',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 202
            ],

            // Configurações de Notificações
            [
                'nome' => 'E-mail para Notificações do Sistema',
                'codigo' => 'notificacoes.email_sistema',
                'descricao' => 'Endereço de e-mail usado para envio de notificações automáticas',
                'valor' => 'sistema@camara.sp.gov.br',
                'valor_padrao' => 'noreply@sistema.gov.br',
                'grupo_parametro_id' => $grupoNotificacoes->id,
                'tipo_parametro_id' => $tipoEmail->id,
                'help_text' => 'E-mail que aparece como remetente nas notificações',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 300
            ],
            [
                'nome' => 'Notificar Alterações em Projetos',
                'codigo' => 'notificacoes.notificar_alteracoes_projetos',
                'descricao' => 'Enviar e-mail quando projetos são modificados',
                'valor' => 'true',
                'valor_padrao' => 'true',
                'grupo_parametro_id' => $grupoNotificacoes->id,
                'tipo_parametro_id' => $tipoBoolean->id,
                'help_text' => 'Notifica autores e relatores sobre alterações',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 301
            ],

            // Configurações de Segurança
            [
                'nome' => 'Tempo de Sessão (minutos)',
                'codigo' => 'seguranca.tempo_sessao',
                'descricao' => 'Tempo em minutos para expiração automática da sessão do usuário',
                'valor' => '480',
                'valor_padrao' => '120',
                'grupo_parametro_id' => $grupoSeguranca->id,
                'tipo_parametro_id' => $tipoInteger->id,
                'help_text' => 'Após este tempo inativo, usuário será deslogado automaticamente',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 400
            ],
            [
                'nome' => 'URL do Sistema de Autenticação',
                'codigo' => 'seguranca.url_autenticacao',
                'descricao' => 'URL para redirecionamento em caso de falha de autenticação',
                'valor' => 'https://auth.camara.sp.gov.br/login',
                'valor_padrao' => '/login',
                'grupo_parametro_id' => $grupoSeguranca->id,
                'tipo_parametro_id' => $tipoUrl->id,
                'help_text' => 'URL completa incluindo protocolo (https://)',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 401
            ],

            // Configurações de Interface
            [
                'nome' => 'Cor Primária do Sistema',
                'codigo' => 'interface.cor_primaria',
                'descricao' => 'Cor principal da interface do sistema (hexadecimal)',
                'valor' => '#009EF7',
                'valor_padrao' => '#007BFF',
                'grupo_parametro_id' => GrupoParametro::where('codigo', 'interface')->first()->id,
                'tipo_parametro_id' => $tipoColor->id,
                'help_text' => 'Cor em formato hexadecimal (ex: #FF0000)',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 500
            ]
        ];

        // Criar parâmetros de exemplo
        foreach ($parametrosExemplo as $parametro) {
            Parametro::updateOrCreate(
                ['codigo' => $parametro['codigo']],
                $parametro
            );
        }

        $this->command->info('Parâmetros de exemplo criados com sucesso!');
        $this->command->info('Total de parâmetros exemplo: ' . count($parametrosExemplo));
        
        // Mostrar alguns exemplos criados
        $this->command->line('');
        $this->command->line('Exemplos criados:');
        $this->command->line('• sistema.mensagem_boas_vindas');
        $this->command->line('• sistema.limite_projetos_parlamentar');
        $this->command->line('• legislativo.horario_inicio_sessao');
        $this->command->line('• notificacoes.email_sistema');
        $this->command->line('• seguranca.tempo_sessao');
        $this->command->line('• interface.cor_primaria');
        $this->command->line('');
        $this->command->line('Para usar: parametro("sistema.mensagem_boas_vindas")');
    }
}