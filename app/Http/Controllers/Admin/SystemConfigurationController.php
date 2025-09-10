<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametro\ParametroCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\DebugHelper;
use App\Services\DatabaseDebugService;

class SystemConfigurationController extends Controller
{
    /**
     * Display the system configuration page
     */
    public function index()
    {
        // Get all system configuration parameters
        $debugLogger = $this->getDebugLoggerConfig();
        
        // Use enhanced view with database debug features
        return view('admin.system-configuration.index-enhanced', compact('debugLogger'));
    }
    
    /**
     * Update system configuration
     */
    public function update(Request $request)
    {
        $request->validate([
            'debug_logger_ativo' => 'sometimes|in:on,off'
        ]);
        
        try {
            // Update debug logger setting
            // Quando checkbox está desmarcado, o campo não é enviado pelo formulário
            // Então verificamos se o campo existe (checked) ou não (unchecked)
            $debugLoggerAtivo = $request->has('debug_logger_ativo') && $request->input('debug_logger_ativo') === 'on';
            $this->updateDebugLogger($debugLoggerAtivo);
            
            $status = $debugLoggerAtivo ? 'ativado' : 'desativado';
            return redirect()->route('admin.system-configuration.index')
                ->with('success', "Debug Logger {$status} com sucesso!");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.system-configuration.index')
                ->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage());
        }
    }
    
    /**
     * Get debug logger configuration
     */
    private function getDebugLoggerConfig()
    {
        try {
            // Find the debug logger field
            $campo = ParametroCampo::where('nome', 'debug_logger_ativo')
                ->where('ativo', true)
                ->first();
                
            if (!$campo) {
                return [
                    'exists' => false,
                    'active' => false,
                    'description' => 'Parâmetro não configurado'
                ];
            }
            
            // Get current value
            $valor = DB::table('parametros_valores')
                ->where('campo_id', $campo->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $isActive = false;
            if ($valor) {
                $isActive = in_array(strtolower($valor->valor), ['true', '1', 'yes', 'on']);
            }
            
            return [
                'exists' => true,
                'active' => $isActive,
                'description' => $campo->descricao,
                'label' => $campo->label,
                'campo_id' => $campo->id
            ];
            
        } catch (\Exception $e) {
            return [
                'exists' => false,
                'active' => false,
                'description' => 'Erro ao carregar configuração: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update debug logger setting
     */
    private function updateDebugLogger(bool $active)
    {
        // Find the debug logger field
        $campo = ParametroCampo::where('nome', 'debug_logger_ativo')
            ->where('ativo', true)
            ->first();
            
        if (!$campo) {
            throw new \Exception('Campo debug_logger_ativo não encontrado');
        }
        
        // Update or create the value
        DB::table('parametros_valores')->updateOrInsert(
            ['campo_id' => $campo->id],
            [
                'valor' => $active ? 'true' : 'false',
                'tipo_valor' => 'boolean',
                'user_id' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now()
            ]
        );
        
        // Clear cache
        DebugHelper::clearCache();
    }
    
    /**
     * Test debug logger status (AJAX endpoint)
     */
    public function testDebugLogger()
    {
        $isActive = DebugHelper::isDebugLoggerActive();
        
        return response()->json([
            'active' => $isActive,
            'message' => $isActive ? 'Debug Logger está ativo' : 'Debug Logger está inativo'
        ]);
    }
    
    /**
     * Clear all system caches
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            DebugHelper::clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache do sistema limpo com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Start database query capture
     */
    public function startDatabaseCapture()
    {
        try {
            $dbDebug = new DatabaseDebugService();
            $dbDebug->startCapture();
            
            return response()->json([
                'success' => true,
                'message' => 'Captura de queries iniciada'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar captura: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Stop database query capture
     */
    public function stopDatabaseCapture()
    {
        try {
            $dbDebug = new DatabaseDebugService();
            $dbDebug->stopCapture();
            
            return response()->json([
                'success' => true,
                'message' => 'Captura de queries parada'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao parar captura: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get captured database queries
     */
    public function getDatabaseQueries()
    {
        try {
            $dbDebug = new DatabaseDebugService();
            $queries = $dbDebug->getCapturedQueries();
            $stats = $dbDebug->getQueryStatistics();
            
            return response()->json([
                'success' => true,
                'queries' => $queries,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter queries: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get database statistics
     */
    public function getDatabaseStats()
    {
        try {
            $dbDebug = new DatabaseDebugService();
            $stats = $dbDebug->getDatabaseStats();
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}