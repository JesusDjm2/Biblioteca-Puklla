@php
    $isBiblioteca = request()->routeIs('index', 'libros', 'libros.*', 'categorias.*', 'secciones.*', 'detallesCat');
    $isVideos = request()->routeIs('videos', 'videos.*', 'videoscat.*');
    $isTaqe = request()->routeIs('taqe', 'taqe.*');
@endphp
<header class="puklla-main-nav">
    <nav class="menuPuklla" aria-label="Puklla Virtual">
        <div class="menu-container">
            <div class="menu-bar">
                <a href="{{ route('index') }}" class="menu-brand">
                    <img src="{{ asset('img/logo-iesp-pukllasunchis.png') }}" alt="IESP Pukllasunchis — inicio"
                        class="logo" width="168" decoding="async">
                </a>

                <button type="button" class="menu-toggle" id="menuToggle" aria-label="Abrir menú" aria-expanded="false"
                    aria-controls="menuSidebar">
                    <span class="hamburger-line" aria-hidden="true"></span>
                    <span class="hamburger-line" aria-hidden="true"></span>
                    <span class="hamburger-line" aria-hidden="true"></span>
                </button>
            </div>

            <div class="menu-overlay" id="menuOverlay" aria-hidden="true"></div>

            <div class="menu-sidebar" id="menuSidebar" role="navigation" aria-label="Menú principal">
                <div class="menu-sidebar-header">
                    <span class="menu-sidebar-title">Menú</span>
                    <button type="button" class="menu-close" id="menuClose" aria-label="Cerrar menú">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="menu-sidebar-scroll">
                    <ul class="menu-nav menu-nav--primary">
                        <li class="menu-item">
                            <a href="{{ route('index') }}"
                                class="menu-link {{ request()->routeIs('index') ? 'is-active' : '' }}"
                                @if (request()->routeIs('index')) aria-current="page" @endif>
                                
                                <span>Inicio</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('libros') }}"
                                class="menu-link {{ $isBiblioteca && !request()->routeIs('index') ? 'is-active' : '' }}"
                                @if ($isBiblioteca && !request()->routeIs('index')) aria-current="page" @endif>
                                
                                <span>Biblioteca</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('videos') }}" class="menu-link {{ $isVideos ? 'is-active' : '' }}"
                                @if ($isVideos) aria-current="page" @endif>
                               
                                <span>Videos</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('taqe.index') }}" class="menu-link {{ $isTaqe ? 'is-active' : '' }}"
                                @if ($isTaqe) aria-current="page" @endif>
                                <span>Taq'e</span>
                            </a>
                        </li>
                        {{-- Eliminado: Canciones --}}
                    </ul>

                    <div class="menu-actions">
                        <button type="button" class="menu-link menu-link--search search-toggle" id="searchToggle"
                            aria-expanded="false" aria-controls="searchBar" title="Buscar">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <span class="menu-link__label">Buscar</span>
                        </button>

                        @auth
                            <a href="{{ route('home') }}" class="menu-link menu-link--login" title="Mi cuenta">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <span class="menu-link__label">Mi cuenta</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="menu-link menu-link--login" title="Acceder">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <span class="menu-link__label">Acceder</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- position:fixed; no suma altura: el sticky lo sigue siendo solo la barra del nav -->
    <div class="search-bar" id="searchBar" role="search" aria-label="Buscar en la biblioteca virtual"
        aria-hidden="true">
        <div class="search-bar__panel">
            <form action="{{ route('busqueda') }}" method="GET" class="search-form-custom">
                <i class="fas fa-search search-form-custom__icon" aria-hidden="true"></i>
                <input type="text" name="query" placeholder="¿Qué estás buscando?" autocomplete="off">
                <button type="submit" aria-label="Buscar"><i class="fas fa-arrow-right"></i></button>
                <button type="button" class="close-search-bar" aria-label="Cerrar buscador">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const menuSidebar = document.getElementById('menuSidebar');
            const menuOverlay = document.getElementById('menuOverlay');
            const menuClose = document.getElementById('menuClose');
            const searchBar = document.getElementById('searchBar');
            const closeSearchBar = document.querySelector('.close-search-bar');
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

            // Crear overlay del buscador si no existe
            let searchOverlay = document.querySelector('.search-overlay');
            if (!searchOverlay) {
                searchOverlay = document.createElement('div');
                searchOverlay.className = 'search-overlay';
                document.body.appendChild(searchOverlay);
            }

            const searchToggleBtn = document.getElementById('searchToggle');

            // Toggle menú móvil
            function toggleMenu() {
                const open = !menuSidebar.classList.contains('active');
                menuToggle.classList.toggle('active', open);
                menuSidebar.classList.toggle('active', open);
                menuOverlay.classList.toggle('active', open);
                menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                menuOverlay.setAttribute('aria-hidden', open ? 'false' : 'true');
                document.body.classList.toggle('menu-drawer-open', open);
                document.body.style.overflow = open ? 'hidden' : '';

                // Cerrar buscador si está abierto
                if (open && searchBar.classList.contains('active')) {
                    toggleSearch();
                }

                // Entrada escalonada de los ítems (solo móvil, solo GSAP si está cargado)
                if (open && window.innerWidth <= 767 && typeof gsap !== 'undefined') {
                    const items = menuSidebar.querySelectorAll('.menu-sidebar-scroll > *');
                    gsap.fromTo(items, { x: -14, opacity: 0 }, {
                        x: 0, opacity: 1, duration: .35, stagger: .05, ease: 'power2.out', overwrite: true
                    });
                }
            }

            // Toggle buscador
            function toggleSearch() {
                const open = !searchBar.classList.contains('active');
                searchBar.classList.toggle('active', open);
                searchOverlay.classList.toggle('active', open);
                if (searchToggleBtn) {
                    searchToggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                searchBar.setAttribute('aria-hidden', open ? 'false' : 'true');
                document.body.classList.toggle('search-open', open);

                if (open) {
                    searchBar.querySelector('input')?.focus();
                }

                // Cerrar menú móvil si está abierto
                if (open && menuSidebar.classList.contains('active')) {
                    menuToggle.classList.remove('active');
                    menuSidebar.classList.remove('active');
                    menuOverlay.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuOverlay.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('menu-drawer-open');
                    document.body.style.overflow = '';
                }
            }

            // Cabecera transparente sobre el hero (cualquier vista con hero, antes del scroll)
            const mainNav = document.querySelector('.puklla-main-nav');
            if (mainNav) {
                const scrollThreshold = 60;
                let onNavScroll = function() {
                    const scrolled = (window.scrollY || window.pageYOffset) > scrollThreshold;
                    mainNav.classList.toggle('is-scrolled', scrolled);
                };
                window.addEventListener('scroll', onNavScroll, { passive: true });
                onNavScroll();
            }

            // Event listeners
            menuToggle?.addEventListener('click', toggleMenu);
            menuClose?.addEventListener('click', toggleMenu);
            menuOverlay?.addEventListener('click', toggleMenu);

            searchToggleBtn?.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSearch();
            });

            closeSearchBar?.addEventListener('click', toggleSearch);
            searchOverlay?.addEventListener('click', toggleSearch);

            // Cerrar con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (menuSidebar?.classList.contains('active')) {
                        toggleMenu();
                    }
                    if (searchBar?.classList.contains('active')) {
                        toggleSearch();
                    }
                }
            });

            // Dropdowns en móvil
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    if (window.innerWidth <= 767) {
                        e.preventDefault();
                        const dropdownMenu = this.nextElementSibling;
                        this.classList.toggle('active');
                        dropdownMenu.classList.toggle('active');
                    }
                });
            });

            // Cerrar dropdowns en desktop
            document.addEventListener('click', function(e) {
                if (window.innerWidth > 767) {
                    dropdownToggles.forEach(toggle => {
                        const dropdownMenu = toggle.nextElementSibling;
                        if (!toggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                            toggle.classList.remove('active');
                            dropdownMenu.classList.remove('active');
                        }
                    });
                }
            });

            // Resize: cerrar drawer al pasar a escritorio
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 767) {
                        menuSidebar?.classList.remove('active');
                        menuToggle?.classList.remove('active');
                        menuOverlay?.classList.remove('active');
                        menuToggle?.setAttribute('aria-expanded', 'false');
                        menuOverlay?.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('menu-drawer-open');
                        document.body.style.overflow = '';
                    }
                }, 200);
            }, {
                passive: true
            });

            // Búsqueda con Enter
            const searchInput = document.querySelector('.search-bar input');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const query = this.value.trim();
                        if (query.length >= 3) {
                            window.location.href = "{{ route('busqueda') }}?query=" + encodeURIComponent(
                                query);
                        }
                    }
                });
            }
        });
    </script>
</header>
