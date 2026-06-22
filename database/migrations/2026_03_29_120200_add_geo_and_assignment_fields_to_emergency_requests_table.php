<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('emergency_requests')) {
            return;
        }

        Schema::table('emergency_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('emergency_requests', 'country_code')) {
                $table->string('country_code', 2)->default('NG')->after('user_id');
            }

            if (! Schema::hasColumn('emergency_requests', 'country_name')) {
                $table->string('country_name', 120)->default('Nigeria')->after('country_code');
            }

            if (! Schema::hasColumn('emergency_requests', 'state_code')) {
                $table->string('state_code', 50)->nullable()->after('location_text');
            }

            if (! Schema::hasColumn('emergency_requests', 'state_name')) {
                $table->string('state_name', 120)->nullable()->after('state_code');
            }

            if (! Schema::hasColumn('emergency_requests', 'local_government_area')) {
                $table->string('local_government_area', 150)->nullable()->after('state_name');
            }

            if (! Schema::hasColumn('emergency_requests', 'assigned_unit_id')) {
                $table->foreignId('assigned_unit_id')->nullable()->after('assigned_unit')->constrained('emergency_service_units')->nullOnDelete();
            }

            if (! Schema::hasColumn('emergency_requests', 'assigned_unit_contact')) {
                $table->string('assigned_unit_contact', 50)->nullable()->after('assigned_unit_id');
            }

            if (! Schema::hasColumn('emergency_requests', 'assigned_unit_toll_free')) {
                $table->string('assigned_unit_toll_free', 50)->nullable()->after('assigned_unit_contact');
            }

            if (! Schema::hasColumn('emergency_requests', 'dispatch_reference')) {
                $table->string('dispatch_reference')->nullable()->after('assigned_unit_toll_free');
            }

            if (! Schema::hasColumn('emergency_requests', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('dispatch_reference');
            }

            if (! Schema::hasColumn('emergency_requests', 'last_tracked_at')) {
                $table->timestamp('last_tracked_at')->nullable()->after('assigned_at');
            }

            if (! Schema::hasColumn('emergency_requests', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('last_tracked_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('emergency_requests')) {
            return;
        }

        Schema::table('emergency_requests', function (Blueprint $table) {
            if (Schema::hasColumn('emergency_requests', 'assigned_unit_id')) {
                $table->dropConstrainedForeignId('assigned_unit_id');
            }

            $columns = [
                'country_code',
                'country_name',
                'state_code',
                'state_name',
                'local_government_area',
                'assigned_unit_contact',
                'assigned_unit_toll_free',
                'dispatch_reference',
                'assigned_at',
                'last_tracked_at',
                'resolved_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('emergency_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
