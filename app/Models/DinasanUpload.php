<?php

namespace App\Models;

use App\Enums\UploadStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DinasanUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_dinasan',
        'tanggal_upload',
        'file_pdf',
        'status',
    ];

    protected $casts = [
        'tanggal_dinasan' => 'date',
        'tanggal_upload' => 'datetime',
        'status' => UploadStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(Dokumentasi::class, 'upload_id');
    }

    public function syncStatus(): void
    {
        $this->loadMissing('dokumentasi');

        $status = $this->file_pdf && $this->dokumentasi->isNotEmpty()
            ? UploadStatus::Lengkap
            : UploadStatus::BelumLengkap;

        if ($this->status !== $status) {
            $this->forceFill(['status' => $status])->saveQuietly();
        }
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdmin()
            ? $query
            : $query->whereBelongsTo($user);
    }
}
