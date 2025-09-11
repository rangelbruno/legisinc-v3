# 🔐 Solução de Certificados Digitais - Sistema Legisinc

## 📋 Visão Geral

Esta documentação descreve a implementação completa do sistema de gerenciamento de certificados digitais no Legisinc, incluindo upload, validação, armazenamento seguro e integração com assinatura digital de documentos.

## 🎯 Problema Identificado

### Sintomas
- Certificados digitais não eram salvos no banco de dados após upload
- Interface mostrava "Parlamentar atualizado com sucesso" mas dados não persistiam
- Erros 404 para assets SVG no console do navegador
- Campos de certificado permaneciam vazios no banco de dados

### Causa Raiz
1. **Permissões de diretório incorretas**: Os diretórios de armazenamento não tinham permissões adequadas
2. **Algoritmos legados**: Certificados PFX usavam RC2-40-CBC, não suportado por padrão no OpenSSL moderno
3. **Erro de movimentação de arquivos**: `Permission denied` ao mover arquivos entre diretórios

## 💡 Solução Implementada

### 1. Correção de Permissões

#### Criação de Diretórios
```bash
# Criar diretórios necessários
docker exec legisinc-app mkdir -p storage/app/private/certificados-digitais
docker exec legisinc-app mkdir -p storage/app/temp

# Definir permissões corretas
docker exec legisinc-app chmod -R 755 storage/app/private/certificados-digitais
docker exec legisinc-app chmod -R 755 storage/app/temp

# Definir ownership correto
docker exec legisinc-app chown -R www-data:www-data storage/app/private/certificados-digitais
docker exec legisinc-app chown -R www-data:www-data storage/app/temp
```

### 2. Suporte a Certificados Legados

#### Atualização do OpenSSL
Adicionada flag `-legacy` em todos os comandos OpenSSL para suportar algoritmos antigos:

```php
// Antes (não funcionava)
$comando = sprintf(
    'openssl pkcs12 -in %s -passin pass:%s -noout 2>&1',
    escapeshellarg($tempFile),
    escapeshellarg($senha)
);

// Depois (funcional)
$comando = sprintf(
    'openssl pkcs12 -legacy -in %s -passin pass:%s -noout 2>&1',
    escapeshellarg($tempFile),
    escapeshellarg($senha)
);
```

### 3. Helper de Certificados

#### Arquivo: `/app/Helpers/CertificadoHelper.php`

```php
namespace App\Helpers;

class CertificadoHelper
{
    // Obter caminho completo do certificado
    public static function getCaminhoCompleto(User $user): ?string
    
    // Verificar se certificado existe fisicamente
    public static function certificadoExiste(User $user): bool
    
    // Obter senha descriptografada
    public static function getSenha(User $user): ?string
    
    // Verificar validade do certificado
    public static function isValido(User $user): bool
    
    // Obter status completo
    public static function getStatus(User $user): array
    
    // Validar certificado com senha
    public static function validar(string $caminho, string $senha): array
    
    // Configurar certificado padrão
    public static function configurarCertificadoPadrao(User $user, string $caminho, string $senha): bool
}
```

### 4. Comando Artisan

#### Arquivo: `/app/Console/Commands/ConfigurarCertificado.php`

```bash
# Uso do comando
php artisan certificado:configurar {email} {arquivo} {senha} [--salvar-senha]

# Exemplo prático
docker exec legisinc-app php artisan certificado:configurar \
  jessica@sistema.gov.br \
  /tmp/certificado_teste.pfx \
  123Ligado \
  --salvar-senha
```

### 5. Atualização do Controller

#### Arquivo: `/app/Http/Controllers/Parlamentar/ParlamentarController.php`

Método `processarCertificadoDigital()` atualizado para:
- Usar diretórios com permissões corretas
- Validar certificado com suporte a algoritmos legados
- Salvar senha criptografada quando solicitado
- Logar operações para debug

## 🔒 Segurança Implementada

### Armazenamento Seguro
- Certificados salvos em `/storage/app/private/certificados-digitais/`
- Diretório fora do public path
- Permissões restritas (0600 para arquivos, 0755 para diretórios)

### Criptografia
- Senhas dos certificados criptografadas usando `encrypt()` do Laravel
- Descriptografia sob demanda com `decrypt()`
- Nunca armazenadas em texto plano

### Validação
- Validação prévia do certificado antes do armazenamento
- Verificação de extensão (.pfx ou .p12)
- Extração e validação de CN e data de validade

## 📊 Estrutura do Banco de Dados

### Campos na tabela `users`:
```sql
certificado_digital_path         VARCHAR(255)  -- Caminho relativo do arquivo
certificado_digital_nome         VARCHAR(255)  -- Nome original do arquivo
certificado_digital_upload_em    TIMESTAMP     -- Data/hora do upload
certificado_digital_validade     TIMESTAMP     -- Data de expiração
certificado_digital_cn           VARCHAR(255)  -- Common Name do certificado
certificado_digital_ativo        BOOLEAN       -- Status ativo/inativo
certificado_digital_senha        TEXT          -- Senha criptografada
certificado_digital_senha_salva  BOOLEAN       -- Flag se senha foi salva
```

## 🚀 Como Usar

### Via Interface Web
1. Acessar `/parlamentares/{id}/edit`
2. Fazer upload do arquivo .pfx
3. Informar a senha
4. Marcar "Salvar senha" se desejar
5. Salvar formulário

### Via Comando Artisan
```bash
# Copiar certificado para o container
docker cp "caminho/certificado.pfx" legisinc-app:/tmp/cert.pfx

# Configurar certificado
docker exec legisinc-app php artisan certificado:configurar \
  email@usuario.com \
  /tmp/cert.pfx \
  senha_do_certificado \
  --salvar-senha
```

### Via Código PHP
```php
use App\Helpers\CertificadoHelper;

$user = User::find($id);
$sucesso = CertificadoHelper::configurarCertificadoPadrao(
    $user,
    '/caminho/certificado.pfx',
    'senha123'
);

if ($sucesso) {
    $status = CertificadoHelper::getStatus($user);
    // Status contém: configurado, existe, ativo, valido, etc.
}
```

## 📝 Scripts de Teste

### Script de Configuração Manual
**Arquivo**: `/scripts/configurar-certificado-jessica.php`
```bash
docker exec legisinc-app php /var/www/html/scripts/configurar-certificado-jessica.php
```

### Script de Teste de Upload
**Arquivo**: `/scripts/teste-upload-certificado.php`
```bash
docker exec legisinc-app php /var/www/html/scripts/teste-upload-certificado.php
```

## ✅ Validação da Solução

### Teste Completo
1. **Upload via interface**: ✅ Funcional
2. **Persistência no banco**: ✅ Dados salvos corretamente
3. **Validação de certificado**: ✅ Suporte a algoritmos legados
4. **Criptografia de senha**: ✅ Senha salva criptografada
5. **Permissões de arquivo**: ✅ Arquivos protegidos

### Status Final
```
Usuário: Jessica Santos
Certificado Path: certificados-digitais/certificado_2_1757614061.pfx
Certificado Nome: BRUNO JOSE PEREIRA RANGEL_31748726854.pfx
CN: BRUNO JOSE PEREIRA RANGEL:31748726854
Validade: 2026-09-09 00:00:00
Ativo: Sim
Senha Salva: Sim
```

## 🔧 Troubleshooting

### Erro: "Permission denied"
```bash
# Recriar permissões
docker exec legisinc-app chmod -R 755 storage/app/private/certificados-digitais
docker exec legisinc-app chown -R www-data:www-data storage/app/private/certificados-digitais
```

### Erro: "unsupported Algorithm (RC2-40-CBC)"
```bash
# Certificado usa algoritmo legado
# Solução: Usar flag -legacy no OpenSSL
openssl pkcs12 -legacy -in certificado.pfx -passin pass:senha -noout
```

### Erro: "Certificado não encontrado"
```bash
# Verificar se arquivo existe
docker exec legisinc-app ls -la storage/app/private/certificados-digitais/

# Verificar caminho no banco
docker exec legisinc-app php artisan tinker
>>> User::find(2)->certificado_digital_path
```

## 📚 Referências

- [OpenSSL Legacy Algorithms](https://www.openssl.org/docs/man3.0/man7/migration_guide.html)
- [Laravel Encryption](https://laravel.com/docs/10.x/encryption)
- [PHP File Permissions](https://www.php.net/manual/en/function.chmod.php)

## 🏆 Resultado

Sistema de certificados digitais totalmente funcional com:
- ✅ Upload seguro de certificados
- ✅ Validação robusta com suporte a algoritmos legados
- ✅ Armazenamento criptografado de senhas
- ✅ Interface amigável e comando CLI
- ✅ Helper completo para integração
- ✅ Documentação detalhada

---

**Versão**: 1.0.0  
**Data**: 11/09/2025  
**Autor**: Sistema Legisinc - Módulo de Certificados Digitais