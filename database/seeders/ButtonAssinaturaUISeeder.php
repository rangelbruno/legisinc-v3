<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ButtonAssinaturaUISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎨 Aplicando melhorias de UI do botão Assinar Documento...');

        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!file_exists($viewPath)) {
            $this->command->error("❌ View não encontrada: {$viewPath}");
            return;
        }

        $content = file_get_contents($viewPath);
        $originalContent = $content;
        $changes = 0;

        // 1. Verificar se o botão já tem a classe melhorada
        if (!str_contains($content, 'btn-assinatura-melhorado')) {
            // Adicionar classe CSS melhorada ao botão de assinatura
            $content = str_replace(
                'class="btn btn-light-success btn-lg w-100 d-flex align-items-center justify-content-center"',
                'class="btn btn-light-success btn-lg w-100 d-flex align-items-center justify-content-center btn-assinatura-melhorado"',
                $content
            );
            
            if ($content !== $originalContent) {
                $changes++;
                $this->command->info('  ✅ Classe btn-assinatura-melhorado adicionada');
            }
        } else {
            $this->command->info('  ✅ Classe btn-assinatura-melhorado já existe');
        }

        // 2. Remover target="_blank" do botão de assinatura (mas manter no PDF)
        $pattern = '/(<a[^>]*href="[^"]*\/assinar"[^>]*)\s*target="_blank"([^>]*>)/';
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, '$1$2', $content);
            $changes++;
            $this->command->info('  ✅ target="_blank" removido do botão de assinatura');
        } else {
            $this->command->info('  ✅ target="_blank" já removido do botão de assinatura');
        }

        // 3. Garantir que o CSS melhorado existe
        if (!str_contains($content, '.btn-assinatura-melhorado')) {
            // Encontrar onde termina o CSS existente e adicionar o novo CSS
            $cssToAdd = '
/* Estilo melhorado para botão Assinar Documento */
.btn-assinatura-melhorado {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.btn-assinatura-melhorado:hover {
    background: linear-gradient(135deg, #157347 0%, #0f5132 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(21, 115, 71, 0.4);
}

.btn-assinatura-melhorado:hover .fw-bold {
    color: #ffffff !important;
}

.btn-assinatura-melhorado:hover .text-muted {
    color: #e8f5e8 !important;
}

.btn-assinatura-melhorado:hover .ki-duotone {
    color: #ffffff !important;
}

.btn-assinatura-melhorado:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(21, 115, 71, 0.3);
}';

            // Adicionar CSS antes do fechamento da tag </style>
            $content = str_replace('</style>', $cssToAdd . "\n</style>", $content);
            $changes++;
            $this->command->info('  ✅ CSS do botão melhorado adicionado');
        } else {
            $this->command->info('  ✅ CSS do botão melhorado já existe');
        }

        // 4. Salvar apenas se houve mudanças
        if ($changes > 0) {
            file_put_contents($viewPath, $content);
            $this->command->info("✅ {$changes} melhorias aplicadas ao botão Assinar Documento");
        } else {
            $this->command->info('✅ Todas as melhorias já estavam aplicadas');
        }

        // 5. Validar as mudanças
        $this->validateChanges($viewPath);
    }

    /**
     * Validar se as melhorias foram aplicadas corretamente
     */
    private function validateChanges(string $viewPath): void
    {
        $content = file_get_contents($viewPath);
        
        $validations = [
            'btn-assinatura-melhorado' => 'Classe CSS melhorada',
            '.btn-assinatura-melhorado:hover' => 'Estilos de hover',
            'color: #ffffff !important' => 'Contraste do texto',
            'transform: translateY(-2px)' => 'Efeito de elevação',
        ];

        $this->command->info('🔍 Validando melhorias aplicadas...');
        
        foreach ($validations as $search => $description) {
            if (str_contains($content, $search)) {
                $this->command->info("  ✅ {$description}");
            } else {
                $this->command->error("  ❌ {$description} não encontrado");
            }
        }

        // Verificar que target="_blank" foi removido apenas do botão de assinatura
        $assinaturaTargetBlank = preg_match('/href="[^"]*\/assinar"[^>]*target="_blank"/', $content);
        $pdfTargetBlank = preg_match('/href="[^"]*\/pdf"[^>]*target="_blank"/', $content);
        
        if (!$assinaturaTargetBlank) {
            $this->command->info('  ✅ target="_blank" removido do botão assinatura');
        } else {
            $this->command->error('  ❌ target="_blank" ainda presente no botão assinatura');
        }
        
        if ($pdfTargetBlank) {
            $this->command->info('  ✅ target="_blank" mantido no botão PDF (correto)');
        }
    }
}