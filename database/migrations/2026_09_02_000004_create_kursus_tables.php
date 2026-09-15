<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kursus Ternakan
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code', 50)->unique();
            $table->string('category', 50); // Ruminan, Unggas, Pemakanan Ternakan, Kesihatan Haiwan, Keusahawanan
            $table->text('description')->nullable();
            $table->string('trainer_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('time', 50)->default('8:30 Pagi - 4:30 Petang');
            $table->string('location');
            $table->string('jajahan', 50);
            $table->integer('capacity')->default(30);
            $table->integer('registered_count')->default(0);
            $table->decimal('fee', 8, 2)->default(0.00);
            $table->string('status', 30)->default('Buka'); // Buka, Tutup, Sedang Berlangsung, Selesai, Batal
            $table->string('cover_image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Permohonan & Kehadiran Kursus (dengan Sijil)
        Schema::create('course_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('registration_number', 50)->unique();
            $table->string('status', 30)->default('Menunggu'); // Menunggu, Disahkan, Ditolak, Hadir, Tidak Hadir, Selesai
            $table->string('certificate_number', 50)->nullable()->unique();
            $table->date('certificate_issued_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_applications');
        Schema::dropIfExists('courses');
    }
};
