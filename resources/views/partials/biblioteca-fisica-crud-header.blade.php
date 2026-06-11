@php
    $active = $active ?? '';
    $tabs = [
        'categorias' => ['label' => 'Categorías', 'route' => route('biblioteca-fisica.categorias.index')],
        'libros' => ['label' => 'Libros', 'route' => route('biblioteca-fisica.libros.index')],
    ];
@endphp
<nav class="bv-crud-nav mb-3" aria-label="Biblioteca física">
    <div class="bv-crud-nav-inner">
        @foreach ($tabs as $key => $t)
            <a href="{{ $t['route'] }}"
                class="bv-crud-tab {{ $active === $key ? 'is-active' : '' }}">{{ $t['label'] }}</a>
        @endforeach
    </div>
</nav>
