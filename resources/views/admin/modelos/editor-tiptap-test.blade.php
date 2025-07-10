<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Teste Editor Tiptap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Teste Editor Tiptap</h1>
    <p>Tipos: {{ json_encode($tipos) }}</p>
    <p>Tipo selecionado: {{ $tipoSelecionado }}</p>
    <p>Modelo: {{ $modelo ? $modelo->id : 'null' }}</p>
</body>
</html>