# 🔐 Solução: Erro de Encriptação de Senha de Certificado Digital

## 📋 Problema Identificado

### Sintomas
- Botão "Usar Este Certificado" retorna erro 422: "Por favor, informe a senha do certificado"
- Interface mostra "Senha salva: Sim" mas sistema solicita senha novamente
- Log mostra erro `senha_salva_nula` (saved password is null)
- Certificado configurado mas senha não é recuperada

### Reprodução do Erro
1. Usuário configura certificado digital com senha
2. Sistema confirma que senha foi salva (`certificado_digital_senha_salva = true`)
3. Ao tentar usar certificado, sistema falha em recuperar senha
4. Retorna erro mesmo com senha aparentemente salva

## 🔍 Causa Raiz

### Análise Técnica
O problema estava na implementação dos métodos de criptografia da senha no modelo `User`:

**Arquivo**: `/app/Models/User.php`

#### Método `salvarSenhaCertificado()` (INCORRETO)
```php
public function salvarSenhaCertificado(string $senha): bool
{
    return $this->update([
        'certificado_digital_senha' => $senha, // ❌ Salvando em texto plano
        'certificado_digital_senha_salva' => true,
    ]);
}
```

#### Método `getSenhaCertificado()` (ESPERANDO CRIPTOGRAFADO)
```php
public function getSenhaCertificado(): ?string
{
    if (empty($this->getAttributes()['certificado_digital_senha'])) {
        return null;
    }
    
    try {
        // ❌ Tentando descriptografar senha em texto plano
        return decrypt($this->getAttributes()['certificado_digital_senha']);
    } catch (\Exception $e) {
        // Falha na descriptografia, limpar campo
        $this->update([
            'certificado_digital_senha' => null,
            'certificado_digital_senha_salva' => false,
        ]);
        return null;
    }
}
```

### Incompatibilidade
- **Método Save**: Salva senha em **texto plano**
- **Método Get**: Espera senha **criptografada**
- **Resultado**: Falha na descriptografia → retorna `null`

## 💡 Solução Implementada

### Correção do Método `salvarSenhaCertificado()`

**Arquivo**: `/app/Models/User.php` - Linha 733

```php
public function salvarSenhaCertificado(string $senha): bool
{
    return $this->update([
        'certificado_digital_senha' => encrypt($senha), // ✅ Criptografar manualmente
        'certificado_digital_senha_salva' => true,
    ]);
}
```

### Validação da Correção
```bash
# Teste via Tinker
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'jessica@sistema.gov.br')->first();

# Salvar senha
\$result = \$user->salvarSenhaCertificado('123Ligado');
echo 'Password saved: ' . (\$result ? 'success' : 'failed') . PHP_EOL;

# Recuperar senha
\$senha = \$user->fresh()->getSenhaCertificado();
echo 'Retrieved password: ' . (\$senha ? \$senha : 'null') . PHP_EOL;
echo 'Password matches: ' . (\$senha === '123Ligado' ? 'yes' : 'no') . PHP_EOL;
"
```

**Resultado esperado**:
```
Password saved: success
Retrieved password: 123Ligado
Password matches: yes
```

## 🔧 Context Técnico

### Por que o Cast 'encrypted' Não Funcionava

No modelo User, linha 75:
```php
// 'certificado_digital_senha' => 'encrypted', // Removido pois estava causando problemas
```

O cast `encrypted` foi removido anteriormente devido a problemas, mas o comentário no método `salvarSenhaCertificado()` ainda referenciava:
```php
'certificado_digital_senha' => $senha, // O cast 'encrypted' cuida da criptografia ❌
```

### Fluxo Correto Após Correção

1. **Salvar senha**: `encrypt($senha)` → Armazena valor criptografado no banco
2. **Recuperar senha**: `decrypt($valorDoBanco)` → Descriptografa e retorna senha original
3. **Usar certificado**: Sistema recupera senha corretamente e prossegue com assinatura

## 📊 Diagnóstico Rápido

### Verificar Estado do Certificado
```bash
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'SEU_EMAIL@sistema.gov.br')->first();
echo 'User ID: ' . \$user->id . PHP_EOL;
echo 'Certificate Path: ' . \$user->certificado_digital_path . PHP_EOL;
echo 'Password Saved Flag: ' . (\$user->certificado_digital_senha_salva ? 'true' : 'false') . PHP_EOL;
echo 'Encrypted Password Length: ' . (\$user->certificado_digital_senha ? strlen(\$user->certificado_digital_senha) : 'null') . PHP_EOL;
echo 'Certificate Active: ' . (\$user->certificado_digital_ativo ? 'true' : 'false') . PHP_EOL;
"
```

### Estados Esperados

#### ❌ Estado com Problema
```
Password Saved Flag: false
Encrypted Password Length: null
```
ou
```
Password Saved Flag: true
Encrypted Password Length: 9  # Senha em texto plano
```

#### ✅ Estado Correto
```
Password Saved Flag: true
Encrypted Password Length: 200+  # Senha criptografada (JSON base64)
```

## 🚨 Troubleshooting

### Erro: `senha_salva_nula`
**Causa**: Senha não foi salva ou não pode ser descriptografada
**Solução**: Aplicar correção no método `salvarSenhaCertificado()` e salvar senha novamente

### Erro: "Por favor, informe a senha do certificado"
**Causa**: `getSenhaCertificado()` retorna `null`
**Solução**: Verificar se senha está criptografada no banco e método save está correto

### Erro: InvalidPayloadException
**Causa**: Tentativa de descriptografar texto plano
**Solução**: Limpar senha atual e salvar novamente com método corrigido

## 🔄 Procedimento de Recuperação

### Para Usuário Específico
```bash
# 1. Identificar usuário
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'USUARIO@sistema.gov.br')->first();
echo 'User found: ' . \$user->name . PHP_EOL;
"

# 2. Limpar senha atual (se corrompida)
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'USUARIO@sistema.gov.br')->first();
\$user->removerSenhaCertificado();
echo 'Password cleared' . PHP_EOL;
"

# 3. Salvar senha novamente (após correção do código)
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'USUARIO@sistema.gov.br')->first();
\$user->salvarSenhaCertificado('SENHA_CORRETA');
echo 'Password saved with encryption' . PHP_EOL;
"
```

### Para Todos os Usuários (Script de Migração)
```php
// Script de correção em massa (se necessário)
$usuarios = User::whereNotNull('certificado_digital_senha')
    ->where('certificado_digital_senha_salva', true)
    ->get();

foreach ($usuarios as $user) {
    $senhaAtual = $user->getAttributes()['certificado_digital_senha'];
    
    // Se senha tem menos de 50 chars, provavelmente é texto plano
    if (strlen($senhaAtual) < 50) {
        echo "Corrigindo senha para usuário: {$user->email}" . PHP_EOL;
        
        // Assumir que é a senha em texto plano e re-criptografar
        $user->update([
            'certificado_digital_senha' => encrypt($senhaAtual),
        ]);
    }
}
```

## ✅ Validação Final

### Teste Completo do Fluxo
1. **Salvar senha**: Via interface ou comando Artisan
2. **Verificar no banco**: Senha deve estar criptografada (>50 caracteres)
3. **Testar recuperação**: `getSenhaCertificado()` deve retornar senha original
4. **Usar certificado**: Botão "Usar Este Certificado" deve funcionar sem pedir senha

### Logs de Sucesso
```
[INFO] Usando senha salva do certificado {"user_id":2}
[INFO] Validação de certificado bem-sucedida
[INFO] Certificado configurado e válido
```

## 📚 Referências Relacionadas

- `docs/SOLUCAO-CERTIFICADO-DIGITAL.md` - Implementação completa do sistema
- `app/Helpers/CertificadoHelper.php` - Helper de certificados
- `app/Console/Commands/ConfigurarCertificado.php` - Comando Artisan

## 🏆 Resultado

Após a correção:
- ✅ Senhas são salvas criptografadas no banco de dados
- ✅ Senhas são recuperadas e descriptografadas corretamente
- ✅ Botão "Usar Este Certificado" funciona automaticamente
- ✅ Sistema de assinatura digital totalmente operacional

---

**Versão**: 1.0.0  
**Data**: 12/09/2025  
**Problema**: Incompatibilidade entre métodos de save/get de senha do certificado  
**Solução**: Criptografia manual no método `salvarSenhaCertificado()`