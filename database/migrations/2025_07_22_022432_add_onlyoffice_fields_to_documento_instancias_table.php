<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documento_instancias', function (Blueprint $table) {
            $table->string('document_key')->nullable()->after('modelo_id');
            $table->json('colaboradores')->nullable()->after('metadados');
            $table->timestamp('editado_em')->nullable()->after('colaboradores');
        });
        
        // Popula document_key para registros existentes
        DB::table('documento_instancias')
            ->whereNull('document_key')
            ->update(['document_key' => DB::raw("'doc_' || id || '_' || EXTRACT(EPOCH FROM created_at)::integer")]);
            
        // Agora torna o campo único e não nulo
        Schema::table('documento_instancias', function (Blueprint $table) {
            $table->string('document_key')->unique()->change();
            $table->index('document_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_instancias', function (Blueprint $table) {
            $table->dropIndex(['document_key']);
            $table->dropColumn(['document_key', 'colaboradores', 'editado_em']);
        });
    }
};
