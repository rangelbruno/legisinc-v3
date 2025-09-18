<?php

namespace Database\Seeders;

use App\Models\Proposicao;
use Illuminate\Database\Seeder;

class PopularOnlyOfficeKeySeeder extends Seeder
{
    /**
     * Populate onlyoffice_key for existing proposições
     */
    public function run(): void
    {
        $this->command->info('🔑 Populando chaves OnlyOffice para proposições existentes...');

        $proposicoes = Proposicao::whereNull('onlyoffice_key')->get();

        foreach ($proposicoes as $proposicao) {
            // Gerar chave estável baseada em dados imutáveis
            $stableData = $proposicao->id . '|' .
                         ($proposicao->created_at ? $proposicao->created_at->timestamp : '0') . '|' .
                         ($proposicao->autor_id ?? '0');

            $stableHash = md5($stableData);
            $hashSuffix = substr($stableHash, 0, 8);

            $documentKey = "proposicao_{$proposicao->id}_{$hashSuffix}";

            $proposicao->update(['onlyoffice_key' => $documentKey]);

            $this->command->info("  ✅ Proposição {$proposicao->id}: {$documentKey}");
        }

        $this->command->info("🎯 {$proposicoes->count()} proposições atualizadas com chaves OnlyOffice");
    }
}
