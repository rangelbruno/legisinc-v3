<?php

namespace App\Logging;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Carbon\Carbon;

class DatabaseLogHandler extends AbstractProcessingHandler
{
    /**
     * Padrões de logs importantes que devem ser salvos no banco
     */
    private array $importantPatterns = [
        'USER CLICK',
        '👤 USER CLICK',
        'ONLYOFFICE CONTAINER',
        '🐳 ONLYOFFICE CONTAINER',
        'LEGISLATIVO APPROVAL',
        '🏛️ LEGISLATIVO APPROVAL',
        'LEGISLATIVO RETURN',
        'PDF REGENERATION',
        '📄 PDF REGENERATION',
        'TEMPLATE SYSTEM',
        '🔍 TEMPLATE SYSTEM',
        'SECURITY',
        '🚨 SECURITY',
        'SISTEMA:',
        '🔧 SISTEMA:',
        'ADMIN:',
        '🔑 ADMIN:',
        'RTF Processing',
        '📝 RTF Processing'
    ];

    /**
     * Níveis de log que sempre devem ser salvos
     */
    private array $criticalLevels = [
        'emergency',
        'alert',
        'critical',
        'error'
    ];

    /**
     * Write the record down to the log of the implementing handler
     */
    protected function write(LogRecord $record): void
    {
        try {
            // Verificar se este log deve ser salvo no banco
            if (!$this->shouldSaveToDatabase($record)) {
                return;
            }

            // Extrair dados do contexto
            $context = $record->context ?? [];
            $extra = $record->extra ?? [];

            // Determinar request_id
            $requestId = $this->extractRequestId($context, $extra);

            // Determinar user_id
            $userId = $this->extractUserId($context);

            // Processar exceção se existir
            $exception = null;
            if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
                $exception = json_encode([
                    'class' => get_class($context['exception']),
                    'message' => $context['exception']->getMessage(),
                    'file' => $context['exception']->getFile(),
                    'line' => $context['exception']->getLine(),
                    'trace' => $context['exception']->getTraceAsString()
                ]);

                // Remove a exceção do contexto para evitar duplicação
                unset($context['exception']);
            }

            // Limpar e preparar contexto
            $contextJson = $this->prepareContext($context);

            // Inserir no banco
            DB::table('monitoring_logs')->insert([
                'level' => strtolower($record->level->getName()),
                'message' => $record->message,
                'context' => !empty($contextJson) ? json_encode($contextJson) : null,
                'exception' => $exception,
                'request_id' => $requestId,
                'user_id' => $userId,
                'created_at' => $record->datetime->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            // Falha silenciosa para evitar loops infinitos de log
            // Podemos logar no arquivo em caso de erro crítico
            error_log("DatabaseLogHandler Error: " . $e->getMessage());
        }
    }

    /**
     * Determinar se este log deve ser salvo no banco de dados
     */
    private function shouldSaveToDatabase(LogRecord $record): bool
    {
        $message = $record->message;
        $level = strtolower($record->level->getName());

        // Sempre salvar níveis críticos
        if (in_array($level, $this->criticalLevels)) {
            return true;
        }

        // Verificar se a mensagem contém padrões importantes
        foreach ($this->importantPatterns as $pattern) {
            if (strpos($message, $pattern) !== false) {
                return true;
            }
        }

        // Verificar contexto para patterns específicos
        $context = $record->context ?? [];
        if (isset($context['proposicao_id']) ||
            isset($context['onlyoffice_info']) ||
            isset($context['container_info']) ||
            isset($context['user_click']) ||
            isset($context['admin_user_id'])) {
            return true;
        }

        return false;
    }

    /**
     * Extrair request_id do contexto
     */
    private function extractRequestId(array $context, array $extra): ?string
    {
        // Tentar várias fontes para request_id
        if (isset($context['request_id'])) {
            return $context['request_id'];
        }

        if (isset($extra['request_id'])) {
            return $extra['request_id'];
        }

        // Tentar obter da sessão atual
        if (request() && request()->hasSession()) {
            return request()->session()->getId();
        }

        // Gerar UUID como fallback para requests importantes
        return \Illuminate\Support\Str::uuid()->toString();
    }

    /**
     * Extrair user_id do contexto
     */
    private function extractUserId(array $context): ?int
    {
        // Prioridade: contexto explicito > admin user > usuário autenticado
        if (isset($context['user_id'])) {
            return (int) $context['user_id'];
        }

        if (isset($context['admin_user_id'])) {
            return (int) $context['admin_user_id'];
        }

        // Tentar usuário autenticado atual
        try {
            if (Auth::check()) {
                return Auth::id();
            }
        } catch (\Exception $e) {
            // Ignorar erros de autenticação
        }

        return null;
    }

    /**
     * Preparar contexto removendo dados sensíveis e limitando tamanho
     */
    private function prepareContext(array $context): array
    {
        // Remover dados sensíveis
        $sensitiveKeys = ['password', 'token', 'secret', 'key', 'authorization'];
        foreach ($sensitiveKeys as $key) {
            if (isset($context[$key])) {
                $context[$key] = '[REDACTED]';
            }
        }

        // Limitar tamanho de strings grandes
        array_walk_recursive($context, function (&$value) {
            if (is_string($value) && strlen($value) > 1000) {
                $value = substr($value, 0, 1000) . '... [TRUNCATED]';
            }
        });

        return $context;
    }
}