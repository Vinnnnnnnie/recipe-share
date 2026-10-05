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
        Schema::table('recipe_ingredient', static function (Blueprint $table) {
            $table->dropColumn('measurement');
            $table->enum('measurement', [
                'g',
                'tsp',
                'tbsp',
                'ml',
                'l',
                'kg',
                'cup',
                'whole',
                'oZ',
                ''])->default('')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_ingredient', static function (Blueprint $table) {
            $table->dropColumn('measurement');
            $table->enum('measurement', [
                'g',
                'tsp',
                'tbsp',
                'ml',
                'l',
                'kg',
                'cup',
                ''])->default('')->after('quantity');
        });
    }
};
