#!/bin/bash

echo "========================================="
echo "Teste: Verificação da Opção PFX"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}1. Verificando se PFX foi adicionado ao service:${NC}"

# Verificar se PFX está na constante TIPOS_CERTIFICADO
if grep -q "'PFX' => 'Arquivo .pfx/.p12'" /home/bruno/legisinc/app/Services/AssinaturaDigitalService.php; then
    echo -e "${GREEN}✅ PFX encontrado na constante TIPOS_CERTIFICADO${NC}"
else
    echo -e "${RED}❌ PFX não encontrado na constante${NC}"
fi

echo ""
echo -e "${YELLOW}2. Testando chamada do getTiposCertificado():${NC}"

# Testar através do artisan tinker
docker exec -it legisinc-app php artisan tinker --execute="
\$service = new App\\Services\\AssinaturaDigitalService();
\$tipos = \$service->getTiposCertificado();
echo 'Tipos disponíveis:' . PHP_EOL;
foreach(\$tipos as \$key => \$value) {
    echo \"- {\$key}: {\$value}\" . PHP_EOL;
}
"

echo ""
echo -e "${YELLOW}3. Limpando cache se necessário:${NC}"

# Limpar cache do Laravel
docker exec -it legisinc-app php artisan cache:clear
docker exec -it legisinc-app php artisan view:clear
docker exec -it legisinc-app php artisan config:clear

echo -e "${GREEN}Cache limpo com sucesso${NC}"

echo ""
echo -e "${YELLOW}4. Verificando se a interface foi atualizada:${NC}"

# Verificar se o formulário tem os campos PFX
if grep -q "campo_pfx" /home/bruno/legisinc/resources/views/assinatura/formulario-simplificado.blade.php; then
    echo -e "${GREEN}✅ Campos PFX encontrados na interface${NC}"
else
    echo -e "${RED}❌ Campos PFX não encontrados na interface${NC}"
fi

echo ""
echo "========================================="
echo -e "${GREEN}Teste Completo!${NC}"
echo ""
echo "Se PFX ainda não aparecer:"
echo "1. Acesse: http://localhost:8001/proposicoes/10/assinatura-digital"
echo "2. Pressione Ctrl+F5 para forçar reload"
echo "3. Verifique se agora aparece a opção 'Arquivo .pfx/.p12'"
echo ""
echo "Opções esperadas no select:"
echo "- A1: Certificado A1 (arquivo digital)"
echo "- A3: Certificado A3 (cartão/token)"
echo "- PFX: Arquivo .pfx/.p12"
echo "- SIMULADO: Assinatura Simulada (desenvolvimento)"
echo "========================================="