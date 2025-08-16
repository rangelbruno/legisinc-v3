✅ 1. Rodar do zero (limpa o banco + recria + roda seeders)

docker exec -it legisinc-app php artisan migrate:fresh --seed


Principais (destacadas em azul):
  - ${assinatura_digital_info} - Bloco completo da assinatura digital
  - ${qrcode_html} - QR Code para consulta do documento

  Configuráveis:
  - ${assinatura_posicao} - Posição da assinatura (centro, direita, esquerda)
  - ${assinatura_texto} - Texto da assinatura digital
  - ${qrcode_posicao} - Posição do QR Code (centro, direita, esquerda)
  - ${qrcode_texto} - Texto do QR Code
  - ${qrcode_tamanho} - Tamanho do QR Code em pixels
  - ${qrcode_url_formato} - URL de consulta formatada

  🚀 Como usar:

  1. Acesse http://localhost:8001/admin/templates
  2. Escolha um tipo de proposição e clique em "Editar Template"
  3. No painel lateral "Variáveis Disponíveis", procure pela seção "ASSINATURA DIGITAL & QR CODE"
  4. Clique nas variáveis para copiá-las
  5. Use Ctrl+V para colar no documento