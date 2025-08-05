@extends('components.layouts.app')

@section('title', 'Emitir Parecer Jurídico')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Emitir Parecer Jurídico
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parecer-juridico.index') }}" class="text-muted text-hover-primary">Parecer Jurídico</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Emitir Parecer</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    
                    <!--begin::Proposição Card-->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">Dados da Proposição</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo:</label>
                                        <span class="fs-6">{{ $proposicao->tipo }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Número:</label>
                                        <span class="fs-6">{{ $proposicao->numero ?? 'Não informado' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Protocolo:</label>
                                        <span class="fs-6">{{ $proposicao->numero_protocolo ?? 'Não protocolado' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Autor:</label>
                                        <span class="fs-6">{{ $proposicao->autor->name ?? 'Não informado' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data Protocolo:</label>
                                        <span class="fs-6">
                                            {{ $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y H:i') : 'Não informado' }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <span class="badge badge-light-info">{{ $proposicao->status }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ementa:</label>
                                        <p class="fs-6 text-gray-800">{{ $proposicao->ementa }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Proposição Card-->

                    <!--begin::Parecer Card-->
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">Parecer Jurídico</h3>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('parecer-juridico.store', $proposicao) }}">
                            @csrf
                            <div class="card-body">
                                
                                <!--begin::Tipo de Parecer-->
                                <div class="mb-10">
                                    <label class="form-label required fw-semibold fs-6 mb-2">Tipo de Parecer</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="radio" name="tipo_parecer" 
                                                       value="FAVORAVEL" id="favoravel" 
                                                       {{ old('tipo_parecer') == 'FAVORAVEL' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="favoravel">
                                                    <span class="badge badge-success">Favorável</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="radio" name="tipo_parecer" 
                                                       value="CONTRARIO" id="contrario"
                                                       {{ old('tipo_parecer') == 'CONTRARIO' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="contrario">
                                                    <span class="badge badge-danger">Contrário</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="radio" name="tipo_parecer" 
                                                       value="COM_EMENDAS" id="com_emendas"
                                                       {{ old('tipo_parecer') == 'COM_EMENDAS' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="com_emendas">
                                                    <span class="badge badge-warning">Favorável com Emendas</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('tipo_parecer')
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div class="fv-help-block">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <!--end::Tipo de Parecer-->

                                <!--begin::Fundamentação-->
                                <div class="mb-10">
                                    <label class="form-label required fw-semibold fs-6 mb-2">Fundamentação Legal</label>
                                    <textarea name="fundamentacao" class="form-control form-control-solid @error('fundamentacao') is-invalid @enderror" 
                                              rows="8" placeholder="Descreva a fundamentação legal do parecer, incluindo base normativa e análise jurídica...">{{ old('fundamentacao') }}</textarea>
                                    <div class="form-text">Mínimo de 50 caracteres</div>
                                    @error('fundamentacao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Fundamentação-->

                                <!--begin::Conclusão-->
                                <div class="mb-10">
                                    <label class="form-label required fw-semibold fs-6 mb-2">Conclusão</label>
                                    <textarea name="conclusao" class="form-control form-control-solid @error('conclusao') is-invalid @enderror" 
                                              rows="4" placeholder="Conclusão do parecer jurídico...">{{ old('conclusao') }}</textarea>
                                    <div class="form-text">Mínimo de 20 caracteres</div>
                                    @error('conclusao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Conclusão-->

                                <!--begin::Emendas-->
                                <div class="mb-10" id="emendas-container" style="display: none;">
                                    <label class="form-label fw-semibold fs-6 mb-2">Emendas Sugeridas</label>
                                    <textarea name="emendas" class="form-control form-control-solid @error('emendas') is-invalid @enderror" 
                                              rows="6" placeholder="Descreva as emendas sugeridas para a proposição...">{{ old('emendas') }}</textarea>
                                    <div class="form-text">Campo obrigatório apenas para pareceres "Favorável com Emendas"</div>
                                    @error('emendas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Emendas-->

                            </div>
                            
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('parecer-juridico.index') }}" class="btn btn-light me-3">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Emitir Parecer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end::Parecer Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
    </div>
    <!--end::Content-->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="tipo_parecer"]');
    const emendasContainer = document.getElementById('emendas-container');
    const emendasTextarea = document.querySelector('textarea[name="emendas"]');

    function toggleEmendas() {
        const selectedValue = document.querySelector('input[name="tipo_parecer"]:checked')?.value;
        
        if (selectedValue === 'COM_EMENDAS') {
            emendasContainer.style.display = 'block';
            emendasTextarea.required = true;
        } else {
            emendasContainer.style.display = 'none';
            emendasTextarea.required = false;
            emendasTextarea.value = '';
        }
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', toggleEmendas);
    });

    // Initialize on page load
    toggleEmendas();
});
</script>
@endsection