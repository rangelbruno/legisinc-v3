# Implementação ONLYOFFICE - Guia Passo a Passo

## Fase 1: Setup do Ambiente Docker (PRIORIDADE ALTA)

### 1.1 Atualizar docker-compose.yml

```yaml
# docker-compose.yml - Adicionar ao arquivo existente
version: '3.8'

services:
  # Serviços existentes do LegisInc...
  
  onlyoffice-documentserver:
    image: onlyoffice/documentserver:8.0
    container_name: legisinc-onlyoffice
    restart: unless-stopped
    environment:
      - JWT_ENABLED=true
      - JWT_SECRET=${ONLYOFFICE_JWT_SECRET:-MySecretKey123}
      - JWT_HEADER=Authorization
      - JWT_IN_BODY=true
      - WOPI_ENABLED=false
      - USE_UNAUTHORIZED_STORAGE=false
      - DB_TYPE=postgres
      - DB_HOST=postgres
      - DB_PORT=5432
      - DB_NAME=${DB_DATABASE}
      - DB_USER=${DB_USERNAME}
      - DB_PWD=${DB_PASSWORD}
      - REDIS_SERVER_HOST=redis
      - REDIS_SERVER_PORT=6379
    ports:
      - "8080:80"
    volumes:
      - onlyoffice_data:/var/www/onlyoffice/Data
      - onlyoffice_logs:/var/log/onlyoffice
      - onlyoffice_cache:/var/lib/onlyoffice/documentserver/App_Data/cache/files
      - onlyoffice_forgotten:/var/lib/onlyoffice/documentserver/App_Data/cache/forgotten
      - ./storage/app/public:/var/www/onlyoffice/Data/public
    networks:
      - legisinc-network
    depends_on:
      - postgres
      - redis

  # Adicionar Redis se não existir
  redis:
    image: redis:7-alpine
    container_name: legisinc-redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - legisinc-network

volumes:
  onlyoffice_data:
  onlyoffice_logs:
  onlyoffice_cache:
  onlyoffice_forgotten:
  redis_data:

networks:
  legisinc-network:
    driver: bridge
```

### 1.2 Atualizar .env

```env
# .env - Adicionar configurações ONLYOFFICE
ONLYOFFICE_SERVER_URL=http://localhost:8080
ONLYOFFICE_JWT_SECRET=MySecretKey123ChangeMeInProduction
ONLYOFFICE_STORAGE_PATH=/storage/onlyoffice
ONLYOFFICE_CALLBACK_URL=http://localhost:8000/api/onlyoffice/callback

# Redis para cache (se não existir)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 1.3 Atualizar Makefile

```makefile
# Makefile - Adicionar comandos ONLYOFFICE
.PHONY: onlyoffice-up onlyoffice-down onlyoffice-logs onlyoffice-restart onlyoffice-test

onlyoffice-up:
	docker-compose up -d onlyoffice-documentserver redis
	@echo "Aguardando ONLYOFFICE inicializar..."
	@sleep 30
	@echo "ONLYOFFICE Document Server disponível em http://localhost:8080"
	@echo "Testando conexão..."
	@make onlyoffice-test

onlyoffice-down:
	docker-compose stop onlyoffice-documentserver redis

onlyoffice-logs:
	docker-compose logs -f onlyoffice-documentserver

onlyoffice-restart:
	docker-compose restart onlyoffice-documentserver
	@sleep 20
	@make onlyoffice-test

onlyoffice-test:
	@echo "Testando ONLYOFFICE..."
	@curl -s http://localhost:8080/healthcheck && echo "✅ ONLYOFFICE está funcionando" || echo "❌ ONLYOFFICE não está respondendo"

# Comando completo para desenvolvimento com ONLYOFFICE
dev-with-onlyoffice: dev-setup onlyoffice-up
	@echo "🚀 Ambiente completo iniciado com ONLYOFFICE"
	@echo "📝 Editor: http://localhost:8080"
	@echo "🌐 LegisInc: http://localhost:8000"

# Logs combinados
logs-all:
	docker-compose logs -f app onlyoffice-documentserver
```

## Fase 2: Configuração Laravel (IMPLEMENTAR AGORA)

### 2.1 Configuração Principal

```php
// config/onlyoffice.php
<?php

return [
    'server_url' => env('ONLYOFFICE_SERVER_URL', 'http://localhost:8080'),
    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET'),
    'storage_path' => env('ONLYOFFICE_STORAGE_PATH', 'storage/onlyoffice'),
    'callback_url' => env('ONLYOFFICE_CALLBACK_URL'),
    
    'document_types' => [
        'text' => ['docx', 'doc', 'odt', 'rtf', 'txt'],
        'spreadsheet' => ['xlsx', 'xls', 'ods', 'csv'],
        'presentation' => ['pptx', 'ppt', 'odp']
    ],
    
    'default_permissions' => [
        'comment' => true,
        'copy' => true,
        'download' => true,
        'edit' => true,
        'fillForms' => true,
        'modifyFilter' => true,
        'modifyContentControl' => true,
        'review' => true,
        'chat' => true,
    ],
    
    'user_groups' => [
        'admin' => 'administrators',
        'legislativo' => 'legislative',
        'parlamentar' => 'parliamentarians',
        'assessor' => 'assistants'
    ]
];
```

### 2.2 Service Provider

```php
// app/Providers/OnlyOfficeServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OnlyOffice\OnlyOfficeService;

class OnlyOfficeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OnlyOfficeService::class, function ($app) {
            return new OnlyOfficeService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/onlyoffice.php' => config_path('onlyoffice.php'),
        ], 'onlyoffice-config');

        // Criar diretórios necessários
        $storagePath = storage_path('app/onlyoffice');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
    }
}
```

### 2.3 Adicionar ao config/app.php

```php
// config/app.php
'providers' => [
    // ... outros providers
    App\Providers\OnlyOfficeServiceProvider::class,
],
```

## Fase 3: Migrations do Banco de Dados

### 3.1 Migration: Documento Modelos

```php
// database/migrations/2025_01_21_000001_create_documento_modelos_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documento_modelos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('tipo_proposicao_id')->nullable()->constrained('tipos_proposicao')->nullOnDelete();
            $table->string('document_key')->unique();
            $table->string('arquivo_path')->nullable();
            $table->string('arquivo_nome');
            $table->bigInteger('arquivo_size')->default(0);
            $table->json('variaveis')->nullable();
            $table->string('versao')->default('1.0');
            $table->string('icon')->nullable();
            $table->boolean('ativo')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['ativo', 'tipo_proposicao_id']);
            $table->index('document_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_modelos');
    }
};
```

### 3.2 Migration: Documento Instâncias

```php
// database/migrations/2025_01_21_000002_create_documento_instancias_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documento_instancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->cascadeOnDelete();
            $table->foreignId('modelo_id')->constrained('documento_modelos')->cascadeOnDelete();
            $table->string('document_key')->unique();
            $table->string('arquivo_path')->nullable();
            $table->string('arquivo_nome');
            $table->enum('status', ['rascunho', 'parlamentar', 'legislativo', 'finalizado'])->default('rascunho');
            $table->integer('versao')->default(1);
            $table->json('metadados')->nullable();
            $table->json('colaboradores')->nullable(); // IDs dos usuários que podem editar
            $table->timestamp('editado_em')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['projeto_id', 'status']);
            $table->index(['status', 'updated_by']);
            $table->index('document_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_instancias');
    }
};
```

### 3.3 Migration: Documento Versões

```php
// database/migrations/2025_01_21_000003_create_documento_versoes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documento_versoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instancia_id')->constrained('documento_instancias')->cascadeOnDelete();
            $table->string('arquivo_path');
            $table->string('arquivo_nome');
            $table->integer('versao');
            $table->text('comentarios')->nullable();
            $table->string('hash_arquivo', 64);
            $table->bigInteger('arquivo_size');
            $table->json('alteracoes')->nullable(); // Resumo das alterações
            $table->foreignId('modificado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at');
            
            $table->index(['instancia_id', 'versao']);
            $table->index(['modificado_por', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_versoes');
    }
};
```

### 3.4 Migration: Documento Colaboradores

```php
// database/migrations/2025_01_21_000004_create_documento_colaboradores_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documento_colaboradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instancia_id')->constrained('documento_instancias')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('permissao', ['view', 'comment', 'edit', 'admin'])->default('view');
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_acesso')->nullable();
            $table->foreignId('adicionado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['instancia_id', 'user_id']);
            $table->index(['user_id', 'ativo']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_colaboradores');
    }
};
```

## Fase 4: Comandos Artisan para Setup

### 4.1 Comando de Setup

```php
// app/Console/Commands/OnlyOfficeSetupCommand.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OnlyOfficeSetupCommand extends Command
{
    protected $signature = 'onlyoffice:setup {--test : Apenas testar conexão}';
    protected $description = 'Configurar e testar ONLYOFFICE Document Server';

    public function handle()
    {
        if ($this->option('test')) {
            return $this->testarConexao();
        }

        $this->info('🚀 Configurando ONLYOFFICE Document Server...');

        // 1. Verificar se Docker está rodando
        $this->verificarDocker();

        // 2. Criar diretórios necessários
        $this->criarDiretorios();

        // 3. Testar conexão
        $this->testarConexao();

        // 4. Criar documento de teste
        $this->criarDocumentoTeste();

        $this->info('✅ ONLYOFFICE configurado com sucesso!');
    }

    private function verificarDocker()
    {
        $this->info('📦 Verificando Docker...');
        
        $result = shell_exec('docker ps --filter "name=legisinc-onlyoffice" --format "{{.Names}}"');
        
        if (empty(trim($result))) {
            $this->error('❌ Container ONLYOFFICE não está rodando');
            $this->info('Execute: make onlyoffice-up');
            exit(1);
        }

        $this->info('✅ Container ONLYOFFICE está rodando');
    }

    private function criarDiretorios()
    {
        $this->info('📁 Criando diretórios...');

        $diretorios = [
            'onlyoffice',
            'onlyoffice/modelos',
            'onlyoffice/instancias',
            'onlyoffice/versoes',
            'onlyoffice/temp',
            'documentos/modelos',
            'documentos/instancias',
            'documentos/versoes',
            'documentos/pdfs'
        ];

        foreach ($diretorios as $dir) {
            Storage::makeDirectory($dir);
            $this->line("  ✓ storage/app/{$dir}");
        }
    }

    private function testarConexao()
    {
        $this->info('🔗 Testando conexão com ONLYOFFICE...');

        $url = config('onlyoffice.server_url');
        
        try {
            $response = Http::timeout(10)->get($url . '/healthcheck');
            
            if ($response->successful()) {
                $this->info("✅ ONLYOFFICE está respondendo em {$url}");
                return true;
            } else {
                $this->error("❌ ONLYOFFICE não está respondendo (Status: {$response->status()})");
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ Erro ao conectar: {$e->getMessage()}");
            return false;
        }
    }

    private function criarDocumentoTeste()
    {
        $this->info('📄 Criando documento de teste...');

        $conteudoTeste = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<w:document xmlns:w=\"http://schemas.openxmlformats.org/wordprocessingml/2006/main\">
    <w:body>
        <w:p>
            <w:r>
                <w:t>Modelo de Teste - LegisInc</w:t>
            </w:r>
        </w:p>
        <w:p>
            <w:r>
                <w:t>Número da Proposição: \${numero_proposicao}</w:t>
            </w:r>
        </w:p>
        <w:p>
            <w:r>
                <w:t>Autor: \${autor_nome}</w:t>
            </w:r>
        </w:p>
        <w:p>
            <w:r>
                <w:t>Data: \${data_atual}</w:t>
            </w:r>
        </w:p>
    </w:body>
</w:document>";

        Storage::put('documentos/modelos/teste.docx', $conteudoTeste);
        $this->info('✅ Documento de teste criado em storage/app/documentos/modelos/teste.docx');
    }
}
```

### 4.2 Registrar o comando

```php
// app/Console/Kernel.php
protected $commands = [
    Commands\OnlyOfficeSetupCommand::class,
];
```

## Fase 5: Testes Básicos

### 5.1 Teste de Feature

```php
// tests/Feature/OnlyOfficeTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class OnlyOfficeTest extends TestCase
{
    use RefreshDatabase;

    public function test_onlyoffice_server_is_accessible()
    {
        Http::fake([
            config('onlyoffice.server_url') . '/healthcheck' => Http::response(['status' => 'ok'], 200)
        ]);

        $response = Http::get(config('onlyoffice.server_url') . '/healthcheck');
        
        $this->assertTrue($response->successful());
    }

    public function test_can_create_document_configuration()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $config = app(\App\Services\OnlyOffice\OnlyOfficeService::class)
            ->criarConfiguracao(
                'test-key',
                'test.docx',
                'http://test.com/file.docx',
                ['id' => $user->id, 'name' => $user->name],
                'edit'
            );

        $this->assertArrayHasKey('document', $config);
        $this->assertArrayHasKey('editorConfig', $config);
        $this->assertEquals('test-key', $config['document']['key']);
    }
}
```

## Comandos de Execução

### Implementar Agora:
```bash
# 1. Atualizar Docker
make onlyoffice-up

# 2. Executar migrations
php artisan migrate

# 3. Configurar ONLYOFFICE
php artisan onlyoffice:setup

# 4. Testar
php artisan test --filter=OnlyOfficeTest

# 5. Ver logs
make onlyoffice-logs
```

### Status Check:
```bash
# Verificar se tudo está funcionando
make onlyoffice-test
curl http://localhost:8080/healthcheck
```

## Próximos Passos

1. **✅ Implementar agora**: Fases 1-3 (Docker + Config + Migrations)
2. **⏳ Em seguida**: Service classes (OnlyOfficeService)
3. **🔄 Depois**: Controllers e Routes
4. **🎨 Final**: Interface do Editor

Qual fase você gostaria de implementar primeiro? Recomendo começar com a **Fase 1 (Docker Setup)** para ter a base funcionando!