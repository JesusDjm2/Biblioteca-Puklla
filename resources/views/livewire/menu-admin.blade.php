<nav id="sidebar">
    <div class="custom-menu">
        <button type="button" id="sidebarCollapse" class="btn btn-primary">
            <i class="fa fa-bars"></i>
            <span class="sr-only">Toggle Menu</span>
        </button>
    </div>
    <div class="p-4 pt-5">
        <h1><a href="{{ route('home') }}" class="logo"><img src="{{ asset('img/Asociacion-logo-blanco.png') }}"
                    width="100%" alt=""></a></h1>
        <ul class="list-unstyled components mb-5">
            @if (auth()->user()->hasRole('admin'))
                <li class="sidebar-menu-label">Biblioteca</li>
                <li>
                    <a href="#admBibVirt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-book"></i> Biblioteca virtual</a>
                    <ul class="collapse list-unstyled" id="admBibVirt">
                        <li>
                            <a href="{{ route('libros.index') }}"><span class="flecha-admin">→</span> Libros</a>
                        </li>
                        <li>
                            <a href="{{ route('categorias.index') }}"><span class="flecha-admin">→</span> Categorías</a>
                        </li>
                        <li>
                            <a href="{{ route('secciones.index') }}"><span class="flecha-admin">→</span> Secciones</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#admBibFis" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-archive"></i> Biblioteca física</a>
                    <ul class="collapse list-unstyled" id="admBibFis">
                        <li>
                            <a href="{{ route('biblioteca-fisica.categorias.index') }}"><span
                                    class="flecha-admin">→</span> Categorías</a>
                        </li>
                        <li>
                            <a href="{{ route('biblioteca-fisica.libros.index') }}"><span class="flecha-admin">→</span>
                                Libros</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-menu-label">Multimedia</li>
                <li>
                    <a href="#admCanciones" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-music"></i> Canciones</a>
                    <ul class="collapse list-unstyled" id="admCanciones">
                        <li>
                            <a href="{{ route('canciones.index') }}"><span class="flecha-admin">→</span> Canciones</a>
                        </li>
                        <li>
                            <a href="{{ route('cancionescat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#admVideos" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-video-camera"></i> Videos</a>
                    <ul class="collapse list-unstyled" id="admVideos">
                        <li>
                            <a href="{{ route('videos.index') }}"><span class="flecha-admin">→</span> Videos</a>
                        </li>
                        <li>
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

                <li class="sidebar-menu-label">Usuarios</li>
                <li>
                    <a href="#admUsers" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-users"></i> Cuentas</a>
                    <ul class="collapse list-unstyled" id="admUsers">
                        <li>
                            <a href="{{ route('admin.index') }}"><span class="flecha-admin">→</span>
                                Administradores</a>
                        </li>
                        <li>
                            <a href="{{ route('alumnos') }}"><span class="flecha-admin">→</span> Alumnos</a>
                        </li>
                    </ul>
                </li>
            @elseif (auth()->user()->hasRole('biblioteca'))
                <li class="sidebar-menu-label" style="font-size:8px; font-weight: bold;">Administración Biblioteca</li>
                <li>
                    <a href="#bibVirt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-book"></i> Biblioteca virtual</a>
                    <ul class="collapse list-unstyled" id="bibVirt">
                        <li>
                            <a href="{{ route('secciones.index') }}"><span class="flecha-admin">→</span>
                                Secciones</a>
                        </li>
                        <li>
                            <a href="{{ route('categorias.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                        <li>
                            <a href="{{ route('libros.index') }}"><span class="flecha-admin">→</span> Libros</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#bibFis" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-archive"></i> Biblioteca física</a>
                    <ul class="collapse list-unstyled" id="bibFis">
                        <li>
                            <a href="{{ route('biblioteca-fisica.categorias.index') }}"><span
                                    class="flecha-admin">→</span> Categorías</a>
                        </li>
                        <li>
                            <a href="{{ route('biblioteca-fisica.libros.index') }}"><span
                                    class="flecha-admin">→</span> Libros</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (!auth()->user()->hasRole('admin') && auth()->user()->hasRole('videos'))
                <li class="sidebar-menu-label">Videos</li>
                <li>
                    <a href="#rolVideos" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-video-camera"></i> Videos</a>
                    <ul class="collapse list-unstyled" id="rolVideos">
                        <li>
                            <a href="{{ route('videos.index') }}"><span class="flecha-admin">→</span> Videos</a>
                        </li>
                        <li>
                            <a href="{{ route('videoscat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (!auth()->user()->hasRole('admin') && auth()->user()->hasRole('audios'))
                <li class="sidebar-menu-label">Canciones</li>
                <li>
                    <a href="#rolAudios" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                            class="fa fa-music"></i> Canciones</a>
                    <ul class="collapse list-unstyled" id="rolAudios">
                        <li>
                            <a href="{{ route('canciones.index') }}"><span class="flecha-admin">→</span>
                                Canciones</a>
                        </li>
                        <li>
                            <a href="{{ route('cancionescat.index') }}"><span class="flecha-admin">→</span>
                                Categorías</a>
                        </li>
                    </ul>
                </li>
            @endif

            <style>
                .sidebar-menu-label {
                    font-size: 0.68rem;
                    letter-spacing: 0.12em;
                    text-transform: uppercase;
                    opacity: 0.55;
                    color: #fff;
                    margin-top: 1.1rem;
                    margin-bottom: 0.35rem;
                    padding-left: 0.35rem;
                    list-style: none;
                }

                .sidebar-menu-label:first-child {
                    margin-top: 0;
                }

                .flecha-admin {
                    opacity: 0;
                    transform: translateX(-20px);
                    transition: opacity 0.5s ease, transform 0.5s ease;
                    display: inline-block;
                }

                a:hover .flecha-admin {
                    opacity: 1;
                    transform: translateX(0);
                }
            </style>

            <li class="sidebar-menu-label" style="font-size:8px; font-weight: bold;">Sitio</li>
            <li>
                <a href="{{ route('index') }}"><i class="fa fa-home"></i> Página de Inicio</a>
            </li>
            <li >
                <a class="nav-link text-white" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                    <i class="fa fa-user"></i> {{ explode(' ', Auth::user()->name)[0] }} → {{ __('Salir') }} <i class="fa fa-sign-out"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
        {{-- <div class="mb-5">
            <a class="nav-link text-white text-center" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                {{ Auth::user()->name }} — {{ __('Logout') }} <i class="fa fa-sign-out"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div> --}}
        <div class="footer">
            <p class="text-center">
                Copyright
                &copy;
                <script>
                    document.write(new Date().getFullYear());
                </script> <br> Made by <a href="" target="_blank">DJM2 </a>
            </p>
        </div>
    </div>
</nav>
