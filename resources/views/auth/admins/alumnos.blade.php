@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
    <div class="row">
        <div class="col-lg-12">
            <h3>Lista de Alumnos
                @if (Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.create') }}" class="btn btn-info btn-sm float-right">Crear Nuevo
                        Alumno</a>
                @else
                    <a href="" class="btn btn-info btn-sm float-right disabled" aria-disabled="true">Crear Nuevo
                        Administrador</a>
                @endif
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
                        <th>Correo</th>
                        <th>Tipo de Admin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($admins as $admin)
                        <tr>
                            <td> {{ $counter }}</td>
                            <td> {{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->dni }}
                                {{--  @php
                                    $role = $admin->getRoleNames()->first();
                                @endphp
                                {{ $role }} --}}
                            </td>
                            <td>
                                @if (Auth::user()->hasRole('admin'))
                                    <a href="" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger disabled"
                                        onclick="confirmDelete('{{ route('admin.destroy', $admin->id) }}')"><i
                                            class="fa fa-trash"></i></button>
                                @else
                                    <a href="" class="btn btn-sm btn-info" aria-disabled="true"><i
                                            class="fa fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete('{{ route('admin.destroy', $admin->id) }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif
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
                    ¿Estás seguro de que deseas eliminar este alumno?
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
