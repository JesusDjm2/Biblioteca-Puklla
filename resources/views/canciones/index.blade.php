@extends('layouts.app')
@section('titulo', 'Canciones')
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <h3>Canciones
                <a href="{{ route('canciones.create') }}" class="btn btn-info btn-sm float-right">Crear Nueva Canción</a>
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

            <table class="table table-hover mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Autor</th>
                        <th>Enlace</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($canciones as $cancion)
                        <tr>
                            <td>{{ $counter }}</td>
                            <td>{{ $cancion->nombre }}</td>
                            <td>{{ $cancion->autor }}</td>
                            <td>
                                @if ($cancion->youtube)
                                    <a class="btn btn-sm btn-danger"
                                        href="https://www.youtube.com/watch?v={{ $cancion->youtube }}" target="_blank">
                                        YouTube <i class="fa fa-external-link"></i>
                                    </a>
                                @elseif($cancion->drive)
                                    <a class="btn btn-warning btn-sm"
                                        href="https://drive.google.com/file/d/{{ $cancion->drive }}/preview"
                                        target="_blank">
                                        Drive <i class="fa fa-external-link"></i>
                                    </a>
                                @elseif($cancion->spotify)
                                    <a class="btn btn-success btn-sm"
                                        href="https://open.spotify.com/track/{{ $cancion->spotify }}" target="_blank">
                                        Spotify <i class="fa fa-external-link"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('canciones.edit', $cancion->id) }}" class="btn btn-sm btn-info"><i
                                        class="fa fa-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="confirmDelete('{{ route('canciones.destroy', $cancion->id) }}')"><i
                                        class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                </tbody>
            </table>
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
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta canción?
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
        function confirmDelete(url) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = url;
            $('#deleteModal').modal('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Additional script for handling other functionalities if needed
        });
    </script>
@endsection
