<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentData['title'] ?? 'Documentação' }} - LegisInc</title>
    <link href="{{ asset('css/documentation.css') }}" rel="stylesheet">
</head>
<body>
    <header class="header">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()">&#9776;</button>
        
        <div class="header-content">
            <h1 id="main-title">{{ $documentData['title'] ?? 'Documentação' }}</h1>
            <p class="subtitle" id="main-subtitle">Centro de Documentação do Sistema</p>
        </div>

        <a href="{{ route('progress.index') }}" class="back-button" aria-label="Voltar para a página de progresso">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
        </a>
    </header>

    <div class="main-layout">
        <aside class="sidebar" id="sidebar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar na documentação...">
            </div>
            
            <nav>
                @foreach($sidebarData as $category => $documents)
                    <div class="category-header">{{ $category }}</div>
                    <ul class="document-list">
                        @foreach($documents as $doc)
                            <li class="document-item">
                                <a href="javascript:void(0)" 
                                   class="document-link {{ $doc['id'] === $documentData['id'] ? 'active' : '' }}"
                                   onclick="loadDocument('{{ $doc['id'] }}')">
                                    <span class="document-icon">{{ $doc['icon'] }}</span>
                                    <div class="document-info">
                                        <div class="document-title">{{ $doc['title'] }}</div>
                                        <div class="document-meta">{{ $doc['last_modified'] }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </nav>
        </aside>

        <main class="content" id="content">
            {!! $documentData['content'] ?? '<p>Nenhum documento selecionado.</p>' !!}
        </main>
    </div>

    <script>
        function loadDocument(docId) {
            var content = document.getElementById('content');
            content.innerHTML = '<div class="loading">Carregando documento...</div>';
            
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/docs/' + docId, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        content.innerHTML = data.data.content;
                        document.getElementById('pageTitle').textContent = data.data.title;
                        
                        // Update active state
                        var links = document.querySelectorAll('.document-link');
                        for (var i = 0; i < links.length; i++) {
                            links[i].classList.remove('active');
                        }
                        
                        var activeLink = document.querySelector('.document-link[onclick*="' + docId + '"]');
                        if (activeLink) {
                            activeLink.classList.add('active');
                        }
                    } else {
                        content.innerHTML = '<div class="error">Erro: ' + data.error + '</div>';
                    }
                } else {
                    content.innerHTML = '<div class="error">Erro de conexão</div>';
                }
            };
            
            xhr.onerror = function() {
                content.innerHTML = '<div class="error">Erro de conexão</div>';
            };
            
            xhr.send();
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Close sidebar when a link is clicked on mobile
            if (window.innerWidth <= 768) {
                document.querySelectorAll('.document-link').forEach(link => {
                    link.addEventListener('click', () => {
                        document.getElementById('sidebar').classList.remove('active');
                    });
                });
            }
        });
    </script>
</body>
</html> 