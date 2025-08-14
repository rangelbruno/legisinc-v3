<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TipoProposicaoTemplate;
use App\Models\User;
use App\Services\Parametro\ParametroService;
use App\Services\Template\TemplateVariableService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TemplateProcessorService
{
    protected ParametroService $parametroService;
    protected TemplateVariableService $templateVariableService;

    public function __construct(ParametroService $parametroService, TemplateVariableService $templateVariableService)
    {
        $this->parametroService = $parametroService;
        $this->templateVariableService = $templateVariableService;
    }

    private array $systemVariables = [
        // Datas e horários
        'data' => 'Data atual (formato: dd/mm/aaaa)',
        'data_atual' => 'Data atual (formato: dd/mm/aaaa)',
        'data_extenso' => 'Data por extenso',
        'mes_atual' => 'Mês atual',
        'ano_atual' => 'Ano atual',
        'dia_atual' => 'Dia atual',
        'hora_atual' => 'Hora atual',
        'data_criacao' => 'Data de criação da proposição',
        
        // Proposição
        'numero_proposicao' => 'Número da proposição',
        'tipo_proposicao' => 'Tipo da proposição',
        'status_proposicao' => 'Status atual da proposição',
        
        // Parlamentar
        'nome_parlamentar' => 'Nome do parlamentar logado',
        'autor_nome' => 'Nome do autor da proposição',
        'cargo_parlamentar' => 'Cargo do parlamentar',
        'email_parlamentar' => 'Email do parlamentar',
        'partido_parlamentar' => 'Partido do parlamentar',
        
        // Instituição
        'nome_municipio' => 'Nome do município',
        'municipio' => 'Nome do município',
        'nome_camara' => 'Nome da câmara',
        'endereco_camara' => 'Endereço da câmara',
        'legislatura_atual' => 'Legislatura atual',
        'sessao_legislativa' => 'Sessão legislativa atual',
        
        // Imagens padrão
        'imagem_cabecalho' => 'Imagem padrão do cabeçalho'
    ];

    private array $editableVariables = [
        'ementa' => 'Ementa da proposição',
        'texto' => 'Texto principal da proposição',
        'justificativa' => 'Justificativa da proposição',
        'observacoes' => 'Observações adicionais',
        'considerandos' => 'Considerandos (separados por ponto e vírgula)',
        'artigo_1' => 'Primeiro artigo',
        'artigo_2' => 'Segundo artigo',
        'artigo_3' => 'Terceiro artigo'
    ];

    /**
     * Processar template com dados da proposição
     */
    public function processarTemplate(
        TipoProposicaoTemplate $template, 
        Proposicao $proposicao, 
        array $dadosEditaveis = []
    ): string {
        // Obter conteúdo do template
        $conteudo = $this->obterConteudoTemplate($template);
        
        // Preparar variáveis do sistema
        $variaveisSystem = $this->prepararVariaveisSystem($proposicao);
        
        // Preparar variáveis editáveis
        $variaveisEditaveis = $this->prepararVariaveisEditaveis($dadosEditaveis);
        
        // Combinar todas as variáveis
        $todasVariaveis = array_merge($variaveisSystem, $variaveisEditaveis);
        
        // Processar template
        $conteudoProcessado = $this->substituirVariaveis($conteudo, $todasVariaveis);
        
        return $conteudoProcessado;
    }

    /**
     * Validar se template possui todas as variáveis necessárias
     */
    public function validarTemplate(string $conteudo): array
    {
        $variaveisEncontradas = $this->extrairVariaveis($conteudo);
        $variaveisValidas = array_merge(
            array_keys($this->systemVariables),
            array_keys($this->editableVariables)
        );
        
        $variaveisInvalidas = array_diff($variaveisEncontradas, $variaveisValidas);
        $variaveisNaoUsadas = array_diff($variaveisValidas, $variaveisEncontradas);
        
        return [
            'valido' => empty($variaveisInvalidas),
            'variaveis_encontradas' => $variaveisEncontradas,
            'variaveis_invalidas' => $variaveisInvalidas,
            'variaveis_nao_usadas' => $variaveisNaoUsadas,
            'variaveis_system' => array_intersect($variaveisEncontradas, array_keys($this->systemVariables)),
            'variaveis_editaveis' => array_intersect($variaveisEncontradas, array_keys($this->editableVariables))
        ];
    }

    /**
     * Obter preview do template com dados simulados
     */
    public function gerarPreview(TipoProposicaoTemplate $template): string
    {
        $conteudo = $this->obterConteudoTemplate($template);
        
        // Dados simulados para preview
        $dadosSimulados = $this->obterDadosSimulados();
        
        return $this->substituirVariaveis($conteudo, $dadosSimulados);
    }

    /**
     * Extrair todas as variáveis do template
     */
    public function extrairVariaveis(string $conteudo): array
    {
        preg_match_all('/\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $conteudo, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Obter lista de variáveis disponíveis
     */
    public function getVariaveisDisponiveis(): array
    {
        return [
            'system' => $this->systemVariables,
            'editaveis' => $this->editableVariables
        ];
    }

    /**
     * Substituir variáveis no conteúdo
     */
    private function substituirVariaveis(string $conteudo, array $variaveis): string
    {
        // Verificar se é conteúdo RTF
        $isRTF = strpos($conteudo, '{\rtf') !== false;
        
        foreach ($variaveis as $variavel => $valor) {
            // Tratamento especial para imagens em RTF
            if ($isRTF && ($variavel === 'imagem_cabecalho' || $variavel === 'cabecalho_imagem')) {
                if ($valor && file_exists(public_path($valor))) {
                    // Log para debug
                    \Log::info('Imagem do cabeçalho encontrada', [
                        'path' => $valor,
                        'full_path' => public_path($valor),
                        'exists' => true
                    ]);
                    
                    // Gerar código RTF para imagem
                    $valor = $this->gerarImagemRTF(public_path($valor));
                } else {
                    \Log::warning('Imagem do cabeçalho não encontrada', [
                        'path' => $valor,
                        'full_path' => public_path($valor),
                        'exists' => false
                    ]);
                    $valor = ''; // Deixar vazio se não encontrada
                }
            } else if ($isRTF) {
                // Primeiro, corrigir caracteres mal codificados comuns
                $valor = $this->corrigirCaracteresMalCodificados($valor);
                
                // Depois converter para códigos RTF se necessário
                $valor = $this->converterParaRTF($valor);
            }
            
            // Tratar diferentes formatos de variáveis encontrados no template
            $formatos = [
                '$\\{' . $variavel . '\\}',  // Formato RTF escapado: $\{variavel\}
                '${' . $variavel . '}',      // Formato normal: ${variavel}
            ];
            
            foreach ($formatos as $formato) {
                $antes = substr_count($conteudo, $formato);
                if ($antes > 0) {
                    $conteudo = str_replace($formato, $valor, $conteudo);
                    // Debug para verificar substituições
                    // Log::info("Substituído $formato por $valor ($antes ocorrências)");
                }
            }
        }
        
        // Limpar variáveis não substituídas (mostrar como placeholders)
        $conteudo = preg_replace('/\$\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/', '[${$1}]', $conteudo);
        $conteudo = preg_replace('/\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '[${$1}]', $conteudo);
        
        return $conteudo;
    }
    
    /**
     * Corrigir caracteres mal codificados comuns
     */
    private function corrigirCaracteresMalCodificados(string $texto): string
    {
        // Corrigir padrões comuns de dupla codificação UTF-8
        $correcoes = [
            // Problemas comuns de dupla codificação
            'MunicÃ­pio' => 'Município',
            'SÃ£o Paulo' => 'São Paulo',
            'CÃ¢mara' => 'Câmara',
            'relaÃ§Ã£o' => 'relação',
            'posiÃ§Ã£o' => 'posição',
            'funÃ§Ã£o' => 'função',
            'criaÃ§Ã£o' => 'criação',
            'legislaÃ§Ã£o' => 'legislação',
            'aprovaÃ§Ã£o' => 'aprovação',
            'situaÃ§Ã£o' => 'situação',
            'informaÃ§Ã£o' => 'informação',
            'descriÃ§Ã£o' => 'descrição',
            'conclusÃ£o' => 'conclusão',
            'populaÃ§Ã£o' => 'população',
            'administraÃ§Ã£o' => 'administração',
            
            // Outros padrões comuns
            'nÃ£o' => 'não',
            'tambÃ©m' => 'também',
            'atà ' => 'até',
            'histÃ³ria' => 'história',
            'memÃ³ria' => 'memória',
            'territÃ³rio' => 'território',
            'obrigatÃ³rio' => 'obrigatório',
            'necessÃ¡rio' => 'necessário',
            'primÃ¡rio' => 'primário',
            'secundÃ¡rio' => 'secundário',
            'temporÃ¡rio' => 'temporário',
            'ordinÃ¡rio' => 'ordinário',
            'extraordinÃ¡rio' => 'extraordinário'
        ];
        
        return str_replace(array_keys($correcoes), array_values($correcoes), $texto);
    }
    
    /**
     * Converter texto UTF-8 para códigos RTF Unicode
     */
    private function converterParaRTF(string $texto): string
    {
        $textoProcessado = '';
        $length = mb_strlen($texto, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            
            if ($codepoint > 127) {
                // Gerar sequência RTF Unicode correta: \uN*
                $textoProcessado .= '\\u' . $codepoint . '*';
            } else {
                // Caracteres ASCII normais
                $textoProcessado .= $char;
            }
        }
        
        return $textoProcessado;
    }

    /**
     * Preparar variáveis do sistema
     */
    private function prepararVariaveisSystem(Proposicao $proposicao): array
    {
        $user = Auth::user();
        $agora = Carbon::now();
        
        // Obter autor da proposição (pode ser diferente do usuário logado)
        $autor = $proposicao->autor ?? $user;
        
        // Obter variáveis do TemplateVariableService (parâmetros do sistema)
        $variaveisParametros = $this->templateVariableService->getTemplateVariables();
        
        // Combinar com variáveis específicas da proposição
        $variaveisProposicao = [
            // Datas e horários
            'data' => $agora->format('d/m/Y'),
            'data_atual' => $agora->format('d/m/Y'),
            'data_extenso' => $this->formatarDataExtenso($agora),
            'mes_atual' => $agora->format('m'),
            'ano_atual' => $agora->format('Y'),
            'dia_atual' => $agora->format('d'),
            'dia' => $agora->format('d'), // Compatibilidade RTF
            'hora_atual' => $agora->format('H:i'),
            'data_criacao' => $proposicao->created_at?->format('d/m/Y') ?? $agora->format('d/m/Y'),
            
            // Mês por extenso em português
            'mes_extenso' => $this->obterMesExtenso($agora->format('n')),
            
            // Proposição
            'numero_proposicao' => $this->gerarNumeroProposicao($proposicao),
            'tipo_proposicao' => $proposicao->tipo_formatado ?? '[TIPO DA PROPOSIÇÃO]',
            'status_proposicao' => $proposicao->status ?? 'rascunho',
            'ementa' => $proposicao->ementa ?? '[EMENTA]',
            'texto' => $proposicao->conteudo ?? '[TEXTO]', // Corrigir campo
            'justificativa' => $proposicao->justificativa ?? '[JUSTIFICATIVA]',
            
            // Parlamentar / Autor
            'nome_parlamentar' => $user->name ?? '[NOME DO PARLAMENTAR]',
            'autor_nome' => $autor->name ?? '[NOME DO AUTOR]',
            'autor_cargo' => $this->obterCargoParlamentar($autor),
            'cargo_parlamentar' => $this->obterCargoParlamentar($user),
            'email_parlamentar' => $user->email ?? '[EMAIL DO PARLAMENTAR]',
            'partido_parlamentar' => $this->obterPartidoParlamentar($user),
        ];
        
        // Combinar todas as variáveis - proposição tem precedência sobre parâmetros para dados específicos
        $todasVariaveis = array_merge($variaveisParametros, $variaveisProposicao);
        
        // Garantir que variáveis críticas da proposição não sejam sobrescritas
        $variaveisCriticas = [
            'ementa' => $proposicao->ementa ?? '[EMENTA]',
            'texto' => $proposicao->conteudo ?? '[TEXTO]',
            'numero_proposicao' => $this->gerarNumeroProposicao($proposicao),
            'autor_nome' => $autor->name ?? '[NOME DO AUTOR]',
            'autor_cargo' => $this->obterCargoParlamentar($autor)
        ];
        
        return array_merge($todasVariaveis, $variaveisCriticas);
    }

    /**
     * Preparar variáveis editáveis
     */
    private function prepararVariaveisEditaveis(array $dados): array
    {
        $variaveisEditaveis = [];
        
        // Apenas incluir variáveis editáveis que foram fornecidas
        foreach (array_keys($this->editableVariables) as $variavel) {
            if (isset($dados[$variavel]) && !empty($dados[$variavel])) {
                $variaveisEditaveis[$variavel] = $dados[$variavel];
            }
        }
        
        return $variaveisEditaveis;
    }

    /**
     * Obter conteúdo do template
     */
    private function obterConteudoTemplate(TipoProposicaoTemplate $template): string
    {
        // Se template tem arquivo físico
        if ($template->arquivo_path) {
            $conteudo = null;
            
            // Verificar primeiro no disco local (storage/app/private/)
            if (\Storage::disk('local')->exists($template->arquivo_path)) {
                $conteudo = \Storage::disk('local')->get($template->arquivo_path);
            } elseif (\Storage::disk('public')->exists($template->arquivo_path)) {
                $conteudo = \Storage::disk('public')->get($template->arquivo_path);
            }
            
            if ($conteudo) {
                // Para RTF do OnlyOffice, retornar diretamente sem processamento
                // O arquivo já contém as variáveis no formato correto
                return $conteudo;
            }
        }
        
        // Template básico se não houver arquivo
        return $this->getTemplateBasico($template->tipoProposicao->nome ?? 'Proposição');
    }

    /**
     * Extrair texto de arquivo RTF
     */
    private function extrairTextoRTF(string $conteudoRTF): string
    {
        // Para RTF do OnlyOffice, não fazer conversão de encoding - retornar como está
        // O RTF já contém as variáveis corretas e deve ser processado diretamente
        return $conteudoRTF;
    }
    
    /**
     * Processar caracteres especiais do RTF
     */
    private function processarCaracteresEspeciaisRTF(string $texto): string
    {
        // Mapear códigos RTF comuns para caracteres UTF-8
        $mapeamento = [
            "\\'e1" => 'á',
            "\\'e0" => 'à',
            "\\'e2" => 'â',
            "\\'e3" => 'ã',
            "\\'e9" => 'é',
            "\\'ea" => 'ê',
            "\\'ed" => 'í',
            "\\'f3" => 'ó',
            "\\'f4" => 'ô',
            "\\'f5" => 'õ',
            "\\'fa" => 'ú',
            "\\'fc" => 'ü',
            "\\'e7" => 'ç',
            "\\'c1" => 'Á',
            "\\'c0" => 'À',
            "\\'c2" => 'Â',
            "\\'c3" => 'Ã',
            "\\'c9" => 'É',
            "\\'ca" => 'Ê',
            "\\'cd" => 'Í',
            "\\'d3" => 'Ó',
            "\\'d4" => 'Ô',
            "\\'d5" => 'Õ',
            "\\'da" => 'Ú',
            "\\'dc" => 'Ü',
            "\\'c7" => 'Ç',
        ];
        
        foreach ($mapeamento as $rtf => $utf8) {
            $texto = str_replace($rtf, $utf8, $texto);
        }
        
        // Processar códigos hexadecimais (\\'XX)
        $texto = preg_replace_callback("/\\\\'([a-fA-F0-9]{2})/", function($matches) {
            $hex = $matches[1];
            $decimal = hexdec($hex);
            
            // Converter para UTF-8 baseado na codificação Windows-1252/ISO-8859-1
            if ($decimal >= 128) {
                return mb_convert_encoding(chr($decimal), 'UTF-8', 'Windows-1252');
            }
            
            return chr($decimal);
        }, $texto);
        
        return $texto;
    }

    /**
     * Obter cargo do parlamentar
     */
    private function obterCargoParlamentar(?User $user): string
    {
        if (!$user) return '[CARGO DO PARLAMENTAR]';
        
        // Buscar na tabela de perfis/roles
        $roles = $user->getRoleNames();
        
        if ($roles->contains('Vereador')) return 'Vereador';
        if ($roles->contains('Presidente')) return 'Presidente da Câmara';
        if ($roles->contains('Vice-Presidente')) return 'Vice-Presidente da Câmara';
        if ($roles->contains('Secretario')) return 'Secretário da Câmara';
        
        return 'Parlamentar';
    }

    /**
     * Obter partido do parlamentar
     */
    private function obterPartidoParlamentar(?User $user): string
    {
        if (!$user) return '[PARTIDO]';
        
        // Verificar se existe campo partido no usuário
        if (isset($user->partido)) {
            return $user->partido;
        }
        
        // Verificar se existe relação com modelo Parlamentar
        if (method_exists($user, 'parlamentar') && $user->parlamentar) {
            return $user->parlamentar->partido ?? '[PARTIDO]';
        }
        
        return '[PARTIDO]';
    }

    /**
     * Obter configurações do cabeçalho dos templates
     */
    private function obterConfiguracoesTemplate(): array
    {
        try {
            return [
                'imagem' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_imagem') ?: 'template/cabecalho.png',
                'usar_padrao' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'usar_cabecalho_padrao') ?: true,
                'altura' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_altura') ?: 150,
                'posicao' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_posicao') ?: 'topo'
            ];
        } catch (\Exception $e) {
            // Se houver erro, usar valores padrão
            return [
                'imagem' => 'template/cabecalho.png',
                'usar_padrao' => true,
                'altura' => 150,
                'posicao' => 'topo'
            ];
        }
    }

    /**
     * Obter URL da imagem do cabeçalho
     */
    private function obterImagemCabecalho(): string
    {
        $configuracoes = $this->obterConfiguracoesTemplate();
        $imagemPath = $configuracoes['imagem'];
        
        // Se o arquivo existir no storage público, usar ele
        if (file_exists(public_path($imagemPath))) {
            return asset($imagemPath);
        }
        
        // Senão, usar a imagem padrão
        return asset('template/cabecalho.png');
    }

    /**
     * Verificar se deve aplicar cabeçalho automaticamente
     */
    public function deveAplicarCabecalho(): bool
    {
        $configuracoes = $this->obterConfiguracoesTemplate();
        return (bool) $configuracoes['usar_padrao'];
    }

    /**
     * Obter altura do cabeçalho em pixels
     */
    public function obterAlturaCabecalho(): int
    {
        $configuracoes = $this->obterConfiguracoesTemplate();
        return (int) $configuracoes['altura'];
    }

    /**
     * Obter posição do cabeçalho
     */
    public function obterPosicaoCabecalho(): string
    {
        $configuracoes = $this->obterConfiguracoesTemplate();
        return $configuracoes['posicao'];
    }

    /**
     * Formatar data por extenso
     */
    private function formatarDataExtenso(Carbon $data): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];
        
        return $data->day . ' de ' . $meses[$data->month] . ' de ' . $data->year;
    }

    /**
     * Obter nome do mês por extenso
     */
    private function obterMesExtenso(int $mes): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];
        
        return $meses[$mes] ?? 'janeiro';
    }

    /**
     * Gerar número da proposição
     */
    private function gerarNumeroProposicao(Proposicao $proposicao): string
    {
        if ($proposicao->numero) {
            return $proposicao->numero;
        }
        
        // Gerar número baseado no ID e ano
        return sprintf('%04d/%d', $proposicao->id, $proposicao->ano ?? date('Y'));
    }

    /**
     * Obter dados simulados para preview
     */
    private function obterDadosSimulados(): array
    {
        $agora = Carbon::now();
        
        return array_merge(
            [
                // Datas e horários
                'data' => $agora->format('d/m/Y'),
                'data_atual' => $agora->format('d/m/Y'),
                'data_extenso' => $this->formatarDataExtenso($agora),
                'mes_atual' => $agora->format('m'),
                'ano_atual' => $agora->format('Y'),
                'dia_atual' => $agora->format('d'),
                'hora_atual' => $agora->format('H:i'),
                'data_criacao' => $agora->format('d/m/Y'),
                
                // Proposição
                'numero_proposicao' => '0001/' . $agora->format('Y'),
                'tipo_proposicao' => 'Projeto de Lei Ordinária',
                'status_proposicao' => 'Rascunho',
                
                // Parlamentar / Autor
                'nome_parlamentar' => 'João da Silva',
                'autor_nome' => 'João da Silva',
                'cargo_parlamentar' => 'Vereador',
                'email_parlamentar' => 'joao.silva@camara.gov.br',
                'partido_parlamentar' => 'PSB',
                
                // Instituição
                'nome_municipio' => 'São Paulo',
                'municipio' => 'São Paulo',
                'nome_camara' => 'Câmara Municipal de São Paulo',
                'endereco_camara' => 'Viaduto Jacareí, 100 - Bela Vista',
                'legislatura_atual' => '2021-2024',
                'sessao_legislativa' => $agora->format('Y'),
                
                // Imagens padrão
                'imagem_cabecalho' => asset('template/cabecalho.png')
            ],
            [
                'ementa' => 'Dispõe sobre exemplo de proposição legislativa',
                'texto' => 'Art. 1º Esta lei estabelece as normas gerais...',
                'justificativa' => 'A presente proposição justifica-se pela necessidade...',
                'observacoes' => 'Observações adicionais sobre a proposição',
                'considerandos' => 'Considerando a necessidade; Considerando a importância',
                'artigo_1' => 'Fica estabelecido que...',
                'artigo_2' => 'Esta lei entra em vigor na data de sua publicação',
                'artigo_3' => 'Revogam-se as disposições em contrário'
            ]
        );
    }

    /**
     * Gerar código RTF para imagem
     */
    private function gerarImagemRTF(string $caminhoImagem): string
    {
        try {
            // Verificar se o arquivo existe
            if (!file_exists($caminhoImagem)) {
                return '';
            }
            
            // Obter informações da imagem
            $imageInfo = getimagesize($caminhoImagem);
            if (!$imageInfo) {
                return '';
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            // Calcular dimensões para RTF (em twips - 1440 twips = 1 polegada)
            // Redimensionar para caber no cabeçalho (máximo 2 polegadas de largura)
            $maxWidthInches = 2;
            $maxWidthTwips = $maxWidthInches * 1440;
            
            $aspectRatio = $width / $height;
            
            if ($width > 200) { // Se a imagem for muito grande
                $rtfWidth = $maxWidthTwips;
                $rtfHeight = (int)($rtfWidth / $aspectRatio);
            } else {
                // Usar tamanho original convertido para twips (assumindo 96 DPI)
                $rtfWidth = (int)($width * 15); // 1440/96 = 15
                $rtfHeight = (int)($height * 15);
            }
            
            // Ler conteúdo da imagem
            $imageContent = file_get_contents($caminhoImagem);
            if (!$imageContent) {
                return '';
            }
            
            // Converter para hexadecimal
            $hexData = bin2hex($imageContent);
            
            // Determinar tipo de imagem
            $extension = strtolower(pathinfo($caminhoImagem, PATHINFO_EXTENSION));
            $imageType = 'pngblip'; // Padrão PNG
            
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $imageType = 'jpegblip';
            }
            
            // Gerar código RTF para a imagem
            $rtf = '{\\pict\\' . $imageType . '\\picw' . $width . '\\pich' . $height;
            $rtf .= '\\picwgoal' . $rtfWidth . '\\pichgoal' . $rtfHeight . ' ';
            
            // Adicionar dados hexadecimais em blocos de 80 caracteres
            $hexLines = str_split($hexData, 80);
            foreach ($hexLines as $line) {
                $rtf .= $line . "\n";
            }
            
            $rtf .= '}';
            
            return $rtf;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar imagem RTF', [
                'path' => $caminhoImagem,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Obter template básico
     */
    private function getTemplateBasico(string $tipoNome): string
    {
        return '# ${tipo_proposicao} Nº ${numero_proposicao}

**Data:** ${data}
**Autor:** ${nome_parlamentar} - ${cargo_parlamentar}

## EMENTA
${ementa}

## TEXTO

${texto}

## JUSTIFICATIVA
${justificativa}

---
${nome_camara}
${data_extenso}';
    }
}