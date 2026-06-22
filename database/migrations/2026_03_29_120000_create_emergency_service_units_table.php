<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_service_units', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->default('NG')->index();
            $table->string('state_name')->nullable()->index();
            $table->string('unit_code')->unique();
            $table->string('service_type', 80)->index();
            $table->string('unit_name');
            $table->string('contact_phone', 50)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('toll_free_line', 50)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('website', 2048)->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->boolean('is_national')->default(false)->index();
            $table->string('coverage_scope', 50)->default('state');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_service_units');
    }
};
