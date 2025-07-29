<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposicao;
use App\Models\User;

class CreateTestNotifications extends Command
{
    protected $signature = 'notifications:create-test';
    protected $description = 'Criar proposições de teste para demonstrar o sistema de notificações';

    public function handle()
    {
        $this->info('Criando proposições de teste para demonstrar notificações...');

        // Buscar usuários do sistema
        $parlamentar = User::whereHas('roles', function($query) {
            $query->where('name', 'PARLAMENTAR');
        })->first();

        if (!$parlamentar) {
            $this->error('Nenhum usuário com perfil Parlamentar encontrado!');
            return;
        }

        // Criar proposições de teste usando apenas status válidos
        Proposicao::create([
            'tipo' => 'Projeto de Lei',
            'titulo' => 'PL sobre Meio Ambiente Urbano',
            'ementa' => 'Dispõe sobre a criação de áreas verdes em centros urbanos.',
            'conteudo' => 'Artigo 1º - Fica instituído...',
            'autor_id' => $parlamentar->id,
            'status' => 'enviado_legislativo', // Usando status válido
            'ano' => date('Y')
        ]);

        // Criar mais proposições para demonstrar notificações
        Proposicao::create([
            'tipo' => 'Projeto de Lei',
            'titulo' => 'PL sobre Transporte Público',
            'ementa' => 'Estabelece diretrizes para melhoria do transporte público municipal.',
            'conteudo' => 'Artigo 1º - Fica estabelecido...',
            'autor_id' => $parlamentar->id,
            'status' => 'retornado_legislativo', // Usando status válido
            'ano' => date('Y')
        ]);

        // Criar proposição em salvamento
        Proposicao::create([
            'tipo' => 'Requerimento',  
            'titulo' => 'Requerimento sobre Educação',
            'ementa' => 'Solicita informações sobre investimentos em educação.',
            'conteudo' => 'Requer...',
            'autor_id' => $parlamentar->id,
            'status' => 'salvando', // Usando status válido
            'ano' => date('Y'),
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(8)
        ]);

        $this->info('✓ Proposições de teste criadas com sucesso!');
        $this->info('✓ Sistema de notificações pronto para demonstração.');
        $this->info('');
        $this->info('Proposições criadas:');
        $this->info('- 1 enviada ao legislativo');
        $this->info('- 1 retornada do legislativo');
        $this->info('- 1 em processo de salvamento');
    }
}