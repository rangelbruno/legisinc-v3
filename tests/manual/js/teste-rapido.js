// 🧪 TESTE RÁPIDO - Cole no console (F12) após recarregar com Ctrl+F5

console.log('🧪 TESTE RÁPIDO INICIADO');

// 1. Testar se função existe
if (typeof handleModuleDeletion === 'function') {
    console.log('✅ handleModuleDeletion encontrada');
    
    // 2. Testar verificação de autenticação
    verificarAutenticacao().then(auth => {
        console.log('🔍 Status autenticação:', auth);
        
        if (!auth.autenticado) {
            console.log('❌ SESSÃO EXPIRADA CONFIRMADA');
            console.log('📝 Agora quando tentar excluir, deve mostrar contagem regressiva automática');
        } else {
            console.log('✅ AUTENTICADO - exclusão funcionará normalmente');
        }
    });
    
} else {
    console.log('❌ Função não encontrada - FORCE REFRESH com Ctrl+F5');
}

// 3. Mostrar instruções
console.log(`
🎯 PRÓXIMOS PASSOS:

1. Se viu "✅ handleModuleDeletion encontrada":
   → Tente excluir um parâmetro agora
   → Deve ver interface melhorada com ícones

2. Se viu "❌ SESSÃO EXPIRADA CONFIRMADA":
   → Ao tentar excluir, verá contagem regressiva automática
   → NÃO mais "Tentando novamente..."

3. Se viu "❌ Função não encontrada":
   → Pressione Ctrl+F5 para forçar atualização
   → Execute este teste novamente
`);

console.log('�� TESTE CONCLUÍDO'); 