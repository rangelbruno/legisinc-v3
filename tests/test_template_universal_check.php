<?php

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICAÇÃO DO TEMPLATE UNIVERSAL ===\n\n";

// Verificar se existe template universal configurado
$tipoProposicao = \App\Models\TipoProposicao::where('codigo', 'mocao')->first();

if ($tipoProposicao) {
    echo "✅ Tipo Moção encontrado: ID {$tipoProposicao->id}\n";
    
    $temTemplateUniversal = \App\Models\TemplateUniversal::where('tipo_proposicao_id', $tipoProposicao->id)->exists();
    echo "Template Universal configurado: " . ($temTemplateUniversal ? '✅ SIM' : '❌ NÃO') . "\n";
    
    if ($temTemplateUniversal) {
        $template = \App\Models\TemplateUniversal::where('tipo_proposicao_id', $tipoProposicao->id)->first();
        echo "Template ativo: " . ($template->ativo ? '✅ SIM' : '❌ NÃO') . "\n";
        echo "Tem conteúdo RTF: " . (!empty($template->conteudo_rtf) ? '✅ SIM' : '❌ NÃO') . "\n";
        
        if (!empty($template->conteudo_rtf)) {
            echo "\n=== PREVIEW DO CONTEÚDO RTF ===\n";
            echo substr($template->conteudo_rtf, 0, 500) . "...\n";
            
            // Verificar se tem caracteres unicode
            if (preg_match_all('/\\\\u[0-9]+\\*/', $template->conteudo_rtf, $matches)) {
                echo "\n✅ Caracteres Unicode encontrados: " . count($matches[0]) . "\n";
                echo "Exemplos: " . implode(', ', array_slice($matches[0], 0, 10)) . "\n";
            }
        }
    }
} else {
    echo "❌ Tipo Moção não encontrado\n";
}

echo "\n=== VERIFICAÇÃO DE PROPOSIÇÕES ===\n";

// Verificar se existem proposições tipo moção
$proposicoes = \App\Models\Proposicao::where('tipo', 'mocao')
    ->orWhere('tipo', 'Moção')
    ->latest()
    ->limit(3)
    ->get();

if ($proposicoes->count() > 0) {
    echo "Encontradas {$proposicoes->count()} proposições tipo Moção:\n\n";
    
    foreach ($proposicoes as $prop) {
        echo "ID: {$prop->id}\n";
        echo "Ementa: " . substr($prop->ementa, 0, 100) . "\n";
        echo "Arquivo: " . ($prop->arquivo_path ? '✅ ' . $prop->arquivo_path : '❌ Sem arquivo') . "\n";
        echo "Conteúdo: " . (strlen($prop->conteudo) > 50 ? '✅ ' . strlen($prop->conteudo) . ' caracteres' : '❌ Vazio ou muito curto') . "\n";
        echo "---\n";
    }
} else {
    echo "❌ Nenhuma proposição tipo Moção encontrada\n";
}