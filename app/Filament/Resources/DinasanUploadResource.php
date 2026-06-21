<?php

namespace App\Filament\Resources;

use App\Enums\Jabatan;
use App\Enums\UploadStatus;
use App\Filament\Resources\DinasanUploadResource\Pages;
use App\Models\DinasanUpload;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DinasanUploadResource extends Resource
{
    protected static ?string $model = DinasanUpload::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static ?string $navigationLabel = 'Upload Dinasan';

    protected static ?string $modelLabel = 'Upload Dinasan';

    protected static ?string $pluralModelLabel = 'Upload Dinasan';

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->isAdmin() ? 'Monitoring' : 'Dinasan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Dinasan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pegawai')
                            ->relationship(
                                'user',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->operational()->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                        Forms\Components\DatePicker::make('tanggal_dinasan')
                            ->label('Tanggal Dinasan')
                            ->required()
                            ->native(false)
                            ->unique(
                                table: DinasanUpload::class,
                                column: 'tanggal_dinasan',
                                ignoreRecord: true,
                                modifyRuleUsing: fn ($rule, Forms\Get $get) => $rule->where('user_id', $get('user_id') ?: auth()->id())
                            ),
                        Forms\Components\FileUpload::make('file_pdf')
                            ->label('Gambar Serah Terima')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                            ->maxSize(5120)
                            ->directory('serah-terima')
                            ->disk('public')
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options(UploadStatus::options())
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                    ]),
                Forms\Components\Section::make('Dokumentasi')
                    ->schema([
                        Forms\Components\Repeater::make('dokumentasi')
                            ->relationship()
                            ->label('Foto Dokumentasi')
                            ->schema([
                                Forms\Components\FileUpload::make('foto')
                                    ->label('Foto')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                                    ->maxSize(5120)
                                    ->directory('dokumentasi')
                                    ->disk('public')
                                    ->imagePreviewHeight('160')
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->maxItems(4)
                            ->addActionLabel('Tambah Foto')
                            ->reorderable(false)
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                Tables\Columns\TextColumn::make('user.jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                Tables\Columns\TextColumn::make('tanggal_dinasan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_upload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (UploadStatus $state): string => $state->label())
                    ->color(fn (UploadStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('dokumentasi_count')
                    ->counts('dokumentasi')
                    ->label('Foto')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_dinasan')
                            ->label('Tanggal')
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['tanggal_dinasan'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('tanggal_dinasan', $date))),
                Tables\Filters\SelectFilter::make('bulan')
                    ->options(self::monthOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['value'] ?? null, fn (Builder $query, int|string $month): Builder => $query->whereMonth('tanggal_dinasan', $month))),
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(self::yearOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['value'] ?? null, fn (Builder $query, int|string $year): Builder => $query->whereYear('tanggal_dinasan', $year))),
                Tables\Filters\SelectFilter::make('jabatan')
                    ->options(Jabatan::operationalOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['value'] ?? null, fn (Builder $query, string $jabatan): Builder => $query->whereHas(
                            'user',
                            fn (Builder $userQuery): Builder => $userQuery->where('jabatan', $jabatan)
                        )))
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pegawai')
                    ->options(fn (): array => User::query()->operational()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                Tables\Filters\SelectFilter::make('status')
                    ->options(UploadStatus::options()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ])
            ->defaultSort('tanggal_dinasan', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pegawai')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')->label('Nama'),
                        Infolists\Components\TextEntry::make('user.nip')->label('NIP'),
                        Infolists\Components\TextEntry::make('user.jabatan')->label('Jabatan')->badge(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (UploadStatus $state): string => $state->label())
                            ->color(fn (UploadStatus $state): string => $state->color()),
                    ]),
                Infolists\Components\Section::make('Detail Upload')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal_dinasan')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('tanggal_upload')
                            ->dateTime('d M Y H:i'),
                        Infolists\Components\ImageEntry::make('file_pdf')
                            ->label('Gambar Serah Terima')
                            ->disk('public')
                            ->height(240),
                    ]),
                Infolists\Components\Section::make('Dokumentasi')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('dokumentasi')
                            ->schema([
                                Infolists\Components\ImageEntry::make('foto')
                                    ->disk('public')
                                    ->height(180),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'dokumentasi']);
        $user = auth()->user();

        return $user ? $query->visibleTo($user) : $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isOperational() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->isAdmin() || $record->user_id === $user?->id;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, class-string<\Filament\Resources\Pages\Page>>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDinasanUploads::route('/'),
            'create' => Pages\CreateDinasanUpload::route('/create'),
            'view' => Pages\ViewDinasanUpload::route('/{record}'),
            'edit' => Pages\EditDinasanUpload::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function monthOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [$month => now()->month($month)->translatedFormat('F')])
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private static function yearOptions(): array
    {
        $currentYear = now()->year;

        return collect(range($currentYear - 3, $currentYear + 1))
            ->reverse()
            ->mapWithKeys(fn (int $year): array => [$year => $year])
            ->all();
    }
}
