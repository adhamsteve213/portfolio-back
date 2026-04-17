<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSample extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id',
        'project_name',
        'description',
        'image_path',
        'sort_order',
    ];

    protected $appends = ['image_url', 'is_cover'];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/'.ltrim($this->image_path, '/'));
    }

    public function getIsCoverAttribute(): bool
    {
        $filename = strtolower(pathinfo($this->image_path, PATHINFO_FILENAME));

        return str_starts_with($filename, 'cover');
    }
}
