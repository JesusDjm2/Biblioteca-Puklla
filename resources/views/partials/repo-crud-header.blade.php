@php
    $active = $active ?? '';
    $tabs = [
        'estructura' => ['label' => 'Comunidades y colecciones', 'route' => route('admin.repositorio.estructura.index')],
        'items' => ['label' => 'Documentos', 'route' => route('admin.repositorio.items.index')],
    ];
@endphp
<nav class="bv-crud-nav mb-3" aria-label="Repositorio Institucional">
    <div class="bv-crud-nav-inner">
        @foreach ($tabs as $key => $t)
            <a href="{{ $t['route'] }}" class="bv-crud-tab {{ $active === $key ? 'is-active' : '' }}">{{ $t['label'] }}</a>
        @endforeach
    </div>
    @if (!empty($stats) || !empty($botones))
        <div class="bv-crud-nav-actions">
            @foreach ($stats ?? [] as $stat)
                <span class="bv-crud-nav-count {{ $stat['tono'] ?? '' }}">
                    <i class="fa {{ $stat['icono'] ?? 'fa-hashtag' }}"></i>
                    {{ number_format($stat['valor']) }} {{ $stat['valor'] === 1 ? $stat['singular'] : $stat['plural'] }}
                </span>
            @endforeach
            @foreach ($botones ?? [] as $boton)
                <a href="{{ $boton['route'] }}" class="btn btn-sm {{ $boton['clase'] ?? 'btn-info' }}">
                    <i class="fa {{ $boton['icono'] ?? 'fa-plus' }} mr-1"></i>{{ $boton['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</nav>

@if (session('success'))
    <div class="alert alert-success bv-alert alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger bv-alert alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger bv-alert">
        <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
