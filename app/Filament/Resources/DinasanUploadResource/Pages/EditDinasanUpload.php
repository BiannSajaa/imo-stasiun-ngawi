<?php

namespace App\Filament\Resources\DinasanUploadResource\Pages;

use App\Filament\Resources\DinasanUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDinasanUpload extends EditRecord
{
    protected static string $resource = DinasanUploadResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['tanggal_upload'] = $this->record->tanggal_upload ?? now();

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncStatus();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }
}
