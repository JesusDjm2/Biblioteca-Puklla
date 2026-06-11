@extends('layouts.app')
@section('titulo', 'Categorias')
@section('contenido')
    <div class="row">
        <div class="col-md-12 mt-3">
            <h3>Lista de categorias dentro de: '{{ $categoria->nombre }}' <span
                    style="font-size: 0.6em">({{ $categoria->libros->count() }}
                    {{ $categoria->libros->count() === 1 ? 'libro' : 'libros' }})
                </span>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger float-right ml-2">Volver</a>

                <a href="{{ route('libros.create', ['categoria' => $categoria->id]) }}" class="btn btn-primary btn-sm ml-2">
                    Agregar libro <i style="font-size: 0.8em" class="fa fa-plus"></i>
                </a>
            </h3>
            <div style="widows: 100%; border-bottom: 1px dashed rgb(74, 113, 138); margin-bottom:1em; padding-bottom:0.6em">
            </div>
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
        </div>
        <div class="col-lg-12">
            @if ($categoria->libros->isEmpty())
                <p>No hay libros relacionados con esta categoría.</p>
            @else
                <ul>
                    @foreach ($categoria->libros as $libro)
                        <li>{{ $libro->nombre }}</li>
                    @endforeach
                </ul>
            @endif
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
                    ¿Estás seguro de que deseas eliminar esta categoria?
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
            const searchInput = document.getElementById('search');
            const categoryTable = document.getElementById('categoryTable');
            const rows = categoryTable.getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.toLowerCase();
                Array.from(rows).forEach(row => {
                    const columns = row.getElementsByTagName('td');
                    const name = columns[1].textContent.toLowerCase();
                    const section = columns[2].textContent.toLowerCase();
                    if (name.includes(query) || section.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
