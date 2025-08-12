-- Script para limpar valores duplicados na tabela parametros_valores
-- Mantém apenas o valor mais recente para cada campo

-- Primeiro, vamos expirar todos os valores antigos, exceto o mais recente de cada campo
UPDATE parametros_valores 
SET valido_ate = NOW() 
WHERE id NOT IN (
    SELECT DISTINCT ON (campo_id) id
    FROM parametros_valores 
    WHERE valido_ate IS NULL
    ORDER BY campo_id, created_at DESC
) 
AND valido_ate IS NULL;

-- Verificar quantos valores cada campo tem após a limpeza
SELECT 
    campo_id,
    COUNT(*) as total_valores,
    COUNT(CASE WHEN valido_ate IS NULL THEN 1 END) as valores_ativos,
    MAX(created_at) as ultimo_valor
FROM parametros_valores 
WHERE campo_id IN (51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71)
GROUP BY campo_id 
ORDER BY campo_id;