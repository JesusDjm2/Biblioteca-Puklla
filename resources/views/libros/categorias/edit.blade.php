@extends('layouts.app')
@section('titulo', 'Editar Categoría')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', ['active' => 'categorias'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => 'Categorías', 'url' => route('categorias.index')],
            ['label' => 'Editar', 'url' => null],
        ],
    ])
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h3>Editar Categoría
                <a href="{{ route('categorias.index') }}" class="btn btn-danger btn-sm float-right">Volver</a>
            </h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control rounded-pill" value="{{ old('nombre', $categoria->nombre) }}" required>
                </div>
                <div class="form-group">
                    <label for="url">URL:</label>
                    <input type="text" name="url" id="url" class="form-control rounded-pill" value="{{ old('url', $categoria->url) }}" required readonly>
                </div>
                <div class="form-group">
                    <label for="seccion_id">Sección:</label>
                    <select name="seccion_id" id="seccion_id" class="form-control rounded-pill" required>
                        <option disabled>Elegir Sección</option>
                        @foreach ($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ old('seccion_id', $categoria->seccion_id) == $seccion->id ? 'selected' : '' }}>{{ $seccion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nombreInput = document.getElementById('nombre');
            const urlInput = document.getElementById('url');

            const removeAccents = (str) => {
                const accentsMap = {
                    'á': 'a',
                    'é': 'e',
                    'í': 'i',
                    'ó': 'o',
                    'ú': 'u',
                    'Á': 'A',
                    'É': 'E',
                    'Í': 'I',
                    'Ó': 'O',
                    'Ú': 'U',
                    'ñ': 'n',
                    'Ñ': 'N'
                };
                return str.split('').map(char => accentsMap[char] || char).join('');
            };

            const toSlug = (text) => {
                return removeAccents(text)
                    .toLowerCase()
                    .replace(/&/g, 'y') // Reemplaza &
                    .replace(/[^a-z0-9]+/g, '-') // Reemplaza caracteres no alfanuméricos por "-"
                    .replace(/^-+|-+$/g, ''); // Remueve "-" al principio y al final
            };

            nombreInput.addEventListener('input', function() {
                urlInput.value = toSlug(nombreInput.value);
            });
        });
    </script>
@endsection
