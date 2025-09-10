@props(['class' => ''])

<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 {{ $class }}">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        {{ $slot }}
    </div>
    <!--end::Toolbar container-->
</div>
<!--end::Toolbar-->