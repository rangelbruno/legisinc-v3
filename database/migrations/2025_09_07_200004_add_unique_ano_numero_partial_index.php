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
            // PostgreSQL: índice parcial nativo (sem soft delete - tabela não usa)
            DB::statement("
                CREATE UNIQUE INDEX IF NOT EXISTS idx_unique_ano_numero_not_null
                ON proposicoes (ano, numero)
                WHERE numero IS NOT NULL
            ");
        } else {
            // MySQL: não suporta índice parcial, usa constraint mais simples
            // Como não há soft delete, apenas garantir que numero não seja duplicado
            DB::statement("
                CREATE UNIQUE INDEX idx_unique_ano_numero_not_null
                ON proposicoes (ano, numero)
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
            DB::statement("DROP INDEX IF EXISTS idx_unique_ano_numero_not_null");
        } else {
            try {
                DB::statement("DROP INDEX IF EXISTS idx_unique_ano_numero_not_null");
            } catch (Exception $e) {
                // Ignora se o índice não existir
            }
        }
    }
};