<?php

namespace App\Services;

use App\Models\TipoProposicaoTemplate;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Support\Facades\Storage;

class TemplateVariablesService
{
    private $templateParametrosService;

    public function __construct(TemplateParametrosService $templateParametrosService)
    {
        $this->templateParametrosService = $templateParametrosService;
    }

    /**
     * Lista das variáveis disponíveis no sistema
     */
    public const SYSTEM_VARIABLES = [
        // Dados da Proposição
        'numero_proposicao' => [
            'label' => 'Número da Proposição',
            'description' => 'Número único da proposição',
            'category' => 'proposicao',
            'type' => 'auto',
            'required' => false
        ],
        'tipo_proposicao' => [
            'label' => 'Tipo da Proposição',
            'description' => 'Tipo da proposição (Lei, Decreto, etc.)',
            'category' => 'proposicao',
            'type' => 'auto',
            'required' => false
        ],
        'status_proposicao' => [
            'label' => 'Status da Proposição',
            'description' => 'Status atual da proposição',
            'category' => 'proposicao',
            'type' => 'auto',
            'required' => false
        ],
        'ementa' => [
            'label' => 'Ementa',
            'description' => 'Ementa da proposição',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => true
        ],
        'texto' => [
            'label' => 'Texto Principal',
            'description' => 'Conteúdo principal da proposição',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => true
        ],
        'justificativa' => [
            'label' => 'Justificativa',
            'description' => 'Justificativa da proposição',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => false
        ],
        'considerandos' => [
            'label' => 'Considerandos',
            'description' => 'Considerandos da proposição',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => false
        ],
        'observacoes' => [
            'label' => 'Observações',
            'description' => 'Observações adicionais',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => false
        ],
        'finalidade' => [
            'label' => 'Finalidade',
            'description' => 'Finalidade da proposta de emenda',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => true
        ],
        'dispositivo_alterado' => [
            'label' => 'Dispositivo Alterado',
            'description' => 'Dispositivo da Constituição que será alterado',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => true
        ],
        
        // Artigos
        'artigo_1' => [
            'label' => 'Artigo 1º',
            'description' => 'Primeiro artigo da proposição',
            'category' => 'artigos',
            'type' => 'textarea',
            'required' => false
        ],
        'artigo_2' => [
            'label' => 'Artigo 2º',
            'description' => 'Segundo artigo da proposição',
            'category' => 'artigos',
            'type' => 'textarea',
            'required' => false
        ],
        'artigo_3' => [
            'label' => 'Artigo 3º',
            'description' => 'Terceiro artigo da proposição',
            'category' => 'artigos',
            'type' => 'textarea',
            'required' => false
        ],
        
        // Autor e Parlamentar
        'autor_nome' => [
            'label' => 'Nome do Autor',
            'description' => 'Nome completo do autor',
            'category' => 'autor',
            'type' => 'auto',
            'required' => false
        ],
        'nome_parlamentar' => [
            'label' => 'Nome do Parlamentar',
            'description' => 'Nome do parlamentar responsável',
            'category' => 'autor',
            'type' => 'auto',
            'required' => false
        ],
        'cargo_parlamentar' => [
            'label' => 'Cargo do Parlamentar',
            'description' => 'Cargo exercido pelo parlamentar',
            'category' => 'autor',
            'type' => 'auto',
            'required' => false
        ],
        'email_parlamentar' => [
            'label' => 'E-mail do Parlamentar',
            'description' => 'E-mail do parlamentar',
            'category' => 'autor',
            'type' => 'auto',
            'required' => false
        ],
        'partido_parlamentar' => [
            'label' => 'Partido do Parlamentar',
            'description' => 'Partido político do parlamentar',
            'category' => 'autor',
            'type' => 'auto',
            'required' => false
        ],
        
        // Datas e Horários
        'data_atual' => [
            'label' => 'Data Atual',
            'description' => 'Data atual (dd/mm/aaaa)',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        'data_extenso' => [
            'label' => 'Data por Extenso',
            'description' => 'Data atual escrita por extenso',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        'hora_atual' => [
            'label' => 'Hora Atual',
            'description' => 'Hora atual',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        'data_criacao' => [
            'label' => 'Data de Criação',
            'description' => 'Data de criação da proposição',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        'dia_atual' => [
            'label' => 'Dia Atual',
            'description' => 'Dia do mês atual',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        'mes_atual' => [
            'label' => 'Mês Atual',
            'description' => 'Mês atual',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        'ano_atual' => [
            'label' => 'Ano Atual',
            'description' => 'Ano atual',
            'category' => 'datas',
            'type' => 'auto',
            'required' => false
        ],
        
        // Instituição
        'municipio' => [
            'label' => 'Município',
            'description' => 'Nome do município',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'nome_camara' => [
            'label' => 'Nome da Câmara',
            'description' => 'Nome da câmara municipal',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'endereco_camara' => [
            'label' => 'Endereço da Câmara',
            'description' => 'Endereço completo da câmara',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'legislatura_atual' => [
            'label' => 'Legislatura Atual',
            'description' => 'Legislatura em exercício',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'sessao_legislativa' => [
            'label' => 'Sessão Legislativa',
            'description' => 'Sessão legislativa atual',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        
        // Imagens
        'imagem_cabecalho' => [
            'label' => 'Imagem do Cabeçalho',
            'description' => 'Imagem padrão do cabeçalho',
            'category' => 'imagens',
            'type' => 'auto',
            'required' => false
        ],

        // Cabeçalho e Rodapé (parâmetros dinâmicos)
        'cabecalho_nome_camara' => [
            'label' => 'Nome da Câmara',
            'description' => 'Nome oficial da Câmara',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'cabecalho_endereco' => [
            'label' => 'Endereço da Câmara',
            'description' => 'Endereço completo da Câmara',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'cabecalho_telefone' => [
            'label' => 'Telefone da Câmara',
            'description' => 'Telefone oficial da Câmara',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'cabecalho_website' => [
            'label' => 'Website da Câmara',
            'description' => 'Website oficial da Câmara',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'rodape_texto' => [
            'label' => 'Texto do Rodapé',
            'description' => 'Texto padrão do rodapé',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        'assinatura_padrao' => [
            'label' => 'Assinatura Padrão',
            'description' => 'Texto padrão para assinatura',
            'category' => 'instituicao',
            'type' => 'auto',
            'required' => false
        ],
        
        // Campos específicos para templates de Lei Orgânica
        'artigo_alterado' => [
            'label' => 'Artigo Alterado',
            'description' => 'Artigo da Lei Orgânica que será alterado',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => true
        ],
        'nova_redacao' => [
            'label' => 'Nova Redação',
            'description' => 'Nova redação do artigo',
            'category' => 'proposicao',
            'type' => 'textarea',
            'required' => true
        ]
    ];

    /**
     * Obtém todas as variáveis disponíveis, incluindo parâmetros dinâmicos
     */
    public function getAllAvailableVariables(): array
    {
        $systemVariables = self::SYSTEM_VARIABLES;
        
        // Adicionar variáveis dos parâmetros dinâmicos
        $parametros = $this->templateParametrosService->obterParametrosTemplates();
        
        foreach ($parametros as $chave => $valor) {
            $partes = explode('.', $chave);
            if (count($partes) === 2) {
                $submodulo = $partes[0];
                $campo = $partes[1];
                
                // Converter chave para formato de variável (substituir pontos por underscores)
                $variableName = str_replace('.', '_', strtolower($chave));
                
                if (!isset($systemVariables[$variableName])) {
                    $systemVariables[$variableName] = [
                        'label' => ucfirst(str_replace(['_', '.'], [' ', ' '], $campo)),
                        'description' => "Parâmetro do sistema: {$submodulo} - {$campo}",
                        'category' => strtolower(str_replace(' ', '_', $submodulo)),
                        'type' => 'auto',
                        'required' => false,
                        'is_parameter' => true
                    ];
                }
            }
        }
        
        return $systemVariables;
    }

    /**
     * Extrai variáveis de um template
     */
    public function extractVariablesFromTemplate($template): array
    {
        // Handle both TipoProposicaoTemplate and simulated DocumentoModelo objects
        if (is_object($template) && property_exists($template, 'is_documento_modelo') && $template->is_documento_modelo) {
            // For DocumentoModelo, return variaveis if they exist
            if (property_exists($template, 'variaveis') && is_array($template->variaveis)) {
                return $template->variaveis;
            }
            return [];
        }
        
        $content = $this->getTemplateContent($template);
        if (!$content) {
            \Log::warning('Template variable extraction: No content available', [
                'template_id' => $template->id ?? 'unknown',
                'error_type' => 'empty_content'
            ]);
            return [];
        }

        // Regex para encontrar variáveis em diferentes formatos
        // Padrões: ${variavel}, $\{variavel\}, $variavel (sem chaves)
        preg_match_all('/\$\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}|\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}|\$([a-zA-Z_][a-zA-Z0-9_]*)\b/', $content, $matches);
        
        // Combinar todos os grupos de captura
        $allVariables = array_filter(array_merge($matches[1], $matches[2], $matches[3]));
        
        $foundVariables = array_unique($allVariables);
        $templateVariables = [];

        \Log::debug('Template variable extraction completed', [
            'template_id' => $template->id,
            'variables_count' => count($foundVariables),
            'variables' => $foundVariables
        ]);

        // Obter todas as variáveis disponíveis (incluindo parâmetros dinâmicos)
        $allAvailableVariables = $this->getAllAvailableVariables();

        foreach ($foundVariables as $variable) {
            if (isset($allAvailableVariables[$variable])) {
                $templateVariables[$variable] = $allAvailableVariables[$variable];
            } else {
                // Variável personalizada encontrada no template
                $templateVariables[$variable] = [
                    'label' => ucfirst(str_replace('_', ' ', $variable)),
                    'description' => 'Variável personalizada: ' . $variable,
                    'category' => 'personalizada',
                    'type' => 'textarea',
                    'required' => true
                ];
            }
        }

        \Log::info('Template variables mapped', [
            'template_id' => $template->id,
            'total_variables' => count($templateVariables),
            'system_variables' => count(array_filter($templateVariables, fn($v) => !isset($v['is_parameter']))),
            'parameter_variables' => count(array_filter($templateVariables, fn($v) => isset($v['is_parameter'])))
        ]);

        return $templateVariables;
    }

    /**
     * Obtém o conteúdo do template
     */
    private function getTemplateContent($template): ?string
    {
        // Handle both TipoProposicaoTemplate and simulated DocumentoModelo objects
        if (!$template || !property_exists($template, 'arquivo_path') || !$template->arquivo_path) {
            return null;
        }
        
        if (!Storage::exists($template->arquivo_path)) {
            return null;
        }

        try {
            $content = Storage::get($template->arquivo_path);
            
            // Se for um arquivo RTF, extrair apenas o texto
            if ($template->arquivo_path && str_ends_with($template->arquivo_path, '.rtf')) {
                $content = $this->extractTextFromRTF($content);
            }
            
            return $content;
        } catch (\Exception $e) {
            \Log::error('Template variable extraction failed: File read error', [
                'template_id' => $template->id,
                'file_path' => $template->arquivo_path,
                'error' => $e->getMessage(),
                'error_type' => 'file_read_failed'
            ]);
            return null;
        }
    }

    /**
     * Extrai texto de arquivo RTF (melhorado para preservar variáveis)
     */
    private function extractTextFromRTF(string $rtfContent): string
    {
        try {
            // Log do conteúdo original para debug
            \Log::debug('RTF content extraction started', [
                'content_size_bytes' => strlen($rtfContent)
            ]);
            
            $text = $rtfContent;
            
            // ETAPA 1: Primeiro, preservar variáveis ${...} convertendo para placeholders temporários
            $variableMap = [];
            $variableCounter = 0;
            
            // Encontrar padrões de variáveis mesmo com escapes RTF
            if (preg_match_all('/\$\\\\\{([^}\\\\]+)\\\\\}|\$\{([^}]+)\}/', $text, $matches)) {
                foreach ($matches[0] as $index => $fullMatch) {
                    $varName = $matches[1][$index] ?: $matches[2][$index];
                    if ($varName) {
                        $placeholder = "__VAR_" . $variableCounter . "__";
                        $variableMap[$placeholder] = '${' . $varName . '}';
                        $text = str_replace($fullMatch, $placeholder, $text);
                        $variableCounter++;
                    }
                }
            }
            
            // ETAPA 2: Decodificar caracteres especiais hex (\\'XX)
            $text = preg_replace_callback("/\\\\'([0-9a-fA-F]{2})/", function($matches) {
                return chr(hexdec($matches[1]));
            }, $text);
            
            // ETAPA 3: Decodificar caracteres Unicode ANTES de remover outros comandos
            $text = preg_replace_callback('/\\\\u(-?\d+)[\?\*]/', function($matches) {
                $code = intval($matches[1]);
                if ($code < 0) {
                    $code = 65536 + $code;
                }
                if ($code < 128) {
                    return chr($code);
                } else if ($code < 256) {
                    return chr($code);
                } else {
                    return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
                }
            }, $text);
            
            // ETAPA 4: Converter comandos de formatação em quebras de linha
            $text = str_replace(['\\par', '\\line'], "\n", $text);
            $text = str_replace('\\tab', "\t", $text);
            
            // ETAPA 5: Remover grupos com \* (grupos de controle especiais)
            $text = preg_replace('/\{\\\\\*[^}]*\}/s', '', $text);
            
            // ETAPA 6: Remover comandos RTF básicos mas preservar texto
            $text = preg_replace('/\\\\[a-z]+[-]?\d*\s*/i', ' ', $text);
            
            // ETAPA 7: Remover grupos vazios {}
            $text = preg_replace('/\{\s*\}/', '', $text);
            
            // ETAPA 8: Remover chaves restantes
            $text = str_replace(['{', '}'], '', $text);
            
            // ETAPA 9: Restaurar variáveis dos placeholders
            foreach ($variableMap as $placeholder => $variable) {
                $text = str_replace($placeholder, $variable, $text);
            }
            
            // ETAPA 10: Limpeza final
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            
            // Garantir UTF-8 (mais robusto)
            if (!mb_check_encoding($text, 'UTF-8')) {
                // Tentar diferentes encodings
                $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
                foreach ($encodings as $encoding) {
                    if (mb_check_encoding($text, $encoding)) {
                        $text = mb_convert_encoding($text, 'UTF-8', $encoding);
                        break;
                    }
                }
                // Se nenhum encoding funcionar, limpar caracteres inválidos
                if (!mb_check_encoding($text, 'UTF-8')) {
                    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8//IGNORE');
                }
            }
            
            \Log::debug('RTF content extraction completed', [
                'original_size_bytes' => strlen($rtfContent),
                'extracted_size_bytes' => strlen($text),
                'variables_preserved' => count($variableMap),
                'extraction_ratio' => round((strlen($text) / strlen($rtfContent)) * 100, 1) . '%'
            ]);
            
            return $text;
            
        } catch (\Exception $e) {
            \Log::error('RTF content extraction failed', [
                'error' => $e->getMessage(),
                'error_type' => 'rtf_processing_failed'
            ]);
            
            // Fallback: método simples para extrair ao menos as variáveis
            return $this->extractTextFromRTFSimple($rtfContent);
        }
    }

    /**
     * Método simples de extração RTF como fallback
     */
    private function extractTextFromRTFSimple(string $rtfContent): string
    {
        try {
            \Log::warning('RTF extraction: Using simple fallback method', [
                'reason' => 'Advanced extraction failed'
            ]);
            
            // Simplesmente remover códigos RTF básicos e manter variáveis
            $text = $rtfContent;
            
            // Preservar variáveis primeiro
            $variables = [];
            if (preg_match_all('/\$\\\\\{([^}\\\\]+)\\\\\}|\$\{([^}]+)\}/', $text, $matches)) {
                foreach ($matches[0] as $match) {
                    $variables[] = $match;
                }
            }
            
            // Limpeza básica
            $text = preg_replace('/\{\\\\[^}]*\}/', ' ', $text);
            $text = preg_replace('/\\\\[a-zA-Z]+\d*/', ' ', $text);
            $text = str_replace(['{', '}', '\\'], ' ', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            
            // Adicionar variáveis encontradas ao texto limpo
            if (!empty($variables)) {
                $text .= ' ' . implode(' ', $variables);
            }
            
            \Log::debug('RTF fallback extraction completed', [
                'variables_preserved' => count($variables),
                'extracted_size_bytes' => strlen($text)
            ]);
            
            return trim($text);
            
        } catch (\Exception $e) {
            \Log::error('RTF extraction failed completely', [
                'error' => $e->getMessage(),
                'error_type' => 'rtf_fallback_failed'
            ]);
            return '';
        }
    }

    /**
     * Agrupa variáveis por categoria
     */
    public function groupVariablesByCategory(array $variables): array
    {
        $grouped = [];
        
        foreach ($variables as $key => $variable) {
            $category = $variable['category'] ?? 'outras';
            $grouped[$category][$key] = $variable;
        }

        // Ordem das categorias
        $categoryOrder = ['proposicao', 'autor', 'artigos', 'datas', 'instituicao', 'imagens', 'personalizada', 'outras'];
        $orderedGroups = [];

        foreach ($categoryOrder as $category) {
            if (isset($grouped[$category])) {
                $orderedGroups[$category] = $grouped[$category];
            }
        }

        return $orderedGroups;
    }

    /**
     * Obtém rótulos amigáveis para categorias
     */
    public function getCategoryLabels(): array
    {
        return [
            'proposicao' => 'Dados da Proposição',
            'autor' => 'Autor & Parlamentar', 
            'artigos' => 'Artigos',
            'datas' => 'Datas & Horários',
            'instituicao' => 'Instituição',
            'imagens' => 'Imagens & Mídia',
            'personalizada' => 'Campos Personalizados',
            'outras' => 'Outros Campos'
        ];
    }

    /**
     * Filtra apenas variáveis que precisam ser preenchidas pelo usuário
     */
    public function getRequiredUserInputVariables(array $variables): array
    {
        return array_filter($variables, function($variable) {
            return $variable['type'] !== 'auto';
        });
    }

    /**
     * Processar template substituindo variáveis com valores dos parâmetros
     */
    public function processTemplateWithParameters(string $conteudo, array $dadosUsuario = []): string
    {
        // Usar o serviço de parâmetros para processar o template
        return $this->templateParametrosService->processarTemplate($conteudo, $dadosUsuario);
    }

    /**
     * Obter o valor atual de uma variável específica dos parâmetros
     */
    public function getVariableValue(string $variableName, array $dados = []): string
    {
        // Preparar dados para o processamento
        $parametros = $this->templateParametrosService->obterParametrosTemplates();
        
        // Mapear nome da variável para chave do parâmetro
        $mapeamento = [
            'cabecalho_nome_camara' => 'Cabeçalho.cabecalho_nome_camara',
            'cabecalho_endereco' => 'Cabeçalho.cabecalho_endereco',
            'cabecalho_telefone' => 'Cabeçalho.cabecalho_telefone',
            'cabecalho_website' => 'Cabeçalho.cabecalho_website',
            'rodape_texto' => 'Rodapé.rodape_texto',
            'assinatura_padrao' => 'Variáveis Dinâmicas.var_assinatura_padrao',
            'imagem_cabecalho' => 'Cabeçalho.cabecalho_imagem'
        ];
        
        if (isset($mapeamento[$variableName])) {
            return $parametros[$mapeamento[$variableName]] ?? '';
        }
        
        // Se não encontrou no mapeamento, tentar buscar diretamente
        $chaveParametro = str_replace('_', '.', ucfirst($variableName));
        return $parametros[$chaveParametro] ?? '';
    }
}