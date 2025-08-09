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
        // Limpar configurações existentes
        AIConfiguration::truncate();

        // Configuração OpenAI Principal
        AIConfiguration::create([
            'name' => 'OpenAI Principal',
            'provider' => 'openai',
            'api_key' => 'sk-exemplo-substitua-pela-sua-chave', // Usuário deve substituir
            'model' => 'gpt-4o',
            'max_tokens' => 4000,
            'temperature' => 0.7,
            'priority' => 1,
            'is_active' => false, // Inativo até configurar API key real
            'daily_token_limit' => 50000,
            'cost_per_1k_tokens' => 0.01,
            'custom_prompt' => null, // Usar padrão
        ]);

        // Configuração Claude Backup
        AIConfiguration::create([
            'name' => 'Claude Backup',
            'provider' => 'anthropic',
            'api_key' => 'sk-ant-exemplo-substitua-pela-sua-chave', // Usuário deve substituir
            'model' => 'claude-3.5-sonnet',
            'max_tokens' => 4000,
            'temperature' => 0.7,
            'priority' => 2,
            'is_active' => false, // Inativo até configurar API key real
            'daily_token_limit' => 30000,
            'cost_per_1k_tokens' => 0.015,
            'custom_prompt' => null,
        ]);

        // Configuração Google Gemini
        AIConfiguration::create([
            'name' => 'Google Gemini',
            'provider' => 'google',
            'api_key' => 'AIzaSyExemplo-substitua-pela-sua-chave', // Usuário deve substituir
            'model' => 'gemini-1.5-pro',
            'max_tokens' => 3000,
            'temperature' => 0.8,
            'priority' => 3,
            'is_active' => false, // Inativo até configurar API key real
            'daily_token_limit' => 40000,
            'cost_per_1k_tokens' => 0.0075,
            'custom_prompt' => null,
        ]);

        // Configuração Local (Ollama) - Pode ficar ativa como exemplo
        AIConfiguration::create([
            'name' => 'Local Ollama',
            'provider' => 'local',
            'api_key' => null, // Não precisa de API key
            'model' => 'llama3.1',
            'base_url' => 'http://localhost:11434',
            'max_tokens' => 2000,
            'temperature' => 0.7,
            'priority' => 4,
            'is_active' => true, // Pode ficar ativo como fallback local
            'daily_token_limit' => null, // Sem limite para local
            'cost_per_1k_tokens' => 0.0, // Gratuito
            'custom_prompt' => 'Você é um assistente especializado em redação legislativa municipal brasileira. Responda de forma clara, objetiva e seguindo as normas ABNT e técnicas legislativas.',
        ]);

        $this->command->info('Configurações de IA de exemplo criadas com sucesso!');
        $this->command->warn('IMPORTANTE: Substitua as API keys pelos valores reais e ative as configurações necessárias.');
    }
}