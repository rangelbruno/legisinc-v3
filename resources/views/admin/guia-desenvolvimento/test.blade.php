@extends('components.layouts.app')

@section('title', 'Teste Guia de Desenvolvimento')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Teste Guia de Desenvolvimento
                </h1>
            </div>
        </div>
    </div>
    
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teste Funcionando</h3>
                </div>
                <div class="card-body">
                    <p>Se você está vendo esta mensagem, o problema está no conteúdo complexo da view original.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection