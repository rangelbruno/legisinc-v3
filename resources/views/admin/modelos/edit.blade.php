@extends('components.layouts.app')

@section('title', 'Editar Modelo de Projeto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Editar Modelo de Projeto</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modelos.index') }}">Modelos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Informações do Modelo</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('modelos.update', $modelo->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Modelo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" name="nome" value="{{ old('nome', $modelo->nome) }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_projeto" class="form-label">Tipo de Projeto <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo_projeto') is-invalid @enderror" 
                                        id="tipo_projeto" name="tipo_projeto" required>
                                    <option value="">Selecione o tipo</option>
                                    @foreach($tipos as $key => $tipo)
                                        <option value="{{ $key }}" {{ old('tipo_projeto', $modelo->tipo_projeto) == $key ? 'selected' : '' }}>
                                            {{ $tipo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_projeto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" name="descricao" rows="3" 
                                          placeholder="Descrição opcional do modelo">{{ old('descricao', $modelo->descricao) }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="ativo" class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="ativo" name="ativo" value="1" 
                                           {{ old('ativo', $modelo->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="conteudo_modelo" class="form-label">Conteúdo do Modelo <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('conteudo_modelo') is-invalid @enderror" 
                                  id="conteudo_modelo" name="conteudo_modelo" rows="15" 
                                  placeholder="Digite o conteúdo do modelo aqui. Use @{{VARIAVEL}} para campos que serão substituídos." 
                                  required>{{ old('conteudo_modelo', $modelo->conteudo_modelo) }}</textarea>
                        @error('conteudo_modelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <strong>Variáveis disponíveis:</strong>
                            <ul class="list-unstyled mt-2">
                                <li><code>@{{DATA_HOJE}}</code> - Data atual (dd/mm/yyyy)</li>
                                <li><code>@{{ANO_ATUAL}}</code> - Ano atual</li>
                                <li><code>@{{MES_ATUAL}}</code> - Mês atual por extenso</li>
                                <li><code>@{{DIA_ATUAL}}</code> - Dia atual</li>
                                <li><code>@{{NOME_AUTOR}}</code> - Nome do autor do projeto</li>
                                <li><code>@{{NUMERO_PROJETO}}</code> - Número do projeto</li>
                                <li><code>@{{ANO_PROJETO}}</code> - Ano do projeto</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Campos Variáveis Personalizados</label>
                        <div id="campos-variaveis">
                            @if(old('campos_variaveis') || $modelo->campos_variaveis)
                                @foreach(old('campos_variaveis', $modelo->campos_variaveis ?? []) as $index => $campo)
                                    <div class="row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" 
                                                   name="campos_variaveis[{{ $index }}][nome]" 
                                                   placeholder="Nome da variável (ex: CIDADE)"
                                                   value="{{ $campo['nome'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" 
                                                   name="campos_variaveis[{{ $index }}][descricao]" 
                                                   placeholder="Descrição da variável"
                                                   value="{{ $campo['descricao'] ?? '' }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removerCampoVariavel(this)">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" 
                                               name="campos_variaveis[0][nome]" 
                                               placeholder="Nome da variável (ex: CIDADE)">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" 
                                               name="campos_variaveis[0][descricao]" 
                                               placeholder="Descrição da variável">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removerCampoVariavel(this)">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarCampoVariavel()">
                            <i class="ri-add-line"></i> Adicionar Campo Variável
                        </button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('modelos.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i> Atualizar Modelo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let contadorCampos = {{ count(old('campos_variaveis', $modelo->campos_variaveis ?? [])) }};

function adicionarCampoVariavel() {
    const container = document.getElementById('campos-variaveis');
    const novoDiv = document.createElement('div');
    novoDiv.className = 'row mb-2';
    novoDiv.innerHTML = `
        <div class="col-md-5">
            <input type="text" class="form-control" 
                   name="campos_variaveis[${contadorCampos}][nome]" 
                   placeholder="Nome da variável (ex: CIDADE)">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" 
                   name="campos_variaveis[${contadorCampos}][descricao]" 
                   placeholder="Descrição da variável">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="removerCampoVariavel(this)">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;
    container.appendChild(novoDiv);
    contadorCampos++;
}

function removerCampoVariavel(button) {
    const container = document.getElementById('campos-variaveis');
    if (container.children.length > 1) {
        button.closest('.row').remove();
    }
}
</script>
@endsection