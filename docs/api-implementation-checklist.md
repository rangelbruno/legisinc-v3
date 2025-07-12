# ✅ Checklist de Implementação da API - LegisInc

## 📋 Visão Geral

Este checklist guia a implementação completa da API do LegisInc, baseado na documentação `apiDocumentation.md`.

## 🚀 Fase 1: Configuração Inicial

### 1.1 Configuração do Laravel Sanctum
- [ ] Instalar Laravel Sanctum: `composer require laravel/sanctum`
- [ ] Publicar configuração: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- [ ] Executar migration: `php artisan migrate`
- [ ] Configurar middleware em `app/Http/Kernel.php`

### 1.2 Estrutura Base da API
- [ ] Criar pasta `app/Http/Controllers/Api/`
- [ ] Criar pasta `app/Http/Resources/`
- [ ] Criar pasta `app/Http/Middleware/`
- [ ] Configurar rotas em `routes/api.php`

### 1.3 Middleware Personalizado
- [ ] Criar `ApiVersionMiddleware`
- [ ] Configurar CORS para API
- [ ] Criar middleware de rate limiting
- [ ] Registrar middlewares no Kernel

## 🔐 Fase 2: Autenticação

### 2.1 AuthController
- [ ] Criar `AuthController` com métodos:
  - [ ] `login()`
  - [ ] `logout()`
  - [ ] `refresh()`
  - [ ] `me()`
- [ ] Implementar validação de credenciais
- [ ] Configurar retorno de tokens
- [ ] Adicionar middleware de autenticação

### 2.2 Testes de Autenticação
- [ ] Criar testes para login válido
- [ ] Criar testes para credenciais inválidas
- [ ] Testar logout
- [ ] Testar refresh de token

## 👥 Fase 3: Gestão de Usuários

### 3.1 UserController API
- [ ] Criar `UserController` com métodos:
  - [ ] `index()` - Listar usuários
  - [ ] `show()` - Mostrar usuário
  - [ ] `store()` - Criar usuário
  - [ ] `update()` - Atualizar usuário
  - [ ] `destroy()` - Excluir usuário
- [ ] Implementar filtros e paginação
- [ ] Validar dados de entrada

### 3.2 UserResource
- [ ] Criar `UserResource` para formatação
- [ ] Configurar campos de retorno
- [ ] Implementar campos condicionais
- [ ] Adicionar relacionamentos

### 3.3 Permissões de Usuário
- [ ] Implementar `assignRole()`
- [ ] Implementar `removeRole()`
- [ ] Validar permissões de acesso
- [ ] Criar middleware de autorização

## 🏛️ Fase 4: Gestão de Parlamentares

### 4.1 ParlamentarController
- [ ] Criar `ParlamentarController` com CRUD completo
- [ ] Implementar busca por filtros
- [ ] Adicionar validações específicas
- [ ] Configurar relacionamentos

### 4.2 ParlamentarResource
- [ ] Criar resource para formatação
- [ ] Implementar campos calculados (idade, etc.)
- [ ] Adicionar informações de mandatos
- [ ] Formatar dados de comissões

## 📄 Fase 5: Gestão de Projetos

### 5.1 ProjetoController Principal
- [ ] Criar `ProjetoController` com métodos:
  - [ ] `index()` - Listar projetos
  - [ ] `show()` - Mostrar projeto completo
  - [ ] `store()` - Criar projeto
  - [ ] `update()` - Atualizar projeto
  - [ ] `destroy()` - Excluir projeto
- [ ] Implementar filtros avançados
- [ ] Configurar paginação
- [ ] Validar dados de entrada

### 5.2 ProjetoResource
- [ ] Criar resource principal
- [ ] Implementar campos calculados
- [ ] Adicionar relacionamentos
- [ ] Formatar datas e status

### 5.3 Funcionalidades Específicas
- [ ] Implementar `updateStatus()`
- [ ] Criar método para próxima etapa
- [ ] Validar transições de status
- [ ] Implementar regras de negócio

## 🔄 Fase 6: Tramitação

### 6.1 TramitacaoController
- [ ] Criar `addTramitacao()`
- [ ] Implementar `getTramitacao()`
- [ ] Validar ações de tramitação
- [ ] Configurar histórico

### 6.2 Sistema de Status
- [ ] Definir fluxo de tramitação
- [ ] Implementar validação de transições
- [ ] Criar logs de mudanças
- [ ] Notificar interessados

## 🗂️ Fase 7: Gestão de Anexos

### 7.1 Sistema de Upload
- [ ] Configurar storage para arquivos
- [ ] Implementar `addAnexo()`
- [ ] Validar tipos de arquivo
- [ ] Configurar limite de tamanho

### 7.2 Gestão de Arquivos
- [ ] Implementar `getAnexos()`
- [ ] Criar `deleteAnexo()`
- [ ] Configurar download seguro
- [ ] Implementar controle de acesso

## 📝 Fase 8: Controle de Versões

### 8.1 VersionController
- [ ] Criar `createVersion()`
- [ ] Implementar `getVersions()`
- [ ] Configurar diff entre versões
- [ ] Implementar changelog

### 8.2 Comparação de Versões
- [ ] Criar endpoint para comparar
- [ ] Implementar visualização de mudanças
- [ ] Configurar rollback se necessário
- [ ] Validar permissões de versionamento

## 🏢 Fase 9: Tipos e Modelos

### 9.1 TipoProjetoController
- [ ] Criar CRUD para tipos
- [ ] Implementar validação de templates
- [ ] Configurar campos obrigatórios
- [ ] Criar sistema de ativação/desativação

### 9.2 ModeloProjetoController
- [ ] Criar CRUD para modelos
- [ ] Implementar variáveis de template
- [ ] Configurar geração automática
- [ ] Validar estrutura de modelos

## 📊 Fase 10: Relatórios

### 10.1 RelatorioController
- [ ] Criar `projetos()` com estatísticas
- [ ] Implementar `parlamentares()`
- [ ] Criar `tramitacao()` com métricas
- [ ] Adicionar filtros por período

### 10.2 Exportação de Dados
- [ ] Implementar export para Excel
- [ ] Criar export para PDF
- [ ] Configurar agendamento de relatórios
- [ ] Implementar cache para relatórios pesados

## 🔍 Fase 11: Busca e Filtros

### 11.1 BuscaController
- [ ] Implementar busca global
- [ ] Configurar relevância dos resultados
- [ ] Implementar autocomplete
- [ ] Criar filtros avançados

### 11.2 Otimização de Busca
- [ ] Implementar índices de busca
- [ ] Configurar cache de buscas
- [ ] Implementar busca full-text
- [ ] Otimizar queries de busca

## 📈 Fase 12: Métricas

### 12.1 MetricasController
- [ ] Criar dashboard de métricas
- [ ] Implementar contadores em tempo real
- [ ] Configurar alertas automáticos
- [ ] Implementar histórico de métricas

### 12.2 Monitoramento
- [ ] Configurar logs de API
- [ ] Implementar rastreamento de performance
- [ ] Criar alertas de erro
- [ ] Implementar health checks

## 🔐 Fase 13: Permissões Avançadas

### 13.1 Sistema de Permissões
- [ ] Criar `PermissaoController`
- [ ] Implementar `RoleController`
- [ ] Configurar permissões granulares
- [ ] Implementar herança de permissões

### 13.2 Middleware de Autorização
- [ ] Criar middleware para cada recurso
- [ ] Implementar verificação de ownership
- [ ] Configurar permissões por endpoint
- [ ] Implementar cache de permissões

## 🧪 Fase 14: Testes

### 14.1 Testes de Integração
- [ ] Criar testes para autenticação
- [ ] Testar CRUD de usuários
- [ ] Testar CRUD de projetos
- [ ] Testar sistema de tramitação

### 14.2 Testes de Permissões
- [ ] Testar acesso não autorizado
- [ ] Testar diferentes níveis de permissão
- [ ] Testar ownership de recursos
- [ ] Testar rate limiting

### 14.3 Testes de Performance
- [ ] Testar endpoints com muitos dados
- [ ] Testar paginação
- [ ] Testar uploads grandes
- [ ] Testar consultas complexas

## 🔄 Fase 15: Versionamento

### 15.1 Estratégia de Versões
- [ ] Implementar versionamento de API
- [ ] Configurar compatibilidade backward
- [ ] Criar sistema de deprecation
- [ ] Implementar changelog automático

### 15.2 Documentação
- [ ] Gerar documentação automática
- [ ] Configurar Swagger/OpenAPI
- [ ] Criar exemplos de uso
- [ ] Implementar sandbox de testes

## 🚀 Fase 16: Deploy e Produção

### 16.1 Configuração de Produção
- [ ] Configurar cache Redis
- [ ] Implementar queue system
- [ ] Configurar rate limiting
- [ ] Implementar HTTPS obrigatório

### 16.2 Monitoramento em Produção
- [ ] Configurar logs estruturados
- [ ] Implementar métricas detalhadas
- [ ] Criar alertas automáticos
- [ ] Configurar backup automático

## 📚 Recursos Adicionais

### Comandos Úteis
```bash
# Criar controller API
php artisan make:controller Api/ProjetoController --api

# Criar resource
php artisan make:resource ProjetoResource

# Criar middleware
php artisan make:middleware ApiVersionMiddleware

# Criar teste de feature
php artisan make:test Api/ProjetoApiTest

# Executar testes
php artisan test --filter=Api

# Gerar documentação
php artisan api:docs
```

### Estrutura de Arquivos Final
```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── UserController.php
│   │       ├── ParlamentarController.php
│   │       ├── ProjetoController.php
│   │       ├── TramitacaoController.php
│   │       ├── AnexoController.php
│   │       ├── VersionController.php
│   │       ├── TipoProjetoController.php
│   │       ├── ModeloProjetoController.php
│   │       ├── RelatorioController.php
│   │       ├── BuscaController.php
│   │       ├── MetricasController.php
│   │       └── PermissaoController.php
│   ├── Resources/
│   │   ├── UserResource.php
│   │   ├── ParlamentarResource.php
│   │   ├── ProjetoResource.php
│   │   ├── TramitacaoResource.php
│   │   └── AnexoResource.php
│   ├── Middleware/
│   │   ├── ApiVersionMiddleware.php
│   │   └── ApiAuthMiddleware.php
│   └── Requests/
│       ├── StoreProjetoRequest.php
│       ├── UpdateProjetoRequest.php
│       └── StoreTramitacaoRequest.php
```

## 🎯 Validação Final

### Checklist de Qualidade
- [ ] Todos os endpoints documentados estão implementados
- [ ] Validação de dados funcionando corretamente
- [ ] Sistema de permissões funcionando
- [ ] Testes cobrindo cenários principais
- [ ] Documentação atualizada e precisa
- [ ] Performance adequada para produção
- [ ] Logs estruturados implementados
- [ ] Tratamento de erros padronizado
- [ ] Rate limiting configurado
- [ ] Backup e recovery testados

---

**Última Atualização**: 2025-07-12  
**Versão do Checklist**: 1.0.0  
**Referência**: [apiDocumentation.md](./apiDocumentation.md) 