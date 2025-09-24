-- Script para criar o parâmetro diretamente no banco

-- Primeiro, vamos verificar se existe o grupo "sistema" ou similar
INSERT INTO grupo_parametros (nome, codigo, descricao, ativo, ordem, created_at, updated_at)
VALUES ('Editor', 'editor', 'Configurações do editor OnlyOffice', true, 100, NOW(), NOW())
ON CONFLICT (codigo) DO NOTHING;

-- Agora inserir o parâmetro
INSERT INTO parametros (
    nome,
    codigo,
    descricao,
    grupo_parametro_id,
    tipo_parametro_id,
    valor,
    valor_padrao,
    obrigatorio,
    editavel,
    visivel,
    ativo,
    ordem,
    help_text,
    created_at,
    updated_at
) VALUES (
    'Exibir Botão Exportar PDF para S3',
    'editor.exibir_botao_exportar_pdf_s3',
    'Controla a exibição do botão de exportar PDF para S3 no editor OnlyOffice',
    (SELECT id FROM grupo_parametros WHERE codigo = 'editor' LIMIT 1),
    1, -- Assumindo que tipo_parametro_id = 1 é boolean
    '1',
    '1',
    false,
    true,
    true,
    true,
    1,
    'Quando habilitado, exibe o botão de exportar PDF no editor OnlyOffice',
    NOW(),
    NOW()
) ON CONFLICT (codigo) DO UPDATE SET
    valor = EXCLUDED.valor,
    updated_at = NOW();