<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->handleRequest(Illuminate\Http\Request::capture());

use App\Models\AIConfiguration;

try {
    $config = AIConfiguration::where('name', 'Google Gemini Pro')->first();
    
    if ($config) {
        // Atualizar com a API key real - será criptografada automaticamente
        $config->api_key = 'AIzaSyBY7tkQhWWQHERr0XG6sbTvoJoTzPPftFk';
        $config->save();
        echo "✓ API key atualizada e criptografada com sucesso!\n";
    } else {
        // Criar nova configuração se não existir
        $config = AIConfiguration::create([
            'name' => 'Google Gemini Pro',
            'provider' => 'google',
            'api_key' => 'AIzaSyBY7tkQhWWQHERr0XG6sbTvoJoTzPPftFk',
            'model' => 'gemini-1.5-flash',
            'max_tokens' => 4000,
            'temperature' => 0.7,
            'priority' => 1,
            'is_active' => true,
            'daily_token_limit' => 100000,
            'daily_tokens_used' => 0,
            'last_reset_date' => now()->toDateString(),
            'cost_per_1k_tokens' => 0.0005
        ]);
        echo "✓ Nova configuração criada com API key criptografada!\n";
    }
    
    // Verificar se está funcionando
    echo "\nTestando descriptografia...\n";
    $config->refresh();
    echo "API Key (primeiros 10 caracteres): " . substr($config->api_key, 0, 10) . "...\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}