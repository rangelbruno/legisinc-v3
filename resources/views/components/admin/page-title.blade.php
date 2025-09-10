<!--begin::Page title-->
<div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
    <!--begin::Title-->
    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
        @if(isset($title))
            {{ $title }}
        @endif
        @if(isset($subtitle))
            <!--begin::Description-->
            <span class="page-desc text-muted fs-7 fw-semibold pt-1">{{ $subtitle }}</span>
            <!--end::Description-->
        @endif
    </h1>
    <!--end::Title-->

    {{ $slot }}
</div>
<!--end::Page title-->