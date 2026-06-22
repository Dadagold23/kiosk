<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'fulfillment_status')) {
                $table->string('fulfillment_status', 50)->default('pending')->after('subtotal');
            }

            if (! Schema::hasColumn('order_items', 'logistics_partner')) {
                $table->string('logistics_partner')->nullable()->after('fulfillment_status');
            }

            if (! Schema::hasColumn('order_items', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('logistics_partner');
            }

            if (! Schema::hasColumn('order_items', 'tracking_url')) {
                $table->string('tracking_url', 2048)->nullable()->after('tracking_number');
            }

            if (! Schema::hasColumn('order_items', 'last_tracked_at')) {
                $table->timestamp('last_tracked_at')->nullable()->after('tracking_url');
            }

            if (! Schema::hasColumn('order_items', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('last_tracked_at');
            }

            if (! Schema::hasColumn('order_items', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $drops = [];

            foreach ([
                'fulfillment_status',
                'logistics_partner',
                'tracking_number',
                'tracking_url',
                'last_tracked_at',
                'shipped_at',
                'delivered_at',
            ] as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
