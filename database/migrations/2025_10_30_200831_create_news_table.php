<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('news', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();

            $t->string('title', 200);
            $t->string('slug', 220)->unique();

            // ringkas untuk kartu/SEO
            $t->string('excerpt', 300)->nullable();

            // isi penuh (boleh HTML)
            $t->longText('body');

            // cover image (public/storage/news/xxx.jpg)
            $t->string('cover_path', 255)->nullable();

            // status & waktu rilis
            $t->enum('status', ['draft','published'])->default('draft')->index();
            $t->timestamp('published_at')->nullable()->index();

            // meta SEO (opsional)
            $t->string('meta_title', 255)->nullable();
            $t->string('meta_description', 255)->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->index(['status', 'published_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('news');
    }
};
