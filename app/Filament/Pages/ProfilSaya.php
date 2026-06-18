<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilSaya extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Akun';

    protected static ?string $navigationLabel = 'Profil';

    protected static ?string $title = 'Profil';

    protected static string $view = 'filament.pages.profil-saya';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ubahPassword')
                ->label('Ubah Password')
                ->icon('heroicon-o-key')
                ->form([
                    Forms\Components\TextInput::make('current_password')
                        ->label('Password Saat Ini')
                        ->password()
                        ->revealable()
                        ->required(),
                    Forms\Components\TextInput::make('password')
                        ->label('Password Baru')
                        ->password()
                        ->revealable()
                        ->required()
                        ->rule(Password::defaults())
                        ->same('password_confirmation'),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Konfirmasi Password')
                        ->password()
                        ->revealable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $user = auth()->user();

                    if (! Hash::check($data['current_password'], $user->password)) {
                        Notification::make()
                            ->title('Password saat ini tidak sesuai.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $user->update(['password' => $data['password']]);

                    Notification::make()
                        ->title('Password berhasil diperbarui.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
