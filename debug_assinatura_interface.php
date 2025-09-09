<?php
/**
 * Debug para verificar se a interface de assinatura está funcionando
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carregar app Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Proposicao;

echo "🔍 Debug da Interface de Assinatura Digital\n\n";

// 1. Verificar usuário com certificado
echo "1️⃣ Verificando usuário Jessica Santos (ID: 2):\n";
try {
    $user = User::find(2);
    if ($user) {
        echo "✅ Usuário encontrado: {$user->name}\n";
        echo "   📄 CN: {$user->certificado_digital_cn}\n";
        echo "   📅 Validade: {$user->certificado_digital_validade}\n";
        echo "   🔵 Ativo: " . ($user->certificado_digital_ativo ? 'Sim' : 'Não') . "\n";
        echo "   🔐 Senha salva: " . ($user->certificado_digital_senha_salva ? 'Sim' : 'Não') . "\n\n";
        
        // Verificar métodos
        echo "   🔍 temCertificadoDigital(): " . ($user->temCertificadoDigital() ? 'Sim' : 'Não') . "\n";
        echo "   ✅ certificadoDigitalValido(): " . ($user->certificadoDigitalValido() ? 'Sim' : 'Não') . "\n";
        
        // Verificar se o certificado está expirado
        $validade = \Carbon\Carbon::parse($user->certificado_digital_validade);
        $agora = \Carbon\Carbon::now();
        echo "   ⏰ Validade em Carbon: {$validade->format('d/m/Y')}\n";
        echo "   📅 Data atual: {$agora->format('d/m/Y')}\n";
        echo "   🔴 Está expirado: " . ($validade->isPast() ? 'Sim' : 'Não') . "\n\n";
        
    } else {
        echo "❌ Usuário não encontrado\n\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: {$e->getMessage()}\n\n";
}

// 2. Verificar proposição
echo "2️⃣ Verificando proposição ID 1:\n";
try {
    $proposicao = Proposicao::find(1);
    if ($proposicao) {
        echo "✅ Proposição encontrada: {$proposicao->ementa}\n";
        echo "   📊 Status: {$proposicao->status}\n";
        echo "   👤 Autor: {$proposicao->autor->name}\n";
        echo "   🎯 Pronta para assinatura: " . (in_array($proposicao->status, ['aprovado', 'aprovado_assinatura']) ? 'Sim' : 'Não') . "\n\n";
    } else {
        echo "❌ Proposição não encontrada\n\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: {$e->getMessage()}\n\n";
}

// 3. Simular dados que serão passados para a view
echo "3️⃣ Dados que serão passados para a view:\n";
if (isset($user) && $user) {
    $certificadoCadastrado = $user->temCertificadoDigital();
    $certificadoValido = $user->certificadoDigitalValido();
    $senhaSalva = $user->certificado_digital_senha_salva ?? false;
    
    echo "   certificadoCadastrado: " . ($certificadoCadastrado ? 'true' : 'false') . "\n";
    echo "   certificadoValido: " . ($certificadoValido ? 'true' : 'false') . "\n";
    echo "   senhaSalva: " . ($senhaSalva ? 'true' : 'false') . "\n\n";
    
    if ($certificadoCadastrado) {
        $dadosCertificado = [
            'cn' => $user->certificado_digital_cn,
            'validade' => \Carbon\Carbon::parse($user->certificado_digital_validade),
            'ativo' => $user->certificado_digital_ativo,
            'senha_salva' => $senhaSalva,
            'path' => $user->certificado_digital_path
        ];
        echo "   dadosCertificado:\n";
        echo "     - CN: {$dadosCertificado['cn']}\n";
        echo "     - Validade: {$dadosCertificado['validade']->format('d/m/Y')}\n";
        echo "     - Ativo: " . ($dadosCertificado['ativo'] ? 'Sim' : 'Não') . "\n";
        echo "     - Senha salva: " . ($dadosCertificado['senha_salva'] ? 'Sim' : 'Não') . "\n\n";
    }
}

// 4. Verificar comportamento esperado da interface
echo "4️⃣ Comportamento esperado da interface:\n";
if (isset($certificadoCadastrado) && $certificadoCadastrado) {
    echo "✅ DEVE mostrar seção 'Certificado Digital Detectado'\n";
    
    if ($certificadoValido) {
        echo "✅ DEVE mostrar alert verde (alert-success)\n";
        echo "✅ DEVE mostrar checkbox 'Usar Certificado Cadastrado' marcado\n";
        echo "✅ DEVE ocultar seção 'Tipo de Certificado' por padrão\n";
        
        if ($senhaSalva) {
            echo "✅ DEVE ocultar campo de senha\n";
            echo "✅ DEVE mostrar botão 'Assinar Automaticamente'\n";
            echo "✅ DEVE mostrar SweetAlert de assinatura automática\n";
        } else {
            echo "✅ DEVE mostrar campo de senha\n";
            echo "✅ DEVE mostrar botão 'Assinar Documento'\n";
            echo "✅ DEVE mostrar SweetAlert informando que senha será solicitada\n";
        }
    } else {
        echo "⚠️  DEVE mostrar alert laranja (alert-warning)\n";
        echo "⚠️  DEVE mostrar badge 'EXPIRADO'\n";
        echo "⚠️  DEVE mostrar SweetAlert de certificado expirado\n";
        echo "⚠️  DEVE permitir usar opção manual\n";
    }
} else {
    echo "ℹ️  DEVE mostrar interface tradicional (sem certificado detectado)\n";
    echo "ℹ️  DEVE mostrar opções A1, A3, PFX normalmente\n";
}

echo "\n";

// 5. URLs para teste
echo "5️⃣ URLs para teste:\n";
echo "   🌐 Assinatura Digital: http://localhost:8001/proposicoes/1/assinatura-digital\n";
echo "   👤 Editar Parlamentar: http://localhost:8001/parlamentares/2/edit\n";
echo "   📄 Ver Proposição: http://localhost:8001/proposicoes/1\n\n";

echo "🎯 SITUAÇÃO ATUAL:\n";
echo "   - Certificado CADASTRADO ✅\n";
echo "   - Certificado EXPIRADO ⚠️\n";
echo "   - Senha NÃO SALVA ❌\n";
echo "   - Interface DEVE mostrar certificado com aviso de expirado\n\n";

echo "💡 PARA TESTAR COMPLETAMENTE:\n";
echo "   1. Atualize a validade do certificado para futuro\n";
echo "   2. Ou cadastre um novo certificado\n";
echo "   3. Acesse a URL de assinatura digital\n\n";

echo "✅ Debug concluído!\n";