@extends('components.layouts.app')

@section('title', $title ?? 'Novo Parlamentar')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Novo Parlamentar
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parlamentares.index') }}" class="text-muted text-hover-primary">Parlamentares</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Novo Parlamentar</li>
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
            <form action="{{ route('parlamentares.store') }}" method="POST" class="form d-flex flex-column flex-lg-row">
                @csrf
                
                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <!--begin::General options-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Dados Pessoais</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Nome Completo</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nome" class="form-control mb-2" placeholder="Nome completo do parlamentar" value="{{ old('nome') }}" required />
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
                                    <label class="required form-label">Email</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="email" class="form-control mb-2" placeholder="email@camara.gov.br" value="{{ old('email') }}" required />
                                    <!--end::Input-->
                                    @error('email')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Telefone</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="telefone" class="form-control mb-2" placeholder="(11) 99999-9999" value="{{ old('telefone') }}" required />
                                    <!--end::Input-->
                                    @error('telefone')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Data de Nascimento</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="date" name="data_nascimento" class="form-control mb-2" value="{{ old('data_nascimento') }}" required />
                                    <!--end::Input-->
                                    @error('data_nascimento')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Profissão</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="profissao" class="form-control mb-2" placeholder="Ex: Advogado, Professor, Médico" value="{{ old('profissao') }}" required />
                                    <!--end::Input-->
                                    @error('profissao')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Escolaridade</label>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select name="escolaridade" class="form-select mb-2" required>
                                    <option value="">Selecione a escolaridade</option>
                                    @foreach($escolaridadeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('escolaridade') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <!--end::Select-->
                                @error('escolaridade')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::General options-->
                    
                    <!--begin::Parliamentary info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações Parlamentares</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Partido</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="partido" class="form-select mb-2" required>
                                        <option value="">Selecione o partido</option>
                                        @foreach($partidos as $sigla => $nome)
                                            <option value="{{ $sigla }}" {{ old('partido') == $sigla ? 'selected' : '' }}>{{ $sigla }} - {{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('partido')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required form-label">Cargo</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="cargo" class="form-select mb-2" required>
                                        <option value="">Selecione o cargo</option>
                                        @foreach($cargos as $value => $label)
                                            <option value="{{ $value }}" {{ old('cargo') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Select-->
                                    @error('cargo')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Comissões</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="comissoes" class="form-control mb-2" placeholder="Ex: Educação, Saúde, Finanças (separadas por vírgula)" value="{{ old('comissoes') }}" />
                                <!--end::Input-->
                                <!--begin::Hint-->
                                <div class="text-muted fs-7">Digite as comissões separadas por vírgula</div>
                                <!--end::Hint-->
                                @error('comissoes')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Parliamentary info-->
                    
                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="{{ route('parlamentares.index') }}" class="btn btn-light me-5">Cancelar</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar Parlamentar</span>
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