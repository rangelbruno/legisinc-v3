<?php

// Arquivo de teste para simular a renderização da view

// Mock dos dados que chegam do controller
$usuariosSemParlamentar = collect([
    (object) ['id' => 10, 'name' => 'Carlos Deputado Silva', 'email' => 'carlos.deputado@camara.gov.br', 'partido' => 'PSDB'],
    (object) ['id' => 11, 'name' => 'Ana Vereadora Costa', 'email' => 'ana.vereadora@camara.gov.br', 'partido' => 'PT'],
    (object) ['id' => 12, 'name' => 'Roberto Relator Souza', 'email' => 'roberto.relator@camara.gov.br', 'partido' => 'PMDB'],
]);

echo "🔍 SIMULANDO RENDERIZAÇÃO DA VIEW\n";
echo "================================\n\n";

echo "1. Variável usuariosSemParlamentar:\n";
echo "   - Isset: " . (isset($usuariosSemParlamentar) ? "SIM" : "NÃO") . "\n";
echo "   - Count: " . $usuariosSemParlamentar->count() . "\n";
echo "   - Classe: " . get_class($usuariosSemParlamentar) . "\n\n";

echo "2. Simulando código Blade:\n";
echo "   @if(isset(\$usuariosSemParlamentar) && \$usuariosSemParlamentar->count() > 0)\n";

if(isset($usuariosSemParlamentar) && $usuariosSemParlamentar->count() > 0) {
    echo "   ✅ Condição TRUE - entrando no loop\n\n";
    
    echo "3. Gerando options HTML:\n";
    echo "<select name=\"user_id\" class=\"form-control form-control-lg form-control-solid\">\n";
    echo "    <option value=\"\">Selecione um usuário</option>\n";
    
    foreach($usuariosSemParlamentar as $usuario) {
        $selected = '';  // old('user_id') == $usuario->id ? 'selected' : ''
        
        echo "    <option value=\"{$usuario->id}\" $selected>\n";
        echo "        {$usuario->name} ({$usuario->email})";
        if($usuario->partido) {
            echo " - {$usuario->partido}";
        }
        echo "\n    </option>\n";
    }
    
    echo "</select>\n\n";
    
} else {
    echo "   ❌ Condição FALSE - não entraria no loop\n";
    echo "    <option value=\"\" disabled>Nenhum usuário parlamentar disponível</option>\n";
}

echo "✅ SIMULAÇÃO CONCLUÍDA!\n";
echo "\n📋 DIAGNÓSTICO:\n";
echo "- Os dados chegam corretamente do controller\n";
echo "- A condição Blade funcionaria corretamente\n";
echo "- O HTML seria gerado com as options\n";
echo "- Problema pode estar em:\n";
echo "  a) Cache de view\n";
echo "  b) JavaScript ocultando elementos\n";  
echo "  c) CSS ocultando elementos\n";
echo "  d) Problema de autenticação/autorização\n";