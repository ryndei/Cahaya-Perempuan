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

            /**
             * PII / data sensitif (TEXT agar aman untuk ciphertext).
             * Akan DIENKRIPSI di Model via $casts = ['encrypted' => ...].
             */
            $t->text('reporter_name')->nullable();       // encrypted
            $t->text('reporter_phone')->nullable();      // encrypted
            $t->text('reporter_address')->nullable();    // encrypted
            $t->text('reporter_job')->nullable();        // encrypted

            // Blind index untuk pencarian exact match nomor telepon (tanpa buka PII)
            $t->char('reporter_phone_hash', 64)->nullable()->index();

            // Umur pelapor → simpan terenkripsi + bucket polos untuk agregasi/filter
            $t->text('reporter_age')->nullable();              // encrypted (isi mentah)
            $t->unsignedTinyInteger('reporter_age_bucket')->nullable()->index(); // 1..7 (lihat Model)

            // Disabilitas: tri-state (null=unknown, 0=no, 1=yes) — polos untuk filter
            $t->boolean('reporter_is_disability')->nullable()->comment('null=unknown, 0=no, 1=yes');

            // Lokasi terstruktur + nama wilayah (polos untuk filter/sort)
            $t->string('province_code', 10)->nullable()->index();
            $t->string('province_name', 100)->nullable();
            $t->string('regency_code', 10)->nullable()->index();
            $t->string('regency_name', 100)->nullable();
            $t->string('district_code', 10)->nullable()->index();
            $t->string('district_name', 100)->nullable();

            // Data pelaku (sensitif)
            $t->text('perpetrator_name')->nullable();   // encrypted
            $t->text('perpetrator_job')->nullable();    // encrypted
            $t->text('perpetrator_age')->nullable();    // encrypted
            $t->unsignedTinyInteger('perpetrator_age_bucket')->nullable()->index();

            // Kategori (opsional, polos)
            $t->string('category')->nullable();

            // Narasi & catatan internal (sensitif → encrypted)
            $t->text('description');                    // encrypted
            $t->text('admin_note')->nullable();         // encrypted

            // Lampiran: simpan PATH saja (file-nya disimpan di disk privat)
            $t->string('attachment_path')->nullable();

            // Status untuk workflow (polos untuk filter/report)
            $t->string('status', 30)->default('submitted')->index(); // submitted|in_review|follow_up|closed|...

            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('complaints');
    }
};
