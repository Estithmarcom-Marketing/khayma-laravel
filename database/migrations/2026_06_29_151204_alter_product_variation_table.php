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
        // Drop the existing foreign keys so the columns can be altered.
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropForeign(['size_id']);
        });

        // Make the columns nullable, then re-create the foreign keys
        // with "set null" on delete and on update.
        Schema::table('product_variations', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable()->change();
            $table->unsignedBigInteger('size_id')->nullable()->change();

            $table->foreign('color_id')
                ->references('id')->on('colors')
                ->nullOnDelete()
                ->onUpdate('set null');

            $table->foreign('size_id')
                ->references('id')->on('sizes')
                ->nullOnDelete()
                ->onUpdate('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropForeign(['size_id']);
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable(false)->change();
            $table->unsignedBigInteger('size_id')->nullable(false)->change();

            $table->foreign('color_id')
                ->references('id')->on('colors')
                ->cascadeOnDelete();

            $table->foreign('size_id')
                ->references('id')->on('sizes')
                ->cascadeOnDelete();
        });
    }
};
