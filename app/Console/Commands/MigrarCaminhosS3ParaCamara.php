<?php

namespace App\Console\Commands;

use App\Models\Proposicao;
use App\Services\CamaraIdentifierService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MigrarCaminhosS3ParaCamara extends Command
{
    protected $signature = 'proposicoes:migrar-s3-camara {--dry-run : Apenas simular sem fazer mudanças} {--force : Forçar migração mesmo se houver riscos}';
    protected $description = 'Migra caminhos S3 antigos para nova estrutura com identificador da câmara';

    protected CamaraIdentifierService $camaraService;

    public function __construct(CamaraIdentifierService $camaraService)
    {
        parent::__construct();
        $this->camaraService = $camaraService;
    }

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Migração de Caminhos S3 para Estrutura com Identificador da Câmara');
        $this->info('=' . str_repeat('=', 70));

        // Obter identificador da câmara
        $camaraIdentifier = $this->camaraService->getFullIdentifier();
        $this->info("📋 Identificador da câmara: {$camaraIdentifier}");

        // Buscar proposições com caminhos antigos
        $proposicoesAntigas = Proposicao::whereNotNull('pdf_s3_path')
            ->where('pdf_s3_path', 'not like', $camaraIdentifier . '/%')
            ->get();

        if ($proposicoesAntigas->isEmpty()) {
            $this->info('✅ Nenhuma proposição com estrutura antiga encontrada!');
            return 0;
        }

        $this->info("📊 Encontradas {$proposicoesAntigas->count()} proposições com estrutura antiga");

        if ($dryRun) {
            $this->warn('🔍 MODO SIMULAÇÃO - Nenhuma mudança será feita');
        }

        $s3Disk = Storage::disk('s3');
        $sucesso = 0;
        $erros = 0;

        foreach ($proposicoesAntigas as $proposicao) {
            $caminhoAntigo = $proposicao->pdf_s3_path;

            // Gerar novo caminho mantendo a estrutura de pastas
            $caminhoNovo = $this->gerarNovoCaminho($caminhoAntigo, $camaraIdentifier);

            $this->info("\n📄 Proposição {$proposicao->id}:");
            $this->line("   🔸 Antigo: {$caminhoAntigo}");
            $this->line("   🔹 Novo:   {$caminhoNovo}");

            try {
                // Verificar se arquivo antigo existe
                if (!$s3Disk->exists($caminhoAntigo)) {
                    $this->warn("   ⚠️  Arquivo antigo não existe no S3");
                    continue;
                }

                // Verificar se novo caminho já existe
                if ($s3Disk->exists($caminhoNovo)) {
                    if (!$force) {
                        $this->error("   ❌ Arquivo novo já existe! Use --force para sobrescrever");
                        $erros++;
                        continue;
                    } else {
                        $this->warn("   ⚠️  Sobrescrevendo arquivo existente (--force ativado)");
                    }
                }

                if (!$dryRun) {
                    // Copiar arquivo para novo local
                    $conteudo = $s3Disk->get($caminhoAntigo);
                    $s3Disk->put($caminhoNovo, $conteudo, [
                        'ContentType' => 'application/pdf',
                        'ACL' => 'private',
                        'Metadata' => [
                            'migrated_from' => $caminhoAntigo,
                            'migration_date' => now()->toISOString(),
                            'camara_identifier' => $camaraIdentifier
                        ]
                    ]);

                    // Atualizar proposição
                    $proposicao->update([
                        'pdf_s3_path' => $caminhoNovo,
                        'pdf_s3_url' => $s3Disk->temporaryUrl($caminhoNovo, now()->addDay())
                    ]);

                    // Remover arquivo antigo
                    $s3Disk->delete($caminhoAntigo);

                    $this->info("   ✅ Migrado com sucesso");
                } else {
                    $this->info("   🔍 Seria migrado (simulação)");
                }

                $sucesso++;

            } catch (\Exception $e) {
                $this->error("   ❌ Erro: {$e->getMessage()}");
                $erros++;

                Log::error('Erro na migração S3', [
                    'proposicao_id' => $proposicao->id,
                    'caminho_antigo' => $caminhoAntigo,
                    'caminho_novo' => $caminhoNovo,
                    'erro' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n" . '=' . str_repeat('=', 70));
        $this->info("📊 Resultado da migração:");
        $this->info("   ✅ Sucessos: {$sucesso}");
        $this->info("   ❌ Erros: {$erros}");

        if ($dryRun) {
            $this->info("\n🚀 Para executar a migração real, rode:");
            $this->info("   php artisan proposicoes:migrar-s3-camara");
        } else {
            $this->info("\n🎉 Migração concluída!");
        }

        return $erros > 0 ? 1 : 0;
    }

    private function gerarNovoCaminho(string $caminhoAntigo, string $camaraIdentifier): string
    {
        // Se já começa com identificador da câmara, não mudar
        if (str_starts_with($caminhoAntigo, $camaraIdentifier . '/')) {
            return $caminhoAntigo;
        }

        // Se começa com outro identificador (ex: camaramunicipal_xxx), substituir
        if (preg_match('/^[a-z]+_[a-f0-9]+\/(.+)$/', $caminhoAntigo, $matches)) {
            return $camaraIdentifier . '/' . $matches[1];
        }

        // Se não tem identificador, adicionar no início
        return $camaraIdentifier . '/' . $caminhoAntigo;
    }
}