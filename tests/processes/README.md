# 🏛️ Sistema de Testes de Fluxo Completo - Legisinc

Este diretório contém um sistema completo de testes que simula todo o fluxo de criação, edição e tramitação de proposições no Sistema Legisinc.

## 📋 Visão Geral

O sistema testa as seguintes etapas do processo legislativo:

1. **🔧 Administração** - Configuração de templates
2. **👤 Parlamentar** - Criação de proposição  
3. **✏️ Parlamentar** - Edição de proposição
4. **📤 Sistema** - Envio ao legislativo
5. **🏛️ Legislativo** - Edição no OnlyOffice
6. **↩️ Sistema** - Retorno ao parlamentar
7. **✍️ Parlamentar** - Assinatura digital
8. **👁️ Sistema** - Visualização PDF
9. **📋 Protocolo** - Numeração oficial

## 🚀 Execução Rápida

### Comando Único (Recomendado)
```bash
cd /home/bruno/legisinc
./tests/processes/executar-fluxo-completo.sh
```

### Execução Manual
```bash
# 1. Preparar ambiente
docker exec -it legisinc-app php artisan migrate:fresh --seed

# 2. Executar teste
docker exec -it legisinc-app php artisan test tests/processes/ProposicaoFluxoCompletoTest.php --verbose

# 3. Abrir visualizador
open tests/processes/fluxo-visualizer.html
```

## 📊 Visualização dos Resultados

### 1. **Centro de Visualização** (`index.html`)
- **Portal principal** com acesso a todas as visualizações
- **Interface moderna** com grid responsivo
- **Descrição detalhada** de cada ferramenta
- **Navegação centralizada** para todos os componentes

### 2. **Visualizador Básico** (`fluxo-visualizer.html`)
- **Interface limpa** e intuitiva
- **Status em tempo real** com cores:
  - 🟢 **Verde**: Etapa concluída com sucesso
  - 🔴 **Vermelho**: Etapa com erro (mostra detalhes)
  - 🟡 **Amarelo**: Etapa pendente
- **Estatísticas** e timeline de eventos
- **Design responsivo** para todos os dispositivos

### 3. **Dashboard Avançado** (`fluxo-dashboard.html`)
- **Gráficos D3.js** interativos
- **Controles avançados** de simulação
- **Sistema de logs** em tempo real
- **Métricas detalhadas** e estatísticas
- **Timeline** com histórico de eventos
- **Polling inteligente** com auto-refresh

### 4. **Mapa de Rede** (`network-flow.html`)
- **Visualização de arquitetura** do sistema
- **Simulação de forças** com D3.js
- **Nós arrastáveis** e interativos
- **Monitor de performance** em tempo real
- **Partículas flutuantes** para ambiente imersivo
- **15 nós** e 20+ conexões mapeadas

### 5. **Fluxo Animado** (`animated-flow.html`)
- **Animações cinematográficas** com GSAP
- **Efeitos de partículas** avançados
- **Transições suaves** entre etapas
- **Controle de velocidade** (0.1x a 3.0x)
- **Notificações visuais** em tempo real
- **Auto-loop** opcional para demonstrações

### 6. Relatório de Console
- Saída detalhada de cada etapa
- Dados técnicos (IDs, caminhos, timestamps)
- Resumo final com estatísticas

### 7. Logs do Sistema
- **Laravel Log**: `storage/logs/laravel.log`
- **OnlyOffice Logs**: Via Docker logs
- **Database**: Dados persistidos para análise

## 🔧 Arquivos do Sistema

### 📁 Estrutura
```
tests/processes/
├── ProposicaoFluxoCompletoTest.php    # Teste principal PHPUnit
├── executar-fluxo-completo.sh         # Script de execução automatizado
├── index.html                         # 🎯 Centro de Visualização (INÍCIO AQUI)
├── fluxo-visualizer.html              # 📊 Visualizador básico
├── fluxo-dashboard.html               # 🎛️ Dashboard avançado com D3.js
├── network-flow.html                  # 🌐 Mapa de rede interativo
├── animated-flow.html                 # 🎭 Fluxo animado cinematográfico
└── README.md                          # 📚 Esta documentação
```

### 📄 Descrição dos Arquivos

#### `ProposicaoFluxoCompletoTest.php`
- **Tipo**: Teste PHPUnit/Laravel
- **Função**: Simula todo o fluxo legislativo
- **Características**:
  - Usa `RefreshDatabase` para isolamento
  - Cria dados realísticos de teste
  - Valida cada etapa independentemente
  - Gera relatório detalhado

#### `executar-fluxo-completo.sh`
- **Tipo**: Script Bash
- **Função**: Automatiza a execução completa
- **Características**:
  - Verifica pré-requisitos (Docker, containers)
  - Prepara ambiente (cache, banco)
  - Executa teste com output colorido
  - Abre visualizador automaticamente

#### `fluxo-visualizer.html`
- **Tipo**: Interface web (HTML/CSS/JS)
- **Função**: Visualização gráfica dos resultados
- **Características**:
  - Design responsivo
  - Atualização em tempo real
  - Detalhes técnicos de cada etapa
  - Estatísticas e resumos

## ✅ Validações Realizadas

### 1. Configuração de Templates
- ✅ Criação de módulos de parâmetros
- ✅ Configuração de submódulos
- ✅ Criação de campos de template
- ✅ Inserção de valores padrão
- ✅ Criação de tipos de proposição
- ✅ Criação de templates RTF

### 2. Criação de Proposição
- ✅ Criação de usuário parlamentar
- ✅ Associação com template correto
- ✅ Preenchimento de dados obrigatórios
- ✅ Gravação no banco de dados
- ✅ Status inicial correto

### 3. Edição pelo Parlamentar
- ✅ Alteração de conteúdo
- ✅ Atualização de status
- ✅ Persistência das alterações
- ✅ Controle de versões

### 4. Envio ao Legislativo
- ✅ Mudança de status
- ✅ Registro de timestamp
- ✅ Disponibilização para legislativo
- ✅ Controle de acesso

### 5. Edição pelo Legislativo
- ✅ Criação de usuário legislativo
- ✅ Simulação de callback OnlyOffice
- ✅ Salvamento de arquivo editado
- ✅ Atualização de conteúdo
- ✅ Registro de revisor

### 6. Retorno ao Parlamentar
- ✅ Mudança de status para retornado
- ✅ Timestamp de retorno
- ✅ Disponibilização para assinatura
- ✅ Controle de fluxo

### 7. Assinatura Digital
- ✅ Geração de hash de assinatura
- ✅ Criação de arquivo PDF
- ✅ Registro de certificado digital
- ✅ Armazenamento de IP e timestamp
- ✅ Status de documento assinado

### 8. Visualização PDF
- ✅ Verificação de existência do arquivo
- ✅ Validação de integridade
- ✅ Controle de acesso
- ✅ Metadados do arquivo

### 9. Protocolo Oficial
- ✅ Criação de usuário protocolo
- ✅ Geração de número sequencial
- ✅ Registro oficial no sistema
- ✅ Status final de documento protocolado

## 🎯 Benefícios do Sistema

### Para Desenvolvimento
- **Detecção Precoce**: Identifica problemas antes da produção
- **Regressão**: Valida que mudanças não quebram funcionalidades
- **Documentação**: Serve como documentação viva do fluxo
- **Qualidade**: Garante que todo o processo funciona end-to-end

### Para Manutenção
- **Diagnóstico**: Identifica exatamente onde falhas ocorrem
- **Monitoramento**: Permite validação após mudanças
- **Automação**: Reduz tempo de validação manual
- **Confiabilidade**: Aumenta confiança nas releases

### Para Usuários
- **Estabilidade**: Sistema mais estável e confiável
- **Performance**: Identifica gargalos de performance
- **Experiência**: Melhora a experiência do usuário final
- **Produtividade**: Reduz tempo de resolução de problemas

## 🔍 Troubleshooting

### Problemas Comuns

#### ❌ "Container não está rodando"
```bash
# Verificar containers
docker ps

# Iniciar containers
docker-compose up -d
```

#### ❌ "Erro de permissão no banco"
```bash
# Verificar conexão com banco
docker exec -it legisinc-app php artisan migrate:status

# Recriar banco se necessário
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

#### ❌ "OnlyOffice não responde"
```bash
# Verificar OnlyOffice
curl http://localhost:8080/healthcheck

# Reiniciar se necessário
docker restart legisinc-onlyoffice
```

#### ❌ "Erro de storage/arquivos"
```bash
# Verificar permissões
docker exec -it legisinc-app ls -la storage/

# Corrigir permissões
docker exec -it legisinc-app php artisan storage:link
```

### Logs Úteis
```bash
# Log da aplicação
docker exec -it legisinc-app tail -f storage/logs/laravel.log

# Log do OnlyOffice
docker logs legisinc-onlyoffice

# Log do banco
docker logs legisinc-db
```

## 📈 Próximos Passos

### Melhorias Planejadas
- [ ] Integração com CI/CD
- [ ] Testes de performance
- [ ] Testes de carga
- [ ] Métricas de tempo de execução
- [ ] Notificações de falha
- [ ] Dashboard de monitoramento

### Expansões Possíveis
- [ ] Testes de diferentes tipos de proposição
- [ ] Simulação de múltiplos usuários
- [ ] Testes de concorrência
- [ ] Validação de segurança
- [ ] Testes de acessibilidade

## 📞 Suporte

Para problemas ou dúvidas:

1. **Verifique os logs** do sistema
2. **Execute o script de diagnóstico**
3. **Consulte a documentação** do projeto
4. **Abra um issue** no repositório

---

**Desenvolvido para garantir a qualidade e confiabilidade do Sistema Legisinc** 🏛️