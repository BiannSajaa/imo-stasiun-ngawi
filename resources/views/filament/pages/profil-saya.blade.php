<x-filament-panels::page>
    @php($user = auth()->user())

    <x-filament::section>
        <dl class="grid gap-6 md:grid-cols-2">
            <div>
                <dt class="text-sm text-gray-500 dark:text-gray-400">Nama</dt>
                <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500 dark:text-gray-400">NIP</dt>
                <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $user->nip ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500 dark:text-gray-400">Jabatan</dt>
                <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $user->jabatan }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500 dark:text-gray-400">Username</dt>
                <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $user->username }}</dd>
            </div>
        </dl>
    </x-filament::section>
</x-filament-panels::page>
