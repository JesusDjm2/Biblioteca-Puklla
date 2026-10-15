@php
    $active = $active ?? '';
    $tabs = [
        'categorias' => ['label' => 'Categorías', 'route' => route('biblioteca-fisica.categorias.index')],
        'libros'     => ['label' => 'Libros', 'route' => route('biblioteca-fisica.libros.index')],
        'prestamos'  => ['label' => 'Préstamos', 'route' => route('biblioteca-fisica.prestamos.index')],
        'materiales' => [
            'label'   => 'Material didáctico',
            'route'   => route('biblioteca-fisica.materiales.index'),
            'aliases' => ['materiales', 'material-categorias'],
        ],
    ];
@endphp
<nav class="bv-crud-nav mb-3" aria-label="Biblioteca física">
    <div class="bv-crud-nav-inner">
        @foreach ($tabs as $key => $t)
            @php
                $isActive = isset($t['aliases'])
                    ? in_array($active, $t['aliases'], true)
                    : ($active === $key);
            @endphp
            <a href="{{ $t['route'] }}"
                class="bv-crud-tab {{ $isActive ? 'is-active' : '' }}">{{ $t['label'] }}</a>
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
