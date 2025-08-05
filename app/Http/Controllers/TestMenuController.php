<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScreenPermission;
use Illuminate\Support\Facades\Auth;

class TestMenuController extends Controller
{
    public function testExpedienteMenu()
    {
        if (!Auth::check()) {
            abort(403, 'Usuário não logado');
        }
        
        $user = Auth::user();
        
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ],
            'permissions' => [
                'proposicoes_module' => ScreenPermission::userCanAccessModule('proposicoes'),
                'expediente_module' => ScreenPermission::userCanAccessModule('expediente'),
                'expediente_index_route' => ScreenPermission::userCanAccessRoute('expediente.index'),
                'show_expediente_submenu' => ScreenPermission::userCanAccessModule('expediente') || ScreenPermission::userCanAccessRoute('expediente.index')
            ],
            'routes' => [
                'expediente.index' => ScreenPermission::userCanAccessRoute('expediente.index'),
                'proposicoes.legislativo.index' => ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'),
                'expediente.aguardando-pauta' => ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'),
                'expediente.relatorio' => ScreenPermission::userCanAccessRoute('expediente.relatorio'),
            ]
        ];
        
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
}