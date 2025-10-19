<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('complaints', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('reference_code')->unique();
            $t->string('title');
            $t->text('description');
            $t->enum('visibility', ['private','shared_agencies'])->default('private');
            $t->unsignedTinyInteger('priority')->default(3); // 1=tinggi, 3=normal
            $t->timestamp('submitted_at')->nullable();
            $t->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
