@props(['class' => ''])

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid {{ $class }}">
    {{ $slot }}
</div>
<!--end::Content wrapper-->