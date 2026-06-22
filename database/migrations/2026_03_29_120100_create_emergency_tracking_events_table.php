<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('emergency_service_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 50)->index();
            $table->string('location_label', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('eta_minutes')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('event_time')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_tracking_events');
    }
};
