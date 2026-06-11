@extends('layouts.app')
@section('titulo', 'videos')
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <h3>Categorias de videos
                <a href="{{ route('videoscat.create') }}" class="btn btn-info btn-sm float-right">Agregar nueva categoria</a>
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

            <input type="text" id="search" class="form-control form-control-sm mt-3" placeholder="Buscar Categoría...">
            <div class="table-responsive">
                <table class="table table-hover mt-4">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>URL</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($videoCategorias as $videoCategoria)
                            <tr>
                                <td>{{ $videoCategoria->id }}</td>
                                <td>{{ $videoCategoria->nombre }}</td>
                                <td>{{ $videoCategoria->url }}</td>
                                <td>
                                    <a href="{{ route('videoscat.show', $videoCategoria->url) }}"
                                        class="btn btn-primary btn-sm" target="_blank">Ver</a>
                                    <a href="{{ route('videoscat.edit', $videoCategoria->id) }}"
                                        class="btn btn-warning btn-sm">Editar</a>
                                    <form action="{{ route('videoscat.destroy', $videoCategoria->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('{{ route('videoscat.destroy', $videoCategoria->id) }}', '{{ $videoCategoria->nombre }}')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
                    <p>¿Estás seguro de que deseas eliminar este Video?</p>
                    <p class="text-primary">'<strong id="videoNombre"></strong>'</p>
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
        function confirmDelete(url, nombreVideo) {
            $('#deleteModal').modal('show');
            document.getElementById('deleteForm').action = url;
            document.getElementById('videoNombre').textContent = nombreVideo;
        }
    </script>
@endsection
