<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-users {--clear : Clear existing test users first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test users for system testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            $this->clearTestUsers();
        }

        $this->createRequiredRoles();
        $this->info('Creating test users...');

        $testUsers = [
            [
                'email' => 'bruno@sistema.gov.br',
                'name' => 'Bruno Administrador',
                'password' => '123456',
                'role' => User::PERFIL_ADMIN
            ],
            [
                'email' => 'jessica@sistema.gov.br',
                'name' => 'Jessica Parlamentar',
                'password' => '123456', 
                'role' => User::PERFIL_PARLAMENTAR
            ],
            [
                'email' => 'joao@sistema.gov.br',
                'name' => 'João Legislativo',
                'password' => '123456',
                'role' => User::PERFIL_LEGISLATIVO
            ],
            [
                'email' => 'roberto@sistema.gov.br',
                'name' => 'Roberto Protocolo',
                'password' => '123456',
                'role' => User::PERFIL_PROTOCOLO
            ],
            [
                'email' => 'expediente@sistema.gov.br',
                'name' => 'Carlos Expediente',
                'password' => '123456',
                'role' => User::PERFIL_EXPEDIENTE
            ],
            [
                'email' => 'juridico@sistema.gov.br',
                'name' => 'Ana Assessora Jurídica',
                'password' => '123456',
                'role' => User::PERFIL_ASSESSOR_JURIDICO
            ]
        ];

        $successCount = 0;
        $errorCount = 0;

        foreach ($testUsers as $userData) {
            try {
                // Verifica se usuário já existe
                $existingUser = User::where('email', $userData['email'])->first();
                
                if ($existingUser) {
                    $this->warn("✗ {$userData['email']} - User already exists");
                    $errorCount++;
                    continue;
                }

                // Cria o usuário
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'ativo' => true
                ]);

                // Verifica se o role existe
                $role = Role::where('name', $userData['role'])->first();
                
                if (!$role) {
                    $this->warn("⚠ {$userData['email']} - User created but role '{$userData['role']}' not found");
                } else {
                    // Atribui o role
                    $user->assignRole($role);
                    $this->info("✓ {$userData['email']} - Created with role '{$userData['role']}'");
                }

                $successCount++;

            } catch (\Exception $e) {
                $this->error("✗ {$userData['email']} - Error: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("✓ Successfully created: $successCount users");
        if ($errorCount > 0) {
            $this->warn("✗ Errors: $errorCount users");
        }

        return Command::SUCCESS;
    }

    private function clearTestUsers()
    {
        $this->info('Clearing existing test users...');
        
        $testEmails = [
            'bruno@sistema.gov.br',
            'jessica@sistema.gov.br',
            'joao@sistema.gov.br',
            'roberto@sistema.gov.br',
            'expediente@sistema.gov.br',
            'juridico@sistema.gov.br'
        ];

        $deletedCount = User::whereIn('email', $testEmails)->delete();
        $this->info("Removed $deletedCount test users");
        $this->newLine();
    }

    private function createRequiredRoles()
    {
        $this->info('Checking required roles...');
        
        $requiredRoles = [
            User::PERFIL_ADMIN,
            User::PERFIL_PARLAMENTAR, 
            User::PERFIL_LEGISLATIVO,
            User::PERFIL_PROTOCOLO,
            User::PERFIL_EXPEDIENTE,
            User::PERFIL_ASSESSOR_JURIDICO
        ];
        
        foreach ($requiredRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                Role::create([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);
                $this->info("✓ Created role '$roleName'");
            } else {
                $this->info("Role '$roleName' already exists");
            }
        }
        
        $this->newLine();
    }
}
