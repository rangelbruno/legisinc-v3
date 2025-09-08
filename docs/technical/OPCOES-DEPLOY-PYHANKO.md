# 🛡️ Opções de Deploy PyHanko - Arquitetura de Produção

## 📋 Visão Geral

O **PyHanko** foi implementado como **container efêmero** que roda **sob demanda**. Isso significa que ele **não aparece** no `docker-compose up -d` porque não é um serviço persistente.

## 📊 Diagramas de Fluxo Visual

Para uma compreensão visual completa da arquitetura PyHanko, consulte os **diagramas interativos Mermaid**:

- **📄 Documentação**: `docs/DIAGRAMAS-FLUXO-PYHANKO-MERMAID.md`
- **🖥️ Interface Admin**: [http://localhost:8001/admin/pyhanko-fluxo](http://localhost:8001/admin/pyhanko-fluxo)

Os diagramas mostram visualmente:
- **Container efêmero** vs **serviços persistentes**
- **Volumes montados** temporariamente
- **Ciclo de vida** do processo de assinatura
- **Estados do sistema** durante execução

## 🔍 Como Verificar se Está Funcionando

### **1. Imagem Existe?**
```bash
docker images | grep pyhanko
# Deve mostrar: legisinc-pyhanko latest 397MB
```

### **2. Binário Responde?**
```bash
docker run --rm legisinc-pyhanko --version
# Deve mostrar: pyHanko, version 0.29.1 (CLI 0.1.2)
```

### **3. Ver Execução Efêmera**
```bash
# Terminal 1: Executar assinatura
./scripts/teste-pyhanko-funcional.sh

# Terminal 2: Monitorar containers
watch docker ps
# PyHanko aparece temporariamente durante a execução
```

## 🏗️ Arquiteturas de Deploy Disponíveis

### **Arquitetura 1: Container Efêmero (Atual) ⭐ RECOMENDADA**

**Como funciona:**
- PyHanko roda **sob demanda** via `docker run --rm`
- Container **não fica persistente**
- Laravel executa comando Docker externo

**Vantagens:**
- ✅ Segurança: container destrutível
- ✅ Recursos: zero overhead quando inativo
- ✅ Isolamento: cada assinatura em ambiente limpo
- ✅ Escalabilidade: múltiplas assinaturas paralelas

**Limitações:**
- ❌ Laravel precisa acessar Docker do host
- ❌ Requer configuração de segurança específica

**Status Atual:**
```bash
# ❌ Laravel NÃO tem acesso ao Docker
docker exec legisinc-app docker --version
# Erro: executable file not found
```

---

### **Arquitetura 2: Docker Socket no Laravel (Solução Simples)**

**Implementação:**
```yaml
# docker-compose.yml
services:
  app:
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock  # Acesso ao Docker host
    environment:
      - DOCKER_HOST=unix:///var/run/docker.sock
```

**Dockerfile Laravel atualizado:**
```dockerfile
# Instalar Docker CLI no container Laravel
RUN curl -fsSL https://get.docker.com | sh
```

**Comando de assinatura:** (mesmo atual)
```php
// AssinaturaDigitalService.php - funciona sem mudanças
$comando = [
    'docker', 'run', '--rm',
    '-v', dirname($pdf) . ':/work',
    '-v', dirname($pfx) . ':/certs:ro',
    '-e', 'PFX_PASS=' . $senha,
    'legisinc-pyhanko', ...
];
```

**Vantagens:**
- ✅ Solução mais simples
- ✅ Código atual funciona sem mudanças
- ✅ Container efêmero mantido

**Desvantagens:**
- ❌ **Implicações de segurança**: Laravel tem controle total do Docker host
- ❌ Container Laravel pode gerenciar qualquer container
- ❌ Risco de escape de container

---

### **Arquitetura 3: Docker Compose Run (Mais Segura)**

**Implementação:**
```yaml
# docker-compose.yml (já implementado)
services:
  pyhanko:
    image: legisinc-pyhanko:latest
    profiles: ["tools", "signing"]  # Não sobe no up -d
    environment:
      - PFX_PASS
    volumes:
      - ./storage:/work
      - ./docker/pyhanko/certs:/certs:ro
    networks:
      - legisinc_network
```

**Comando de assinatura:**
```bash
# Em vez de docker run, usar docker compose run
docker compose run --rm pyhanko \
  --config /work/pyhanko.yml \
  sign addsig --use-pades \
  --timestamp-url https://freetsa.org/tsr \
  --with-validation-info \
  pkcs12 --p12-setup legisinc \
  /work/in.pdf /work/out.pdf
```

**Laravel atualizado:**
```php
// AssinaturaDigitalService.php
$comando = [
    'docker', 'compose', 'run', '--rm', 'pyhanko',
    '--config', '/work/pyhanko.yml',
    'sign', 'addsig', '--use-pades',
    '--timestamp-url', 'https://freetsa.org/tsr',
    '--with-validation-info',
    'pkcs12', '--p12-setup', 'legisinc',
    '/work/' . basename($pdf_in),
    '/work/' . basename($pdf_out)
];
```

**Vantagens:**
- ✅ Volumes/redes versionados no compose
- ✅ Não aparece no `up -d`
- ✅ Melhor organização
- ✅ Profiles para diferentes ambientes

**Desvantagens:**
- ❌ Ainda precisa do Docker socket
- ❌ Requer mudança no código Laravel

---

### **Arquitetura 4: PyHanko Nativo no Laravel (Sem Docker Nesting)**

**Implementação:**
```dockerfile
# Dockerfile Laravel
RUN apt-get update && apt-get install -y python3 python3-pip
RUN pip3 install "pyHanko[pkcs11,image-support,opentype]" pyhanko-cli
```

**Laravel atualizado:**
```php
// AssinaturaDigitalService.php
$comando = [
    'pyhanko', 
    '--config', storage_path('pyhanko.yml'),
    'sign', 'addsig', '--use-pades',
    '--timestamp-url', 'https://freetsa.org/tsr',
    '--with-validation-info',
    'pkcs12', '--p12-setup', 'legisinc',
    $pdf_in, $pdf_out
];
```

**Vantagens:**
- ✅ Sem Docker nesting
- ✅ Sem implicações de segurança
- ✅ Execução mais rápida
- ✅ Controle total do ambiente

**Desvantagens:**
- ❌ Laravel container fica maior
- ❌ Dependências Python no PHP
- ❌ Menos isolamento

---

### **Arquitetura 5: Job/Worker no Host (Mais Segura)**

**Implementação:**
```bash
# Worker no host (fora do container)
# /etc/systemd/system/legisinc-signer.service
[Unit]
Description=Legisinc Digital Signature Worker
After=docker.service

[Service]
Type=simple
ExecStart=/opt/legisinc/signature-worker.sh
Restart=always
User=legisinc-signer

[Install]
WantedBy=multi-user.target
```

**Worker script:**
```bash
#!/bin/bash
# /opt/legisinc/signature-worker.sh
while true; do
    # Monitor queue Redis/database
    # Execute PyHanko when needed
    docker run --rm -v /data:/work legisinc-pyhanko ...
    sleep 5
done
```

**Laravel atualizado:**
```php
// Envia para queue em vez de executar diretamente
dispatch(new AssinaturaDigitalJob($pdf, $certificado));
```

**Vantagens:**
- ✅ **Máxima segurança**: worker isolado
- ✅ Laravel não acessa Docker
- ✅ Processamento assíncrono
- ✅ Pode rodar em servidor separado

**Desvantagens:**
- ❌ Arquitetura mais complexa
- ❌ Requer worker/queue setup
- ❌ Monitoramento adicional

---

## 📋 Comparação das Arquiteturas

| Arquitetura | Segurança | Simplicidade | Performance | Isolamento | Recomendação |
|-------------|-----------|---------------|-------------|------------|---------------|
| **1. Efêmero (atual)** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ✅ **Para dev/teste** |
| **2. Docker Socket** | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⚠️ **Apenas dev** |
| **3. Compose Run** | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ✅ **Organizado** |
| **4. PyHanko Nativo** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ | ✅ **Produção simples** |
| **5. Worker Host** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ✅ **Produção enterprise** |

## 🎯 Recomendações por Ambiente

### **🏠 Desenvolvimento**
**Arquitetura 2 (Docker Socket)**
```bash
# Habilitar Docker socket (APENAS DESENVOLVIMENTO)
# docker-compose.override.yml
services:
  app:
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
```

### **🏢 Produção Simples**
**Arquitetura 4 (PyHanko Nativo)**
- Instalar PyHanko diretamente no container Laravel
- Sem Docker nesting, sem riscos de segurança
- Performance otimizada

### **🏛️ Produção Enterprise**
**Arquitetura 5 (Worker no Host)**
- Worker dedicado no host
- Queue assíncrona (Redis/Database)
- Máxima segurança e escalabilidade

## 🚀 Como Implementar Cada Arquitetura

### **Implementar Arquitetura 2 (Docker Socket):**
```bash
# 1. Adicionar Docker socket
echo "
services:
  app:
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock" >> docker-compose.override.yml

# 2. Instalar Docker CLI no Laravel
docker exec legisinc-app bash -c "curl -fsSL https://get.docker.com | sh"

# 3. Testar
docker exec legisinc-app docker --version
```

### **Implementar Arquitetura 4 (PyHanko Nativo):**
```bash
# 1. Atualizar Dockerfile
echo 'RUN apt-get update && apt-get install -y python3 python3-pip
RUN pip3 install "pyHanko[pkcs11,image-support,opentype]" pyhanko-cli' >> Dockerfile

# 2. Rebuild container
docker-compose build app

# 3. Testar
docker exec legisinc-app pyhanko --version
```

### **Testar Arquitetura 3 (Compose Run):**
```bash
# Já implementado! Testar:
cd /home/bruno/legisinc

# Definir senha
export PFX_PASS="123456"

# Executar (profiles aplicados automaticamente)
docker compose run --rm pyhanko --version
```

## 🔧 Script de Teste para Cada Arquitetura

### **Teste Arquitetura Efêmera (atual):**
```bash
./scripts/teste-pyhanko-funcional.sh
```

### **Teste Docker Compose Run:**
```bash
./scripts/teste-pyhanko-compose-run.sh  # Criar este script
```

### **Teste PyHanko Nativo:**
```bash
./scripts/teste-pyhanko-nativo.sh       # Criar este script
```

---

## 🎯 Conclusão

**Para produção empresarial segura**, recomendamos:

1. **Desenvolvimento**: Arquitetura 2 (Docker Socket) - simples e funcional
2. **Produção**: Arquitetura 4 (PyHanko Nativo) - segura e performática  
3. **Enterprise**: Arquitetura 5 (Worker no Host) - máxima segurança

A **arquitetura efêmera atual** está perfeita para **desenvolvimento e testes**. Para produção, escolha a arquitetura adequada ao seu nível de segurança e complexidade desejados.

**🛡️ Sistema PyHanko v2.2 está preparado para qualquer arquitetura de deploy!**