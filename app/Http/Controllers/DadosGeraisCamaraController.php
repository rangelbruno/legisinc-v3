<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DadosGeraisCamaraController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração dos dados gerais da câmara
     */
    public function index(): View
    {
        \Log::info('📋 DadosGeraisCamaraController::index chamado', [
            'user' => auth()->user()->email ?? 'não autenticado',
            'timestamp' => now()
        ]);
        
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.dados-gerais-camara', compact('configuracoes'));
    }

    /**
     * Salvar configurações dos dados gerais
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nome_camara' => 'required|string|max:255',
            'sigla_camara' => 'required|string|max:20',
            'cnpj' => 'required|string|size:18',
            'endereco' => 'required|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:9',
            'telefone' => 'required|string|max:15',
            'telefone_secundario' => 'nullable|string|max:15',
            'email_institucional' => 'required|email|max:255',
            'email_contato' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'horario_funcionamento' => 'required|string|max:100',
            'horario_atendimento' => 'required|string|max:100',
            'presidente_nome' => 'required|string|max:255',
            'presidente_partido' => 'required|string|max:50',
            'legislatura_atual' => 'required|string|max:50',
            'numero_vereadores' => 'required|integer|min:5|max:55'
        ]);

        try {
            // Salvar cada configuração - Dados Básicos
            $this->parametroService->salvarValor('Dados Gerais', 'Identificação', 'nome_camara', $request->input('nome_camara'));
            $this->parametroService->salvarValor('Dados Gerais', 'Identificação', 'sigla_camara', $request->input('sigla_camara'));
            $this->parametroService->salvarValor('Dados Gerais', 'Identificação', 'cnpj', $request->input('cnpj'));
            
            // Endereço
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'endereco', $request->input('endereco'));
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'numero', $request->input('numero'));
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'complemento', $request->input('complemento'));
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'bairro', $request->input('bairro'));
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'cidade', $request->input('cidade'));
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'estado', $request->input('estado'));
            $this->parametroService->salvarValor('Dados Gerais', 'Endereço', 'cep', $request->input('cep'));
            
            // Contatos
            $this->parametroService->salvarValor('Dados Gerais', 'Contatos', 'telefone', $request->input('telefone'));
            $this->parametroService->salvarValor('Dados Gerais', 'Contatos', 'telefone_secundario', $request->input('telefone_secundario'));
            $this->parametroService->salvarValor('Dados Gerais', 'Contatos', 'email_institucional', $request->input('email_institucional'));
            $this->parametroService->salvarValor('Dados Gerais', 'Contatos', 'email_contato', $request->input('email_contato'));
            $this->parametroService->salvarValor('Dados Gerais', 'Contatos', 'website', $request->input('website'));
            
            // Funcionamento
            $this->parametroService->salvarValor('Dados Gerais', 'Funcionamento', 'horario_funcionamento', $request->input('horario_funcionamento'));
            $this->parametroService->salvarValor('Dados Gerais', 'Funcionamento', 'horario_atendimento', $request->input('horario_atendimento'));
            
            // Gestão
            $this->parametroService->salvarValor('Dados Gerais', 'Gestão', 'presidente_nome', $request->input('presidente_nome'));
            $this->parametroService->salvarValor('Dados Gerais', 'Gestão', 'presidente_partido', $request->input('presidente_partido'));
            $this->parametroService->salvarValor('Dados Gerais', 'Gestão', 'legislatura_atual', $request->input('legislatura_atual'));
            $this->parametroService->salvarValor('Dados Gerais', 'Gestão', 'numero_vereadores', $request->input('numero_vereadores'));

            return response()->json([
                'success' => true,
                'message' => 'Dados gerais da câmara salvos com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar dados gerais da câmara', [
                'error' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter configurações atuais
     */
    private function obterConfiguracoes(): array
    {
        try {
            return [
                // Identificação
                'nome_camara' => $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'nome_camara') ?: 'Câmara Municipal',
                'sigla_camara' => $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'sigla_camara') ?: 'CM',
                'cnpj' => $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'cnpj') ?: '',
                
                // Endereço
                'endereco' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'endereco') ?: '',
                'numero' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'numero') ?: '',
                'complemento' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'complemento') ?: '',
                'bairro' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'bairro') ?: '',
                'cidade' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'cidade') ?: '',
                'estado' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'estado') ?: 'SP',
                'cep' => $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'cep') ?: '',
                
                // Contatos
                'telefone' => $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'telefone') ?: '',
                'telefone_secundario' => $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'telefone_secundario') ?: '',
                'email_institucional' => $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'email_institucional') ?: '',
                'email_contato' => $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'email_contato') ?: '',
                'website' => $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'website') ?: '',
                
                // Funcionamento
                'horario_funcionamento' => $this->parametroService->obterValor('Dados Gerais', 'Funcionamento', 'horario_funcionamento') ?: 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => $this->parametroService->obterValor('Dados Gerais', 'Funcionamento', 'horario_atendimento') ?: 'Segunda a Sexta, 8h às 16h',
                
                // Gestão
                'presidente_nome' => $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'presidente_nome') ?: '',
                'presidente_partido' => $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'presidente_partido') ?: '',
                'legislatura_atual' => $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'legislatura_atual') ?: '2021-2024',
                'numero_vereadores' => $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'numero_vereadores') ?: 9
            ];
        } catch (\Exception $e) {
            \Log::warning('Erro ao obter dados gerais da câmara, usando padrões', [
                'error' => $e->getMessage()
            ]);
            // Se houver erro, usar valores padrão
            return [
                'nome_camara' => 'Câmara Municipal',
                'sigla_camara' => 'CM',
                'cnpj' => '',
                'endereco' => '',
                'numero' => '',
                'complemento' => '',
                'bairro' => '',
                'cidade' => '',
                'estado' => 'SP',
                'cep' => '',
                'telefone' => '',
                'telefone_secundario' => '',
                'email_institucional' => '',
                'email_contato' => '',
                'website' => '',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'presidente_nome' => '',
                'presidente_partido' => '',
                'legislatura_atual' => '2021-2024',
                'numero_vereadores' => 9
            ];
        }
    }
}