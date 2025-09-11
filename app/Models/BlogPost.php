<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'reading_time',
        'pinned',
        'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'pinned' => 'boolean',
        'tags' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (BlogPost $post) {
            // Slug handling
            if (empty($post->slug) && !empty($post->title)) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            } elseif ($post->isDirty('slug') && !empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->slug, $post->id);
            }

            // Reading time (approx 200 wpm)
            if (!empty($post->content)) {
                $words = str_word_count(strip_tags($post->content));
                $post->reading_time = max(1, (int) ceil($words / 200));
            }

            // Tags from comma-separated string to array if needed
            if (is_string($post->tags)) {
                $tags = array_filter(array_map('trim', explode(',', $post->tags)));
                $post->tags = array_values(array_unique($tags));
            }

            // Meta fallbacks
            if (empty($post->meta_title) && !empty($post->title)) {
                $post->meta_title = $post->title;
            }
            if (empty($post->meta_description)) {
                if (!empty($post->excerpt)) {
                    $post->meta_description = Str::limit(strip_tags($post->excerpt), 155);
                } elseif (!empty($post->content)) {
                    $post->meta_description = Str::limit(strip_tags($post->content), 155);
                }
            }
        });
    }

    public static function uniqueSlug(string $value, $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base ?: Str::random(8);
        $i = 2;

        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $base . '-' . $i;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            $i++;
        }

        return $slug;
    }

    // Accessors
    public function getCoverImageAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        $path = $value;
        // Normalizza percorsi errati
        $path = str_replace('/storage/public/', '/storage/', $path);
        $path = str_replace('storage/public/', 'storage/', $path);
        if (Str::startsWith($path, 'public/')) {
            $path = '/storage/' . substr($path, 7);
        }
        // Se non è assoluto o già con /storage, forza /storage/
        if (!Str::startsWith($path, ['http://', 'https://', '/'])) {
            $path = '/storage/' . ltrim($path, '/');
        }
        return $path;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePinnedFirst($query)
    {
        return $query->orderByDesc('pinned')->orderByDesc('published_at');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('title', 'like', $like)
              ->orWhere('excerpt', 'like', $like)
              ->orWhere('content', 'like', $like);
        });
    }
}
