@if ($errors->any())
    <div class="alert alert-danger bv-alert mb-3">
        <i class="fa fa-exclamation-circle mr-2"></i><strong>Corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
