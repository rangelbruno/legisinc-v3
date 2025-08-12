#!/bin/bash

echo "🧪 Testando salvamento do formulário dados gerais..."

# Test POST request to save identificacao tab
curl -X POST http://localhost:8001/parametros-dados-gerais-camara \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d "save_tab=identificacao&nome_camara=Teste Câmara&sigla_camara=TC&cnpj=12.345.678/0001-90&_token=test"

echo ""
echo "✅ Teste concluído. Verifique os logs para debug."