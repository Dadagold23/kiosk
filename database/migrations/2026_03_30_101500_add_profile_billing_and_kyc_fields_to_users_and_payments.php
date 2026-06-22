<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $userColumns = [
                    'alternate_phone' => fn () => $table->string('alternate_phone', 30)->nullable()->after('phone'),
                    'date_of_birth' => fn () => $table->date('date_of_birth')->nullable()->after('alternate_phone'),
                    'gender' => fn () => $table->string('gender', 20)->nullable()->after('date_of_birth'),
                    'nationality' => fn () => $table->string('nationality', 100)->nullable()->after('gender'),
                    'state' => fn () => $table->string('state', 120)->nullable()->after('nationality'),
                    'city' => fn () => $table->string('city', 120)->nullable()->after('state'),
                    'postal_code' => fn () => $table->string('postal_code', 30)->nullable()->after('city'),
                    'delivery_contact_name' => fn () => $table->string('delivery_contact_name')->nullable()->after('address'),
                    'delivery_phone' => fn () => $table->string('delivery_phone', 30)->nullable()->after('delivery_contact_name'),
                    'delivery_address_line_1' => fn () => $table->string('delivery_address_line_1')->nullable()->after('delivery_phone'),
                    'delivery_address_line_2' => fn () => $table->string('delivery_address_line_2')->nullable()->after('delivery_address_line_1'),
                    'delivery_city' => fn () => $table->string('delivery_city', 120)->nullable()->after('delivery_address_line_2'),
                    'delivery_state' => fn () => $table->string('delivery_state', 120)->nullable()->after('delivery_city'),
                    'delivery_postal_code' => fn () => $table->string('delivery_postal_code', 30)->nullable()->after('delivery_state'),
                    'delivery_country' => fn () => $table->string('delivery_country', 120)->nullable()->after('delivery_postal_code'),
                    'delivery_landmark' => fn () => $table->string('delivery_landmark')->nullable()->after('delivery_country'),
                    'preferred_payment_method' => fn () => $table->string('preferred_payment_method', 50)->nullable()->after('delivery_landmark'),
                    'billing_name' => fn () => $table->string('billing_name')->nullable()->after('preferred_payment_method'),
                    'billing_email' => fn () => $table->string('billing_email')->nullable()->after('billing_name'),
                    'billing_phone' => fn () => $table->string('billing_phone', 30)->nullable()->after('billing_email'),
                    'billing_address' => fn () => $table->text('billing_address')->nullable()->after('billing_phone'),
                    'kyc_status' => fn () => $table->string('kyc_status', 50)->nullable()->after('billing_address'),
                    'kyc_submitted_at' => fn () => $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status'),
                    'kyc_approved_at' => fn () => $table->timestamp('kyc_approved_at')->nullable()->after('kyc_submitted_at'),
                    'identity_type' => fn () => $table->string('identity_type', 50)->nullable()->after('kyc_approved_at'),
                    'identity_number' => fn () => $table->string('identity_number', 120)->nullable()->after('identity_type'),
                    'identity_country' => fn () => $table->string('identity_country', 120)->nullable()->after('identity_number'),
                ];

                foreach ($userColumns as $column => $definition) {
                    if (! Schema::hasColumn('users', $column)) {
                        $definition();
                    }
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $paymentColumns = [
                    'payer_name' => fn () => $table->string('payer_name')->nullable()->after('payment_method'),
                    'payer_email' => fn () => $table->string('payer_email')->nullable()->after('payer_name'),
                    'payer_phone' => fn () => $table->string('payer_phone', 30)->nullable()->after('payer_email'),
                    'billing_address' => fn () => $table->text('billing_address')->nullable()->after('payer_phone'),
                    'delivery_address_snapshot' => fn () => $table->text('delivery_address_snapshot')->nullable()->after('billing_address'),
                    'customer_profile_snapshot' => fn () => $table->json('customer_profile_snapshot')->nullable()->after('delivery_address_snapshot'),
                ];

                foreach ($paymentColumns as $column => $definition) {
                    if (! Schema::hasColumn('payments', $column)) {
                        $definition();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $drops = [];

                foreach ([
                    'alternate_phone',
                    'date_of_birth',
                    'gender',
                    'nationality',
                    'state',
                    'city',
                    'postal_code',
                    'delivery_contact_name',
                    'delivery_phone',
                    'delivery_address_line_1',
                    'delivery_address_line_2',
                    'delivery_city',
                    'delivery_state',
                    'delivery_postal_code',
                    'delivery_country',
                    'delivery_landmark',
                    'preferred_payment_method',
                    'billing_name',
                    'billing_email',
                    'billing_phone',
                    'billing_address',
                    'kyc_status',
                    'kyc_submitted_at',
                    'kyc_approved_at',
                    'identity_type',
                    'identity_number',
                    'identity_country',
                ] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $drops[] = $column;
                    }
                }

                if ($drops !== []) {
                    $table->dropColumn($drops);
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $drops = [];

                foreach ([
                    'payer_name',
                    'payer_email',
                    'payer_phone',
                    'billing_address',
                    'delivery_address_snapshot',
                    'customer_profile_snapshot',
                ] as $column) {
                    if (Schema::hasColumn('payments', $column)) {
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
