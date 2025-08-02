<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

echo "Criando template DOCX para substituir RTF...\n";

try {
    // Criar novo documento
    $phpWord = new PhpWord();
    
    // Configurar documento para português
    $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::PT_BR));
    
    // Adicionar seção
    $section = $phpWord->addSection([
        'marginTop' => 1440,    // 1 inch = 1440 twips
        'marginBottom' => 1440,
        'marginLeft' => 1440,
        'marginRight' => 1440,
    ]);
    
    // Título centralizado
    $section->addText(
        'MOÇÃO Nº ${numero_proposicao}',
        ['bold' => true, 'size' => 16],
        ['alignment' => Jc::CENTER]
    );
    
    $section->addTextBreak(1);
    
    // Informações centralizadas
    $section->addText('Data: ${data_atual}', [], ['alignment' => Jc::CENTER]);
    $section->addText('Autor: ${autor_nome}', [], ['alignment' => Jc::CENTER]);
    $section->addText('Município: ${municipio}', [], ['alignment' => Jc::CENTER]);
    
    $section->addTextBreak(2);
    
    // Ementa
    $section->addText('EMENTA', ['bold' => true, 'size' => 14]);
    $section->addTextBreak(1);
    $section->addText('${ementa}');
    
    $section->addTextBreak(2);
    
    // Texto
    $section->addText('TEXTO', ['bold' => true, 'size' => 14]);
    $section->addTextBreak(1);
    $section->addText('${texto}');
    
    $section->addTextBreak(2);
    
    // Rodapé alinhado à direita
    $section->addText('Câmara Municipal de ${municipio}', [], ['alignment' => Jc::END]);
    $section->addText('${data_atual}', [], ['alignment' => Jc::END]);
    
    // Salvar arquivo
    $templatePath = __DIR__ . '/../storage/app/templates/template_1.docx';
    
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($templatePath);
    
    echo "✓ Arquivo DOCX criado: $templatePath\n";
    
    // Atualizar banco de dados
    echo "Atualizando banco de dados...\n";
    
    // Configurar Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Buscar e atualizar template
    $template = App\Models\TipoProposicaoTemplate::find(4);
    
    if ($template) {
        $template->update(['arquivo_path' => 'templates/template_1.docx']);
        echo "✓ Template atualizado no banco de dados\n";
        echo "✓ Novo caminho: templates/template_1.docx\n";
    } else {
        echo "⚠ Template ID 4 não encontrado no banco\n";
    }
    
    echo "\n✓ Conversão concluída com sucesso!\n";
    echo "Agora teste a edição do template no OnlyOffice.\n";
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
    exit(1);
}