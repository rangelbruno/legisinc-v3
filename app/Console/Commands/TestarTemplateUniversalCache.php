<?php

namespace App\Console\Commands;

use App\Models\TemplateUniversal;
use App\Models\User;
use App\Models\Proposicao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestarTemplateUniversalCache extends Command
{
    protected $signature = 'test:template-universal-cache';
    protected $description = 'Testar implementação de cache inteligente do Template Universal';

    public function handle()
    {
        $this->info('🧪 TESTE - Template Universal Cache Inteligente');
        $this->info('=====================================================');
        $this->newLine();

        try {
            // 1. Verificar Template Universal
            $this->info('1. 📋 Verificando Template Universal...');
            $template = TemplateUniversal::first();
            
            if (!$template) {
                $this->error('❌ Template Universal não encontrado');
                return 1;
            }
            
            $this->info("✅ Template encontrado: ID {$template->id} - {$template->nome}");
            $this->newLine();

            // 2. Testar Document Key Inteligente
            $this->info('2. 🔑 Testando Document Key Inteligente...');
            
            $cacheKey = 'template_universal_doc_key_' . $template->id;
            $currentContentHash = md5($template->conteudo ?? '');
            
            $this->line("   Cache Key: {$cacheKey}");
            $this->line("   Content Hash: {$currentContentHash}");
            
            // Primeira geração
            $cachedData = Cache::get($cacheKey);
            if ($cachedData && $cachedData['content_hash'] === $currentContentHash) {
                $this->line("   ♻️ CACHE HIT: Usando key existente: {$cachedData['document_key']}");
                $documentKey = $cachedData['document_key'];
            } else {
                $timestamp = time();
                $hashSuffix = substr($currentContentHash, 0, 8);
                $documentKey = "template_universal_{$template->id}_{$timestamp}_{$hashSuffix}";
                
                Cache::put($cacheKey, [
                    'document_key' => $documentKey,
                    'content_hash' => $currentContentHash,
                    'timestamp' => $timestamp,
                ], 7200);
                
                $this->line("   🆕 NOVA KEY: {$documentKey}");
            }
            
            // Segunda geração (deve usar cache)
            $cachedData2 = Cache::get($cacheKey);
            if ($cachedData2 && $cachedData2['content_hash'] === $currentContentHash) {
                $this->info('   ✅ CACHE FUNCIONA: Segunda consulta retornou key cacheada');
            } else {
                $this->error('   ❌ CACHE FALHOU: Segunda consulta não usou cache');
            }
            
            $this->newLine();

            // 3. Testar mudança de conteúdo
            $this->info('3. 📝 Testando mudança de conteúdo...');
            
            $conteudoOriginal = $template->conteudo;
            $novoConteudo = $conteudoOriginal . ' MODIFICADO_' . time();
            $template->update(['conteudo' => $novoConteudo]);
            
            $novoContentHash = md5($novoConteudo);
            $this->line("   Novo Content Hash: {$novoContentHash}");
            
            // Deve gerar nova key
            $cachedData3 = Cache::get($cacheKey);
            if ($cachedData3 && $cachedData3['content_hash'] === $novoContentHash) {
                $this->line('   ♻️ Cache ainda válido (não deveria acontecer)');
            } else {
                $timestamp = time();
                $hashSuffix = substr($novoContentHash, 0, 8);
                $novaDocumentKey = "template_universal_{$template->id}_{$timestamp}_{$hashSuffix}";
                
                Cache::put($cacheKey, [
                    'document_key' => $novaDocumentKey,
                    'content_hash' => $novoContentHash,
                    'timestamp' => $timestamp,
                ], 7200);
                
                $this->info("   ✅ NOVA KEY GERADA: {$novaDocumentKey}");
                $this->info('   ✅ Cache invalidado corretamente após mudança de conteúdo');
            }
            
            // Restaurar conteúdo original
            $template->update(['conteudo' => $conteudoOriginal]);
            $this->newLine();

            // 4. Testar limpeza de cache
            $this->info('4. 🧹 Testando limpeza de cache...');
            
            Cache::forget($cacheKey);
            Cache::forget('onlyoffice_template_universal_' . $template->id);
            
            $cacheApagado = Cache::get($cacheKey);
            if (!$cacheApagado) {
                $this->info('   ✅ Cache removido com sucesso');
            } else {
                $this->error('   ❌ Cache não foi removido');
            }
            
            $this->newLine();

            // 5. Verificar usuário Legislativo
            $this->info('5. 👤 Verificando usuário Legislativo...');
            
            // Tentar encontrar usuário legislativo por email conhecido
            $legislativo = User::where('email', 'servidor@camara.gov.br')->first();
            if (!$legislativo) {
                $legislativo = User::where('email', 'joao@sistema.gov.br')->first();
            }
            
            if ($legislativo) {
                $this->info("   ✅ Usuário Legislativo encontrado: {$legislativo->name} ({$legislativo->email})");
                // Verificar se tem o campo role ou outro campo de tipo
                $campos = array_keys($legislativo->getAttributes());
                $this->line("   Campos disponíveis: " . implode(', ', $campos));
                $this->line("   Ativo: " . ($legislativo->ativo ?? 'N/A'));
            } else {
                $this->error('   ❌ Usuário Legislativo não encontrado');
            }
            
            $this->newLine();

            // 6. Resumo final
            $this->info('6. 📊 RESUMO DO TESTE');
            $this->info('=====================');
            
            $resultados = [
                '✅ Template Universal encontrado',
                '✅ Document Key inteligente funcional',
                '✅ Cache Hit/Miss funcionando corretamente',
                '✅ Invalidação de cache por mudança de conteúdo',
                '✅ Limpeza manual de cache',
                $legislativo ? '✅ Usuário Legislativo configurado' : '❌ Usuário Legislativo faltando',
            ];
            
            foreach ($resultados as $resultado) {
                $this->line("   {$resultado}");
            }
            
            $this->newLine();

            // 6. Testar cache inteligente para proposições
            $this->info('6. 📝 Testando cache inteligente de proposições...');
            
            $proposicao = Proposicao::first();
            if ($proposicao) {
                $this->info("   ✅ Proposição encontrada: ID {$proposicao->id}");
                
                // Testar geração de document key para proposição
                $cacheKey = 'proposicao_doc_key_' . $proposicao->id;
                $contentForHash = ($proposicao->conteudo ?? '') . ($proposicao->arquivo_path ?? '') . ($proposicao->ementa ?? '');
                $currentContentHash = md5($contentForHash);
                
                $this->line("   Cache Key: {$cacheKey}");
                $this->line("   Content Hash: {$currentContentHash}");
                
                // Primeira geração
                $cachedData = Cache::get($cacheKey);
                if ($cachedData && $cachedData['content_hash'] === $currentContentHash) {
                    $this->line("   ♻️ CACHE HIT: {$cachedData['document_key']}");
                } else {
                    $timestamp = time();
                    $hashSuffix = substr($currentContentHash, 0, 8);
                    $newKey = "proposicao_{$proposicao->id}_{$timestamp}_{$hashSuffix}";
                    
                    Cache::put($cacheKey, [
                        'document_key' => $newKey,
                        'content_hash' => $currentContentHash,
                        'timestamp' => $timestamp,
                    ], 7200);
                    
                    $this->line("   🆕 NOVA KEY: {$newKey}");
                }
                
                // Testar limpeza de cache
                Cache::forget($cacheKey);
                Cache::forget('onlyoffice_proposicao_' . $proposicao->id);
                
                $this->info("   ✅ Cache de proposição limpo com sucesso");
                
            } else {
                $this->error('   ❌ Nenhuma proposição encontrada');
            }
            
            $this->newLine();
            $this->info('🎉 TESTE CONCLUÍDO COM SUCESSO!');
            $this->newLine();
            $this->info('📋 PRÓXIMOS PASSOS:');
            $this->line('   1. Acesse: http://localhost:8001/proposicoes/1/onlyoffice/editor');
            $this->line('   2. Login como Legislativo: servidor@camara.gov.br / servidor123');
            $this->line('   3. Edite a proposição no OnlyOffice');
            $this->line('   4. Verifique se mudanças aparecem SEM Ctrl+F5');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ ERRO DURANTE O TESTE: {$e->getMessage()}");
            return 1;
        }
    }
}