<?php

namespace App\Filament\Widgets;

use App\Enums\UploadStatus;
use App\Models\DinasanUpload;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $monthlyUploads = DinasanUpload::query()
            ->whereBelongsTo(auth()->user())
            ->whereMonth('tanggal_dinasan', now()->month)
            ->whereYear('tanggal_dinasan', now()->year);

        return [
            Stat::make('Upload Lengkap', (clone $monthlyUploads)->where('status', UploadStatus::Lengkap->value)->count())->color('success'),
            Stat::make('Upload Belum Lengkap', (clone $monthlyUploads)->where('status', UploadStatus::BelumLengkap->value)->count())->color('warning'),
            Stat::make('Total Upload Bulan Ini', (clone $monthlyUploads)->count()),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isOperational() ?? false;
    }
}
