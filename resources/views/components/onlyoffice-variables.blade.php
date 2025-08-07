{{-- OnlyOffice Variables Panel Component --}}
{{-- Lista de variáveis disponíveis para inserir no documento --}}

<!-- Busca de variáveis -->
<input type="text" id="variableSearch" class="variable-search form-control mb-3" placeholder="Buscar variáveis...">

<!-- Categorias de variáveis -->
<div id="variablesList">
    <!-- Dados da Proposição -->
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-document">
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
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${status_proposicao}')" class="btn variable-btn">
                <span class="var-name">${status_proposicao}</span>
                <span class="var-desc">Status atual</span>
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
        </div>
    </div>
    
    <!-- Autor e Parlamentar -->
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-user">
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
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${nome_parlamentar}')" class="btn variable-btn">
                <span class="var-name">${nome_parlamentar}</span>
                <span class="var-desc">Nome do parlamentar</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${cargo_parlamentar}')" class="btn variable-btn">
                <span class="var-name">${cargo_parlamentar}</span>
                <span class="var-desc">Cargo do parlamentar</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${email_parlamentar}')" class="btn variable-btn">
                <span class="var-name">${email_parlamentar}</span>
                <span class="var-desc">E-mail do parlamentar</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${partido_parlamentar}')" class="btn variable-btn">
                <span class="var-name">${partido_parlamentar}</span>
                <span class="var-desc">Partido político</span>
            </button>
        </div>
    </div>
    
    <!-- Datas e Horários -->
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-calendar">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            DATAS & HORÁRIOS
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${data_atual}')" class="btn variable-btn">
                <span class="var-name">${data_atual}</span>
                <span class="var-desc">Data atual (dd/mm/aaaa)</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${data_extenso}')" class="btn variable-btn">
                <span class="var-name">${data_extenso}</span>
                <span class="var-desc">Data por extenso</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${hora_atual}')" class="btn variable-btn">
                <span class="var-name">${hora_atual}</span>
                <span class="var-desc">Hora atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${data_criacao}')" class="btn variable-btn">
                <span class="var-name">${data_criacao}</span>
                <span class="var-desc">Data de criação</span>
            </button>
        </div>
    </div>
    
    <!-- Instituição -->
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-bank">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            INSTITUIÇÃO
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${municipio}')" class="btn variable-btn">
                <span class="var-name">${municipio}</span>
                <span class="var-desc">Nome do município</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${nome_camara}')" class="btn variable-btn">
                <span class="var-name">${nome_camara}</span>
                <span class="var-desc">Nome da câmara</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${endereco_camara}')" class="btn variable-btn">
                <span class="var-name">${endereco_camara}</span>
                <span class="var-desc">Endereço da câmara</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${legislatura_atual}')" class="btn variable-btn">
                <span class="var-name">${legislatura_atual}</span>
                <span class="var-desc">Legislatura atual</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${sessao_legislativa}')" class="btn variable-btn">
                <span class="var-name">${sessao_legislativa}</span>
                <span class="var-desc">Sessão legislativa</span>
            </button>
        </div>
    </div>
    
    <!-- Imagens e Mídia -->
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-picture">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            IMAGENS & MÍDIA
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${imagem_cabecalho}')" class="btn variable-btn bg-light-primary">
                <span class="var-name">${imagem_cabecalho}</span>
                <span class="var-desc">Inserir imagem padrão do cabeçalho</span>
            </button>
        </div>
    </div>
    
    <!-- Campos Editáveis -->
    <div class="variable-category">
        <div class="variable-category-title">
            <i class="ki-duotone ki-pencil">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            CAMPOS EDITÁVEIS
        </div>
        <div class="variable-items">
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${observacoes}')" class="btn variable-btn">
                <span class="var-name">${observacoes}</span>
                <span class="var-desc">Observações adicionais</span>
            </button>
            <button type="button" onclick="onlyofficeEditor.inserirVariavel('${considerandos}')" class="btn variable-btn">
                <span class="var-name">${considerandos}</span>
                <span class="var-desc">Considerandos</span>
            </button>
        </div>
    </div>
</div>

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
// Adicionar função de busca ao componente principal
if (window.onlyofficeEditor) {
    onlyofficeEditor.inserirTemplateExemplo = function() {
        const templateExemplo = `\${imagem_cabecalho}

MOÇÃO Nº \${numero_proposicao}

Autor: \${autor_nome}
Cargo: \${cargo_parlamentar}
Partido: \${partido_parlamentar}
Data: \${data_atual}
Município: \${municipio}

Ementa: \${ementa}

\${texto}

Justificativa:
\${justificativa}

\${nome_camara}
\${data_extenso}

_______________________________
Assinatura do Autor`;
        
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(templateExemplo).then(() => {
                this.showToast('Template de exemplo copiado! Use Ctrl+V para colar', 'success', 3000);
            });
        }
    };
    
    // Setup variable search
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('variableSearch');
        if (searchInput) {
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
                            button.style.display = 'block';
                            hasVisibleButtons = true;
                        } else {
                            button.style.display = 'none';
                        }
                    });
                    
                    category.style.display = hasVisibleButtons ? 'block' : 'none';
                });
            });
        }
    });
}
</script>