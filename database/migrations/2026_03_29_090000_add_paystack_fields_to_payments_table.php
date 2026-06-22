<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 10)->default(config('kiosk.payments.currency', 'NGN'))->after('amount');
            }

            if (! Schema::hasColumn('payments', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->after('reference');
            }

            if (! Schema::hasColumn('payments', 'gateway_response')) {
                $table->string('gateway_response')->nullable()->after('status');
            }

            if (! Schema::hasColumn('payments', 'gateway_verified_at')) {
                $table->timestamp('gateway_verified_at')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            $drops = [];

            foreach (['currency', 'gateway_transaction_id', 'gateway_response', 'gateway_verified_at'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
