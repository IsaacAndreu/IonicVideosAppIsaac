<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Multimedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'type', // vídeo o imatge
        'user_id',
    ];

    /**
     * Relació: Un arxiu multimèdia pertany a un usuari
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
