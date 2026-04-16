<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['image_url'];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function getImageUrlAttribute(): string
    {
        return url(Storage::url($this->image_path));
    }
}
