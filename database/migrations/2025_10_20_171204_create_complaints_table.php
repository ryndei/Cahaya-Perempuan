<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('complaints', function (Blueprint $t) {
            $t->id();

            // Relasi user yang membuat pengaduan
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Kode unik (mis. CP-YYMMDD-ABCDE)
            $t->string('code')->unique();

            // Data pelapor (opsional)
            $t->string('reporter_name', 120)->nullable();
            $t->string('reporter_phone', 30)->nullable();

            // Disabilitas: gunakan tri-state (null=tidak diisi, 0=tidak, 1=ya)
            $t->boolean('reporter_is_disability')->nullable()->comment('null=unknown, 0=no, 1=yes');

            // Umur & pekerjaan pelapor
            $t->unsignedTinyInteger('reporter_age')->nullable();
            $t->string('reporter_job', 100)->nullable();

            // Lokasi terstruktur + alamat detail
            $t->string('province_code', 10)->nullable()->index();
            $t->string('province_name', 100)->nullable();
            $t->string('regency_code', 10)->nullable()->index();
            $t->string('regency_name', 100)->nullable();
            $t->string('district_code', 10)->nullable()->index();
            $t->string('district_name', 100)->nullable();

            // Alamat spesifik (jalan, RT/RW, dsb.)
            $t->string('reporter_address', 255)->nullable();

            // Data pelaku (opsional)
            $t->string('perpetrator_name', 120)->nullable();
            $t->string('perpetrator_job', 100)->nullable();
            $t->unsignedTinyInteger('perpetrator_age')->nullable();

            // Lainnya
            $t->string('category')->nullable();           // opsional
            $t->text('description');
            $t->string('attachment_path')->nullable();    // opsional
            $t->string('status', 30)->default('submitted')->index(); // submitted|in_review|follow_up|closed
            $t->text('admin_note')->nullable();           // catatan admin (opsional)

            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('complaints');
    }
};
