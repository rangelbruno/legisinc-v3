<?php

use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

test('página de documentação do fluxo carrega corretamente para admin', function () {
    // Usar admin do seeder ou criar um simples
    $admin = User::where('email', 'bruno@sistema.gov.br')->first() 
        ?? User::factory()->create();
    
    $response = $this->actingAs($admin)
        ->get('/admin/docs/fluxo-proposicoes');
    
    $response->assertStatus(200);
    $response->assertViewIs('docs.fluxo-proposicoes');
    $response->assertViewHas('htmlContent');
    $response->assertViewHas('fileInfo');
});

test('página de documentação requer autenticação', function () {
    $response = $this->get('/admin/docs/fluxo-proposicoes');
    
    $response->assertRedirect('/login');
});

test('arquivo de documentação existe e é legível', function () {
    $filePath = base_path('docs/FLUXO-PROPOSICOES-MERMAID.md');
    
    expect(File::exists($filePath))->toBeTrue();
    expect(File::isReadable($filePath))->toBeTrue();
    expect(File::size($filePath))->toBeGreaterThan(1000); // Arquivo tem conteúdo
    
    $content = File::get($filePath);
    expect($content)->toContain('# Diagrama de Fluxo de Proposições');
    expect($content)->toContain('```mermaid');
});

test('comando de verificação executa corretamente', function () {
    $this->artisan('docs:verificar-fluxo')
        ->assertExitCode(0)
        ->expectsOutput('🔍 Verificando documentação do fluxo de proposições...')
        ->expectsOutput('✅ Verificação concluída com sucesso!');
});

test('documentação contém versão 2.0 e melhores práticas', function () {
    $admin = User::where('email', 'bruno@sistema.gov.br')->first() 
        ?? User::factory()->create();
    
    $response = $this->actingAs($admin)
        ->get('/admin/docs/fluxo-proposicoes');
    
    $response->assertStatus(200);
    $htmlContent = $response->viewData('htmlContent');
    
    // Verificar que conteúdo foi processado e contém elementos esperados
    expect($htmlContent)->toContain('v2.0');
    expect($htmlContent)->toContain('Produção com Melhores Práticas');
    expect($htmlContent)->toContain('Melhorias Implementadas');
});
