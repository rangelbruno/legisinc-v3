#!/bin/bash

echo "Testing RTF encoding with accented characters..."

# Create a simple RTF test file with UTF-8 encoding
cat > /tmp/test_utf8.rtf << 'EOF'
{\rtf1\ansi\ansicpg65001\deff0\deflang1046
{\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs32 TESTE DE ACENTUAÇÃO\par}
\par
São Paulo\par
Endereço da Câmara\par
Brasília\par
João\par
\par
}
EOF

echo "Created test RTF file at /tmp/test_utf8.rtf"
echo "Contents:"
cat /tmp/test_utf8.rtf

echo -e "\n\nFile encoding check:"
file /tmp/test_utf8.rtf

echo -e "\n\nHex dump of accented characters:"
hexdump -C /tmp/test_utf8.rtf | grep -A2 -B2 "São\|Endereço\|Brasília\|João"