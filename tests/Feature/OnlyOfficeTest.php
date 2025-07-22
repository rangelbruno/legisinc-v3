<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('onlyoffice server is accessible', function () {
    Http::fake([
        config('onlyoffice.server_url') . '/healthcheck' => Http::response(['status' => 'ok'], 200)
    ]);

    $response = Http::get(config('onlyoffice.server_url') . '/healthcheck');
    
    expect($response->successful())->toBeTrue();
});

test('onlyoffice config exists', function () {
    expect(config('onlyoffice.server_url'))->not->toBeNull();
    expect(config('onlyoffice.jwt_secret'))->not->toBeNull();
    expect(config('onlyoffice.callback_url'))->not->toBeNull();
    expect(config('onlyoffice.document_types'))->toBeArray();
    expect(config('onlyoffice.default_permissions'))->toBeArray();
});

test('directories are created correctly', function () {
    $directories = [
        'onlyoffice',
        'onlyoffice/modelos',
        'onlyoffice/instancias',
        'onlyoffice/versoes',
        'onlyoffice/temp',
        'documentos/modelos',
        'documentos/instancias',
        'documentos/versoes',
        'documentos/pdfs'
    ];

    foreach ($directories as $dir) {
        expect(Storage::exists($dir))->toBeTrue();
    }
});