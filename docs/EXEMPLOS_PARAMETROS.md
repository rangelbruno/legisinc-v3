# Exemplos Práticos de Uso dos Parâmetros - LegisInc

## 🎯 Parâmetros de Exemplo Criados

Este documento mostra como usar os parâmetros de exemplo criados pelo `ParametroExemploSeeder`.

## 📋 Lista de Parâmetros Criados

### Sistema
- `sistema.mensagem_boas_vindas` - Mensagem da tela inicial
- `sistema.limite_projetos_parlamentar` - Limite de projetos por parlamentar
- `sistema.permitir_edicao_protocolados` - Permitir edição de projetos protocolados

### Legislativo
- `legislativo.horario_inicio_sessao` - Horário padrão das sessões
- `legislativo.quorum_minimo_votacao` - Quórum mínimo para votações
- `legislativo.tipos_projeto_permitidos` - Tipos de projetos permitidos (JSON)

### Notificações
- `notificacoes.email_sistema` - E-mail do sistema
- `notificacoes.notificar_alteracoes_projetos` - Notificar alterações

### Segurança
- `seguranca.tempo_sessao` - Tempo de sessão em minutos
- `seguranca.url_autenticacao` - URL de autenticação

### Interface
- `interface.cor_primaria` - Cor principal do sistema

## 💡 Exemplos de Uso no Código

### 1. Exibir Mensagem de Boas-vindas no Dashboard

```php
// resources/views/dashboard.blade.php
<div class="alert alert-info">
    <i class="ki-duotone ki-information-5 fs-2x text-primary me-3"></i>
    <div class="d-flex flex-column">
        <h4>{{ parametro('sistema.mensagem_boas_vindas', 'Bem-vindo!') }}</h4>
        <span>Sistema versão {{ parametro('sistema.versao', '1.0') }}</span>
    </div>
</div>
```

### 2. Verificar Limite de Projetos

```php
// app/Http/Controllers/Projeto/ProjetoController.php
public function store(Request $request)
{
    $user = auth()->user();
    $limite = (int) parametro('sistema.limite_projetos_parlamentar', 25);
    
    // Contar projetos ativos do usuário
    $projetosAtivos = $user->projetos()->where('status', 'ativo')->count();
    
    if ($projetosAtivos >= $limite) {
        return redirect()->back()->with('error', 
            "Você atingiu o limite de {$limite} projetos simultâneos."
        );
    }
    
    // Continuar com a criação do projeto...
}
```

### 3. Configurar Horário de Sessões

```php
// app/Models/Sessao.php
public function getHorarioInicioAttribute()
{
    return $this->attributes['horario_inicio'] ?? 
           parametro('legislativo.horario_inicio_sessao', '14:00');
}

// app/Http/Controllers/Session/SessionController.php
public function create()
{
    $horarioPadrao = parametro('legislativo.horario_inicio_sessao', '14:00');
    
    return view('admin.sessions.create', compact('horarioPadrao'));
}
```

### 4. Verificar Quórum para Votação

```php
// app/Services/VotacaoService.php
public function podeIniciarVotacao(Sessao $sessao): bool
{
    $quorumMinimo = (int) parametro('legislativo.quorum_minimo_votacao', 10);
    $presentes = $sessao->parlamentares_presentes()->count();
    
    return $presentes >= $quorumMinimo;
}

// Em uma view Blade
@if(app('App\Services\VotacaoService')->podeIniciarVotacao($sessao))
    <button class="btn btn-primary">Iniciar Votação</button>
@else
    <div class="alert alert-warning">
        Quórum insuficiente. Mínimo necessário: 
        {{ parametro('legislativo.quorum_minimo_votacao', 10) }} parlamentares
    </div>
@endif
```

### 5. Usar Tipos de Projetos Permitidos (JSON)

```php
// app/Http/Controllers/Projeto/ProjetoController.php
public function create()
{
    $tiposPermitidos = parametro('legislativo.tipos_projeto_permitidos', 
        '["Lei Ordinária", "Resolução", "Requerimento"]'
    );
    
    $tipos = json_decode($tiposPermitidos, true);
    
    return view('projetos.create', compact('tipos'));
}

// Em um formulário Blade
<select name="tipo_projeto" class="form-select">
    @php
        $tipos = json_decode(parametro('legislativo.tipos_projeto_permitidos', '[]'), true);
    @endphp
    @foreach($tipos as $tipo)
        <option value="{{ $tipo }}">{{ $tipo }}</option>
    @endforeach
</select>
```

### 6. Configurar E-mail de Notificações

```php
// app/Notifications/ProjetoAlterado.php
public function via($notifiable)
{
    $notificarPorEmail = parametro('notificacoes.notificar_alteracoes_projetos', 'true');
    
    return filter_var($notificarPorEmail, FILTER_VALIDATE_BOOLEAN) ? ['mail'] : [];
}

// app/Mail/NotificacaoSistema.php
public function build()
{
    return $this->from(parametro('notificacoes.email_sistema', 'noreply@sistema.gov.br'))
                ->subject('Notificação do Sistema LegisInc')
                ->view('emails.notificacao');
}
```

### 7. Configurar Tempo de Sessão

```php
// config/session.php
'lifetime' => (int) parametro('seguranca.tempo_sessao', 120),

// Ou em um middleware
public function handle(Request $request, Closure $next)
{
    $tempoSessao = (int) parametro('seguranca.tempo_sessao', 120);
    $ultimaAtividade = session('ultima_atividade', now());
    
    if (now()->diffInMinutes($ultimaAtividade) > $tempoSessao) {
        auth()->logout();
        return redirect()->route('login')->with('message', 'Sessão expirada');
    }
    
    session(['ultima_atividade' => now()]);
    
    return $next($request);
}
```

### 8. Aplicar Cor Primária na Interface

```php
// resources/views/layouts/app.blade.php
<style>
    :root {
        --primary-color: {{ parametro('interface.cor_primaria', '#007BFF') }};
        --primary-rgb: {{ hex2rgb(parametro('interface.cor_primaria', '#007BFF')) }};
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .text-primary {
        color: var(--primary-color) !important;
    }
</style>

{{-- Helper para converter hex para RGB --}}
@php
function hex2rgb($hex) {
    $hex = str_replace('#', '', $hex);
    return implode(',', [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    ]);
}
@endphp
```

### 9. Verificar Permissão de Edição

```php
// app/Http/Controllers/Projeto/ProjetoController.php
public function edit(Projeto $projeto)
{
    $permitirEdicao = parametro('sistema.permitir_edicao_protocolados', 'false');
    
    if ($projeto->status === 'protocolado' && !filter_var($permitirEdicao, FILTER_VALIDATE_BOOLEAN)) {
        return redirect()->back()->with('error', 
            'Projetos protocolados não podem ser editados.'
        );
    }
    
    return view('projetos.edit', compact('projeto'));
}
```

### 10. Middleware de Autenticação Customizado

```php
// app/Http/Middleware/CheckAuthentication.php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check()) {
        $urlAuth = parametro('seguranca.url_autenticacao', '/login');
        return redirect($urlAuth);
    }
    
    return $next($request);
}
```

## 🔧 Helpers Personalizados

Você pode criar helpers específicos para seus parâmetros:

```php
// app/helpers.php

if (!function_exists('mensagem_boas_vindas')) {
    function mensagem_boas_vindas(): string
    {
        return parametro('sistema.mensagem_boas_vindas', 'Bem-vindo ao Sistema!');
    }
}

if (!function_exists('limite_projetos')) {
    function limite_projetos(): int
    {
        return (int) parametro('sistema.limite_projetos_parlamentar', 25);
    }
}

if (!function_exists('horario_sessao')) {
    function horario_sessao(): string
    {
        return parametro('legislativo.horario_inicio_sessao', '14:00');
    }
}

if (!function_exists('email_sistema')) {
    function email_sistema(): string
    {
        return parametro('notificacoes.email_sistema', 'noreply@sistema.gov.br');
    }
}

if (!function_exists('cor_primaria')) {
    function cor_primaria(): string
    {
        return parametro('interface.cor_primaria', '#007BFF');
    }
}
```

## 🎨 Exemplo de Uso em Blade

```blade
{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Mensagem de boas-vindas --}}
    <div class="alert alert-info mb-4">
        <h4>{{ mensagem_boas_vindas() }}</h4>
        <small>Sistema versão {{ parametro('sistema.versao', '1.0') }}</small>
    </div>
    
    {{-- Informações do usuário --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: {{ cor_primaria() }}">
                    <h5 class="text-white">Seus Projetos</h5>
                </div>
                <div class="card-body">
                    <p>Limite: {{ limite_projetos() }} projetos</p>
                    <p>Seus projetos ativos: {{ auth()->user()->projetos()->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Próxima Sessão</h5>
                </div>
                <div class="card-body">
                    <p>Horário padrão: {{ horario_sessao() }}</p>
                    <p>Quórum mínimo: {{ parametro('legislativo.quorum_minimo_votacao', 10) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

## 📊 Comandos Úteis

```bash
# Executar seeder de exemplo
php artisan db:seed --class=ParametroExemploSeeder

# Testar parâmetros no Tinker
php artisan tinker
>>> parametro('sistema.mensagem_boas_vindas')
>>> parametro('sistema.limite_projetos_parlamentar')
>>> parametro('legislativo.horario_inicio_sessao')

# Verificar total de parâmetros
>>> \App\Models\Parametro::count()

# Listar parâmetros por grupo
>>> \App\Models\Parametro::whereHas('grupo', function($q) { 
        $q->where('codigo', 'sistema'); 
    })->get(['codigo', 'valor']);
```

## 🔄 Fluxo de Desenvolvimento

1. **Identifique** valores que precisam ser configuráveis
2. **Crie** o parâmetro via interface ou seeder
3. **Substitua** valores hardcoded por `parametro()`
4. **Teste** com diferentes valores
5. **Documente** o uso do parâmetro

## 🎯 Casos de Uso Avançados

### Configuração Condicional

```php
// Comportamento baseado em parâmetros
if (parametro('sistema.permitir_edicao_protocolados', 'false') === 'true') {
    $projeto->update($request->all());
} else {
    throw new Exception('Edição não permitida');
}
```

### Validação Dinâmica

```php
// Validação baseada em parâmetros
$rules = [
    'titulo' => 'required|max:255',
    'projetos' => 'max:' . parametro('sistema.limite_projetos_parlamentar', 25)
];
```

### Cache de Parâmetros

```php
// O sistema já faz cache automaticamente
$valor = parametro('sistema.mensagem_boas_vindas'); // Primeira chamada: DB
$valor = parametro('sistema.mensagem_boas_vindas'); // Segunda chamada: Cache
```

---

## 🚀 Como Executar

Para aplicar os parâmetros de exemplo:

```bash
# Executar o seeder
docker exec legisinc-app php artisan db:seed --class=ParametroExemploSeeder

# Limpar cache
docker exec legisinc-app php artisan cache:clear

# Testar parâmetros
docker exec legisinc-app php artisan tinker --execute="echo parametro('sistema.mensagem_boas_vindas')"
```

Agora você tem **11 parâmetros de exemplo** prontos para usar e testar no sistema! 🎉