<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['email'] = $data['email'] ?: "{$data['username']}@imo.local";

        return $data;
    }

    protected function afterSave(): void
    {
        Role::findOrCreate($this->record->jabatan);
        $this->record->syncRoles([$this->record->jabatan]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->hidden(fn (): bool => auth()->id() === $this->record->getKey()),
        ];
    }
}
