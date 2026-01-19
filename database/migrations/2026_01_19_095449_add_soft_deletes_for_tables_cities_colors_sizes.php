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
        Schema::table('cities', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('colors', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->softDeletes();
            $table->unique(['code', 'deleted_at']);
        });
        Schema::table('sizes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('colors', function (Blueprint $table) {
            $table->dropUnique(['code', 'deleted_at']);
            $table->dropSoftDeletes();
            $table->unique('code');
        });
        Schema::table('sizes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
