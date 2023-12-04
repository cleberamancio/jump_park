<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->char('vehiclePlate', 7);
            $table->dateTime('entryDateTime')->default(now());
            $table->dateTime('exitDateTime')->nullable();
            $table->string('priceType', 55)->nullable();
            $table->decimal('price', 12, 2)->default(0.00);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
