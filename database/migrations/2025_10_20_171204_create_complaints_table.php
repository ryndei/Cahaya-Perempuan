<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('complaints', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('code')->unique();                 // CP-YYMMDD-ABCDE
            $t->string('title', 180);
            $t->string('reporter_name', 120)->nullable()->after('title');
            $t->string('reporter_address', 255)->nullable()->after('reporter_name');
            $t->string('reporter_phone', 30)->nullable()->after('reporter_address');
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

