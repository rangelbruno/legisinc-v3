// TESTE RÁPIDO - Cole este código no console (F12) para verificar se as mudanças estão ativas

console.log('🧪 INICIANDO TESTE DE SESSÃO EXPIRADA...');

// Verificar se as funções existem
if (typeof verificarAutenticacao === 'function') {
    console.log('✅ Função verificarAutenticacao encontrada');
    
    // Testar verificação de autenticação
    verificarAutenticacao().then(result => {
        console.log('🔍 Resultado da verificação:', result);
        
        if (result.autenticado) {
            console.log('✅ Usuário está autenticado');
            
            // Simular tentativa de exclusão
            console.log('🧪 Simulando tentativa de exclusão...');
            
            // Se você quiser testar o fluxo completo (CUIDADO - isso vai tentar excluir algo!):
            // handleModuleDeletion(999999); // ID inexistente para testar
            
        } else {
            console.log('❌ Usuário NÃO está autenticado:', result.motivo);
        }
    }).catch(error => {
        console.error('❌ Erro na verificação:', error);
    });
    
} else {
    console.error('❌ Função verificarAutenticacao NÃO encontrada - código não foi atualizado!');
    console.log('📝 INSTRUÇÕES:');
    console.log('1. Pressione Ctrl+F5 (ou Cmd+Shift+R no Mac) para forçar atualização');
    console.log('2. Ou limpe o cache: DevTools > Application > Storage > Clear site data');
    console.log('3. Recarregue a página e execute este teste novamente');
}

// Verificar outras funções
const funcoesEsperadas = ['handleModuleDeletion', 'executeModuleDeletion', 'podeExcluirModulo'];
funcoesEsperadas.forEach(funcao => {
    if (typeof window[funcao] === 'function') {
        console.log(`✅ Função ${funcao} encontrada`);
    } else {
        console.log(`❌ Função ${funcao} NÃO encontrada`);
    }
});

console.log('🧪 TESTE CONCLUÍDO - Verifique os resultados acima'); 