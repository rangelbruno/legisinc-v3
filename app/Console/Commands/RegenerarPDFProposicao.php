<?php

namespace App\Console\Commands;

use App\Http\Controllers\ProposicaoAssinaturaController;
use App\Models\Proposicao;
use Illuminate\Console\Command;

class RegenerarPDFProposicao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proposicao:regenerar-pdf {id : ID da proposição}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerar PDF de uma proposição com número do protocolo e assinatura';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $proposicaoId = $this->argument('id');

        $this->info("Regenerando PDF para proposição ID: {$proposicaoId}");

        try {
            // Buscar proposição
            $proposicao = Proposicao::find($proposicaoId);

            if (! $proposicao) {
                $this->error("Proposição com ID {$proposicaoId} não encontrada!");

                return 1;
            }

            $this->info('Proposição encontrada:');
            $this->info("- Tipo: {$proposicao->tipo}");
            $this->info("- Status: {$proposicao->status}");
            $this->info('- Protocolo: '.($proposicao->numero_protocolo ?? 'N/A'));
            $this->info('- Assinatura: '.($proposicao->assinatura_digital ? 'Sim' : 'Não'));

            // Regenerar PDF
            $this->info('Iniciando regeneração do PDF...');
            $assinaturaController = app(ProposicaoAssinaturaController::class);
            $assinaturaController->regenerarPDFAtualizado($proposicao);

            $this->info('✅ PDF regenerado com sucesso!');
            $this->info('Caminho: '.($proposicao->arquivo_pdf_path ?? 'N/A'));

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erro ao regenerar PDF: '.$e->getMessage());
            $this->error('Stack trace: '.$e->getTraceAsString());

            return 1;
        }
    }
}
