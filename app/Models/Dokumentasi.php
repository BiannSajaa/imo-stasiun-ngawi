<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumentasi extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi';

    protected $fillable = [
        'upload_id',
        'foto',
    ];

    public function upload(): BelongsTo
    {
        return $this->belongsTo(DinasanUpload::class, 'upload_id');
    }
}
