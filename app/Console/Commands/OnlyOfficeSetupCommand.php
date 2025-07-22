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
        
        // Pular verificação quando executando dentro do container
        if (getenv('CONTAINER') !== false || file_exists('/.dockerenv')) {
            $this->info('✅ Executando dentro do container Docker');
            return;
        }
        
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
