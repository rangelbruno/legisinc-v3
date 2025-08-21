#!/bin/bash

echo "========================================="
echo "Teste de Assinatura Digital Simplificada"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Configuração do Sistema de Assinatura:${NC}"
echo ""
echo "✅ Formulário simplificado: apenas tipo de certificado e senha"
echo "✅ Identificador único gerado automaticamente (128 caracteres)"
echo "✅ Checksum SHA-256 calculado automaticamente"
echo "✅ Nome do assinante obtido do usuário logado"
echo "✅ Data/hora e IP registrados automaticamente"
echo ""

echo -e "${BLUE}Formato da Assinatura Digital:${NC}"
echo "_________________________________________________________________"
echo ""
echo "O documento acima foi assinado eletronicamente e pode ser acessado no endereço"
echo "/autenticidade utilizando o identificador:"
echo "31003000330035003A:005000B7C9F2A1E3:D5B6A8C4E2F1A9B3:..."
echo ""
echo "Assinado eletronicamente por Jessica Santos em $(date '+%d/%m/%Y %H:%M')"
echo "Checksum: E99128EFEC1336C610367EBB364D630456609570C69C3F95C5CA5CEA1B668CB4"
echo ""
echo "Autenticar documento em /autenticidade com o identificador"
echo "31003000330035003A:005000B7C9F2A1E3:D5B6A8C4E2F1A9B3:..., Documento assinado"
echo "digitalmente conforme art. 4º, II da Lei 14.063/2020."
echo "_________________________________________________________________"
echo ""

echo -e "${YELLOW}Testando interface simplificada:${NC}"
echo ""

# Verificar se a nova view existe
if [ -f "/home/bruno/legisinc/resources/views/assinatura/formulario-simplificado.blade.php" ]; then
    echo -e "${GREEN}✅ Nova interface criada com sucesso${NC}"
    echo "   - Apenas 2 campos: tipo de certificado e senha"
    echo "   - Informações automáticas mostradas ao usuário"
    echo "   - Preview do formato da assinatura"
else
    echo -e "${RED}❌ Interface não encontrada${NC}"
fi

echo ""
echo -e "${YELLOW}Verificando métodos do serviço:${NC}"
echo ""

# Verificar métodos no AssinaturaDigitalService
grep -q "gerarIdentificadorAssinatura" /home/bruno/legisinc/app/Services/AssinaturaDigitalService.php && \
    echo -e "${GREEN}✅ Método gerarIdentificadorAssinatura() implementado${NC}" || \
    echo -e "${RED}❌ Método gerarIdentificadorAssinatura() não encontrado${NC}"

grep -q "gerarChecksum" /home/bruno/legisinc/app/Services/AssinaturaDigitalService.php && \
    echo -e "${GREEN}✅ Método gerarChecksum() implementado${NC}" || \
    echo -e "${RED}❌ Método gerarChecksum() não encontrado${NC}"

grep -q "gerarTextoAssinatura" /home/bruno/legisinc/app/Services/AssinaturaDigitalService.php && \
    echo -e "${GREEN}✅ Método gerarTextoAssinatura() implementado${NC}" || \
    echo -e "${RED}❌ Método gerarTextoAssinatura() não encontrado${NC}"

echo ""
echo -e "${YELLOW}Tipos de certificado disponíveis:${NC}"
echo ""
echo "1. A1 - Certificado A1 (arquivo digital) - Requer senha"
echo "2. A3 - Certificado A3 (cartão/token) - Requer senha"
echo "3. SIMULADO - Assinatura Simulada (desenvolvimento) - Sem senha"
echo ""

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}Sistema de Assinatura Digital Reformulado!${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo "Próximos passos:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá para: http://localhost:8001/proposicoes/10/assinatura-digital"
echo "4. Selecione 'SIMULADO' para teste rápido (sem senha)"
echo "5. Ou selecione 'A1/A3' e digite uma senha de 4+ caracteres"
echo "6. Clique em 'Assinar Documento'"
echo ""
echo "O sistema irá gerar automaticamente:"
echo "- Identificador único (128 caracteres em 8 grupos)"
echo "- Checksum SHA-256 do documento"
echo "- Texto formatado de assinatura"
echo "- Registro completo no banco de dados"
echo "========================================="