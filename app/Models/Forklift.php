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
        'main_image',      // ← was 'image'
         'gallery_images',         // ["forklifts/a.jpg", ...]
        'location_id',
        'features',
        'status',
    ];

    protected $casts = [
    'capacity_kg'     => 'integer',
    'hourly_rate'     => 'decimal:2',
    'gallery_images'  => 'array',  // ← was 'images'
];

    // expose computed fields
    protected $appends = ['image_url', 'images_urls', 'formatted_hourly_rate'];

    // keep raw paths hidden if you ever return the model as JSON
    protected $hidden = ['main_image', 'gallery_images']; 

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

    
    public function getImageUrlAttribute(): ?string
{
    if (!$this->main_image) {  // ← was $this->image
        return null;
    }
    return asset('storage/' . ltrim($this->main_image, '/'));
}

public function getImagesUrlsAttribute(): array
{
    $urls = [];

    if (is_array($this->gallery_images)) {  // ← was $this->images
        foreach ($this->gallery_images as $img) {
            if ($img) {
                $urls[] = asset('storage/' . ltrim($img, '/'));
            }
        }
    }

    // Main image always first
    if ($this->main_image) {  // ← was $this->image
        array_unshift($urls, asset('storage/' . ltrim($this->main_image, '/')));
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
