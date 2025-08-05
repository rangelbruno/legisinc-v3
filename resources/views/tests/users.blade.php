@extends('components.layouts.app')

@section('title', 'Testes de Usuários - Sistema Parlamentar')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Testes de Usuários
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('tests.index') }}" class="text-muted text-hover-primary">Testes</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Usuários</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('tests.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrows-left fs-4 me-1"></i>
                    Voltar aos Testes
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Row-->
            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Test Actions Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Testes de Criação de Usuários</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Gerenciar usuários de teste do sistema</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-3 mb-5">
                                <button 
                                    id="createUsersBtn" 
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="ki-duotone ki-user-plus fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Criar Usuários de Teste
                                </button>
                                
                                <button 
                                    id="listUsersBtn" 
                                    class="btn btn-success btn-sm"
                                >
                                    <i class="ki-duotone ki-people fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Listar Usuários
                                </button>
                                
                                <button 
                                    id="clearUsersBtn" 
                                    class="btn btn-danger btn-sm"
                                >
                                    <i class="ki-duotone ki-trash fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Limpar Usuários de Teste
                                </button>
                            </div>

                            <!-- Área de Resultados -->
                            <div id="results" class="mb-5"></div>
                            
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Test Actions Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Row-->
            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-8">
                    <!-- Tabela de Usuários -->
                    <div id="usersTable" class="card card-xl-stretch mb-5 mb-xl-8" style="display: none;">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Usuários de Teste no Sistema</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Lista dos usuários de teste ativos</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-50px">ID</th>
                                            <th class="min-w-150px">Nome</th>
                                            <th class="min-w-140px">Email</th>
                                            <th class="min-w-120px">Roles</th>
                                            <th class="min-w-100px">Criado em</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-4">
                    <!-- Informações dos Usuários de Teste -->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Usuários de Teste</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Configuração dos usuários</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="mb-7">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-35px me-4">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-shield-tick text-primary fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">bruno@sistema.gov.br</a>
                                        <span class="text-muted d-block fw-semibold">Administrador - senha: 123456</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-35px me-4">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-user text-info fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">jessica@sistema.gov.br</a>
                                        <span class="text-muted d-block fw-semibold">Parlamentar - senha: 123456</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-35px me-4">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-document text-success fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">joao@sistema.gov.br</a>
                                        <span class="text-muted d-block fw-semibold">Legislativo - senha: 123456</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-35px me-4">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-file-up text-warning fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">roberto@sistema.gov.br</a>
                                        <span class="text-muted d-block fw-semibold">Protocolo - senha: 123456</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-35px me-4">
                                        <div class="symbol-label bg-light-dark">
                                            <i class="ki-duotone ki-clipboard text-dark fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">expediente@sistema.gov.br</a>
                                        <span class="text-muted d-block fw-semibold">Expediente - senha: 123456</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-35px me-4">
                                        <div class="symbol-label bg-light-danger">
                                            <i class="ki-duotone ki-law text-danger fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">juridico@sistema.gov.br</a>
                                        <span class="text-muted d-block fw-semibold">Assessor Jurídico - senha: 123456</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createBtn = document.getElementById('createUsersBtn');
        const listBtn = document.getElementById('listUsersBtn');
        const clearBtn = document.getElementById('clearUsersBtn');
        const resultsDiv = document.getElementById('results');
        const usersTable = document.getElementById('usersTable');
        const usersTableBody = document.getElementById('usersTableBody');

        // Função para mostrar resultados
        function showResults(results) {
            let html = '';
            
            results.forEach(result => {
                let alertClass = 'alert-success';
                let iconClass = 'ki-check-circle text-success';
                let icon = '✓';
                
                if (result.status === 'error') {
                    alertClass = 'alert-danger';
                    iconClass = 'ki-cross-circle text-danger';
                    icon = '✗';
                } else if (result.status === 'warning') {
                    alertClass = 'alert-warning';
                    iconClass = 'ki-information text-warning';
                    icon = '⚠';
                }
                
                html += `
                    <div class="alert ${alertClass} d-flex align-items-center p-5 mb-3">
                        <i class="ki-duotone ${iconClass} fs-2hx me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">${result.email}</h4>
                            <span class="fs-7">${result.message}</span>
                        </div>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
        }

        // Função para mostrar usuários
        function showUsers(users) {
            if (users.length === 0) {
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-10">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ki-duotone ki-information-5 fs-3x text-muted mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="text-muted fw-semibold fs-6">Nenhum usuário de teste encontrado</div>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                let html = '';
                users.forEach(user => {
                    let rolesBadge = '';
                    if (user.roles && user.roles.trim() !== '') {
                        let badgeClass = 'badge-light-primary';
                        let displayName = user.roles;
                        
                        switch(user.roles.trim()) {
                            case 'ADMIN':
                                badgeClass = 'badge-light-danger';
                                displayName = 'Administrador';
                                break;
                            case 'PARLAMENTAR':
                                badgeClass = 'badge-light-info';
                                displayName = 'Parlamentar';
                                break;
                            case 'LEGISLATIVO':
                                badgeClass = 'badge-light-success';
                                displayName = 'Legislativo';
                                break;
                            case 'PROTOCOLO':
                                badgeClass = 'badge-light-warning';
                                displayName = 'Protocolo';
                                break;
                            case 'EXPEDIENTE':
                                badgeClass = 'badge-light-dark';
                                displayName = 'Expediente';
                                break;
                            case 'ASSESSOR_JURIDICO':
                                badgeClass = 'badge-light-primary';
                                displayName = 'Assessor Jurídico';
                                break;
                        }
                        rolesBadge = `<span class="badge ${badgeClass}">${displayName}</span>`;
                    } else {
                        rolesBadge = '<span class="badge badge-light-secondary">Sem role</span>';
                    }
                        
                    html += `
                        <tr>
                            <td>
                                <div class="text-dark fw-bold text-hover-primary fs-6">${user.id}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-user text-info fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start flex-column">
                                        <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">${user.name}</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-semibold d-block fs-7">${user.email}</div>
                            </td>
                            <td>
                                ${rolesBadge}
                            </td>
                            <td>
                                <div class="text-dark fw-semibold d-block fs-7">${user.created_at}</div>
                            </td>
                        </tr>
                    `;
                });
                usersTableBody.innerHTML = html;
            }
            usersTable.style.display = 'block';
        }

        // Criar usuários
        createBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';
            
            fetch('/tests/create-users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResults(data.results);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                resultsDiv.innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center p-5">
                        <i class="ki-duotone ki-cross-circle text-danger fs-2hx me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Erro</h4>
                            <span class="fs-7">Erro ao criar usuários</span>
                        </div>
                    </div>
                `;
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = `
                    <i class="ki-duotone ki-user-plus fs-4 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Criar Usuários de Teste
                `;
            });
        });

        // Listar usuários
        listBtn.addEventListener('click', function() {
            fetch('/tests/list-users')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showUsers(data.users);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        });

        // Limpar usuários
        clearBtn.addEventListener('click', function() {
            if (!confirm('Tem certeza que deseja remover todos os usuários de teste?')) {
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Removendo...';
            
            fetch('/tests/clear-users', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-success d-flex align-items-center p-5">
                            <i class="ki-duotone ki-check-circle text-success fs-2hx me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-dark fw-bold">Sucesso</h4>
                                <span class="fs-7">${data.message}</span>
                            </div>
                        </div>
                    `;
                    usersTable.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                resultsDiv.innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center p-5">
                        <i class="ki-duotone ki-cross-circle text-danger fs-2hx me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Erro</h4>
                            <span class="fs-7">Erro ao remover usuários</span>
                        </div>
                    </div>
                `;
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = `
                    <i class="ki-duotone ki-trash fs-4 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                    Limpar Usuários de Teste
                `;
            });
        });
    });
</script>
@endpush