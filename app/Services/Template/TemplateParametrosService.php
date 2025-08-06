<?php

namespace App\Services\Template;

use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroValor;
use App\Models\Proposicao;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TemplateParametrosService
{
    /**
     * Obter todos os parâmetros do módulo Templates
     */
    public function obterParametrosTemplates(): array
    {
        return Cache::remember('parametros.templates', 3600, function () {
            $modulo = ParametroModulo::where('nome', 'Templates')
                ->with(['submodulos.campos.valores'])
                ->first();
            
            if (!$modulo) {
                return $this->getParametrosPadrao();
            }

            $parametros = [];
            
            foreach ($modulo->submodulos as $submodulo) {
                foreach ($submodulo->campos as $campo) {
                    $chave = $submodulo->nome . '.' . $campo->nome;
                    // Usar o valor mais recente válido
                    $valorAtual = $campo->valores()->whereNull('valido_ate')->orWhere('valido_ate', '>', now())->latest()->first();
                    $parametros[$chave] = $valorAtual ? $valorAtual->valor_formatado : $campo->valor_padrao;
                }
            }

            return $parametros;
        });
    }

    /**
     * Obter variáveis disponíveis para substituição
     */
    public function obterVariaveisDisponiveis(): array
    {
        return [
            // Dados da Proposição
            '${numero_proposicao}' => 'Número da proposição',
            '${tipo_proposicao}' => 'Tipo da proposição',
            '${ementa}' => 'Ementa da proposição',
            '${texto}' => 'Texto principal',
            '${justificativa}' => 'Justificativa',
            '${ano}' => 'Ano da proposição',
            '${protocolo}' => 'Número do protocolo',
            
            // Dados do Autor
            '${autor_nome}' => 'Nome do autor',
            '${autor_cargo}' => 'Cargo do autor',
            '${autor_partido}' => 'Partido do autor',
            
            // Datas
            '${data_atual}' => 'Data atual',
            '${data_criacao}' => 'Data de criação',
            '${data_protocolo}' => 'Data do protocolo',
            '${dia}' => 'Dia atual',
            '${mes}' => 'Mês atual',
            '${ano_atual}' => 'Ano atual',
            '${mes_extenso}' => 'Mês por extenso',
            
            // Dados da Câmara (dos parâmetros)
            '${nome_camara}' => 'Nome da Câmara',
            '${municipio}' => 'Nome do município',
            '${endereco_camara}' => 'Endereço da Câmara',
            '${telefone_camara}' => 'Telefone da Câmara',
            '${website_camara}' => 'Website da Câmara',
            
            // Formatação
            '${assinatura_padrao}' => 'Área de assinatura',
            '${rodape}' => 'Texto do rodapé'
        ];
    }

    /**
     * Processar template com substituição de variáveis
     */
    public function processarTemplate(string $conteudo, array $dados = []): string
    {
        // Obter parâmetros do sistema
        $parametros = $this->obterParametrosTemplates();
        
        // Preparar variáveis de substituição
        $variaveis = $this->prepararVariaveis($dados, $parametros);
        
        // Realizar substituições
        foreach ($variaveis as $chave => $valor) {
            $conteudo = str_replace($chave, $valor, $conteudo);
        }
        
        return $conteudo;
    }

    /**
     * Preparar variáveis para substituição
     */
    private function prepararVariaveis(array $dados, array $parametros): array
    {
        $variaveis = [];
        
        // Dados da proposição
        if (isset($dados['proposicao']) && $dados['proposicao'] instanceof Proposicao) {
            $proposicao = $dados['proposicao'];
            $variaveis['${numero_proposicao}'] = $proposicao->numero ?? '';
            $variaveis['${tipo_proposicao}'] = $proposicao->tipo ?? '';
            $variaveis['${ementa}'] = $proposicao->ementa ?? '';
            $variaveis['${texto}'] = $proposicao->conteudo ?? '';
            $variaveis['${justificativa}'] = $proposicao->justificativa ?? '';
            $variaveis['${ano}'] = $proposicao->ano ?? date('Y');
            $variaveis['${protocolo}'] = $proposicao->protocolo ?? '';
            $variaveis['${data_criacao}'] = $proposicao->created_at ? $proposicao->created_at->format('d/m/Y') : '';
            $variaveis['${data_protocolo}'] = $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y') : '';
        }
        
        // Dados do autor
        if (isset($dados['autor']) && $dados['autor'] instanceof User) {
            $autor = $dados['autor'];
            $variaveis['${autor_nome}'] = $autor->name ?? '';
            $variaveis['${autor_cargo}'] = $autor->cargo ?? 'Vereador(a)';
            $variaveis['${autor_partido}'] = $autor->partido ?? '';
        }
        
        // Datas
        $dataAtual = now();
        $variaveis['${data_atual}'] = $dataAtual->format($parametros['Formatação.format_formato_data'] ?? 'd/m/Y');
        $variaveis['${dia}'] = $dataAtual->format('d');
        $variaveis['${mes}'] = $dataAtual->format('m');
        $variaveis['${ano_atual}'] = $dataAtual->format('Y');
        $variaveis['${mes_extenso}'] = $this->mesExtenso($dataAtual->format('n'));
        
        // Dados da Câmara (dos parâmetros)
        $variaveis['${nome_camara}'] = $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL';
        $variaveis['${municipio}'] = $this->extrairMunicipio($parametros['Cabeçalho.cabecalho_nome_camara'] ?? '');
        $variaveis['${endereco_camara}'] = $parametros['Cabeçalho.cabecalho_endereco'] ?? '';
        $variaveis['${telefone_camara}'] = $parametros['Cabeçalho.cabecalho_telefone'] ?? '';
        $variaveis['${website_camara}'] = $parametros['Cabeçalho.cabecalho_website'] ?? '';
        
        // Formatação
        $variaveis['${assinatura_padrao}'] = $parametros['Variáveis Dinâmicas.var_assinatura_padrao'] ?? '';
        $variaveis['${rodape}'] = $parametros['Rodapé.rodape_texto'] ?? '';
        
        // Adicionar variáveis customizadas passadas diretamente
        if (isset($dados['variaveis'])) {
            foreach ($dados['variaveis'] as $chave => $valor) {
                $variaveis['${' . $chave . '}'] = $valor;
            }
        }
        
        return $variaveis;
    }

    /**
     * Extrair município do nome da câmara
     */
    private function extrairMunicipio(string $nomeCamara): string
    {
        // Remove "CÂMARA MUNICIPAL DE " para obter o município
        $patterns = [
            '/^CÂMARA MUNICIPAL DE /i',
            '/^CÂMARA DE /i',
            '/^CÂMARA /i'
        ];
        
        foreach ($patterns as $pattern) {
            $municipio = preg_replace($pattern, '', $nomeCamara);
            if ($municipio !== $nomeCamara) {
                return $municipio;
            }
        }
        
        return '';
    }

    /**
     * Converter mês para extenso
     */
    private function mesExtenso(int $mes): string
    {
        $meses = [
            1 => 'janeiro',
            2 => 'fevereiro',
            3 => 'março',
            4 => 'abril',
            5 => 'maio',
            6 => 'junho',
            7 => 'julho',
            8 => 'agosto',
            9 => 'setembro',
            10 => 'outubro',
            11 => 'novembro',
            12 => 'dezembro'
        ];
        
        return $meses[$mes] ?? '';
    }

    /**
     * Obter parâmetros padrão caso não existam no banco
     */
    private function getParametrosPadrao(): array
    {
        return [
            'Cabeçalho.cabecalho_nome_camara' => 'CÂMARA MUNICIPAL',
            'Cabeçalho.cabecalho_endereco' => '',
            'Cabeçalho.cabecalho_telefone' => '',
            'Cabeçalho.cabecalho_website' => '',
            'Rodapé.rodape_texto' => 'Documento oficial',
            'Rodapé.rodape_numeracao' => true,
            'Variáveis Dinâmicas.var_prefixo_numeracao' => 'PROP',
            'Variáveis Dinâmicas.var_formato_data' => 'd/m/Y',
            'Variáveis Dinâmicas.var_assinatura_padrao' => "Sala das Sessões, em _____ de _____________ de _______.\n\n\n_________________________________\nVereador(a)",
            'Formatação.format_fonte' => 'Arial',
            'Formatação.format_tamanho_fonte' => '12',
            'Formatação.format_espacamento' => '1.5',
            'Formatação.format_margens' => '2.5, 2.5, 3, 2'
        ];
    }

    /**
     * Limpar cache de parâmetros
     */
    public function limparCache(): void
    {
        Cache::forget('parametros.templates');
    }
}