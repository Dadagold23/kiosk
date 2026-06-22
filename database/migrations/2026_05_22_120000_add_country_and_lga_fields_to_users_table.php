<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country', 120)->nullable()->after('nationality');
            }

            if (! Schema::hasColumn('users', 'local_government_area')) {
                $table->string('local_government_area', 150)->nullable()->after('state');
            }

            if (! Schema::hasColumn('users', 'delivery_local_government_area')) {
                $table->string('delivery_local_government_area', 150)->nullable()->after('delivery_state');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $drops = [];

            foreach (['country', 'local_government_area', 'delivery_local_government_area'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
