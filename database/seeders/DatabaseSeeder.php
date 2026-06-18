<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (Jabatan::cases() as $jabatan) {
            Role::findOrCreate($jabatan->value);
        }

        $admin = User::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Kepala Stasiun',
                'nip' => 'ADMIN',
                'jabatan' => Jabatan::Admin->value,
                'email' => 'admin@imo.local',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );

        $admin->syncRoles([Jabatan::Admin->value]);
    }
}
