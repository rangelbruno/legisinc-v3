<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoParametro;

class GrupoParametroSeeder extends Seeder
{
    public function run(): void
    {
        $grupos = [
            [
                'nome' => 'Sistema',
                'codigo' => 'sistema',
                'descricao' => 'Configurações gerais do sistema',
                'icone' => 'ki-setting-2',
                'cor' => '#009EF7',
                'ordem' => 1,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Legislativo',
                'codigo' => 'legislativo',
                'descricao' => 'Configurações específicas do módulo legislativo',
                'icone' => 'ki-courthouse',
                'cor' => '#7239EA',
                'ordem' => 2,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Notificações',
                'codigo' => 'notificacoes',
                'descricao' => 'Configurações de notificações e alertas',
                'icone' => 'ki-notification-bing',
                'cor' => '#FFC700',
                'ordem' => 3,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Segurança',
                'codigo' => 'seguranca',
                'descricao' => 'Configurações de segurança e acesso',
                'icone' => 'ki-security-check',
                'cor' => '#F1416C',
                'ordem' => 4,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Interface',
                'codigo' => 'interface',
                'descricao' => 'Configurações da interface do usuário',
                'icone' => 'ki-design-1',
                'cor' => '#50CD89',
                'ordem' => 5,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Integração',
                'codigo' => 'integracao',
                'descricao' => 'Configurações de integração com sistemas externos',
                'icone' => 'ki-abstract-44',
                'cor' => '#FF6800',
                'ordem' => 6,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Performance',
                'codigo' => 'performance',
                'descricao' => 'Configurações de performance e cache',
                'icone' => 'ki-flash',
                'cor' => '#3F4254',
                'ordem' => 7,
                'ativo' => true,
                'grupo_pai_id' => null
            ],
            [
                'nome' => 'Backup',
                'codigo' => 'backup',
                'descricao' => 'Configurações de backup e recuperação',
                'icone' => 'ki-cloud',
                'cor' => '#5E6278',
                'ordem' => 8,
                'ativo' => true,
                'grupo_pai_id' => null
            ]
        ];

        foreach ($grupos as $grupo) {
            GrupoParametro::updateOrCreate(
                ['codigo' => $grupo['codigo']],
                $grupo
            );
        }

        $this->command->info('Grupos de parâmetros criados com sucesso!');
    }
}