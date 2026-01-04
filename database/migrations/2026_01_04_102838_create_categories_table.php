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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('slug_ar')->unique();
            $table->string('slug_en')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories');
            $table->timestamps();

            $table->index(['name_ar', 'name_en', 'slug_ar', 'slug_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name_ar', 'name_en', 'slug_ar', 'slug_en']);
        });
        Schema::dropIfExists('categories');
    }
};
