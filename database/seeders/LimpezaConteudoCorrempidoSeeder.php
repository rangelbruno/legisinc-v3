<?php

namespace Database\Seeders;

use App\Models\Proposicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class LimpezaConteudoCorrempidoSeeder extends Seeder
{
    /**
     * Limpar conteúdo corrompido de proposições que foram afetadas ANTES da correção
     * 
     * PROBLEMA:
     * - Proposições antigas ainda têm conteúdo "ansi Objetivo geral..." de extrações RTF corrompidas
     * - A correção previne novos casos, mas não limpa casos existentes
     * 
     * SOLUÇÃO:
     * - Identificar proposições com conteúdo suspeito
     * - Substituir por conteúdo padrão baseado no tipo de proposição
     * - Manter log de limpezas realizadas
     */
    public function run(): void
    {
        // AUTO-REGISTRO: Garantir que este seeder sempre seja executado
        $this->garantirAutoRegistro();
        
        Log::info('🧹 Iniciando limpeza de conteúdo corrompido de proposições antigas');
        
        $padroesSuspeitos = [
            'ansi Objetivo',
            'ansi CONSIDERANDO',
            'ansi RESOLVE',
        ];
        
        $proposicoesCorrempidas = collect();
        
        // Buscar proposições com conteúdo suspeito
        foreach ($padroesSuspeitos as $padrao) {
            $encontradas = Proposicao::where('conteudo', 'LIKE', '%' . $padrao . '%')->get();
            $proposicoesCorrempidas = $proposicoesCorrempidas->merge($encontradas);
        }
        
        // Remover duplicatas
        $proposicoesCorrempidas = $proposicoesCorrempidas->unique('id');
        
        if ($proposicoesCorrempidas->isEmpty()) {
            Log::info('✅ Nenhuma proposição com conteúdo corrompido encontrada');
            echo "✅ Nenhum conteúdo corrompido para limpar\n";
            return;
        }
        
        echo "🧹 Limpando {$proposicoesCorrempidas->count()} proposições com conteúdo corrompido\n";
        
        $limpas = 0;
        foreach ($proposicoesCorrempidas as $proposicao) {
            $conteudoAntigo = $proposicao->conteudo;
            
            // Gerar conteúdo padrão baseado no tipo
            $conteudoNovo = $this->gerarConteudoPadrao($proposicao);
            
            $proposicao->update([
                'conteudo' => $conteudoNovo
            ]);
            
            Log::info('Proposição limpa', [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'conteudo_antigo_preview' => substr($conteudoAntigo, 0, 50),
                'conteudo_novo_preview' => substr($conteudoNovo, 0, 50)
            ]);
            
            $limpas++;
        }
        
        Log::info('Limpeza de conteúdo corrompido concluída', [
            'proposicoes_limpas' => $limpas
        ]);
        
        echo "✅ Limpeza concluída: {$limpas} proposições restauradas\n";
    }
    
    /**
     * Gerar conteúdo padrão apropriado para cada tipo de proposição
     */
    private function gerarConteudoPadrao($proposicao): string
    {
        $tipo = $proposicao->tipo ?? 'projeto_lei_ordinaria';
        $autorNome = $proposicao->autor?->name ?? 'Autor';
        
        $conteudos = [
            'mocao' => "MOÇÃO\n\nO Vereador que esta subscreve, no uso de suas atribuições regimentais, apresenta a presente MOÇÃO para manifestar apoio/reconhecimento/protesto conforme segue:\n\nCONSIDERANDO que:\n- [Justificativa 1];\n- [Justificativa 2];\n- [Justificativa 3].\n\nRESOLVE:\n\nArt. 1º Manifestar [apoio/reconhecimento/protesto] a [objeto da moção].\n\nArt. 2º Encaminhar cópia desta Moção aos interessados.\n\nArt. 3º Esta Moção entra em vigor na data de sua aprovação.\n\nCaraguatatuba, [data].\n\n{$autorNome}\nVereador",
            
            'indicacao' => "INDICAÇÃO\n\nO Vereador que esta subscreve, no uso de suas atribuições regimentais, apresenta a presente INDICAÇÃO para sugerir ao Poder Executivo:\n\nCONSIDERANDO que:\n- [Justificativa 1];\n- [Justificativa 2];\n- [Justificativa 3].\n\nINDICA:\n\nQue o Poder Executivo Municipal estude a viabilidade de [objeto da indicação].\n\nJUSTIFICATIVA:\n\n[Texto justificativo da indicação]\n\nCaraguatatuba, [data].\n\n{$autorNome}\nVereador",
            
            'requerimento' => "REQUERIMENTO\n\nO Vereador que esta subscreve, no uso de suas atribuições regimentais, REQUER:\n\n[Objeto do requerimento]\n\nJUSTIFICATIVA:\n\n[Texto justificativo do requerimento]\n\nCaraguatatuba, [data].\n\n{$autorNome}\nVereador",
            
            'projeto_lei_ordinaria' => "PROJETO DE LEI Nº ___/2025\n\n\"[Ementa do projeto de lei]\"\n\nO PREFEITO MUNICIPAL DE CARAGUATATUBA, no uso de suas atribuições legais, submete à apreciação da Câmara Municipal o seguinte projeto de lei:\n\nArt. 1º [Disposição principal da lei].\n\nArt. 2º Esta lei entra em vigor na data de sua publicação.\n\nCaraguatatuba, [data].\n\n{$autorNome}\nVereador",
            
            'projeto_decreto_legislativo' => "PROJETO DE DECRETO LEGISLATIVO Nº ___/2025\n\n\"[Ementa do decreto legislativo]\"\n\nA CÂMARA MUNICIPAL DE CARAGUATATUBA decreta:\n\nArt. 1º [Disposição principal do decreto].\n\nArt. 2º Este Decreto Legislativo entra em vigor na data de sua publicação.\n\nCaraguatatuba, [data].\n\n{$autorNome}\nVereador"
        ];
        
        return $conteudos[$tipo] ?? $conteudos['projeto_lei_ordinaria'];
    }
    
    /**
     * Auto-registro: Garantir que este seeder crítico sempre seja executado
     */
    private function garantirAutoRegistro(): void
    {
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        
        if (!\Illuminate\Support\Facades\File::exists($databaseSeederPath)) {
            return;
        }
        
        $conteudo = \Illuminate\Support\Facades\File::get($databaseSeederPath);
        $className = self::class;
        
        // Se já está registrado, não fazer nada
        if (strpos($conteudo, $className) !== false) {
            return;
        }
        
        // Procurar por um ponto seguro para inserir (antes do último seeder ou final)
        $pontos = [
            '        // ÚLTIMO:',
            '        // CORREÇÕES CRÍTICAS FINAIS:',
            '    }' // Final da função run
        ];
        
        foreach ($pontos as $ponto) {
            if (strpos($conteudo, $ponto) !== false) {
                $insercao = "        // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup\n";
                $insercao .= "        \$this->call([\n";
                $insercao .= "            {$className}::class,\n";
                $insercao .= "        ]);\n\n        {$ponto}";
                
                $conteudo = str_replace($ponto, $insercao, $conteudo);
                
                \Illuminate\Support\Facades\File::put($databaseSeederPath, $conteudo);
                \Illuminate\Support\Facades\Log::info('Auto-registro aplicado: LimpezaConteudoCorrempidoSeeder');
                break;
            }
        }
    }
}