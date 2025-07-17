<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentData['title'] ?? 'Documentação' }} - LegisInc</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #282a36;
            --bg-secondary: #44475a;
            --bg-tertiary: #6272a4;
            --text-primary: #f8f8f2;
            --text-secondary: #bd93f9;
            --text-accent: #ff79c6;
            --text-link: #8be9fd;
            --text-success: #50fa7b;
            --text-warning: #f1fa8c;
            --text-error: #ff5555;
            --text-orange: #ffb86c;
            --border-color: #6272a4;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        body {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
            padding: 20px 30px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-top: 8px;
            font-weight: 400;
            opacity: 0.9;
        }

        .main-layout {
            display: flex;
            min-height: calc(100vh - 140px);
        }

        .sidebar {
            width: 350px;
            background: var(--bg-secondary);
            border-right: 2px solid var(--border-color);
            position: sticky;
            top: 140px;
            height: calc(100vh - 140px);
            overflow-y: auto;
            padding: 20px 0;
        }

        .search-box {
            margin: 0 20px 20px 20px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--text-secondary);
            box-shadow: 0 0 0 3px rgba(189, 147, 249, 0.2);
        }

        .category-header {
            padding: 12px 20px;
            background: var(--bg-primary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-left: 4px solid var(--text-secondary);
            margin-bottom: 8px;
        }

        .document-list {
            list-style: none;
            margin-bottom: 20px;
        }

        .document-item {
            margin-bottom: 2px;
        }

        .document-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 14px;
            cursor: pointer;
        }

        .document-link:hover {
            background: var(--bg-primary);
            border-left-color: var(--text-secondary);
            color: var(--text-secondary);
        }

        .document-link.active {
            background: var(--bg-primary);
            border-left-color: var(--text-accent);
            color: var(--text-accent);
        }

        .document-icon {
            font-size: 1.2em;
            flex-shrink: 0;
        }

        .document-info {
            flex: 1;
        }

        .document-title {
            font-weight: 500;
            margin-bottom: 2px;
        }

        .document-meta {
            font-size: 0.8em;
            color: var(--text-primary);
            opacity: 0.6;
        }

        .content {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        .content h1 {
            color: var(--text-primary);
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .content h2 {
            color: var(--text-secondary);
            font-size: 1.8rem;
            margin-bottom: 20px;
            margin-top: 30px;
        }

        .content h2::before {
            content: "## ";
            color: var(--text-accent);
            font-weight: 700;
        }

        .content h3 {
            color: var(--text-link);
            font-size: 1.4rem;
            margin-bottom: 15px;
            margin-top: 25px;
        }

        .content h3::before {
            content: "### ";
            color: var(--text-accent);
            font-weight: 700;
        }

        .content h4 {
            color: var(--text-warning);
            font-size: 1.2rem;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        .content h4::before {
            content: "#### ";
            color: var(--text-accent);
            font-weight: 700;
        }

        .content p {
            margin-bottom: 15px;
            line-height: 1.7;
        }

        .content ul, .content ol {
            margin-bottom: 15px;
            padding-left: 20px;
        }

        .content ul li, .content ol li {
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .content strong {
            color: var(--text-warning);
            font-weight: 600;
        }

        .content code {
            background: var(--bg-secondary);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
            color: var(--text-success);
        }

        .content pre {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 20px 0;
            position: relative;
        }

        .content pre code {
            background: none;
            padding: 0;
            font-size: 0.9em;
            color: var(--text-primary);
        }

        .content a {
            color: var(--text-link);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .content a:hover {
            color: var(--text-secondary);
            text-decoration: underline;
        }

        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: var(--text-secondary);
        }

        .loading::before {
            content: "⏳ ";
            margin-right: 8px;
        }

        .error {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: var(--text-error);
        }

        .error::before {
            content: "❌ ";
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                max-height: 400px;
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>
            <span>📚</span>
            <span id="pageTitle">{{ $documentData['title'] ?? 'Documentação' }}</span>
        </h1>
        <div class="subtitle">{{ $documentData['filename'] ?? 'docs' }} - Centro de Documentação</div>
    </header>

    <div class="main-layout">
        <aside class="sidebar">
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
    </script>
</body>
</html> 