{{-- Painel de Variáveis do Editor OnlyOffice --}}

<!-- Busca de variáveis -->
<input type="text" id="variableSearch" class="variable-search form-control mb-3" 
       placeholder="🔍 Buscar variáveis..." 
       autocomplete="off">

<!-- Categorias de variáveis -->
<div id="variablesList">
    
    {{-- DADOS DA PROPOSIÇÃO --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-document fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            DADOS DA PROPOSIÇÃO
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${numero_proposicao}')" class="btn variable-btn">
                <span class="var-name">${numero_proposicao}</span>
                <span class="var-desc">Número da proposição</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${tipo_proposicao}')" class="btn variable-btn">
                <span class="var-name">${tipo_proposicao}</span>
                <span class="var-desc">Tipo da proposição</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${ano}')" class="btn variable-btn">
                <span class="var-name">${ano}</span>
                <span class="var-desc">Ano da proposição</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${protocolo}')" class="btn variable-btn">
                <span class="var-name">${protocolo}</span>
                <span class="var-desc">Número do protocolo</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${ementa}')" class="btn variable-btn">
                <span class="var-name">${ementa}</span>
                <span class="var-desc">Ementa da proposição</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${texto}')" class="btn variable-btn">
                <span class="var-name">${texto}</span>
                <span class="var-desc">Texto principal</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${justificativa}')" class="btn variable-btn">
                <span class="var-name">${justificativa}</span>
                <span class="var-desc">Justificativa</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${status_proposicao}')" class="btn variable-btn">
                <span class="var-name">${status_proposicao}</span>
                <span class="var-desc">Status atual</span>
            </button>
        </div>
    </div>
    
    {{-- AUTOR & PARLAMENTAR --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-user fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            AUTOR & PARLAMENTAR
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${autor_nome}')" class="btn variable-btn">
                <span class="var-name">${autor_nome}</span>
                <span class="var-desc">Nome do autor</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${autor_cargo}')" class="btn variable-btn">
                <span class="var-name">${autor_cargo}</span>
                <span class="var-desc">Cargo do autor</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${autor_partido}')" class="btn variable-btn">
                <span class="var-name">${autor_partido}</span>
                <span class="var-desc">Partido do autor</span>
            </button>
        </div>
    </div>
    
    {{-- DATAS & HORÁRIOS --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-calendar fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            DATAS & HORÁRIOS
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${data_atual}')" class="btn variable-btn">
                <span class="var-name">${data_atual}</span>
                <span class="var-desc">Data atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${data_criacao}')" class="btn variable-btn">
                <span class="var-name">${data_criacao}</span>
                <span class="var-desc">Data de criação</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${data_protocolo}')" class="btn variable-btn">
                <span class="var-name">${data_protocolo}</span>
                <span class="var-desc">Data do protocolo</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${dia}')" class="btn variable-btn">
                <span class="var-name">${dia}</span>
                <span class="var-desc">Dia atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${mes}')" class="btn variable-btn">
                <span class="var-name">${mes}</span>
                <span class="var-desc">Mês atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${ano_atual}')" class="btn variable-btn">
                <span class="var-name">${ano_atual}</span>
                <span class="var-desc">Ano atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${mes_extenso}')" class="btn variable-btn">
                <span class="var-name">${mes_extenso}</span>
                <span class="var-desc">Mês por extenso</span>
            </button>
        </div>
    </div>
    
    {{-- DADOS DA CÂMARA --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-bank fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            DADOS DA CÂMARA
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${nome_camara}')" class="btn variable-btn">
                <span class="var-name">${nome_camara}</span>
                <span class="var-desc">Nome oficial da Câmara</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${nome_camara_abreviado}')" class="btn variable-btn">
                <span class="var-name">${nome_camara_abreviado}</span>
                <span class="var-desc">Sigla da Câmara</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${municipio}')" class="btn variable-btn">
                <span class="var-name">${municipio}</span>
                <span class="var-desc">Nome do município</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${municipio_uf}')" class="btn variable-btn">
                <span class="var-name">${municipio_uf}</span>
                <span class="var-desc">Estado (UF)</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${endereco_camara}')" class="btn variable-btn">
                <span class="var-name">${endereco_camara}</span>
                <span class="var-desc">Endereço da Câmara</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${endereco_completo}')" class="btn variable-btn">
                <span class="var-name">${endereco_completo}</span>
                <span class="var-desc">Endereço completo com número, complemento, bairro e CEP</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${endereco_bairro}')" class="btn variable-btn">
                <span class="var-name">${endereco_bairro}</span>
                <span class="var-desc">Bairro</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${endereco_cep}')" class="btn variable-btn">
                <span class="var-name">${endereco_cep}</span>
                <span class="var-desc">CEP</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${telefone_camara}')" class="btn variable-btn">
                <span class="var-name">${telefone_camara}</span>
                <span class="var-desc">Telefone principal</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${telefone_protocolo}')" class="btn variable-btn">
                <span class="var-name">${telefone_protocolo}</span>
                <span class="var-desc">Telefone secundário</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${email_camara}')" class="btn variable-btn">
                <span class="var-name">${email_camara}</span>
                <span class="var-desc">E-mail institucional</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${website_camara}')" class="btn variable-btn">
                <span class="var-name">${website_camara}</span>
                <span class="var-desc">Website oficial</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${cnpj_camara}')" class="btn variable-btn">
                <span class="var-name">${cnpj_camara}</span>
                <span class="var-desc">CNPJ da Câmara</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${presidente_nome}')" class="btn variable-btn">
                <span class="var-name">${presidente_nome}</span>
                <span class="var-desc">Nome do Presidente</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${presidente_partido}')" class="btn variable-btn">
                <span class="var-name">${presidente_partido}</span>
                <span class="var-desc">Partido do Presidente</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${legislatura_atual}')" class="btn variable-btn">
                <span class="var-name">${legislatura_atual}</span>
                <span class="var-desc">Legislatura atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${numero_vereadores}')" class="btn variable-btn">
                <span class="var-name">${numero_vereadores}</span>
                <span class="var-desc">Número de vereadores</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${presidente_tratamento}')" class="btn variable-btn">
                <span class="var-name">${presidente_tratamento}</span>
                <span class="var-desc">Tratamento do Presidente</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${horario_funcionamento}')" class="btn variable-btn">
                <span class="var-name">${horario_funcionamento}</span>
                <span class="var-desc">Horário de funcionamento</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${horario_atendimento}')" class="btn variable-btn">
                <span class="var-name">${horario_atendimento}</span>
                <span class="var-desc">Horário de atendimento</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${horario_protocolo}')" class="btn variable-btn">
                <span class="var-name">${horario_protocolo}</span>
                <span class="var-desc">Horário de atendimento ao público</span>
            </button>
        </div>
    </div>
    
    {{-- CABEÇALHO & RODAPÉ --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-element-11 fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
            CABEÇALHO & RODAPÉ
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${cabecalho_nome_camara}')" class="btn variable-btn">
                <span class="var-name">${cabecalho_nome_camara}</span>
                <span class="var-desc">Nome no cabeçalho</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${cabecalho_endereco}')" class="btn variable-btn">
                <span class="var-name">${cabecalho_endereco}</span>
                <span class="var-desc">Endereço no cabeçalho</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${cabecalho_telefone}')" class="btn variable-btn">
                <span class="var-name">${cabecalho_telefone}</span>
                <span class="var-desc">Telefone no cabeçalho</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${cabecalho_website}')" class="btn variable-btn">
                <span class="var-name">${cabecalho_website}</span>
                <span class="var-desc">Website no cabeçalho</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${imagem_cabecalho}')" class="btn variable-btn bg-light-primary">
                <span class="var-name">${imagem_cabecalho}</span>
                <span class="var-desc">Imagem do cabeçalho</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${rodape_texto}')" class="btn variable-btn">
                <span class="var-name">${rodape_texto}</span>
                <span class="var-desc">Texto do rodapé</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${rodape_numeracao}')" class="btn variable-btn">
                <span class="var-name">${rodape_numeracao}</span>
                <span class="var-desc">Numeração de página</span>
            </button>
        </div>
    </div>
    
    {{-- VARIÁVEIS DINÂMICAS --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-abstract-26 fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            VARIÁVEIS DINÂMICAS
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${prefixo_numeracao}')" class="btn variable-btn">
                <span class="var-name">${prefixo_numeracao}</span>
                <span class="var-desc">Prefixo para numeração</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${formato_data}')" class="btn variable-btn">
                <span class="var-name">${formato_data}</span>
                <span class="var-desc">Formato de exibição de datas</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${var_assinatura_padrao}')" class="btn variable-btn">
                <span class="var-name">${var_assinatura_padrao}</span>
                <span class="var-desc">Assinatura padrão configurada</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${assinatura_padrao}')" class="btn variable-btn">
                <span class="var-name">${assinatura_padrao}</span>
                <span class="var-desc">Área de assinatura</span>
            </button>
        </div>
    </div>
    
    {{-- FORMATAÇÃO --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-text-resize fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            FORMATAÇÃO
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${formato_fonte}')" class="btn variable-btn">
                <span class="var-name">${formato_fonte}</span>
                <span class="var-desc">Fonte padrão dos documentos</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${tamanho_fonte}')" class="btn variable-btn">
                <span class="var-name">${tamanho_fonte}</span>
                <span class="var-desc">Tamanho da fonte</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${espacamento_linhas}')" class="btn variable-btn">
                <span class="var-name">${espacamento_linhas}</span>
                <span class="var-desc">Espaçamento entre linhas</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${margens}')" class="btn variable-btn">
                <span class="var-name">${margens}</span>
                <span class="var-desc">Margens do documento</span>
            </button>
        </div>
    </div>
    
    {{-- ASSINATURA DIGITAL & QR CODE --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-security-user fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            ASSINATURA DIGITAL & QR CODE
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${assinatura_digital_info}')" class="btn variable-btn bg-light-primary">
                <span class="var-name">${assinatura_digital_info}</span>
                <span class="var-desc">Bloco completo da assinatura digital</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${qrcode_html}')" class="btn variable-btn bg-light-primary">
                <span class="var-name">${qrcode_html}</span>
                <span class="var-desc">QR Code para consulta do documento</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${assinatura_posicao}')" class="btn variable-btn">
                <span class="var-name">${assinatura_posicao}</span>
                <span class="var-desc">Posição da assinatura (centro, direita, esquerda)</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${assinatura_texto}')" class="btn variable-btn">
                <span class="var-name">${assinatura_texto}</span>
                <span class="var-desc">Texto da assinatura digital</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${qrcode_posicao}')" class="btn variable-btn">
                <span class="var-name">${qrcode_posicao}</span>
                <span class="var-desc">Posição do QR Code (centro, direita, esquerda)</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${qrcode_texto}')" class="btn variable-btn">
                <span class="var-name">${qrcode_texto}</span>
                <span class="var-desc">Texto do QR Code</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${qrcode_tamanho}')" class="btn variable-btn">
                <span class="var-name">${qrcode_tamanho}</span>
                <span class="var-desc">Tamanho do QR Code em pixels</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${qrcode_url_formato}')" class="btn variable-btn">
                <span class="var-name">${qrcode_url_formato}</span>
                <span class="var-desc">URL de consulta formatada</span>
            </button>
        </div>
    </div>
    
    {{-- CAMPOS ESPECIAIS --}}
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-abstract-42 fs-5">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            CAMPOS ESPECIAIS
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${considerandos}')" class="btn variable-btn">
                <span class="var-name">${considerandos}</span>
                <span class="var-desc">Considerandos</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${considerando_1}')" class="btn variable-btn">
                <span class="var-name">${considerando_1}</span>
                <span class="var-desc">Primeiro considerando</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${considerando_2}')" class="btn variable-btn">
                <span class="var-name">${considerando_2}</span>
                <span class="var-desc">Segundo considerando</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${considerando_3}')" class="btn variable-btn">
                <span class="var-name">${considerando_3}</span>
                <span class="var-desc">Terceiro considerando</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${texto_artigo_1}')" class="btn variable-btn">
                <span class="var-name">${texto_artigo_1}</span>
                <span class="var-desc">Texto do artigo 1º</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${texto_artigo_2}')" class="btn variable-btn">
                <span class="var-name">${texto_artigo_2}</span>
                <span class="var-desc">Texto do artigo 2º</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${texto_paragrafo_unico}')" class="btn variable-btn">
                <span class="var-name">${texto_paragrafo_unico}</span>
                <span class="var-desc">Parágrafo único</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${destinatario_mocao}')" class="btn variable-btn">
                <span class="var-name">${destinatario_mocao}</span>
                <span class="var-desc">Destinatário da moção</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${tipo_mocao}')" class="btn variable-btn">
                <span class="var-name">${tipo_mocao}</span>
                <span class="var-desc">Tipo de moção</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${tipo_manifestacao}')" class="btn variable-btn">
                <span class="var-name">${tipo_manifestacao}</span>
                <span class="var-desc">Tipo de manifestação</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${observacoes}')" class="btn variable-btn">
                <span class="var-name">${observacoes}</span>
                <span class="var-desc">Observações adicionais</span>
            </button>
        </div>
    </div>
</div>

<!-- Informações e ações -->
<div class="bg-light p-3 rounded mt-3">
    <div class="fs-8 text-muted mb-3">
        <strong>💡 Como usar:</strong> Clique na variável para copiá-la e use Ctrl+V para colar no documento.
    </div>
    
    <!-- Botão para inserir template exemplo -->
    <button type="button" onclick="onlyofficeEditor.inserirTemplateExemplo()" class="btn btn-sm btn-success w-100">
        <i class="ki-duotone ki-document fs-6 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        Inserir Template de Exemplo
    </button>
</div>

<script>
// Configurar busca de variáveis quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('variableSearch');
    if (searchInput) {
        // Função de busca
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const categories = document.querySelectorAll('.variable-category');
            
            categories.forEach(category => {
                const buttons = category.querySelectorAll('.variable-btn');
                let hasVisibleButtons = false;
                
                buttons.forEach(button => {
                    const varName = button.querySelector('.var-name').textContent.toLowerCase();
                    const varDesc = button.querySelector('.var-desc').textContent.toLowerCase();
                    
                    if (varName.includes(searchTerm) || varDesc.includes(searchTerm)) {
                        button.style.display = 'flex';
                        hasVisibleButtons = true;
                    } else {
                        button.style.display = 'none';
                    }
                });
                
                // Mostrar/ocultar categoria inteira
                category.style.display = hasVisibleButtons ? 'block' : 'none';
            });
        });
        
        // Adicionar clear button quando houver texto
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.style.paddingRight = '30px';
            } else {
                this.style.paddingRight = '12px';
            }
        });
    }
});

// Adicionar função de template exemplo ao onlyofficeEditor
if (window.onlyofficeEditor) {
    onlyofficeEditor.inserirTemplateExemplo = function() {
        const templateExemplo = `\${imagem_cabecalho}

\${cabecalho_nome_camara}
\${cabecalho_endereco}
Tel: \${cabecalho_telefone} - \${cabecalho_website}
================================================================================

\${tipo_proposicao} Nº \${prefixo_numeracao}\${numero_proposicao}/\${ano}

EMENTA: \${ementa}

Autor: \${autor_nome}
Cargo: \${autor_cargo}
Partido: \${autor_partido}

\${texto}

JUSTIFICATIVA:
\${justificativa}

\${assinatura_padrao}

--------------------------------------------------------------------------------
\${rodape_texto}
\${nome_camara} - \${nome_camara_abreviado}
\${endereco_completo}
\${municipio}/\${municipio_uf}
Tel: \${telefone_camara} | Tel. Secundário: \${telefone_protocolo}
E-mail: \${email_camara} | \${website_camara}
CNPJ: \${cnpj_camara}
Presidente: \${presidente_nome} (\${presidente_partido})
Legislatura: \${legislatura_atual} | Vereadores: \${numero_vereadores}
Horário de Funcionamento: \${horario_funcionamento}
Horário de Atendimento: \${horario_atendimento}`;
        
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(templateExemplo).then(() => {
                this.showToast('Template de exemplo copiado! Use Ctrl+V para colar no documento', 'success', 4000);
            }).catch(err => {
                console.error('Erro ao copiar template:', err);
                this.showToast('Erro ao copiar template', 'error', 3000);
            });
        } else {
            // Fallback para browsers antigos
            const textArea = document.createElement("textarea");
            textArea.value = templateExemplo;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                this.showToast('Template de exemplo copiado! Use Ctrl+V para colar no documento', 'success', 4000);
            } catch (err) {
                console.error('Erro ao copiar template:', err);
                this.showToast('Erro ao copiar template', 'error', 3000);
            }
            
            document.body.removeChild(textArea);
        }
    };
}
</script>

<style>
/* Estilos adicionais para melhorar a visualização */
.variable-category {
    margin-bottom: 1.5rem;
}

.variable-category-title {
    font-weight: 700;
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 6px;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.variable-items {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.variable-btn {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
    padding: 0.625rem 0.875rem !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
}

.variable-btn:hover {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    border-color: #2196f3 !important;
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15);
}

.variable-btn:active {
    transform: scale(0.98);
}

.var-name {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Courier New', monospace;
    font-size: 0.8rem;
    font-weight: 600;
    color: #1976d2;
}

.var-desc {
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 400;
}

.variable-search {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.variable-search:focus {
    border-color: #2196f3;
    box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.1);
    outline: none;
}

/* Estilo para categoria colapsada quando não há resultados */
.variable-category[style*="display: none"] {
    margin: 0;
}

/* Scroll suave para a lista de variáveis */
#variablesList {
    scroll-behavior: smooth;
}

/* Highlight na variável quando copiada */
@keyframes highlight {
    0% { background-color: #4caf50; transform: scale(1.05); }
    100% { background-color: transparent; transform: scale(1); }
}

.variable-btn.copied {
    animation: highlight 0.5s ease;
}
</style>