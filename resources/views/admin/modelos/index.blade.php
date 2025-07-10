@extends('components.layouts.app')

@section('title', 'Modelos de Projeto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Modelos de Projeto</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Modelos de Projeto</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title">Lista de Modelos</h4>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('modelos.editor-tiptap') }}" class="btn btn-success btn-sm">
                                <i class="ri-edit-2-line"></i> Editor Tiptap
                            </a>
                            <a href="{{ route('modelos.create') }}" class="btn btn-primary btn-sm">
                                <i class="ri-add-line"></i> Novo Modelo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filtroTipo">
                            <option value="">Todos os Tipos</option>
                            @foreach($tipos as $key => $tipo)
                                <option value="{{ $key }}" {{ request('tipo_projeto') == $key ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filtroAtivo">
                            <option value="">Todos os Status</option>
                            <option value="1" {{ request('ativo') == '1' ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="campoBusca" 
                                   placeholder="Buscar por nome ou descrição..." 
                                   value="{{ request('busca') }}">
                            <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo de Projeto</th>
                                <th>Status</th>
                                <th>Criado por</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modelos as $modelo)
                                <tr>
                                    <td>
                                        <strong>{{ $modelo->nome }}</strong>
                                        @if($modelo->descricao)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($modelo->descricao, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $modelo->tipo_projeto_formatado }}</td>
                                    <td>
                                        <span class="badge badge-{{ $modelo->ativo ? 'success' : 'danger' }}">
                                            {{ $modelo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>{{ $modelo->criadoPor->name }}</td>
                                    <td>{{ $modelo->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('modelos.show', $modelo->id) }}" 
                                               class="btn btn-sm btn-outline-info" title="Visualizar">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('modelos.edit', $modelo->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ route('modelos.toggle-status', $modelo->id) }}" 
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $modelo->ativo ? 'warning' : 'success' }}" 
                                                        title="{{ $modelo->ativo ? 'Desativar' : 'Ativar' }}">
                                                    <i class="ri-{{ $modelo->ativo ? 'pause' : 'play' }}-line"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('modelos.destroy', $modelo->id) }}" 
                                                  method="POST" style="display:inline;" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este modelo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum modelo encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $modelos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroAtivo = document.getElementById('filtroAtivo');
    const campoBusca = document.getElementById('campoBusca');
    const btnBuscar = document.getElementById('btnBuscar');

    function aplicarFiltros() {
        const params = new URLSearchParams(window.location.search);
        
        if (filtroTipo.value) {
            params.set('tipo_projeto', filtroTipo.value);
        } else {
            params.delete('tipo_projeto');
        }
        
        if (filtroAtivo.value) {
            params.set('ativo', filtroAtivo.value);
        } else {
            params.delete('ativo');
        }
        
        if (campoBusca.value) {
            params.set('busca', campoBusca.value);
        } else {
            params.delete('busca');
        }
        
        window.location.search = params.toString();
    }

    filtroTipo.addEventListener('change', aplicarFiltros);
    filtroAtivo.addEventListener('change', aplicarFiltros);
    btnBuscar.addEventListener('click', aplicarFiltros);
    
    campoBusca.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            aplicarFiltros();
        }
    });
});
</script>
@endsection