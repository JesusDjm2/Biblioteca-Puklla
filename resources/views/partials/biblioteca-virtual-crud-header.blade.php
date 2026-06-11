@php
    $active = $active ?? '';
    $tabs = [
        'secciones' => ['label' => 'Secciones', 'route' => route('secciones.index')],
        'categorias' => ['label' => 'Categorías', 'route' => route('categorias.index')],        
        'libros' => ['label' => 'Libros', 'route' => route('libros.index')],
    ];
@endphp
<nav class="bv-crud-nav mb-3" aria-label="Biblioteca virtual">
    <div class="bv-crud-nav-inner">
        @foreach ($tabs as $key => $t)
            <a href="{{ $t['route'] }}"
                class="bv-crud-tab {{ $active === $key ? 'is-active' : '' }}">{{ $t['label'] }}</a>
        @endforeach
    </div>
</nav>
