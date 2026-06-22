<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('marketplace_providers')) {
            return;
        }

        Schema::create('marketplace_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_key', 50)->unique();
            $table->string('label', 100);
            $table->boolean('enabled')->default(true)->index();
            $table->text('feed_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_providers');
    }
};
