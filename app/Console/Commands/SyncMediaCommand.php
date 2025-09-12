<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan media:sync --disk=s3 --prefix=videos --dry-run
     */
    protected $signature = 'media:sync {--disk=s3} {--prefix=} {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Indicizza su database i file già presenti sul disco (default: S3) nelle cartelle videos/ e images/.';

    public function handle(): int
    {
        $disk = (string) $this->option('disk');
        $prefixOpt = $this->option('prefix');
        $dryRun = (bool) $this->option('dry-run');

        if (!Storage::disk($disk)) {
            $this->error("Disco '{$disk}' non configurato.");
            return self::FAILURE;
        }

        $prefixes = [];
        if (is_string($prefixOpt) && strlen($prefixOpt) > 0) {
            $prefixes[] = trim($prefixOpt, '/');
        } else {
            $prefixes = ['videos', 'images'];
        }

        $created = 0; $skipped = 0; $scanned = 0; $errors = 0;

        foreach ($prefixes as $prefix) {
            $this->info("Scansione: {$disk}://{$prefix}/ ...");
            try {
                // allFiles è ricorsivo
                $files = Storage::disk($disk)->allFiles($prefix);
            } catch (\Throwable $e) {
                $this->error("Errore nel listare i file per prefix '{$prefix}': {$e->getMessage()}");
                $errors++;
                continue;
            }

            foreach ($files as $path) {
                $scanned++;
                // ignora file nascosti o placeholder
                if (Str::endsWith($path, ['/','.'])) { continue; }
                $exists = Media::where('disk', $disk)->where('key', $path)->exists();
                if ($exists) { $skipped++; continue; }

                $filename = basename($path);
                $mime = null; $size = null; $type = null;
                try { $mime = Storage::disk($disk)->mimeType($path); } catch (\Throwable $e) { $mime = null; }
                try { $size = Storage::disk($disk)->size($path); } catch (\Throwable $e) { $size = null; }
                if (is_string($mime)) {
                    if (str_starts_with($mime, 'image/')) { $type = 'image'; }
                    elseif (str_starts_with($mime, 'video/')) { $type = 'video'; }
                }
                if (!$type) {
                    $type = Str::startsWith($path, 'images/') ? 'image' : (Str::startsWith($path, 'videos/') ? 'video' : 'other');
                }

                if ($dryRun) {
                    $this->line("[DRY-RUN] indicizzato: {$path} ({$mime} | {$size} B) -> {$type}");
                    continue;
                }

                try {
                    Media::create([
                        'disk' => $disk,
                        'key' => $path,
                        'filename' => $filename,
                        'mime_type' => $mime,
                        'size' => $size,
                        'type' => $type,
                    ]);
                    $created++;
                } catch (\Throwable $e) {
                    $errors++;
                    $this->error("Errore creando record per {$path}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Completato. Scansionati: {$scanned}, Creati: {$created}, Saltati: {$skipped}, Errori: {$errors}");
        return self::SUCCESS;
    }
}
