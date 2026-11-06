{{--
    Lista lateral de secciones (categorías) de videos, cada una con su botón para compartir.
    Parámetros: $secciones (con videos_count), $activa (id de la categoría actual o null), $total.
--}}
@once
    <style>
        .vl-sidebar__item { display: flex; align-items: center; gap: 4px; }
        .vl-sidebar__item .vl-sidebar__link { flex: 1; min-width: 0; }
        .vl-sidebar__item .cmp-btn { flex: 0 0 auto; width: 28px; height: 28px; font-size: .75rem; opacity: .55; transition: opacity .2s; }
        .vl-sidebar__item:hover .cmp-btn, .vl-sidebar__item .cmp-btn:focus-visible,
        .vl-sidebar__item .cmp-btn[aria-expanded="true"] { opacity: 1; }
        @media (hover: none) { .vl-sidebar__item .cmp-btn { opacity: 1; } }
    </style>
@endonce

<ul class="vl-sidebar__list">
    <li class="vl-sidebar__item">
        <a href="{{ route('videos') }}" class="vl-sidebar__link {{ $activa ? '' : 'is-active' }}">
            <span>Todos los videos</span>
            <span class="vl-sidebar__count">{{ $total }}</span>
        </a>
        @include('partials.compartir', ['url' => route('videos'), 'titulo' => 'Videos — Puklla Virtual'])
    </li>
    @foreach ($secciones as $sec)
        <li class="vl-sidebar__item">
            <a href="{{ route('videoscat.show', $sec->url) }}" class="vl-sidebar__link {{ $activa === $sec->id ? 'is-active' : '' }}">
                <span>{{ $sec->nombre }}</span>
                <span class="vl-sidebar__count">{{ $sec->videos_count }}</span>
            </a>
            @include('partials.compartir', [
                'url' => route('videoscat.show', $sec->url),
                'titulo' => 'Videos: '.$sec->nombre.' — Puklla Virtual',
            ])
        </li>
    @endforeach
</ul>
