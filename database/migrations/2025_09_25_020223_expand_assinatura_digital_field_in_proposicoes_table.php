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
        Schema::table('proposicoes', function (Blueprint $table) {
            // Expand assinatura_digital from varchar(255) to text to accommodate larger JSON
            $table->text('assinatura_digital')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            // Revert back to varchar(255) - Note: this may truncate data if it exists
            $table->string('assinatura_digital', 255)->nullable()->change();
        });
    }
};
