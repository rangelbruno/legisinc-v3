<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Node.js API - User Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="userApiManager()">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">🚀 Node.js API</h1>
                    <span class="ml-4 px-3 py-1 text-sm rounded-full"
                          :class="authStatus.authenticated ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        <span x-text="authStatus.authenticated ? '✅ Autenticado' : '❌ Não Autenticado'"></span>
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="checkHealth()" 
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                        🔍 Health Check
                    </button>
                    <button @click="refreshAuthStatus()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                        🔄 Refresh
                    </button>
                    <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            🚪 Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alert Messages -->
        <div x-show="alert.show" x-cloak class="mb-6 fade-in">
            <div class="rounded-md p-4" 
                 :class="alert.type === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium" 
                           :class="alert.type === 'success' ? 'text-green-800' : 'text-red-800'"
                           x-text="alert.message"></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="alert.show = false" 
                                class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">Fechar</span>
                            ×
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Info -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">📡 API Information</h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Base URL</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $config['base_url'] ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Provider</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $config['provider_name'] ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Timeout</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $config['timeout'] ?? 'N/A' }}s</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Authentication Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Login Form -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">🔑 Login</h3>
                </div>
                <div class="px-6 py-4">
                    <form @submit.prevent="login()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="loginForm.email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="bruno@test.com">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                            <input type="password" x-model="loginForm.password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="senha123">
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit" :disabled="loading"
                                    class="flex-1 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 disabled:opacity-50 transition-colors">
                                <span x-show="!loading">🔑 Login</span>
                                <span x-show="loading" x-cloak>⏳ Carregando...</span>
                            </button>
                            <button type="button" @click="autoLogin()" :disabled="loading"
                                    class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 disabled:opacity-50 transition-colors">
                                🤖 Auto
                            </button>
                            <button type="button" @click="logout()" :disabled="loading"
                                    class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 disabled:opacity-50 transition-colors">
                                🚪 Logout
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Register Form -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">👤 Registrar Usuário</h3>
                </div>
                <div class="px-6 py-4">
                    <form @submit.prevent="register()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                            <input type="text" x-model="registerForm.name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="João Silva">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="registerForm.email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="joao@test.com">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                            <input type="password" x-model="registerForm.password" required minlength="6"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="senha123">
                        </div>
                        <button type="submit" :disabled="loading"
                                class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 disabled:opacity-50 transition-colors">
                            <span x-show="!loading">👤 Registrar</span>
                            <span x-show="loading" x-cloak>⏳ Registrando...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users Management -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">👥 Gerenciamento de Usuários</h3>
                <div class="flex space-x-3">
                    <button @click="loadUsers()" :disabled="loading"
                            class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 disabled:opacity-50 transition-colors">
                        🔄 Recarregar
                    </button>
                    <button @click="showCreateModal = true"
                            class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition-colors">
                        ➕ Criar Usuário
                    </button>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.id"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.name"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.email"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button @click="editUser(user)"
                                            class="text-blue-600 hover:text-blue-900">✏️ Editar</button>
                                    <button @click="deleteUser(user.id)"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Confirma a exclusão?')">🗑️ Deletar</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="users.length === 0 && !loading">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <span x-show="!authStatus.authenticated">🔒 Faça login para ver os usuários</span>
                                <span x-show="authStatus.authenticated">📭 Nenhum usuário encontrado</span>
                            </td>
                        </tr>
                        <tr x-show="loading">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                ⏳ Carregando usuários...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div x-show="showCreateModal || showEditModal" x-cloak 
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
             @click.self="closeModals()">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" 
                        x-text="showCreateModal ? '➕ Criar Usuário' : '✏️ Editar Usuário'"></h3>
                    
                    <form @submit.prevent="showCreateModal ? createUser() : updateUser()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                            <input type="text" x-model="userForm.name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="userForm.email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModals()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="loading"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 transition-colors">
                                <span x-show="!loading" x-text="showCreateModal ? 'Criar' : 'Atualizar'"></span>
                                <span x-show="loading" x-cloak>⏳ Salvando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function userApiManager() {
            return {
                // State
                loading: false,
                users: @json($users ?? []),
                authStatus: @json($authStatus ?? ['authenticated' => false]),
                
                // Forms
                loginForm: {
                    email: '{{ $config['default_email'] ?? 'bruno@test.com' }}',
                    password: '{{ $config['default_password'] ?? 'senha123' }}'
                },
                registerForm: {
                    name: '',
                    email: '',
                    password: ''
                },
                userForm: {
                    id: null,
                    name: '',
                    email: ''
                },
                
                // Modals
                showCreateModal: false,
                showEditModal: false,
                
                // Alert
                alert: {
                    show: false,
                    type: 'success',
                    message: ''
                },

                init() {
                    // Check for server-side error
                    @if(isset($error))
                        this.showAlert('error', 'Erro: {{ $error }}');
                    @endif
                    
                    // Auto-load users if authenticated
                    if (this.authStatus.authenticated) {
                        this.loadUsers();
                    }
                },

                // Utility methods
                async makeRequest(url, options = {}) {
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                ...options.headers
                            },
                            ...options
                        });
                        
                        const data = await response.json();
                        return data;
                    } catch (error) {
                        this.showAlert('error', 'Erro de conexão: ' + error.message);
                        throw error;
                    }
                },

                showAlert(type, message) {
                    this.alert = { show: true, type, message };
                    setTimeout(() => { this.alert.show = false; }, 5000);
                },

                // Authentication methods
                async login() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/login', {
                            method: 'POST',
                            body: JSON.stringify(this.loginForm)
                        });

                        if (data.success) {
                            this.authStatus = data.auth_status;
                            this.showAlert('success', data.message);
                            this.loadUsers();
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async autoLogin() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/auto-login', {
                            method: 'POST'
                        });

                        if (data.success) {
                            this.authStatus = data.auth_status;
                            this.showAlert('success', data.message);
                            this.loadUsers();
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async logout() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/logout', {
                            method: 'POST'
                        });

                        this.authStatus = data.auth_status;
                        this.users = [];
                        this.showAlert('success', data.message);
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async register() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/register', {
                            method: 'POST',
                            body: JSON.stringify(this.registerForm)
                        });

                        if (data.success) {
                            this.showAlert('success', data.message);
                            this.registerForm = { name: '', email: '', password: '' };
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                // User management methods
                async loadUsers() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/users');

                        if (data.success) {
                            this.users = data.data;
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async createUser() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/users', {
                            method: 'POST',
                            body: JSON.stringify(this.userForm)
                        });

                        if (data.success) {
                            this.showAlert('success', data.message);
                            this.closeModals();
                            this.loadUsers();
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async updateUser() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest(`/node-api/users/${this.userForm.id}`, {
                            method: 'PUT',
                            body: JSON.stringify(this.userForm)
                        });

                        if (data.success) {
                            this.showAlert('success', data.message);
                            this.closeModals();
                            this.loadUsers();
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteUser(id) {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest(`/node-api/users/${id}`, {
                            method: 'DELETE'
                        });

                        if (data.success) {
                            this.showAlert('success', data.message);
                            this.loadUsers();
                        } else {
                            this.showAlert('error', data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                // Modal methods
                editUser(user) {
                    this.userForm = { ...user };
                    this.showEditModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.userForm = { id: null, name: '', email: '' };
                },

                // Health check
                async checkHealth() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/health-check');
                        
                        if (data.success) {
                            const status = data.overall_status ? 'Saudável' : 'Com problemas';
                            this.showAlert('success', `API Status: ${status}`);
                        } else {
                            this.showAlert('error', 'Health check falhou: ' + data.error);
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                },

                async refreshAuthStatus() {
                    this.loading = true;
                    try {
                        const data = await this.makeRequest('/node-api/auth-status');
                        
                        if (data.success) {
                            this.authStatus = data.auth_status;
                            this.showAlert('success', 'Status atualizado');
                        } else {
                            this.showAlert('error', 'Falha ao atualizar status');
                        }
                    } catch (error) {
                        // Error already handled in makeRequest
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html> 