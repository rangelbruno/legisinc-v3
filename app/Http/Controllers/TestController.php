<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestController extends Controller
{
    public function index()
    {
        return view('tests.index');
    }

    public function createTestUsers()
    {
        $results = [];
        
        $testUsers = [
            [
                'email' => 'bruno@sistema.gov.br',
                'name' => 'Bruno Administrador',
                'password' => '13ligado',
                'role' => 'Administrador'
            ],
            [
                'email' => 'jessica@sistema.gov.br',
                'name' => 'Jessica Parlamentar',
                'password' => '13ligado', 
                'role' => 'Parlamentar'
            ],
            [
                'email' => 'joao@sistema.gov.br',
                'name' => 'João Legislativo',
                'password' => '13ligado',
                'role' => 'Legislativo'
            ],
            [
                'email' => 'roberto@sistema.gov.br',
                'name' => 'Roberto Protocolo',
                'password' => '13ligado',
                'role' => 'Protocolo'
            ]
        ];

        foreach ($testUsers as $userData) {
            try {
                // Verifica se usuário já existe
                $existingUser = User::where('email', $userData['email'])->first();
                
                if ($existingUser) {
                    $results[] = [
                        'email' => $userData['email'],
                        'status' => 'error',
                        'message' => 'Usuário já existe no sistema'
                    ];
                    continue;
                }

                // Cria o usuário
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now()
                ]);

                // Verifica se o role existe
                $role = Role::where('name', $userData['role'])->first();
                
                if (!$role) {
                    $results[] = [
                        'email' => $userData['email'],
                        'status' => 'warning',
                        'message' => 'Usuário criado, mas role "' . $userData['role'] . '" não existe'
                    ];
                } else {
                    // Atribui o role
                    $user->assignRole($role);
                    
                    $results[] = [
                        'email' => $userData['email'],
                        'status' => 'success',
                        'message' => 'Usuário criado com sucesso com role "' . $userData['role'] . '"'
                    ];
                }

            } catch (\Exception $e) {
                $results[] = [
                    'email' => $userData['email'],
                    'status' => 'error',
                    'message' => 'Erro ao criar usuário: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    public function clearTestUsers()
    {
        $testEmails = [
            'bruno@sistema.gov.br',
            'jessica@sistema.gov.br', 
            'joao@sistema.gov.br',
            'roberto@sistema.gov.br'
        ];

        $deletedCount = User::whereIn('email', $testEmails)->delete();

        return response()->json([
            'success' => true,
            'message' => "Removidos {$deletedCount} usuários de teste"
        ]);
    }

    public function listTestUsers()
    {
        $testEmails = [
            'bruno@sistema.gov.br',
            'jessica@sistema.gov.br',
            'joao@sistema.gov.br', 
            'roberto@sistema.gov.br'
        ];

        $users = User::whereIn('email', $testEmails)
            ->with('roles')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->join(', '),
                    'created_at' => $user->created_at->format('d/m/Y H:i:s')
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}