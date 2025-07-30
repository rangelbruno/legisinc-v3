<?php

namespace App\Services;

use App\Models\Proposicao;
use Illuminate\Support\Facades\DB;

class ProposicaoNumeroService
{
    /**
     * Gerar próximo número para um tipo de proposição
     */
    public function gerarProximoNumero(string $tipo, int $ano = null): string
    {
        $ano = $ano ?: date('Y');
        
        // Buscar último número usado para este tipo no ano
        $ultimoNumero = Proposicao::where('tipo', $tipo)
            ->where('numero_protocolo', 'like', $tipo . '/' . $ano . '/%')
            ->orderBy('numero_protocolo', 'desc')
            ->value('numero_protocolo');

        if ($ultimoNumero) {
            // Extrair sequencial do último número (formato: PL/2025/0001)
            $partes = explode('/', $ultimoNumero);
            $ultimoSequencial = (int) end($partes);
            $novoSequencial = $ultimoSequencial + 1;
        } else {
            $novoSequencial = 1;
        }

        return $tipo . '/' . $ano . '/' . sprintf('%04d', $novoSequencial);
    }

    /**
     * Verificar se um número já existe
     */
    public function numeroExiste(string $numeroProtocolo): bool
    {
        return Proposicao::where('numero_protocolo', $numeroProtocolo)->exists();
    }

    /**
     * Atribuir número automático a uma proposição
     */
    public function atribuirNumeroAutomatico(Proposicao $proposicao): string
    {
        return DB::transaction(function () use ($proposicao) {
            $numeroProtocolo = $this->gerarProximoNumero($proposicao->tipo);
            
            // Verificar se o número não foi usado por outra transação
            while ($this->numeroExiste($numeroProtocolo)) {
                $numeroProtocolo = $this->gerarProximoNumero($proposicao->tipo);
            }
            
            $proposicao->update([
                'numero_protocolo' => $numeroProtocolo,
                'data_protocolo' => now(),
                'funcionario_protocolo_id' => auth()->id()
            ]);
            
            return $numeroProtocolo;
        });
    }

    /**
     * Atribuir número manual a uma proposição
     */
    public function atribuirNumeroManual(Proposicao $proposicao, string $numeroProtocolo): bool
    {
        // Validar formato do número
        if (!$this->validarFormatoNumero($numeroProtocolo)) {
            throw new \InvalidArgumentException('Formato de número inválido. Use o padrão: TIPO/ANO/SEQUENCIAL (ex: PL/2025/0001)');
        }

        // Verificar se já existe
        if ($this->numeroExiste($numeroProtocolo)) {
            throw new \InvalidArgumentException('Este número já foi atribuído a outra proposição.');
        }

        return DB::transaction(function () use ($proposicao, $numeroProtocolo) {
            $proposicao->update([
                'numero_protocolo' => $numeroProtocolo,
                'data_protocolo' => now(),
                'funcionario_protocolo_id' => auth()->id()
            ]);
            
            return true;
        });
    }

    /**
     * Validar formato do número de protocolo
     */
    private function validarFormatoNumero(string $numero): bool
    {
        // Formato esperado: TIPO/ANO/SEQUENCIAL (ex: PL/2025/0001)
        $pattern = '/^[A-Z]{2,3}\/\d{4}\/\d{4}$/';
        return preg_match($pattern, $numero) === 1;
    }

    /**
     * Obter estatísticas de numeração por tipo
     */
    public function obterEstatisticasPorTipo(int $ano = null): array
    {
        $ano = $ano ?: date('Y');
        
        return Proposicao::select('tipo', DB::raw('COUNT(*) as total'))
            ->whereNotNull('numero_protocolo')
            ->where('numero_protocolo', 'like', '%/' . $ano . '/%')
            ->groupBy('tipo')
            ->orderBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();
    }

    /**
     * Obter próximos números disponíveis para cada tipo
     */
    public function obterProximosNumeros(): array
    {
        $tipos = ['PL', 'PLP', 'PEC', 'PDC', 'REQ', 'MOC', 'IND'];
        $proximos = [];
        
        foreach ($tipos as $tipo) {
            $proximos[$tipo] = $this->gerarProximoNumero($tipo);
        }
        
        return $proximos;
    }
}