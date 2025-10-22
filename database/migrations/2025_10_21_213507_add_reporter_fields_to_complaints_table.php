<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $t) {
            // Tambah kolom hanya jika belum ada (aman untuk rerun di lingkungan berbeda)
            if (! Schema::hasColumn('complaints', 'reporter_name')) {
                $t->string('reporter_name', 120)->nullable()->after('title');
            }
            if (! Schema::hasColumn('complaints', 'reporter_address')) {
                $t->string('reporter_address', 255)->nullable()->after('reporter_name');
            }
            if (! Schema::hasColumn('complaints', 'reporter_phone')) {
                $t->string('reporter_phone', 30)->nullable()->after('reporter_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $t) {
            // Hapus kolom saat rollback
            $drop = [];
            foreach (['reporter_name', 'reporter_address', 'reporter_phone'] as $col) {
                if (Schema::hasColumn('complaints', $col)) {
                    $drop[] = $col;
                }
            }
            if ($drop) {
                $t->dropColumn($drop);
            }
        });
    }
};
