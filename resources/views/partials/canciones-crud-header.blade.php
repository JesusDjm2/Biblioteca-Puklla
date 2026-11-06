@include('partials.modulo-crud-header', [
    'aria' => 'Canciones',
    'tabs' => [
        'categorias' => ['label' => 'Categorías', 'route' => route('cancionescat.index')],
        'canciones' => ['label' => 'Canciones', 'route' => route('canciones.index')],
    ],
    'active' => $active ?? '',
    'stats' => $stats ?? [],
    'botones' => $botones ?? [],
])
