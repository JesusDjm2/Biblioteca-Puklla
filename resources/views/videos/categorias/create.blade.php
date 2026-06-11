@extends('layouts.app')

@section('titulo', 'Agregar Nuevo Video')

@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <h3>Agregar Nueva Categoría</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('videoscat.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control rounded-pill {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre') }}" required>
                    @if ($errors->has('nombre'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('nombre') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="url">URL:</label>
                    <input type="text" name="url" id="url" class="form-control rounded-pill {{ $errors->has('url') ? 'is-invalid' : '' }}" value="{{ old('url') }}" readonly>
                    @if ($errors->has('url'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('url') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
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
                // Obtener el valor actual del campo de nombre
                const nombreValue = nombreInput.value.trim();

                // Generar el slug y separar por comas si hay texto
                const slugValue = nombreValue ? toSlug(nombreValue) : '';
                urlInput.value = slugValue;
            });
        });
    </script>
@endsection
