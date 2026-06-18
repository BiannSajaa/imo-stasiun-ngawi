<?php

namespace App\Models;

use App\Enums\Jabatan;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nip',
        'jabatan',
        'username',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function dinasanUploads(): HasMany
    {
        return $this->hasMany(DinasanUpload::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->jabatan, [
            Jabatan::Admin->value,
            Jabatan::Pjl->value,
            Jabatan::Ppka->value,
        ], true);
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function isAdmin(): bool
    {
        return $this->jabatan === Jabatan::Admin->value;
    }

    public function isOperational(): bool
    {
        return in_array($this->jabatan, [Jabatan::Pjl->value, Jabatan::Ppka->value], true);
    }

    public function scopeOperational(Builder $query): Builder
    {
        return $query->whereIn('jabatan', [Jabatan::Pjl->value, Jabatan::Ppka->value]);
    }
}
