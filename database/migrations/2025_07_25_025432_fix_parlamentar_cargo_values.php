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
        // Corrigir valores de cargo em minúscula para a capitalização correta
        $cargoMapping = [
            'vereador' => 'Vereador',
            'vereadora' => 'Vereadora', 
            'presidente' => 'Presidente da Câmara',
            'vice_presidente' => 'Vice-Presidente',
            'primeiro_secretario' => '1º Secretário',
            'segundo_secretario' => '2º Secretário',
            'presidente da câmara' => 'Presidente da Câmara',
            'vice-presidente' => 'Vice-Presidente',
            '1o secretário' => '1º Secretário',
            '2o secretário' => '2º Secretário',
        ];

        foreach ($cargoMapping as $oldValue => $newValue) {
            DB::table('parlamentars')
                ->where('cargo', 'ILIKE', $oldValue)
                ->update(['cargo' => $newValue]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para valores em minúscula se necessário
        $cargoMapping = [
            'Vereador' => 'vereador',
            'Vereadora' => 'vereadora',
            'Presidente da Câmara' => 'presidente',
            'Vice-Presidente' => 'vice_presidente',
            '1º Secretário' => 'primeiro_secretario',
            '2º Secretário' => 'segundo_secretario',
        ];

        foreach ($cargoMapping as $oldValue => $newValue) {
            DB::table('parlamentars')
                ->where('cargo', $oldValue)
                ->update(['cargo' => $newValue]);
        }
    }
};
