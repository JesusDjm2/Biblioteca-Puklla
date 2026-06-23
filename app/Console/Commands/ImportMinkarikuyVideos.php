<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Models\VideoCategoria;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportMinkarikuyVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:minkarikuy-videos
                            {file? : Ruta del archivo JSON (por defecto storage/app/minkarikuy_videos.json)}
                            {--dry-run : Solo mostrar qué se importaría sin guardar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los videos de la categoría Mink\'arikuy desde un archivo JSON';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('app/minkarikuy_videos.json');
        $dryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("El archivo {$filePath} no existe.");
            return 1;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (!is_array($data)) {
            $this->error('El archivo JSON no tiene un formato válido.');
            return 1;
        }

        $this->info("Se encontraron " . count($data) . " registros en el archivo.");
        $this->newLine();

        if ($dryRun) {
            $this->warn("--- MODO DRY RUN: No se guardarán datos ---");
        }

        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        foreach ($data as $index => $item) {
            $rowNum = $index + 1;

            try {
                $categoriaName = !empty($item['categoria']) ? trim($item['categoria']) : 'General';
                $nombre = !empty($item['nombre']) ? trim($item['nombre']) : '';
                $descripcion = !empty($item['descripcion']) ? trim($item['descripcion']) : '';
                $rawYoutube = !empty($item['youtube']) ? trim($item['youtube']) : '';
                $rawDrive = !empty($item['drive']) ? trim($item['drive']) : '';

                // Extraer IDs
                $youtubeId = $this->extractYoutubeId($rawYoutube);
                $driveId = $this->extractDriveId($rawDrive);

                if (empty($nombre)) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                if (empty($youtubeId) && empty($driveId)) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [#{$rowNum}] {$nombre} -> YouTube: {$youtubeId}");
                    $bar->advance();
                    continue;
                }

                // 1. Find or create Category
                $categoria = VideoCategoria::firstOrCreate(
                    ['nombre' => $categoriaName],
                    ['url' => Str::slug($categoriaName)]
                );

                // 2. Find or update Video
                $video = null;
                if (!empty($youtubeId)) {
                    $video = Video::where('youtube', $youtubeId)->first();
                }

                if (!$video && !empty($driveId)) {
                    $video = Video::where('drive', $driveId)->first();
                }

                if (!$video) {
                    $video = Video::where('nombre', $nombre)->first();
                }

                if ($video) {
                    $video->update([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'categoria_id' => $categoria->id,
                        'youtube' => $youtubeId ?: $video->youtube,
                        'drive' => $driveId ?: $video->drive,
                    ]);
                    $updatedCount++;
                } else {
                    Video::create([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'categoria_id' => $categoria->id,
                        'youtube' => $youtubeId,
                        'drive' => $driveId,
                    ]);
                    $importedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error en registro #{$rowNum} ({$item['nombre']}): " . $e->getMessage();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("DRY RUN completado. {$importedCount} a crear, {$updatedCount} a actualizar, {$skippedCount} omitidos.");
            return 0;
        }

        $this->info("Importación completada.");
        $this->table(
            ['Tipo', 'Cantidad'],
            [
                ['Creados', $importedCount],
                ['Actualizados', $updatedCount],
                ['Omitidos', $skippedCount],
            ]
        );

        if (!empty($errors)) {
            $this->warn("Errores encontrados:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        $totalCategoria = Video::whereHas('categoria', function ($q) {
            $q->where('nombre', 'Mink\'arikuy');
        })->count();

        $this->newLine();
        $this->info("Total de videos en categoría Mink'arikuy ahora: {$totalCategoria}");

        return 0;
    }

    private function extractYoutubeId($urlOrId)
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|win/.+|watch\?v=|\?v=)|youtu\.be/)([^"&?/\s]{11})%i', $urlOrId, $match)) {
            return $match[1];
        }
        return $urlOrId;
    }

    private function extractDriveId($urlOrId)
    {
        if (preg_match('/[-\w]{25,}/', $urlOrId, $matches)) {
            return $matches[0];
        }
        return $urlOrId;
    }
}
