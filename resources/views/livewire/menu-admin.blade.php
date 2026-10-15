@php
    $inGroup = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p));
    $rolActual = auth()->user()->getRoleNames()->first();
@endphp
<nav id="sidebar">
    <div class="bv-sidebar-brand">
        <a href="{{ route('home') }}" class="logo"><img src="{{ asset('img/Asociacion-logo-blanco.png') }}"
                width="100%" alt="ESPP Pukllasunchis"></a>
    </div>

    <div class="bv-sidebar-user">
        <div class="bv-sidebar-user__avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div class="bv-sidebar-user__info">
            <span class="bv-sidebar-user__name">{{ explode(' ', Auth::user()->name)[0] }}</span>
            @if ($rolActual)
                <span class="bv-sidebar-user__role">{{ ucfirst($rolActual) }}</span>
            @endif
        </div>
        <button type="button" id="bvThemeToggle" class="bv-theme-toggle" title="Cambiar tema"
            aria-label="Cambiar entre tema claro y oscuro">
            <i class="fa fa-moon-o" data-theme-icon="dark"></i>
            <i class="fa fa-sun-o" data-theme-icon="light"></i>
        </button>
    </div>

    <div class="bv-sidebar-nav">
        <ul class="list-unstyled components mb-4">
            @if (auth()->user()->hasRole('admin'))
                @php $openBibVirt = $inGroup(['libros.*', 'categorias.*', 'secciones.*']); @endphp
                <li class="sidebar-menu-label">Biblioteca</li>
                <li>
                    <a href="#admBibVirt" data-toggle="collapse" aria-expanded="{{ $openBibVirt ? 'true' : 'false' }}"
                        class="dropdown-toggle"><i class="fa fa-book"></i> Biblioteca virtual</a>
                    <ul class="collapse list-unstyled {{ $openBibVirt ? 'show' : '' }}" id="admBibVirt">
                        <li class="{{ request()->routeIs('libros.*') ? 'active' : '' }}">
                            <a href="{{ route('libros.index') }}"><span class="flecha-admin">→</span> Libros</a>
                        </li>
                        <li class="{{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                            <a href="{{ route('categorias.index') }}"><span class="flecha-admin">→</span> Categorías</a>
                        </li>
                        <li class="{{ request()->routeIs('secciones.*') ? 'active' : '' }}">
                            <a href="{{ route('secciones.index') }}"><span class="flecha-admin">→</span> Secciones</a>
                        </li>
                    </ul>
                </li>

                @php
                    $openBibFis = $inGroup([
                        'biblioteca-fisica.categorias.*',
                        'biblioteca-fisica.libros.*',
                        'biblioteca-fisica.prestamos.*',
                        'biblioteca-fisica.material-categorias.*',
                        'biblioteca-fisica.materiales.*',
                    ]);
                @endphp
                <li>
                    <a href="#admBibFis" data-toggle="collapse" aria-expanded="{{ $openBibFis ? 'true' : 'false' }}"
                        class="dropdown-toggle"><i class="fa fa-archive"></i> Biblioteca física</a>
                    <ul class="collapse list-unstyled {{ $openBibFis ? 'show' : '' }}" id="admBibFis">
                        <li class="{{ request()->routeIs('biblioteca-fisica.categorias.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.categorias.index') }}"><span
                                    class="flecha-admin">→</span> Categorías</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.libros.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.libros.index') }}"><span class="flecha-admin">→</span>
                                Libros</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.prestamos.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.prestamos.index') }}"><span class="flecha-admin">→</span>
                                Préstamos</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.material-categorias.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.material-categorias.index') }}"><span
                                    class="flecha-admin">→</span> Cat. material didáctico</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.materiales.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.materiales.index') }}"><span
                                    class="flecha-admin">→</span> Material didáctico</a>
                        </li>
                    </ul>
                </li>

                @php $openCanciones = $inGroup(['canciones.*', 'cancionescat.*']); @endphp
                <li class="sidebar-menu-label">Multimedia</li>
                <li>
                    <a href="#admCanciones" data-toggle="collapse"
                        aria-expanded="{{ $openCanciones ? 'true' : 'false' }}" class="dropdown-toggle"><i
                            class="fa fa-music"></i> Canciones</a>
                    <ul class="collapse list-unstyled {{ $openCanciones ? 'show' : '' }}" id="admCanciones">
                        <li class="{{ request()->routeIs('canciones.*') ? 'active' : '' }}">
                            <a href="{{ route('canciones.index') }}"><span class="flecha-admin">→</span> Canciones</a>
                        </li>
                        <li class="{{ request()->routeIs('cancionescat.*') ? 'active' : '' }}">
                            <a href="{{ route('cancionescat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>
                @php $openVideos = $inGroup(['videos.*', 'videoscat.*']); @endphp
                <li>
                    <a href="#admVideos" data-toggle="collapse" aria-expanded="{{ $openVideos ? 'true' : 'false' }}"
                        class="dropdown-toggle"><i class="fa fa-video-camera"></i> Videos</a>
                    <ul class="collapse list-unstyled {{ $openVideos ? 'show' : '' }}" id="admVideos">
                        <li class="{{ request()->routeIs('videos.*') ? 'active' : '' }}">
                            <a href="{{ route('videos.index') }}"><span class="flecha-admin">→</span> Videos</a>
                        </li>
                        <li class="{{ request()->routeIs('videoscat.*') ? 'active' : '' }}">
                            <a href="{{ route('videoscat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-menu-label">Otros</li>
                <li>
                    <a href="#admSisicha" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-graduation-cap"></i> Sisichakunay</a>
                    <ul class="collapse list-unstyled" id="admSisicha">
                        <li><a href="#">Página 1</a></li>
                        <li><a href="#">Página 2</a></li>
                        <li><a href="#">Página 3</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#admTake" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-building"></i> Take</a>
                    <ul class="collapse list-unstyled" id="admTake">
                        <li><a href="#">Página 1</a></li>
                        <li><a href="#">Página 2</a></li>
                        <li><a href="#">Página 3</a></li>
                    </ul>
                </li>

                @php $openUsuarios = $inGroup(['admin.*', 'alumnos']); @endphp
                <li class="sidebar-menu-label">Usuarios</li>
                <li>
                    <a href="#admUsers" data-toggle="collapse" aria-expanded="{{ $openUsuarios ? 'true' : 'false' }}"
                        class="dropdown-toggle"><i class="fa fa-users"></i> Cuentas</a>
                    <ul class="collapse list-unstyled {{ $openUsuarios ? 'show' : '' }}" id="admUsers">
                        <li class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.index') }}"><span class="flecha-admin">→</span>
                                Administradores</a>
                        </li>
                        <li class="{{ request()->routeIs('alumnos') ? 'active' : '' }}">
                            <a href="{{ route('alumnos') }}"><span class="flecha-admin">→</span> Alumnos</a>
                        </li>
                    </ul>
                </li>
            @elseif (auth()->user()->hasRole('biblioteca'))
                @php
                    $openBibVirt = $inGroup(['secciones.*', 'categorias.*', 'libros.*']);
                    $openBibFis = $inGroup([
                        'biblioteca-fisica.categorias.*',
                        'biblioteca-fisica.libros.*',
                        'biblioteca-fisica.prestamos.*',
                        'biblioteca-fisica.material-categorias.*',
                        'biblioteca-fisica.materiales.*',
                    ]);
                @endphp
                <li class="sidebar-menu-label">Administración Biblioteca</li>
                <li>
                    <a href="#bibVirt" data-toggle="collapse" aria-expanded="{{ $openBibVirt ? 'true' : 'false' }}"
                        class="dropdown-toggle"><i class="fa fa-book"></i> Biblioteca virtual</a>
                    <ul class="collapse list-unstyled {{ $openBibVirt ? 'show' : '' }}" id="bibVirt">
                        <li class="{{ request()->routeIs('secciones.*') ? 'active' : '' }}">
                            <a href="{{ route('secciones.index') }}"><span class="flecha-admin">→</span>
                                Secciones</a>
                        </li>
                        <li class="{{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                            <a href="{{ route('categorias.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                        <li class="{{ request()->routeIs('libros.*') ? 'active' : '' }}">
                            <a href="{{ route('libros.index') }}"><span class="flecha-admin">→</span> Libros</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#bibFis" data-toggle="collapse" aria-expanded="{{ $openBibFis ? 'true' : 'false' }}"
                        class="dropdown-toggle"><i class="fa fa-archive"></i> Biblioteca física</a>
                    <ul class="collapse list-unstyled {{ $openBibFis ? 'show' : '' }}" id="bibFis">
                        <li class="{{ request()->routeIs('biblioteca-fisica.categorias.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.categorias.index') }}"><span
                                    class="flecha-admin">→</span> Categorías</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.libros.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.libros.index') }}"><span
                                    class="flecha-admin">→</span> Libros</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.prestamos.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.prestamos.index') }}"><span
                                    class="flecha-admin">→</span> Préstamos</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.material-categorias.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.material-categorias.index') }}"><span
                                    class="flecha-admin">→</span> Cat. material didáctico</a>
                        </li>
                        <li class="{{ request()->routeIs('biblioteca-fisica.materiales.*') ? 'active' : '' }}">
                            <a href="{{ route('biblioteca-fisica.materiales.index') }}"><span
                                    class="flecha-admin">→</span> Material didáctico</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (!auth()->user()->hasRole('admin') && auth()->user()->hasRole('videos'))
                @php $openRolVideos = $inGroup(['videos.*', 'videoscat.*']); @endphp
                <li class="sidebar-menu-label">Videos</li>
                <li>
                    <a href="#rolVideos" data-toggle="collapse"
                        aria-expanded="{{ $openRolVideos ? 'true' : 'false' }}" class="dropdown-toggle"><i
                            class="fa fa-video-camera"></i> Videos</a>
                    <ul class="collapse list-unstyled {{ $openRolVideos ? 'show' : '' }}" id="rolVideos">
                        <li class="{{ request()->routeIs('videos.*') ? 'active' : '' }}">
                            <a href="{{ route('videos.index') }}"><span class="flecha-admin">→</span> Videos</a>
                        </li>
                        <li class="{{ request()->routeIs('videoscat.*') ? 'active' : '' }}">
                            <a href="{{ route('videoscat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (!auth()->user()->hasRole('admin') && auth()->user()->hasRole('audios'))
                @php $openRolAudios = $inGroup(['canciones.*', 'cancionescat.*']); @endphp
                <li class="sidebar-menu-label">Canciones</li>
                <li>
                    <a href="#rolAudios" data-toggle="collapse"
                        aria-expanded="{{ $openRolAudios ? 'true' : 'false' }}" class="dropdown-toggle"><i
                            class="fa fa-music"></i> Canciones</a>
                    <ul class="collapse list-unstyled {{ $openRolAudios ? 'show' : '' }}" id="rolAudios">
                        <li class="{{ request()->routeIs('canciones.*') ? 'active' : '' }}">
                            <a href="{{ route('canciones.index') }}"><span class="flecha-admin">→</span>
                                Canciones</a>
                        </li>
                        <li class="{{ request()->routeIs('cancionescat.*') ? 'active' : '' }}">
                            <a href="{{ route('cancionescat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>

    <div class="bv-sidebar-footer">
        <a href="{{ route('index') }}" class="bv-sidebar-link"><i class="fa fa-home"></i> Página de inicio</a>
        <a href="#" class="bv-sidebar-link bv-sidebar-link--danger" id="bvLogoutLink">
            <i class="fa fa-sign-out"></i> {{ __('Salir') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <div class="bv-sidebar-copy">
        <p class="mb-0">Copyright &copy; <span id="bvSidebarYear"></span><br>Made by
            <a href="https://www.facebook.com/DjmWebMaster" target="_blank" rel="noopener">DJM2</a>
        </p>
    </div>
</nav>
