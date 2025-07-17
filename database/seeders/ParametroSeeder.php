<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro;
use App\Models\GrupoParametro;
use App\Models\TipoParametro;

class ParametroSeeder extends Seeder
{
    public function run(): void
    {
        // Obter grupos e tipos
        $grupos = GrupoParametro::pluck('id', 'codigo');
        $tipos = TipoParametro::pluck('id', 'codigo');

        $parametros = [
            // Sistema
            [
                'nome' => 'Nome do Sistema',
                'codigo' => 'sistema.nome',
                'descricao' => 'Nome do sistema exibido na interface',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['string'],
                'valor' => 'LegisInc',
                'valor_padrao' => 'LegisInc',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Este nome aparecerá no título e cabeçalho do sistema'
            ],
            [
                'nome' => 'Versão do Sistema',
                'codigo' => 'sistema.versao',
                'descricao' => 'Versão atual do sistema',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['string'],
                'valor' => '1.0.0',
                'valor_padrao' => '1.0.0',
                'obrigatorio' => true,
                'editavel' => false,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Versão atual do sistema (apenas leitura)'
            ],
            [
                'nome' => 'Email do Administrador',
                'codigo' => 'sistema.admin_email',
                'descricao' => 'Email do administrador do sistema',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['email'],
                'valor' => 'admin@legisinc.com',
                'valor_padrao' => 'admin@legisinc.com',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Email usado para notificações administrativas'
            ],
            [
                'nome' => 'Modo Manutenção',
                'codigo' => 'sistema.manutencao',
                'descricao' => 'Ativar modo de manutenção',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '0',
                'valor_padrao' => '0',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 4,
                'help_text' => 'Quando ativado, apenas administradores podem acessar o sistema'
            ],
            [
                'nome' => 'Mensagem de Manutenção',
                'codigo' => 'sistema.mensagem_manutencao',
                'descricao' => 'Mensagem exibida durante a manutenção',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['text'],
                'valor' => 'Sistema em manutenção. Voltamos em breve!',
                'valor_padrao' => 'Sistema em manutenção. Voltamos em breve!',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 5,
                'help_text' => 'Mensagem exibida aos usuários durante a manutenção'
            ],
            [
                'nome' => 'Fuso Horário',
                'codigo' => 'sistema.timezone',
                'descricao' => 'Fuso horário do sistema',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['enum'],
                'valor' => 'America/Sao_Paulo',
                'valor_padrao' => 'America/Sao_Paulo',
                'configuracao' => [
                    'options' => [
                        'America/Sao_Paulo' => 'São Paulo (GMT-3)',
                        'America/Manaus' => 'Manaus (GMT-4)',
                        'America/Rio_Branco' => 'Rio Branco (GMT-5)',
                        'UTC' => 'UTC (GMT+0)'
                    ]
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 6,
                'help_text' => 'Fuso horário usado para exibir datas e horários'
            ],
            [
                'nome' => 'Limite de Sessão',
                'codigo' => 'sistema.sessao_limite',
                'descricao' => 'Tempo limite da sessão em minutos',
                'grupo_parametro_id' => $grupos['sistema'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '120',
                'valor_padrao' => '120',
                'configuracao' => [
                    'min' => 30,
                    'max' => 480
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 7,
                'help_text' => 'Tempo em minutos antes da sessão expirar'
            ],

            // Legislativo
            [
                'nome' => 'Nome da Câmara',
                'codigo' => 'legislativo.nome_camara',
                'descricao' => 'Nome da câmara legislativa',
                'grupo_parametro_id' => $grupos['legislativo'],
                'tipo_parametro_id' => $tipos['string'],
                'valor' => 'Câmara Municipal',
                'valor_padrao' => 'Câmara Municipal',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Nome oficial da câmara legislativa'
            ],
            [
                'nome' => 'Cidade',
                'codigo' => 'legislativo.cidade',
                'descricao' => 'Nome da cidade',
                'grupo_parametro_id' => $grupos['legislativo'],
                'tipo_parametro_id' => $tipos['string'],
                'valor' => 'São Paulo',
                'valor_padrao' => 'São Paulo',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Nome da cidade onde a câmara está localizada'
            ],
            [
                'nome' => 'Estado',
                'codigo' => 'legislativo.estado',
                'descricao' => 'Estado da federação',
                'grupo_parametro_id' => $grupos['legislativo'],
                'tipo_parametro_id' => $tipos['string'],
                'valor' => 'SP',
                'valor_padrao' => 'SP',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Sigla do estado (UF)'
            ],
            [
                'nome' => 'Tipos de Projeto',
                'codigo' => 'legislativo.tipos_projeto',
                'descricao' => 'Tipos de projeto legislativo permitidos',
                'grupo_parametro_id' => $grupos['legislativo'],
                'tipo_parametro_id' => $tipos['array'],
                'valor' => 'Projeto de Lei,Projeto de Resolução,Projeto de Decreto,Requerimento,Indicação,Moção',
                'valor_padrao' => 'Projeto de Lei,Projeto de Resolução,Projeto de Decreto,Requerimento,Indicação,Moção',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 4,
                'help_text' => 'Lista dos tipos de projeto separados por vírgula'
            ],
            [
                'nome' => 'Prazo Tramitação',
                'codigo' => 'legislativo.prazo_tramitacao',
                'descricao' => 'Prazo padrão para tramitação em dias',
                'grupo_parametro_id' => $grupos['legislativo'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '30',
                'valor_padrao' => '30',
                'configuracao' => [
                    'min' => 1,
                    'max' => 365
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 5,
                'help_text' => 'Prazo padrão em dias para tramitação de projetos'
            ],
            [
                'nome' => 'Numeração Automática',
                'codigo' => 'legislativo.numeracao_automatica',
                'descricao' => 'Ativar numeração automática de projetos',
                'grupo_parametro_id' => $grupos['legislativo'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 6,
                'help_text' => 'Gerar automaticamente números sequenciais para projetos'
            ],

            // Notificações
            [
                'nome' => 'Email Habilitado',
                'codigo' => 'notificacoes.email_habilitado',
                'descricao' => 'Habilitar notificações por email',
                'grupo_parametro_id' => $grupos['notificacoes'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Ativar o envio de notificações por email'
            ],
            [
                'nome' => 'Email Remetente',
                'codigo' => 'notificacoes.email_remetente',
                'descricao' => 'Email remetente das notificações',
                'grupo_parametro_id' => $grupos['notificacoes'],
                'tipo_parametro_id' => $tipos['email'],
                'valor' => 'noreply@legisinc.com',
                'valor_padrao' => 'noreply@legisinc.com',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Email usado como remetente das notificações'
            ],
            [
                'nome' => 'Nome Remetente',
                'codigo' => 'notificacoes.nome_remetente',
                'descricao' => 'Nome do remetente das notificações',
                'grupo_parametro_id' => $grupos['notificacoes'],
                'tipo_parametro_id' => $tipos['string'],
                'valor' => 'Sistema LegisInc',
                'valor_padrao' => 'Sistema LegisInc',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Nome exibido como remetente das notificações'
            ],
            [
                'nome' => 'Notificar Tramitação',
                'codigo' => 'notificacoes.tramitacao',
                'descricao' => 'Notificar mudanças na tramitação',
                'grupo_parametro_id' => $grupos['notificacoes'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 4,
                'help_text' => 'Enviar notificações sobre mudanças na tramitação'
            ],
            [
                'nome' => 'Notificar Prazos',
                'codigo' => 'notificacoes.prazos',
                'descricao' => 'Notificar sobre prazos vencendo',
                'grupo_parametro_id' => $grupos['notificacoes'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 5,
                'help_text' => 'Enviar notificações sobre prazos próximos do vencimento'
            ],

            // Segurança
            [
                'nome' => 'Senha Mínima',
                'codigo' => 'seguranca.senha_minima',
                'descricao' => 'Tamanho mínimo da senha',
                'grupo_parametro_id' => $grupos['seguranca'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '8',
                'valor_padrao' => '8',
                'configuracao' => [
                    'min' => 6,
                    'max' => 32
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Número mínimo de caracteres para senhas'
            ],
            [
                'nome' => 'Exigir Maiúscula',
                'codigo' => 'seguranca.senha_maiuscula',
                'descricao' => 'Exigir letra maiúscula na senha',
                'grupo_parametro_id' => $grupos['seguranca'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Senha deve conter pelo menos uma letra maiúscula'
            ],
            [
                'nome' => 'Exigir Número',
                'codigo' => 'seguranca.senha_numero',
                'descricao' => 'Exigir número na senha',
                'grupo_parametro_id' => $grupos['seguranca'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Senha deve conter pelo menos um número'
            ],
            [
                'nome' => 'Tentativas Login',
                'codigo' => 'seguranca.tentativas_login',
                'descricao' => 'Máximo de tentativas de login',
                'grupo_parametro_id' => $grupos['seguranca'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '5',
                'valor_padrao' => '5',
                'configuracao' => [
                    'min' => 3,
                    'max' => 10
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 4,
                'help_text' => 'Número máximo de tentativas antes do bloqueio'
            ],
            [
                'nome' => 'Bloqueio Duração',
                'codigo' => 'seguranca.bloqueio_duracao',
                'descricao' => 'Duração do bloqueio em minutos',
                'grupo_parametro_id' => $grupos['seguranca'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '30',
                'valor_padrao' => '30',
                'configuracao' => [
                    'min' => 5,
                    'max' => 1440
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 5,
                'help_text' => 'Duração do bloqueio após exceder tentativas'
            ],

            // Interface
            [
                'nome' => 'Tema Padrão',
                'codigo' => 'interface.tema',
                'descricao' => 'Tema padrão da interface',
                'grupo_parametro_id' => $grupos['interface'],
                'tipo_parametro_id' => $tipos['enum'],
                'valor' => 'light',
                'valor_padrao' => 'light',
                'configuracao' => [
                    'options' => [
                        'light' => 'Claro',
                        'dark' => 'Escuro'
                    ]
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Tema padrão da interface do sistema'
            ],
            [
                'nome' => 'Cor Primária',
                'codigo' => 'interface.cor_primaria',
                'descricao' => 'Cor primária da interface',
                'grupo_parametro_id' => $grupos['interface'],
                'tipo_parametro_id' => $tipos['color'],
                'valor' => '#009EF7',
                'valor_padrao' => '#009EF7',
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Cor primária usada na interface'
            ],
            [
                'nome' => 'Itens por Página',
                'codigo' => 'interface.itens_pagina',
                'descricao' => 'Número de itens por página',
                'grupo_parametro_id' => $grupos['interface'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '20',
                'valor_padrao' => '20',
                'configuracao' => [
                    'min' => 10,
                    'max' => 100
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Número padrão de itens por página nas listas'
            ],
            [
                'nome' => 'Mostrar Ajuda',
                'codigo' => 'interface.mostrar_ajuda',
                'descricao' => 'Mostrar textos de ajuda',
                'grupo_parametro_id' => $grupos['interface'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 4,
                'help_text' => 'Exibir textos de ajuda nos formulários'
            ],

            // Performance
            [
                'nome' => 'Cache Habilitado',
                'codigo' => 'performance.cache_habilitado',
                'descricao' => 'Habilitar cache do sistema',
                'grupo_parametro_id' => $grupos['performance'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Ativar sistema de cache para melhor performance'
            ],
            [
                'nome' => 'Cache TTL',
                'codigo' => 'performance.cache_ttl',
                'descricao' => 'Tempo de vida do cache em minutos',
                'grupo_parametro_id' => $grupos['performance'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '60',
                'valor_padrao' => '60',
                'configuracao' => [
                    'min' => 5,
                    'max' => 1440
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Tempo em minutos que os dados ficam em cache'
            ],
            [
                'nome' => 'Debug Habilitado',
                'codigo' => 'performance.debug_habilitado',
                'descricao' => 'Habilitar modo debug',
                'grupo_parametro_id' => $grupos['performance'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '0',
                'valor_padrao' => '0',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Ativar modo debug (apenas desenvolvimento)'
            ],

            // Backup
            [
                'nome' => 'Backup Automático',
                'codigo' => 'backup.automatico',
                'descricao' => 'Habilitar backup automático',
                'grupo_parametro_id' => $grupos['backup'],
                'tipo_parametro_id' => $tipos['boolean'],
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Executar backup automático periodicamente'
            ],
            [
                'nome' => 'Frequência Backup',
                'codigo' => 'backup.frequencia',
                'descricao' => 'Frequência do backup em horas',
                'grupo_parametro_id' => $grupos['backup'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '24',
                'valor_padrao' => '24',
                'configuracao' => [
                    'min' => 1,
                    'max' => 168
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Intervalo em horas entre backups automáticos'
            ],
            [
                'nome' => 'Manter Backups',
                'codigo' => 'backup.manter_backups',
                'descricao' => 'Quantidade de backups a manter',
                'grupo_parametro_id' => $grupos['backup'],
                'tipo_parametro_id' => $tipos['integer'],
                'valor' => '30',
                'valor_padrao' => '30',
                'configuracao' => [
                    'min' => 1,
                    'max' => 365
                ],
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Número de backups para manter no histórico'
            ]
        ];

        foreach ($parametros as $parametro) {
            Parametro::updateOrCreate(
                ['codigo' => $parametro['codigo']],
                $parametro
            );
        }

        $this->command->info('Parâmetros criados com sucesso!');
    }
}