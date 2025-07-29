<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposicao;

class DebugProposicao extends Command
{
    protected $signature = 'debug:proposicao {id}';
    protected $description = 'Debug proposição content';

    public function handle()
    {
        $id = $this->argument('id');
        $proposicao = Proposicao::with('autor')->find($id);
        
        if (!$proposicao) {
            $this->error("Proposição não encontrada: {$id}");
            return;
        }
        
        $this->info("=== PROPOSIÇÃO {$id} ===");
        $this->info("Tipo: {$proposicao->tipo}");
        $this->info("Status: {$proposicao->status}");
        $this->info("Autor: " . ($proposicao->autor ? $proposicao->autor->name : 'N/A'));
        $this->info("Ementa: " . ($proposicao->ementa ? substr($proposicao->ementa, 0, 100) . '...' : 'Vazia'));
        
        $this->info("\n=== CONTEÚDO ===");
        if ($proposicao->conteudo) {
            $this->line("Tamanho: " . strlen($proposicao->conteudo) . " caracteres");
            $this->line("Primeiros 200 caracteres:");
            $this->line(substr(strip_tags($proposicao->conteudo), 0, 200) . '...');
        } else {
            $this->warn("CONTEÚDO VAZIO!");
        }
        
        $this->info("\n=== CAMPOS ADICIONAIS ===");
        $this->info("Template ID: " . ($proposicao->template_id ?? 'N/A'));
        $this->info("Arquivo Path: " . ($proposicao->arquivo_path ?? 'N/A'));
        $this->info("Observações Edição: " . ($proposicao->observacoes_edicao ?? 'N/A'));
        $this->info("Última Modificação: " . ($proposicao->ultima_modificacao ?? 'N/A'));
    }
}