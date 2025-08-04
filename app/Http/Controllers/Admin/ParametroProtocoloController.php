<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use App\Services\NumeroProcessoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParametroProtocoloController extends Controller
{
    private NumeroProcessoService $numeroProcessoService;

    public function __construct(NumeroProcessoService $numeroProcessoService)
    {
        $this->numeroProcessoService = $numeroProcessoService;
    }

    /**
     * Exibir tela de configuração de parâmetros do protocolo
     */
    public function index()
    {
        // Buscar parâmetros do protocolo
        $parametros = Parametro::where('codigo', 'like', 'protocolo.%')
            ->with(['grupo', 'tipo'])
            ->orderBy('ordem')
            ->get()
            ->keyBy('codigo');
        
        // Obter configurações atuais
        $configuracoes = $this->numeroProcessoService->obterConfiguracoes();
        
        // Prever próximos números
        $proximosNumeros = $this->numeroProcessoService->preverProximosNumeros();
        
        return view('admin.parametros.protocolo', compact('parametros', 'configuracoes', 'proximosNumeros'));
    }

    /**
     * Atualizar parâmetros do protocolo
     */
    public function update(Request $request)
    {
        $request->validate([
            'parametros' => 'required|array',
            'parametros.protocolo.formato_numero_processo' => 'required|string',
            'parametros.protocolo.digitos_sequencial' => 'required|in:3,4,5,6',
            'parametros.protocolo.reiniciar_sequencial_anualmente' => 'required|boolean',
            'parametros.protocolo.sequencial_por_tipo' => 'required|boolean',
            'parametros.protocolo.permitir_numero_manual' => 'required|boolean',
            'parametros.protocolo.prefixo_processo' => 'nullable|string|max:10',
            'parametros.protocolo.sufixo_processo' => 'nullable|string|max:10',
            'parametros.protocolo.inserir_numero_documento' => 'required|boolean',
            'parametros.protocolo.posicao_numero_documento' => 'required|in:cabecalho,rodape,primeira_pagina,marca_dagua,nao_inserir'
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($request->parametros as $codigo => $valor) {
                // Converter valores booleanos
                if (in_array($codigo, [
                    'protocolo.reiniciar_sequencial_anualmente',
                    'protocolo.sequencial_por_tipo',
                    'protocolo.permitir_numero_manual',
                    'protocolo.inserir_numero_documento'
                ])) {
                    $valor = $valor ? '1' : '0';
                }
                
                Parametro::where('codigo', $codigo)->update(['valor' => $valor]);
                
                Log::info('Parâmetro atualizado', [
                    'codigo' => $codigo,
                    'valor' => $valor,
                    'usuario_id' => auth()->id()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.parametros.protocolo')
                ->with('success', 'Parâmetros de protocolo atualizados com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao atualizar parâmetros de protocolo', [
                'error' => $e->getMessage(),
                'usuario_id' => auth()->id()
            ]);
            
            return redirect()->route('admin.parametros.protocolo')
                ->with('error', 'Erro ao atualizar parâmetros: ' . $e->getMessage());
        }
    }

    /**
     * Testar formato de numeração
     */
    public function testarFormato(Request $request)
    {
        $request->validate([
            'formato' => 'required|string',
            'digitos' => 'required|in:3,4,5,6',
            'prefixo' => 'nullable|string|max:10',
            'sufixo' => 'nullable|string|max:10'
        ]);

        try {
            // Simular geração de número
            $sequencial = str_pad('1', $request->digitos, '0', STR_PAD_LEFT);
            
            $numero = str_replace(
                ['{TIPO}', '{ANO}', '{SEQUENCIAL}', '{MES}', '{DIA}'],
                [
                    'PL',
                    date('Y'),
                    $sequencial,
                    date('m'),
                    date('d')
                ],
                $request->formato
            );
            
            $numeroCompleto = ($request->prefixo ?? '') . $numero . ($request->sufixo ?? '');
            
            return response()->json([
                'success' => true,
                'exemplo' => $numeroCompleto,
                'formato_valido' => true
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Formato inválido',
                'erro' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Restaurar valores padrão
     */
    public function restaurarPadroes()
    {
        DB::beginTransaction();
        
        try {
            $parametros = Parametro::where('codigo', 'like', 'protocolo.%')->get();
            
            foreach ($parametros as $parametro) {
                $parametro->update(['valor' => $parametro->valor_padrao]);
            }
            
            DB::commit();
            
            Log::info('Parâmetros de protocolo restaurados para valores padrão', [
                'usuario_id' => auth()->id()
            ]);
            
            return redirect()->route('admin.parametros.protocolo')
                ->with('success', 'Parâmetros restaurados para valores padrão!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.parametros.protocolo')
                ->with('error', 'Erro ao restaurar parâmetros: ' . $e->getMessage());
        }
    }
}