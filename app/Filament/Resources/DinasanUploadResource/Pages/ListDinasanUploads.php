<?php

namespace App\Filament\Resources\DinasanUploadResource\Pages;

use App\Filament\Resources\DinasanUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDinasanUploads extends ListRecords
{
    protected static string $resource = DinasanUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->isOperational() ?? false),
        ];
    }
}
