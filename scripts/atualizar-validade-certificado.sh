#!/bin/bash

echo "🔧 Atualizando validade do certificado para demonstração..."

# Atualizar a validade do certificado da Jessica para 1 ano no futuro
docker exec legisinc-app php artisan tinker --execute="
\$user = App\\Models\\User::find(2);
if (\$user) {
    \$user->update([
        'certificado_digital_validade' => now()->addYear()
    ]);
    echo 'Certificado atualizado para: ' . \$user->certificado_digital_validade . \"\\n\";
    echo 'Usuário: ' . \$user->name . \"\\n\";
    echo 'CN: ' . \$user->certificado_digital_cn . \"\\n\";
    echo 'Ativo: ' . (\$user->certificado_digital_ativo ? 'Sim' : 'Não') . \"\\n\";
    echo 'Senha salva: ' . (\$user->certificado_digital_senha_salva ? 'Sim' : 'Não') . \"\\n\";
    echo \"\\n\";
    echo '✅ Certificado válido até: ' . \$user->certificado_digital_validade->format('d/m/Y') . \"\\n\";
    echo '🌐 Teste agora em: http://localhost:8001/proposicoes/1/assinatura-digital' . \"\\n\";
} else {
    echo 'Usuário não encontrado';
}
"

echo ""
echo "🎯 Certificado atualizado! Agora você pode testar:"
echo "   1. Acesse: http://localhost:8001/proposicoes/1/assinatura-digital"
echo "   2. Faça login como jessica@sistema.gov.br / 123456"
echo "   3. Observe a interface otimizada com certificado detectado!"
echo ""