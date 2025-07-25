<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TipoProposicaoTemplate;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TemplateProcessorService
{
    private array $systemVariables = [
        'data' => 'Data atual',
        'nome_parlamentar' => 'Nome do parlamentar logado',
        'cargo_parlamentar' => 'Cargo do parlamentar',
        'email_parlamentar' => 'Email do parlamentar',
        'data_extenso' => 'Data por extenso',
        'mes_atual' => 'Mês atual',
        'ano_atual' => 'Ano atual',
        'dia_atual' => 'Dia atual',
        'hora_atual' => 'Hora atual',
        'data_criacao' => 'Data de criação da proposição',
        'numero_proposicao' => 'Número da proposição',
        'tipo_proposicao' => 'Tipo da proposição',
        'nome_municipio' => 'Nome do município',
        'nome_camara' => 'Nome da câmara',
        'legislatura_atual' => 'Legislatura atual',
        'sessao_legislativa' => 'Sessão legislativa atual'
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
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $conteudo, $matches);
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
        foreach ($variaveis as $variavel => $valor) {
            $placeholder = '{' . $variavel . '}';
            $conteudo = str_replace($placeholder, $valor, $conteudo);
        }
        
        // Limpar variáveis não substituídas (mostrar como placeholders)
        $conteudo = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '[{$1}]', $conteudo);
        
        return $conteudo;
    }

    /**
     * Preparar variáveis do sistema
     */
    private function prepararVariaveisSystem(Proposicao $proposicao): array
    {
        $user = Auth::user();
        $agora = Carbon::now();
        
        return [
            'data' => $agora->format('d/m/Y'),
            'nome_parlamentar' => $user->name ?? '[NOME DO PARLAMENTAR]',
            'cargo_parlamentar' => $this->obterCargoParlamentar($user),
            'email_parlamentar' => $user->email ?? '[EMAIL DO PARLAMENTAR]',
            'data_extenso' => $this->formatarDataExtenso($agora),
            'mes_atual' => $agora->format('m'),
            'ano_atual' => $agora->format('Y'),
            'dia_atual' => $agora->format('d'),
            'hora_atual' => $agora->format('H:i'),
            'data_criacao' => $proposicao->created_at?->format('d/m/Y') ?? $agora->format('d/m/Y'),
            'numero_proposicao' => $this->gerarNumeroProposicao($proposicao),
            'tipo_proposicao' => $proposicao->tipo_formatado ?? '[TIPO DA PROPOSIÇÃO]',
            'nome_municipio' => config('app.municipio', 'São Paulo'),
            'nome_camara' => config('app.nome_camara', 'Câmara Municipal'),
            'legislatura_atual' => config('app.legislatura', '2021-2024'),
            'sessao_legislativa' => $agora->format('Y')
        ];
    }

    /**
     * Preparar variáveis editáveis
     */
    private function prepararVariaveisEditaveis(array $dados): array
    {
        $variaveisEditaveis = [];
        
        foreach (array_keys($this->editableVariables) as $variavel) {
            $variaveisEditaveis[$variavel] = $dados[$variavel] ?? '[' . strtoupper($variavel) . ']';
        }
        
        return $variaveisEditaveis;
    }

    /**
     * Obter conteúdo do template
     */
    private function obterConteudoTemplate(TipoProposicaoTemplate $template): string
    {
        // Se template tem arquivo físico
        if ($template->arquivo_path && \Storage::disk('public')->exists($template->arquivo_path)) {
            $conteudo = \Storage::disk('public')->get($template->arquivo_path);
            
            // Se é RTF, extrair texto simples
            if (Str::endsWith($template->arquivo_path, '.rtf')) {
                return $this->extrairTextoRTF($conteudo);
            }
            
            return $conteudo;
        }
        
        // Template básico se não houver arquivo
        return $this->getTemplateBasico($template->tipoProposicao->nome ?? 'Proposição');
    }

    /**
     * Extrair texto de arquivo RTF
     */
    private function extrairTextoRTF(string $conteudoRTF): string
    {
        // Remover códigos RTF básicos
        $texto = preg_replace('/\{\\\\.*?\}/', '', $conteudoRTF);
        $texto = preg_replace('/\\\\[a-z]+[0-9]*\s?/', '', $texto);
        $texto = str_replace(['\\par', '\\'], ['', ''], $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        
        return trim($texto);
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
                'data' => $agora->format('d/m/Y'),
                'nome_parlamentar' => 'João da Silva',
                'cargo_parlamentar' => 'Vereador',
                'email_parlamentar' => 'joao.silva@camara.gov.br',
                'data_extenso' => $this->formatarDataExtenso($agora),
                'mes_atual' => $agora->format('m'),
                'ano_atual' => $agora->format('Y'),
                'dia_atual' => $agora->format('d'),
                'hora_atual' => $agora->format('H:i'),
                'data_criacao' => $agora->format('d/m/Y'),
                'numero_proposicao' => '0001/' . $agora->format('Y'),
                'tipo_proposicao' => 'Projeto de Lei Ordinária',
                'nome_municipio' => 'São Paulo',
                'nome_camara' => 'Câmara Municipal de São Paulo',
                'legislatura_atual' => '2021-2024',
                'sessao_legislativa' => $agora->format('Y')
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
     * Obter template básico
     */
    private function getTemplateBasico(string $tipoProposicao): string
    {
        return "# {tipo_proposicao} Nº {numero_proposicao}

**Data:** {data}
**Autor:** {nome_parlamentar} - {cargo_parlamentar}

## EMENTA
{ementa}

## TEXTO

{texto}

## JUSTIFICATIVA
{justificativa}

---
{nome_camara}
{data_extenso}";
    }
}