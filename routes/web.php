<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriaFisicaController;
use App\Http\Controllers\LibroFisicoController;
use App\Http\Controllers\CancionesCategoriaController;
use App\Http\Controllers\CancionesController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EnlacesController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\CategoriaMaterialDidacticoController;
use App\Http\Controllers\MaterialDidacticoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\VideoCategoriaController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\RepoEstructuraAdminController;
use App\Http\Controllers\RepoItemAdminController;
use App\Http\Controllers\RepositorioController;
use App\Http\Controllers\TaqeController;
use App\Http\Controllers\TaqeTrabajoAdminController;
use App\Http\Controllers\TaqeCategoriaAdminController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* Route::get('/', function () {
    return view('welcome'); })->name('index')->middleware('auth'); */
Route::get('/', [EnlacesController::class, 'index'])->name('index');
Auth::routes();
/* Route::get('administrador', [EnlacesController::class, 'admin'])->name('administrador')->middleware('auth'); */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('administradores', AdminController::class)->names('admin');
    Route::get('Lista-de-alumnos', [AdminController::class, 'alumnos'])->name('alumnos');
    Route::get('Bibliotecario', [AdminController::class, 'bibliotecario'])->name('bibliotecario');
    Route::get('Administrador-Videos', [AdminController::class, 'videos'])->name('adminVideos');
    Route::get('Administrador-Canciones', [AdminController::class, 'canciones'])->name('adminCanciones');
    Route::get('Admini-Sisichakunay', [AdminController::class, 'sisicha'])->name('adminSisicha');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

Route::middleware(['auth', 'role:admin|biblioteca'])->prefix('biblioteca-fisica')->name('biblioteca-fisica.')->group(function () {
    Route::resource('categorias', CategoriaFisicaController::class)->except(['show'])
        ->parameters(['categorias' => 'categoria_fisica']);
    Route::resource('libros', LibroFisicoController::class)->except(['show'])
        ->parameters(['libros' => 'libro_fisico']);
    Route::get('libros-buscar-ajax', [LibroFisicoController::class, 'buscarAjax'])
        ->name('libros.buscar-ajax');
    Route::resource('prestamos', PrestamoController::class)->except(['show']);
    Route::patch('prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver'])
        ->name('prestamos.devolver');
    Route::resource('material-categorias', CategoriaMaterialDidacticoController::class)->except(['show'])
        ->parameters(['material-categorias' => 'material_categoria']);
    Route::resource('materiales', MaterialDidacticoController::class)->except(['show'])
        ->parameters(['materiales' => 'material']);
    Route::get('materiales-buscar-ajax', [MaterialDidacticoController::class, 'buscarAjax'])
        ->name('materiales.buscar-ajax');
});

// Biblioteca virtual — administración. Antes estas rutas eran públicas: cualquier
// visitante podía crear, editar o borrar libros, categorías y secciones.
Route::middleware(['auth', 'role:admin|biblioteca'])->group(function () {
    Route::resource('Secciones', SeccionController::class)->names('secciones');
    Route::resource('Categorias', CategoriaController::class)->except(['show'])->names('categorias');
    Route::get('Categoria/{id}/', [CategoriaController::class, 'detallesCat'])->name('detallesCat');
    Route::resource('Libros', LibroController::class)->except(['show'])->names('libros');
    Route::get('libros-importar', [LibroController::class, 'importForm'])->name('libros.import.form');
    Route::post('libros-importar', [LibroController::class, 'importProcess'])->name('libros.import.process');
});

// Biblioteca virtual — público
Route::get('Categorias/{Categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
Route::get('Libros/{Libro}', [LibroController::class, 'show'])->name('libros.show');

// Videos — administración (enlace de YouTube/Drive o archivo subido)
Route::middleware(['auth', 'role:admin|videos|biblioteca'])->group(function () {
    Route::get('videos-importar', [VideoController::class, 'importForm'])->name('videos.import.form');
    Route::post('videos-importar', [VideoController::class, 'importProcess'])->name('videos.import.process');
    Route::post('videos-drive/iniciar', [VideoController::class, 'iniciarSubidaDrive'])->name('videos.drive.iniciar');
    Route::resource('Videos', VideoController::class)->except(['show'])->names('videos');
    Route::resource('video-categorias', VideoCategoriaController::class)->except(['show'])->names('videoscat');
});

Route::get('libros/create/{categoria?}', [LibroController::class, 'create'])->name('libros.create')
    ->middleware(['auth', 'role:admin|biblioteca']);
Route::get('Lista-de-Libros', [LibroController::class, 'libros'])->name('libros');
Route::get('catalogo-fisico', [LibroFisicoController::class, 'catalogo'])->name('catalogo-fisico');
Route::get('api/codigo-fisico/sugerir', [LibroFisicoController::class, 'sugerirCodigo'])
    ->name('libros-fisicos.sugerir-codigo')
    ->middleware('auth');

//Videos (público)
Route::get('Videos/{Video}', [VideoController::class, 'show'])->name('videos.show');
Route::get('video-categorias/{video_categoria}', [VideoCategoriaController::class, 'show'])->name('videoscat.show');
Route::get('videos-Puklla', [VideoCategoriaController::class, 'lista'])->name('videos');

// Canciones — administración (antes pública, igual que la biblioteca virtual)
Route::middleware(['auth', 'role:admin|audios'])->group(function () {
    Route::resource('Categorias-canciones', CancionesCategoriaController::class)->except(['show'])->names('cancionescat');
    Route::resource('canciones', CancionesController::class)->except(['show'])->names('canciones');
});

// Canciones — público
Route::get('canciones-Puklla', [CancionesController::class, 'canciones'])->name('cancionesPuklla');

Route::resource('Dashboard', AdminController::class)->names('admin')->middleware('auth');
Route::get('resultados-de-busqueda', [EnlacesController::class, 'busqueda'])->name('busqueda');

// Taq'e — Actividades y eventos de saberes (Público)
Route::get('taqe', [TaqeController::class, 'index'])->name('taqe.index');
Route::get('taqe/{slug}', [TaqeController::class, 'show'])->name('taqe.show');

// Taq'e — Administración
Route::middleware(['auth', 'role:admin|biblioteca'])->prefix('admin/taqe')->name('admin.taqe.')->group(function () {
    Route::resource('trabajos', TaqeTrabajoAdminController::class)->except(['show']);
    Route::resource('categorias', TaqeCategoriaAdminController::class)->except(['show'])
        ->parameters(['categorias' => 'categoria']);
});

// Repositorio Institucional (Público)
Route::get('repositorio', [RepositorioController::class, 'index'])->name('repositorio.index');
Route::get('repositorio/{slug}', [RepositorioController::class, 'show'])->name('repositorio.show');
Route::get('repositorio/{slug}/pdf', [RepositorioController::class, 'pdf'])->name('repositorio.pdf');
Route::get('repositorio/{slug}/descargar', [RepositorioController::class, 'descargar'])->name('repositorio.descargar');
Route::get('r/{codigo}', [RepositorioController::class, 'corto'])->where('codigo', '[A-Za-z0-9]{4,12}')->name('repositorio.corto');

// Repositorio Institucional — Administración
Route::middleware(['auth', 'role:admin|biblioteca'])->prefix('admin/repositorio')->name('admin.repositorio.')->group(function () {
    Route::resource('items', RepoItemAdminController::class)->except(['show']);
    Route::post('drive/iniciar', [RepoItemAdminController::class, 'iniciarSubidaDrive'])->name('drive.iniciar');

    Route::controller(RepoEstructuraAdminController::class)->prefix('estructura')->name('estructura.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('comunidades/crear', 'crearComunidad')->name('comunidades.create');
        Route::post('comunidades', 'guardarComunidad')->name('comunidades.store');
        Route::get('comunidades/{comunidad}/editar', 'editarComunidad')->name('comunidades.edit');
        Route::put('comunidades/{comunidad}', 'actualizarComunidad')->name('comunidades.update');
        Route::delete('comunidades/{comunidad}', 'eliminarComunidad')->name('comunidades.destroy');
        Route::get('colecciones/crear', 'crearColeccion')->name('colecciones.create');
        Route::post('colecciones', 'guardarColeccion')->name('colecciones.store');
        Route::get('colecciones/{coleccion}/editar', 'editarColeccion')->name('colecciones.edit');
        Route::put('colecciones/{coleccion}', 'actualizarColeccion')->name('colecciones.update');
        Route::delete('colecciones/{coleccion}', 'eliminarColeccion')->name('colecciones.destroy');
    });
});

Route::get('/limpiar-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    return "Cache limpiada correctamente";
});

/* Route::get('migrar', function(){
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return nl2br("Migración:\n" . \Illuminate\Support\Facades\Artisan::output());
}); */
/* Route::get('migrarRollback', function(){
    Artisan::call('migrate:rollback');
    return "rollback corrió correctamente!";
});
Route::get('migrarFresh', function(){
    Artisan::call('migrate:fresh');
    return "migración Fresh corrió correctamente!";
}); */