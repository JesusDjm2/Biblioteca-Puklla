@include('partials.modulo-crud-header', [
    'aria' => 'Videos',
    'tabs' => [
        'categorias' => ['label' => 'Categorías', 'route' => route('videoscat.index')],
        'videos' => ['label' => 'Videos', 'route' => route('videos.index')],
    ],
    'active' => $active ?? '',
    'stats' => $stats ?? [],
    'botones' => $botones ?? [],
])
