<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->repairUsers();
        $this->repairCategories();
        $this->repairProducts();
        $this->repairCarts();
        $this->repairCartItems();
        $this->repairOrders();
        $this->repairOrderItems();
        $this->repairServiceRequests();
        $this->repairConsultancyRequests();
        $this->repairBookings();
        $this->repairEmergencyRequests();
        $this->repairPayments();
    }

    public function down(): void
    {
    }

    private function repairUsers(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable();
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    private function repairCategories(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'type')) {
                $table->string('type', 50)->nullable()->index();
            }
            if (! Schema::hasColumn('categories', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable();
            }
            if (! Schema::hasColumn('categories', 'status')) {
                $table->boolean('status')->default(true);
            }
        });
    }

    private function repairProducts(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('products', 'source_type')) {
                $table->string('source_type', 50)->default('local');
            }
            if (! Schema::hasColumn('products', 'source_marketplace')) {
                $table->string('source_marketplace')->nullable();
            }
            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique();
            }
            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('products', 'quantity')) {
                $table->unsignedInteger('quantity')->default(0);
            }
            if (! Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable();
            }
            if (! Schema::hasColumn('products', 'external_url')) {
                $table->string('external_url', 2048)->nullable();
            }
            if (! Schema::hasColumn('products', 'featured')) {
                $table->boolean('featured')->default(false);
            }
            if (! Schema::hasColumn('products', 'status')) {
                $table->boolean('status')->default(true);
            }
        });
    }

    private function repairCarts(): void
    {
        if (! Schema::hasTable('carts')) {
            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            if (! Schema::hasColumn('carts', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->unique();
            }
        });
    }

    private function repairCartItems(): void
    {
        if (! Schema::hasTable('cart_items')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'cart_id')) {
                $table->foreignId('cart_id')->nullable()->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('cart_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('cart_items', 'item_name')) {
                $table->string('item_name')->nullable();
            }
            if (! Schema::hasColumn('cart_items', 'source_type')) {
                $table->string('source_type', 50)->default('local');
            }
            if (! Schema::hasColumn('cart_items', 'source_marketplace')) {
                $table->string('source_marketplace')->nullable();
            }
            if (! Schema::hasColumn('cart_items', 'qty')) {
                $table->unsignedInteger('qty')->default(1);
            }
            if (! Schema::hasColumn('cart_items', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('cart_items', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('cart_items', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    private function repairOrders(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'order_no')) {
                $table->string('order_no')->nullable()->unique();
            }
            if (! Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type', 50)->nullable();
            }
            if (! Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'service_charge')) {
                $table->decimal('service_charge', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 50)->default('pending');
            }
            if (! Schema::hasColumn('orders', 'order_status')) {
                $table->string('order_status', 50)->default('pending');
            }
            if (! Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable();
            }
            if (! Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable();
            }
            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    private function repairOrderItems(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'order_id')) {
                $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable();
            }
            if (! Schema::hasColumn('order_items', 'qty')) {
                $table->unsignedInteger('qty')->default(1);
            }
            if (! Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('order_items', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('order_items', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    private function repairServiceRequests(): void
    {
        if (! Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('service_requests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('service_requests', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('service_requests', 'title')) {
                $table->string('title')->nullable();
            }
            if (! Schema::hasColumn('service_requests', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('service_requests', 'location')) {
                $table->string('location')->nullable();
            }
            if (! Schema::hasColumn('service_requests', 'preferred_date')) {
                $table->date('preferred_date')->nullable();
            }
            if (! Schema::hasColumn('service_requests', 'budget')) {
                $table->decimal('budget', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('service_requests', 'images')) {
                $table->json('images')->nullable();
            }
            if (! Schema::hasColumn('service_requests', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('service_requests', 'status')) {
                $table->string('status', 50)->default('pending');
            }
            if (! Schema::hasColumn('service_requests', 'payment_status')) {
                $table->string('payment_status', 50)->default('pending');
            }
            if (! Schema::hasColumn('service_requests', 'fee')) {
                $table->decimal('fee', 12, 2)->nullable();
            }
        });
    }

    private function repairConsultancyRequests(): void
    {
        if (! Schema::hasTable('consultancy_requests')) {
            return;
        }

        Schema::table('consultancy_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('consultancy_requests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('consultancy_requests', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('consultancy_requests', 'subject')) {
                $table->string('subject')->nullable();
            }
            if (! Schema::hasColumn('consultancy_requests', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('consultancy_requests', 'preferred_date')) {
                $table->date('preferred_date')->nullable();
            }
            if (! Schema::hasColumn('consultancy_requests', 'assigned_consultant_id')) {
                $table->foreignId('assigned_consultant_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('consultancy_requests', 'status')) {
                $table->string('status', 50)->default('pending');
            }
            if (! Schema::hasColumn('consultancy_requests', 'payment_status')) {
                $table->string('payment_status', 50)->default('pending');
            }
            if (! Schema::hasColumn('consultancy_requests', 'fee')) {
                $table->decimal('fee', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('consultancy_requests', 'report_file')) {
                $table->string('report_file')->nullable();
            }
            if (! Schema::hasColumn('consultancy_requests', 'admin_note')) {
                $table->text('admin_note')->nullable();
            }
        });
    }

    private function repairBookings(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'booking_type')) {
                $table->string('booking_type', 50)->nullable();
            }
            if (! Schema::hasColumn('bookings', 'title')) {
                $table->string('title')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'location')) {
                $table->string('location')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'check_in_date')) {
                $table->date('check_in_date')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'check_out_date')) {
                $table->date('check_out_date')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'travel_date')) {
                $table->date('travel_date')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'persons')) {
                $table->unsignedInteger('persons')->default(1);
            }
            if (! Schema::hasColumn('bookings', 'details')) {
                $table->text('details')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'status')) {
                $table->string('status', 50)->default('pending');
            }
            if (! Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status', 50)->default('pending');
            }
            if (! Schema::hasColumn('bookings', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('bookings', 'confirmation_code')) {
                $table->string('confirmation_code')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'confirmation_file')) {
                $table->string('confirmation_file')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'admin_note')) {
                $table->text('admin_note')->nullable();
            }
        });
    }

    private function repairEmergencyRequests(): void
    {
        if (! Schema::hasTable('emergency_requests')) {
            return;
        }

        Schema::table('emergency_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('emergency_requests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('emergency_requests', 'emergency_type')) {
                $table->string('emergency_type', 50)->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'full_name')) {
                $table->string('full_name')->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'phone')) {
                $table->string('phone', 30)->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'alternate_phone')) {
                $table->string('alternate_phone', 30)->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'location_text')) {
                $table->string('location_text', 500)->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'status')) {
                $table->string('status', 50)->default('pending');
            }
            if (! Schema::hasColumn('emergency_requests', 'assigned_unit')) {
                $table->string('assigned_unit')->nullable();
            }
            if (! Schema::hasColumn('emergency_requests', 'response_note')) {
                $table->text('response_note')->nullable();
            }
        });
    }

    private function repairPayments(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('payments', 'payable_type')) {
                $table->string('payable_type')->nullable();
            }
            if (! Schema::hasColumn('payments', 'payable_id')) {
                $table->unsignedBigInteger('payable_id')->nullable();
            }
            if (! Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable();
            }
            if (! Schema::hasColumn('payments', 'gateway')) {
                $table->string('gateway', 50)->nullable();
            }
            if (! Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->unique();
            }
            if (! Schema::hasColumn('payments', 'receipt_no')) {
                $table->string('receipt_no')->nullable()->unique();
            }
            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status', 50)->default('pending');
            }
            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
            if (! Schema::hasColumn('payments', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }
};
