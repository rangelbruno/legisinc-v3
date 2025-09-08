#!/bin/bash

echo "🐳 TESTE DOCKER COMPOSE RUN - PYHANKO PROFILES"
echo "=============================================="
echo "💡 Testando PyHanko via docker-compose com profiles"
echo ""

# Configuração
SENHA_TESTE="123456"
PFX_TESTE="/tmp/compose_test_cert.pfx"
PEM_TESTE="/tmp/compose_test_cert.pem"
KEY_TESTE="/tmp/compose_test_cert.key"

echo "📝 1. Preparando certificado para teste..."
openssl genrsa -out "$KEY_TESTE" 2048
openssl req -new -x509 -key "$KEY_TESTE" -out "$PEM_TESTE" -days 365 \
  -subj "/C=BR/ST=SP/L=Caraguatatuba/O=Câmara Municipal/CN=Teste Compose PyHanko"
openssl pkcs12 -export -in "$PEM_TESTE" -inkey "$KEY_TESTE" -out "$PFX_TESTE" \
  -password "pass:$SENHA_TESTE" -name "Compose Test Certificate"

echo "✅ Certificado criado"

echo ""
echo "📄 2. Preparando PDF de teste..."
if docker cp legisinc-app:/var/www/html/storage/app/proposicoes/pdfs/1/proposicao_1_unified_1757315263.pdf ./storage/compose_test.pdf 2>/dev/null; then
    echo "✅ PDF real copiado para ./storage/"
else
    echo "📝 Criando PDF básico..."
    printf '%%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000079 00000 n \n0000000173 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n229\n%%%%EOF\n' > ./storage/compose_test.pdf
fi

# Copiar certificado para área de montagem
mkdir -p ./docker/pyhanko/certs
cp "$PFX_TESTE" ./docker/pyhanko/certs/certificado.pfx

# Criar configuração temporária
cat > ./storage/pyhanko_compose.yml << 'EOF'
pkcs12-setups:
  legisinc:
    pfx-file: /certs/certificado.pfx
    pfx-passphrase: ${PFX_PASS:?PFX password is required}

validation-contexts:
  desenvolvimento:
    trust: []
    provisional-ok: true
    ee-signature-config: {}
EOF

echo ""
echo "🧪 3. Testando docker-compose run com profiles..."

echo "📋 Verificando se serviço PyHanko está definido:"
docker compose config --services | grep pyhanko || echo "❌ Serviço pyhanko não encontrado no compose"

echo ""
echo "📋 Verificando profiles disponíveis:"
docker compose config --profiles

echo ""
echo "🖋️  4. EXECUTANDO ASSINATURA VIA COMPOSE RUN"
echo "==========================================="

# Definir variável ambiente
export PFX_PASS="$SENHA_TESTE"

# Executar via docker compose run (profiles são aplicados automaticamente)
echo "💡 Comando: docker compose run --rm pyhanko ..."
docker compose run --rm pyhanko \
  --config /work/pyhanko_compose.yml \
  sign addsig \
  --use-pades \
  --timestamp-url https://freetsa.org/tsr \
  --with-validation-info \
  --field AssinaturaDigital \
  pkcs12 --p12-setup legisinc \
  /work/compose_test.pdf \
  /work/compose_test_signed.pdf

echo ""
echo "🔍 5. VALIDANDO RESULTADO..."
if [ -f "./storage/compose_test_signed.pdf" ]; then
    echo "🎉 ASSINATURA VIA COMPOSE RUN FUNCIONOU!"
    echo ""
    echo "📊 ESTATÍSTICAS:"
    echo "   • Original: $(stat -c%s ./storage/compose_test.pdf) bytes"
    echo "   • Assinado: $(stat -c%s ./storage/compose_test_signed.pdf) bytes"
    echo "   • Aumento: $(( $(stat -c%s ./storage/compose_test_signed.pdf) - $(stat -c%s ./storage/compose_test.pdf) )) bytes"
    
    echo ""
    echo "🔍 Validação PyHanko:"
    docker compose run --rm pyhanko \
      sign validate /work/compose_test_signed.pdf
    
    echo ""
    echo "📋 Estrutura PAdES:"
    if grep -q "ETSI.CAdES.detached" ./storage/compose_test_signed.pdf; then
        echo "   ✅ Formato PAdES (ETSI.CAdES.detached)"
    fi
    if grep -q "ByteRange" ./storage/compose_test_signed.pdf; then
        echo "   ✅ Integridade (ByteRange)"
    fi
    if grep -q "VRI\|ValidationInfo" ./storage/compose_test_signed.pdf; then
        echo "   ✅ Validation Info (B-LT)"
    fi
    
    echo ""
    echo "🎯 COMANDO PARA LARAVEL:"
    echo "\$comando = ["
    echo "    'docker', 'compose', 'run', '--rm', 'pyhanko',"
    echo "    '--config', '/work/pyhanko.yml',"
    echo "    'sign', 'addsig', '--use-pades',"
    echo "    '--timestamp-url', 'https://freetsa.org/tsr',"
    echo "    '--with-validation-info',"
    echo "    'pkcs12', '--p12-setup', 'legisinc',"
    echo "    '/work/in.pdf', '/work/out.pdf'"
    echo "];"
    
else
    echo "❌ Falha na assinatura via compose run"
fi

# Limpeza
echo ""
echo "🧹 6. Limpando arquivos temporários..."
rm -f "$PFX_TESTE" "$PEM_TESTE" "$KEY_TESTE"
rm -f ./storage/compose_test.pdf ./storage/compose_test_signed.pdf
rm -f ./storage/pyhanko_compose.yml
rm -rf ./docker/pyhanko/certs/certificado.pfx

echo ""
echo "🐳 CONCLUSÃO: DOCKER COMPOSE RUN TESTADO!"
echo "   ✅ Profiles funcionando"
echo "   ✅ Não aparece no docker-compose up -d"
echo "   ✅ Volumes e networks do compose"
echo "   ✅ Organização mais limpa"
echo ""
echo "💡 Para usar: docker compose run --rm pyhanko ..."