import { chromium } from '@playwright/test';

async function globalSetup() {
  console.log('🚀 Iniciando configuração global dos testes...');
  
  // Verificar se o sistema está rodando
  try {
    const browser = await chromium.launch();
    const page = await browser.newPage();
    
    // Testar conectividade básica
    await page.goto('http://localhost:8001', { timeout: 30000 });
    
    console.log('✅ Sistema Legisinc está respondendo');
    
    await browser.close();
  } catch (error) {
    console.error('❌ Erro na configuração global:', error.message);
    throw error;
  }
}

export default globalSetup;