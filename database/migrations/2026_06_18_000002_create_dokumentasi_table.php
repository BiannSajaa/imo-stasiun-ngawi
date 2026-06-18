<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('upload_id')->constrained('dinasan_uploads')->cascadeOnDelete();
            $table->string('foto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi');
    }
};
