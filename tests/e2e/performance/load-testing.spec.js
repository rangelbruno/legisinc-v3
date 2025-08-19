import { test, expect } from '@playwright/test';
import { AuthHelper } from '../utils/auth-helper.js';
import { ProposicaoHelper } from '../utils/proposicao-helper.js';

test.describe('Testes de Performance', () => {
  
  test('deve carregar dashboard rapidamente', async ({ page }) => {
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    const startTime = Date.now();
    await page.goto('/dashboard');
    await page.waitForLoadState('networkidle');
    const endTime = Date.now();
    
    const loadTime = endTime - startTime;
    console.log(`⏱️ Tempo de carregamento da dashboard: ${loadTime}ms`);
    
    // Dashboard deve carregar em menos de 3 segundos
    expect(loadTime).toBeLessThan(3000);
  });

  test('deve carregar lista de proposições rapidamente', async ({ page }) => {
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    const startTime = Date.now();
    await page.goto('/proposicoes');
    await page.waitForLoadState('networkidle');
    const endTime = Date.now();
    
    const loadTime = endTime - startTime;
    console.log(`⏱️ Tempo de carregamento da lista: ${loadTime}ms`);
    
    // Lista deve carregar em menos de 5 segundos
    expect(loadTime).toBeLessThan(5000);
  });

  test('deve criar proposição rapidamente', async ({ page }) => {
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    const startTime = Date.now();
    
    await ProposicaoHelper.criarProposicao(page, {
      ementa: `Performance Test ${Date.now()}`
    });
    
    const endTime = Date.now();
    const creationTime = endTime - startTime;
    
    console.log(`⏱️ Tempo de criação da proposição: ${creationTime}ms`);
    
    // Criação deve ser concluída em menos de 10 segundos
    expect(creationTime).toBeLessThan(10000);
  });

  test('deve abrir editor OnlyOffice em tempo aceitável', async ({ page }) => {
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    // Criar proposição primeiro
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `Editor Performance Test ${Date.now()}`
    });
    
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    
    const startTime = Date.now();
    
    // Abrir editor
    await ProposicaoHelper.abrirEditor(page);
    
    // Aguardar iframe aparecer
    await page.waitForSelector('iframe[name*="onlyoffice"], iframe[src*="onlyoffice"]', {
      timeout: 30000
    });
    
    const endTime = Date.now();
    const editorLoadTime = endTime - startTime;
    
    console.log(`⏱️ Tempo de carregamento do editor: ${editorLoadTime}ms`);
    
    // Editor deve carregar em menos de 30 segundos
    expect(editorLoadTime).toBeLessThan(30000);
  });

  test('deve medir performance de requests da API', async ({ page }) => {
    const apiRequests = [];
    
    // Interceptar requests da API
    page.on('request', request => {
      if (request.url().includes('/api/') || 
          request.url().includes('onlyoffice') ||
          request.method() === 'POST') {
        apiRequests.push({
          url: request.url(),
          method: request.method(),
          startTime: Date.now()
        });
      }
    });
    
    page.on('response', response => {
      const matchingRequest = apiRequests.find(req => 
        req.url === response.url() && !req.endTime
      );
      
      if (matchingRequest) {
        matchingRequest.endTime = Date.now();
        matchingRequest.duration = matchingRequest.endTime - matchingRequest.startTime;
        matchingRequest.status = response.status();
      }
    });
    
    // Executar fluxo típico
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `API Performance Test ${Date.now()}`
    });
    
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    
    // Aguardar requests completarem
    await page.waitForTimeout(2000);
    
    // Analisar performance
    const completedRequests = apiRequests.filter(req => req.endTime);
    
    console.log(`📊 Total de requests da API: ${completedRequests.length}`);
    
    const slowRequests = completedRequests.filter(req => req.duration > 2000);
    const avgDuration = completedRequests.reduce((sum, req) => sum + req.duration, 0) / completedRequests.length;
    
    console.log(`📊 Duração média: ${avgDuration.toFixed(0)}ms`);
    console.log(`📊 Requests lentos (>2s): ${slowRequests.length}`);
    
    if (slowRequests.length > 0) {
      console.log('⚠️ Requests lentos encontrados:');
      slowRequests.forEach(req => {
        console.log(`   ${req.method} ${req.url.split('/').pop()} - ${req.duration}ms`);
      });
    }
    
    // A maioria dos requests deve ser rápida
    expect(avgDuration).toBeLessThan(2000);
    expect(slowRequests.length).toBeLessThan(3);
  });

  test('deve medir consumo de memória durante uso intenso', async ({ page }) => {
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    // Criar múltiplas proposições para simular uso intenso
    const proposicoes = [];
    
    for (let i = 0; i < 5; i++) {
      const prop = await ProposicaoHelper.criarProposicao(page, {
        ementa: `Memory Test ${i} - ${Date.now()}`
      });
      proposicoes.push(prop);
      
      // Pequena pausa entre criações
      await page.waitForTimeout(500);
    }
    
    // Abrir cada proposição
    for (const prop of proposicoes) {
      await ProposicaoHelper.abrirProposicao(page, prop.ementa);
      await page.waitForTimeout(1000);
    }
    
    // Medir métricas do browser
    const metrics = await page.evaluate(() => {
      if (performance.memory) {
        return {
          usedJSMemory: performance.memory.usedJSMemory,
          totalJSMemory: performance.memory.totalJSMemory,
          jsMemoryLimit: performance.memory.jsMemoryLimit
        };
      }
      return null;
    });
    
    if (metrics) {
      const usedMB = (metrics.usedJSMemory / 1024 / 1024).toFixed(2);
      const totalMB = (metrics.totalJSMemory / 1024 / 1024).toFixed(2);
      
      console.log(`💾 Memória JS usada: ${usedMB}MB`);
      console.log(`💾 Memória JS total: ${totalMB}MB`);
      
      // Não deve consumir mais que 100MB
      expect(metrics.usedJSMemory).toBeLessThan(100 * 1024 * 1024);
    }
    
    console.log('✅ Teste de memória concluído');
  });

  test('deve validar estabilidade com múltiplas abas', async ({ browser }) => {
    const contexts = [];
    const pages = [];
    
    try {
      // Abrir 3 abas simultâneas
      for (let i = 0; i < 3; i++) {
        const context = await browser.newContext();
        const page = await context.newPage();
        
        contexts.push(context);
        pages.push(page);
        
        await AuthHelper.login(page, 'PARLAMENTAR');
      }
      
      // Criar proposição em cada aba
      const startTime = Date.now();
      
      const promises = pages.map((page, index) => 
        ProposicaoHelper.criarProposicao(page, {
          ementa: `Multi-Tab Test ${index} - ${Date.now()}`
        })
      );
      
      await Promise.all(promises);
      
      const endTime = Date.now();
      const multiTabTime = endTime - startTime;
      
      console.log(`⏱️ Tempo para 3 criações simultâneas: ${multiTabTime}ms`);
      
      // Deve completar em menos de 30 segundos
      expect(multiTabTime).toBeLessThan(30000);
      
      console.log('✅ Sistema estável com múltiplas abas');
      
    } finally {
      // Cleanup
      for (const context of contexts) {
        await context.close();
      }
    }
  });
});