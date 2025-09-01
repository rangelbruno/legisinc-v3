<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateUniversal;
use Illuminate\Support\Facades\Log;

class TemplateUniversalSimplificadoSeeder extends Seeder
{
    /**
     * Configura o Template Universal Simplificado com apenas as variáveis essenciais
     */
    public function run(): void
    {
        $this->command->info('🔧 Configurando Template Universal Simplificado...');
        
        // Conteúdo RTF simplificado apenas com os elementos essenciais
        $conteudoRTF = <<<'RTF'
{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil\fcharset0 Times New Roman;}}
{\colortbl ;\red0\green0\blue0;}
{\*\generator Legisinc}\viewkind4\uc1
\pard\sa200\sl276\slmult1\qc\f0\fs24\lang22

${imagem_cabecalho}

\par
\par
\b\fs28 ${tipo_proposicao} N\u176* ${numero_proposicao}\b0\fs24
\par
\par
\b EMENTA:\b0  ${ementa}
\par
\par
\b CONTE\u218*DO PRINCIPAL:\b0
\par
${texto}
\par
\par
\par
${rodape_texto}
\par
}
RTF;

        try {
            // Buscar ou criar o template
            $template = TemplateUniversal::where('nome', 'Template Universal Padrão')
                ->orWhere('id', 1)
                ->first();
            
            if (!$template) {
                $template = new TemplateUniversal();
                $template->nome = 'Template Universal Padrão';
                $this->command->info('  → Criando novo Template Universal Padrão');
            } else {
                $this->command->info('  → Atualizando Template Universal Padrão existente');
            }
            
            // Configurar o template
            $template->descricao = 'Template universal simplificado para todas as proposições';
            $template->conteudo = $conteudoRTF;
            $template->formato = 'rtf';
            $template->ativo = true;
            $template->is_default = true;
            
            // Definir variáveis essenciais
            $variaveis = [
                'imagem_cabecalho',
                'tipo_proposicao', 
                'numero_proposicao',
                'ementa',
                'texto',
                'rodape_texto'
            ];
            
            $template->variaveis = json_encode($variaveis);
            
            // Gerar document_key único se não existir
            if (empty($template->document_key)) {
                $template->document_key = md5(uniqid('template_universal_', true));
            }
            
            $template->save();
            
            $this->command->info('✅ Template Universal Simplificado configurado com sucesso!');
            $this->command->info('   ID: ' . $template->id);
            $this->command->info('   Variáveis disponíveis:');
            foreach ($variaveis as $var) {
                $this->command->info('   - ${' . $var . '}');
            }
            
            // Log para auditoria
            Log::info('Template Universal Simplificado configurado', [
                'template_id' => $template->id,
                'variaveis' => $variaveis,
                'tamanho_rtf' => strlen($conteudoRTF) . ' bytes'
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao configurar Template Universal: ' . $e->getMessage());
            Log::error('Erro no TemplateUniversalSimplificadoSeeder', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}