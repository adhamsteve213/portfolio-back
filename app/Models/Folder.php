<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Folder extends Model
{
    use HasFactory;

    protected $appends = ['thumbnail_url'];

    protected $fillable = [
        'name',
        'description',
        'sort_order',
    ];

    public function workSamples(): HasMany
    {
        return $this->hasMany(WorkSample::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $samples = $this->relationLoaded('workSamples') ? $this->workSamples : $this->workSamples()->get();

        $coverSample = $samples->first(function (WorkSample $sample): bool {
            $filename = strtolower(pathinfo($sample->image_path, PATHINFO_FILENAME));

            return str_starts_with($filename, 'cover');
        });

        $thumbnailPath = $coverSample?->image_path ?? $samples->first()?->image_path;

        if (! $thumbnailPath) {
            return null;
        }

        return url(Storage::url($thumbnailPath));
    }
}
