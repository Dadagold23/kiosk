<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('consultancy_requests') && ! Schema::hasColumn('consultancy_requests', 'admin_note')) {
            Schema::table('consultancy_requests', function (Blueprint $table) {
                $table->text('admin_note')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('consultancy_requests') && Schema::hasColumn('consultancy_requests', 'admin_note')) {
            Schema::table('consultancy_requests', function (Blueprint $table) {
                $table->dropColumn('admin_note');
            });
        }
    }
};
