<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CepController extends Controller
{
    /**
     * Busca dados de endereço por CEP
     */
    public function buscar(Request $request, string $cep): JsonResponse
    {
        // Validar o CEP que vem da URL
        if (!preg_match('/^\d{5}-?\d{3}$/', $cep)) {
            return response()->json([
                'success' => false,
                'message' => 'CEP deve conter exatamente 8 dígitos',
                'error' => 'INVALID_FORMAT'
            ], 422);
        }

        $cep = preg_replace('/\D/', '', $cep); // Remove caracteres não numéricos

        // Log::info('🔍 Busca de CEP iniciada', [
            //     'cep' => $cep,
            //     'user_id' => auth()->id(),
            //     'ip' => $request->ip()
        // ]);

        try {
            // Tentar múltiplas APIs para maior confiabilidade
            $endereco = $this->buscarViaCep($cep);
            
            if (!$endereco) {
                $endereco = $this->buscarAwesomeApi($cep);
            }
            
            if (!$endereco) {
                $endereco = $this->buscarBrasilApi($cep);
            }

            if (!$endereco) {
                // Log::warning('CEP não encontrado em nenhuma API', ['cep' => $cep]);
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado',
                    'error' => 'NOT_FOUND'
                ], 404);
            }

            // Log::info('✅ CEP encontrado com sucesso', [
                //     'cep' => $cep,
                //     'endereco' => $endereco
            // ]);

            return response()->json([
                'success' => true,
                'data' => $endereco
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro na busca de CEP', [
                //     'cep' => $cep,
                //     'error' => $e->getMessage(),
                //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno na busca do CEP',
                'error' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /**
     * Busca via ViaCEP
     */
    private function buscarViaCep(string $cep): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['erro']) && !empty($data['cep'])) {
                    return [
                        'cep' => $data['cep'],
                        'logradouro' => $data['logradouro'] ?? '',
                        'complemento' => $data['complemento'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'localidade' => $data['localidade'] ?? '',
                        'uf' => $data['uf'] ?? '',
                        'ibge' => $data['ibge'] ?? '',
                        'gia' => $data['gia'] ?? '',
                        'ddd' => $data['ddd'] ?? '',
                        'siafi' => $data['siafi'] ?? '',
                        'fonte' => 'ViaCEP'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log::warning('Erro na API ViaCEP', ['cep' => $cep, 'error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Busca via AwesomeAPI
     */
    private function buscarAwesomeApi(string $cep): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://cep.awesomeapi.com.br/json/{$cep}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['status']) || $data['status'] !== 400) {
                    return [
                        'cep' => $data['cep'] ?? $cep,
                        'logradouro' => $data['address'] ?? '',
                        'complemento' => '',
                        'bairro' => $data['district'] ?? '',
                        'localidade' => $data['city'] ?? '',
                        'uf' => $data['state'] ?? '',
                        'ibge' => $data['city_ibge'] ?? '',
                        'gia' => '',
                        'ddd' => $data['ddd'] ?? '',
                        'siafi' => '',
                        'fonte' => 'AwesomeAPI'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log::warning('Erro na API AwesomeAPI', ['cep' => $cep, 'error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Busca via BrasilAPI
     */
    private function buscarBrasilApi(string $cep): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://brasilapi.com.br/api/cep/v1/{$cep}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['message'])) { // BrasilAPI retorna message em caso de erro
                    return [
                        'cep' => $data['cep'] ?? $cep,
                        'logradouro' => $data['street'] ?? '',
                        'complemento' => '',
                        'bairro' => $data['neighborhood'] ?? '',
                        'localidade' => $data['city'] ?? '',
                        'uf' => $data['state'] ?? '',
                        'ibge' => '',
                        'gia' => '',
                        'ddd' => '',
                        'siafi' => '',
                        'fonte' => 'BrasilAPI'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log::warning('Erro na API BrasilAPI', ['cep' => $cep, 'error' => $e->getMessage()]);
        }
        
        return null;
    }
}