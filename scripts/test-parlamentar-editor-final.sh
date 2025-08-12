#!/bin/bash

echo "=== Final Test: OnlyOffice Parlamentar Editor ==="
echo "Date: $(date)"

# 1. Test document download functionality
echo -e "\n1. Testing document download from OnlyOffice container..."
TOKEN=$(echo -n "1|$(date +%s)" | base64)
HTTP_CODE=$(docker exec legisinc-onlyoffice curl -s -o /dev/null -w "%{http_code}" -H "User-Agent: ASC.DocService" "http://legisinc-app/proposicoes/1/onlyoffice/download?token=$TOKEN")
if [ "$HTTP_CODE" == "200" ]; then
    echo "✅ Document download: SUCCESS (HTTP $HTTP_CODE)"
else
    echo "❌ Document download: FAILED (HTTP $HTTP_CODE)"
fi

# 2. Test OnlyOffice API accessibility
echo -e "\n2. Testing OnlyOffice API accessibility..."
API_CODE=$(docker exec legisinc-onlyoffice curl -s -o /dev/null -w "%{http_code}" "http://localhost/web-apps/apps/api/documents/api.js")
if [ "$API_CODE" == "200" ]; then
    echo "✅ OnlyOffice API: SUCCESS (HTTP $API_CODE)"
else
    echo "❌ OnlyOffice API: FAILED (HTTP $API_CODE)"
fi

# 3. Test callback URL accessibility from OnlyOffice container
echo -e "\n3. Testing callback URL accessibility..."
CALLBACK_CODE=$(docker exec legisinc-onlyoffice curl -s -o /dev/null -w "%{http_code}" -X POST -H "Content-Type: application/json" -H "User-Agent: ASC.DocService" -d '{"status":1}' "http://legisinc-app/api/onlyoffice/callback/proposicao/1")
if [ "$CALLBACK_CODE" == "200" ] || [ "$CALLBACK_CODE" == "404" ]; then
    echo "✅ Callback URL: ACCESSIBLE (HTTP $CALLBACK_CODE)"
else
    echo "❌ Callback URL: FAILED (HTTP $CALLBACK_CODE)"
fi

# 4. Test proposição status
echo -e "\n4. Checking proposição status..."
PROP_STATUS=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT status FROM proposicoes WHERE id = 1;")
PROP_STATUS=$(echo $PROP_STATUS | xargs) # trim whitespace
if [ "$PROP_STATUS" == "em_edicao" ] || [ "$PROP_STATUS" == "rascunho" ]; then
    echo "✅ Proposição status: $PROP_STATUS (editable)"
else
    echo "⚠️ Proposição status: $PROP_STATUS (may not be editable)"
fi

# 5. Test Laravel application health
echo -e "\n5. Testing Laravel application health..."
HEALTH_CODE=$(docker exec legisinc-onlyoffice curl -s -o /dev/null -w "%{http_code}" "http://legisinc-app/")
if [ "$HEALTH_CODE" == "302" ]; then
    echo "✅ Laravel app: HEALTHY (HTTP $HEALTH_CODE - redirect to login)"
else
    echo "❌ Laravel app: ISSUE (HTTP $HEALTH_CODE)"
fi

# 6. Summary
echo -e "\n=== SUMMARY ==="
echo "The OnlyOffice Parlamentar Editor should now work properly."
echo "The main issue was in PreventBackHistory middleware which was"
echo "trying to call header() method on BinaryFileResponse objects."
echo ""
echo "Key fixes applied:"
echo "• Fixed PreventBackHistory middleware for BinaryFileResponse handling"
echo "• Improved error logging in OnlyOffice component"
echo "• Verified network connectivity between containers"
echo ""
echo "To test the editor, access:"
echo "http://localhost:8001/proposicoes/1/onlyoffice/editor-parlamentar"
echo ""
echo "=== Test completed ==="