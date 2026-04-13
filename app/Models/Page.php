<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    protected $casts = [
        'content' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Get a page by its slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', Str::slug($slug))
            ->where('is_published', true)
            ->first();
    }

    /**
     * Get content value by key
     */
    public function getContent(string $key, $default = null)
    {
        $content = $this->content ?? [];

        if (array_key_exists($key, $content)) {
            return $content[$key] ?? $default;
        }

        $normalized = Str::snake(trim($key));
        foreach ($content as $contentKey => $value) {
            if (Str::snake(trim((string) $contentKey)) === $normalized) {
                return $value;
            }
        }

        return $default;
    }
}
