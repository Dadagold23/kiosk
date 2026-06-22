<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_requests')) {
            Schema::table('service_requests', function (Blueprint $table) {
                if (! Schema::hasColumn('service_requests', 'progress_status')) {
                    $table->string('progress_status', 50)->default('request_received')->after('status');
                }

                if (! Schema::hasColumn('service_requests', 'assigned_team')) {
                    $table->string('assigned_team')->nullable()->after('assigned_to');
                }

                if (! Schema::hasColumn('service_requests', 'tracking_updated_at')) {
                    $table->timestamp('tracking_updated_at')->nullable()->after('assigned_team');
                }

                if (! Schema::hasColumn('service_requests', 'service_window_start')) {
                    $table->timestamp('service_window_start')->nullable()->after('tracking_updated_at');
                }

                if (! Schema::hasColumn('service_requests', 'service_window_end')) {
                    $table->timestamp('service_window_end')->nullable()->after('service_window_start');
                }

                if (! Schema::hasColumn('service_requests', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('service_window_end');
                }
            });
        }

        if (! Schema::hasTable('service_tracking_events')) {
            Schema::create('service_tracking_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
                $table->string('status', 50);
                $table->string('location')->nullable();
                $table->string('next_step')->nullable();
                $table->text('note')->nullable();
                $table->timestamp('event_time')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_tracking_events');

        if (Schema::hasTable('service_requests')) {
            Schema::table('service_requests', function (Blueprint $table) {
                $drops = [];

                foreach ([
                    'progress_status',
                    'assigned_team',
                    'tracking_updated_at',
                    'service_window_start',
                    'service_window_end',
                    'completed_at',
                ] as $column) {
                    if (Schema::hasColumn('service_requests', $column)) {
                        $drops[] = $column;
                    }
                }

                if ($drops !== []) {
                    $table->dropColumn($drops);
                }
            });
        }
    }
};
