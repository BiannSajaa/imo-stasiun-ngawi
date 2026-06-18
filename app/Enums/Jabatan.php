<?php

namespace App\Enums;

enum Jabatan: string
{
    case Admin = 'Admin';
    case Pjl = 'PJL';
    case Ppka = 'PPKA';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $jabatan): array => [$jabatan->value => $jabatan->label()])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function operationalOptions(): array
    {
        return [
            self::Pjl->value => self::Pjl->label(),
            self::Ppka->value => self::Ppka->label(),
        ];
    }
}
