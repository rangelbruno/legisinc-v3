@extends('components.layouts.app')

@section('title', 'Visualizar Modelo de Projeto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Visualizar Modelo de Projeto</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modelos.index') }}">Modelos</a></li>
                    <li class="breadcrumb-item active">Visualizar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $modelo->nome }}</h4>
                <div class="card-tools">
                    <span class="badge badge-{{ $modelo->ativo ? 'success' : 'danger' }}">
                        {{ $modelo->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Tipo de Projeto:</strong></div>
                    <div class="col-sm-9">{{ $modelo->tipo_projeto_formatado }}</div>
                </div>
                
                @if($modelo->descricao)
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Descrição:</strong></div>
                        <div class="col-sm-9">{{ $modelo->descricao }}</div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Criado por:</strong></div>
                    <div class="col-sm-9">{{ $modelo->criadoPor->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Criado em:</strong></div>
                    <div class="col-sm-9">{{ $modelo->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Última atualização:</strong></div>
                    <div class="col-sm-9">{{ $modelo->updated_at->format('d/m/Y H:i') }}</div>
                </div>

                <hr>

                <h5>Conteúdo do Modelo</h5>
                <div class="bg-light p-3 border rounded">
                    <pre class="mb-0">{{ $modelo->conteudo_modelo }}</pre>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ações</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('modelos.edit', $modelo->id) }}" class="btn btn-primary">
                        <i class="ri-edit-line"></i> Editar Modelo
                    </a>
                    
                    <form action="{{ route('modelos.toggle-status', $modelo->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $modelo->ativo ? 'warning' : 'success' }} w-100">
                            <i class="ri-{{ $modelo->ativo ? 'pause' : 'play' }}-line"></i>
                            {{ $modelo->ativo ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('modelos.destroy', $modelo->id) }}" method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir este modelo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="ri-delete-bin-line"></i> Excluir Modelo
                        </button>
                    </form>
                    
                    <a href="{{ route('modelos.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        @if($modelo->campos_variaveis && count($modelo->campos_variaveis) > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">Campos Variáveis</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Variável</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modelo->campos_variaveis as $campo)
                                    <tr>
                                        <td><code>@{{{{ $campo['nome'] }}}}</code></td>
                                        <td>{{ $campo['descricao'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Variáveis do Sistema</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Variável</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>@{{DATA_HOJE}}</code></td>
                                <td>Data atual (dd/mm/yyyy)</td>
                            </tr>
                            <tr>
                                <td><code>@{{ANO_ATUAL}}</code></td>
                                <td>Ano atual</td>
                            </tr>
                            <tr>
                                <td><code>@{{MES_ATUAL}}</code></td>
                                <td>Mês atual por extenso</td>
                            </tr>
                            <tr>
                                <td><code>@{{DIA_ATUAL}}</code></td>
                                <td>Dia atual</td>
                            </tr>
                            <tr>
                                <td><code>@{{NOME_AUTOR}}</code></td>
                                <td>Nome do autor do projeto</td>
                            </tr>
                            <tr>
                                <td><code>@{{NUMERO_PROJETO}}</code></td>
                                <td>Número do projeto</td>
                            </tr>
                            <tr>
                                <td><code>@{{ANO_PROJETO}}</code></td>
                                <td>Ano do projeto</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Preview do Modelo</h4>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="gerarPreview()">
                    <i class="ri-eye-line"></i> Ver Preview
                </button>
                <div id="preview-container" class="mt-3" style="display:none;">
                    <div class="bg-light p-3 border rounded">
                        <pre id="preview-content"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function gerarPreview() {
    const container = document.getElementById('preview-container');
    const content = document.getElementById('preview-content');
    
    fetch(`{{ route('modelos.conteudo', $modelo->id) }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.textContent = data.conteudo;
                container.style.display = 'block';
            } else {
                alert('Erro ao gerar preview: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao gerar preview');
        });
}
</script>
@endsection