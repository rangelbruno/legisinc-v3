// 🧪 TESTE COMPLETO DO SISTEMA DE TOKEN - Cole no console após Ctrl+F5

console.log('🧪 TESTANDO SISTEMA COMPLETO DE AUTENTICAÇÃO POR TOKEN');
console.log('===============================================');

async function testarSistemaCompleto() {
    console.log('\n🔍 FASE 1: Verificando funções disponíveis');
    console.log('-----------------------------------------');
    
    // Verificar se funções existem
    const funcoes = [
        'obterTokenAutenticacao',
        'tokenEstaValido', 
        'verificarAutenticacao',
        'executarExclusaoAjax',
        'handleModuleDeletion'
    ];
    
    let todasFuncoesExistem = true;
    funcoes.forEach(funcao => {
        if (typeof window[funcao] === 'function') {
            console.log(`✅ ${funcao} - ENCONTRADA`);
        } else {
            console.log(`❌ ${funcao} - NÃO ENCONTRADA`);
            todasFuncoesExistem = false;
        }
    });
    
    if (!todasFuncoesExistem) {
        console.log('\n❌ ERRO: Algumas funções não foram encontradas');
        console.log('🔧 SOLUÇÃO: Pressione Ctrl+F5 para recarregar completamente');
        return;
    }
    
    console.log('\n🔐 FASE 2: Testando obtenção de token');
    console.log('----------------------------------');
    
    try {
        const tokenResult = await obterTokenAutenticacao();
        
        if (tokenResult.success) {
            console.log('✅ Token obtido com sucesso!');
            console.log('🔑 Token:', tokenResult.token.substring(0, 20) + '...');
            console.log('⏰ Expira em:', tokenExpireTime?.toLocaleTimeString());
            
            // Testar se token está válido
            const tokenValido = tokenEstaValido();
            console.log('🔍 Token válido:', tokenValido ? '✅ SIM' : '❌ NÃO');
            
        } else {
            console.log('❌ Falha ao obter token:', tokenResult.error);
            if (tokenResult.needsLogin) {
                console.log('🔄 Usuário precisa fazer login');
                return;
            }
        }
    } catch (error) {
        console.log('❌ Erro na obtenção do token:', error);
        return;
    }
    
    console.log('\n🔍 FASE 3: Testando verificação de autenticação');
    console.log('---------------------------------------------');
    
    try {
        const authResult = await verificarAutenticacao();
        console.log('🔍 Resultado da verificação:', authResult);
        
        if (authResult.autenticado) {
            console.log('✅ Usuário está autenticado via token!');
            console.log('🎯 Sistema funcionando perfeitamente');
        } else {
            console.log('❌ Usuário não está autenticado');
            console.log('📝 Motivo:', authResult.motivo);
            
            if (authResult.needsLogin) {
                console.log('🔄 Redirecionamento para login necessário');
            }
        }
    } catch (error) {
        console.log('❌ Erro na verificação:', error);
    }
    
    console.log('\n🎯 FASE 4: Resultado Final');
    console.log('-------------------------');
    
    if (authToken && tokenEstaValido()) {
        console.log(`
✅ SISTEMA DE TOKEN FUNCIONANDO PERFEITAMENTE!

🔐 Status do Token:
   • Token: ${authToken.substring(0, 20)}...
   • Válido até: ${tokenExpireTime?.toLocaleTimeString()}
   • Status: ATIVO

🎯 Próximos Passos:
   1. Tente excluir um módulo/parâmetro
   2. Observe os logs detalhados no console
   3. Veja a nova interface profissional

📋 O que mudou:
   ❌ ANTES: "Tentando novamente..." + erro de sessão
   ✅ AGORA: Token seguro + verificação prévia + interface clara

🚀 EXCLUSÃO DEVE FUNCIONAR PERFEITAMENTE AGORA!
`);
    } else {
        console.log(`
⚠️ SISTEMA PARCIALMENTE FUNCIONAL

💡 Possíveis causas:
   • Sessão expirada (normal)
   • Problemas de conectividade
   • Permissões insuficientes

🔄 Sugestões:
   • Tente fazer login novamente
   • Verifique suas permissões
   • Contate o administrador se persistir
`);
    }
}

// Executar teste
testarSistemaCompleto();

// Instruções adicionais
console.log(`
🧪 COMANDOS ÚTEIS PARA DEBUG:

// Ver token atual
console.log('Token atual:', authToken);

// Ver expiração
console.log('Expira em:', tokenExpireTime);

// Verificar se está válido
console.log('Token válido:', tokenEstaValido());

// Obter novo token manualmente
obterTokenAutenticacao().then(result => console.log('Novo token:', result));

// Testar verificação de auth
verificarAutenticacao().then(auth => console.log('Auth status:', auth));
`); 