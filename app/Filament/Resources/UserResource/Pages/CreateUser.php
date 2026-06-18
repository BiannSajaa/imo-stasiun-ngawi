<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email'] = $data['email'] ?: "{$data['username']}@imo.local";

        return $data;
    }

    protected function afterCreate(): void
    {
        Role::findOrCreate($this->record->jabatan);
        $this->record->syncRoles([$this->record->jabatan]);
    }
}
