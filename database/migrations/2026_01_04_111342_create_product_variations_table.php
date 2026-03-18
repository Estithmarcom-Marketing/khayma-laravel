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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('color_id')->constrained('colors')->onDelete('cascade')->nullable();
            $table->foreignId('size_id')->constrained('sizes')->onDelete('cascade')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('offer', 8, 2)->nullable();
            $table->dateTime('offer_expired_date')->nullable();
            $table->dateTime('offer_started_date')->nullable();
            $table->integer('stock_quantity');
            $table->string('sku')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(
                ['product_id', 'color_id', 'size_id', 'sku', 'offer_expired_date', 'offer_started_date'],
                'pv_product_color_size_sku_offer_idx'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropIndex('pv_product_color_size_sku_offer_idx');
        });

        Schema::dropIfExists('product_variations');
    }
};
