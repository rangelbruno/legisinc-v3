<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'Editor de Documentos')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Minimal styles for OnlyOffice -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body, html {
            height: 100%;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        
        #onlyoffice-editor {
            width: 100%;
            height: 100vh;
        }
        
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>Carregando editor...</p>
    </div>
    
    @yield('content')

    <script>
        // Hide loading when editor is ready
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('loading').style.display = 'none';
            }, 2000);
        });
    </script>
</body>
</html>