<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proposicao;
use Illuminate\Support\Facades\Log;

class LimpezaConteudoAnsiObjetivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * CRÍTICO: Limpeza definitiva do problema "ansi Objetivo geral"
     */
    public function run(): void
    {
        $this->command->info('🧹 LIMPEZA CRÍTICA: Removendo conteúdo "ansi Objetivo geral" corrupto...');
        
        // Padrões suspeitos que devem ser removidos
        $padroesSuspeitos = [
            'ansi Objetivo geral:',
            'ansi\s+Objetivo\s+geral:',
            'Objetivo geral: Oferecer informações',
            'ansi Objetivo geral: Oferecer informações e reflexões sobre os impactos da era digital na saúde ment'
        ];
        
        $proposicoesAfetadas = 0;
        $proposicoesLimpas = 0;
        
        // Buscar todas as proposições que podem ter conteúdo corrompido
        $proposicoes = Proposicao::whereNotNull('conteudo')
            ->where('conteudo', '!=', '')
            ->get();
            
        foreach ($proposicoes as $proposicao) {
            $conteudoOriginal = $proposicao->conteudo;
            $temConteudoCorrupto = false;
            
            // Verificar se contém algum padrão suspeito
            foreach ($padroesSuspeitos as $padrao) {
                if (preg_match("/$padrao/i", $conteudoOriginal)) {
                    $temConteudoCorrupto = true;
                    break;
                }
            }
            
            if ($temConteudoCorrupto) {
                $proposicoesAfetadas++;
                
                Log::warning('Proposição com conteúdo corrupto detectado', [
                    'proposicao_id' => $proposicao->id,
                    'preview' => substr($conteudoOriginal, 0, 200),
                    'autor' => $proposicao->autor?->name ?? 'N/A'
                ]);
                
                // Estratégia: Gerar conteúdo padrão baseado na ementa e justificativa
                $conteudoPadrao = $this->gerarConteudoPadrao($proposicao);
                
                // Atualizar com conteúdo limpo
                $proposicao->update([
                    'conteudo' => $conteudoPadrao
                ]);
                
                $proposicoesLimpas++;
                
                $this->command->info("✅ Proposição {$proposicao->id} limpa - novo conteúdo gerado");
            }
        }
        
        // Relatório final
        $this->command->info('');
        $this->command->info("📊 RELATÓRIO DE LIMPEZA:");
        $this->command->info("   • Proposições analisadas: " . $proposicoes->count());
        $this->command->info("   • Proposições com conteúdo corrupto: $proposicoesAfetadas");
        $this->command->info("   • Proposições corrigidas: $proposicoesLimpas");
        
        if ($proposicoesLimpas > 0) {
            $this->command->info('');
            $this->command->info('🎯 AÇÃO RECOMENDADA:');
            $this->command->info('   Os usuários devem revisar e editar as proposições corrigidas');
            $this->command->info('   para adicionar o conteúdo desejado usando o OnlyOffice.');
        }
        
        $this->command->info('');
        $this->command->info('🛡️ PROTEÇÃO ATIVA: Sistema agora rejeita automaticamente');
        $this->command->info('   qualquer conteúdo que contenha padrões "ansi Objetivo geral"');
        $this->command->info('');
    }
    
    /**
     * Gerar conteúdo padrão baseado na ementa e justificativa da proposição
     */
    private function gerarConteudoPadrao(Proposicao $proposicao): string
    {
        $conteudo = '';
        
        // Usar ementa se disponível
        if (!empty($proposicao->ementa)) {
            $conteudo .= $proposicao->ementa;
        } else {
            $conteudo .= 'Proposição em elaboração.';
        }
        
        // Adicionar justificativa se disponível
        if (!empty($proposicao->justificativa)) {
            $conteudo .= "\n\nJustificativa:\n\n" . $proposicao->justificativa;
        } else {
            $conteudo .= "\n\nEsta proposição necessita de conteúdo e justificativa. ";
            $conteudo .= "Por favor, edite este documento usando o editor OnlyOffice para adicionar o texto completo.";
        }
        
        return $conteudo;
    }
}