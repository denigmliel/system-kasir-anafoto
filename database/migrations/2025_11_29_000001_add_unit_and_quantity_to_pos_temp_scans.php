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
        Schema::table('pos_temp_scans', function (Blueprint $table) {
            $table->foreignId('product_unit_id')
                ->nullable()
                ->after('product_code')
                ->constrained('product_units')
                ->nullOnDelete();

            $table->unsignedInteger('quantity')
                ->default(1)
                ->after('product_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_temp_scans', function (Blueprint $table) {
            $table->dropColumn(['quantity']);
            $table->dropConstrainedForeignId('product_unit_id');
        });
    }
};
