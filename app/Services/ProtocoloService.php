<?php

namespace App\Services;

use App\Models\Proposicao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProtocoloService
{
    /**
     * Atribui número de protocolo de forma transacional e à prova de cluster
     * 
     * @param Proposicao $proposicao
     * @param int $userId
     * @return string Número do protocolo atribuído
     * @throws Exception
     */
    public function atribuirNumero(Proposicao $proposicao, int $userId): string
    {
        // Validar se pode protocolar
        if ($proposicao->status !== 'assinado') {
            throw new Exception("Proposição deve estar 'assinado' para ser protocolada. Status atual: {$proposicao->status}");
        }
        
        if ($proposicao->numero) {
            throw new Exception("Proposição já possui número de protocolo: {$proposicao->numero}");
        }
        
        $ano = date('Y');
        $numeroAtribuido = null;
        
        try {
            DB::transaction(function () use ($ano, $proposicao, $userId, &$numeroAtribuido) {
                
                // Advisory lock específico por ano para cluster/múltiplas instâncias
                if (DB::getDriverName() === 'pgsql') {
                    $lockId = crc32("protocolo-{$ano}");
                    DB::select('SELECT pg_advisory_xact_lock(?)', [$lockId]);
                    Log::debug("Advisory lock adquirido para protocolo", ['ano' => $ano, 'lock_id' => $lockId]);
                }
                
                // Buscar ou criar sequência do ano com lock de linha
                $sequencia = DB::table('protocolo_sequencias')
                    ->where('ano', $ano)
                    ->lockForUpdate()
                    ->first();
                
                if (!$sequencia) {
                    // Primeira proposição do ano
                    DB::table('protocolo_sequencias')->insert([
                        'ano' => $ano,
                        'proximo_numero' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $numeroAtribuido = 1;
                    Log::info("Criada nova sequência de protocolo", ['ano' => $ano, 'numero' => $numeroAtribuido]);
                } else {
                    // Incrementar sequência e obter número
                    $resultado = DB::table('protocolo_sequencias')
                        ->where('ano', $ano)
                        ->increment('proximo_numero', 1, ['updated_at' => now()]);
                    
                    if ($resultado === 0) {
                        throw new Exception("Falha ao incrementar sequência do protocolo para o ano {$ano}");
                    }
                    
                    $numeroAtribuido = $sequencia->proximo_numero;
                    Log::debug("Número de protocolo obtido da sequência", ['ano' => $ano, 'numero' => $numeroAtribuido]);
                }
                
                // Formatar número com zeros à esquerda
                $numeroFormatado = str_pad($numeroAtribuido, 4, '0', STR_PAD_LEFT);
                
                // Atualizar proposição
                $updated = $proposicao->update([
                    'numero' => $numeroFormatado,
                    'status' => 'protocolado',
                    'protocolado_em' => now(),
                    'protocolado_por' => $userId,
                ]);
                
                if (!$updated) {
                    throw new Exception("Falha ao atualizar proposição com número de protocolo");
                }
                
                // Registrar no histórico de protocolo (tabela existente)
                if (Schema::hasTable('protocolo_registro')) {
                    DB::table('protocolo_registro')->insert([
                        'proposicao_id' => $proposicao->id,
                        'numero_protocolo' => "{$numeroFormatado}/{$ano}",
                        'data_protocolo' => now(),
                        'responsavel_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                Log::info("Protocolo atribuído com sucesso", [
                    'proposicao_id' => $proposicao->id,
                    'numero' => $numeroFormatado,
                    'ano' => $ano,
                    'user_id' => $userId
                ]);
            });
            
        } catch (Exception $e) {
            Log::error("Erro ao atribuir número de protocolo", [
                'proposicao_id' => $proposicao->id,
                'ano' => $ano,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        return str_pad($numeroAtribuido, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Verifica se um número de protocolo já existe no ano
     * 
     * @param string $numero
     * @param int $ano
     * @return bool
     */
    public function numeroExiste(string $numero, int $ano): bool
    {
        return Proposicao::where('numero', $numero)
            ->where('ano', $ano)
            ->whereNull('deleted_at')
            ->exists();
    }
    
    /**
     * Obter próximo número disponível (para preview/estimativa)
     * 
     * @param int|null $ano
     * @return int
     */
    public function proximoNumero(?int $ano = null): int
    {
        $ano = $ano ?: date('Y');
        
        $sequencia = DB::table('protocolo_sequencias')
            ->where('ano', $ano)
            ->first();
            
        return $sequencia ? $sequencia->proximo_numero : 1;
    }
    
    /**
     * Obter estatísticas de protocolos do ano
     * 
     * @param int|null $ano
     * @return array
     */
    public function estatisticas(?int $ano = null): array
    {
        $ano = $ano ?: date('Y');
        
        $total = Proposicao::where('ano', $ano)
            ->where('status', 'protocolado')
            ->whereNotNull('numero')
            ->whereNull('deleted_at')
            ->count();
            
        $proximo = $this->proximoNumero($ano);
        
        return [
            'ano' => $ano,
            'total_protocolados' => $total,
            'proximo_numero' => $proximo,
            'numeros_disponiveis' => 9999 - $proximo + 1
        ];
    }
}