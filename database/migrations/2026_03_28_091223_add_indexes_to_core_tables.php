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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'order_ref')) {
                    $table->index('order_ref');
                }

                if (Schema::hasColumn('orders', 'user_id')) {
                    $table->index('user_id');
                }

                if (Schema::hasColumn('orders', 'order_no')) {
                    $table->index('order_no');
                }

                if (Schema::hasColumn('orders', 'order_status')) {
                    $table->index('order_status');
                }

                if (Schema::hasColumn('orders', 'created_at')) {
                    $table->index('created_at');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'reference')) {
                    $table->index('reference');
                }

                if (Schema::hasColumn('payments', 'receipt_no')) {
                    $table->index('receipt_no');
                }

                if (Schema::hasColumn('payments', 'status')) {
                    $table->index('status');
                }

                if (Schema::hasColumn('payments', 'created_at')) {
                    $table->index('created_at');
                }
            });
        }

        if (Schema::hasTable('service_requests')) {
            Schema::table('service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('service_requests', 'status')) {
                    $table->index('status');
                }

                if (Schema::hasColumn('service_requests', 'payment_status')) {
                    $table->index('payment_status');
                }
            });
        }

        if (Schema::hasTable('consultancy_requests')) {
            Schema::table('consultancy_requests', function (Blueprint $table) {
                if (Schema::hasColumn('consultancy_requests', 'status')) {
                    $table->index('status');
                }

                if (Schema::hasColumn('consultancy_requests', 'payment_status')) {
                    $table->index('payment_status');
                }
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'status')) {
                    $table->index('status');
                }

                if (Schema::hasColumn('bookings', 'payment_status')) {
                    $table->index('payment_status');
                }
            });
        }

        if (Schema::hasTable('emergency_requests')) {
            Schema::table('emergency_requests', function (Blueprint $table) {
                if (Schema::hasColumn('emergency_requests', 'status')) {
                    $table->index('status');
                }

                if (Schema::hasColumn('emergency_requests', 'emergency_type')) {
                    $table->index('emergency_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'order_ref')) {
                    $table->dropIndex(['order_ref']);
                }

                if (Schema::hasColumn('orders', 'user_id')) {
                    $table->dropIndex(['user_id']);
                }

                if (Schema::hasColumn('orders', 'order_no')) {
                    $table->dropIndex(['order_no']);
                }

                if (Schema::hasColumn('orders', 'order_status')) {
                    $table->dropIndex(['order_status']);
                }

                if (Schema::hasColumn('orders', 'created_at')) {
                    $table->dropIndex(['created_at']);
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'reference')) {
                    $table->dropIndex(['reference']);
                }

                if (Schema::hasColumn('payments', 'receipt_no')) {
                    $table->dropIndex(['receipt_no']);
                }

                if (Schema::hasColumn('payments', 'status')) {
                    $table->dropIndex(['status']);
                }

                if (Schema::hasColumn('payments', 'created_at')) {
                    $table->dropIndex(['created_at']);
                }
            });
        }

        if (Schema::hasTable('service_requests')) {
            Schema::table('service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('service_requests', 'status')) {
                    $table->dropIndex(['status']);
                }

                if (Schema::hasColumn('service_requests', 'payment_status')) {
                    $table->dropIndex(['payment_status']);
                }
            });
        }

        if (Schema::hasTable('consultancy_requests')) {
            Schema::table('consultancy_requests', function (Blueprint $table) {
                if (Schema::hasColumn('consultancy_requests', 'status')) {
                    $table->dropIndex(['status']);
                }

                if (Schema::hasColumn('consultancy_requests', 'payment_status')) {
                    $table->dropIndex(['payment_status']);
                }
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'status')) {
                    $table->dropIndex(['status']);
                }

                if (Schema::hasColumn('bookings', 'payment_status')) {
                    $table->dropIndex(['payment_status']);
                }
            });
        }

        if (Schema::hasTable('emergency_requests')) {
            Schema::table('emergency_requests', function (Blueprint $table) {
                if (Schema::hasColumn('emergency_requests', 'status')) {
                    $table->dropIndex(['status']);
                }

                if (Schema::hasColumn('emergency_requests', 'emergency_type')) {
                    $table->dropIndex(['emergency_type']);
                }
            });
        }
    }
};
