<?php

use App\Http\Resources\ProposicaoResource;
use App\Models\Proposicao;
use App\Models\User;
use Illuminate\Http\Request;

test('proposicao resource has correct structure', function () {
    $user = User::factory()->create();
    $proposicao = Proposicao::factory()->create([
        'autor_id' => $user->id,
        'status' => 'RASCUNHO',
    ]);

    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    $resource = new ProposicaoResource($proposicao);
    $array = $resource->toArray($request);

    expect($array)->toHaveKeys([
        'id', 'tipo', 'ementa', 'status', 'ano', 'autor',
    ]);

    expect($array['id'])->toBe($proposicao->id);
    expect($array['tipo'])->toBe($proposicao->tipo);
    expect($array['status'])->toBe($proposicao->status);
});

test('sensitive content is hidden for unauthorized users', function () {
    $autor = User::factory()->create();
    $otherUser = User::factory()->create();

    $proposicao = Proposicao::factory()->create([
        'autor_id' => $autor->id,
        'conteudo' => 'Conteúdo confidencial',
        'status' => 'RASCUNHO',
    ]);

    $request = Request::create('/test');
    $request->setUserResolver(fn () => $otherUser);

    $resource = new ProposicaoResource($proposicao);
    $array = $resource->toArray($request);

    expect($array['conteudo'])->toBeNull();
});

test('autor can view content', function () {
    $autor = User::factory()->create();

    $proposicao = Proposicao::factory()->create([
        'autor_id' => $autor->id,
        'conteudo' => 'Conteúdo privado',
        'status' => 'RASCUNHO',
    ]);

    $request = Request::create('/test');
    $request->setUserResolver(fn () => $autor);

    $resource = new ProposicaoResource($proposicao);
    $array = $resource->toArray($request);

    expect($array['conteudo'])->toBe('Conteúdo privado');
});

test('boolean flags are calculated correctly', function () {
    $user = User::factory()->create();
    $proposicao = Proposicao::factory()->create([
        'autor_id' => $user->id,
        'status' => 'RASCUNHO',
        'numero_protocolo' => null,
        'pdf_assinado_path' => null,
    ]);

    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    $resource = new ProposicaoResource($proposicao);
    $array = $resource->toArray($request);

    expect($array['foi_assinada'])->toBeFalse();
    expect($array['foi_protocolada'])->toBeFalse();
});
