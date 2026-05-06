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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('product_variation_id')
                ->constrained('product_variations')
                ->restrictOnDelete();

            $table->unsignedBigInteger('quantity')->default(1);
            $table->decimal('price', 8, 2);
            $table->decimal('offer', 8, 2)->nullable();
            $table->decimal('tax', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
