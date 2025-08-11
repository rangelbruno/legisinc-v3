<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class AIProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Criando configurações de IA...');

        // Clear existing AI data
        DB::table('ai_provider_configs')->truncate();
        DB::table('ai_providers')->truncate();

        // Ensure IA module exists in parametros_modulos
        $iaModule = DB::table('parametros_modulos')->where('nome', 'IA')->first();
        
        if (!$iaModule) {
            DB::table('parametros_modulos')->insert([
                'nome' => 'IA',
                'descricao' => 'Configurações de Inteligência Artificial',
                'icon' => 'ki-brain',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $this->command->info('✅ Módulo IA criado nos parâmetros');
        }

        // Create AI providers
        $providers = [
            [
                'name' => 'openai',
                'label' => 'OpenAI',
                'description' => 'Provedor OpenAI com modelos GPT-4 e GPT-3.5 para geração de texto avançada',
                'icon' => 'ki-brain',
                'base_url' => 'https://api.openai.com/v1',
                'default_model' => 'gpt-4',
                'supported_models' => json_encode([
                    'gpt-4',
                    'gpt-4-turbo',
                    'gpt-3.5-turbo',
                    'gpt-3.5-turbo-16k'
                ]),
                'config_template' => json_encode([
                    'api_key' => [
                        'type' => 'password',
                        'label' => 'Chave da API',
                        'placeholder' => 'sk-...',
                        'required' => true,
                        'help' => 'Obtenha sua chave API em https://platform.openai.com/api-keys'
                    ],
                    'organization' => [
                        'type' => 'text',
                        'label' => 'ID da Organização (Opcional)',
                        'placeholder' => 'org-...',
                        'required' => false,
                        'help' => 'ID da organização, se aplicável'
                    ]
                ]),
                'is_enabled' => true,
                'is_active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'anthropic',
                'label' => 'Anthropic (Claude)',
                'description' => 'Provedor Anthropic com modelos Claude para conversas avançadas e análise de texto',
                'icon' => 'ki-technology-2',
                'base_url' => 'https://api.anthropic.com/v1',
                'default_model' => 'claude-3-haiku-20240307',
                'supported_models' => json_encode([
                    'claude-3-opus-20240229',
                    'claude-3-sonnet-20240229',
                    'claude-3-haiku-20240307'
                ]),
                'config_template' => json_encode([
                    'api_key' => [
                        'type' => 'password',
                        'label' => 'Chave da API',
                        'placeholder' => 'sk-ant-api03-...',
                        'required' => true,
                        'help' => 'Obtenha sua chave API em https://console.anthropic.com/'
                    ]
                ]),
                'is_enabled' => true,
                'is_active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'google',
                'label' => 'Google AI',
                'description' => 'Provedor Google com modelos Gemini para análise e geração de conteúdo',
                'icon' => 'ki-abstract-26',
                'base_url' => 'https://generativelanguage.googleapis.com/v1',
                'default_model' => 'gemini-1.5-pro',
                'supported_models' => json_encode([
                    'gemini-1.5-pro',
                    'gemini-1.5-flash',
                    'gemini-pro'
                ]),
                'config_template' => json_encode([
                    'api_key' => [
                        'type' => 'password',
                        'label' => 'Chave da API',
                        'placeholder' => 'AIzaSy...',
                        'required' => true,
                        'help' => 'Obtenha sua chave API em https://makersuite.google.com/app/apikey'
                    ]
                ]),
                'is_enabled' => true,
                'is_active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'ollama',
                'label' => 'Ollama (Local)',
                'description' => 'Provedor local Ollama para modelos de IA hospedados localmente sem custo',
                'icon' => 'ki-rocket',
                'base_url' => 'http://localhost:11434/v1',
                'default_model' => 'llama3.1',
                'supported_models' => json_encode([
                    'llama3.1',
                    'llama3.1:8b',
                    'llama3.1:70b',
                    'codellama',
                    'mistral'
                ]),
                'config_template' => json_encode([
                    'base_url' => [
                        'type' => 'text',
                        'label' => 'URL Base',
                        'placeholder' => 'http://localhost:11434/v1',
                        'default' => 'http://localhost:11434/v1',
                        'required' => true,
                        'help' => 'URL do servidor Ollama local'
                    ]
                ]),
                'is_enabled' => true,
                'is_active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        // Insert providers
        foreach ($providers as $provider) {
            DB::table('ai_providers')->insert($provider);
        }

        $this->command->info('✅ Provedores de IA criados com sucesso!');
        $this->command->line('');
        $this->command->line('📋 Provedores disponíveis:');
        $this->command->line('   • OpenAI (GPT-4, GPT-3.5)');
        $this->command->line('   • Anthropic (Claude 3)');
        $this->command->line('   • Google AI (Gemini)');
        $this->command->line('   • Ollama (Local)');
        $this->command->line('');
        $this->command->warn('⚠️  IMPORTANTE: Configure as chaves de API em /admin/parametros/ia/config');
        $this->command->line('');
    }
}