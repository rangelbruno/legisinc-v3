FROM python:3.12-slim

# Utilitários essenciais do sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    openssl ca-certificates && \
    rm -rf /var/lib/apt/lists/*

# PyHanko com CLI separado + extras para produção
RUN pip install --no-cache-dir \
    "pyHanko[pkcs11,image-support,opentype]" \
    pyhanko-cli

# Criar diretórios para certificados e configuração
RUN mkdir -p /certs/roots /work

# Copiar configuração PyHanko blindada
COPY pyhanko.yml /work/pyhanko.yml

WORKDIR /work

# Configurar timezone para Brasil
ENV TZ=America/Sao_Paulo

# CLI como ponto de entrada (não-interativo)
ENTRYPOINT ["pyhanko"]