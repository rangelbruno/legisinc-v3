# Sistema de Certificado Digital - Documentação Completa

## 📋 Visão Geral

Sistema completo para gerenciamento de certificados digitais (arquivos .pfx/.p12) integrado ao módulo de parlamentares, permitindo upload, validação, armazenamento seguro e uso para assinatura digital de documentos.

## 🎯 Funcionalidades Principais

### 1. Upload de Certificados
- Aceita arquivos com extensão `.pfx` ou `.p12`
- Validação de senha no momento do upload
- Suporte a certificados com algoritmos legacy (RC2-40-CBC)
- Tamanho máximo: 5MB
- Opção para salvar senha criptografada para assinatura automática

### 2. Validação com OpenSSL
- Validação usando OpenSSL 3.5 com flag `-legacy`
- Extração automática de CN (Common Name)
- Extração da data de validade
- Verificação de senha antes do armazenamento

### 3. Armazenamento Seguro
- Arquivos salvos em `/storage/app/private/certificados-digitais/`
- Nome único: `certificado_{user_id}_{timestamp}.pfx`
- Permissões de arquivo: 644 (leitura pelo owner)
- Diretório com permissões: 777

### 4. Interface Visual
- Upload disponível em `/parlamentares/{id}/edit`
- Upload disponível em `/parlamentares/create`
- Exibição na aba "Visão Geral" do parlamentar
- Interface de substituição de certificados com detalhes do atual
- Badges coloridos indicando status:
  - 🟢 Verde: Certificado ativo
  - 🟡 Amarelo: Certificado inativo
  - 🔴 Vermelho: Não cadastrado
- Indicadores de senha salva: ✅ Sim / ⚪ Não

## 🗄️ Estrutura do Banco de Dados

### Campos na tabela `users`:
```sql
certificado_digital_path       VARCHAR(255)  -- Caminho relativo do arquivo
certificado_digital_nome       VARCHAR(255)  -- Nome original do arquivo
certificado_digital_upload_em  TIMESTAMP     -- Data/hora do upload
certificado_digital_validade   TIMESTAMP     -- Data de validade do certificado
certificado_digital_cn         VARCHAR(255)  -- Common Name do certificado
certificado_digital_ativo      BOOLEAN       -- Status ativo/inativo
certificado_digital_senha      TEXT          -- Senha criptografada (opcional)
certificado_digital_senha_salva BOOLEAN      -- Indica se a senha foi salva
```

## 📁 Arquivos do Sistema

### 1. Migrations

**Campos iniciais**: `/database/migrations/2025_09_08_195747_add_certificate_fields_to_users_table.php`
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('certificado_digital_path')->nullable();
    $table->string('certificado_digital_nome')->nullable();
    $table->timestamp('certificado_digital_upload_em')->nullable();
    $table->timestamp('certificado_digital_validade')->nullable();
    $table->string('certificado_digital_cn')->nullable();
    $table->boolean('certificado_digital_ativo')->default(false);
});
```

**Campos de senha**: `/database/migrations/2025_09_09_add_certificate_password_to_users_table.php`
```php
Schema::table('users', function (Blueprint $table) {
    $table->text('certificado_digital_senha')->nullable()
        ->comment('Senha do certificado criptografada (opcional)');
    $table->boolean('certificado_digital_senha_salva')->default(false)
        ->comment('Indica se a senha foi salva');
});
```

### 2. Model User
**Arquivo**: `/app/Models/User.php`

Métodos adicionados:
- `temCertificadoDigital()`: Verifica se usuário tem certificado
- `certificadoDigitalValido()`: Verifica se certificado está válido e não expirado
- `getCaminhoCompletoCertificado()`: Retorna caminho completo do arquivo
- `getStatusCertificadoDigital()`: Retorna status formatado
- `removerCertificadoDigital()`: Remove certificado do sistema
- `salvarSenhaCertificado()`: Salva senha criptografada do certificado
- `getSenhaCertificado()`: Recupera senha descriptografada
- `removerSenhaCertificado()`: Remove senha salva

### 3. Controller Principal
**Arquivo**: `/app/Http/Controllers/CertificadoDigitalController.php`

Métodos principais:
- `upload()`: Processa upload e validação
- `remover()`: Remove certificado
- `toggleAtivo()`: Ativa/desativa certificado
- `testar()`: Testa certificado com senha
- `validarCertificado()`: Validação usando OpenSSL

### 4. Controller Parlamentar
**Arquivo**: `/app/Http/Controllers/Parlamentar/ParlamentarController.php`

Métodos adicionados:
- `processarCertificadoDigital()`: Processa certificado no update
- `processarCertificadoDigitalCreate()`: Processa certificado na criação

### 5. Views

**Form de Upload**: `/resources/views/modules/parlamentares/components/form.blade.php`
```blade
@if($editMode && isset($parlamentar['user']))
    <!-- Seção de Certificado Digital -->
    <div class="card-header">
        <h3 class="card-title">Certificado Digital</h3>
    </div>
    <div class="card-body">
        <!-- Upload de certificado -->
    </div>
@endif
```

**Visão Geral**: `/resources/views/modules/parlamentares/show.blade.php`
```blade
<tr>
    <td class="text-muted">Certificado Digital</td>
    <td class="fw-bold text-end">
        @if($parlamentar['user'] && $parlamentar['user']['certificado_digital_ativo'])
            <span class="badge badge-success">Ativo</span>
            <div>{{ $parlamentar['user']['certificado_digital_cn'] }}</div>
            <div>Válido até: {{ date_format }}</div>
        @endif
    </td>
</tr>
```

## 🔧 Configurações Importantes

### 1. Storage Configuration
**Arquivo**: `/config/filesystems.php`

O disco `local` padrão usa `/storage/app/private/`:
```php
'local' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
]
```

### 2. Validação de Arquivos
- Extensões aceitas: `.pfx`, `.p12`
- Validação customizada (não usa `mimes:` do Laravel)
- Verificação manual de extensão

## ⚡ Melhorias de Performance e Correções v2.0

### 1. Correções JavaScript
- **Erro eliminado**: "Cannot read properties of null (reading 'style')"
- **Solução**: Verificações de segurança em todas manipulações DOM
- **Código mais robusto**: Todos elementos verificados antes do uso

### 2. Otimização de Event Listeners
- **Performance melhorada**: Event listeners passivos implementados
- **Menos warnings**: Configuração global para eventos de scroll/touch
- **Responsividade**: Interface mais fluida em dispositivos móveis

### 3. Melhorias de UX
- **Autocomplete**: Campos de senha com atributos adequados
- **Segurança visual**: Ícones e badges indicando status de senha
- **Interface aprimorada**: Formulário de substituição mais informativo

## 🐛 Problemas Conhecidos e Soluções

### 1. Erro: "Algorithm (RC2-40-CBC) not supported"
**Causa**: Certificados antigos usam algoritmos legacy não suportados por padrão no OpenSSL 3.x

**Solução**: Adicionar flag `-legacy` em todos comandos OpenSSL:
```bash
openssl pkcs12 -in "certificado.pfx" -passin "pass:senha" -noout -legacy
```

### 2. Erro: "Unable to create directory"
**Causa**: Diretório de destino não existe ou sem permissões

**Solução**: Criar diretório com permissões adequadas:
```bash
mkdir -p /var/www/html/storage/app/private/certificados-digitais
chmod 777 /var/www/html/storage/app/private/certificados-digitais
```

### 3. Erro: "store() returning false"
**Causa**: Método `store()` do Laravel com problemas

**Solução**: Usar `move()` diretamente:
```php
$arquivo->move(storage_path('app/temp'), $nomeTemp)
```

### 4. Erro: Validação "must be file of type: pfx, p12"
**Causa**: Laravel não reconhece MIME types de certificados

**Solução**: Validação customizada de extensão:
```php
$validator->after(function ($validator) use ($request) {
    if ($request->hasFile('certificado')) {
        $extensao = strtolower($arquivo->getClientOriginalExtension());
        if (!in_array($extensao, ['pfx', 'p12'])) {
            $validator->errors()->add('certificado', 'Extensão inválida');
        }
    }
});
```

## 📝 Comandos Úteis

### Verificar certificado cadastrado:
```bash
docker exec legisinc-app php artisan tinker --execute="
\$user = App\\Models\\User::find(2);
echo 'Certificado: ' . \$user->certificado_digital_cn;
echo 'Validade: ' . \$user->certificado_digital_validade;
"
```

### Testar validação OpenSSL:
```bash
docker exec legisinc-app openssl pkcs12 \
  -in "/path/to/cert.pfx" \
  -passin "pass:senha" \
  -noout -legacy
```

### Limpar logs para debug:
```bash
docker exec legisinc-app truncate -s 0 /var/www/html/storage/logs/laravel.log
```

## 🚀 Como Usar

### 1. Para adicionar certificado a um parlamentar:
1. Acesse `/parlamentares/{id}/edit`
2. Na seção "Certificado Digital", clique em "Escolher arquivo"
3. Selecione o arquivo .pfx ou .p12
4. Digite a senha do certificado
5. **Opcional**: Marque "Salvar senha criptografada para assinatura automática"
6. Clique em "Salvar"

### 2. Para criar parlamentar com certificado:
1. Acesse `/parlamentares/create`
2. Preencha os dados do parlamentar
3. **Opcional**: Marque "Fazer upload do certificado digital agora"
4. Selecione o arquivo .pfx ou .p12
5. Digite a senha do certificado
6. **Opcional**: Marque "Salvar senha criptografada para assinatura automática"
7. Clique em "Salvar"

### 3. Para substituir certificado existente:
1. Acesse `/parlamentares/{id}/edit`
2. Na seção "Certificado Digital", clique em "Substituir"
3. Veja detalhes do certificado atual
4. Selecione o novo arquivo .pfx ou .p12
5. Digite a nova senha
6. **Opcional**: Marque "Salvar senha criptografada para assinatura automática"
7. Clique em "Substituir certificado"

### 4. Para visualizar informações do certificado:
1. Acesse `/parlamentares/{id}`
2. Na aba "Visão Geral", veja a linha "Certificado Digital"
3. Informações exibidas: Status, CN, Validade, Status da senha

### 5. Para remover certificado:
1. Acesse `/parlamentares/{id}/edit`
2. Na seção "Certificado Digital", clique em "Remover certificado"
3. Confirme a remoção (também remove senha salva)

## 🔒 Segurança

### Medidas Implementadas:
- ✅ Arquivos salvos fora da pasta pública
- ✅ Validação de senha antes do armazenamento
- ✅ Nome do arquivo randomizado no servidor
- ✅ Permissões restritas nos arquivos
- ✅ **NOVO**: Senha armazenada com criptografia Laravel (opcional)
- ✅ **NOVO**: Verificações JavaScript para prevenir erros
- ✅ **NOVO**: Event listeners otimizados para performance
- ✅ Logs de todas operações com certificados
- ✅ Interface com permissões por role (Admin/Parlamentar)

### Recomendações Adicionais:
- Implementar criptografia dos arquivos .pfx no servidor
- Adicionar autenticação 2FA para operações com certificados
- Implementar audit log detalhado
- Configurar backup automático dos certificados

## 📊 Exemplo de Dados

### Certificado Cadastrado com Sucesso:
```
Usuário: Jessica Santos
Arquivo: JEAN_JONATAS_LUCAS_37651930894.pfx
CN: JEAN JONATAS LUCAS:37651930894
Validade: 08/11/2024
Status: ATIVO
Caminho: certificados-digitais/certificado_2_1757377308.pfx
Upload: 09/09/2025 00:21:48
Senha Salva: SIM (criptografada)
```

## 🎯 Próximos Passos

1. **✅ CONCLUÍDO: Integração com AssinaturaDigitalService**
   - ✅ Usar certificado para assinar PDFs
   - ✅ Implementar assinatura PAdES com PyHanko
   - ✅ Opção de senha salva para assinatura automática

2. **Validação Automática**
   - Verificar validade diariamente
   - Notificar usuários sobre certificados expirando

3. **Interface de Gerenciamento**
   - ✅ Interface melhorada de substituição
   - ✅ Exibição de status da senha
   - Histórico de uso do certificado

4. **✅ CONCLUÍDO: Melhorias de Performance**
   - ✅ Correções JavaScript implementadas
   - ✅ Event listeners otimizados
   - ✅ Interface mais responsiva

---

**Última atualização**: 09/09/2025 02:30
**Versão**: 2.0.0 - Interface Aprimorada + Senha Criptografada + Correções Performance
**Autor**: Sistema Legisinc