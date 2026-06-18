<?php

namespace App\Filament\Widgets;

use App\Enums\Jabatan;
use App\Enums\UploadStatus;
use App\Models\DinasanUpload;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $operationalUsers = User::query()->operational()->where('is_active', true);

        return [
            Stat::make('Total Pegawai', (clone $operationalUsers)->count()),
            Stat::make('Total PJL', User::query()->where('jabatan', Jabatan::Pjl->value)->where('is_active', true)->count()),
            Stat::make('Total PPKA', User::query()->where('jabatan', Jabatan::Ppka->value)->where('is_active', true)->count()),
            Stat::make('Upload Lengkap', DinasanUpload::query()->where('status', UploadStatus::Lengkap->value)->count())->color('success'),
            Stat::make('Upload Belum Lengkap', DinasanUpload::query()->where('status', UploadStatus::BelumLengkap->value)->count())->color('warning'),
            Stat::make('Tidak Upload', (clone $operationalUsers)->whereDoesntHave('dinasanUploads')->count())->color('danger'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
