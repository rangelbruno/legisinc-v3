@extends('components.layouts.app')

@section('title', 'Teste Debug Logger')

@section('content')
<div class="container">
    <h1>Teste do Debug Logger</h1>
    <p>Esta página é para testar o sistema de debug logger.</p>
    
    <div class="alert alert-info">
        <h4>Como testar:</h4>
        <ol>
            <li>Procure o botão flutuante 🔧 no canto inferior direito</li>
            <li>Se estiver vermelho, é o botão fallback (clique para tentar inicializar Vue.js)</li>
            <li>Se estiver azul, Vue.js está funcionando</li>
            <li>Clique no botão para abrir o painel</li>
            <li>Clique em "▶️ Iniciar" para começar a gravar</li>
            <li>Navegue e interaja com a página</li>
            <li>Veja as ações sendo registradas em tempo real</li>
        </ol>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <button class="btn btn-primary">Botão de Teste</button>
            <button class="btn btn-success">Outro Botão</button>
            <button class="btn btn-danger">Botão Danger</button>
        </div>
        <div class="col-md-6">
            <form>
                <input type="text" class="form-control mb-2" placeholder="Campo de teste">
                <button type="submit" class="btn btn-info">Enviar Formulário</button>
            </form>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Status do Debug Logger:</h3>
        <div id="debug-status">Verificando...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusDiv = document.getElementById('debug-status');
    
    // Verificar se Vue está carregado
    if (typeof Vue !== 'undefined') {
        statusDiv.innerHTML = '<span class="text-success">✅ Vue.js carregado</span>';
    } else {
        statusDiv.innerHTML = '<span class="text-danger">❌ Vue.js NÃO carregado</span>';
    }
    
    // Verificar se o elemento debug-logger existe
    const debugElement = document.getElementById('debug-logger');
    if (debugElement) {
        statusDiv.innerHTML += '<br><span class="text-success">✅ Elemento debug-logger encontrado</span>';
    } else {
        statusDiv.innerHTML += '<br><span class="text-danger">❌ Elemento debug-logger NÃO encontrado</span>';
    }
    
    // Verificar se o botão fallback existe
    const fallbackButton = document.getElementById('debug-fallback');
    if (fallbackButton) {
        statusDiv.innerHTML += '<br><span class="text-success">✅ Botão fallback encontrado</span>';
        statusDiv.innerHTML += '<br>Visibilidade do botão: ' + fallbackButton.style.display;
    } else {
        statusDiv.innerHTML += '<br><span class="text-danger">❌ Botão fallback NÃO encontrado</span>';
    }
});
</script>
@endsection