/**
 * Helper para autenticação nos testes E2E
 */

export class AuthHelper {
  
  /**
   * Usuários do sistema para testes
   */
  static USERS = {
    ADMIN: {
      email: 'bruno@sistema.gov.br',
      password: '123456',
      role: 'ADMIN'
    },
    PARLAMENTAR: {
      email: 'jessica@sistema.gov.br',
      password: '123456',
      role: 'PARLAMENTAR'
    },
    LEGISLATIVO: {
      email: 'joao@sistema.gov.br',
      password: '123456',
      role: 'LEGISLATIVO'
    },
    PROTOCOLO: {
      email: 'roberto@sistema.gov.br',
      password: '123456',
      role: 'PROTOCOLO'
    },
    EXPEDIENTE: {
      email: 'expediente@sistema.gov.br',
      password: '123456',
      role: 'EXPEDIENTE'
    },
    JURIDICO: {
      email: 'juridico@sistema.gov.br',
      password: '123456',
      role: 'ASSESSOR_JURIDICO'
    }
  };

  /**
   * Realizar login no sistema
   * @param {import('@playwright/test').Page} page 
   * @param {string} userType - Tipo de usuário (ADMIN, PARLAMENTAR, etc.)
   */
  static async login(page, userType = 'PARLAMENTAR') {
    const user = this.USERS[userType];
    if (!user) {
      throw new Error(`Usuário tipo '${userType}' não encontrado`);
    }

    console.log(`🔐 Fazendo login como ${user.role}: ${user.email}`);

    // Ir para página de login
    await page.goto('/login');
    
    // Preencher formulário
    await page.fill('input[name="email"]', user.email);
    await page.fill('input[name="password"]', user.password);
    
    // Submeter
    await page.click('button[type="submit"]');
    
    // Aguardar redirecionamento
    await page.waitForURL('/dashboard', { timeout: 10000 });
    
    console.log(`✅ Login realizado com sucesso para ${user.role}`);
    
    return user;
  }

  /**
   * Fazer logout
   * @param {import('@playwright/test').Page} page 
   */
  static async logout(page) {
    console.log('🚪 Fazendo logout...');
    
    try {
      // Procurar botão de logout
      await page.click('a[href*="logout"], button:has-text("Sair"), a:has-text("Logout")');
      await page.waitForURL('/login', { timeout: 5000 });
      console.log('✅ Logout realizado com sucesso');
    } catch (error) {
      console.log('⚠️ Logout via botão falhou, tentando URL direta');
      await page.goto('/logout');
      await page.waitForURL('/login', { timeout: 5000 });
    }
  }

  /**
   * Verificar se está autenticado
   * @param {import('@playwright/test').Page} page 
   */
  static async isAuthenticated(page) {
    try {
      await page.goto('/dashboard');
      return page.url().includes('/dashboard');
    } catch {
      return false;
    }
  }
}