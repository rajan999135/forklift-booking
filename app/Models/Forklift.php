<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forklift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity_kg',
        'hourly_rate',   // decimal(10,2)
        'image',         // e.g. "forklifts/abc.jpg"
        'images',        // ["forklifts/a.jpg", ...]
        'location_id',
        'features',
    ];

    protected $casts = [
        'capacity_kg' => 'integer',
        'hourly_rate' => 'decimal:2',
        'images'      => 'array',
    ];

    // expose computed fields
    protected $appends = ['image_url', 'images_urls', 'formatted_hourly_rate'];

    // keep raw paths hidden if you ever return the model as JSON
    protected $hidden = ['image', 'images'];

    /* ---------- Relationships ---------- */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /* ---------- Scopes ---------- */

    public function scopeAtLocation($q, $locationId = null)
    {
        return $locationId ? $q->where('location_id', $locationId) : $q;
    }

    /* ---------- URL helpers (works with any port) ---------- */

    /**
     * Convert a stored path (or absolute URL) into a public URL
     * that respects the current host:port. Supports:
     *   - absolute http(s) URLs → returned as-is
     *   - "/storage/..."        → left as-is
     *   - "storage/..."         → prefixed with asset()
     *   - "forklifts/..."       → "storage/..." via asset()
     */
    protected static function toPublicUrl(?string $path): ?string
    {
        if (!$path) return null;

        // already absolute?
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        // already looks like a public link
        if (str_starts_with($path, '/storage/')) {
            return url($path);
        }

        // normalise: ensure single "storage/" prefix and make it host-aware
        $clean = ltrim($path, '/');
        if (!str_starts_with($clean, 'storage/')) {
            $clean = 'storage/' . $clean;
        }
        return asset($clean);
    }

    /* ---------- Accessors ---------- */

    // public function getImageUrlAttribute(): ?string
    // {
    //     // Prefer primary image; else first in gallery
    //     $path = $this->image ?: ($this->images[0] ?? null);
    //     return static::toPublicUrl($path);
    // }

    // public function getImagesUrlsAttribute(): array
    // {
    //     $paths = is_array($this->images) ? $this->images : [];
    //     if ($this->image && !in_array($this->image, $paths, true)) {
    //         array_unshift($paths, $this->image);
    //     }

    //     return collect($paths)
    //         ->map(fn ($p) => static::toPublicUrl($p))
    //         ->filter()
    //         ->values()
    //         ->all();
    // }
    public function getImageUrlAttribute(): ?string
{
    if (! $this->image) {
        return null;
    }

    // DB: "forklifts/abc123.jpg" → URL: "/storage/forklifts/abc123.jpg"
    return asset('storage/' . ltrim($this->image, '/'));
}

public function getImagesUrlsAttribute(): array
{
    $urls = [];

    // If images column is an array/json of paths
    if (is_array($this->images)) {
        foreach ($this->images as $img) {
            if ($img) {
                $urls[] = asset('storage/' . ltrim($img, '/'));
            }
        }
    }

    // Make sure main image is first in the list
    if ($this->image) {
        array_unshift($urls, asset('storage/' . ltrim($this->image, '/')));
    }

    return $urls;
}


    public function getFormattedHourlyRateAttribute(): string
    {
        return '$' . number_format((float) $this->hourly_rate, 2) . ' CAD';
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}


}
