# Guia: Como Aplicar o Estilo dos Cards do Dashboard

Este guia explica como replicar o estilo visual dos cards coloridos do dashboard em outras páginas do sistema.

## 1. Estrutura HTML dos Cards

### Estrutura Básica
```html
<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-[COR] cursor-pointer">
        <div class="card-header pt-5 pb-3">
            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                <i class="ki-duotone ki-[ICONE] text-white fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <!-- Adicione mais paths conforme necessário -->
                </i>
            </div>
        </div>
        
        <div class="card-body d-flex flex-column justify-content-end pt-0">
            <div class="d-flex align-items-center mb-3">
                <span class="fs-2hx fw-bold text-white me-2">[NÚMERO]</span>
                <span class="fs-6 fw-semibold text-white opacity-75">[UNIDADE]</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fs-6 fw-bold text-white">[TÍTULO]</span>
                <span class="badge badge-light-[COR] fs-8">[PORCENTAGEM]%</span>
            </div>
            
            <div class="progress h-6px bg-white bg-opacity-50">
                <div class="progress-bar bg-white" style="width: [PORCENTAGEM]%"></div>
            </div>
        </div>
    </div>
</div>
```

### Classes Obrigatórias
- `card card-flush h-100 mb-5 mb-xl-10` - Estrutura base do card
- `dashboard-card-[COR]` - Classe que aplica o gradiente de cor
- `cursor-pointer` - Para indicar que o card é clicável

## 2. CSS Necessário

### Estilos dos Cards
Adicione este CSS na seção `<style>` da sua view:

```css
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
```

## 3. Cores Disponíveis

### Mapeamento de Cores
- `dashboard-card-primary` - Rosa/Vermelho (#F1416C)
- `dashboard-card-success` - Verde (#17C653)
- `dashboard-card-warning` - Amarelo/Laranja (#FFC700)
- `dashboard-card-info` - Roxo (#7239EA)

### Badges Correspondentes
- `badge-light-primary` - Para cards primary
- `badge-light-success` - Para cards success  
- `badge-light-warning` - Para cards warning
- `badge-light-info` - Para cards info

## 4. Exemplo Prático

### Card Verde (Success)
```html
<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
        <div class="card-header pt-5 pb-3">
            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                <i class="ki-duotone ki-check-circle text-white fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        
        <div class="card-body d-flex flex-column justify-content-end pt-0">
            <div class="d-flex align-items-center mb-3">
                <span class="fs-2hx fw-bold text-white me-2">5</span>
                <span class="fs-6 fw-semibold text-white opacity-75">aprovadas</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fs-6 fw-bold text-white">Aguardando Assinatura</span>
                <span class="badge badge-light-success fs-8">83%</span>
            </div>
            
            <div class="progress h-6px bg-white bg-opacity-50">
                <div class="progress-bar bg-white" style="width: 83%"></div>
            </div>
        </div>
    </div>
</div>
```

## 5. Funcionalidades Interativas

### Tornando Cards Clicáveis
```html
<!-- Navegação direta -->
<div class="card ... cursor-pointer" onclick="window.location.href='{{ route('rota.destino') }}'">

<!-- Ação JavaScript -->
<div class="card ... cursor-pointer" onclick="minhaFuncao()">

<!-- Navegação para aba -->
<div class="card ... cursor-pointer" onclick="document.getElementById('tab-id').click()">
```

## 6. Dicas Importantes

### ⚠️ Pontos de Atenção
1. **Sempre inclua o CSS** - Os cards aparecerão brancos sem os estilos CSS
2. **Use !important** - Necessário para sobrescrever estilos do template
3. **Paths dos ícones** - Verifique se todos os `<span class="pathX">` estão incluídos
4. **Imagem de fundo** - Certifique-se que `vector-1.png` existe em `assets/media/patterns/`
5. **❗ IMPORTANTE: URLs de Asset** - Use caminhos diretos `/assets/media/patterns/vector-1.png` em vez de `{{ asset() }}` dentro do CSS para evitar problemas de renderização

### ✅ Boas Práticas
1. **Consistência** - Use sempre as mesmas cores para os mesmos tipos de dados
2. **Responsividade** - Mantenha as classes de grid `col-xl-4 col-lg-6 col-md-6 col-sm-12`
3. **Acessibilidade** - Adicione `aria-label` em cards clicáveis
4. **Performance** - Reutilize o CSS em um arquivo separado se usar em muitas páginas

## 7. Localização dos Exemplos

### Arquivos de Referência
- `/resources/views/dashboard.blade.php` - Implementação original
- `/resources/views/modules/parlamentares/index.blade.php` - CSS completo
- `/resources/views/proposicoes/assinatura/index.blade.php` - Exemplo de implementação

### Como Encontrar o CSS
```bash
# Buscar por classes CSS
grep -r "dashboard-card-" resources/views/

# Buscar arquivos que usam os cards
find resources/views/ -name "*.blade.php" -exec grep -l "dashboard-card-" {} \;
```

---

## Resumo Rápido

1. **Copie a estrutura HTML** do exemplo
2. **Adicione o CSS** na seção `<style>`
3. **Substitua os placeholders** ([COR], [NÚMERO], etc.)
4. **Teste a funcionalidade** de clique
5. **Verifique a responsividade** em diferentes telas

Com este guia, você conseguirá replicar os cards do dashboard em qualquer página do sistema mantendo a consistência visual!