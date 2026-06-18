<?php

namespace Database\Seeders;

use App\Enums\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Kepala Stasiun',
                'nip' => 'ADMIN',
                'jabatan' => Jabatan::Admin->value,
                'username' => 'admin',
                'email' => 'admin@imo.local',
            ],
            [
                'name' => 'Petugas PJL',
                'nip' => 'PJL001',
                'jabatan' => Jabatan::Pjl->value,
                'username' => 'pjl',
                'email' => 'pjl@imo.local',
            ],
            [
                'name' => 'Petugas PPKA',
                'nip' => 'PPKA001',
                'jabatan' => Jabatan::Ppka->value,
                'username' => 'ppka',
                'email' => 'ppka@imo.local',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::query()->firstOrNew(['username' => $userData['username']]);
            $user->fill([
                ...$userData,
                'is_active' => true,
            ]);

            if (! $user->exists) {
                $user->password = Hash::make('password');
            }

            $user->save();

            $user->syncRoles([$userData['jabatan']]);
        }
    }
}
