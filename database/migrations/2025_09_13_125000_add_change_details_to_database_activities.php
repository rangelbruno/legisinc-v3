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
        Schema::table('database_activities', function (Blueprint $table) {
            $table->json('change_details')->nullable()->after('sql_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('database_activities', function (Blueprint $table) {
            $table->dropColumn('change_details');
        });
    }
};