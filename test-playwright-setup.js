#!/usr/bin/env node
import { test, expect } from '@playwright/test';

console.log('🧪 Validando configuração do Playwright...');

// Teste simples para verificar se a configuração está funcionando
test('configuração básica', async ({ browser }) => {
  console.log('✅ Browser iniciado:', browser.browserType().name());
  
  const context = await browser.newContext();
  const page = await context.newPage();
  
  // Teste básico navegando para o sistema
  try {
    await page.goto('http://localhost:8001', { timeout: 10000 });
    console.log('✅ Sistema Legisinc acessível');
    
    // Verificar se é a página de login ou dashboard
    const title = await page.title();
    console.log('📄 Título da página:', title);
    
    // Verificar elementos básicos
    const hasLoginForm = await page.locator('input[name="email"]').count() > 0;
    const hasDashboard = await page.locator('h1:has-text("Dashboard"), h2:has-text("Dashboard")').count() > 0;
    
    if (hasLoginForm) {
      console.log('🔐 Página de login detectada');
    } else if (hasDashboard) {
      console.log('🏠 Dashboard detectada');
    } else {
      console.log('❓ Página não identificada');
    }
    
  } catch (error) {
    console.log('❌ Erro ao acessar sistema:', error.message);
  }
  
  await context.close();
});

console.log('🎯 Configure o sistema e execute: npm test');
console.log('📋 Para mais opções: npm run test:ui');