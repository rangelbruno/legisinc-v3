# Nova API - Dockerfile
FROM node:18-alpine

WORKDIR /app

# Copiar package.json e instalar dependências
COPY package*.json ./
RUN npm install --production

# Copiar código fonte
COPY . .

# Expor porta
EXPOSE 3001

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "const http=require('http'); \
    const options={hostname:'localhost',port:3001,path:'/health',timeout:2000}; \
    const req=http.get(options,(res)=>{process.exit(res.statusCode===200?0:1)}); \
    req.on('error',()=>process.exit(1)); \
    req.on('timeout',()=>process.exit(1));"

# Usuário não-root
USER node

# Comando de inicialização
CMD ["npm", "start"]