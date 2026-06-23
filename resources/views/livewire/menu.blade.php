@php
    $isBiblioteca = request()->routeIs('index', 'libros', 'libros.*', 'categorias.*', 'secciones.*', 'detallesCat');
    $isVideos = request()->routeIs('videos', 'videos.*', 'videoscat.*');
    $isCanciones = request()->routeIs('cancionesPuklla', 'canciones.*', 'cancionescat.*');
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
                            <a href="{{ route('cancionesPuklla') }}"
                                class="menu-link {{ $isCanciones ? 'is-active' : '' }}"
                                @if ($isCanciones) aria-current="page" @endif>

                                <span>Canciones</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <span class="menu-link menu-link--soon" tabindex="-1" aria-disabled="true">

                                <span>Sisichakunaq</span>
                            </span>
                        </li>
                    </ul>

                    <p class="menu-section-label menu-section-label--social">Síguenos</p>
                    <ul class="menu-nav menu-nav--social" aria-label="Redes sociales">
                        <li class="menu-item">
                            <a href="#" class="menu-link menu-link--icon"
                                aria-label="Facebook (enlace pendiente)">
                                <i class="fab fa-facebook-f" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link menu-link--icon" aria-label="Twitter (enlace pendiente)">
                                <i class="fab fa-twitter" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link menu-link--icon"
                                aria-label="Instagram (enlace pendiente)">
                                <i class="fab fa-instagram" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>

                    <p class="menu-section-label menu-section-label--auth">Acceso</p>
                    <div class="menu-login-wrap">
                        @auth
                            <a href="{{ route('home') }}" class="menu-link menu-link--login">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="menu-link menu-link--login">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </a>
                        @endauth
                    </div>

                    <div class="menu-search-wrap">
                        <button type="button" class="menu-link menu-link--search search-toggle" id="searchToggle"
                            aria-expanded="false" aria-controls="searchBar">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <span>Buscar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- position:fixed; no suma altura: el sticky lo sigue siendo solo la barra del nav -->
    <div class="search-bar" id="searchBar" role="search" aria-label="Buscar en la biblioteca virtual"
        aria-hidden="true">
        <form action="{{ route('busqueda') }}" method="GET" class="search-form-custom">
            <input type="text" name="query" placeholder="¿Qué estás buscando?" autocomplete="off">
            <button type="submit"><i class="fas fa-search"></i></button>
            <button type="button" class="close-search-bar"><i class="fas fa-times"></i></button>
        </form>
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
