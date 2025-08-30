<?php

use App\Http\Requests\StoreProposicaoRequest;

test('store proposicao request validates required fields', function () {
    $request = new StoreProposicaoRequest;
    $rules = $request->rules();

    expect($rules)->toHaveKeys(['tipo', 'ementa']);
    expect($rules['tipo'])->toContain('required');
    expect($rules['ementa'])->toContain('required');
});

test('store proposicao request validates field lengths', function () {
    $request = new StoreProposicaoRequest;
    $rules = $request->rules();

    expect($rules['tipo'])->toContain('max:100');
    expect($rules['ementa'])->toContain('max:1000');
    expect($rules['conteudo'])->toContain('max:100000');
});

test('store proposicao request validates anexos correctly', function () {
    $request = new StoreProposicaoRequest;
    $rules = $request->rules();

    expect($rules['anexos'])->toContain('max:5');
    expect($rules['anexos.*'])->toContain('mimes:pdf,doc,docx,jpg,jpeg,png');
    expect($rules['anexos.*'])->toContain('max:10240');
});

test('store proposicao request provides custom messages', function () {
    $request = new StoreProposicaoRequest;
    $messages = $request->messages();

    expect($messages)->toHaveKeys([
        'tipo.required',
        'ementa.required',
        'anexos.max',
        'anexos.*.mimes',
    ]);

    expect($messages['tipo.required'])->toBe('O tipo da proposição é obrigatório.');
    expect($messages['ementa.required'])->toBe('A ementa é obrigatória.');
});
