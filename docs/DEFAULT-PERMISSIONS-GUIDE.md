# 🚀 Guia: Sistema de Permissões Padrão por Tipo de Usuário

Este guia explica como funciona o sistema automático de permissões padrão e como utilizá-lo.

## 📋 Visão Geral

O sistema aplica automaticamente permissões lógicas para cada tipo de usuário:

- **🔴 ADMIN**: Acesso total (todas as funcionalidades)
- **🔵 LEGISLATIVO**: Gestão completa do processo legislativo
- **🟢 PARLAMENTAR**: Proposições próprias + consultas básicas
- **🟡 RELATOR**: Relatoria de proposições atribuídas
- **⚫ PROTOCOLO**: Controle de entrada e distribuição
- **🔷 ASSESSOR**: Suporte aos parlamentares
- **🔵 CIDADÃO_VERIFICADO**: Consultas públicas + participação
- **⚪ PUBLICO**: Consultas básicas públicas

## 🔧 Como Funciona

### 1. **Aplicação Automática**
- Quando um usuário acessa pela primeira vez, as permissões padrão são aplicadas automaticamente
- Não há necessidade de configuração manual inicial

### 2. **Detecção de Configuração**
- O sistema detecta se um tipo de usuário está usando:
  - ✅ **Configuração Padrão**: Verde - "Configuração Padrão"
  - ⚠️ **Personalizado**: Amarelo - "Personalizado"  
  - ⚙️ **Não Configurado**: Cinza - "Não Configurado"

### 3. **Personalização Flexível**
- Você pode personalizar as permissões a qualquer momento
- O botão "Aplicar Padrão" restaura as configurações recomendadas

## 🎯 Permissões Padrão Detalhadas

### 🔴 ADMIN (Nível 8)
```
✅ Todas as funcionalidades do sistema
✅ Gerenciamento de usuários
✅ Configurações avançadas
✅ Relatórios completos
```

### 🔵 LEGISLATIVO (Nível 7)
```
✅ dashboard.index
✅ proposicoes.* (todas as ações)
✅ usuarios.index, usuarios.show
✅ partidos.* (todas as ações)
✅ relatorios.proposicoes, relatorios.tramitacao
```

### 🟢 PARLAMENTAR (Nível 6)
```
✅ dashboard.index, profile.edit
✅ proposicoes.index (suas proposições)
✅ proposicoes.create, proposicoes.edit
✅ proposicoes.assinatura.index, proposicoes.assinatura.assinar
✅ usuarios.index (parlamentares), partidos.index
✅ relatorios.proposicoes (suas proposições)
```

### 🟡 RELATOR (Nível 5)
```
✅ dashboard.index, profile.edit
✅ proposicoes.index, proposicoes.show
✅ proposicoes.relator.* (ações de relatoria)
✅ usuarios.index, partidos.index
✅ relatorios.relatoria
```

### ⚫ PROTOCOLO (Nível 4)
```
✅ dashboard.index, profile.edit
✅ proposicoes.protocolo.* (ações de protocolo)
✅ proposicoes.index, proposicoes.show
✅ usuarios.index, usuarios.show
✅ relatorios.protocolo, relatorios.tramitacao
```

### 🔷 ASSESSOR (Nível 3)
```
✅ dashboard.index, profile.edit
✅ proposicoes.index (do parlamentar assessorado)
✅ proposicoes.create, proposicoes.edit (limitado)
✅ usuarios.index, partidos.index
```

### 🔵 CIDADÃO_VERIFICADO (Nível 2)
```
✅ dashboard.index, profile.edit
✅ proposicoes.index (públicas), proposicoes.show
✅ usuarios.index (parlamentares), partidos.index
✅ proposicoes.comentar, proposicoes.favoritar
```

### ⚪ PUBLICO (Nível 1)
```
✅ proposicoes.index (públicas/aprovadas)
✅ proposicoes.show (públicas/aprovadas)
✅ usuarios.index (parlamentares)
✅ partidos.index
```

## 🛠️ Comandos Disponíveis

### Inicializar Todas as Permissões
```bash
php artisan permissions:initialize
```

### Aplicar Para Tipo Específico
```bash
php artisan permissions:initialize --role=PARLAMENTAR
```

### Forçar Recriação
```bash
php artisan permissions:initialize --force
```

### Executar Seeder
```bash
php artisan db:seed --class=DefaultPermissionsSeeder
```

## 🎨 Interface Visual

### Cards de Tipos de Usuário
- **Badge Verde**: Usando configuração padrão
- **Badge Amarelo**: Configuração personalizada
- **Badge Cinza**: Não configurado
- **Contador**: Número de permissões ativas

### Alertas Informativos
- **🟢 Verde**: "Configuração Padrão Aplicada"
- **🟡 Amarelo**: "Configuração Personalizada"
- **🔵 Azul**: Instruções de uso

### Botões de Ação
- **"Aplicar Padrão"**: Restaura configurações recomendadas
- **"Ver Padrão"**: Preview em modal das permissões padrão
- **"Salvar Alterações"**: Botão flutuante para salvar

## 💡 Melhores Práticas

### ✅ Recomendações
1. **Deixe as configurações padrão** para a maioria dos usuários
2. **Personalize apenas quando necessário** para casos específicos
3. **Use "Ver Padrão"** para comparar configurações atuais
4. **Teste as permissões** antes de aplicar em produção

### ⚠️ Cuidados
1. **ADMIN sempre tem acesso total** - não pode ser limitado
2. **Dashboard sempre habilitado** - não pode ser removido
3. **Backup das configurações** antes de mudanças importantes
4. **Teste com usuários reais** após alterações

## 🚀 Workflow Recomendado

1. **Instalação Inicial**
   ```bash
   php artisan permissions:initialize
   ```

2. **Configuração por Demanda**
   - Acesse `/admin/screen-permissions`
   - Selecione o tipo de usuário
   - Ajuste conforme necessário

3. **Manutenção Regular**
   - Monitore badges de status
   - Restaure padrões quando apropriado
   - Documente personalizações específicas

## 📊 Monitoramento

### Logs Importantes
```
Log::info('Default permissions applied for role: PARLAMENTAR')
Log::info('No permissions found for role: RELATOR. Applying defaults.')
```

### Verificação de Status
- Interface visual mostra status atual
- Command line exibe resumo completo
- Logs registram aplicações automáticas

---

## 🎉 Resumo

O sistema de permissões padrão **simplifica drasticamente** a configuração inicial, aplicando automaticamente as permissões lógicas para cada tipo de usuário. Você pode personalizar quando necessário, mas na maioria dos casos, **as configurações padrão são suficientes e seguem as melhores práticas**.

**Para começar**: Execute `php artisan permissions:initialize` e acesse `/admin/screen-permissions` para revisar! 🚀