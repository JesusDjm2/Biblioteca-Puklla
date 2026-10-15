@extends('layouts.padre')
@section('titulo', "- {$categoria->nombre}")

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/Flip.min.js"></script>
@endsection

@section('contenido')

    {{-- ===== HERO ===== --}}
    <div class="lib-hero">
        <img src="{{ asset('img/fondo/tramites-2026.webp') }}" alt="Biblioteca Pukllasunchis" class="lib-hero__bg"
            loading="eager">
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">Biblioteca Virtual · ESPP Pukllasunchis</p>
            <h1 class="lib-hero__title lib-hero__title--cat">{{ $categoria->nombre }}</h1>
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                <span>
                    <i class="fa fa-book-open"></i>
                    {{ $libros->count() }}
                    {{ $libros->count() === 1 ? 'texto' : 'textos' }}
                </span>
            </div>
            <div class="mt-3">
                <a href="{{ route('catalogo-fisico') }}"
                   style="display:inline-flex;align-items:center;gap:.45rem;
                          background:rgba(255,255,255,.15);backdrop-filter:blur(6px);
                          border:1px solid rgba(255,255,255,.35);border-radius:999px;
                          color:#fff;font-size:.82rem;font-weight:600;
                          padding:.45rem 1.1rem;text-decoration:none;
                          transition:background .2s,transform .18s;"
                   onmouseover="this.style.background='rgba(255,255,255,.28)';this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='rgba(255,255,255,.15)';this.style.transform=''">
                    <i class="fa fa-book"></i> Ver Biblioteca Física
                    <i class="fa fa-arrow-right" style="font-size:.72rem;opacity:.8;"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- FAB: siempre visible en mobile, oculto cuando el sidebar está en pantalla --}}
    <button id="lib-sidebar-fab" class="lib-sidebar-fab" aria-label="Ver secciones">
        <i class="fa fa-layer-group"></i> Secciones
    </button>

    {{-- ===== BODY ===== --}}
    <div class="container py-4 py-lg-5">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-4 order-2 order-lg-1">
                <aside class="lib-sidebar" id="lib-sidebar">
                    <h4 class="lib-sidebar__title">
                        <i class="fa fa-layer-group"></i> Secciones
                    </h4>
                    @foreach ($secciones as $seccion)
                        @php
                            $totalLibros = $seccion->categorias->sum('libros_count');
                            $isActive = $seccion->categorias->contains('id', $categoria->id);
                        @endphp
                        <div class="lib-sidebar__section">
                            <button type="button" class="lib-sidebar__section-btn {{ $isActive ? 'is-open' : '' }}">
                                <span class="lib-sidebar__section-name">{{ $seccion->nombre }}</span>
                                <span class="lib-sidebar__meta">
                                    <span class="sidebar-badge sidebar-badge-cat"
                                        title="{{ $seccion->categorias_count }} categorías">
                                        {{ $seccion->categorias_count }}
                                    </span>
                                    <span class="sidebar-badge sidebar-badge-book" title="{{ $totalLibros }} textos">
                                        {{ $totalLibros }}
                                    </span>
                                </span>
                                <span class="lib-sidebar__chevron">▼</span>
                            </button>
                            <div class="lib-sidebar__submenu">
                                @foreach ($seccion->categorias as $cat)
                                    <a href="{{ route('categorias.show', $cat->url) }}"
                                        class="lib-sidebar__cat-link {{ $cat->id === $categoria->id ? 'is-active' : '' }}">
                                        <span class="lib-sidebar__cat-name">{{ $cat->nombre }}</span>
                                        <span class="lib-sidebar__cat-count">{{ $cat->libros_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </aside>
            </div>

            {{-- Content --}}
            <div class="col-lg-8 order-1 order-lg-2">

                {{-- Heading --}}
                <div class="lib-content-head">
                    <h2 class="lib-content-head__title">{{ $categoria->nombre }}</h2>
                    <p class="lib-content-head__sub">
                        {{ $libros->count() }} {{ $libros->count() === 1 ? 'texto' : 'textos' }} en esta categoría
                    </p>
                </div>

                {{-- Controls --}}
                <div class="lib-controls">
                    <div class="lib-search">
                        <i class="fa fa-search lib-search__icon"></i>
                        <input type="text" id="search-input" class="lib-search__input" placeholder=""
                            aria-label="Buscar por título o autor">
                    </div>
                    <div class="lib-sort-btns">
                        <button type="button" class="lib-sort-btn" onclick="ordenarPor('nombre', this)">
                            <i class="fa fa-font"></i> Título A–Z
                        </button>
                        <button type="button" class="lib-sort-btn" onclick="ordenarPor('autor', this)">
                            <i class="fa fa-user"></i> Autor A–Z
                        </button>
                    </div>
                </div>

                {{-- Grid de libros --}}
                <div id="contenedorLibros" class="row g-3">
                    @foreach ($libros as $libro)
                        @php
                            $urlVistaPrevia = "https://drive.google.com/thumbnail?id={$libro->identificador}&sz=w600";
                        @endphp
                        <div class="col-6 col-md-4 lib-book-col" data-nombre="{{ $libro->nombre }}"
                            data-autor="{{ $libro->autor }}">
                            <div class="lib-book-card">
                                <div class="lib-book-card__cover">
                                    <img src="{{ $urlVistaPrevia }}" alt="{{ $libro->nombre }}" loading="lazy">
                                    <div class="lib-book-card__overlay">
                                        <button type="button" class="lib-book-card__btn"
                                            onclick="abrirVistaPreviaLibro({{ \Illuminate\Support\Js::from($libro->identificador) }}, {{ \Illuminate\Support\Js::from($libro->nombre) }})">
                                            <i class="fa fa-eye"></i> Vista previa
                                        </button>
                                    </div>
                                </div>
                                <div class="lib-book-card__info">
                                    <h3 class="lib-book-card__title" title="{{ $libro->nombre }}">{{ $libro->nombre }}</h3>
                                    <p class="lib-book-card__author">
                                        <i class="fa fa-user"></i> {{ $libro->autor }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>{{-- /col content --}}
        </div>{{-- /row --}}
    </div>{{-- /container --}}

    <script>
        (function() {
            gsap.registerPlugin(ScrollTrigger, Flip);

            /* ---- Hero parallax ---- */
            gsap.to('.lib-hero__bg', {
                y: '20%',
                ease: 'none',
                scrollTrigger: {
                    trigger: '.lib-hero',
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 1.2
                }
            });

            /* ---- Hero text reveal ---- */
            gsap.set('.lib-hero__eyebrow', { y: 20 });
            gsap.set('.lib-hero__title',   { y: 28 });
            gsap.set('.lib-hero__meta',    { y: 16 });
            gsap.set('.lib-hero__content .mt-3', { opacity: 0, y: 14 });

            gsap.timeline({
                    defaults: {
                        ease: 'power3.out'
                    }
                })
                .to('.lib-hero__eyebrow', {
                    opacity: 1,
                    y: 0,
                    duration: .75
                }, .25)
                .to('.lib-hero__title', {
                    opacity: 1,
                    y: 0,
                    duration: .85
                }, .45)
                .to('.lib-hero__divider', {
                    width: 56,
                    duration: .5
                }, .82)
                .to('.lib-hero__meta', {
                    opacity: 1,
                    y: 0,
                    duration: .55
                }, 1.0)
                .to('.lib-hero__content .mt-3', {
                    opacity: 1,
                    y: 0,
                    duration: .5
                }, 1.2);

            /* ---- Cards scroll-reveal (solo y, sin opacity para evitar transparencias) ---- */
            gsap.from('.lib-book-col', {
                y: 28,
                duration: .45,
                stagger: .05,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: '#contenedorLibros',
                    start: 'top 88%',
                    once: true
                }
            });

            /* ---- Sidebar accordion ---- */
            document.querySelectorAll('.lib-sidebar__section-btn').forEach(btn => {
                const sub = btn.nextElementSibling;
                const startOpen = btn.classList.contains('is-open');
                /* Sección activa ya abierta; resto cerrado */
                gsap.set(sub, {
                    height: startOpen ? 'auto' : 0,
                    overflow: 'hidden'
                });

                btn.addEventListener('click', () => {
                    const isOpen = btn.classList.toggle('is-open');
                    gsap.to(sub, {
                        height: isOpen ? 'auto' : 0,
                        duration: isOpen ? .38 : .26,
                        ease: isOpen ? 'power2.out' : 'power2.in'
                    });
                });
            });

            /* ---- Search ---- */
            function norm(s) {
                return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
            }
            document.getElementById('search-input').addEventListener('input', function() {
                const q = norm(this.value.trim());
                document.querySelectorAll('.lib-book-col').forEach(col => {
                    const hit = norm(col.dataset.nombre).includes(q) ||
                        norm(col.dataset.autor).includes(q);
                    col.classList.toggle('hidden', !hit);
                });
            });

            /* ---- Typewriter placeholder ---- */
            const inp = document.getElementById('search-input');
            const texts = ['Educación intercultural bilingüe...', 'Metodología Montessori...',
                'El docente como educador...'
            ];
            let ti = 0,
                ci = 0;

            function typeOn() {
                if (ci < texts[ti].length) {
                    inp.placeholder += texts[ti][ci++];
                    setTimeout(typeOn, 90);
                } else setTimeout(typeOff, 2200);
            }

            function typeOff() {
                if (ci > 0) {
                    inp.placeholder = inp.placeholder.slice(0, --ci);
                    setTimeout(typeOff, 42);
                } else {
                    ti = (ti + 1) % texts.length;
                    setTimeout(typeOn, 500);
                }
            }
            setTimeout(typeOn, 900);

            /* ---- FAB: scroll al sidebar, se oculta cuando sidebar es visible ---- */
            const fab = document.getElementById('lib-sidebar-fab');
            const sidebar = document.getElementById('lib-sidebar');
            if (fab && sidebar) {
                fab.addEventListener('click', () =>
                    sidebar.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    })
                );
                new IntersectionObserver(([e]) => {
                    if (e.isIntersecting) {
                        gsap.to(fab, {
                            y: 70,
                            opacity: 0,
                            duration: .28,
                            ease: 'power2.in',
                            onComplete: () => {
                                fab.style.pointerEvents = 'none';
                            }
                        });
                    } else {
                        fab.style.pointerEvents = 'auto';
                        gsap.to(fab, {
                            y: 0,
                            opacity: 1,
                            duration: .35,
                            ease: 'power2.out'
                        });
                    }
                }, {
                    threshold: 0.15
                }).observe(sidebar);
            }

            /* ---- Sort with GSAP Flip ---- */
            const ordenActual = {
                nombre: 'asc',
                autor: 'asc'
            };
            window.ordenarPor = function(campo, btn) {
                const wrap = document.getElementById('contenedorLibros');
                const cards = Array.from(wrap.querySelectorAll('.lib-book-col'));
                const state = Flip.getState(cards);
                const orden = ordenActual[campo];

                cards.sort((a, b) => {
                    const vA = a.dataset[campo].toLowerCase();
                    const vB = b.dataset[campo].toLowerCase();
                    return orden === 'asc' ?
                        vA.localeCompare(vB, 'es') :
                        vB.localeCompare(vA, 'es');
                });
                cards.forEach(c => wrap.appendChild(c));

                Flip.from(state, {
                    duration: .48,
                    ease: 'power2.inOut',
                    stagger: .025
                });
                ordenActual[campo] = orden === 'asc' ? 'desc' : 'asc';

                document.querySelectorAll('.lib-sort-btn').forEach(b => b.classList.remove('active'));
                if (btn) btn.classList.add('active');
            };
        })();
    </script>

@endsection
