@extends('components.layouts.app')

@section('title', 'Administração do Banco de Dados')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-database fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Administração do Banco de Dados
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Administração</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Banco de Dados</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(isset($error))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-information fs-2hx text-danger me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark">Erro de Conectividade</h4>
                    <span>{{ $error }}</span>
                    <small class="text-muted mt-1">Verifique se o banco de dados está acessível e as configurações estão corretas.</small>
                </div>
            </div>
            @endif

            @if(count($tables) > 0)
            <!--begin::Filter Section-->
            <div class="card mb-5">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center flex-wrap">
                        <h4 class="text-gray-800 fw-bold me-5 mb-2 mb-md-0">Filtrar por categoria:</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-light-primary filter-btn active" data-filter="all">
                                Todas ({{ count($tables) }})
                            </button>
                            @php
                                $categories = collect($tables)->groupBy('category');
                            @endphp
                            @foreach($categories as $category => $categoryTables)
                            <button class="btn btn-sm btn-light-secondary filter-btn" data-filter="{{ strtolower(str_replace(' ', '-', $category)) }}">
                                {{ $category }} ({{ count($categoryTables) }})
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Filter Section-->

            <!--begin::Row-->
            <div class="row g-5 g-xl-8" id="tables-grid">
                @foreach($tables as $index => $table)
                <div class="col-xl-4 col-lg-6 table-card" data-category="{{ strtolower(str_replace(' ', '-', $table['category'])) }}">
                    <!--begin::Card-->
                    <div class="card card-flush h-100">
                        <div class="card-header pt-5">
                            <!--begin::Title-->
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone {{ $table['icon'] }} fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        @if(in_array($table['icon'], ['ki-shield-tick', 'ki-information-2']))
                                        <span class="path3"></span>
                                        @endif
                                        @if(in_array($table['icon'], ['ki-calendar-8']))
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        @endif
                                    </i>
                                    <span class="text-gray-800 fw-bold fs-4">{{ $table['name'] }}</span>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    @php
                                        $badgeColors = [
                                            'Usuários' => 'primary',
                                            'Parlamentares' => 'success', 
                                            'Proposições' => 'info',
                                            'Sessões' => 'warning',
                                            'Documentos' => 'secondary',
                                            'Configuração' => 'dark',
                                            'Segurança' => 'danger',
                                            'Inteligência Artificial' => 'primary',
                                            'Sistema' => 'secondary'
                                        ];
                                        $color = $badgeColors[$table['category']] ?? 'light';
                                    @endphp
                                    <span class="badge badge-light-{{ $color }} me-2">
                                        {{ $table['category'] }}
                                    </span>
                                </div>
                            </div>
                            <!--end::Title-->
                        </div>
                        <div class="card-body pt-5">
                            <div class="text-center">
                                <p class="text-gray-600 fs-6 mb-5" style="min-height: 48px;">
                                    {{ $table['description'] }}
                                </p>
                                <a href="{{ route('admin.database.table', $table['name']) }}" 
                                   class="btn btn-light-primary btn-sm">
                                    <i class="ki-duotone ki-eye fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Visualizar Dados
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                @endforeach
            </div>
            <!--end::Row-->

            @elseif(!isset($error))
            <!--begin::Empty state-->
            <div class="card">
                <div class="card-body text-center py-20">
                    <i class="ki-duotone ki-database fs-5x text-gray-300 mb-10">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3 class="text-gray-800 fs-2 fw-bold mb-3">Nenhuma tabela encontrada</h3>
                    <p class="text-gray-600 fs-6 fw-semibold mb-0">Não foi possível encontrar tabelas no banco de dados.</p>
                </div>
            </div>
            <!--end::Empty state-->
            @endif

            <!--begin::Security Notice-->
            <div class="alert alert-warning d-flex align-items-center p-5 mt-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-warning me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark">Aviso de Segurança</h4>
                    <span>Esta funcionalidade permite visualização completa dos dados do sistema. Use com responsabilidade e apenas para fins administrativos.</span>
                </div>
            </div>
            <!--end::Security Notice-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const tableCards = document.querySelectorAll('.table-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('btn-light-primary');
                btn.classList.add('btn-light-secondary');
            });
            
            this.classList.add('active');
            this.classList.remove('btn-light-secondary');
            this.classList.add('btn-light-primary');
            
            // Filter cards
            tableCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                    card.style.opacity = '0';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transition = 'opacity 0.3s ease';
                    }, 50);
                } else {
                    card.style.transition = 'opacity 0.2s ease';
                    card.style.opacity = '0';
                    
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 200);
                }
            });
        });
    });
});
</script>
@endpush