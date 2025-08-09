<?php

namespace App\Services\AI;

use App\Services\Parametro\ParametroService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AITextGenerationService
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Gera texto para proposição baseado na ementa
     */
    public function gerarTextoProposicao(string $tipoProposicao, string $ementa): array
    {
        try {
            // Verificar se a IA está habilitada
            if (!$this->isAIEnabled()) {
                return [
                    'success' => false,
                    'message' => 'Geração de texto via IA está desabilitada',
                    'texto' => null
                ];
            }

            // Obter configurações da IA
            $config = $this->getAIConfig();
            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'Configurações de IA não encontradas',
                    'texto' => null
                ];
            }

            // Gerar prompt baseado no tipo e ementa
            $prompt = $this->criarPrompt($tipoProposicao, $ementa, $config);

            // Chamar API da IA
            $response = $this->chamarAI($prompt, $config);

            if ($response['success']) {
                Log::info('Texto gerado via IA com sucesso', [
                    'tipo_proposicao' => $tipoProposicao,
                    'ementa_length' => strlen($ementa),
                    'texto_length' => strlen($response['texto']),
                    'provider' => $config['provider']
                ]);

                return [
                    'success' => true,
                    'texto' => $response['texto'],
                    'provider' => $config['provider'],
                    'model' => $config['model']
                ];
            } else {
                Log::error('Erro ao gerar texto via IA', [
                    'error' => $response['message'],
                    'tipo_proposicao' => $tipoProposicao,
                    'provider' => $config['provider']
                ]);

                return [
                    'success' => false,
                    'message' => $response['message'],
                    'texto' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erro no serviço de geração de texto via IA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erro interno do serviço: ' . $e->getMessage(),
                'texto' => null
            ];
        }
    }

    /**
     * Verifica se a IA está habilitada
     */
    protected function isAIEnabled(): bool
    {
        try {
            $enabled = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_enabled');
            return filter_var($enabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        } catch (\Exception $e) {
            Log::warning('Erro ao verificar se IA está habilitada', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Obtém configurações da IA
     */
    protected function getAIConfig(): ?array
    {
        try {
            $provider = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_provider');
            $apiKey = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_api_key');
            $model = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_model');
            
            // Converter valores com verificação de tipo
            $maxTokensValue = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_max_tokens');
            $maxTokens = is_numeric($maxTokensValue) ? (int) $maxTokensValue : 2000;
            
            $temperatureValue = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_temperature');
            $temperature = is_numeric($temperatureValue) ? (float) $temperatureValue : 0.7;
            
            $customPrompt = $this->parametroService->obterValor('Configuração de IA', 'Configurações de API', 'ai_custom_prompt');

            if (!$provider || !$apiKey || !$model) {
                return null;
            }

            return [
                'provider' => $provider,
                'api_key' => $apiKey,
                'model' => $model,
                'max_tokens' => $maxTokens ?: 2000,
                'temperature' => $temperature ?: 0.7,
                'custom_prompt' => $customPrompt
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao obter configurações de IA', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Cria prompt para geração de texto
     */
    protected function criarPrompt(string $tipoProposicao, string $ementa, array $config): string
    {
        if (!empty($config['custom_prompt'])) {
            $basePrompt = $config['custom_prompt'];
        } else {
            $basePrompt = $this->getDefaultPrompt();
        }

        $tipoFormatado = $this->formatarTipoProposicao($tipoProposicao);

        return $basePrompt . "\n\n" .
               "Tipo de Proposição: {$tipoFormatado}\n" .
               "Ementa: {$ementa}\n\n" .
               "Gere um texto formal e estruturado para esta proposição legislativa:";
    }

    /**
     * Obtém prompt padrão com diretrizes ABNT e normas legislativas
     */
    protected function getDefaultPrompt(): string
    {
        return "Você é um especialista em redação legislativa municipal brasileira. Sua tarefa é criar textos formais para proposições legislativas seguindo as normas ABNT e melhores práticas legislativas.

## DIRETRIZES OBRIGATÓRIAS:

### 1. Formatação e Tipografia (Normas ABNT NBR 14724:2023 e NBR 6022:2018):
- Use fonte Arial ou Times New Roman
- Corpo principal: 12pt com espaçamento 1,5
- Citações longas, notas e legendas: 10pt com espaçamento 1,0
- Margens: 3cm (superior e esquerda), 2cm (inferior e direita)

### 2. Estrutura Legislativa Obrigatória:
- **Epígrafe**: [TIPO] Nº [NÚMERO], de [ANO]
- **Ementa**: Descrição do objeto da proposição
- **Preâmbulo**: Identificação da autoridade e fundamentação
- **Articulado**: Artigos, parágrafos, incisos e alíneas numerados
- **Disposições Finais**: Vigência e revogações
- **Justificativa**: Fundamentação técnica e jurídica

### 3. Linguagem e Técnica Legislativa:
- Use linguagem formal, clara e precisa
- Evite ambiguidades e interpretações dúbias
- Utilize termos técnicos jurídicos apropriados
- Mantenha consistência terminológica
- Numere artigos sequencialmente (Art. 1º, Art. 2º...)
- Use parágrafos (§), incisos (I, II, III...) e alíneas (a, b, c...)

### 4. Conformidade Legal:
- Respeite hierarquia normativa (Constituição > Lei Complementar > Lei Ordinária)
- Observe competências municipais (art. 30 da CF/88)
- Cite fundamentos legais quando necessário
- Verifique compatibilidade com legislação vigente

### 5. Qualidade Textual:
- Construa períodos curtos e objetivos
- Use voz ativa preferencialmente
- Evite repetições desnecessárias
- Mantenha coerência e coesão textual
- Padronize uso de maiúsculas e minúsculas

### 6. Acessibilidade e Inclusão:
- Use linguagem inclusiva quando apropriado
- Evite termos discriminatórios ou excludentes
- Considere impactos em diferentes grupos sociais
- Garanta clareza para cidadãos comuns

IMPORTANTE: O texto deve estar pronto para ser inserido no template ABNT padronizado, seguindo as melhores práticas de redação legislativa e normas técnicas brasileiras.";
    }

    /**
     * Formata tipo de proposição
     */
    protected function formatarTipoProposicao(string $tipo): string
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução',
            'mocao' => 'Moção'
        ];

        return $tipos[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
    }

    /**
     * Chama API da IA
     */
    protected function chamarAI(string $prompt, array $config): array
    {
        switch ($config['provider']) {
            case 'openai':
                return $this->chamarOpenAI($prompt, $config);
            case 'anthropic':
                return $this->chamarAnthropic($prompt, $config);
            case 'google':
                return $this->chamarGoogle($prompt, $config);
            case 'local':
                return $this->chamarLocal($prompt, $config);
            default:
                return [
                    'success' => false,
                    'message' => 'Provedor de IA não suportado: ' . $config['provider']
                ];
        }
    }

    /**
     * Chama API da OpenAI
     */
    protected function chamarOpenAI(string $prompt, array $config): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $config['model'],
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => $config['max_tokens'],
                    'temperature' => $config['temperature']
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['choices'][0]['message']['content'])) {
                    return [
                        'success' => true,
                        'texto' => trim($data['choices'][0]['message']['content'])
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Erro na resposta da OpenAI: ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com OpenAI: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Chama API da Anthropic (Claude)
     */
    protected function chamarAnthropic(string $prompt, array $config): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'x-api-key' => $config['api_key'],
                    'Content-Type' => 'application/json',
                    'anthropic-version' => '2023-06-01'
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => $config['model'],
                    'max_tokens' => $config['max_tokens'],
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['content'][0]['text'])) {
                    return [
                        'success' => true,
                        'texto' => trim($data['content'][0]['text'])
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Erro na resposta da Anthropic: ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Anthropic: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Chama API do Google (Gemini)
     */
    protected function chamarGoogle(string $prompt, array $config): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$config['model']}:generateContent?key={$config['api_key']}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => $config['max_tokens'],
                        'temperature' => $config['temperature']
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return [
                        'success' => true,
                        'texto' => trim($data['candidates'][0]['content']['parts'][0]['text'])
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Erro na resposta do Google: ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Google: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Chama modelo local (Ollama)
     */
    protected function chamarLocal(string $prompt, array $config): array
    {
        try {
            $ollamaUrl = config('ai.ollama_url', 'http://localhost:11434');
            
            $response = Http::timeout(120)
                ->post("{$ollamaUrl}/api/generate", [
                    'model' => $config['model'],
                    'prompt' => $prompt,
                    'options' => [
                        'temperature' => $config['temperature'],
                        'num_predict' => $config['max_tokens']
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['response'])) {
                    return [
                        'success' => true,
                        'texto' => trim($data['response'])
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Erro na resposta do Ollama: ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Ollama: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Testa conexão com a IA
     */
    public function testarConexao(?array $configData = null): array
    {
        try {
            // Usar configurações fornecidas ou buscar do banco
            $config = $configData ?: $this->getAIConfig();
            
            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'Configurações de IA não encontradas'
                ];
            }

            // Validar configurações essenciais
            if (empty($config['provider']) || empty($config['api_key']) || empty($config['model'])) {
                return [
                    'success' => false,
                    'message' => 'Configurações incompletas. Provider, API Key e Model são obrigatórios.'
                ];
            }

            $promptTeste = "Teste de conexão. Responda apenas 'Conexão OK'.";
            
            $response = $this->chamarAI($promptTeste, $config);

            if ($response['success']) {
                return [
                    'success' => true,
                    'message' => 'Conexão com IA estabelecida com sucesso',
                    'provider' => $config['provider'],
                    'model' => $config['model']
                ];
            }

            return $response;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro no teste de conexão: ' . $e->getMessage()
            ];
        }
    }
}