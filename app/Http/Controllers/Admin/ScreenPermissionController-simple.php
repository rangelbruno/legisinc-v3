<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ScreenPermissionController extends Controller
{
    /**
     * Exibir tela principal de gerenciamento de permissões
     */
    public function index(): View
    {
        try {
            // Dados estáticos temporários para evitar travamentos
            $roles = [
                'ADMIN' => 'Administrador',
                'PARLAMENTAR' => 'Parlamentar', 
                'LEGISLATIVO' => 'Legislativo',
                'PROTOCOLO' => 'Protocolo'
            ];
            
            $modules = [
                'dashboard' => 'Dashboard',
                'proposicoes' => 'Proposições',
                'parlamentares' => 'Parlamentares',
                'usuarios' => 'Usuários'
            ];
            
            $permissionMatrix = [];
            $statistics = [
                'total_permissions' => 0,
                'active_permissions' => 0,
                'coverage_percentage' => 0
            ];
            $cacheStats = [
                'hits' => 0,
                'misses' => 0,
                'hit_ratio' => 0,
                'total' => 0
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao carregar tela de permissões: ' . $e->getMessage());
            
            // Dados vazios em caso de erro
            $roles = [];
            $modules = [];
            $permissionMatrix = [];
            $statistics = ['total_permissions' => 0, 'active_permissions' => 0, 'coverage_percentage' => 0];
            $cacheStats = ['hits' => 0, 'misses' => 0, 'hit_ratio' => 0, 'total' => 0];
        }
        
        return view('admin.screen-permissions.index-simple', compact(
            'roles',
            'modules', 
            'permissionMatrix',
            'statistics',
            'cacheStats'
        ));
    }

    /**
     * Atualizar permissões - TEMPORARIAMENTE DESABILITADO
     */
    public function update(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Função temporariamente desabilitada para evitar travamentos. Use o Laravel/Spatie diretamente.'
        ]);
    }

    /**
     * Buscar permissões de uma role - TEMPORARIAMENTE DESABILITADO
     */
    public function getRolePermissions(string $role): JsonResponse
    {
        return response()->json([
            'success' => false,
            'permissions' => [],
            'message' => 'Função temporariamente desabilitada.'
        ]);
    }

    /**
     * Reset permissões - TEMPORARIAMENTE DESABILITADO
     */
    public function reset(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Função temporariamente desabilitada.'
        ]);
    }

    /**
     * Sincronizar permissões - TEMPORARIAMENTE DESABILITADO
     */
    public function sync(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Função temporariamente desabilitada.'
        ]);
    }

    /**
     * Exportar permissões - TEMPORARIAMENTE DESABILITADO
     */
    public function export(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Função temporariamente desabilitada.'
        ]);
    }

    /**
     * Importar permissões - TEMPORARIAMENTE DESABILITADO
     */
    public function import(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Função temporariamente desabilitada.'
        ]);
    }

    /**
     * Estatísticas do cache - TEMPORARIAMENTE DESABILITADO
     */
    public function cacheStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'stats' => [
                'hits' => 0,
                'misses' => 0,
                'hit_ratio' => 0,
                'total' => 0
            ]
        ]);
    }

    /**
     * Limpar cache - TEMPORARIAMENTE DESABILITADO
     */
    public function clearCache(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Execute: php artisan cache:clear'
        ]);
    }

    /**
     * Aquecer cache - TEMPORARIAMENTE DESABILITADO
     */
    public function warmCache(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Execute: php artisan config:cache'
        ]);
    }

    /**
     * Inicializar permissões - TEMPORARIAMENTE DESABILITADO
     */
    public function initialize(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Execute: php artisan db:seed --class=ProposicaoPermissionsSeeder'
        ]);
    }
}