<?php

namespace App\Http\Controllers\Reports;

use App\Enums\Jabatan;
use App\Http\Controllers\Controller;
use App\Services\UploadReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

        $pdf = Pdf::loadView('reports.monthly', [
            'summary' => $reportService->monthlySummary($month, $year, $jabatan),
            'rows' => $reportService->monthlyRows($month, $year, $jabatan),
            'month' => $month,
            'year' => $year,
            'jabatan' => $jabatan,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("laporan-imo-{$year}-{$month}.pdf");
    }
}
