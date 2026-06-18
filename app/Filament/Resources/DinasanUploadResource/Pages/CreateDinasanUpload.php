<?php

namespace App\Filament\Resources\DinasanUploadResource\Pages;

use App\Filament\Resources\DinasanUploadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDinasanUpload extends CreateRecord
{
    protected static string $resource = DinasanUploadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->isAdmin() ? $data['user_id'] : auth()->id();
        $data['tanggal_upload'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncStatus();
    }
}
