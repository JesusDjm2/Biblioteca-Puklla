@extends('layouts.app')
@section('titulo', 'videos')
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <h3>Videos
                <a href="{{ route('videos.create') }}" class="btn btn-info btn-sm float-right">Agregar nuevo video</a>
            </h3>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <input type="text" id="search" class="form-control form-control-sm mt-3" placeholder="Buscar video...">
            <div class="table-responsive" id="tablavideos">
                <table class="table table-hover mt-4">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Video</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablavideos">
                        @php $counter = 1; @endphp
                        @foreach ($videos as $video)
                            <tr>
                                <td>{{ $counter }}</td>
                                <td>{{ $video->nombre }}</td>
                                <td>{{ $video->categoria->nombre }}</td>
                                <td>
                                    @if ($video->youtube)
                                        <a class="btn btn-sm btn-info"
                                            href="https://www.youtube.com/watch?v={{ $video->youtube }}" target="_blank">
                                            Ver en YouTube <i class="fa fa-external-link"></i>
                                        </a>
                                    @elseif($video->drive)
                                        <a class="btn btn-success btn-sm"
                                            href="https://drive.google.com/file/d/{{ $video->drive }}/view"
                                            target="_blank">Ver en Drive <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete('{{ route('videos.destroy', $video->id) }}', '{{ $video->nombre }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @php $counter++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>¿Estás seguro de que deseas eliminar este video?
                        <span>
                            <p class="text-primary">'<strong id="videoNombre"></strong>'</p>
                        </span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(url, nombrevideo) {
            $('#deleteModal').modal('show');
            document.getElementById('deleteForm').action = url;
            document.getElementById('videoNombre').textContent = nombrevideo;
        }
    </script>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const tablavideos = document.getElementById('tablavideos');

            searchInput.addEventListener('input', function() {
                const searchTerm = searchInput.value.toLowerCase();

                const videos = @json($videos); // Convierte los videos de PHP a JSON

                // Función para remover tildes y caracteres especiales
                function removeAccents(str) {
                    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                }

                // Filtrar los videos que coincidan con el término de búsqueda ignorando tildes
                const filteredvideos = videos.filter(video => {
                    const nombreSinTildes = removeAccents(video.nombre.toLowerCase());
                    return nombreSinTildes.includes(removeAccents(searchTerm));
                });

                // Mostrar los videos filtrados en la tabla
                rendervideos(filteredvideos);
            });

            function rendervideos(videos) {
                tablavideos.innerHTML = ''; // Limpiar contenido actual de la tabla
                let counter = 1;

                videos.forEach(video => {
                    const categoriasHTML = video.categorias.map(categoria => `
                        <li>
                            ${categoria.nombre}
                            <small>&nbsp;
                                <i class="fa fa-angle-right font-weight-bold text-info"></i>
                                <i class="fa fa-angle-right font-weight-bold text-info"></i>
                            </small> &nbsp;
                            ${categoria.seccion.nombre}
                        </li>
                    `).join('');

                    const rowHTML = `
                        <tr>
                            <td>${counter}</td>
                            <td>${video.nombre}</td>
                            <td>
                                <ul>${categoriasHTML}</ul>
                            </td>
                            <td>
                                <a href="{{ route('videos.show', '__video_id__') }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('videos.edit', '__video_id__') }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ route('videos.destroy', '__video_id__') }}')"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;

                    // Reemplazar placeholders en los enlaces de acciones con el ID real del video
                    const rowHTMLWithIds = rowHTML
                        .replace(/__video_id__/g, video.id);

                    tablavideos.innerHTML += rowHTMLWithIds;
                    counter++;
                });
            }
        });
    </script>
@endsection
