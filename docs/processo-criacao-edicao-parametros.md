# Processo de Criação e Edição de Parâmetros - SGVP Online

## 📋 Visão Geral

Este documento detalha o processo completo de criação e edição de parâmetros no sistema SGVP Online, desde a concepção até a implementação e manutenção.

**Versão:** 1.0
**Última Atualização:** 2024-01-15
**Responsável:** Equipe de Desenvolvimento SGVP

## 🎯 Tipos de Parâmetros

O sistema SGVP possui dois tipos principais de parâmetros:

### 1. **Parâmetros de Configuração Geral**
- **Característica:** Registro único (ID fixo = 1)
- **Operação:** Apenas atualização (PUT)
- **Exemplos:** Dados da Câmara, Configurações de Sessão, Configurações de Painel

### 2. **Parâmetros de Dados Específicos**
- **Característica:** Múltiplos registros
- **Operação:** CRUD completo (Create, Read, Update, Delete)
- **Exemplos:** Tipos de Sessão, Momentos, Autores, Tempo, Documentos

## 🚀 Processo de Criação - Parâmetros de Dados Específicos

### **Passo 1: Planejamento e Análise**

#### 1.1 Definição do Parâmetro
- **Nome do Parâmetro:** Ex: "Tipo de Sessão"
- **Finalidade:** Categorizar diferentes tipos de sessões
- **Campos Necessários:** 
  - `tipoSessao` (string) - Nome do tipo
  - `ativo` (boolean) - Status ativo/inativo
  - `nrSequence` (integer) - Identificador único

#### 1.2 Estrutura da API
- **Endpoint Base:** `/tipoSessao`
- **Operações:**
  - `GET /tipoSessao` - Listar todos
  - `POST /tipoSessao` - Criar novo
  - `PUT /tipoSessao/{id}` - Atualizar
  - `DELETE /tipoSessao/{id}` - Excluir

### **Passo 2: Criação do Controller**

#### 2.1 Estrutura Base do Controller
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ApiSgvp;
use Exception;
use Illuminate\Support\Facades\Log;

class TipoController extends Controller
{
    // Métodos base: index, cadastro, cadastrar, editar, atualizar, excluir
    // Métodos auxiliares: getToken, redirectToLogin
}
```

#### 2.2 Implementação dos Métodos Principais

**Método Index - Listagem**
```php
public function index()
{
    $token = $this->getToken();
    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    try {
        $response = ApiSgvp::withToken($token)->get('/tipoSessao');
        
        return $response->successful()
            ? view('parametrizacao.tipo.index', compact('token'))
            : redirect()->route('parametro.tipo')->withErrors('Erro ao obter dados.');
    } catch (Exception $e) {
        Log::error('Erro ao se conectar à API: ' . $e->getMessage());
        return redirect()->route('parametro.tipo')->withErrors('Erro inesperado.');
    }
}
```

**Método Cadastro - Formulário de Criação**
```php
public function cadastro()
{
    $token = $this->getToken();
    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    return view('parametrizacao.tipo.cadastrar', compact('token'));
}
```

**Método Cadastrar - Processamento da Criação**
```php
public function cadastrar(Request $request)
{
    $token = $this->getToken();
    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    // Preparação dos dados
    $novosDados = [
        'tipoSessao' => $request->input('dto.tipoSessao'),
        'ativo' => $request->input('dto.ativo') ? true : false,
    ];

    try {
        $response = ApiSgvp::withToken($token)->post('/tipoSessao', $novosDados);

        if ($response->successful()) {
            return redirect()->route('parametro.tipo')->with('success', 'Criado com sucesso.');
        } else {
            Log::error('Erro na API: ' . $response->body());
            return redirect()->route('parametro.tipo')->withErrors('Erro ao criar.');
        }
    } catch (Exception $e) {
        Log::error('Erro de conexão: ' . $e->getMessage());
        return redirect()->route('parametro.tipo')->withErrors('Erro inesperado.');
    }
}
```

### **Passo 3: Criação das Rotas**

#### 3.1 Definição das Rotas
```php
// Rotas para Tipo de Sessão
Route::get('/parametros/tipo', [TipoController::class, 'index'])->name('parametro.tipo');
Route::get('/parametros/tipo/cadastro', [TipoController::class, 'cadastro'])->name('parametro.tipo.cadastro');
Route::post('/parametros/tipo/cadastrar', [TipoController::class, 'cadastrar'])->name('parametro.tipo.cadastrar');
Route::get('/parametros/tipo/editar', [TipoController::class, 'editar'])->name('parametro.tipo.editar');
Route::put('/parametros/tipo/{nrSequence}', [TipoController::class, 'atualizar'])->name('parametro.tipo.atualizar');
Route::delete('/parametros/tipo/{nrSequence}', [TipoController::class, 'excluir'])->name('parametro.tipo.excluir');
```

#### 3.2 Padrão de Nomenclatura
- **Listar:** `parametro.{nome}`
- **Formulário:** `parametro.{nome}.cadastro`
- **Criar:** `parametro.{nome}.cadastrar`
- **Editar:** `parametro.{nome}.editar`
- **Atualizar:** `parametro.{nome}.atualizar`
- **Excluir:** `parametro.{nome}.excluir`

### **Passo 4: Criação das Views**

#### 4.1 Estrutura de Diretórios
```
resources/views/parametrizacao/tipo/
├── index.blade.php (Listagem)
├── cadastrar.blade.php (Formulário de criação)
└── editar.blade.php (Formulário de edição)
```

#### 4.2 View Index - Listagem com DataTables
```blade
<x-layouts.app title="Tipos de Sessão" namepage="Tipos de Sessão">
    @push('styles')
        <link href="{{ url('assets/plugins/table/datatable/datatables.css') }}" rel="stylesheet" />
        <!-- Outros estilos necessários -->
    @endpush

    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-one ml-3 mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parametro') }}">Parametrização</a></li>
                    <li class="breadcrumb-item active">Tipo de Sessão</li>
                </ol>
            </nav>
            
            <!-- Botão Cadastrar -->
            <h5 class="mb-3 ml-3 mt-3">
                <a href="{{ route('parametro.tipo.cadastro') }}" class="btn btn-success">CADASTRAR</a>
            </h5>
            
            <!-- Tabela DataTables -->
            <table id="tipoSessaoTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Preenchido via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="{{ url('assets/plugins/table/datatable/datatables.js') }}"></script>
        <script>
            // Configuração DataTables com AJAX
            $('#tipoSessaoTable').DataTable({
                "ajax": {
                    "url": window.config.API_BASE_URL + "tipoSessao",
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{ session('token') }}");
                    }
                },
                "columns": [
                    { data: "nrSequence" },
                    { data: "tipoSessao" },
                    { data: "ativo", render: function(data) {
                        return data ? 'Ativo' : 'Inativo';
                    }},
                    { data: null, render: function(data, type, row) {
                        return '<a href="/parametros/tipo/editar?nrSequence=' + row.nrSequence + '" class="btn btn-primary">Editar</a>' +
                               '<button onclick="excluir(' + row.nrSequence + ')" class="btn btn-danger">Excluir</button>';
                    }}
                ]
            });
        </script>
    @endpush
</x-layouts.app>
```

#### 4.3 View Cadastrar - Formulário de Criação
```blade
<x-layouts.app title="Cadastrar Tipo de Sessão">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area">
                <form action="{{ route('parametro.tipo.cadastrar') }}" method="POST">
                    @include('parametrizacao._partials.form_tipo')
                    <button type="submit" class="btn btn-primary">ENVIAR</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
```

#### 4.4 Partial Form - Formulário Reutilizável
```blade
@csrf
<div class="widget-content widget-content-area">
    <div class="form-row mb-4">
        <div class="form-group col-md-6">
            <label for="inputTipoSessao">TIPO DE SESSÃO</label>
            <input type="text" class="form-control" id="inputTipoSessao" 
                   name="dto[tipoSessao]" 
                   value="{{ old('dto.tipoSessao', $tipoSessao['tipoSessao'] ?? '') }}">
        </div>
    </div>

    <div class="form-row ml-1 mb-2 mt-5">
        <div class="form-group col-md-6">
            <div class="custom-switch">
                <label class="switch-label">ATIVO</label>
                <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                    <input type="checkbox" name="dto[ativo]" 
                           {{ old('dto.ativo', $tipoSessao['ativo'] ?? false) ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
</div>
```

### **Passo 5: Adição ao Menu Principal**

#### 5.1 Atualização da View Principal
```blade
<!-- resources/views/parametrizacao/index.blade.php -->
<div class="col-12 col-xl-6 col-lg-12 mb-xl-5 mb-5">
    <div class="infobox-3">
        <div class="info-icon" style="background-color: #805dca">
            <svg><!-- Ícone SVG --></svg>
        </div>
        <h5 class="info-heading mb-3">TIPO DE SESSÃO</h5>
        <p class="info-text"></p>
        <a href="{{ route('parametro.tipo') }}" class="btn btn-outline-primary">ENTRAR</a>
    </div>
</div>
```

## 🔧 Processo de Edição - Parâmetros de Dados Específicos

### **Passo 1: Método Editar - Carregamento dos Dados**
```php
public function editar(Request $request)
{
    $nrSequence = $request->query('nrSequence');
    $token = $this->getToken();

    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    try {
        $response = ApiSgvp::withToken($token)->get('/tipoSessao?nrSequence=' . $nrSequence);

        if ($response->successful()) {
            $dados = $response->json();
            $tipoSessao = collect($dados)->firstWhere('nrSequence', $nrSequence);

            if ($tipoSessao) {
                return view('parametrizacao.tipo.editar', compact('tipoSessao', 'token'));
            } else {
                return redirect()->route('parametro.tipo')->withErrors('Registro não encontrado.');
            }
        } else {
            Log::error('Erro na API: ' . $response->body());
            return redirect()->route('parametro.tipo')->withErrors('Erro ao buscar dados.');
        }
    } catch (Exception $e) {
        Log::error('Erro de conexão: ' . $e->getMessage());
        return redirect()->route('parametro.tipo')->withErrors('Erro inesperado.');
    }
}
```

### **Passo 2: Método Atualizar - Processamento da Edição**
```php
public function atualizar(Request $request, $nrSequence)
{
    $token = $this->getToken();
    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    // Validação
    $request->validate([
        'dto.tipoSessao' => 'required',
    ]);

    // Preparação dos dados
    $dadosAtualizados = [
        'tipoSessao' => $request->input('dto.tipoSessao'),
        'ativo' => $request->input('dto.ativo') ? true : false,
    ];

    try {
        $response = ApiSgvp::withToken($token)->put("/tipoSessao/{$nrSequence}", $dadosAtualizados);

        if ($response->successful()) {
            return redirect()->route('parametro.tipo')->with('success', 'Atualizado com sucesso.');
        } else {
            Log::error('Erro na API: ' . $response->body());
            return redirect()->route('parametro.tipo')->withErrors('Erro ao atualizar.');
        }
    } catch (Exception $e) {
        Log::error('Erro de conexão: ' . $e->getMessage());
        return redirect()->route('parametro.tipo')->withErrors('Erro inesperado.');
    }
}
```

### **Passo 3: View Editar**
```blade
<x-layouts.app title="Editar Tipo de Sessão">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area">
                <form action="{{ route('parametro.tipo.atualizar', $tipoSessao['nrSequence']) }}" method="POST">
                    @method('PUT')
                    @include('parametrizacao._partials.form_tipo')
                    <button type="submit" class="btn btn-primary">ATUALIZAR</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
```

## 🔄 Processo de Edição - Parâmetros de Configuração Geral

### **Passo 1: Carregamento dos Dados Atuais**
```php
public function index()
{
    $token = $this->getToken();
    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    try {
        $response = ApiSgvp::withToken($token)->get('/camParameter');

        if ($response->successful()) {
            $dados = $response->json();
            $camParameter = $dados[0]; // Sempre o primeiro elemento (ID = 1)

            return view('parametrizacao.dados.index', compact('camParameter', 'token'));
        } else {
            Log::error('Erro na API: ' . $response->body());
            return back()->withErrors('Erro ao buscar dados.');
        }
    } catch (Exception $e) {
        Log::error('Erro de conexão: ' . $e->getMessage());
        return back()->withErrors('Erro inesperado.');
    }
}
```

### **Passo 2: Processamento da Atualização**
```php
public function atualizar(Request $request)
{
    $token = $this->getToken();
    if (!$token) {
        return $this->redirectToLogin('Acesso não autorizado.');
    }

    $dadosAtuais = $this->getDadosAtuais($token);

    // Preparação dos dados (mantém valores existentes para campos não editados)
    $dadosAtualizados = [
        'nomeCamara' => $request->input('nomeCamara'),
        'endereco' => $request->input('endereco'),
        'qtQuorum' => $request->input('qtQuorum'),
        'qtVereadores' => $request->input('qtVereadores'),
        'tempoSessao' => $request->input('tempoSessao'),
        'integracao' => $request->input('integracao'),
        'logoCamara' => $this->tratarUpload($request),
        // Mantém valores existentes para campos não editados
        'relogioOuLogo' => $dadosAtuais[0]['relogioOuLogo'],
        'nomeclaturaVeto' => $dadosAtuais[0]['nomeclaturaVeto'],
        // ... outros campos
    ];

    try {
        // Atualização sempre no registro ID = 1
        $response = ApiSgvp::withToken($token)->put('/camParameter/1', $dadosAtualizados);
        
        if ($response->successful()) {
            return back()->with('success', 'Dados atualizados com sucesso.');
        } else {
            Log::error('Erro na API: ' . $response->body());
            return back()->withErrors('Erro ao atualizar dados.');
        }
    } catch (Exception $e) {
        Log::error('Erro de conexão: ' . $e->getMessage());
        return back()->withErrors('Erro inesperado.');
    }
}
```

## 🛠️ Funcionalidades Especiais

### **1. Upload de Arquivos**
```php
protected function tratarUpload(Request $request)
{
    $dadosAtuais = $this->getDadosAtuais($this->getToken());

    if ($request->hasFile('logoCamara') && $request->file('logoCamara')->isValid()) {
        $imagem = $request->file('logoCamara');
        $nome_arquivo = uniqid() . '.' . $imagem->getClientOriginalExtension();

        // Salva no S3
        $caminho_imagem = $imagem->storeAs('uploads', $nome_arquivo, 's3');
        $caminho_imagem = 'https://sgvp-bucket.s3.amazonaws.com/' . $caminho_imagem;

        return $caminho_imagem;
    }

    return $dadosAtuais[0]['logoCamara']; // Mantém o valor existente
}
```

### **2. Exclusão com Confirmação**
```javascript
function confirmarExclusaoTipoSessao(url) {
    swal({
        title: 'Tem certeza?',
        text: "Você não poderá reverter isso!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'SIM',
        cancelButtonText: 'NÃO',
    }).then(function(result) {
        if (result.value) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
            })
            .then(() => {
                swal('Excluído!', 'O registro foi excluído.', 'success')
                    .then(() => location.reload());
            })
            .catch(() => {
                swal('Erro!', 'Ocorreu um erro ao excluir.', 'error');
            });
        }
    });
}
```

### **3. Switches para Campos Booleanos**
```blade
<label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
    <input type="checkbox" name="dto[ativo]" 
           {{ old('dto.ativo', $registro['ativo'] ?? false) ? 'checked' : '' }}>
    <span class="slider round"></span>
</label>
```

## 🔐 Segurança e Validações

### **1. Controle de Acesso**
```php
private function getToken()
{
    return session('token');
}

private function redirectToLogin($errorMessage)
{
    return redirect()->route('login')->withErrors($errorMessage);
}
```

### **2. Validação de Dados**
```php
$request->validate([
    'dto.tipoSessao' => 'required|string|max:255',
    'dto.ativo' => 'boolean',
]);
```

### **3. Tratamento de Erros**
```php
try {
    $response = ApiSgvp::withToken($token)->post('/endpoint', $dados);
    
    if ($response->successful()) {
        return redirect()->route('parametro.tipo')->with('success', 'Sucesso!');
    } else {
        Log::error('Erro API: ' . $response->body());
        return back()->withErrors('Erro ao processar.');
    }
} catch (Exception $e) {
    Log::error('Erro conexão: ' . $e->getMessage());
    return back()->withErrors('Erro inesperado.');
}
```

## 📊 Padrões e Convenções

### **1. Nomenclatura de Arquivos**
```
Controllers: {Nome}Controller.php
Views: parametrizacao/{nome}/index.blade.php
Partials: _partials/form_{nome}.blade.php
Rotas: parametro.{nome}.{acao}
```

### **2. Estrutura de Dados**
```php
// Entrada do formulário
$dados = [
    'campo1' => $request->input('dto.campo1'),
    'campo2' => $request->input('dto.campo2') ? true : false,
    // Para campos booleanos usar conversão explícita
];
```

### **3. Retornos Padrão**
```php
// Sucesso
return redirect()->route('parametro.nome')->with('success', 'Mensagem de sucesso');

// Erro
return back()->withErrors('Mensagem de erro');

// Erro com log
Log::error('Descrição do erro: ' . $exception->getMessage());
return back()->withErrors('Mensagem amigável');
```

## 🧪 Testes e Validação

### **1. Checklist de Criação**
- [ ] Controller criado com todos os métodos
- [ ] Rotas definidas seguindo padrão
- [ ] Views criadas (index, cadastrar, editar)
- [ ] Formulário partial criado
- [ ] Validações implementadas
- [ ] Tratamento de erros adequado
- [ ] Logs configurados
- [ ] Testes de CRUD funcionais

### **2. Checklist de Edição**
- [ ] Carregamento de dados existentes
- [ ] Preservação de campos não editados
- [ ] Validação de dados
- [ ] Tratamento de upload de arquivos
- [ ] Feedback ao usuário
- [ ] Logs de operações

### **3. Testes Recomendados**
```php
// Teste de criação
1. Acesso sem token
2. Dados válidos
3. Dados inválidos
4. Erro de API
5. Erro de conexão

// Teste de edição
1. Registro existente
2. Registro inexistente
3. Atualização parcial
4. Upload de arquivo
5. Validação de dados
```

## 🔮 Melhorias Futuras

### **1. Funcionalidades Avançadas**
- Cache de dados frequentes
- Versionamento de configurações
- Importação/exportação de parâmetros
- Histórico de alterações

### **2. Interface**
- Validação em tempo real
- Tooltips explicativos
- Agrupamento lógico de campos
- Interface responsiva melhorada

### **3. Performance**
- Lazy loading para listas grandes
- Paginação server-side
- Compressão de dados
- Otimização de consultas

---

## 📚 Referências

- [Sistema de Parametrização - Visão Geral](./parametrizacao-sistema.md)
- [Documentação Laravel](https://laravel.com/docs)
- [DataTables Documentation](https://datatables.net/)
- [SweetAlert2 Documentation](https://sweetalert2.github.io/)

---

**Nota:** Este documento deve ser atualizado sempre que houver modificações no processo de criação ou edição de parâmetros. 