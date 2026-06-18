<?php

namespace App\Services;

use App\Enums\Jabatan;
use App\Enums\UploadStatus;
use App\Models\DinasanUpload;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UploadReportService
{
    /**
     * @return array{lengkap: int, belum_lengkap: int, tidak_upload: int}
     */
    public function monthlySummary(int $month, int $year, ?string $jabatan = null): array
    {
        $uploads = $this->uploadQuery($month, $year, $jabatan)->get();
        $employeesWithoutUpload = $this->employeesWithoutUpload($month, $year, $jabatan)->count();

        return [
            UploadStatus::Lengkap->value => $uploads->where('status', UploadStatus::Lengkap)->count(),
            UploadStatus::BelumLengkap->value => $uploads->where('status', UploadStatus::BelumLengkap)->count(),
            UploadStatus::TidakUpload->value => $employeesWithoutUpload,
        ];
    }

    public function uploadQuery(int $month, int $year, ?string $jabatan = null): Builder
    {
        return DinasanUpload::query()
            ->with(['user', 'dokumentasi'])
            ->whereMonth('tanggal_dinasan', $month)
            ->whereYear('tanggal_dinasan', $year)
            ->when($jabatan, fn (Builder $query): Builder => $query->whereHas(
                'user',
                fn (Builder $userQuery): Builder => $userQuery->where('jabatan', $jabatan)
            ))
            ->latest('tanggal_dinasan');
    }

    public function employeesWithoutUpload(int $month, int $year, ?string $jabatan = null): Collection
    {
        return User::query()
            ->whereIn('jabatan', [Jabatan::Pjl->value, Jabatan::Ppka->value])
            ->where('is_active', true)
            ->when($jabatan, fn (Builder $query): Builder => $query->where('jabatan', $jabatan))
            ->whereDoesntHave('dinasanUploads', function (Builder $query) use ($month, $year): void {
                $query->whereMonth('tanggal_dinasan', $month)
                    ->whereYear('tanggal_dinasan', $year);
            })
            ->orderBy('name')
            ->get();
    }

    public function monthlyRows(int $month, int $year, ?string $jabatan = null): Collection
    {
        $uploadedRows = $this->uploadQuery($month, $year, $jabatan)
            ->get()
            ->map(fn (DinasanUpload $upload): array => [
                'user' => $upload->user,
                'tanggal_dinasan' => $upload->tanggal_dinasan,
                'tanggal_upload' => $upload->tanggal_upload,
                'status' => $upload->status,
                'upload' => $upload,
            ]);

        $missingRows = $this->employeesWithoutUpload($month, $year, $jabatan)
            ->map(fn (User $user): array => [
                'user' => $user,
                'tanggal_dinasan' => null,
                'tanggal_upload' => null,
                'status' => UploadStatus::TidakUpload,
                'upload' => null,
            ]);

        return $uploadedRows->concat($missingRows);
    }
}
