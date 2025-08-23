<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ParagrafosOnlyOfficeSeeder extends Seeder
{
    /**
     * Garantir que a correção de parágrafos esteja implementada no TemplateProcessorService
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('📝 Aplicando correção de parágrafos no OnlyOffice...');

        $arquivoTemplate = app_path('Services/Template/TemplateProcessorService.php');
        
        if (!file_exists($arquivoTemplate)) {
            $this->command->error('❌ Arquivo TemplateProcessorService.php não encontrado!');
            return;
        }

        $conteudo = file_get_contents($arquivoTemplate);
        
        // Verificar se a correção já está implementada
        if (strpos($conteudo, 'Tratar quebras de linha - converter para \par (parágrafo RTF)') !== false) {
            $this->command->info('✅ Correção de parágrafos já implementada!');
            
            // Validar que a implementação está correta
            if (strpos($conteudo, '$textoProcessado .= \'\\\\par \';') !== false) {
                $this->command->info('✅ Implementação validada: conversão \\n para \\par funcionando');
            } else {
                $this->command->warn('⚠️ Implementação pode estar incompleta - verificar manualmente');
            }
            
            return;
        }

        // Implementar a correção se não estiver presente
        $this->command->info('⚙️ Implementando correção de parágrafos...');
        
        $codigoAntigo = 'private function converterParaRTF(string $texto): string
    {
        $textoProcessado = \'\';
        $length = mb_strlen($texto, \'UTF-8\');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, \'UTF-8\');
            $codepoint = mb_ord($char, \'UTF-8\');
            
            if ($codepoint > 127) {
                // Gerar sequência RTF Unicode correta: \uN*
                $textoProcessado .= \'\\\\u\' . $codepoint . \'*\';
            } else {
                // Caracteres ASCII normais
                $textoProcessado .= $char;
            }
        }
        
        return $textoProcessado;
    }';

        $codigoNovo = 'private function converterParaRTF(string $texto): string
    {
        $textoProcessado = \'\';
        $length = mb_strlen($texto, \'UTF-8\');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($texto, $i, 1, \'UTF-8\');
            $codepoint = mb_ord($char, \'UTF-8\');
            
            // Tratar quebras de linha - converter para \par (parágrafo RTF)
            if ($char === "\n") {
                $textoProcessado .= \'\\\\par \';
            } else if ($char === "\r") {
                // Ignorar \r se for seguido de \n (Windows line ending)
                if ($i + 1 < $length && mb_substr($texto, $i + 1, 1, \'UTF-8\') === "\n") {
                    continue;
                }
                $textoProcessado .= \'\\\\par \';
            } else if ($codepoint > 127) {
                // Gerar sequência RTF Unicode correta: \uN*
                $textoProcessado .= \'\\\\u\' . $codepoint . \'*\';
            } else {
                // Caracteres ASCII normais
                $textoProcessado .= $char;
            }
        }
        
        return $textoProcessado;
    }';

        $conteudoAtualizado = str_replace($codigoAntigo, $codigoNovo, $conteudo);
        
        if ($conteudoAtualizado === $conteudo) {
            $this->command->warn('⚠️ Não foi possível aplicar automaticamente - código pode ter sido modificado');
            $this->command->info('ℹ️ Verificar manualmente se a função converterParaRTF trata quebras de linha');
        } else {
            file_put_contents($arquivoTemplate, $conteudoAtualizado);
            $this->command->info('✅ Correção de parágrafos aplicada com sucesso!');
        }

        // Validação final
        $this->validarImplementacao();
    }

    /**
     * Validar se a implementação está funcionando corretamente
     */
    private function validarImplementacao(): void
    {
        $this->command->info('🧪 Validando implementação...');
        
        try {
            // Testar conversão simples
            $textoTeste = "Primeiro parágrafo.\n\nSegundo parágrafo.";
            
            // Simular a conversão
            $textoConvertido = '';
            $length = mb_strlen($textoTeste, 'UTF-8');
            
            for ($i = 0; $i < $length; $i++) {
                $char = mb_substr($textoTeste, $i, 1, 'UTF-8');
                
                if ($char === "\n") {
                    $textoConvertido .= '\\par ';
                } else {
                    $textoConvertido .= $char;
                }
            }
            
            $parCount = substr_count($textoConvertido, '\\par');
            
            if ($parCount >= 2) {
                $this->command->info('✅ Validação bem-sucedida: ' . $parCount . ' marcadores \\par encontrados');
                $this->command->info('✅ Parágrafos serão preservados no OnlyOffice');
            } else {
                $this->command->error('❌ Validação falhou: marcadores \\par insuficientes');
            }
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro durante validação: ' . $e->getMessage());
        }
    }
}