@if (!empty($items))
    <nav aria-label="Miga de pan">
        <ol class="breadcrumb bibli-crud-breadcrumb mb-3">
            @foreach ($items as $item)
                @if (!empty($item['url']))
                    <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
