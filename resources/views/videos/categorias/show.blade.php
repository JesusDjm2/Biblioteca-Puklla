@extends('layouts.padre')
@section('titulo', '- ' . $categoria->nombre)

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        .video-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(42, 37, 32, .07);
            border: 1px solid rgba(200, 142, 65, .1);
            transition: box-shadow .28s ease, transform .28s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .video-card:hover {
            box-shadow: 0 8px 28px rgba(42, 37, 32, .14);
            transform: translateY(-3px);
        }

        .video-card__thumb {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #1a1612;
            flex-shrink: 0;
            cursor: pointer;
        }

        .video-card__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }

        .video-card:hover .video-card__thumb img {
            transform: scale(1.05);
        }

        .video-card__overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.6rem;
            color: rgba(255, 255, 255, .92);
            text-shadow: 0 2px 10px rgba(0, 0, 0, .5);
            background: rgba(20, 16, 12, .18);
            transition: background .25s ease, transform .25s ease;
            pointer-events: none;
        }

        .video-card:hover .video-card__overlay {
            background: rgba(20, 16, 12, .32);
            transform: scale(1.06);
        }

        .video-card__drive-wrap {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .video-card__drive-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .video-card__body {
            padding: 10px 12px 12px;
            border-top: 2px solid rgba(200, 142, 65, .2);
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .video-card__title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: .9rem;
            font-weight: 600;
            color: #2a2520;
            margin: 0;
            line-height: 1.35;
        }

        .video-card__desc {
            font-size: .75rem;
            color: #9c8c7c;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .vl-search-wrap {
            position: relative;
            max-width: 400px;
        }

        .vl-search-wrap .vl-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b8a898;
            font-size: .85rem;
            pointer-events: none;
        }

        .vl-search-input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1px solid rgba(200, 142, 65, .25);
            border-radius: 999px;
            font-size: .85rem;
            color: #2a2520;
            background: #fff;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .vl-search-input:focus {
            border-color: #cb9042;
            box-shadow: 0 0 0 3px rgba(203, 144, 66, .15);
        }

        .vl-search-input::placeholder {
            color: #b8a898;
        }

        .vl-sidebar {
            position: sticky;
            top: 6rem;
        }

        .vl-sidebar__title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: #2a2520;
            margin-bottom: .8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vl-sidebar__list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .vl-sidebar__item+.vl-sidebar__item {
            margin-top: 2px;
        }

        .vl-sidebar__link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            color: #5a4a3a;
            font-size: .84rem;
            text-decoration: none;
            transition: background .2s, color .2s;
            border-left: 3px solid transparent;
        }

        .vl-sidebar__link:hover {
            background: rgba(203, 144, 66, .08);
            color: #2a2520;
        }

        .vl-sidebar__link.is-active {
            background: rgba(203, 144, 66, .12);
            color: #a06e2e;
            border-left-color: #cb9042;
            font-weight: 600;
        }

        .vl-sidebar__count {
            font-size: .72rem;
            background: rgba(200, 142, 65, .1);
            color: #8a7a6a;
            padding: 1px 8px;
            border-radius: 999px;
        }

        .vl-sidebar__link.is-active .vl-sidebar__count {
            background: rgba(203, 144, 66, .2);
            color: #a06e2e;
        }

        .vl-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #b8a898;
            background: #fff;
            border: 1px solid rgba(200, 142, 65, .13);
            border-radius: 12px;
        }

        .vl-empty i {
            font-size: 2.6rem;
            margin-bottom: .8rem;
            display: block;
            color: #cb9042;
            opacity: .5;
        }

        .vl-empty p {
            font-size: .9rem;
            margin: 0;
        }

        .vl-cta-wrap {
            margin-top: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .vl-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 1.5rem;
            }
        }
    </style>
@endsection

@section('contenido')
    <div class="lib-hero">
        <img src="{{ asset('img/FIlmadora-Puklla-Virtual-Fondo.webp') }}" alt="{{ $categoria->nombre }}" class="lib-hero__bg"
            loading="eager">
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">ESPP Pukllasunchis</p>
            <h1 class="lib-hero__title">{{ $categoria->nombre }}</h1>
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                <span><i class="fa fa-film"></i> {{ $videos->count() }}
                    {{ $videos->count() === 1 ? 'video' : 'videos' }}</span>
            </div>
        </div>
    </div>
    <button id="vl-fab" class="lib-sidebar-fab" aria-label="Ver secciones">
        <i class="fa fa-folder-open"></i> Secciones
    </button>
    <div class="container py-4 py-lg-5">
        <div class="row g-4">
            <div class="col-lg-3 order-2 order-lg-1">
                <aside class="vl-sidebar" id="vl-sidebar">
                    <h4 class="vl-sidebar__title">
                        <i class="fa fa-folder-open"></i> Secciones
                    </h4>
                    <ul class="vl-sidebar__list">
                        <li class="vl-sidebar__item">
                            <a href="{{ route('videos') }}" class="vl-sidebar__link">
                                <span>Todos los videos</span>
                                <span class="vl-sidebar__count">{{ $secciones->sum('videos_count') }}</span>
                            </a>
                        </li>
                        @foreach ($secciones as $sec)
                            <li class="vl-sidebar__item">
                                <a href="{{ route('videoscat.show', $sec->url) }}"
                                    class="vl-sidebar__link {{ $sec->id === $categoria->id ? 'is-active' : '' }}">
                                    <span>{{ $sec->nombre }}</span>
                                    <span class="vl-sidebar__count">{{ $sec->videos_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            </div>

            <div class="col-lg-9 order-1 order-lg-2">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                    <div class="lib-content-head mb-0">
                        <h2 class="lib-content-head__title">{{ $categoria->nombre }}</h2>
                        <p class="lib-content-head__sub">{{ $videos->count() }}
                            {{ $videos->count() === 1 ? 'video' : 'videos' }} en esta sección</p>
                    </div>
                    <div class="vl-search-wrap">
                        <i class="fa fa-search vl-search-icon"></i>
                        <input type="text" id="vl-search" class="vl-search-input" placeholder="Buscar en esta sección…"
                            autocomplete="off">
                    </div>
                </div>

                <div class="row g-3" id="vl-grid">
                    @forelse ($videos as $video)
                        <div class="col-6 col-md-4 col-lg-4 vl-item" data-nombre="{{ mb_strtolower($video->nombre) }}">
                            <div class="video-card">
                                <div class="video-card__thumb">
                                    @if ($video->youtube)
                                        <a href="https://www.youtube.com/watch?v={{ $video->youtube }}" target="_blank"
                                            rel="noopener" style="display:block;width:100%;height:100%;position:relative;">
                                            <img src="https://img.youtube.com/vi/{{ $video->youtube }}/hqdefault.jpg"
                                                alt="{{ $video->nombre }}" loading="lazy">
                                            <span class="video-card__overlay"><i class="fab fa-youtube"></i></span>
                                        </a>
                                    @elseif ($video->drive)
                                        <div class="video-card__drive-wrap">
                                            <iframe src="https://drive.google.com/file/d/{{ $video->drive }}/preview"
                                                loading="lazy" allowfullscreen></iframe>
                                        </div>
                                    @endif
                                </div>
                                <div class="video-card__body">
                                    <h3 class="video-card__title" title="{{ $video->nombre }}">{{ $video->nombre }}</h3>
                                    @if ($video->descripcion)
                                        <p class="video-card__desc">{{ Str::limit($video->descripcion, 80) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="vl-empty">
                                <i class="fa fa-film"></i>
                                <p>Aún no hay videos en esta sección.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div id="vl-noresults" class="vl-empty" style="display:none;">
                    <i class="fa fa-search"></i>
                    <p>No se encontraron videos con ese nombre.</p>
                </div>

                <div class="vl-cta-wrap">
                    <a href="{{ route('videos') }}" class="lib-sidebar-anchor">
                        <i class="fa fa-arrow-left"></i> Ver todos los videos
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function() {
            gsap.registerPlugin(ScrollTrigger);
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
            gsap.set('.lib-hero__eyebrow', {
                y: 22
            });
            gsap.set('.lib-hero__title', {
                y: 30
            });
            gsap.set('.lib-hero__meta', {
                y: 16
            });
            gsap.timeline({
                    defaults: {
                        ease: 'power3.out'
                    }
                })
                .to('.lib-hero__eyebrow', {
                    opacity: 1,
                    y: 0,
                    duration: .75
                }, .2)
                .to('.lib-hero__title', {
                    opacity: 1,
                    y: 0,
                    duration: .85
                }, .4)
                .to('.lib-hero__divider', {
                    width: 56,
                    duration: .5
                }, .8)
                .to('.lib-hero__meta', {
                    opacity: 1,
                    y: 0,
                    duration: .6
                }, 1.0);
            gsap.from('.vl-sidebar', {
                x: -18,
                opacity: 0,
                duration: .45,
                ease: 'power2.out',
                delay: .15
            });
            gsap.from('.vl-item', {
                y: 26,
                duration: .4,
                stagger: .05,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: '#vl-grid',
                    start: 'top 90%',
                    once: true
                }
            });

            var input = document.getElementById('vl-search');
            var items = document.querySelectorAll('.vl-item');
            var noResults = document.getElementById('vl-noresults');
            var grid = document.getElementById('vl-grid');
            if (input) {
                function norm(s) {
                    return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
                }
                input.addEventListener('input', function() {
                    var q = norm(this.value.trim());
                    var visible = 0;
                    items.forEach(function(it) {
                        var match = q === '' || norm(it.getAttribute('data-nombre') || '').includes(q);
                        it.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    if (noResults) noResults.style.display = (q !== '' && visible === 0) ? 'block' : 'none';
                    if (grid) grid.style.display = (q !== '' && visible === 0) ? 'none' : '';
                });
                var texts = ['Buscar en esta sección…', 'Filtrar por nombre…', '¿Qué video buscas?…'];
                var ti = 0,
                    ci = 0;

                function typeOn() {
                    if (ci < texts[ti].length) {
                        input.placeholder += texts[ti][ci++];
                        setTimeout(typeOn, 90);
                    } else setTimeout(typeOff, 2200);
                }

                function typeOff() {
                    if (ci > 0) {
                        input.placeholder = input.placeholder.slice(0, --ci);
                        setTimeout(typeOff, 42);
                    } else {
                        ti = (ti + 1) % texts.length;
                        setTimeout(typeOn, 500);
                    }
                }
                setTimeout(typeOn, 900);
            }
            var fab = document.getElementById('vl-fab');
            var sidebar = document.getElementById('vl-sidebar');
            if (fab && sidebar) {
                fab.addEventListener('click', function() {
                    sidebar.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
                new IntersectionObserver(function(entries) {
                    var e = entries[0];
                    if (e.isIntersecting) {
                        gsap.to(fab, {
                            y: 70,
                            opacity: 0,
                            duration: .28,
                            ease: 'power2.in',
                            onComplete: function() {
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
        })();
    </script>
@endsection
