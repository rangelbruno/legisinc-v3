@extends('components.layouts.app')

@section('title', 'Criar Modelo de Projeto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Criar Modelo de Projeto</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modelos.index') }}">Modelos</a></li>
                    <li class="breadcrumb-item active">Criar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Selecionar Tipo de Projeto</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($tipos as $key => $tipo)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 card-hover">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="ri-file-text-line fs-1 text-primary"></i>
                                    </div>
                                    <h5 class="card-title">{{ $tipo }}</h5>
                                    <p class="card-text text-muted">
                                        Criar modelo para {{ strtolower($tipo) }}
                                    </p>
                                    <a href="{{ route('modelos.editor', ['tipo' => $key]) }}" class="btn btn-primary">
                                        <i class="ri-edit-line"></i> Criar Modelo
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.card-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>
@endsection