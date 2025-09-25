<?php

namespace App\Services;

use App\Services\Parametro\ParametroService;
use Illuminate\Support\Facades\Log;

/**
 * Service para obter identificadores únicos da câmara
 * Usado para evitar conflitos em caminhos S3 quando bancos são resetados
 */
class CamaraIdentifierService
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Obter identificador único da câmara baseado nos dados institucionais
     * Este identificador permanece o mesmo mesmo após reset do banco
     *
     * @return string
     */
    public function getUniqueIdentifier(): string
    {
        try {
            // Tentar obter dados da câmara
            $siglaCamara = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'sigla_camara') ?? '';
            $nomeCamara = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'nome_camara') ?? 'camara';
            $cnpj = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'cnpj') ?? '';
            $cidade = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'cidade') ?? '';

            // Se temos CNPJ, usar os primeiros 8 dígitos (sem formatação)
            if (!empty($cnpj)) {
                $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);
                if (strlen($cnpjLimpo) >= 8) {
                    $identifier = substr($cnpjLimpo, 0, 8);

                    Log::info('CamaraIdentifier: Usando CNPJ como identificador', [
                        'cnpj_original' => $cnpj,
                        'cnpj_limpo' => $cnpjLimpo,
                        'identifier' => $identifier
                    ]);

                    return $identifier;
                }
            }

            // Fallback: usar hash dos dados disponíveis (priorizar sigla)
            $dadosCombinados = !empty($siglaCamara)
                ? strtolower(trim($siglaCamara . $cidade))
                : strtolower(trim($nomeCamara . $cidade));

            $dadosCombinados = preg_replace('/[^a-z0-9]/', '', $dadosCombinados);

            if (empty($dadosCombinados)) {
                // Último fallback: usar nome do app ou valor padrão
                $dadosCombinados = config('app.name', 'legisinc');
            }

            // Gerar hash curto (8 caracteres) para identificação única
            $identifier = substr(md5($dadosCombinados), 0, 8);

            Log::info('CamaraIdentifier: Usando hash de dados como identificador', [
                'sigla_camara' => $siglaCamara,
                'nome_camara' => $nomeCamara,
                'cidade' => $cidade,
                'dados_combinados' => $dadosCombinados,
                'identifier' => $identifier,
                'usando_sigla' => !empty($siglaCamara)
            ]);

            return $identifier;

        } catch (\Exception $e) {
            Log::warning('CamaraIdentifier: Erro ao obter identificador, usando fallback', [
                'error' => $e->getMessage()
            ]);

            // Fallback de emergência
            return 'default';
        }
    }

    /**
     * Obter nome limpo da câmara para usar em caminhos (prioriza sigla)
     *
     * @return string
     */
    public function getSlugName(): string
    {
        try {
            $siglaCamara = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'sigla_camara') ?? '';
            $nomeCamara = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'nome_camara') ?? 'camara-municipal';

            // Priorizar sigla se disponível (geralmente mais limpa e curta)
            $textoBase = !empty($siglaCamara) ? $siglaCamara : $nomeCamara;

            // Limpar e converter para slug
            $slug = strtolower(trim($textoBase));
            $slug = preg_replace('/[^a-z0-9\s]/', '', $slug); // Remove caracteres especiais
            $slug = preg_replace('/\s+/', '-', $slug); // Substitui espaços por hífens
            $slug = trim($slug, '-'); // Remove hífens das extremidades

            // Para siglas, manter mais compacto; para nomes completos, limitar mais
            $maxLength = !empty($siglaCamara) ? 15 : 30;
            if (strlen($slug) > $maxLength) {
                $slug = substr($slug, 0, $maxLength);
                $slug = trim($slug, '-');
            }

            // Se ficou vazio, usar fallback
            if (empty($slug)) {
                $slug = !empty($siglaCamara) ? strtolower($siglaCamara) : 'camara-municipal';
            }

            return $slug;

        } catch (\Exception $e) {
            Log::warning('CamaraIdentifier: Erro ao obter slug, usando fallback', [
                'error' => $e->getMessage()
            ]);

            return 'camara-municipal';
        }
    }

    /**
     * Obter identificador completo para usar como pasta raiz no S3
     * Formato: {slug}_{identifier}
     *
     * @return string
     */
    public function getFullIdentifier(): string
    {
        $slug = $this->getSlugName();
        $identifier = $this->getUniqueIdentifier();

        $fullIdentifier = $slug . '_' . $identifier;

        Log::debug('CamaraIdentifier: Identificador completo gerado', [
            'slug' => $slug,
            'identifier' => $identifier,
            'full_identifier' => $fullIdentifier
        ]);

        return $fullIdentifier;
    }
}