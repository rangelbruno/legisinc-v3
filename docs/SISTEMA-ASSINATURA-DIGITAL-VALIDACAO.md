# Sistema de Assinatura Digital - Validação e Fluxo

## 📋 Visão Geral

Este documento explica como funciona o sistema de validação de assinatura digital no LegisInc, incluindo o fluxo inteligente de validação de senhas e o processo de assinatura automática com certificados cadastrados.

## 🔄 Fluxo Principal de Validação

### 1. **Detecção de Certificado Cadastrado**

Quando o usuário acessa `/proposicoes/{id}/assinatura-digital`:

```php
// AssinaturaDigitalController@mostrarFormulario
$certificadoCadastrado = $user->temCertificadoDigital();
$certificadoValido = $certificadoCadastrado ? $user->certificadoDigitalValido() : false;
$senhaSalva = $user->certificado_digital_senha_salva;
```

**Interface exibida:**
- ✅ **Com certificado**: Mostra seção "Certificado Digital Cadastrado" 
- ❌ **Sem certificado**: Mostra apenas opções de upload

### 2. **Fluxo de Assinatura Automática**

Quando o usuário clica **"Usar Este Certificado"**:

```javascript
// Vue.js - resources/views/proposicoes/assinatura/assinar-vue.blade.php
usarCertificadoCadastrado() {
    this.mostrarAlerta('info', 'Tentando usar certificado automaticamente...');
    // Tenta primeiro sem senha (se salva no BD)
    this.processarAssinatura();
}
```

### 3. **Validação Inteligente de Senha**

O sistema segue esta hierarquia de validação:

#### **Passo 1: Tentar Senha Salva (se disponível)**

```php
// AssinaturaDigitalController@processarAssinaturaCertificadoCadastrado
if ($user->certificado_digital_senha_salva) {
    try {
        $senhaTestar = $user->getSenhaCertificado();
        if ($this->validarSenhaPFX($caminhoCompleto, $senhaTestar)) {
            $senhaCertificado = $senhaTestar;
            Log::info('Usando senha salva validada com sucesso');
        } else {
            // Senha salva não funciona - remover do banco
            $user->removerSenhaCertificado();
            Log::info('Senha salva não é mais válida - removida do banco');
        }
    } catch (\Exception $e) {
        // Erro de descriptografia - limpar senha corrompida
        $user->removerSenhaCertificado();
    }
}
```

#### **Passo 2: Solicitar Senha Manual (se necessário)**

```php
// Se não há senha salva ou ela é inválida
if (!$senhaCertificado) {
    $senhaCertificado = $request->input('senha_certificado');
    if (!$senhaCertificado) {
        return response()->json([
            'success' => false,
            'message' => 'A senha salva não é mais válida. Por favor, insira a senha do certificado.'
        ], 422);
    }
}
```

## 🔐 Validação Técnica da Senha PFX

### **Método de Validação**

```php
// AssinaturaDigitalController@validarSenhaPFX
private function validarSenhaPFX($arquivoPFX, $senha)
{
    try {
        $certificates = [];
        $pfxContent = file_get_contents($arquivoPFX);
        
        // Tentar abrir o certificado com a senha
        $result = openssl_pkcs12_read($pfxContent, $certificates, $senha);
        
        if (!$result) {
            Log::info('PFX inválido com senha fornecida');
            return false;
        }
        
        // Verificar se contém dados necessários
        if (!isset($certificates['cert']) || !isset($certificates['pkey'])) {
            Log::error('Certificado PFX inválido - dados necessários não encontrados');
            return false;
        }
        
        // Verificar validade temporal
        $certInfo = openssl_x509_parse($certificates['cert']);
        $validTo = $certInfo['validTo_time_t'];
        if ($validTo < time()) {
            Log::error('Certificado PFX expirado');
            return false;
        }
        
        return true;
    } catch (\Exception $e) {
        Log::error('Erro na validação de senha PFX: ' . $e->getMessage());
        return false;
    }
}
```

## 🎯 Estados do Sistema

### **Estado 1: Senha Salva Válida**
```
Usuário clica "Usar Este Certificado"
  ↓
Sistema tenta senha salva do BD
  ↓
✅ Senha funciona → Assinatura automática
```

### **Estado 2: Senha Salva Inválida**
```
Usuário clica "Usar Este Certificado"
  ↓
Sistema tenta senha salva do BD
  ↓
❌ Senha não funciona → Remove do BD
  ↓
Exibe campo para senha manual
  ↓
Usuário insere senha → Validação
```

### **Estado 3: Sem Senha Salva**
```
Usuário clica "Usar Este Certificado"
  ↓
Sistema verifica: não há senha salva
  ↓
Exibe campo para senha manual
  ↓
Usuário insere senha → Validação
```

## 📊 Logs de Acompanhamento

### **Logs Principais**

1. **Início do processo:**
```
[INFO] processarAssinaturaCertificadoCadastrado iniciado
{
    "proposicao_id": 1,
    "user_id": 2,
    "usar_certificado_cadastrado": "1",
    "senha_certificado_presente": true/false
}
```

2. **Validação da senha salva:**
```
[INFO] Usando senha salva validada com sucesso
// OU
[INFO] Senha salva não é mais válida - removida do banco
```

3. **Dados enviados para assinatura:**
```
[INFO] Dados da assinatura enviados para service
{
    "pfx_path": "/caminho/certificado.pfx",
    "senha_length": 6,
    "tipo_certificado": "PFX"
}
```

4. **Resultado da assinatura:**
```
[INFO] Proposição assinada com certificado cadastrado
// OU
[ERROR] Senha do certificado PFX é inválida
```

## 🔧 Arquivos Envolvidos

### **Controller Principal**
- `app/Http/Controllers/AssinaturaDigitalController.php`
  - `mostrarFormulario()` - Exibe interface
  - `processarAssinatura()` - Router principal
  - `processarAssinaturaCertificadoCadastrado()` - Lógica específica
  - `validarSenhaPFX()` - Validação técnica

### **Modelo do Usuário**
- `app/Models/User.php`
  - `temCertificadoDigital()` - Verifica se há certificado
  - `certificadoDigitalValido()` - Verifica validade
  - `getSenhaCertificado()` - Recupera senha criptografada
  - `removerSenhaCertificado()` - Limpa senha inválida

### **Serviço de Assinatura**
- `app/Services/AssinaturaDigitalService.php`
  - `assinarPDF()` - Processo principal de assinatura
  - `assinarComCertificadoPFX()` - Assinatura específica PFX

### **Interface Vue.js**
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php`
  - Interface reativa com certificado cadastrado
  - Botão "Usar Este Certificado"
  - Campo de senha condicional

## ✅ Casos de Sucesso

1. **Assinatura Automática Completa**
   - Certificado válido no BD
   - Senha salva válida
   - Assinatura processada sem intervenção

2. **Recuperação de Senha Inválida**
   - Senha salva expirada/corrompida
   - Sistema detecta e remove automaticamente
   - Solicita nova senha do usuário

3. **Primeira Assinatura com Certificado**
   - Certificado cadastrado, sem senha salva
   - Sistema solicita senha
   - Processo manual bem-sucedido

## 🚨 Tratamento de Erros

- **Certificado não encontrado**: Exibe erro claro
- **Senha inválida**: Remove do BD e solicita nova
- **Certificado expirado**: Bloqueia processo com aviso
- **Arquivo corrompido**: Log detalhado para debug
- **Erro de descriptografia**: Limpa dados corrompidos

---

**📝 Última atualização:** 11/09/2025  
**🔧 Status:** Produção - Funcionando perfeitamente  
**👤 Testado por:** Usuário Parlamentar (jessica@sistema.gov.br)