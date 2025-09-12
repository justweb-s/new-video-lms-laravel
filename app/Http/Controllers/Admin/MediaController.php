<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    // Pagina galleria
    public function index()
    {
        return view('admin.media.index');
    }

    // Lista JSON per galleria (con filtro opzionale type=image|video)
    public function list(Request $request)
    {
        $type = $request->query('type');
        $perPage = (int) ($request->query('per_page', 50));
        $query = Media::query()->orderByDesc('id');
        if ($type && in_array($type, ['image', 'video'])) {
            $query->where('type', $type);
        }
        $items = $query->limit($perPage)->get()->map(function (Media $m) {
            return [
                'id' => $m->id,
                'url' => $m->url,
                'filename' => $m->filename,
                'mime_type' => $m->mime_type,
                'size' => $m->size,
                'type' => $m->type,
                'created_at' => $m->created_at?->toDateTimeString(),
            ];
        });
        return response()->json(['data' => $items]);
    }

    // Upload file nella galleria (immagini o video)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/avi,video/mpeg|max:1024000',
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'image/') ? 'image' : 'video';
        $folder = $type === 'image' ? 'images' : 'videos';

        // nome originale sanitizzato e anti-collisione
        $originalName = $file->getClientOriginalName();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeBase = Str::slug($baseName) ?: ($type === 'image' ? 'image' : 'video');
        $fileName = $safeBase . '.' . $extension;
        $key = $folder . '/' . $fileName;

        try {
            $counter = 1;
            while (Storage::disk('s3')->exists($key)) {
                $fileName = $safeBase . '-' . $counter . '.' . $extension;
                $key = $folder . '/' . $fileName;
                $counter++;
            }
        } catch (\Throwable $e) {
            // Se non possiamo verificare l'esistenza, usa un suffisso random per evitare collisioni
            $fileName = $safeBase . '-' . Str::random(6) . '.' . $extension;
            $key = $folder . '/' . $fileName;
        }

        $file->storeAs($folder, $fileName, 's3');

        $media = Media::create([
            'disk' => 's3',
            'key' => $key,
            'filename' => $fileName,
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'type' => $type,
        ]);

        return response()->json([
            'status' => 'ok',
            'media' => [
                'id' => $media->id,
                'url' => $media->url,
                'filename' => $media->filename,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'type' => $media->type,
            ]
        ], 201);
    }

    // Elimina media (S3 + DB)
    public function destroy(Media $media)
    {
        try {
            Storage::disk($media->disk)->delete($media->key);
        } catch (\Throwable $e) {
            // ignora errori di delete su S3 per resilienza
        }
        $media->delete();
        return response()->json(['status' => 'ok']);
    }
}
