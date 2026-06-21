<?php

namespace App\Filament\Pages;

use App\Enums\Jabatan;
use App\Enums\UploadStatus;
use App\Services\UploadReportService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class LaporanBulanan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'Laporan Bulanan';

    protected static ?string $title = 'Laporan Bulanan';

    protected static string $view = 'filament.pages.laporan-bulanan';

    public ?int $bulan = null;

    public ?int $tahun = null;

    public ?string $jabatan = null;

    public function mount(): void
    {
        $this->form->fill([
            'bulan' => now()->month,
            'tahun' => now()->year,
            'jabatan' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bulan')
                    ->label('Bulan')
                    ->options($this->monthOptions())
                    ->required()
                    ->native(false)
                    ->live(),
                Forms\Components\Select::make('tahun')
                    ->label('Tahun')
                    ->options($this->yearOptions())
                    ->required()
                    ->native(false)
                    ->live(),
                Forms\Components\Select::make('jabatan')
                    ->options(Jabatan::operationalOptions())
                    ->placeholder('Semua Pegawai')
                    ->native(false)
                    ->live(),
            ])
            ->columns(3);
    }

    /**
     * @return array{lengkap: int, belum_lengkap: int, tidak_upload: int}
     */
    public function getSummary(): array
    {
        return app(UploadReportService::class)->monthlySummary(
            month: (int) $this->bulan,
            year: (int) $this->tahun,
            jabatan: $this->jabatan,
        );
    }

    public function getRows(): Collection
    {
        return app(UploadReportService::class)->monthlyRows(
            month: (int) $this->bulan,
            year: (int) $this->tahun,
            jabatan: $this->jabatan,
        );
    }

    public function statusLabel(UploadStatus|string $status): string
    {
        return $status instanceof UploadStatus ? $status->label() : UploadStatus::from($status)->label();
    }

    public function statusColor(UploadStatus|string $status): string
    {
        return $status instanceof UploadStatus ? $status->color() : UploadStatus::from($status)->color();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (): string => route('reports.monthly.pdf', $this->reportFilters()))
                ->openUrlInNewTab(),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<int, string>
     */
    private function monthOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [$month => now()->month($month)->translatedFormat('F')])
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function yearOptions(): array
    {
        $currentYear = now()->year;

        return collect(range($currentYear - 3, $currentYear + 1))
            ->reverse()
            ->mapWithKeys(fn (int $year): array => [$year => $year])
            ->all();
    }

    /**
     * @return array{bulan: int, tahun: int, jabatan: ?string}
     */
    private function reportFilters(): array
    {
        return [
            'bulan' => $this->bulan ?: now()->month,
            'tahun' => $this->tahun ?: now()->year,
            'jabatan' => $this->jabatan,
        ];
    }
}
