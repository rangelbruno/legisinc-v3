<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cria índice único parcial para (ano, numero) ignorando registros com soft-delete
     * Evita duplicatas de protocolo mesmo com exclusões lógicas
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL: índice parcial nativo
            DB::statement("
                CREATE UNIQUE INDEX IF NOT EXISTS idx_unique_ano_numero_not_deleted
                ON proposicoes (ano, numero)
                WHERE deleted_at IS NULL AND numero IS NOT NULL
            ");
        } else {
            // MySQL: não suporta índice parcial, usa constraint + trigger
            // Alternativa: validação na aplicação (implementada no Model)
            DB::statement("
                ALTER TABLE proposicoes 
                ADD CONSTRAINT unique_ano_numero_check 
                CHECK (deleted_at IS NOT NULL OR numero IS NULL OR 
                       (ano, numero) NOT IN (
                           SELECT ano, numero 
                           FROM proposicoes p2 
                           WHERE p2.deleted_at IS NULL 
                           AND p2.numero IS NOT NULL 
                           AND p2.id != proposicoes.id
                       ))
            ");
        }
    }

    /**
     * Remove o índice/constraint único
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS idx_unique_ano_numero_not_deleted");
        } else {
            try {
                DB::statement("ALTER TABLE proposicoes DROP CONSTRAINT unique_ano_numero_check");
            } catch (Exception $e) {
                // Ignora se o constraint não existir
            }
        }
    }
};