<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação da API - LegisInc</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
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

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
            padding: 10px 20px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .back-button {
            color: var(--text-primary);
            text-decoration: none;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .back-button svg {
            width: 28px;
            height: 28px;
        }

        .header-content {
            flex-grow: 1;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 .emoji {
            font-size: 1.6rem;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.3));
        }

        .header .subtitle {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 400;
            opacity: 0.9;
        }

        .header .badges {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.version {
            background: linear-gradient(135deg, var(--text-success) 0%, #45a049 100%);
            color: var(--bg-primary);
        }

        .badge.api {
            background: linear-gradient(135deg, var(--text-link) 0%, #1e90ff 100%);
            color: var(--bg-primary);
        }

        .badge.date {
            background: linear-gradient(135deg, var(--text-warning) 0%, #ffa500 100%);
            color: var(--bg-primary);
        }

        /* Layout principal */
        .main-layout {
            display: flex;
            min-height: calc(100vh - 140px);
        }

        /* Sidebar */
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

        .nav-menu {
            list-style: none;
        }

        .nav-menu > li {
            margin-bottom: 5px;
        }

        .nav-menu a {
            display: block;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 14px;
        }

        .nav-menu a:hover {
            background: var(--bg-primary);
            border-left-color: var(--text-secondary);
            color: var(--text-secondary);
        }

        .nav-menu a.active {
            background: var(--bg-primary);
            border-left-color: var(--text-accent);
            color: var(--text-accent);
        }

        .nav-menu .nav-section {
            font-weight: 600;
            color: var(--text-secondary);
            padding: 8px 20px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }

        .nav-menu .nav-section:first-child {
            margin-top: 0;
        }

        .nav-menu .nav-subsection {
            padding-left: 40px;
            font-size: 13px;
            color: var(--text-primary);
            opacity: 0.8;
        }

        .nav-menu .nav-subsection:hover {
            opacity: 1;
        }

        /* Conteúdo principal */
        .content {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        /* Scrollbar customizada */
        .sidebar::-webkit-scrollbar,
        .content::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track,
        .content::-webkit-scrollbar-track {
            background: var(--bg-primary);
        }

        .sidebar::-webkit-scrollbar-thumb,
        .content::-webkit-scrollbar-thumb {
            background: var(--bg-tertiary);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .content::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Estilos do conteúdo */
        .content h2 {
            color: var(--text-secondary);
            font-size: 1.8rem;
            margin-bottom: 20px;
            margin-top: 30px;
        }

        .content h2:first-child {
            margin-top: 0;
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

        .content ul {
            margin-bottom: 15px;
            padding-left: 20px;
        }

        .content ul li {
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .content ul li::marker {
            color: var(--text-accent);
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

        /* Code blocks */
        .code-block {
            background: var(--bg-secondary);
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            font-family: 'Fira Code', 'JetBrains Mono', 'Monaco', 'Menlo', monospace;
        }

        .code-header {
            background: var(--bg-tertiary);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            min-height: 45px;
        }

        .code-header .dots {
            display: flex;
            gap: 8px;
        }

        .code-header .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .code-header .dot.red {
            background: var(--text-error);
        }

        .code-header .dot.yellow {
            background: var(--text-warning);
        }

        .code-header .dot.green {
            background: var(--text-success);
        }

        .code-header .language {
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .copy-button {
            background: var(--text-secondary);
            color: var(--bg-primary);
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .copy-button:hover {
            background: var(--text-accent);
            transform: translateY(-1px);
        }

        .copy-button.copied {
            background: var(--text-success);
            transform: scale(0.95);
        }

        pre {
            padding: 20px;
            margin: 0;
            overflow-x: auto;
            background: var(--bg-secondary) !important;
            white-space: pre-wrap;
            word-wrap: break-word;
            min-height: 40px;
            position: relative;
        }

        pre code {
            background: none !important;
            padding: 0 !important;
            font-size: 0.9rem;
            line-height: 1.8;
            display: block;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: 'Fira Code', 'JetBrains Mono', 'Monaco', 'Menlo', monospace;
            color: var(--text-primary);
            min-height: 20px;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        /* Prism.js token fixes */
        .token.string {
            color: var(--text-warning) !important;
        }

        .token.keyword {
            color: var(--text-accent) !important;
        }

        .token.property {
            color: var(--text-link) !important;
        }

        .token.number {
            color: var(--text-secondary) !important;
        }

        .token.comment {
            color: var(--bg-tertiary) !important;
        }

        .token.punctuation {
            color: var(--text-primary) !important;
        }

        .token.function {
            color: var(--text-success) !important;
        }

        .token.boolean {
            color: var(--text-secondary) !important;
        }

        .token.operator {
            color: var(--text-accent) !important;
        }

        /* HTTP specific tokens */
        .language-http .token.request-line {
            color: var(--text-success) !important;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .language-http .token.response-status {
            color: var(--text-link) !important;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .language-http .token.header-name {
            color: var(--text-orange) !important;
            display: inline-block;
            margin-right: 5px;
        }

        .language-http .token.header-value {
            color: var(--text-warning) !important;
            display: inline-block;
        }

        /* Ensure proper spacing for all language code blocks */
        pre[class*="language-"] {
            text-align: left;
            overflow-wrap: break-word;
            word-break: break-word;
            hyphens: none;
        }

        code[class*="language-"] {
            text-align: left;
            overflow-wrap: break-word;
            word-break: break-word;
            hyphens: none;
        }

        /* Específico para HTTP - corrigir sobreposição */
        .language-http {
            tab-size: 4;
        }

        /* Corrigir altura de linha para todo o código HTTP */
        pre.language-http,
        pre.language-http code,
        .language-http * {
            line-height: 2.5 !important;
        }

        /* Garantir que quebras de linha sejam respeitadas */
        .language-http code {
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            display: block !important;
        }

        /* Forçar espaçamento correto para todos os elementos dentro do código HTTP */
        .language-http .token {
            display: inline;
            line-height: 2.5 !important;
            vertical-align: baseline;
            height: auto;
        }

        /* Headers HTTP com espaçamento adequado */
        .language-http .token.header-name:before {
            content: "";
            display: block;
            height: 2px;
        }

        .language-http .token.header-value:after {
            content: "";
            display: block;
            height: 2px;
        }

        /* Força espaçamento entre todas as linhas HTTP */
        pre.language-http {
            padding: 25px 20px !important;
        }

        /* Força altura mínima para cada linha */
        .language-http {
            font-size: 0.9rem !important;
            line-height: 2.8 !important;
        }

        /* Específico para corrigir sobreposição completa */
        pre[class*="language-http"] code {
            line-height: 2.8 !important;
            font-size: 0.9rem !important;
            display: block !important;
        }

        /* Forçar quebra de linha após cada token HTTP */
        .language-http .token + .token {
            margin-top: 3px;
        }

        /* Última tentativa - forçar espaçamento HTTP com !important */
        pre.language-http,
        pre.language-http *,
        code.language-http,
        code.language-http * {
            line-height: 3.0 !important;
            font-size: 0.85rem !important;
        }

        /* Resetar qualquer height que possa estar causando problema */
        .language-http .token {
            height: auto !important;
            min-height: 25px !important;
        }

        /* Se ainda não funcionar, separar cada linha visualmente */
        .language-http {
            word-spacing: 0.1em !important;
            letter-spacing: 0.02em !important;
        }

        /* Forçar que cada linha HTTP seja um bloco separado */
        .language-http br {
            display: block !important;
            margin: 8px 0 !important;
            line-height: 3.0 !important;
            height: 8px !important;
        }

        /* Garantir que não há overlap */
        .language-http .token {
            position: relative !important;
            z-index: 1 !important;
            background: transparent !important;
        }

        /* Forçar espaçamento entre headers */
        .language-http:not(.token) {
            padding: 3px 0 !important;
        }

        /* Solução definitiva - forçar espaçamento com pseudo-elementos */
        .language-http::before {
            content: '';
            display: block;
            height: 5px;
        }

        .language-http::after {
            content: '';
            display: block;
            height: 5px;
        }

        /* Forçar cada linha HTTP a ter espaçamento */
        pre.language-http code {
            white-space: pre-line !important;
            word-spacing: normal !important;
            line-height: 3.2 !important;
        }

        /* Específico para headers HTTP */
        .language-http .token.header-name,
        .language-http .token.header-value {
            display: inline-block !important;
            margin: 2px 0 !important;
        }

        /* Solução final - CSS clean para HTTP */
        pre[class*="language-http"] {
            line-height: 3.5 !important;
            font-size: 0.9rem !important;
            padding: 30px 25px !important;
        }

        pre[class*="language-http"] code {
            line-height: 3.5 !important;
            font-size: 0.9rem !important;
            white-space: pre-wrap !important;
        }

        /* Resetar todos os tokens HTTP */
        pre[class*="language-http"] .token {
            line-height: 3.5 !important;
            font-size: 0.9rem !important;
            display: inline !important;
        }

        /* SOLUÇÃO FINAL: Força absoluta para HTTP */
        pre.language-http,
        pre.language-http code,
        pre.language-http .token,
        pre.language-http *,
        code.language-http,
        code.language-http .token,
        code.language-http * {
            line-height: 4.0 !important;
            font-size: 0.85rem !important;
            margin: 0 !important;
            padding: 0 !important;
            vertical-align: baseline !important;
            white-space: pre-wrap !important;
        }

        /* Espaçamento extra no container HTTP */
        pre.language-http {
            padding: 35px 25px !important;
            min-height: 120px !important;
        }

        /* Search highlights */
        .search-highlight {
            background: var(--text-warning);
            color: var(--bg-primary);
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 600;
        }

        /* Mobile responsivo */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .back-button {
                position: absolute;
                top: 15px;
                right: 15px;
                padding: 6px;
            }

            .back-button svg {
                width: 24px;
                height: 24px;
            }

            .header-content {
                width: 100%;
                margin-top: 10px;
            }
            
            .header h1 {
                font-size: 1.5rem;
                gap: 8px;
            }

            .header h1 .emoji {
                font-size: 1.4rem;
            }

            .header .subtitle {
                font-size: 0.8rem;
                margin-top: 2px;
            }

            .header .badges {
                margin-top: 12px;
                gap: 8px;
                flex-wrap: wrap;
            }

            .badge {
                padding: 5px 10px;
                font-size: 0.75rem;
            }

            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                border-right: none;
                border-bottom: 2px solid var(--border-color);
            }

            .content {
                padding: 20px;
            }

            .mobile-menu-toggle {
                display: block;
                background: var(--text-secondary);
                color: var(--bg-primary);
                border: none;
                padding: 10px;
                margin: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
            }

            .nav-menu {
                display: none;
            }

            .nav-menu.active {
                display: block;
            }
        }

        @media (min-width: 769px) {
            .mobile-menu-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
         <!-- Header -->
     <header class="header">
         <a href="{{ url('/progress') }}" class="back-button" aria-label="Voltar para a página de progresso">
             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                 <path d="M0 0h24v24H0z" fill="none"/>
                 <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
             </svg>
         </a>
         <div class="header-content">
             <h1>
                 <span class="emoji"># 🚀</span>
                 {{ $documentationData['title'] ?? 'Documentação da API - LegisInc' }}
             </h1>
             <p class="subtitle">{{ $documentationData['subtitle'] ?? 'Sistema de Gestão Parlamentar - API REST Completa' }}</p>
             <div class="badges">
                 <span class="badge version">VERSÃO {{ $documentationData['version'] ?? '1.0.0' }}</span>
                 <span class="badge api">`{{ $documentationData['api_version'] ?? '/API/V1' }}`</span>
                 <span class="badge date">{{ $documentationData['date'] ?? '2025-07-12' }}</span>
             </div>
         </div>
     </header>

        <div class="main-layout">
            <aside class="sidebar" id="sidebar">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar endpoints...">
                </div>
                <ul class="nav-menu" id="navMenu">
                    @foreach($documentationData['sections'] as $section)
                        <li>
                            <a href="#{{ $section['id'] }}" class="nav-link">
                                <i class="{{ $section['icon'] }} nav-icon"></i>
                                <span>{{ $section['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <main class="content" id="content">
                {!! $documentationData['content'] !!}
            </main>
        </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script>
        // Generate navigation menu
        function generateNavigation() {
            const content = document.getElementById('content');
            const navMenu = document.getElementById('navMenu');
            const headers = content.querySelectorAll('h2, h3, h4');
            
            navMenu.innerHTML = '';
            
            headers.forEach((header, index) => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                
                // Create unique ID for header
                const headerId = `header-${index}`;
                header.id = headerId;
                
                a.href = `#${headerId}`;
                a.textContent = header.textContent.replace(/^#+\s/, '');
                
                // Add classes based on header level
                if (header.tagName === 'H2') {
                    a.classList.add('nav-section');
                } else if (header.tagName === 'H3') {
                    a.classList.add('nav-subsection');
                } else if (header.tagName === 'H4') {
                    a.classList.add('nav-subsection');
                    a.style.paddingLeft = '60px';
                }
                
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    header.scrollIntoView({ behavior: 'smooth' });
                    updateActiveNavItem(a);
                });
                
                li.appendChild(a);
                navMenu.appendChild(li);
            });
        }
        
        // Update active navigation item
        function updateActiveNavItem(activeItem) {
            document.querySelectorAll('.nav-menu a').forEach(item => {
                item.classList.remove('active');
            });
            activeItem.classList.add('active');
        }
        
        // Search functionality
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            const content = document.getElementById('content');
            
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                clearHighlights();
                
                if (searchTerm.length > 2) {
                    highlightSearchResults(searchTerm);
                }
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    clearHighlights();
                }
            });
        }
        
        // Highlight search results
        function highlightSearchResults(searchTerm) {
            const content = document.getElementById('content');
            const walker = document.createTreeWalker(
                content,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            const textNodes = [];
            let node;
            
            while (node = walker.nextNode()) {
                if (node.parentNode.tagName !== 'SCRIPT' && 
                    node.parentNode.tagName !== 'STYLE' &&
                    !node.parentNode.classList.contains('search-highlight')) {
                    textNodes.push(node);
                }
            }
            
            textNodes.forEach(textNode => {
                const text = textNode.textContent;
                const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
                
                if (regex.test(text)) {
                    const highlightedText = text.replace(regex, '<span class="search-highlight">$1</span>');
                    const wrapper = document.createElement('span');
                    wrapper.innerHTML = highlightedText;
                    textNode.parentNode.replaceChild(wrapper, textNode);
                }
            });
            
            // Scroll to first match
            const firstMatch = content.querySelector('.search-highlight');
            if (firstMatch) {
                firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        // Clear search highlights
        function clearHighlights() {
            const highlights = document.querySelectorAll('.search-highlight');
            highlights.forEach(highlight => {
                const parent = highlight.parentNode;
                parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
                parent.normalize();
            });
        }
        
        // Escape regex special characters
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Copy code functionality
        function setupCopyButtons() {
            const codeBlocks = document.querySelectorAll('pre code');
            
            codeBlocks.forEach(codeBlock => {
                const pre = codeBlock.parentElement;
                const wrapper = document.createElement('div');
                wrapper.className = 'code-block';
                
                const header = document.createElement('div');
                header.className = 'code-header';
                
                const dots = document.createElement('div');
                dots.className = 'dots';
                dots.innerHTML = '<div class="dot red"></div><div class="dot yellow"></div><div class="dot green"></div>';
                
                const language = document.createElement('span');
                language.className = 'language';
                language.textContent = codeBlock.className.replace('language-', '') || 'code';
                
                const copyButton = document.createElement('button');
                copyButton.className = 'copy-button';
                copyButton.textContent = 'Copiar';
                copyButton.onclick = () => copyCode(codeBlock, copyButton);
                
                header.appendChild(dots);
                header.appendChild(language);
                header.appendChild(copyButton);
                
                pre.parentNode.insertBefore(wrapper, pre);
                wrapper.appendChild(header);
                wrapper.appendChild(pre);
            });
        }
        
        // Copy code to clipboard
        function copyCode(codeBlock, button) {
            const text = codeBlock.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                const originalText = button.textContent;
                button.textContent = 'Copiado!';
                button.classList.add('copied');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            }).catch(err => {
                console.error('Erro ao copiar:', err);
                button.textContent = 'Erro!';
                setTimeout(() => {
                    button.textContent = 'Copiar';
                }, 2000);
            });
        }
        
        // Toggle mobile menu
        function toggleMobileMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        }
        
        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            generateNavigation();
            setupSearch();
            setupCopyButtons();
            
            // Set up Prism.js for syntax highlighting
            Prism.highlightAll();
        });
        
        // Scroll spy for active navigation
        window.addEventListener('scroll', () => {
            const headers = document.querySelectorAll('h2, h3, h4');
            const navItems = document.querySelectorAll('.nav-menu a');
            
            let current = '';
            headers.forEach(header => {
                const rect = header.getBoundingClientRect();
                if (rect.top <= 100) {
                    current = header.id;
                }
            });
            
            navItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === `#${current}`) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html> 