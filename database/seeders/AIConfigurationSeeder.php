<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AIConfiguration;

class AIConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configuração Google Gemini - Ativa com API Key real
        AIConfiguration::updateOrCreate(
            ['name' => 'Google Gemini Pro'],
            [
                'provider' => 'google',
                'api_key' => 'AIzaSyBY7tkQhWWQHERr0XG6sbTvoJoTzPPftFk',
                'model' => 'gemini-pro',
                'max_tokens' => 4000,
                'temperature' => 0.7,
                'priority' => 1,
                'is_active' => true, // Ativa com API key válida
                'daily_token_limit' => 100000,
                'cost_per_1k_tokens' => 0.0005,
                'custom_prompt' => null, // Usar padrão
            ]
        );

        // Configuração OpenAI Principal (inativa, sem API key)
        AIConfiguration::updateOrCreate(
            ['name' => 'OpenAI GPT-4'],
            [
                'provider' => 'openai',
                'api_key' => 'sk-exemplo-substitua-pela-sua-chave', // Usuário deve substituir
                'model' => 'gpt-4o',
                'max_tokens' => 4000,
                'temperature' => 0.7,
                'priority' => 2,
                'is_active' => false, // Inativo até configurar API key real
                'daily_token_limit' => 50000,
                'cost_per_1k_tokens' => 0.01,
                'custom_prompt' => null,
            ]
        );

        // Configuração Claude Backup (inativa, sem API key)
        AIConfiguration::updateOrCreate(
            ['name' => 'Claude 3.5 Sonnet'],
            [
                'provider' => 'anthropic',
                'api_key' => 'sk-ant-exemplo-substitua-pela-sua-chave', // Usuário deve substituir
                'model' => 'claude-3.5-sonnet',
                'max_tokens' => 4000,
                'temperature' => 0.7,
                'priority' => 3,
                'is_active' => false, // Inativo até configurar API key real
                'daily_token_limit' => 30000,
                'cost_per_1k_tokens' => 0.015,
                'custom_prompt' => null,
            ]
        );

        // Configuração Local (Ollama) - Desativada por padrão
        AIConfiguration::updateOrCreate(
            ['name' => 'Local Ollama'],
            [
                'provider' => 'local',
                'api_key' => null, // Não precisa de API key
                'model' => 'llama3.1',
                'base_url' => 'http://localhost:11434',
                'max_tokens' => 2000,
                'temperature' => 0.7,
                'priority' => 4,
                'is_active' => false, // Desativada até Ollama estar instalado
                'daily_token_limit' => null, // Sem limite para local
                'cost_per_1k_tokens' => 0.0, // Gratuito
                'custom_prompt' => 'Você é um assistente especializado em redação legislativa municipal brasileira. Responda de forma clara, objetiva e seguindo as normas ABNT e técnicas legislativas.',
            ]
        );

        $this->command->info('Configurações de IA de exemplo criadas com sucesso!');
        $this->command->warn('IMPORTANTE: Substitua as API keys pelos valores reais e ative as configurações necessárias.');
    }
}