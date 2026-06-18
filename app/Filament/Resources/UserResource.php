<?php

namespace App\Filament\Resources;

use App\Enums\Jabatan;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'Data Pegawai';

    protected static ?string $modelLabel = 'Pegawai';

    protected static ?string $pluralModelLabel = 'Data Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pegawai')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('jabatan')
                            ->options(Jabatan::options())
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Opsional untuk pegawai. Jika kosong, sistem membuat email internal dari username.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Akun Aktif')
                            ->default(true),
                    ]),
                Forms\Components\Section::make('Password')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->rule(Password::defaults())
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->same('password_confirmation'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jabatan')
                    ->options(Jabatan::options()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Akun'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->form([
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
                    ->action(function (User $record, array $data): void {
                        $record->update(['password' => $data['password']]);

                        Notification::make()
                            ->title('Password pegawai berhasil direset.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record): bool => auth()->id() === $record->id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->isAdmin() ?? false) && auth()->id() !== $record->getKey();
    }

    /**
     * @return array<string, class-string<\Filament\Resources\Pages\Page>>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
