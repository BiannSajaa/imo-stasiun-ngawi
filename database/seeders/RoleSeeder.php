<?php

namespace Database\Seeders;

use App\Enums\Jabatan;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Jabatan::cases() as $jabatan) {
            Role::findOrCreate($jabatan->value);
        }
    }
}
