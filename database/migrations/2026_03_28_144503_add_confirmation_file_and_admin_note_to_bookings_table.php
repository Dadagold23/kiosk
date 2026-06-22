<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        if (! Schema::hasColumn('bookings', 'confirmation_file')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('confirmation_file')->nullable();
            });
        }

        if (! Schema::hasColumn('bookings', 'admin_note')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->text('admin_note')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('bookings', 'confirmation_file')) {
                $drops[] = 'confirmation_file';
            }

            if (Schema::hasColumn('bookings', 'admin_note')) {
                $drops[] = 'admin_note';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
