<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add FULLTEXT index for name and description combined
            $table->fullText(['name', 'description'], 'products_fulltext_idx');

            // Add individual FULLTEXT indexes to allow separate MATCH score calculation
            $table->fullText('name', 'products_name_fulltext_idx');
            $table->fullText('description', 'products_description_fulltext_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText('products_fulltext_idx');
            $table->dropFullText('products_name_fulltext_idx');
            $table->dropFullText('products_description_fulltext_idx');
        });
    }
};
?>
