<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class SiteContent extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'site_contents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
    ];

    /**
     * Cache duration in seconds (1 hour).
     */
    protected static int $cacheDuration = 3600;

    /**
     * Get a site content value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = "site_content.{$key}";

        return Cache::remember($cacheKey, static::$cacheDuration, function () use ($key, $default) {
            $content = static::where('key', $key)->first();

            if (!$content) {
                return $default;
            }

            // Auto-decode JSON values
            if ($content->type === 'json') {
                return json_decode($content->value, true) ?? $default;
            }

            return $content->value ?? $default;
        });
    }

    /**
     * Set a site content value by key.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $group
     * @param string|null $type
     * @param string|null $label
     * @return static
     */
    public static function set(string $key, mixed $value, ?string $group = null, ?string $type = null, ?string $label = null): static
    {
        // JSON encode arrays/objects
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $type = $type ?? 'json';
        }

        $content = static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'label' => $label,
            ], fn($v) => $v !== null)
        );

        // Clear cache
        Cache::forget("site_content.{$key}");
        Cache::forget("site_content.group.{$content->group}");
        Cache::forget('site_content.all');

        return $content;
    }

    /**
     * Get all site contents by group.
     *
     * @param string $group
     * @return array
     */
    public static function getGroup(string $group): array
    {
        $cacheKey = "site_content.group.{$group}";

        return Cache::remember($cacheKey, static::$cacheDuration, function () use ($group) {
            $contents = static::where('group', $group)->get();
            $result = [];

            foreach ($contents as $content) {
                $value = $content->value;

                // Auto-decode JSON
                if ($content->type === 'json') {
                    $value = json_decode($value, true);
                }

                $result[$content->key] = $value;
            }

            return $result;
        });
    }

    /**
     * Get all site contents as an associative array.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return Cache::remember('site_content.all', static::$cacheDuration, function () {
            $contents = static::all();
            $result = [];

            foreach ($contents as $content) {
                $value = $content->value;

                if ($content->type === 'json') {
                    $value = json_decode($value, true);
                }

                $result[$content->key] = $value;
            }

            return $result;
        });
    }

    /**
     * Clear all site content caches.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        $contents = static::all();

        foreach ($contents as $content) {
            Cache::forget("site_content.{$content->key}");
        }

        $groups = static::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("site_content.group.{$group}");
        }

        Cache::forget('site_content.all');
    }

    /**
     * Bulk update multiple site contents.
     *
     * @param array $data Associative array of key => value pairs
     * @return void
     */
    public static function bulkUpdate(array $data): void
    {
        foreach ($data as $key => $value) {
            $content = static::where('key', $key)->first();

            if ($content) {
                // JSON encode if needed
                if ($content->type === 'json' && (is_array($value) || is_object($value))) {
                    $value = json_encode($value);
                }

                $content->update(['value' => $value]);
                Cache::forget("site_content.{$key}");
            }
        }

        Cache::forget('site_content.all');
    }
}
