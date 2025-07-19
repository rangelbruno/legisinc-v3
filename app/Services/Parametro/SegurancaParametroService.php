<?php

namespace App\Services\Parametro;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SegurancaParametroService
{
    private const SENSITIVE_FIELDS = [
        'password', 'token', 'secret', 'key', 'credential',
        'api_key', 'database_password', 'smtp_password'
    ];

    private const ALLOWED_HTML_TAGS = [
        '<p>', '<br>', '<strong>', '<em>', '<u>', '<ol>', '<ul>', '<li>'
    ];

    /**
     * Sanitiza valor de entrada
     */
    public function sanitizarValor(mixed $valor, string $tipoCampo = 'string'): mixed
    {
        if (is_null($valor)) {
            return null;
        }

        return match ($tipoCampo) {
            'string', 'text' => $this->sanitizarString($valor),
            'html' => $this->sanitizarHtml($valor),
            'email' => $this->sanitizarEmail($valor),
            'url' => $this->sanitizarUrl($valor),
            'number', 'integer' => $this->sanitizarNumero($valor),
            'decimal', 'float' => $this->sanitizarDecimal($valor),
            'boolean' => $this->sanitizarBoolean($valor),
            'json' => $this->sanitizarJson($valor),
            'password' => $this->sanitizarPassword($valor),
            'date', 'datetime' => $this->sanitizarData($valor),
            default => $this->sanitizarString($valor)
        };
    }

    /**
     * Sanitiza string básica
     */
    private function sanitizarString(mixed $valor): string
    {
        $valor = trim((string) $valor);
        $valor = strip_tags($valor);
        $valor = html_entity_decode($valor, ENT_QUOTES, 'UTF-8');
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        
        // Remove caracteres de controle
        $valor = preg_replace('/[\x00-\x1F\x7F]/', '', $valor);
        
        return $valor;
    }

    /**
     * Sanitiza HTML permitindo apenas tags seguras
     */
    private function sanitizarHtml(mixed $valor): string
    {
        $valor = (string) $valor;
        
        // Remove scripts e estilos
        $valor = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $valor);
        $valor = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $valor);
        
        // Remove eventos JavaScript
        $valor = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $valor);
        $valor = preg_replace('/\s*on\w+\s*=\s*[^>\s]+/', '', $valor);
        
        // Mantém apenas tags permitidas
        $valor = strip_tags($valor, implode('', self::ALLOWED_HTML_TAGS));
        
        return $valor;
    }

    /**
     * Sanitiza email
     */
    private function sanitizarEmail(mixed $valor): string
    {
        $valor = trim((string) $valor);
        $valor = filter_var($valor, FILTER_SANITIZE_EMAIL);
        
        return $valor ?: '';
    }

    /**
     * Sanitiza URL
     */
    private function sanitizarUrl(mixed $valor): string
    {
        $valor = trim((string) $valor);
        $valor = filter_var($valor, FILTER_SANITIZE_URL);
        
        // Validar protocolo
        if ($valor && !preg_match('/^https?:\/\//', $valor)) {
            $valor = 'https://' . $valor;
        }
        
        return $valor ?: '';
    }

    /**
     * Sanitiza número
     */
    private function sanitizarNumero(mixed $valor): int
    {
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitiza decimal
     */
    private function sanitizarDecimal(mixed $valor): float
    {
        return (float) filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Sanitiza boolean
     */
    private function sanitizarBoolean(mixed $valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }
        
        if (is_string($valor)) {
            return in_array(strtolower($valor), ['true', '1', 'yes', 'on', 'sim']);
        }
        
        return (bool) $valor;
    }

    /**
     * Sanitiza JSON
     */
    private function sanitizarJson(mixed $valor): ?string
    {
        if (is_array($valor)) {
            $valor = json_encode($valor);
        }
        
        if (!is_string($valor)) {
            return null;
        }
        
        // Verificar se é JSON válido
        $decoded = json_decode($valor, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                'valor' => 'JSON inválido: ' . json_last_error_msg()
            ]);
        }
        
        // Re-encode para garantir formatação consistente
        return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Sanitiza senha (hash)
     */
    private function sanitizarPassword(mixed $valor): string
    {
        if (empty($valor)) {
            return '';
        }
        
        $valor = (string) $valor;
        
        // Verificar força da senha
        if (!$this->validarForcaSenha($valor)) {
            throw ValidationException::withMessages([
                'valor' => 'Senha não atende aos critérios de segurança mínimos'
            ]);
        }
        
        return Hash::make($valor);
    }

    /**
     * Sanitiza data
     */
    private function sanitizarData(mixed $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }
        
        try {
            $data = new \DateTime($valor);
            return $data->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'valor' => 'Data inválida: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Valida força da senha
     */
    private function validarForcaSenha(string $senha): bool
    {
        // Mínimo 8 caracteres
        if (strlen($senha) < 8) {
            return false;
        }
        
        // Deve conter pelo menos um número
        if (!preg_match('/\d/', $senha)) {
            return false;
        }
        
        // Deve conter pelo menos uma letra minúscula
        if (!preg_match('/[a-z]/', $senha)) {
            return false;
        }
        
        // Deve conter pelo menos uma letra maiúscula
        if (!preg_match('/[A-Z]/', $senha)) {
            return false;
        }
        
        // Deve conter pelo menos um caractere especial
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $senha)) {
            return false;
        }
        
        return true;
    }

    /**
     * Verifica se um campo é sensível
     */
    public function isCampoSensivel(string $nomeCampo): bool
    {
        $nomeCampo = strtolower($nomeCampo);
        
        foreach (self::SENSITIVE_FIELDS as $campo) {
            if (strpos($nomeCampo, $campo) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Criptografa valor sensível
     */
    public function criptografarValor(mixed $valor): string
    {
        if (empty($valor)) {
            return '';
        }
        
        try {
            return Crypt::encryptString((string) $valor);
        } catch (\Exception $e) {
            Log::error('Erro ao criptografar valor sensível', [
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Erro ao criptografar valor');
        }
    }

    /**
     * Descriptografa valor sensível
     */
    public function descriptografarValor(string $valorCriptografado): string
    {
        if (empty($valorCriptografado)) {
            return '';
        }
        
        try {
            return Crypt::decryptString($valorCriptografado);
        } catch (\Exception $e) {
            Log::error('Erro ao descriptografar valor sensível', [
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Erro ao descriptografar valor');
        }
    }

    /**
     * Valida permissões de usuário para uma operação
     */
    public function validarPermissoes(int $userId, string $operacao, string $recurso): bool
    {
        // Verificar se usuário está ativo
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->active) {
            return false;
        }
        
        // Super admin sempre tem acesso
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        // Verificar permissão específica
        $permissao = "parametros.{$recurso}.{$operacao}";
        if ($user->can($permissao)) {
            return true;
        }
        
        // Log tentativa de acesso negado
        Log::warning('Acesso negado a parâmetro', [
            'user_id' => $userId,
            'operacao' => $operacao,
            'recurso' => $recurso,
            'permissao_requerida' => $permissao,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return false;
    }

    /**
     * Valida rate limiting por usuário
     */
    public function validarRateLimit(int $userId, string $operacao = 'update'): bool
    {
        $chave = "parametros_rate_limit:{$userId}:{$operacao}";
        $limite = $this->obterLimiteOperacao($operacao);
        $janela = 60; // 1 minuto
        
        $contador = cache()->remember($chave, $janela, function() {
            return 0;
        });
        
        if ($contador >= $limite) {
            Log::warning('Rate limit excedido para parâmetros', [
                'user_id' => $userId,
                'operacao' => $operacao,
                'contador' => $contador,
                'limite' => $limite
            ]);
            return false;
        }
        
        cache()->put($chave, $contador + 1, $janela);
        return true;
    }

    /**
     * Obtém limite de operações por tipo
     */
    private function obterLimiteOperacao(string $operacao): int
    {
        return match ($operacao) {
            'create' => 10,
            'update' => 50,
            'delete' => 5,
            'read' => 200,
            default => 30
        };
    }

    /**
     * Verifica integridade de dados
     */
    public function verificarIntegridade(array $dados, array $dadosOriginais = []): array
    {
        $problemas = [];
        
        // Verificar alterações suspeitas
        if (!empty($dadosOriginais)) {
            foreach ($dados as $campo => $valor) {
                $valorOriginal = $dadosOriginais[$campo] ?? null;
                
                if ($this->isCampoSensivel($campo) && $valor !== $valorOriginal) {
                    $problemas[] = "Alteração detectada em campo sensível: {$campo}";
                }
                
                if ($this->isAlteracaoSuspeita($valor, $valorOriginal)) {
                    $problemas[] = "Alteração suspeita detectada no campo: {$campo}";
                }
            }
        }
        
        // Verificar padrões maliciosos
        foreach ($dados as $campo => $valor) {
            if ($this->contemPadraoMalicioso($valor)) {
                $problemas[] = "Padrão malicioso detectado no campo: {$campo}";
            }
        }
        
        return [
            'integro' => empty($problemas),
            'problemas' => $problemas,
            'verificado_em' => now()
        ];
    }

    /**
     * Verifica se alteração é suspeita
     */
    private function isAlteracaoSuspeita(mixed $valorNovo, mixed $valorAntigo): bool
    {
        if (is_null($valorAntigo) || is_null($valorNovo)) {
            return false;
        }
        
        $valorNovo = (string) $valorNovo;
        $valorAntigo = (string) $valorAntigo;
        
        // Alteração muito grande
        if (strlen($valorNovo) > strlen($valorAntigo) * 10) {
            return true;
        }
        
        // Contém caracteres suspeitos
        if (preg_match('/[<>"\']/', $valorNovo) && !preg_match('/[<>"\']/', $valorAntigo)) {
            return true;
        }
        
        return false;
    }

    /**
     * Verifica padrões maliciosos
     */
    private function contemPadraoMalicioso(mixed $valor): bool
    {
        if (!is_string($valor)) {
            return false;
        }
        
        $padroesMaliciosos = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onclick\s*=/i',
            '/onerror\s*=/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/\$\{.*\}/i', // Template injection
            '/<\?php/i',
            '/<\?=/i',
            '/<%.*%>/i',
        ];
        
        foreach ($padroesMaliciosos as $padrao) {
            if (preg_match($padrao, $valor)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Gera hash de integridade para dados
     */
    public function gerarHashIntegridade(array $dados): string
    {
        ksort($dados); // Ordenar para consistência
        $serializado = serialize($dados);
        return hash('sha256', $serializado);
    }

    /**
     * Verifica hash de integridade
     */
    public function verificarHashIntegridade(array $dados, string $hashEsperado): bool
    {
        $hashAtual = $this->gerarHashIntegridade($dados);
        return hash_equals($hashEsperado, $hashAtual);
    }

    /**
     * Mascarar valores sensíveis para logs
     */
    public function mascararValorSensivel(string $nomeCampo, mixed $valor): mixed
    {
        if (!$this->isCampoSensivel($nomeCampo)) {
            return $valor;
        }
        
        if (empty($valor)) {
            return $valor;
        }
        
        $valor = (string) $valor;
        $tamanho = strlen($valor);
        
        if ($tamanho <= 4) {
            return str_repeat('*', $tamanho);
        }
        
        return substr($valor, 0, 2) . str_repeat('*', $tamanho - 4) . substr($valor, -2);
    }

    /**
     * Audita operação de segurança
     */
    public function auditarOperacaoSeguranca(string $operacao, array $detalhes = []): void
    {
        Log::info('Operação de segurança em parâmetros', array_merge([
            'operacao' => $operacao,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ], $detalhes));
    }
}