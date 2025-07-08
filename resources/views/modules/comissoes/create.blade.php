@extends('components.layouts.app')

@section('title', $title ?? 'Nova Comissão')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Nova Comissão
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('comissoes.index') }}" class="text-muted text-hover-primary">Comissões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Nova Comissão</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif

            <!--begin::Form-->
            <form action="{{ route('comissoes.store') }}" method="POST" class="form d-flex flex-column flex-lg-row">
                @csrf
                
                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <!--begin::General options-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações Básicas</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Nome da Comissão</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nome" class="form-control mb-2" placeholder="Nome da comissão" value="{{ old('nome') }}" required />
                                <!--end::Input-->
                                @error('nome')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Tipo</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="tipo" class="form-select mb-2" required>
                                        <option value="">Selecione o tipo</option>
                                        @foreach($tipos as $value => $label)
                                            <option value="{{ $value }}" {{ old('tipo') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('tipo')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Status</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="status" class="form-select mb-2" required>
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', 'ativa') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('status')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Descrição</label>
                                <!--end::Label-->
                                <!--begin::Textarea-->
                                <textarea name="descricao" class="form-control mb-2" rows="4" placeholder="Descrição da comissão">{{ old('descricao') }}</textarea>
                                <!--end::Textarea-->
                                @error('descricao')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Finalidade</label>
                                <!--end::Label-->
                                <!--begin::Textarea-->
                                <textarea name="finalidade" class="form-control mb-2" rows="3" placeholder="Finalidade e objetivos da comissão" required>{{ old('finalidade') }}</textarea>
                                <!--end::Textarea-->
                                @error('finalidade')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::General options-->
                    
                    <!--begin::Mesa diretora-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Mesa Diretora</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="row mb-10">
                                <div class="col-md-4">
                                    <!--begin::Label-->
                                    <label class="form-label">Presidente</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="presidente_id" class="form-select mb-2">
                                        <option value="">Selecione o presidente</option>
                                        @foreach($parlamentares as $id => $nome)
                                            <option value="{{ $id }}" {{ old('presidente_id') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('presidente_id')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <!--begin::Label-->
                                    <label class="form-label">Vice-Presidente</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="vice_presidente_id" class="form-select mb-2">
                                        <option value="">Selecione o vice-presidente</option>
                                        @foreach($parlamentares as $id => $nome)
                                            <option value="{{ $id }}" {{ old('vice_presidente_id') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('vice_presidente_id')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <!--begin::Label-->
                                    <label class="form-label">Relator</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="relator_id" class="form-select mb-2">
                                        <option value="">Selecione o relator</option>
                                        @foreach($parlamentares as $id => $nome)
                                            <option value="{{ $id }}" {{ old('relator_id') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('relator_id')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Membros</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="membros" class="form-control mb-2" placeholder="Digite os nomes dos membros separados por vírgula" value="{{ old('membros') }}" />
                                <!--end::Input-->
                                <!--begin::Hint-->
                                <div class="text-muted fs-7">Digite os nomes dos membros separados por vírgula</div>
                                <!--end::Hint-->
                                @error('membros')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Mesa diretora-->
                    
                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="{{ route('comissoes.index') }}" class="btn btn-light me-5">Cancelar</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar Comissão</span>
                        </button>
                        <!--end::Button-->
                    </div>
                </div>
                <!--end::Main column-->
            </form>
            <!--end::Form-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection