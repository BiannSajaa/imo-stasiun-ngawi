<?php

use App\Enums\Jabatan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('nip')->nullable()->unique()->after('name');
            $table->string('jabatan')->default(Jabatan::Pjl->value)->after('nip');
            $table->string('username')->nullable()->unique()->after('email');
            $table->boolean('is_active')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['nip']);
            $table->dropUnique(['username']);
            $table->dropColumn(['nip', 'jabatan', 'username', 'is_active']);
        });
    }
};
