<!--begin::Input group-->
<div class="mb-10 fv-row">
    <!--begin::Label-->
    <label class="required form-label">Parlamentar</label>
    <!--end::Label-->
    <!--begin::Select2-->
    <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione um parlamentar" data-allow-clear="true" name="parlamentar_id">
        <option></option>
        @foreach($parlamentares as $parlamentar)
            <option value="{{ $parlamentar->id }}" 
                {{ (old('parlamentar_id', $membro['parlamentar_id'] ?? '') == $parlamentar->id) ? 'selected' : '' }}>
                {{ $parlamentar->nome }} ({{ $parlamentar->partido }})
            </option>
        @endforeach
    </select>
    <!--end::Select2-->
    @error('parlamentar_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    <!--begin::Description-->
    <div class="text-muted fs-7">Selecione o parlamentar que ocupará o cargo na mesa diretora.</div>
    <!--end::Description-->
</div>
<!--end::Input group-->

<!--begin::Input group-->
<div class="mb-10 fv-row">
    <!--begin::Label-->
    <label class="required form-label">Cargo na Mesa</label>
    <!--end::Label-->
    <!--begin::Select2-->
    <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione um cargo" data-allow-clear="true" name="cargo_mesa">
        <option></option>
        @foreach($cargos as $cargo)
            <option value="{{ $cargo }}" 
                {{ (old('cargo_mesa', $membro['cargo_mesa'] ?? '') == $cargo) ? 'selected' : '' }}>
                {{ $cargo }}
            </option>
        @endforeach
    </select>
    <!--end::Select2-->
    @error('cargo_mesa')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    <!--begin::Description-->
    <div class="text-muted fs-7">Selecione o cargo que o parlamentar ocupará na mesa diretora.</div>
    <!--end::Description-->
</div>
<!--end::Input group-->

<!--begin::Input group-->
<div class="row mb-10">
    <div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="required form-label">Início do Mandato</label>
        <!--end::Label-->
        <!--begin::Input-->
        <input class="form-control mb-2" placeholder="Selecione a data" name="mandato_inicio" id="kt_datepicker_1" 
               value="{{ old('mandato_inicio', $membro['mandato_inicio'] ?? '') }}" />
        <!--end::Input-->
        @error('mandato_inicio')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <!--begin::Description-->
        <div class="text-muted fs-7">Data de início do mandato na mesa diretora.</div>
        <!--end::Description-->
    </div>
    
    <div class="col-md-6 fv-row">
        <!--begin::Label-->
        <label class="required form-label">Fim do Mandato</label>
        <!--end::Label-->
        <!--begin::Input-->
        <input class="form-control mb-2" placeholder="Selecione a data" name="mandato_fim" id="kt_datepicker_2" 
               value="{{ old('mandato_fim', $membro['mandato_fim'] ?? '') }}" />
        <!--end::Input-->
        @error('mandato_fim')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <!--begin::Description-->
        <div class="text-muted fs-7">Data de fim do mandato na mesa diretora.</div>
        <!--end::Description-->
    </div>
</div>
<!--end::Input group-->

@if(isset($isEdit) && $isEdit)
<!--begin::Input group-->
<div class="mb-10 fv-row">
    <!--begin::Label-->
    <label class="required form-label">Status</label>
    <!--end::Label-->
    <!--begin::Select2-->
    <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione o status" name="status">
        <option value="ativo" {{ (old('status', $membro['status'] ?? '') == 'ativo') ? 'selected' : '' }}>Ativo</option>
        <option value="finalizado" {{ (old('status', $membro['status'] ?? '') == 'finalizado') ? 'selected' : '' }}>Finalizado</option>
    </select>
    <!--end::Select2-->
    @error('status')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    <!--begin::Description-->
    <div class="text-muted fs-7">Status atual do mandato na mesa diretora.</div>
    <!--end::Description-->
</div>
<!--end::Input group-->
@endif

<!--begin::Input group-->
<div class="mb-10 fv-row">
    <!--begin::Label-->
    <label class="form-label">Observações</label>
    <!--end::Label-->
    <!--begin::Textarea-->
    <textarea class="form-control mb-2" rows="3" name="observacoes" placeholder="Observações sobre o mandato (opcional)">{{ old('observacoes', $membro['observacoes'] ?? '') }}</textarea>
    <!--end::Textarea-->
    @error('observacoes')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    <!--begin::Description-->
    <div class="text-muted fs-7">Informações adicionais sobre o mandato (opcional).</div>
    <!--end::Description-->
</div>
<!--end::Input group-->