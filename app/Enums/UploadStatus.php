<?php

namespace App\Enums;

enum UploadStatus: string
{
    case Lengkap = 'lengkap';
    case BelumLengkap = 'belum_lengkap';
    case TidakUpload = 'tidak_upload';

    public function label(): string
    {
        return match ($this) {
            self::Lengkap => 'Lengkap',
            self::BelumLengkap => 'Belum Lengkap',
            self::TidakUpload => 'Tidak Upload',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Lengkap => 'success',
            self::BelumLengkap => 'warning',
            self::TidakUpload => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}
