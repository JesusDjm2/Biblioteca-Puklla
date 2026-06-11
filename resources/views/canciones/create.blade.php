@extends('layouts.app')

@section('titulo', 'Agregar Nuevo Video')

@section('contenido')
    <div class="row">
        <div class="col-7">
            <h3>Agregar Nueva Canción</h3>
        </div>
        <div class="col-5">
            <a href="{{route('canciones.index')}}" class="float-right btn btn-sm btn-danger">Volver</a>
        </div>
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('canciones.store') }}" method="POST">
                @csrf
                <div class="form-group mt-3">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control form-control-sm rounded-pill"
                        value="{{ old('nombre') }}" required>
                    @if ($errors->has('nombre'))
                        <div class="text-danger">{{ $errors->first('nombre') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="autor">Autor/Compositor: </label>
                    <input type="text" name="autor" id="autor"
                           class="form-control form-control-sm rounded-pill" 
                           value="{{ old('autor') }}" maxlength="255">
                    @if ($errors->has('autor'))
                        <div class="text-danger">{{ $errors->first('autor') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="categoria_id">Categoría:</label>
                    <select name="categoria_id" id="categoria_id" class="form-control form-control-sm rounded-pill"
                        required>
                        <option value="">Seleccionar categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('categoria_id'))
                        <div class="text-danger">{{ $errors->first('categoria_id') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <p>Elegir plataforma:</p>
                    <button type="button" id="youtubeBtn" class="btn btn-sm btn-danger">YouTube</button>
                    <button type="button" id="driveBtn" class="btn btn-sm btn-success">Drive</button>
                    <button type="button" id="spotifyBtn" class="btn btn-sm btn-warning">Spotify</button>
                </div>
                <div class="form-group" id="youtubeField" style="display:none;">
                    <label for="youtube">YouTube: <small class="text-primary">(Insertar identificador)</small></label>
                    <textarea name="youtube" id="youtube" class="form-control form-control-sm rounded-pill" rows="3">{{ old('youtube') }}</textarea>
                    @if ($errors->has('youtube'))
                        <div class="text-danger">{{ $errors->first('youtube') }}</div>
                    @endif
                </div>
                <div class="form-group" id="driveField" style="display:none;">
                    <label for="drive">Drive: <small class="text-primary">(Insertar identificador)</small></label>
                    <input type="text" name="drive" id="drive" class="form-control form-control-sm rounded-pill"
                        value="{{ old('drive') }}">
                    @if ($errors->has('drive'))
                        <div class="text-danger">{{ $errors->first('drive') }}</div>
                    @endif
                </div>
                <div class="form-group" id="spotifyField" style="display:none;">
                    <label for="spotify">Spotify: <small class="text-primary">(Insertar identificador)</small></label>
                    <input type="text" name="spotify" id="spotify" class="form-control form-control-sm rounded-pill"
                        value="{{ old('spotify') }}">
                    @if ($errors->has('spotify'))
                        <div class="text-danger">{{ $errors->first('spotify') }}</div>
                    @endif
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const youtubeBtn = document.getElementById('youtubeBtn');
            const driveBtn = document.getElementById('driveBtn');
            const spotifyBtn = document.getElementById('spotifyBtn');
            const youtubeField = document.getElementById('youtubeField');
            const driveField = document.getElementById('driveField');
            const spotifyField = document.getElementById('spotifyField');

            youtubeBtn.addEventListener('click', function() {
                youtubeField.style.display = 'block';
                driveField.style.display = 'none';
                spotifyField.style.display = 'none';
                document.getElementById('drive').value = ''; // Clear drive field
                document.getElementById('spotify').value = ''; // Clear spotify field
            });

            driveBtn.addEventListener('click', function() {
                driveField.style.display = 'block';
                youtubeField.style.display = 'none';
                spotifyField.style.display = 'none';
                document.getElementById('youtube').value = ''; // Clear youtube field
                document.getElementById('spotify').value = ''; // Clear spotify field
            });

            spotifyBtn.addEventListener('click', function() {
                spotifyField.style.display = 'block';
                youtubeField.style.display = 'none';
                driveField.style.display = 'none';
                document.getElementById('youtube').value = ''; // Clear youtube field
                document.getElementById('drive').value = ''; // Clear drive field
            });
        });
    </script>
@endsection
