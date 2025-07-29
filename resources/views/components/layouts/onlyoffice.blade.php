<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name') }} - @yield('title')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
        }
        
        .editor-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 50px;
            flex-shrink: 0;
        }
        
        .editor-container {
            height: calc(100vh - 50px);
            width: 100%;
        }
        
        .editor-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .editor-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 4px 12px;
            font-size: 12px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Editor Toolbar -->
    <div class="editor-toolbar">
        <div>
            <h6 class="editor-title">@yield('editor-title', 'Editor de Documento')</h6>
        </div>
        <div class="editor-actions">
            @yield('toolbar-actions')
            <a href="@yield('back-url', '/')" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i>Fechar
            </a>
        </div>
    </div>
    
    <!-- Editor Content -->
    <div class="editor-container">
        @yield('content')
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Configurar toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
    
    @stack('scripts')
</body>
</html>