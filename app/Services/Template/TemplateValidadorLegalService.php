<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TipoProposicao;
use Illuminate\Support\Collection;

class TemplateValidadorLegalService
{
    /**
     * Validar proposição completa conforme LC 95/1998 e padrões técnicos
     */
    public function validarProposicaoCompleta(array $dados, TipoProposicao $tipo): array
    {
        $resultados = [];
        
        // Validações da LC 95/1998
        $resultados['lc95_1998'] = $this->validarLC95($dados);
        
        // Validações de estrutura textual
        $resultados['estrutura_textual'] = $this->validarEstruturaTextual($dados);
        
        // Validações de metadados
        $resultados['metadados'] = $this->validarMetadados($dados, $tipo);
        
        // Validações de numeração
        $resultados['numeracao'] = $this->validarNumeracao($dados, $tipo);
        
        // Validações de acessibilidade
        $resultados['acessibilidade'] = $this->validarAcessibilidade($dados);
        
        // Consolidar resultado geral
        $resultados['resumo'] = $this->consolidarResultados($resultados);
        
        return $resultados;
    }

    /**
     * Validar conformidade com LC 95/1998
     */
    public function validarLC95(array $dados): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        // Art. 7º - Estrutura obrigatória
        if (empty($dados['ementa'])) {
            $erros[] = 'Ementa é obrigatória (LC 95/1998, Art. 7º)';
        } else {
            $aprovado[] = 'Ementa presente';
            
            // Ementa deve ser frase única
            if (substr_count($dados['ementa'], '.') > 1) {
                $avisos[] = 'Ementa deve ser uma frase única (LC 95/1998, Art. 7º)';
            } else {
                $aprovado[] = 'Ementa é frase única';
            }
            
            // Ementa não deve terminar com "etc."
            if (str_contains(strtolower($dados['ementa']), 'etc.')) {
                $avisos[] = 'Evitar "etc." na ementa (LC 95/1998, Art. 7º, § 3º)';
            }
        }

        // Texto principal obrigatório
        if (empty($dados['texto']) && empty($dados['conteudo'])) {
            $erros[] = 'Texto principal é obrigatório (LC 95/1998, Art. 7º)';
        } else {
            $aprovado[] = 'Texto principal presente';
        }

        // Art. 10º - Numeração de artigos
        $texto = $dados['texto'] ?? $dados['conteudo'] ?? '';
        if (!empty($texto)) {
            $validacaoArtigos = $this->validarNumeracaoArtigos($texto);
            $erros = array_merge($erros, $validacaoArtigos['erros']);
            $avisos = array_merge($avisos, $validacaoArtigos['avisos']);
            $aprovado = array_merge($aprovado, $validacaoArtigos['aprovado']);
        }

        // Art. 11º - Cláusula de vigência
        if (!empty($texto)) {
            if (!$this->temClausulaVigencia($texto)) {
                $avisos[] = 'Recomenda-se incluir cláusula de vigência (LC 95/1998, Art. 11º)';
            } else {
                $aprovado[] = 'Cláusula de vigência presente';
            }
        }

        // Art. 12º - Citações
        if (!empty($texto)) {
            $validacaoCitacoes = $this->validarCitacoes($texto);
            $avisos = array_merge($avisos, $validacaoCitacoes);
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado,
            'conforme' => empty($erros)
        ];
    }

    /**
     * Validar estrutura textual e linguagem
     */
    public function validarEstruturaTextual(array $dados): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];
        
        // Validar ementa
        if (!empty($dados['ementa'])) {
            $validacaoEmenta = $this->validarEmenta($dados['ementa']);
            $erros = array_merge($erros, $validacaoEmenta['erros']);
            $avisos = array_merge($avisos, $validacaoEmenta['avisos']);
            $aprovado = array_merge($aprovado, $validacaoEmenta['aprovado']);
        }

        // Validar linguagem simples
        $texto = $dados['texto'] ?? $dados['conteudo'] ?? '';
        if (!empty($texto)) {
            $validacaoLinguagem = $this->validarLinguagemSimples($texto);
            $avisos = array_merge($avisos, $validacaoLinguagem);
        }

        // Validar formatação de parágrafos e incisos
        if (!empty($texto)) {
            $validacaoFormatacao = $this->validarFormatacao($texto);
            $avisos = array_merge($avisos, $validacaoFormatacao['avisos']);
            $aprovado = array_merge($aprovado, $validacaoFormatacao['aprovado']);
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado,
            'adequada' => empty($erros)
        ];
    }

    /**
     * Validar metadados e identificadores
     */
    public function validarMetadados(array $dados, TipoProposicao $tipo): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        // Tipo de proposição válido
        if ($tipo) {
            $aprovado[] = 'Tipo de proposição válido';
        } else {
            $erros[] = 'Tipo de proposição não especificado';
        }

        // Número válido
        $numero = $dados['numero'] ?? null;
        if (empty($numero) || !is_numeric($numero) || $numero <= 0) {
            $avisos[] = 'Número da proposição deve ser especificado';
        } else {
            $aprovado[] = 'Número da proposição válido';
        }

        // Ano válido
        $ano = $dados['ano'] ?? date('Y');
        if (!is_numeric($ano) || $ano < 1990 || $ano > (date('Y') + 1)) {
            $avisos[] = 'Ano inválido ou não especificado';
        } else {
            $aprovado[] = 'Ano válido';
        }

        // Autor especificado
        if (empty($dados['autor_nome']) && empty($dados['user_id'])) {
            $avisos[] = 'Autor da proposição não especificado';
        } else {
            $aprovado[] = 'Autor especificado';
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado,
            'completos' => empty($erros)
        ];
    }

    /**
     * Validar numeração unificada
     */
    public function validarNumeracao(array $dados, TipoProposicao $tipo): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        $numero = $dados['numero'] ?? null;
        $ano = $dados['ano'] ?? date('Y');

        // Verificar se segue padrão unificado (desde 2019)
        if ($ano >= 2019) {
            $aprovado[] = 'Segue padrão unificado (pós-2019)';
            
            if (!empty($numero)) {
                if ($numero > 9999) {
                    $avisos[] = 'Número muito alto - verificar sequência';
                } else {
                    $aprovado[] = 'Número dentro do padrão';
                }
            }
        }

        // Validar formato de numeração
        if (!empty($numero)) {
            if (!is_numeric($numero)) {
                $erros[] = 'Número deve ser numérico';
            } else {
                $aprovado[] = 'Formato de número correto';
            }
        }

        // Verificar duplicação (simulado - seria verificado no banco)
        // Esta validação seria implementada no controller/service principal
        $aprovado[] = 'Verificação de duplicação pendente';

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado,
            'conforme' => empty($erros)
        ];
    }

    /**
     * Validar acessibilidade e usabilidade
     */
    public function validarAcessibilidade(array $dados): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        // Verificar linguagem simples na ementa
        if (!empty($dados['ementa'])) {
            if ($this->usaLinguagemSimples($dados['ementa'])) {
                $aprovado[] = 'Ementa usa linguagem simples';
            } else {
                $avisos[] = 'Ementa poderia usar linguagem mais simples';
            }
        }

        // Verificar estrutura clara
        $texto = $dados['texto'] ?? $dados['conteudo'] ?? '';
        if (!empty($texto)) {
            if ($this->temEstruturaClara($texto)) {
                $aprovado[] = 'Texto tem estrutura clara';
            } else {
                $avisos[] = 'Texto poderia ter estrutura mais clara';
            }
        }

        // Verificar tamanho de parágrafos
        if (!empty($texto)) {
            $validacaoParagrafos = $this->validarTamanhoParagrafos($texto);
            $avisos = array_merge($avisos, $validacaoParagrafos['avisos']);
            $aprovado = array_merge($aprovado, $validacaoParagrafos['aprovado']);
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado,
            'acessivel' => true // Sempre aprovado, apenas avisos
        ];
    }

    /**
     * Consolidar todos os resultados
     */
    private function consolidarResultados(array $resultados): array
    {
        $totalErros = 0;
        $totalAvisos = 0;
        $totalAprovado = 0;

        foreach ($resultados as $categoria => $resultado) {
            if ($categoria === 'resumo') continue;
            
            $totalErros += count($resultado['erros'] ?? []);
            $totalAvisos += count($resultado['avisos'] ?? []);
            $totalAprovado += count($resultado['aprovado'] ?? []);
        }

        // Determinar status geral
        $status = 'aprovado';
        if ($totalErros > 0) {
            $status = 'rejeitado';
        } elseif ($totalAvisos > 5) {
            $status = 'revisao_recomendada';
        }

        // Calcular pontuação de qualidade
        $pontuacaoTotal = $totalAprovado * 2 - $totalAvisos - $totalErros * 3;
        $pontuacaoMaxima = $totalAprovado * 2;
        $qualidade = $pontuacaoMaxima > 0 ? min(100, max(0, ($pontuacaoTotal / $pontuacaoMaxima) * 100)) : 0;

        return [
            'status' => $status,
            'total_erros' => $totalErros,
            'total_avisos' => $totalAvisos,
            'total_aprovado' => $totalAprovado,
            'qualidade_percentual' => round($qualidade, 1),
            'recomendacoes' => $this->gerarRecomendacoes($resultados),
            'conforme_lc95' => $resultados['lc95_1998']['conforme'] ?? false,
            'estrutura_adequada' => $resultados['estrutura_textual']['adequada'] ?? false,
            'metadados_completos' => $resultados['metadados']['completos'] ?? false,
            'numeracao_conforme' => $resultados['numeracao']['conforme'] ?? false,
            'acessivel' => $resultados['acessibilidade']['acessivel'] ?? false
        ];
    }

    // Métodos auxiliares de validação

    private function validarEmenta(string $ementa): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        // Verificar se começa com verbo no indicativo
        $verbosValidos = [
            'dispõe', 'autoriza', 'institui', 'cria', 'estabelece',
            'altera', 'revoga', 'acrescenta', 'modifica', 'fixa',
            'determina', 'concede', 'permite', 'define', 'regula',
            'disciplina', 'denomina', 'designa', 'aprova', 'declara'
        ];

        $palavraInicial = strtolower(explode(' ', trim($ementa))[0] ?? '');
        if (in_array($palavraInicial, $verbosValidos)) {
            $aprovado[] = 'Ementa inicia com verbo adequado';
        } else {
            $avisos[] = 'Ementa deveria iniciar com verbo no indicativo (dispõe, autoriza, etc.)';
        }

        // Verificar tamanho
        if (strlen($ementa) > 200) {
            $avisos[] = 'Ementa muito longa - prefira frases concisas';
        } else {
            $aprovado[] = 'Ementa tem tamanho adequado';
        }

        // Verificar pontuação final
        if (str_ends_with(trim($ementa), '.')) {
            $aprovado[] = 'Ementa termina corretamente com ponto';
        } else {
            $avisos[] = 'Ementa deve terminar com ponto';
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado
        ];
    }

    private function validarNumeracaoArtigos(string $texto): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        // Extrair artigos
        preg_match_all('/Art\.?\s*(\d+º?)/i', $texto, $matches);
        
        if (empty($matches[1])) {
            $avisos[] = 'Nenhum artigo identificado com numeração clara';
            return ['erros' => $erros, 'avisos' => $avisos, 'aprovado' => $aprovado];
        }

        $numeros = [];
        foreach ($matches[1] as $numeroStr) {
            $numero = (int)str_replace('º', '', $numeroStr);
            $numeros[] = $numero;
        }

        // Verificar sequência
        $numerosUnicos = array_unique($numeros);
        sort($numerosUnicos);

        for ($i = 0; $i < count($numerosUnicos); $i++) {
            $esperado = $i + 1;
            if ($numerosUnicos[$i] !== $esperado) {
                $avisos[] = "Numeração incorreta: encontrado Art. {$numerosUnicos[$i]}, esperado Art. {$esperado}";
            }
        }

        if (empty($avisos)) {
            $aprovado[] = 'Numeração de artigos está sequencial';
        }

        // Verificar formato ordinal até 9º
        foreach ($matches[1] as $numeroStr) {
            $numero = (int)str_replace('º', '', $numeroStr);
            $temOrdinal = str_contains($numeroStr, 'º');
            
            if ($numero <= 9 && !$temOrdinal) {
                $avisos[] = "Art. {$numero} deveria usar ordinal ({$numero}º) conforme LC 95/1998";
            } elseif ($numero <= 9 && $temOrdinal) {
                $aprovado[] = "Art. {$numero}º usa formato ordinal correto";
            }
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado
        ];
    }

    private function temClausulaVigencia(string $texto): bool
    {
        $padroes = [
            '/esta\s+lei\s+entra\s+em\s+vigor/i',
            '/entra(?:rá)?\s+em\s+vigor/i',
            '/vigência/i',
            '/vigor/i'
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto)) {
                return true;
            }
        }

        return false;
    }

    private function validarCitacoes(string $texto): array
    {
        $avisos = [];

        // Verificar citações de leis
        if (preg_match_all('/lei\s+n[ºo°]?\s*\d+/i', $texto, $matches)) {
            foreach ($matches[0] as $citacao) {
                if (!preg_match('/lei\s+n[º°]\s*\d+[,\/]\d{4}/i', $citacao)) {
                    $avisos[] = "Citação '{$citacao}' deveria incluir o ano";
                }
            }
        }

        return $avisos;
    }

    private function validarLinguagemSimples(string $texto): array
    {
        $avisos = [];

        // Verificar palavras muito técnicas ou complexas
        $palavrasComplexas = [
            'obstante', 'outrossim', 'destarte', 'ademais',
            'conquanto', 'porquanto', 'entrementes', 'noutro turno'
        ];

        foreach ($palavrasComplexas as $palavra) {
            if (stripos($texto, $palavra) !== false) {
                $avisos[] = "Considere substituir '{$palavra}' por linguagem mais simples";
            }
        }

        // Verificar frases muito longas
        $frases = preg_split('/[.!?]+/', $texto);
        foreach ($frases as $frase) {
            $palavras = str_word_count(trim($frase));
            if ($palavras > 30) {
                $avisos[] = 'Algumas frases são muito longas - considere dividi-las';
                break;
            }
        }

        return $avisos;
    }

    private function validarFormatacao(string $texto): array
    {
        $avisos = [];
        $aprovado = [];

        // Verificar parágrafos
        if (preg_match_all('/§\s*\d+º?/', $texto)) {
            $aprovado[] = 'Parágrafos formatados corretamente';
        }

        // Verificar incisos
        if (preg_match_all('/[IVX]+\s*[-–]/', $texto)) {
            $aprovado[] = 'Incisos formatados com numeração romana';
        }

        // Verificar alíneas
        if (preg_match_all('/[a-z]\)\s*/', $texto)) {
            $aprovado[] = 'Alíneas formatadas corretamente';
        }

        return [
            'avisos' => $avisos,
            'aprovado' => $aprovado
        ];
    }

    private function usaLinguagemSimples(string $texto): bool
    {
        // Critérios simples para linguagem simples
        $palavras = str_word_count($texto);
        $frases = count(preg_split('/[.!?]+/', $texto));
        
        if ($frases === 0) return true;
        
        $mediaPalavrasPorFrase = $palavras / $frases;
        
        return $mediaPalavrasPorFrase <= 20; // Máximo 20 palavras por frase
    }

    private function temEstruturaClara(string $texto): bool
    {
        // Verificar se tem estrutura com artigos, parágrafos, etc.
        return preg_match('/Art\.?\s*\d+/i', $texto) > 0;
    }

    private function validarTamanhoParagrafos(string $texto): array
    {
        $avisos = [];
        $aprovado = [];

        $paragrafos = explode("\n", $texto);
        $paragrafosLongos = 0;

        foreach ($paragrafos as $paragrafo) {
            $palavras = str_word_count(trim($paragrafo));
            if ($palavras > 100) {
                $paragrafosLongos++;
            }
        }

        if ($paragrafosLongos > 0) {
            $avisos[] = "{$paragrafosLongos} parágrafo(s) muito longo(s) - considere dividir";
        } else {
            $aprovado[] = 'Tamanho dos parágrafos adequado';
        }

        return [
            'avisos' => $avisos,
            'aprovado' => $aprovado
        ];
    }

    private function gerarRecomendacoes(array $resultados): array
    {
        $recomendacoes = [];

        // Recomendações baseadas nos erros mais comuns
        if (!empty($resultados['lc95_1998']['erros'])) {
            $recomendacoes[] = 'Revisar conformidade com LC 95/1998 - estrutura obrigatória';
        }

        if (!empty($resultados['estrutura_textual']['avisos'])) {
            $recomendacoes[] = 'Melhorar redação e estrutura do texto';
        }

        if (count($resultados['acessibilidade']['avisos'] ?? []) > 2) {
            $recomendacoes[] = 'Simplificar linguagem para melhor acessibilidade';
        }

        if (empty($recomendacoes)) {
            $recomendacoes[] = 'Proposição está bem estruturada - pronta para tramitação';
        }

        return $recomendacoes;
    }
}