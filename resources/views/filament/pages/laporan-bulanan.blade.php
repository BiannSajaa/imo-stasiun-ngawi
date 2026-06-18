<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        @php
            $summary = $this->getSummary();
            $rows = $this->getRows();
        @endphp

        <div class="grid gap-4 md:grid-cols-3">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Upload Lengkap</div>
                <div class="mt-2 text-3xl font-semibold text-success-600">{{ $summary['lengkap'] }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Upload Belum Lengkap</div>
                <div class="mt-2 text-3xl font-semibold text-warning-600">{{ $summary['belum_lengkap'] }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Tidak Upload</div>
                <div class="mt-2 text-3xl font-semibold text-danger-600">{{ $summary['tidak_upload'] }}</div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                    <thead>
                        <tr class="text-left font-semibold text-gray-700 dark:text-gray-200">
                            <th class="px-3 py-3">Nama</th>
                            <th class="px-3 py-3">NIP</th>
                            <th class="px-3 py-3">Jabatan</th>
                            <th class="px-3 py-3">Tanggal Dinasan</th>
                            <th class="px-3 py-3">Tanggal Upload</th>
                            <th class="px-3 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($rows as $row)
                            <tr>
                                <td class="px-3 py-3">{{ $row['user']->name }}</td>
                                <td class="px-3 py-3">{{ $row['user']->nip ?: '-' }}</td>
                                <td class="px-3 py-3">{{ $row['user']->jabatan }}</td>
                                <td class="px-3 py-3">{{ $row['tanggal_dinasan']?->format('d M Y') ?: '-' }}</td>
                                <td class="px-3 py-3">{{ $row['tanggal_upload']?->format('d M Y H:i') ?: '-' }}</td>
                                <td class="px-3 py-3">{{ $this->statusLabel($row['status']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
