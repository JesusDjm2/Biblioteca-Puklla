<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriaFisicaController;
use App\Http\Controllers\LibroFisicoController;
use App\Http\Controllers\CancionesCategoriaController;
use App\Http\Controllers\CancionesController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EnlacesController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\VideoCategoriaController;
use App\Http\Controllers\VideoController;
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
});

//Biblioteca (público)
Route::resource('Secciones', SeccionController::class)->names('secciones');
Route::resource('Categorias', CategoriaController::class)->names('categorias');
Route::get('Categoria/{id}/', [CategoriaController::class, 'detallesCat'])->name('detallesCat');
Route::resource('Libros', LibroController::class)->names('libros');

Route::middleware(['auth'])->group(function () {
    Route::get('libros-importar', [LibroController::class, 'importForm'])->name('libros.import.form');
    Route::post('libros-importar', [LibroController::class, 'importProcess'])->name('libros.import.process');
    Route::get('videos-importar', [VideoController::class, 'importForm'])->name('videos.import.form');
    Route::post('videos-importar', [VideoController::class, 'importProcess'])->name('videos.import.process');
});

Route::get('libros/create/{categoria?}', [LibroController::class, 'create'])->name('libros.create');
Route::get('Lista-de-Libros', [LibroController::class, 'libros'])->name('libros');

//Videos (público)
Route::resource('Videos', VideoController::class)->names('videos');
Route::resource('video-categorias', VideoCategoriaController::class)->names('videoscat');
Route::get('videos-Puklla', [VideoCategoriaController::class, 'lista'])->name('videos');

//Canciones (público)
Route::resource('Categorias-canciones', CancionesCategoriaController::class)->names('cancionescat');
Route::resource('canciones', CancionesController::class)->names('canciones');
Route::get('canciones-Puklla', [CancionesController::class, 'canciones'])->name('cancionesPuklla');

Route::resource('Dashboard', AdminController::class)->names('admin')->middleware('auth');
Route::get('resultados-de-busqueda', [EnlacesController::class, 'busqueda'])->name('busqueda');

Route::get('/limpiar-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    return "Cache limpiada correctamente";
});
/* Route::get('migrar', function(){
    Artisan::call('migrate');
    return "migración corrió correctamente!";
});
Route::get('migrarRollback', function(){
    Artisan::call('migrate:rollback');
    return "rollback corrió correctamente!";
});
Route::get('migrarFresh', function(){
    Artisan::call('migrate:fresh');
    return "migración Fresh corrió correctamente!";
}); */