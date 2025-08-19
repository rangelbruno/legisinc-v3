/**
 * Helper para operações com proposições nos testes E2E
 */

export class ProposicaoHelper {
  
  /**
   * Criar uma nova proposição
   * @param {import('@playwright/test').Page} page 
   * @param {Object} data - Dados da proposição
   */
  static async criarProposicao(page, data = {}) {
    const proposicao = {
      tipo: 'Moção',
      ementa: 'Teste E2E - Proposição criada automaticamente',
      texto: 'Este é um teste automatizado E2E para validar o sistema de proposições.',
      justificativa: 'Teste de funcionamento do sistema Legisinc',
      ...data
    };

    console.log(`📝 Criando proposição: ${proposicao.ementa}`);

    // Ir para página de criação
    await page.goto('/proposicoes/create');
    
    // Aguardar carregamento
    await page.waitForLoadState('networkidle');

    // Selecionar tipo
    await page.selectOption('select[name="tipo"]', proposicao.tipo);
    
    // Preencher ementa
    await page.fill('input[name="ementa"], textarea[name="ementa"]', proposicao.ementa);
    
    // Preencher texto se houver campo
    const textoField = page.locator('textarea[name="texto"], textarea[name="conteudo"]');
    if (await textoField.count() > 0) {
      await textoField.fill(proposicao.texto);
    }
    
    // Preencher justificativa se houver campo
    const justificativaField = page.locator('textarea[name="justificativa"]');
    if (await justificativaField.count() > 0) {
      await justificativaField.fill(proposicao.justificativa);
    }

    // Submeter formulário
    await page.click('button[type="submit"], button:has-text("Salvar"), button:has-text("Criar")');
    
    // Aguardar redirecionamento ou sucesso
    await page.waitForTimeout(2000);
    
    console.log('✅ Proposição criada com sucesso');
    
    return proposicao;
  }

  /**
   * Buscar proposição na lista
   * @param {import('@playwright/test').Page} page 
   * @param {string} ementa 
   */
  static async buscarProposicao(page, ementa) {
    console.log(`🔍 Buscando proposição: ${ementa}`);
    
    // Ir para lista de proposições
    await page.goto('/proposicoes');
    await page.waitForLoadState('networkidle');
    
    // Procurar na tabela
    const row = page.locator(`tr:has-text("${ementa}")`);
    
    if (await row.count() > 0) {
      console.log('✅ Proposição encontrada');
      return row;
    }
    
    console.log('❌ Proposição não encontrada');
    return null;
  }

  /**
   * Abrir proposição para visualização
   * @param {import('@playwright/test').Page} page 
   * @param {string} ementa 
   */
  static async abrirProposicao(page, ementa) {
    const row = await this.buscarProposicao(page, ementa);
    
    if (row) {
      // Clicar no link de visualização
      await row.locator('a:has-text("Visualizar"), a:has-text("Ver"), a[href*="show"]').first().click();
      await page.waitForLoadState('networkidle');
      
      console.log('✅ Proposição aberta para visualização');
      return true;
    }
    
    return false;
  }

  /**
   * Verificar status da proposição
   * @param {import('@playwright/test').Page} page 
   */
  static async verificarStatus(page) {
    const statusElement = page.locator('.status, .badge, [class*="status"]');
    
    if (await statusElement.count() > 0) {
      const status = await statusElement.first().textContent();
      console.log(`📊 Status da proposição: ${status}`);
      return status?.trim();
    }
    
    return null;
  }

  /**
   * Abrir editor OnlyOffice
   * @param {import('@playwright/test').Page} page 
   */
  static async abrirEditor(page) {
    console.log('📝 Abrindo editor OnlyOffice...');
    
    // Procurar botão do editor
    const editorButton = page.locator(
      'a:has-text("Editar"), a:has-text("Editor"), a[href*="onlyoffice"], .btn-onlyoffice'
    );
    
    if (await editorButton.count() > 0) {
      await editorButton.first().click();
      
      // Aguardar carregamento do editor
      await page.waitForTimeout(5000);
      
      console.log('✅ Editor OnlyOffice aberto');
      return true;
    }
    
    console.log('❌ Botão do editor não encontrado');
    return false;
  }

  /**
   * Verificar se documento foi salvo
   * @param {import('@playwright/test').Page} page 
   */
  static async verificarDocumentoSalvo(page) {
    // Verificar indicadores de salvamento
    const indicators = [
      '.saved-indicator',
      '[class*="saved"]',
      ':has-text("Salvo")',
      ':has-text("Saved")'
    ];
    
    for (const indicator of indicators) {
      if (await page.locator(indicator).count() > 0) {
        console.log('✅ Documento foi salvo');
        return true;
      }
    }
    
    console.log('⚠️ Indicador de salvamento não encontrado');
    return false;
  }

  /**
   * Simular assinatura digital
   * @param {import('@playwright/test').Page} page 
   */
  static async assinarDocumento(page) {
    console.log('✍️ Iniciando processo de assinatura...');
    
    // Procurar botão de assinatura
    const assinaturaButton = page.locator(
      'a:has-text("Assinar"), button:has-text("Assinar"), .btn-assinatura'
    );
    
    if (await assinaturaButton.count() > 0) {
      await assinaturaButton.first().click();
      
      // Aguardar processo de assinatura
      await page.waitForTimeout(3000);
      
      console.log('✅ Processo de assinatura iniciado');
      return true;
    }
    
    console.log('❌ Botão de assinatura não encontrado');
    return false;
  }
}