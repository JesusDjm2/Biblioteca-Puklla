@extends('layouts.padre')
@section('titulo', '- Biblioteca virtual')
@section('contenido')
    <div class="fixed-image-container">
        <img src="{{ asset('img/biblioteca-muestra.webp') }}" alt="Fondo Pukllasunchis" loading="lazy">
        <div class="overlay"></div>
        <div class="centered-content">
            <h2>LIBROS</h2>
            <h1>ESPP Pukllasunchis</h1>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-lg-4 mt-4">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text btn btn-sm btn-primary" id="basic-addon1">Buscar</span>
                    </div>
                    <input type="text" id="search-input" placeholder="" class="form-control form-control-sm"
                        aria-label="Buscar libros por nombre o autor" aria-describedby="basic-addon1">
                </div>
                <div class="custom-sidebar">
                    <h4 class="custom-sidebar-title">Secciones:</h4>
                    <ul class="custom-sidebar-menu">
                        @foreach ($secciones as $seccion)
                            <li class="custom-sidebar-menu-item">
                                <a href="#" class="custom-sidebar-link custom-section-toggle">
                                    {{ $seccion->nombre }}
                                    <span class="custom-toggle-icon">▼</span>
                                </a>
                                <ul class="custom-sidebar-submenu">
                                    @foreach ($seccion->categorias as $categoria)
                                        <li class="custom-sidebar-submenu-item">
                                            <a href="{{ route('detallesCat', $categoria->id) }}"
                                                class="custom-sidebar-link">{{ $categoria->nombre }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row">
                    <h2 class="titulo2Puklla mb-5">Últimas publicaciones</h2>
                    @foreach ($libros as $libro)
                        <div class="col-lg-4 mb-4 cardLibros" data-nombre="{{ $libro->nombre }}"
                            data-autor="{{ $libro->autor }}">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class='text-center'>{{ $libro->nombre }}</h5>
                                    <p class="text-center"><i style="font-size: 12px"
                                            class="fa fa-user text-primary"></i>{{ $libro->autor }}</p>
                                    <iframe loading="lazy"
                                        src="https://drive.google.com/file/d/{{ $libro->identificador }}/preview"
                                        width="100%" height="280"></iframe>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        // Función para remover tildes y caracteres especiales
        function normalizeString(str) {
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const libros = document.querySelectorAll('.cardLibros');

            searchInput.addEventListener('input', function() {
                const searchTerm = normalizeString(this.value.trim());

                libros.forEach(libro => {
                    const nombre = normalizeString(libro.getAttribute('data-nombre'));
                    const autor = normalizeString(libro.getAttribute('data-autor'));
                    const matches = nombre.includes(searchTerm) || autor.includes(searchTerm);

                    if (matches) {
                        libro.style.display = 'block';
                    } else {
                        libro.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <script>
        //Tiperar texto en buscador:
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const texts = [
                'Educación en los niños de 5 a 8 años...',
                'Metodo montessori de educación en adolescentes...',
                'El docente como educador...'
            ];
            let textIndex = 0;
            let charIndex = 0;

            function typeText() {
                if (charIndex < texts[textIndex].length) {
                    searchInput.placeholder += texts[textIndex].charAt(charIndex);
                    charIndex++;
                    setTimeout(typeText, 100); // Velocidad de tipeo
                } else {
                    setTimeout(eraseText, 2000); // Tiempo antes de borrar el texto
                }
            }

            function eraseText() {
                if (charIndex > 0) {
                    searchInput.placeholder = searchInput.placeholder.slice(0, -1);
                    charIndex--;
                    setTimeout(eraseText, 50); // Velocidad de borrado
                } else {
                    textIndex = (textIndex + 1) % texts.length;
                    setTimeout(typeText, 500); // Tiempo antes de comenzar a tipear nuevamente
                }
            }

            setTimeout(typeText, 500); // Tiempo antes de comenzar la animación
        });

        //Menu lateral secciones:
        document.addEventListener('DOMContentLoaded', function() {
            var toggles = document.querySelectorAll('.custom-section-toggle');
            toggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    var icon = this.querySelector('.custom-toggle-icon');
                    var submenu = this.nextElementSibling;
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                    icon.classList.toggle('rotate');
                });
            });
        });
    </script>
@endsection
