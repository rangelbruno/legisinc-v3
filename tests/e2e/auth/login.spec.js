import { test, expect } from '@playwright/test';
import { AuthHelper } from '../utils/auth-helper.js';

test.describe('Sistema de Autenticação', () => {
  
  test('deve permitir login do Parlamentar', async ({ page }) => {
    const user = await AuthHelper.login(page, 'PARLAMENTAR');
    
    // Verificar se está na dashboard
    await expect(page).toHaveURL(/.*dashboard/);
    
    // Verificar elementos da dashboard parlamentar
    await expect(page.locator('h1, h2, .dashboard-title')).toContainText(['Dashboard', 'Bem-vindo', 'Parlamentar']);
  });

  test('deve permitir login do Legislativo', async ({ page }) => {
    const user = await AuthHelper.login(page, 'LEGISLATIVO');
    
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('h1, h2, .dashboard-title')).toContainText(['Dashboard', 'Bem-vindo', 'Legislativo']);
  });

  test('deve permitir login do Protocolo', async ({ page }) => {
    const user = await AuthHelper.login(page, 'PROTOCOLO');
    
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('h1, h2, .dashboard-title')).toContainText(['Dashboard', 'Bem-vindo', 'Protocolo']);
  });

  test('deve permitir logout', async ({ page }) => {
    // Fazer login primeiro
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    // Fazer logout
    await AuthHelper.logout(page);
    
    // Verificar redirecionamento para login
    await expect(page).toHaveURL(/.*login/);
  });

  test('deve rejeitar credenciais inválidas', async ({ page }) => {
    await page.goto('/login');
    
    await page.fill('input[name="email"]', 'usuario@invalido.com');
    await page.fill('input[name="password"]', 'senhaerrada');
    
    await page.click('button[type="submit"]');
    
    // Deve permanecer na página de login
    await expect(page).toHaveURL(/.*login/);
    
    // Deve mostrar mensagem de erro
    await expect(page.locator('.alert, .error, [class*="error"]')).toBeVisible();
  });

  test('deve redirecionar usuário não autenticado', async ({ page }) => {
    // Tentar acessar página protegida sem login
    await page.goto('/proposicoes/create');
    
    // Deve redirecionar para login
    await expect(page).toHaveURL(/.*login/);
  });

  test('deve manter sessão após recarregar página', async ({ page }) => {
    // Fazer login
    await AuthHelper.login(page, 'PARLAMENTAR');
    
    // Recarregar página
    await page.reload();
    
    // Deve continuar na dashboard
    await expect(page).toHaveURL(/.*dashboard/);
  });
});