#!/bin/bash

echo "Convertendo template RTF para DOCX para compatibilidade com OnlyOffice..."

# Verificar se o arquivo RTF existe
RTF_FILE="/home/bruno/legisinc/storage/app/templates/template_1.rtf"
DOCX_FILE="/home/bruno/legisinc/storage/app/templates/template_1.docx"

if [ ! -f "$RTF_FILE" ]; then
    echo "Erro: Arquivo RTF não encontrado: $RTF_FILE"
    exit 1
fi

echo "Arquivo RTF encontrado. Criando versão DOCX..."

# Criar conteúdo DOCX usando Python
python3 << 'EOF'
import os
import sys

# Verificar se python-docx está disponível
try:
    from docx import Document
    from docx.shared import Inches
    from docx.enum.text import WD_ALIGN_PARAGRAPH
except ImportError:
    print("Instalando python-docx...")
    os.system("pip3 install python-docx")
    from docx import Document
    from docx.shared import Inches
    from docx.enum.text import WD_ALIGN_PARAGRAPH

# Ler conteúdo RTF
with open('/home/bruno/legisinc/storage/app/templates/template_1.rtf', 'r', encoding='utf-8') as f:
    rtf_content = f.read()

# Criar documento DOCX
doc = Document()

# Configurar margens
sections = doc.sections
for section in sections:
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)

# Adicionar conteúdo estruturado do template
# Título centralizado
title = doc.add_paragraph()
title.alignment = WD_ALIGN_PARAGRAPH.CENTER
title_run = title.add_run('MOÇÃO Nº ${numero_proposicao}')
title_run.bold = True
title_run.font.size = doc.styles['Normal'].font.size

doc.add_paragraph()

# Informações centralizadas
info_para = doc.add_paragraph()
info_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
info_para.add_run('Data: ${data_atual}')

info_para2 = doc.add_paragraph()
info_para2.alignment = WD_ALIGN_PARAGRAPH.CENTER
info_para2.add_run('Autor: ${autor_nome}')

info_para3 = doc.add_paragraph()
info_para3.alignment = WD_ALIGN_PARAGRAPH.CENTER
info_para3.add_run('Município: ${municipio}')

doc.add_paragraph()
doc.add_paragraph()

# Ementa
ementa_title = doc.add_paragraph()
ementa_run = ementa_title.add_run('EMENTA')
ementa_run.bold = True

doc.add_paragraph()
doc.add_paragraph('${ementa}')

doc.add_paragraph()
doc.add_paragraph()

# Texto
texto_title = doc.add_paragraph()
texto_run = texto_title.add_run('TEXTO')
texto_run.bold = True

doc.add_paragraph()
doc.add_paragraph('${texto}')

doc.add_paragraph()
doc.add_paragraph()

# Rodapé alinhado à direita
footer_para = doc.add_paragraph()
footer_para.alignment = WD_ALIGN_PARAGRAPH.RIGHT
footer_para.add_run('Câmara Municipal de ${municipio}')

footer_para2 = doc.add_paragraph()
footer_para2.alignment = WD_ALIGN_PARAGRAPH.RIGHT
footer_para2.add_run('${data_atual}')

# Salvar documento
doc.save('/home/bruno/legisinc/storage/app/templates/template_1.docx')
print("Arquivo DOCX criado com sucesso!")
EOF

if [ $? -eq 0 ]; then
    echo "✓ Conversão para DOCX realizada com sucesso!"
    echo "✓ Arquivo criado: $DOCX_FILE"
    
    # Atualizar banco de dados para usar o arquivo DOCX
    echo "Atualizando banco de dados..."
    cd /home/bruno/legisinc
    
    # Usar artisan para atualizar o caminho do arquivo
    php artisan tinker --execute="
    \$template = App\Models\TipoProposicaoTemplate::find(4);
    if (\$template) {
        \$template->update(['arquivo_path' => 'templates/template_1.docx']);
        echo 'Template atualizado para usar arquivo DOCX\n';
    } else {
        echo 'Template não encontrado\n';
    }
    "
    
    echo "✓ Template configurado para usar DOCX!"
    echo ""
    echo "Agora o OnlyOffice deve abrir o arquivo corretamente."
    echo "Teste novamente a edição do template."
    
else
    echo "✗ Erro na conversão para DOCX"
    exit 1
fi