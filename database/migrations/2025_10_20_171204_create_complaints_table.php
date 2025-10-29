<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 40)->unique();

            // PII terenkripsi (pakai $casts di Model)
            $table->text('reporter_name')->nullable();
            $table->text('reporter_phone')->nullable();
            $table->text('reporter_address')->nullable();
            $table->text('reporter_job')->nullable();
            $table->char('reporter_phone_hash', 64)->nullable()->index('idx_complaints_rphash');

            // Umur asli (terenkripsi) + bucket untuk agregasi
            $table->text('reporter_age')->nullable();
            $table->unsignedTinyInteger('reporter_age_bucket')->nullable()->index('idx_complaints_age_bucket');

            $table->boolean('reporter_is_disability')->nullable()->comment('null=unknown, 0=no, 1=yes');

            // Lokasi
            $table->string('province_code', 10)->nullable()->index('idx_complaints_prov_code');
            $table->string('province_name', 100)->nullable();
            $table->string('regency_code', 10)->nullable()->index('idx_complaints_reg_code');
            $table->string('regency_name', 100)->nullable();
            $table->string('district_code', 10)->nullable()->index('idx_complaints_dist_code');
            $table->string('district_name', 100)->nullable();

            // Data pelaku (sensitif)
            $table->text('perpetrator_name')->nullable();
            $table->text('perpetrator_job')->nullable();
            $table->text('perpetrator_age')->nullable();
            $table->unsignedTinyInteger('perpetrator_age_bucket')->nullable()->index('idx_complaints_p_age_bucket');

            // Kategori & konten
            $table->string('category', 120)->nullable()->index('idx_complaints_category');
            $table->text('description');
            $table->text('admin_note')->nullable();

            // Status & closed_at
            $table->string('status', 30)->default('submitted')->index('idx_complaints_status');
            $table->timestamp('closed_at')->nullable()->index('idx_complaints_closed_at');

            // Kolom operasional (SLA/assign/pin)
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->unsignedTinyInteger('priority')->default(2)->index(); // 1=High,2=Normal,3=Low
            $table->timestamp('due_at')->nullable()->index();
            $table->timestamp('first_response_at')->nullable()->index();
            $table->timestamp('pinned_until')->nullable()->index();

            $table->timestamps();

            // Index komposit umum
            $table->index(['created_at'], 'idx_complaints_created_at');
            $table->index(['updated_at'], 'idx_complaints_updated_at');
            $table->index(['user_id', 'status', 'created_at'], 'idx_complaints_user_status_created');
            $table->index(['status', 'updated_at'], 'idx_complaints_status_updated');
            $table->index(['province_code', 'regency_code', 'district_code'], 'idx_complaints_location_codes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
