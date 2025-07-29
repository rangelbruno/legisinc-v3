<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposicao;

class TestOnlyOfficeDownload extends Command
{
    protected $signature = 'onlyoffice:test-download {proposicao_id}';
    protected $description = 'Test OnlyOffice document download';

    public function handle()
    {
        $proposicaoId = $this->argument('proposicao_id');
        $proposicao = Proposicao::find($proposicaoId);
        
        if (!$proposicao) {
            $this->error("Proposição não encontrada: {$proposicaoId}");
            return;
        }
        
        $this->info("Testando download para proposição {$proposicaoId}");
        $this->info("Tipo: {$proposicao->tipo}");
        $this->info("Autor: " . ($proposicao->autor ? $proposicao->autor->name : 'N/A'));
        
        $downloadUrl = route('proposicoes.onlyoffice.download', $proposicao);
        $this->info("URL de download: {$downloadUrl}");
        
        // Testar se a URL é acessível
        $ch = curl_init($downloadUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->info("HTTP Status Code: {$httpCode}");
        
        if ($httpCode === 200) {
            $this->info("✅ URL acessível!");
        } else {
            $this->error("❌ URL não acessível. Verifique as permissões.");
        }
    }
}