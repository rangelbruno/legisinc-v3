<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class CorrecaoOnlyOfficeConteudoSeeder extends Seeder
{
    /**
     * Aplicar correção permanente para evitar substituição de conteúdo no OnlyOffice
     * 
     * PROBLEMA RESOLVIDO:
     * - Sistema substituía conteúdo original por texto extraído de RTF corrompido
     * - Texto "ansi Objetivo geral..." era considerado válido e substituía conteúdo real
     * 
     * SOLUÇÃO:
     * - Lógica conservadora: preserva conteúdo original sempre que possível
     * - Validação rigorosa: rejeita padrões suspeitos como "ansi Objetivo"
     * - Logs detalhados para debugging
     */
    public function run(): void
    {
        // AUTO-REGISTRO: Garantir que este seeder sempre seja executado
        $this->garantirAutoRegistro();
        
        Log::info('🔧 Aplicando correção OnlyOffice - Preservação de Conteúdo');
        
        $arquivoOnlyOffice = app_path('Services/OnlyOffice/OnlyOfficeService.php');
        
        if (!File::exists($arquivoOnlyOffice)) {
            Log::error('Arquivo OnlyOfficeService não encontrado', ['caminho' => $arquivoOnlyOffice]);
            return;
        }
        
        $conteudo = File::get($arquivoOnlyOffice);
        $correcoesAplicadas = 0;
        
        // 1. VERIFICAR E APLICAR LÓGICA CONSERVADORA
        if (strpos($conteudo, 'ESTRATÉGIA CONSERVADORA: PRIORIZAR PRESERVAÇÃO DO CONTEÚDO ORIGINAL') === false) {
            Log::info('Aplicando lógica conservadora no callback OnlyOffice');
            
            $antigoCallback = '// ESTRATÉGIA HÍBRIDA: Tentar atualizar conteúdo se foi extraído com sucesso';
            $novoCallback = '// ESTRATÉGIA CONSERVADORA: PRIORIZAR PRESERVAÇÃO DO CONTEÚDO ORIGINAL
                $conteudoOriginal = $proposicao->conteudo;
                $temConteudoOriginalValido = !empty($conteudoOriginal) && strlen(trim($conteudoOriginal)) > 10;
                
                // ESTRATÉGIA HÍBRIDA: Tentar atualizar conteúdo se foi extraído com sucesso';
            
            $conteudo = str_replace($antigoCallback, $novoCallback, $conteudo);
            $correcoesAplicadas++;
        }
        
        // 2. VERIFICAR E APLICAR NOVA LÓGICA DE PRESERVAÇÃO
        if (strpos($conteudo, 'CONSERVANDO conteúdo original existente') === false) {
            Log::info('Aplicando nova lógica de preservação de conteúdo');
            
            $antigaLogica = 'if (! empty($conteudoExtraido) && $this->isConteudoValido($conteudoExtraido)) {
                        $updateData[\'conteudo\'] = $conteudoExtraido;';
            
            $novaLogica = 'if ($temConteudoOriginalValido) {
                        // Se já tem conteúdo válido, NÃO substituir - apenas salvar arquivo
                        Log::info(\'CONSERVANDO conteúdo original existente - não extraindo do RTF\', [
                            \'proposicao_id\' => $proposicao->id,
                            \'estrategia\' => \'preservar_conteudo_original_existente\',
                            \'conteudo_original_length\' => strlen($conteudoOriginal),
                            \'conteudo_extraido_length\' => strlen($conteudoExtraido ?? \'\'),
                        ]);
                    } elseif (! empty($conteudoExtraido) && $this->isConteudoValidoRigoroso($conteudoExtraido)) {
                        // Só substituir se não há conteúdo original E conteúdo extraído é muito confiável
                        $updateData[\'conteudo\'] = $conteudoExtraido;';
            
            $conteudo = str_replace($antigaLogica, $novaLogica, $conteudo);
            $correcoesAplicadas++;
        }
        
        // 3. VERIFICAR E APLICAR MÉTODO DE VALIDAÇÃO RIGOROSA
        if (strpos($conteudo, 'isConteudoValidoRigoroso') === false) {
            Log::info('Adicionando método de validação rigorosa');
            
            // Procurar onde inserir o método (após método isConteudoValido)
            $posicaoInsercao = strpos($conteudo, 'private function isConteudoValido(string $conteudo): bool');
            
            if ($posicaoInsercao !== false) {
                // Encontrar final do método isConteudoValido
                $posicaoFim = strpos($conteudo, '    }', $posicaoInsercao);
                $posicaoFim = strpos($conteudo, "\n", $posicaoFim) + 1;
                
                $metodoRigoroso = '
    /**
     * Validar se conteúdo extraído é texto válido (não corrompido) - VERSÃO RIGOROSA
     */
    private function isConteudoValidoRigoroso(string $conteudo): bool
    {
        Log::info(\'Validando conteúdo extraído RIGOROSAMENTE\', [
            \'tamanho\' => strlen($conteudo),
            \'preview\' => substr($conteudo, 0, 150)
        ]);
        
        // REJEITAR IMEDIATAMENTE se começa com padrões suspeitos
        $padroesSuspeitos = [
            \'/^ansi\s/\',                    // Começa com "ansi " - muito suspeito
            \'/^[a-z]{4,8}\s+(Objetivo|CONSIDERANDO|RESOLVE)/\',  // Padrão específico do problema
            \'/^\w{3,6}\s+\w+\s+geral:/\',   // Padrão "xxx xxx geral:"
            \'/^[*\s;:]{10,}/\',             // Começa com lixo RTF
        ];
        
        foreach ($padroesSuspeitos as $padrao) {
            if (preg_match($padrao, $conteudo)) {
                Log::warning(\'Conteúdo REJEITADO por padrão suspeito\', [
                    \'padrao\' => $padrao,
                    \'preview\' => substr($conteudo, 0, 100)
                ]);
                return false;
            }
        }
        
        // Conteúdo muito pequeno não é válido
        if (strlen($conteudo) < 30) {
            Log::info(\'Conteúdo rejeitado: muito pequeno para ser confiável\', [\'tamanho\' => strlen($conteudo)]);
            return false;
        }
        
        // Se após limpeza ainda contém muito padrão RTF corrompido, rejeitar
        if (preg_match(\'/[*\s]{5,}[;:]/\', $conteudo)) {
            Log::info(\'Conteúdo rejeitado: ainda contém muito padrão RTF corrompido\');
            return false;
        }
        
        // Deve ter MUITAS palavras reais (pelo menos 5 palavras de 3+ caracteres)
        $palavras = preg_match_all(\'/\b[a-zA-ZÀ-ÿ]{3,}\b/\', $conteudo);
        if ($palavras < 5) {
            Log::info(\'Conteúdo rejeitado: poucas palavras válidas para ser confiável\', [\'palavras_encontradas\' => $palavras]);
            return false;
        }
        
        // Pelo menos 50% do conteúdo deve ser caracteres alfanuméricos ou espaços (mais rigoroso)
        $totalChars = mb_strlen($conteudo, \'UTF-8\');
        if ($totalChars == 0) {
            Log::info(\'Conteúdo rejeitado: vazio após limpeza\');
            return false;
        }
        
        $validChars = preg_match_all(\'/[a-zA-ZÀ-ÿ0-9\s]/\', $conteudo);
        $porcentagemValida = $validChars / $totalChars;
        
        if ($porcentagemValida < 0.5) { // MAIS RIGOROSO: 50% em vez de 30%
            Log::info(\'Conteúdo rejeitado: muitos caracteres especiais para ser confiável\', [
                \'porcentagem_valida\' => round($porcentagemValida * 100, 2) . \'%\'
            ]);
            return false;
        }
        
        Log::info(\'Conteúdo APROVADO na validação rigorosa\', [
            \'palavras\' => $palavras,
            \'porcentagem_valida\' => round($porcentagemValida * 100, 2) . \'%\'
        ]);
        
        return true;
    }
';
                
                $conteudo = substr_replace($conteudo, $metodoRigoroso, $posicaoFim, 0);
                $correcoesAplicadas++;
            }
        }
        
        // Salvar arquivo se correções foram aplicadas
        if ($correcoesAplicadas > 0) {
            File::put($arquivoOnlyOffice, $conteudo);
            Log::info('Correções OnlyOffice aplicadas', [
                'correções' => $correcoesAplicadas,
                'arquivo' => $arquivoOnlyOffice
            ]);
            
            echo "✅ Correção OnlyOffice aplicada - {$correcoesAplicadas} melhorias\n";
        } else {
            Log::info('Correções OnlyOffice já estão aplicadas');
            echo "✅ Correções OnlyOffice já aplicadas\n";
        }
    }
    
    /**
     * Auto-registro: Garantir que este seeder crítico sempre seja executado
     */
    private function garantirAutoRegistro(): void
    {
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        
        if (!File::exists($databaseSeederPath)) {
            return;
        }
        
        $conteudo = File::get($databaseSeederPath);
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
                $insercao = "        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection\n";
                $insercao .= "        \$this->call([\n";
                $insercao .= "            {$className}::class,\n";
                $insercao .= "        ]);\n\n        {$ponto}";
                
                $conteudo = str_replace($ponto, $insercao, $conteudo);
                
                File::put($databaseSeederPath, $conteudo);
                Log::info('Auto-registro aplicado: CorrecaoOnlyOfficeConteudoSeeder');
                break;
            }
        }
    }
}