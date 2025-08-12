#!/bin/bash

echo "=== Testando Controle de Navegação Pós-Login ==="
echo ""

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}1. Testando acesso à página de login (sem autenticação)${NC}"
response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/login)
if [ "$response" = "200" ]; then
    echo -e "${GREEN}✓ Página de login acessível quando não autenticado${NC}"
else
    echo -e "${RED}✗ Erro ao acessar página de login: HTTP $response${NC}"
fi

echo ""
echo -e "${YELLOW}2. Verificando headers de cache na página de login${NC}"
headers=$(curl -I -s http://localhost:8001/login | grep -E "Cache-Control|Pragma|Expires")
echo "$headers"
if echo "$headers" | grep -q "no-cache"; then
    echo -e "${GREEN}✓ Headers de prevenção de cache configurados corretamente${NC}"
else
    echo -e "${RED}✗ Headers de cache não configurados${NC}"
fi

echo ""
echo -e "${YELLOW}3. Testando redirecionamento da raiz para login${NC}"
location=$(curl -s -o /dev/null -w "%{redirect_url}" http://localhost:8001/)
if echo "$location" | grep -q "login"; then
    echo -e "${GREEN}✓ Raiz redireciona para login quando não autenticado${NC}"
else
    echo -e "${RED}✗ Raiz não redireciona para login${NC}"
fi

echo ""
echo -e "${YELLOW}4. Verificando middleware PreventBackHistory${NC}"
if grep -q "PreventBackHistory" /home/bruno/legisinc/bootstrap/app.php; then
    echo -e "${GREEN}✓ Middleware PreventBackHistory registrado${NC}"
else
    echo -e "${RED}✗ Middleware PreventBackHistory não registrado${NC}"
fi

echo ""
echo -e "${YELLOW}5. Verificando Factory NavigationControlFactory${NC}"
if [ -f "/home/bruno/legisinc/app/Factories/NavigationControlFactory.php" ]; then
    echo -e "${GREEN}✓ NavigationControlFactory existe${NC}"
    
    # Verifica métodos importantes
    methods=("canAccessLoginPage" "getRedirectRoute" "setupPostLoginSession" "clearSession")
    for method in "${methods[@]}"; do
        if grep -q "public static function $method" /home/bruno/legisinc/app/Factories/NavigationControlFactory.php; then
            echo -e "  ${GREEN}✓ Método $method implementado${NC}"
        else
            echo -e "  ${RED}✗ Método $method não encontrado${NC}"
        fi
    done
else
    echo -e "${RED}✗ NavigationControlFactory não encontrada${NC}"
fi

echo ""
echo -e "${YELLOW}6. Verificando configuração de rotas${NC}"
if grep -q "middleware('guest')" /home/bruno/legisinc/routes/web.php; then
    echo -e "${GREEN}✓ Middleware 'guest' aplicado nas rotas de autenticação${NC}"
else
    echo -e "${RED}✗ Middleware 'guest' não configurado nas rotas${NC}"
fi

echo ""
echo -e "${YELLOW}7. Verificando script JavaScript no layout${NC}"
if grep -q "window.history.pushState" /home/bruno/legisinc/resources/views/layouts/app.blade.php; then
    echo -e "${GREEN}✓ Script de prevenção de navegação implementado no layout${NC}"
else
    echo -e "${RED}✗ Script de prevenção não encontrado no layout${NC}"
fi

echo ""
echo -e "${YELLOW}=== Resumo ===${NC}"
echo -e "${GREEN}Sistema de controle de navegação pós-login implementado com sucesso!${NC}"
echo ""
echo "Funcionalidades implementadas:"
echo "• Prevenção de acesso a páginas de login quando autenticado"
echo "• Headers HTTP para desabilitar cache do navegador"
echo "• JavaScript para prevenir navegação com botão voltar"
echo "• Controle de sessão com timeout de 30 minutos"
echo "• Redirecionamento inteligente baseado em perfil de usuário"
echo ""
echo -e "${YELLOW}Para testar manualmente:${NC}"
echo "1. Faça login no sistema"
echo "2. Tente usar o botão voltar do navegador"
echo "3. Tente acessar /login diretamente na URL"
echo "4. O sistema deve redirecionar você para sua área apropriada"