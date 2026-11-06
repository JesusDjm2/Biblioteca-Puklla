@extends('layouts.padre')
@section('titulo', '- '.($query !== '' ? '«'.$query.'» · ' : '').'Resultados de búsqueda')
@section('og_titulo', 'Búsqueda en Puklla Virtual')

@php
    use App\Services\BuscadorGlobal;

    $modulos = BuscadorGlobal::MODULOS;
    // Ancho de cada tarjeta en el carrusel / cuadrícula según el tipo de contenido.
    $anchos = ['libros' => '185px', 'fisicos' => '230px', 'repositorio' => '250px', 'taqe' => '260px', 'videos' => '270px', 'canciones' => '230px', 'material' => '220px'];
    $urlTab = fn (?string $modulo = null) => route('busqueda', array_filter(['query' => $query, 'tipo' => $modulo])).'#resultados';
@endphp

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        /* ═══ Buscador global — complementos al sistema "lib-*" ═══ */
        /* Portada más baja que en los catálogos: aquí lo importante son los resultados. */
        .lib-hero.rb-hero { height: calc(clamp(220px, 34vh, 380px) + var(--puklla-nav-h, 5rem)); }
        #resultados { scroll-margin-top: var(--puklla-nav-h, 5rem); }
        .rb-refine { display: flex; gap: .5rem; margin-bottom: 1.25rem; }
        .rb-refine .lib-search { flex: 1; }
        .rb-refine__btn {
            flex: 0 0 auto; border: 0; border-radius: 999px; padding: 0 1.25rem; font-weight: 600; font-size: .9rem;
            background: var(--puklla-accent, #cb9042); color: #fff; transition: filter .2s;
        }
        .rb-refine__btn:hover { filter: brightness(.93); }

        /* Pestañas por módulo */
        .rb-tabs {
            position: sticky; top: var(--puklla-nav-h, 5rem); z-index: 20;
            display: flex; gap: .45rem; overflow-x: auto; scrollbar-width: none;
            padding: .6rem 0; margin-bottom: 1.5rem; background: rgba(248, 245, 241, .95); backdrop-filter: blur(6px);
            border-bottom: 1px solid rgba(200, 142, 65, .18);
        }
        .rb-tabs::-webkit-scrollbar { display: none; }
        .rb-tab {
            flex: 0 0 auto; display: inline-flex; align-items: center; gap: .45rem;
            padding: .45rem .9rem; border-radius: 999px; font-size: .84rem; font-weight: 600;
            color: #5a4a3a; background: #fff; border: 1px solid rgba(200, 142, 65, .25);
            text-decoration: none; transition: background .2s, color .2s, border-color .2s;
        }
        .rb-tab:hover { border-color: #cb9042; color: #2a2520; }
        .rb-tab.is-activo { background: #2a2520; border-color: #2a2520; color: #fff; }
        .rb-tab__n { font-size: .72rem; padding: .05rem .45rem; border-radius: 999px; background: rgba(203, 144, 66, .16); color: #8a5a1c; }
        .rb-tab.is-activo .rb-tab__n { background: rgba(255, 255, 255, .18); color: #fff; }

        /* Sección de un módulo */
        .rb-modulo { margin-bottom: 2.4rem; }
        .rb-modulo__cab { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: .9rem; }
        .rb-modulo__titulo { display: flex; align-items: center; gap: .6rem; margin: 0; font-size: 1.25rem; font-weight: 700; color: #2a2520; }
        .rb-modulo__icono {
            width: 2.2rem; height: 2.2rem; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;
            background: rgba(203, 144, 66, .14); color: #a06e2e; font-size: 1rem;
        }
        .rb-modulo__sub { margin: .15rem 0 0; font-size: .82rem; color: #8c7b6d; }
        .rb-ver-todos {
            display: inline-flex; align-items: center; gap: .4rem; padding: .45rem 1rem; border-radius: 999px;
            font-size: .84rem; font-weight: 700; color: #a06e2e; border: 1px solid rgba(203, 144, 66, .45);
            text-decoration: none; white-space: nowrap; transition: background .2s, color .2s;
        }
        .rb-ver-todos:hover { background: #cb9042; border-color: #cb9042; color: #fff; }

        /* Carrusel (desplazamiento nativo + flechas) */
        .rb-carrusel { position: relative; }
        .rb-pista {
            display: grid; grid-auto-flow: column; grid-auto-columns: var(--rb-ancho, 230px); gap: 1rem;
            overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; overscroll-behavior-x: contain;
            padding: .25rem .25rem 1rem; margin: 0 -.25rem; scroll-padding-inline: .25rem; scrollbar-width: thin; scrollbar-color: rgba(203, 144, 66, .45) transparent;
        }
        .rb-pista::-webkit-scrollbar { height: 6px; }
        .rb-pista::-webkit-scrollbar-thumb { background: rgba(203, 144, 66, .4); border-radius: 999px; }
        .rb-slide { scroll-snap-align: start; min-width: 0; display: flex; }
        .rb-slide > * { width: 100%; }
        .rb-flecha {
            position: absolute; top: 40%; z-index: 3; width: 42px; height: 42px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; border: 1px solid rgba(200, 142, 65, .3);
            background: #fff; color: #2a2520; box-shadow: 0 6px 18px rgba(42, 37, 32, .16); transition: opacity .2s, transform .2s;
        }
        .rb-flecha:hover { transform: scale(1.06); color: #a06e2e; }
        .rb-flecha--izq { left: -14px; }
        .rb-flecha--der { right: -14px; }
        .rb-flecha[disabled] { opacity: 0; pointer-events: none; }
        @media (hover: none), (max-width: 767.98px) { .rb-flecha { display: none; } }

        /* Tarjeta final "Ver todos" */
        .rb-mas {
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .5rem; text-align: center;
            min-height: 100%; padding: 1.25rem; border-radius: 12px; border: 2px dashed rgba(203, 144, 66, .45);
            color: #a06e2e; font-weight: 700; text-decoration: none; background: rgba(203, 144, 66, .05); transition: background .2s;
        }
        .rb-mas:hover { background: rgba(203, 144, 66, .12); color: #8a5a1c; }
        .rb-mas i { font-size: 1.6rem; }
        .rb-mas small { font-weight: 500; color: #8c7b6d; }

        /* Cuadrícula de la pestaña de un módulo */
        .rb-rejilla { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(var(--rb-ancho, 230px), 100%), 1fr)); gap: 1rem; }
        .rb-rejilla > * { display: flex; }
        .rb-rejilla > * > * { width: 100%; }
        .rb-paginacion { display: flex; justify-content: center; margin-top: 1.75rem; }
        .rb-modulo__acciones { display: flex; gap: .5rem; flex-wrap: wrap; }
        .rb-volver { display: inline-flex; align-items: center; gap: .4rem; font-size: .84rem; font-weight: 600; color: #8c7b6d; text-decoration: none; margin-bottom: .75rem; }
        .rb-volver:hover { color: #a06e2e; }

        /* Tarjeta genérica (repositorio, Taq'e, videos, canciones, material) */
        .rb-card {
            display: flex; flex-direction: column; height: 100%; overflow: hidden; border-radius: 12px;
            background: #fff; border: 1px solid rgba(200, 142, 65, .14); box-shadow: 0 2px 10px rgba(42, 37, 32, .07);
            transition: box-shadow .25s, transform .25s;
        }
        .rb-card:hover { box-shadow: 0 8px 26px rgba(42, 37, 32, .13); transform: translateY(-3px); }
        .rb-card__portada {
            position: relative; display: flex; align-items: center; justify-content: center; aspect-ratio: 16 / 10;
            overflow: hidden; background: #2a2520; color: #fff; font-size: 2.2rem; text-decoration: none; flex-shrink: 0;
        }
        .rb-card__portada img, .rb-card__portada video { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s; pointer-events: none; }
        .rb-card:hover .rb-card__portada img { transform: scale(1.05); }
        .rb-card__portada--video, .rb-card__portada--audio { aspect-ratio: 16 / 9; }
        .rb-card__portada--doc { background: linear-gradient(135deg, #7f1d1d, #b91c1c); aspect-ratio: 16 / 7; }
        .rb-card__portada--spotify { background: linear-gradient(135deg, #14532d, #1db954); }
        .rb-card__portada--drive { background: linear-gradient(135deg, #1e3a8a, #2563eb); }
        .rb-card__portada--material { background: linear-gradient(135deg, #5b3a12, #c78e41); }
        .rb-card__play {
            position: absolute; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: rgba(20, 16, 12, .6); color: #fff; font-size: 1rem; backdrop-filter: blur(3px); transition: transform .25s, background .25s;
        }
        .rb-card:hover .rb-card__play { transform: scale(1.1); background: #cb9042; }
        .rb-card__etiqueta {
            position: absolute; left: 8px; top: 8px; max-width: calc(100% - 16px); font-size: .68rem; font-weight: 700;
            padding: 3px 8px; border-radius: 6px; background: rgba(20, 16, 12, .78); color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .rb-card__cuerpo { flex: 1; display: flex; flex-direction: column; gap: .3rem; padding: .7rem .85rem .85rem; border-top: 2px solid rgba(200, 142, 65, .2); }
        .rb-card__cat { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #a06e2e; }
        .rb-card__titulo { margin: 0; font-size: .9rem; font-weight: 700; line-height: 1.35; color: #2a2520;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .rb-card__titulo--3 { -webkit-line-clamp: 3; }
        .rb-card__titulo a { color: inherit; text-decoration: none; }
        .rb-card__titulo a:hover { color: #a06e2e; }
        .rb-card__sub { margin: 0; font-size: .76rem; color: #8c7b6d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .rb-card__pie { margin: auto 0 0; padding-top: .35rem; display: flex; flex-wrap: wrap; gap: .3rem .8rem; font-size: .72rem; color: #9c8c7c; }
        .rb-card__nota { margin: 0; font-size: .7rem; color: #a89787; font-style: italic; }
        .rb-disp { font-weight: 700; }
        .rb-disp.is-si { color: #2e8b57; }
        .rb-disp.is-no { color: #b7791f; }
        .rb-tarjeta.lib-book-card { height: 100%; }
        /* Sin esto, los títulos largos empujan la tarjeta fuera de su columna */
        .rb-rejilla > *, .rb-slide, .rb-tarjeta, .rb-tarjeta .cmp-fila > * { min-width: 0; }
        .rb-tarjeta .lib-book-card__title { overflow-wrap: anywhere; }
        /* La tarjeta de libro es columna + align-items:flex-start: el texto tomaría el ancho del autor (nowrap) */
        .rb-tarjeta .lib-book-card__info.cmp-fila > div { align-self: stretch; }
        .rb-tarjeta .lib-book-card__author { overflow: hidden; }
        .rb-tarjeta .lib-book-card__author > span { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
        .rb-paginacion .pagination { flex-wrap: wrap; justify-content: center; gap: .25rem 0; }

        /* Celular: libros en dos columnas y portadas de documentos más bajas */
        @media (max-width: 575.98px) {
            .rb-rejilla--2col { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
            .rb-card__portada--doc { aspect-ratio: 16 / 4; font-size: 1.6rem; }
            .rb-modulo__titulo { font-size: 1.1rem; }
        }

        /* Estados vacíos */
        .rb-empty { text-align: center; padding: 3rem 1rem; color: #7a6a5a; background: #fff; border: 1px solid rgba(200, 142, 65, .13); border-radius: 12px; }
        .rb-empty > i { font-size: 2.6rem; margin-bottom: .9rem; display: block; color: var(--puklla-accent); opacity: .55; }
        .rb-empty p { margin: 0 auto .4rem; max-width: 34rem; }
        .rb-explorar { display: flex; flex-wrap: wrap; justify-content: center; gap: .5rem; margin-top: 1.25rem; }

        /* Resaltado de coincidencias */
        .rb-hl { background: #ffe58a; color: #2a2520; border-radius: 2px; padding: 0 1px; }
    </style>
@endsection

@section('contenido')

    {{-- ═══ HERO ═══ --}}
    <div class="lib-hero rb-hero">
        <img src="{{ asset('img/fondo/tramites-2-2026.jpeg') }}" alt="" class="lib-hero__bg" loading="eager">
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">ESPP Pukllasunchis · Resultados de búsqueda</p>
            <h1 class="lib-hero__title lib-hero__title--cat">&ldquo;{{ $query }}&rdquo;</h1>
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                @if ($total > 0)
                    <span><i class="fa fa-check-circle"></i> {{ number_format($total, 0, ',', '.') }} {{ $total === 1 ? 'resultado' : 'resultados' }}
                        en {{ count($conteos) }} {{ count($conteos) === 1 ? 'sección' : 'secciones' }}</span>
                @else
                    <span><i class="fa fa-circle-xmark"></i> Sin resultados</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ BODY ═══ --}}
    <div class="container py-4 py-lg-5" id="resultados">

        <form action="{{ route('busqueda') }}" method="GET" class="rb-refine" role="search">
            <div class="lib-search">
                <i class="fa fa-search lib-search__icon"></i>
                <input type="search" name="query" id="rb-query" class="lib-search__input" value="{{ $query }}"
                    placeholder="Buscar libros, documentos, videos, actividades Taq'e…" autocomplete="off" aria-label="Buscar">
            </div>
            <button type="submit" class="rb-refine__btn">Buscar</button>
        </form>

        @if (! $buscador->esValida())
            <div class="rb-empty">
                <i class="fa fa-keyboard"></i>
                <p>Escribe al menos {{ BuscadorGlobal::MIN_CARACTERES }} letras para buscar.</p>
            </div>
        @elseif ($total === 0)
            <div class="rb-empty">
                <i class="fa fa-search"></i>
                <p>No encontramos resultados para <strong>&ldquo;{{ $query }}&rdquo;</strong>.</p>
                <p class="small">Revisa la ortografía, usa menos palabras o prueba con el nombre del autor.</p>
                <div class="rb-explorar">
                    <a href="{{ route('libros') }}" class="rb-ver-todos"><i class="fa-solid fa-book-open"></i> Biblioteca virtual</a>
                    <a href="{{ route('catalogo-fisico') }}" class="rb-ver-todos"><i class="fa-solid fa-book"></i> Biblioteca física</a>
                    <a href="{{ route('repositorio.index') }}" class="rb-ver-todos"><i class="fa-solid fa-landmark"></i> Repositorio</a>
                    <a href="{{ route('taqe.index') }}" class="rb-ver-todos"><i class="fa-solid fa-calendar-check"></i> Taq'e</a>
                    <a href="{{ route('videos') }}" class="rb-ver-todos"><i class="fa-solid fa-film"></i> Videos</a>
                </div>
            </div>
        @else

            {{-- Pestañas: "Todo" + un acceso por módulo con su total --}}
            <nav class="rb-tabs" aria-label="Filtrar resultados por sección">
                <a href="{{ $urlTab() }}" class="rb-tab {{ $tipo ? '' : 'is-activo' }}" @if (! $tipo) aria-current="page" @endif>
                    Todo <span class="rb-tab__n">{{ number_format($total, 0, ',', '.') }}</span>
                </a>
                @foreach ($conteos as $modulo => $n)
                    <a href="{{ $urlTab($modulo) }}" class="rb-tab {{ $tipo === $modulo ? 'is-activo' : '' }}" @if ($tipo === $modulo) aria-current="page" @endif>
                        <i class="{{ $modulos[$modulo][3] }}"></i> {{ $modulos[$modulo][0] }}
                        <span class="rb-tab__n">{{ number_format($n, 0, ',', '.') }}</span>
                    </a>
                @endforeach
            </nav>

            @if ($tipo)
                {{-- ── Pestaña de un módulo: todos sus resultados paginados ── --}}
                @php $catalogo = $buscador->enlaceCatalogo($tipo); @endphp
                <section class="rb-modulo" aria-labelledby="rb-titulo-{{ $tipo }}">
                    <a href="{{ $urlTab() }}" class="rb-volver"><i class="fa fa-arrow-left"></i> Todos los resultados</a>
                    <div class="rb-modulo__cab">
                        <div>
                            <h2 class="rb-modulo__titulo" id="rb-titulo-{{ $tipo }}">
                                <span class="rb-modulo__icono"><i class="{{ $modulos[$tipo][3] }}"></i></span>{{ $modulos[$tipo][0] }}
                            </h2>
                            <p class="rb-modulo__sub">
                                {{ BuscadorGlobal::totalTexto($tipo, $resultados->total()) }} para &ldquo;{{ $query }}&rdquo;
                                @if ($resultados->lastPage() > 1) · página {{ $resultados->currentPage() }} de {{ $resultados->lastPage() }} @endif
                            </p>
                        </div>
                        @if ($catalogo)
                            <div class="rb-modulo__acciones">
                                <a href="{{ $catalogo[1] }}" class="rb-ver-todos"><i class="fa fa-sliders"></i> {{ $catalogo[0] }}</a>
                            </div>
                        @endif
                    </div>

                    <div class="rb-rejilla {{ in_array($tipo, ['libros', 'fisicos', 'material']) ? 'rb-rejilla--2col' : '' }}" style="--rb-ancho: {{ $anchos[$tipo] }};">
                        @foreach ($resultados as $item)
                            <div class="rb-item">@include('buscador._tarjeta', ['modulo' => $tipo, 'item' => $item])</div>
                        @endforeach
                    </div>

                    @if ($resultados->hasPages())
                        <div class="rb-paginacion">{{ $resultados->onEachSide(1)->fragment('resultados')->links() }}</div>
                    @endif
                </section>
            @else
                {{-- ── Vista "Todo": un carrusel por módulo ── --}}
                @foreach ($carruseles as $modulo => $items)
                    @php $n = $conteos[$modulo]; @endphp
                    <section class="rb-modulo" aria-labelledby="rb-titulo-{{ $modulo }}">
                        <div class="rb-modulo__cab">
                            <div>
                                <h2 class="rb-modulo__titulo" id="rb-titulo-{{ $modulo }}">
                                    <span class="rb-modulo__icono"><i class="{{ $modulos[$modulo][3] }}"></i></span>{{ $modulos[$modulo][0] }}
                                </h2>
                                <p class="rb-modulo__sub">{{ BuscadorGlobal::totalTexto($modulo, $n) }}</p>
                            </div>
                            @if ($n > $items->count())
                                <a href="{{ $urlTab($modulo) }}" class="rb-ver-todos">
                                    Ver {{ $n === 1 ? 'el' : 'los' }} {{ number_format($n, 0, ',', '.') }} <i class="fa fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>

                        <div class="rb-carrusel">
                            <button type="button" class="rb-flecha rb-flecha--izq" data-dir="-1" aria-label="Anteriores" disabled><i class="fa fa-chevron-left"></i></button>
                            <div class="rb-pista" style="--rb-ancho: {{ $anchos[$modulo] }};" tabindex="0" aria-label="{{ $modulos[$modulo][0] }}: resultados">
                                @foreach ($items as $item)
                                    <div class="rb-slide rb-item">@include('buscador._tarjeta', ['modulo' => $modulo, 'item' => $item])</div>
                                @endforeach
                                @if ($n > $items->count())
                                    <div class="rb-slide">
                                        <a href="{{ $urlTab($modulo) }}" class="rb-mas">
                                            <i class="fa fa-arrow-right"></i>
                                            Ver {{ $n === 1 ? 'el' : 'los' }} {{ number_format($n, 0, ',', '.') }}
                                            <small>{{ $modulos[$modulo][2] }} encontrados</small>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="rb-flecha rb-flecha--der" data-dir="1" aria-label="Siguientes"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </section>
                @endforeach
            @endif
        @endif
    </div>

    <script>
    (function () {
        gsap.registerPlugin(ScrollTrigger);

        /* Hero */
        gsap.to('.lib-hero__bg', { y: '20%', ease: 'none', scrollTrigger: { trigger: '.lib-hero', start: 'top top', end: 'bottom top', scrub: 1.2 } });
        gsap.set('.lib-hero__eyebrow', { y: 22 });
        gsap.set('.lib-hero__title', { y: 30 });
        gsap.set('.lib-hero__meta', { y: 16 });
        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .to('.lib-hero__eyebrow', { opacity: 1, y: 0, duration: .75 }, .2)
            .to('.lib-hero__title', { opacity: 1, y: 0, duration: .85 }, .4)
            .to('.lib-hero__divider', { width: 56, duration: .5 }, .8)
            .to('.lib-hero__meta', { opacity: 1, y: 0, duration: .6 }, 1.0);

        /* Entrada de secciones (solo desplazamiento, sin opacidad) */
        document.querySelectorAll('.rb-modulo').forEach(function (sec) {
            gsap.from(sec.querySelectorAll('.rb-item'), {
                y: 22, duration: .4, stagger: .04, ease: 'power2.out', clearProps: 'transform',
                scrollTrigger: { trigger: sec, start: 'top 90%', once: true }
            });
        });

        /* Carruseles: flechas que avanzan casi una "pantalla" y se ocultan en los extremos */
        document.querySelectorAll('.rb-carrusel').forEach(function (car) {
            var pista = car.querySelector('.rb-pista');
            var izq = car.querySelector('.rb-flecha--izq');
            var der = car.querySelector('.rb-flecha--der');
            function estado() {
                // Margen de 8px: el ajuste de scroll-snap deja el carrusel unos píxeles desplazado.
                var max = pista.scrollWidth - pista.clientWidth - 8;
                izq.disabled = pista.scrollLeft <= 8;
                der.disabled = pista.scrollLeft >= max;
            }
            car.querySelectorAll('.rb-flecha').forEach(function (b) {
                b.addEventListener('click', function () {
                    pista.scrollBy({ left: pista.clientWidth * 0.85 * parseInt(b.dataset.dir, 10), behavior: 'smooth' });
                });
            });
            pista.addEventListener('scroll', estado, { passive: true });
            window.addEventListener('resize', estado);
            estado();
        });

        /* Resaltado de coincidencias (cada palabra buscada, sin distinguir tildes) */
        (function () {
            var q = (document.getElementById('rb-query') || {}).value || '';
            var palabras = q.trim().split(/\s+/).filter(function (p) { return p.length >= 2; });
            if (!palabras.length) return;
            function quitarTildes(s) { return s.normalize('NFD').replace(/[̀-ͯ]/g, ''); }
            function escRx(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
            function esc(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
            var rx = new RegExp(palabras.map(function (p) { return escRx(quitarTildes(p)); }).join('|'), 'gi');

            document.querySelectorAll('.rb-resaltar').forEach(function (el) {
                var nodos = [];
                (function walk(n) {
                    if (n.nodeType === 3) nodos.push(n);
                    else if (n.nodeType === 1 && n.tagName !== 'MARK' && n.tagName !== 'I') n.childNodes.forEach(walk);
                })(el);
                nodos.forEach(function (tn) {
                    var txt = tn.textContent;
                    // Se quita la tilde carácter por carácter para que las posiciones coincidan
                    // con el texto original ("Educación" → "Educacion", mismo largo).
                    var plano = '';
                    for (var i = 0; i < txt.length; i++) plano += quitarTildes(txt.charAt(i)).charAt(0) || txt.charAt(i);
                    rx.lastIndex = 0;
                    if (!rx.test(plano)) return;
                    rx.lastIndex = 0;
                    var html = '', ultimo = 0, m;
                    while ((m = rx.exec(plano)) !== null) {
                        html += esc(txt.slice(ultimo, m.index)) + '<mark class="rb-hl">' + esc(txt.slice(m.index, m.index + m[0].length)) + '</mark>';
                        ultimo = m.index + m[0].length;
                        if (m[0].length === 0) rx.lastIndex++;
                    }
                    html += esc(txt.slice(ultimo));
                    var span = document.createElement('span');
                    span.innerHTML = html;
                    tn.parentNode.replaceChild(span, tn);
                });
            });
        })();
    })();
    </script>
@endsection
