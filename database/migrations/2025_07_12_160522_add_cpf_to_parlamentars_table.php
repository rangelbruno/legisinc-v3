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
            $table->string('cpf', 14)->nullable()->after('email');
            $table->index('cpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parlamentars', function (Blueprint $table) {
            $table->dropIndex(['cpf']);
            $table->dropColumn('cpf');
        });
    }
};
