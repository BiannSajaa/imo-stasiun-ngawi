<?php

namespace App\Filament\Resources\DinasanUploadResource\Pages;

use App\Filament\Resources\DinasanUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDinasanUpload extends ViewRecord
{
    protected static string $resource = DinasanUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
