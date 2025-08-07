<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CamaraApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CamaraInfoController extends Controller
{
    private CamaraApiService $camaraApiService;

    public function __construct(CamaraApiService $camaraApiService)
    {
        $this->camaraApiService = $camaraApiService;
    }

    /**
     * Buscar informações de uma câmara municipal pelo nome
     */
    public function buscarPorNome(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|min:2'
        ]);

        $nome = $request->input('nome');
        
        try {
            \Log::info('🔍 Iniciando busca por câmara', ['nome' => $nome]);
            
            // Usar o novo serviço que integra com APIs externas
            $dadosCamara = $this->camaraApiService->buscarCamaraPorCidade($nome);
            
            if (empty($dadosCamara['nome_camara'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma câmara encontrada com esse nome',
                    'sugestoes' => $this->obterSugestoes($nome)
                ]);
            }
            
            // Adicionar informações de fonte dos dados
            $fonte = $dadosCamara['fonte'] ?? 'api_externa';
            $mensagem = $this->obterMensagemPorFonte($fonte);
            
            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'camaras' => [$dadosCamara], // Retornar como array para compatibilidade
                'fonte' => $fonte,
                'aviso' => $dadosCamara['aviso'] ?? 'Dados obtidos de APIs externas. Sempre verificar a precisão antes de salvar.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar câmara', [
                'nome' => $nome,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Tente novamente'
            ], 500);
        }
    }

    /**
     * Endpoint para verificar status das APIs
     */
    public function verificarStatusApis(): JsonResponse
    {
        try {
            $status = $this->camaraApiService->verificarStatusApis();
            
            return response()->json([
                'success' => true,
                'status_apis' => $status,
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status das APIs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para limpar cache de uma cidade
     */
    public function limparCache(Request $request): JsonResponse
    {
        $request->validate([
            'cidade' => 'required|string|min:2'
        ]);

        try {
            $cidade = $request->input('cidade');
            $this->camaraApiService->limparCache($cidade);
            
            return response()->json([
                'success' => true,
                'message' => "Cache da cidade '{$cidade}' limpo com sucesso"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter mensagem baseada na fonte dos dados
     */
    private function obterMensagemPorFonte(string $fonte): string
    {
        return match($fonte) {
            'ibge' => 'Dados obtidos do IBGE - Informações oficiais do município',
            'viacep' => 'Dados obtidos via ViaCEP - Informações de endereçamento',
            'dadosgovbr' => 'Dados obtidos do Portal Brasileiro de Dados Abertos',
            'apilib_sp' => 'Dados obtidos da APILIB da Prefeitura de São Paulo',
            'generico' => 'Dados genéricos gerados - Necessária verificação completa',
            default => 'Dados obtidos de APIs externas - Verificar precisão antes de salvar'
        };
    }
    
    /**
     * Database básico de câmaras municipais (dados genéricos para modelo)
     * IMPORTANTE: Usuário deve sempre verificar e ajustar os dados específicos
     */
    private function obterDatabaseCamaras(): array
    {
        return [
            // Template básico para capitais e cidades principais
            [
                'nome_camara' => 'Câmara Municipal de São Paulo',
                'sigla_camara' => 'CMSP',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'endereco' => '', // Usuário deve preencher
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.saopaulo.sp.leg.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 55
            ],
            [
                'nome_camara' => 'Câmara Municipal do Rio de Janeiro',
                'sigla_camara' => 'CMRJ',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.camara.rj.gov.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 51
            ],
            [
                'nome_camara' => 'Câmara Municipal de Belo Horizonte',
                'sigla_camara' => 'CMBH',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.cmbh.mg.gov.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 41
            ],
            [
                'nome_camara' => 'Câmara Municipal de Salvador',
                'sigla_camara' => 'CMS',
                'cidade' => 'Salvador',
                'estado' => 'BA',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.cms.ba.gov.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 43
            ],
            [
                'nome_camara' => 'Câmara Municipal de Curitiba',
                'sigla_camara' => 'CMC',
                'cidade' => 'Curitiba',
                'estado' => 'PR',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.cmc.pr.gov.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 38
            ],
            [
                'nome_camara' => 'Câmara Municipal de Caraguatatuba',
                'sigla_camara' => 'CMC',
                'cidade' => 'Caraguatatuba',
                'estado' => 'SP',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => '',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 13
            ],
            [
                'nome_camara' => 'Câmara Municipal de Santos',
                'sigla_camara' => 'CMS',
                'cidade' => 'Santos',
                'estado' => 'SP',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.santos.sp.leg.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 21
            ],
            [
                'nome_camara' => 'Câmara Municipal de Campinas',
                'sigla_camara' => 'CMC',
                'cidade' => 'Campinas',
                'estado' => 'SP',
                'endereco' => '',
                'numero' => '',
                'bairro' => 'Centro',
                'cep' => '',
                'telefone' => '',
                'email_institucional' => '',
                'website' => 'https://www.campinas.sp.leg.br',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'numero_vereadores' => 33
            ]
        ];
    }
    
    /**
     * Obter sugestões quando não encontrar resultados
     */
    private function obterSugestoes(string $nome): array
    {
        return [
            'Verifique se digitou o nome da cidade corretamente',
            'Tente usar apenas o nome da cidade (ex: "São Paulo")',
            'Use o nome completo se conhecer (ex: "Câmara Municipal de São Paulo")',
            'Nem todas as câmaras municipais estão cadastradas no sistema',
            'Para cidades menores, você pode preencher os dados manualmente'
        ];
    }
}

// Função auxiliar para remover acentos
if (!function_exists('removeAccents')) {
    function removeAccents($string)
    {
        $accents = [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C'
        ];
        
        return strtr($string, $accents);
    }
}