<?php

namespace App\Http\Controllers\Reports;

use App\Enums\Jabatan;
use App\Http\Controllers\Controller;
use App\Services\UploadReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MonthlyReportPdfController extends Controller
{
    public function __invoke(Request $request, UploadReportService $reportService)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'between:2020,2100'],
            'jabatan' => ['nullable', Rule::in(array_keys(Jabatan::operationalOptions()))],
        ]);

        $month = (int) $validated['bulan'];
        $year = (int) $validated['tahun'];
        $jabatan = $validated['jabatan'] ?? null;

        $rows = $reportService->monthlyRows($month, $year, $jabatan)
            ->filter(fn (array $row): bool => $row['upload'] !== null)
            ->map(fn (array $row): array => $this->prepareReportRow($row))
            ->values();
        $generatedAt = now('Asia/Jakarta');

        $pdf = Pdf::loadView('reports.monthly', [
            'summary' => $reportService->monthlySummary($month, $year, $jabatan),
            'rows' => $rows,
            'month' => $month,
            'year' => $year,
            'jabatan' => $jabatan,
            'logos' => $this->logoDataUris(),
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("laporan-imo-{$year}-{$month}.pdf");
    }

    /**
     * @param  array{upload: ?\App\Models\DinasanUpload, user: \App\Models\User}  $row
     * @return array<string, mixed>
     */
    private function prepareReportRow(array $row): array
    {
        $upload = $row['upload'];
        $documents = $upload?->dokumentasi ?? collect();

        $row['serah_terima_name'] = $upload?->file_pdf ? basename($upload->file_pdf) : null;
        $row['serah_terima_image'] = $upload?->file_pdf ? $this->storageImageDataUri($upload->file_pdf) : null;
        $row['kegiatan'] = trim('Dinasan '.($row['user']->jabatan ?: ''));
        $row['dokumentasi_images'] = $documents
            ->take(4)
            ->map(function ($document): ?array {
                $src = $this->storageImageDataUri($document->foto);

                return $src ? [
                    'src' => $src,
                    'name' => basename($document->foto),
                ] : null;
            })
            ->filter()
            ->values()
            ->all();

        return $row;
    }

    /**
     * @return array<string, ?string>
     */
    private function logoDataUris(): array
    {
        return [
            'danantara' => $this->publicImageDataUri('images/report-logos/danantara-indonesia.png'),
            'citar' => $this->publicImageDataUri('images/report-logos/citar.jpeg'),
            'semakin_melayani' => $this->publicImageDataUri('images/report-logos/semakin-melayani.jpeg'),
            'kai' => $this->publicImageDataUri('images/report-logos/kai.jpeg'),
        ];
    }

    private function publicImageDataUri(string $relativePath): ?string
    {
        $path = public_path($relativePath);

        return $this->fileDataUri($path);
    }

    private function storageImageDataUri(string $relativePath): ?string
    {
        $path = Storage::disk('public')->path($relativePath);

        return $this->fileDataUri($path);
    }

    private function fileDataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => null,
        };

        if (! $mime) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }
}
