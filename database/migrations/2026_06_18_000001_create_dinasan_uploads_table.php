<?php

use App\Enums\UploadStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinasan_uploads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_dinasan');
            $table->timestamp('tanggal_upload')->nullable();
            $table->string('file_pdf')->nullable();
            $table->string('status')->default(UploadStatus::BelumLengkap->value);
            $table->timestamps();

            $table->unique(['user_id', 'tanggal_dinasan']);
            $table->index(['tanggal_dinasan', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinasan_uploads');
    }
};
