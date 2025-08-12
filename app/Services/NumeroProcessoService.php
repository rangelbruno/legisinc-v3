<?php

namespace App\Services;

use App\Models\Proposicao;
use App\Models\Parametro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NumeroProcessoService
{
    private array $parametros;

    public function __construct()
    {
        $this->carregarParametros();
    }

    /**
     * Carregar parâmetros de configuração
     */
    private function carregarParametros(): void
    {
        $this->parametros = Parametro::where('codigo', 'like', 'protocolo.%')
            ->where('ativo', true)
            ->pluck('valor', 'codigo')
            ->toArray();
    }

    /**
     * Gerar número de processo baseado nos parâmetros
     */
    public function gerarNumeroProcesso(Proposicao $proposicao): string
    {
        return DB::transaction(function () use ($proposicao) {
            // Obter formato configurado
            $formato = $this->parametros['protocolo.formato_numero_processo'] ?? '{TIPO}/{ANO}/{SEQUENCIAL}';
            
            // Obter próximo sequencial
            $sequencial = $this->obterProximoSequencial($proposicao);
            
            // Formatar número com zeros à esquerda
            $digitos = (int) ($this->parametros['protocolo.digitos_sequencial'] ?? 4);
            $sequencialFormatado = str_pad($sequencial, $digitos, '0', STR_PAD_LEFT);
            
            // Substituir variáveis no formato
            $numero = str_replace(
                ['{TIPO}', '{ANO}', '{SEQUENCIAL}', '{MES}', '{DIA}'],
                [
                    $proposicao->tipo,
                    date('Y'),
                    $sequencialFormatado,
                    date('m'),
                    date('d')
                ],
                $formato
            );
            
            // Adicionar prefixo e sufixo se configurados
            $prefixo = $this->parametros['protocolo.prefixo_processo'] ?? '';
            $sufixo = $this->parametros['protocolo.sufixo_processo'] ?? '';
            
            return $prefixo . $numero . $sufixo;
        });
    }

    /**
     * Obter próximo número sequencial
     */
    private function obterProximoSequencial(Proposicao $proposicao): int
    {
        $query = Proposicao::whereNotNull('numero_processo');
        
        // Verificar se deve reiniciar anualmente
        $reiniciarAnualmente = filter_var(
            $this->parametros['protocolo.reiniciar_sequencial_anualmente'] ?? true,
            FILTER_VALIDATE_BOOLEAN
        );
        
        if ($reiniciarAnualmente) {
            $query->whereYear('data_protocolo', date('Y'));
        }
        
        // Verificar se deve ter sequencial por tipo
        $sequencialPorTipo = filter_var(
            $this->parametros['protocolo.sequencial_por_tipo'] ?? true,
            FILTER_VALIDATE_BOOLEAN
        );
        
        if ($sequencialPorTipo) {
            $query->where('tipo', $proposicao->tipo);
        }
        
        // Buscar último número usado
        $ultimoNumero = $query->orderBy('numero_sequencial', 'desc')
            ->value('numero_sequencial');
        
        return ($ultimoNumero ?? 0) + 1;
    }

    /**
     * Atribuir número de processo a uma proposição
     */
    public function atribuirNumeroProcesso(Proposicao $proposicao, ?string $numeroManual = null): string
    {
        return DB::transaction(function () use ($proposicao, $numeroManual) {
            // Verificar se já possui número
            if ($proposicao->numero_processo) {
                throw new \Exception('Proposição já possui número de processo: ' . $proposicao->numero_processo);
            }
            
            // Determinar número a ser usado
            if ($numeroManual) {
                // Verificar se permite número manual
                $permitirManual = filter_var(
                    $this->parametros['protocolo.permitir_numero_manual'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );
                
                if (!$permitirManual) {
                    throw new \Exception('Configuração não permite números manuais');
                }
                
                // Validar se número não existe
                if ($this->numeroExiste($numeroManual)) {
                    throw new \Exception('Número de processo já existe: ' . $numeroManual);
                }
                
                $numeroProcesso = $numeroManual;
                $numeroSequencial = $this->extrairSequencial($numeroManual);
            } else {
                // Gerar número automático
                $numeroSequencial = $this->obterProximoSequencial($proposicao);
                $numeroProcesso = $this->gerarNumeroProcesso($proposicao);
                
                // Verificar duplicação (proteção adicional)
                while ($this->numeroExiste($numeroProcesso)) {
                    $numeroSequencial++;
                    $numeroProcesso = $this->gerarNumeroProcessoComSequencial($proposicao, $numeroSequencial);
                }
            }
            
            // Atualizar proposição
            $proposicao->update([
                'numero_processo' => $numeroProcesso,
                'numero_sequencial' => $numeroSequencial,
                'data_protocolo' => now(),
                'funcionario_protocolo_id' => auth()->id()
            ]);
            
            // Verificar se deve inserir no documento
            $inserirNoDocumento = filter_var(
                $this->parametros['protocolo.inserir_numero_documento'] ?? true,
                FILTER_VALIDATE_BOOLEAN
            );
            
            if ($inserirNoDocumento) {
                $this->inserirNumeroNoDocumento($proposicao);
            }
            
            // Log::info('Número de processo atribuído', [
                //     'proposicao_id' => $proposicao->id,
                //     'numero_processo' => $numeroProcesso,
                //     'usuario_id' => auth()->id()
            // ]);
            
            return $numeroProcesso;
        });
    }

    /**
     * Inserir número de processo no documento
     */
    private function inserirNumeroNoDocumento(Proposicao $proposicao): void
    {
        try {
            $posicao = $this->parametros['protocolo.posicao_numero_documento'] ?? 'cabecalho';
            
            if ($posicao === 'nao_inserir') {
                return;
            }
            
            // Chamar serviço do OnlyOffice para inserir número
            $onlyOfficeService = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
            $onlyOfficeService->inserirNumeroProcesso($proposicao, $posicao);
            
        } catch (\Exception $e) {
            // Log::warning('Falha ao inserir número de processo no documento', [
                //     'proposicao_id' => $proposicao->id,
                //     'numero_processo' => $proposicao->numero_processo,
                //     'erro' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Verificar se número já existe
     */
    private function numeroExiste(string $numero): bool
    {
        return Proposicao::where('numero_processo', $numero)->exists();
    }

    /**
     * Extrair sequencial de um número manual
     */
    private function extrairSequencial(string $numeroManual): int
    {
        // Tentar extrair o último número da string
        if (preg_match('/(\d+)(?!.*\d)/', $numeroManual, $matches)) {
            return (int) $matches[1];
        }
        
        // Se não encontrar, usar timestamp para garantir unicidade
        return time();
    }

    /**
     * Gerar número com sequencial específico
     */
    private function gerarNumeroProcessoComSequencial(Proposicao $proposicao, int $sequencial): string
    {
        $formato = $this->parametros['protocolo.formato_numero_processo'] ?? '{TIPO}/{ANO}/{SEQUENCIAL}';
        $digitos = (int) ($this->parametros['protocolo.digitos_sequencial'] ?? 4);
        $sequencialFormatado = str_pad($sequencial, $digitos, '0', STR_PAD_LEFT);
        
        $numero = str_replace(
            ['{TIPO}', '{ANO}', '{SEQUENCIAL}', '{MES}', '{DIA}'],
            [
                $proposicao->tipo,
                date('Y'),
                $sequencialFormatado,
                date('m'),
                date('d')
            ],
            $formato
        );
        
        $prefixo = $this->parametros['protocolo.prefixo_processo'] ?? '';
        $sufixo = $this->parametros['protocolo.sufixo_processo'] ?? '';
        
        return $prefixo . $numero . $sufixo;
    }

    /**
     * Obter configurações atuais
     */
    public function obterConfiguracoes(): array
    {
        return [
            'formato' => $this->parametros['protocolo.formato_numero_processo'] ?? '{TIPO}/{ANO}/{SEQUENCIAL}',
            'digitos_sequencial' => (int) ($this->parametros['protocolo.digitos_sequencial'] ?? 4),
            'reiniciar_anualmente' => filter_var($this->parametros['protocolo.reiniciar_sequencial_anualmente'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'sequencial_por_tipo' => filter_var($this->parametros['protocolo.sequencial_por_tipo'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'permitir_manual' => filter_var($this->parametros['protocolo.permitir_numero_manual'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'prefixo' => $this->parametros['protocolo.prefixo_processo'] ?? '',
            'sufixo' => $this->parametros['protocolo.sufixo_processo'] ?? '',
            'inserir_no_documento' => filter_var($this->parametros['protocolo.inserir_numero_documento'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'posicao_documento' => $this->parametros['protocolo.posicao_numero_documento'] ?? 'cabecalho'
        ];
    }

    /**
     * Prever próximo número para cada tipo
     */
    public function preverProximosNumeros(): array
    {
        $tipos = ['PL', 'PLP', 'PEC', 'PDC', 'REQ', 'MOC', 'IND'];
        $proximos = [];
        
        foreach ($tipos as $tipo) {
            // Criar proposição temporária para simulação
            $proposicaoTemp = new Proposicao(['tipo' => $tipo]);
            $sequencial = $this->obterProximoSequencial($proposicaoTemp);
            $proximos[$tipo] = $this->gerarNumeroProcessoComSequencial($proposicaoTemp, $sequencial);
        }
        
        return $proximos;
    }
}