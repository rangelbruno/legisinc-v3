@props(['items' => []])

<!--begin::Breadcrumb-->
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
    @foreach($items as $index => $item)
        <li class="breadcrumb-item text-muted">
            @if(isset($item['active']) && $item['active'])
                {{ $item['label'] }}
            @elseif(isset($item['url']))
                <a href="{{ $item['url'] }}" class="text-muted text-hover-primary">{{ $item['label'] }}</a>
            @else
                {{ $item['label'] }}
            @endif
        </li>
        @if($index < count($items) - 1)
            <li class="breadcrumb-item">
                <span class="bullet bg-gray-400 w-5px h-2px"></span>
            </li>
        @endif
    @endforeach
</ul>
<!--end::Breadcrumb-->