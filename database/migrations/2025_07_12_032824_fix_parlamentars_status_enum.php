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
        Schema::table('parlamentars', function (Blueprint $table) {
            // Drop the existing enum constraint and recreate as string
            $table->string('status')->default('ativo')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parlamentars', function (Blueprint $table) {
            $table->enum('status', ['ativo', 'licenciado', 'inativo'])->default('ativo')->change();
        });
    }
};
