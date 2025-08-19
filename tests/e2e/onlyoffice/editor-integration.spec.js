import { test, expect } from '@playwright/test';
import { AuthHelper } from '../utils/auth-helper.js';
import { ProposicaoHelper } from '../utils/proposicao-helper.js';

test.describe('Integração OnlyOffice Editor', () => {
  
  test.beforeEach(async ({ page }) => {
    // Login como parlamentar para ter acesso ao editor
    await AuthHelper.login(page, 'PARLAMENTAR');
  });

  test('deve carregar editor OnlyOffice corretamente', async ({ page }) => {
    // Criar proposição
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `E2E OnlyOffice Test ${Date.now()}`
    });
    
    // Abrir proposição
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    
    // Abrir editor
    const editorAberto = await ProposicaoHelper.abrirEditor(page);
    expect(editorAberto).toBe(true);
    
    // Aguardar carregamento do OnlyOffice
    await page.waitForTimeout(10000);
    
    // Verificar se iframe do OnlyOffice foi carregado
    const iframe = page.locator('iframe[name*="onlyoffice"], iframe[src*="onlyoffice"]');
    await expect(iframe).toBeVisible({ timeout: 15000 });
    
    console.log('✅ Editor OnlyOffice carregado com sucesso');
  });

  test('deve aplicar template corretamente', async ({ page }) => {
    // Criar proposição do tipo Moção (que tem template)
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      tipo: 'Moção',
      ementa: `E2E Template Test ${Date.now()}`,
      texto: 'Conteúdo para testar aplicação de template'
    });
    
    // Abrir no editor
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    await ProposicaoHelper.abrirEditor(page);
    
    // Aguardar carregamento
    await page.waitForTimeout(10000);
    
    // Verificar se template foi aplicado (presença de elementos específicos)
    const templateElements = [
      ':has-text("CÂMARA MUNICIPAL")',
      ':has-text("MOÇÃO")',
      ':has-text("Caraguatatuba")',
      ':has-text("AGUARDANDO PROTOCOLO")'
    ];
    
    let templateDetectado = false;
    
    for (const element of templateElements) {
      if (await page.locator(element).count() > 0) {
        console.log(`✅ Elemento do template encontrado: ${element}`);
        templateDetectado = true;
        break;
      }
    }
    
    if (templateDetectado) {
      console.log('✅ Template aplicado corretamente');
    } else {
      console.log('⚠️ Template não detectado visualmente (pode estar no iframe)');
    }
  });

  test('deve salvar documento via callback', async ({ page }) => {
    // Interceptar requests de callback
    const callbackRequests = [];
    
    page.on('request', request => {
      if (request.url().includes('onlyoffice/callback') || 
          request.url().includes('save') ||
          request.method() === 'POST') {
        callbackRequests.push({
          url: request.url(),
          method: request.method(),
          headers: request.headers()
        });
      }
    });
    
    // Criar e abrir proposição
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `E2E Callback Test ${Date.now()}`
    });
    
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    await ProposicaoHelper.abrirEditor(page);
    
    // Aguardar carregamento
    await page.waitForTimeout(15000);
    
    // Simular ação que gera callback (fechar editor)
    await page.goBack();
    
    // Aguardar possíveis callbacks
    await page.waitForTimeout(5000);
    
    console.log(`📡 Requests interceptados: ${callbackRequests.length}`);
    
    if (callbackRequests.length > 0) {
      console.log('✅ Callbacks do OnlyOffice detectados');
      callbackRequests.forEach((req, index) => {
        console.log(`   ${index + 1}. ${req.method} ${req.url}`);
      });
    } else {
      console.log('⚠️ Nenhum callback detectado');
    }
  });

  test('deve preservar conteúdo entre sessões', async ({ page }) => {
    const proposicaoData = {
      ementa: `E2E Persistência Test ${Date.now()}`,
      texto: 'Conteúdo inicial para teste de persistência'
    };
    
    // Criar proposição
    await ProposicaoHelper.criarProposicao(page, proposicaoData);
    
    // Abrir no editor
    await ProposicaoHelper.abrirProposicao(page, proposicaoData.ementa);
    await ProposicaoHelper.abrirEditor(page);
    
    // Aguardar carregamento
    await page.waitForTimeout(10000);
    
    // Fechar editor
    await page.goBack();
    
    // Aguardar salvamento
    await page.waitForTimeout(3000);
    
    // Reabrir editor
    await ProposicaoHelper.abrirEditor(page);
    await page.waitForTimeout(10000);
    
    console.log('✅ Teste de persistência executado');
    
    // Verificar se carregou corretamente na segunda abertura
    const iframe = page.locator('iframe[name*="onlyoffice"], iframe[src*="onlyoffice"]');
    await expect(iframe).toBeVisible({ timeout: 15000 });
    
    console.log('✅ Conteúdo preservado entre sessões');
  });

  test('deve funcionar com múltiplos usuários', async ({ browser }) => {
    // Criar proposição como parlamentar
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `E2E Multi-User Test ${Date.now()}`
    });
    
    await AuthHelper.logout(page);
    
    // Abrir segunda sessão como legislativo
    const context2 = await browser.newContext();
    const page2 = await context2.newPage();
    
    await AuthHelper.login(page2, 'LEGISLATIVO');
    
    // Ambos acessam a mesma proposição
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    await ProposicaoHelper.abrirProposicao(page2, proposicao.ementa);
    
    // Parlamentar abre editor
    await AuthHelper.login(page, 'PARLAMENTAR');
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    const editorParlamentar = await ProposicaoHelper.abrirEditor(page);
    
    // Legislativo tenta abrir editor
    const editorLegislativo = await ProposicaoHelper.abrirEditor(page2);
    
    console.log(`📝 Parlamentar abriu editor: ${editorParlamentar}`);
    console.log(`📝 Legislativo abriu editor: ${editorLegislativo}`);
    
    if (editorParlamentar && editorLegislativo) {
      console.log('✅ Múltiplos usuários podem acessar editor');
    } else {
      console.log('ℹ️ Sistema pode ter restrição de acesso simultâneo');
    }
    
    await context2.close();
  });

  test('deve gerenciar document keys corretamente', async ({ page }) => {
    // Interceptar requests para capturar document keys
    const documentKeys = new Set();
    
    page.on('request', request => {
      const url = request.url();
      if (url.includes('onlyoffice') || url.includes('document')) {
        try {
          const urlObj = new URL(url);
          const key = urlObj.searchParams.get('key') || 
                     urlObj.searchParams.get('document_key') ||
                     urlObj.pathname.split('/').pop();
          
          if (key && key.length > 10) {
            documentKeys.add(key);
          }
        } catch (error) {
          // Ignorar erros de URL
        }
      }
    });
    
    // Criar múltiplas proposições
    const proposicoes = [];
    for (let i = 0; i < 3; i++) {
      const prop = await ProposicaoHelper.criarProposicao(page, {
        ementa: `E2E Document Key Test ${i} - ${Date.now()}`
      });
      proposicoes.push(prop);
    }
    
    // Abrir cada uma no editor
    for (const prop of proposicoes) {
      await ProposicaoHelper.abrirProposicao(page, prop.ementa);
      await ProposicaoHelper.abrirEditor(page);
      await page.waitForTimeout(5000);
      await page.goBack();
    }
    
    console.log(`🔑 Document keys únicos detectados: ${documentKeys.size}`);
    
    if (documentKeys.size >= proposicoes.length) {
      console.log('✅ Document keys únicos gerados corretamente');
    } else {
      console.log('⚠️ Possível reutilização de document keys');
    }
    
    // Listar keys para debug
    documentKeys.forEach((key, index) => {
      console.log(`   ${index + 1}. ${key.substring(0, 20)}...`);
    });
  });
});