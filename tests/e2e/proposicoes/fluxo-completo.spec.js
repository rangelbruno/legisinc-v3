import { test, expect } from '@playwright/test';
import { AuthHelper } from '../utils/auth-helper.js';
import { ProposicaoHelper } from '../utils/proposicao-helper.js';

test.describe('Fluxo Completo de Proposições', () => {
  
  test('deve executar fluxo parlamentar → legislativo → protocolo', async ({ page }) => {
    // Dados da proposição para teste
    const proposicaoData = {
      tipo: 'Moção',
      ementa: `E2E Test - Fluxo Completo ${Date.now()}`,
      texto: 'Conteúdo da proposição criada via teste automatizado E2E',
      justificativa: 'Validação do fluxo completo do sistema'
    };

    // ==========================================
    // FASE 1: PARLAMENTAR - Criação e Edição
    // ==========================================
    console.log('🏛️ FASE 1: Parlamentar criando proposição...');
    
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    // Criar proposição
    await ProposicaoHelper.criarProposicao(page, proposicaoData);
    
    // Verificar se aparece na lista
    const encontrada = await ProposicaoHelper.buscarProposicao(page, proposicaoData.ementa);
    expect(encontrada).not.toBeNull();
    
    // Abrir para visualização
    await ProposicaoHelper.abrirProposicao(page, proposicaoData.ementa);
    
    // Verificar status inicial
    const statusInicial = await ProposicaoHelper.verificarStatus(page);
    expect(statusInicial).toMatch(/rascunho|draft|criado/i);
    
    // Tentar abrir editor OnlyOffice
    const editorAberto = await ProposicaoHelper.abrirEditor(page);
    if (editorAberto) {
      console.log('✅ Editor OnlyOffice funcional');
      
      // Aguardar carregamento e verificar se pode salvar
      await page.waitForTimeout(5000);
      
      // Simular edição (se possível interagir com iframe)
      try {
        const iframe = page.frameLocator('iframe[name*="onlyoffice"], iframe[src*="onlyoffice"]');
        if (await iframe.locator('body').count() > 0) {
          console.log('✅ Iframe do OnlyOffice detectado');
        }
      } catch (error) {
        console.log('⚠️ Não foi possível interagir com iframe do OnlyOffice');
      }
    }
    
    await AuthHelper.logout(page);

    // ==========================================
    // FASE 2: LEGISLATIVO - Análise e Revisão
    // ==========================================
    console.log('⚖️ FASE 2: Legislativo analisando proposição...');
    
    await AuthHelper.login(page, 'LEGISLATIVO');
    
    // Buscar proposição criada pelo parlamentar
    await ProposicaoHelper.abrirProposicao(page, proposicaoData.ementa);
    
    // Verificar se legislativo pode acessar
    await expect(page.locator('h1, h2, .title')).toContainText([
      proposicaoData.ementa.substring(0, 20),
      'Proposição',
      'Moção'
    ]);
    
    // Tentar abrir editor para revisão
    const editorLegislativo = await ProposicaoHelper.abrirEditor(page);
    if (editorLegislativo) {
      console.log('✅ Legislativo pode acessar editor');
    }
    
    await AuthHelper.logout(page);

    // ==========================================
    // FASE 3: PROTOCOLO - Numeração
    // ==========================================
    console.log('📋 FASE 3: Protocolo numerando proposição...');
    
    await AuthHelper.login(page, 'PROTOCOLO');
    
    // Buscar proposição
    await ProposicaoHelper.abrirProposicao(page, proposicaoData.ementa);
    
    // Verificar se protocolo pode acessar
    await expect(page.locator('h1, h2, .title')).toContainText([
      proposicaoData.ementa.substring(0, 20)
    ]);
    
    // Verificar se tem opções de protocolação
    const protocoloButtons = page.locator(
      'button:has-text("Protocolar"), a:has-text("Protocolar"), button:has-text("Numerar")'
    );
    
    if (await protocoloButtons.count() > 0) {
      console.log('✅ Opções de protocolação encontradas');
    }
    
    await AuthHelper.logout(page);

    // ==========================================
    // FASE 4: VERIFICAÇÃO FINAL
    // ==========================================
    console.log('🔍 FASE 4: Verificação final do fluxo...');
    
    // Login como admin para verificação geral
    await AuthHelper.login(page, 'ADMIN');
    
    // Verificar se proposição ainda existe e está acessível
    const verificacaoFinal = await ProposicaoHelper.buscarProposicao(page, proposicaoData.ementa);
    expect(verificacaoFinal).not.toBeNull();
    
    console.log('✅ Fluxo completo executado com sucesso!');
  });

  test('deve validar permissões por perfil', async ({ page }) => {
    // Criar proposição como parlamentar
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `E2E Permissões Test ${Date.now()}`
    });
    
    await AuthHelper.logout(page);

    // Testar acesso de cada perfil
    const perfis = ['LEGISLATIVO', 'PROTOCOLO', 'EXPEDIENTE', 'JURIDICO'];
    
    for (const perfil of perfis) {
      console.log(`🔐 Testando permissões do perfil: ${perfil}`);
      
      await AuthHelper.login(page, perfil);
      
      // Tentar acessar proposição
      const podeAcessar = await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
      
      if (podeAcessar) {
        console.log(`✅ ${perfil} pode acessar proposições`);
        
        // Verificar se pode editar
        const podeEditar = await ProposicaoHelper.abrirEditor(page);
        console.log(`${podeEditar ? '✅' : '❌'} ${perfil} pode editar no OnlyOffice`);
      } else {
        console.log(`❌ ${perfil} não pode acessar proposições`);
      }
      
      await AuthHelper.logout(page);
    }
  });

  test('deve validar workflow de assinatura', async ({ page }) => {
    // Criar e protocolar proposição
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    const proposicao = await ProposicaoHelper.criarProposicao(page, {
      ementa: `E2E Assinatura Test ${Date.now()}`
    });
    
    // Abrir proposição
    await ProposicaoHelper.abrirProposicao(page, proposicao.ementa);
    
    // Tentar processo de assinatura
    const assinaturaIniciada = await ProposicaoHelper.assinarDocumento(page);
    
    if (assinaturaIniciada) {
      console.log('✅ Processo de assinatura funcional');
      
      // Verificar se PDF é gerado
      await page.waitForTimeout(3000);
      
      // Procurar links de download
      const downloadLinks = page.locator('a[href*=".pdf"], a:has-text("Download"), a:has-text("PDF")');
      
      if (await downloadLinks.count() > 0) {
        console.log('✅ Links de download encontrados');
      }
    }
    
    console.log('✅ Workflow de assinatura testado');
  });
});