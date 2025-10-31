<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            // bisa null: saat dibuat oleh job/seed tanpa session
            $table->foreignId('user_id')->nullable()
                  ->constrained()->nullOnDelete();

            // route key aman: pastikan unik
            $table->string('code', 40)->unique()->index();

            // --- inti data ---
            $table->string('category', 100);
            $table->string('status', 32)->index();
            $table->longText('description')->nullable();
            $table->longText('admin_note')->nullable();

            // --- reporter (PII terenkripsi) ---
            $table->longText('reporter_name')->nullable();
            $table->longText('reporter_phone')->nullable();
            $table->boolean('reporter_is_disability')->nullable();
            $table->longText('reporter_age')->nullable();      // terenkripsi
            $table->longText('reporter_job')->nullable();
            $table->unsignedTinyInteger('reporter_age_bucket')->nullable(); // 1..7
            $table->string('reporter_phone_hash', 64)->nullable()->index(); // sha256

            // --- lokasi (nama di-resolve server-side) ---
            $table->string('province_code', 2)->nullable();
            $table->string('province_name', 150)->nullable();
            $table->string('regency_code', 4)->nullable();
            $table->string('regency_name', 150)->nullable();
            $table->string('district_code', 6)->nullable();
            $table->string('district_name', 150)->nullable();
            $table->longText('reporter_address')->nullable();

            // --- perpetrator (terenkripsi) ---
            $table->longText('perpetrator_name')->nullable();
            $table->longText('perpetrator_job')->nullable();
            $table->longText('perpetrator_age')->nullable();   // terenkripsi
            $table->unsignedTinyInteger('perpetrator_age_bucket')->nullable();

            // --- operasional (opsional) ---
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('priority')->nullable(); // 1-5 misalnya
            $table->timestamp('due_at')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('pinned_until')->nullable();

            // closed_at otomatis via model hook saat status -> closed*
            $table->timestamp('closed_at')->nullable();

            // index tambahan untuk performa list/filter
            $table->index(['user_id', 'created_at'], 'complaints_user_created_idx');
            $table->index(['province_code','regency_code','district_code'], 'complaints_region_codes_idx');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
