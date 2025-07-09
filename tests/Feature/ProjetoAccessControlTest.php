<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Projeto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class ProjetoAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pode_ver_todos_os_projetos()
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $parlamentar = User::factory()->create();
        $parlamentar->assignRole('PARLAMENTAR');

        $projeto = Projeto::factory()->create([
            'autor_id' => $parlamentar->id,
            'status' => 'rascunho'
        ]);

        $this->actingAs($admin);
        $response = $this->get('/projetos');
        $response->assertStatus(200);

        $response = $this->get("/projetos/{$projeto->id}");
        $response->assertStatus(200);
    }

    public function test_parlamentar_pode_ver_apenas_seus_projetos_e_votados()
    {
        $parlamentar1 = User::factory()->create();
        $parlamentar1->assignRole('PARLAMENTAR');

        $parlamentar2 = User::factory()->create();
        $parlamentar2->assignRole('PARLAMENTAR');

        $projetoRascunho = Projeto::factory()->create([
            'autor_id' => $parlamentar2->id,
            'status' => 'rascunho'
        ]);

        $projetoAprovado = Projeto::factory()->create([
            'autor_id' => $parlamentar2->id,
            'status' => 'aprovado'
        ]);

        $this->actingAs($parlamentar1);

        $response = $this->get("/projetos/{$projetoRascunho->id}");
        $response->assertStatus(403);

        $response = $this->get("/projetos/{$projetoAprovado->id}");
        $response->assertStatus(200);
    }

    public function test_parlamentar_pode_editar_apenas_seus_projetos()
    {
        $parlamentar1 = User::factory()->create();
        $parlamentar1->assignRole('PARLAMENTAR');

        $parlamentar2 = User::factory()->create();
        $parlamentar2->assignRole('PARLAMENTAR');

        $projetoProprio = Projeto::factory()->create([
            'autor_id' => $parlamentar1->id,
            'status' => 'rascunho'
        ]);

        $projetoOutro = Projeto::factory()->create([
            'autor_id' => $parlamentar2->id,
            'status' => 'rascunho'
        ]);

        $this->actingAs($parlamentar1);

        $response = $this->get("/projetos/{$projetoProprio->id}/edit");
        $response->assertStatus(200);

        $response = $this->get("/projetos/{$projetoOutro->id}/edit");
        $response->assertStatus(403);
    }

    public function test_parlamentar_pode_excluir_apenas_seus_projetos_em_rascunho()
    {
        $parlamentar = User::factory()->create();
        $parlamentar->assignRole('PARLAMENTAR');

        $projetoRascunho = Projeto::factory()->create([
            'autor_id' => $parlamentar->id,
            'status' => 'rascunho'
        ]);

        $projetoProtocolado = Projeto::factory()->create([
            'autor_id' => $parlamentar->id,
            'status' => 'protocolado'
        ]);

        $this->actingAs($parlamentar);

        $response = $this->delete("/projetos/{$projetoRascunho->id}");
        $response->assertStatus(302);

        $response = $this->delete("/projetos/{$projetoProtocolado->id}");
        $response->assertStatus(302);
    }

    public function test_usuario_comum_pode_ver_apenas_projetos_votados()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('PUBLICO');

        $projetoRascunho = Projeto::factory()->create([
            'status' => 'rascunho'
        ]);

        $projetoAprovado = Projeto::factory()->create([
            'status' => 'aprovado'
        ]);

        $this->actingAs($usuario);

        $response = $this->get("/projetos/{$projetoRascunho->id}");
        $response->assertStatus(403);

        $response = $this->get("/projetos/{$projetoAprovado->id}");
        $response->assertStatus(200);
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'ADMIN']);
        Role::create(['name' => 'PARLAMENTAR']);
        Role::create(['name' => 'PUBLICO']);
    }
}