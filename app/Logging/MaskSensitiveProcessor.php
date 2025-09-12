<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class MaskSensitiveProcessor implements ProcessorInterface
{
    /**
     * Patterns to mask in logs
     */
    private array $patterns = [
        // CPF pattern
        '/\b\d{3}\.\d{3}\.\d{3}\-\d{2}\b/' => '***.***.***-**',
        
        // Email pattern
        '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i' => '***@***.***',
        
        // Credit card pattern
        '/\b\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4}\b/' => '****-****-****-****',
        
        // Phone numbers (Brazilian format)
        '/\(\d{2}\)\s?\d{4,5}\-\d{4}/' => '(**) *****-****',
        
        // JWT tokens
        '/Bearer\s+[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+/' => 'Bearer ***',
        
        // API keys (generic pattern)
        '/["\']?(api[_\-]?key|apikey|api_secret)["\']?\s*[:=]\s*["\']?[A-Za-z0-9\-_]{20,}["\']?/i' => '$1: ***',
        
        // Passwords in JSON/URLs
        '/(password|senha|pwd|pass)["\']?\s*[:=]\s*["\'][^"\']+["\']/i' => '$1: "***"',
        
        // Authorization headers
        '/(authorization|token)["\']?\s*[:=]\s*["\'][^"\']+["\']/i' => '$1: "***"',
    ];

    public function __invoke(LogRecord $record): LogRecord
    {
        // Mask message
        $record['message'] = $this->maskSensitiveData($record['message']);
        
        // Mask context recursively
        if (isset($record['context']) && is_array($record['context'])) {
            $record['context'] = $this->maskArray($record['context']);
        }
        
        // Mask extra data
        if (isset($record['extra']) && is_array($record['extra'])) {
            $record['extra'] = $this->maskArray($record['extra']);
        }
        
        return $record;
    }

    private function maskSensitiveData($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        foreach ($this->patterns as $pattern => $replacement) {
            $value = preg_replace($pattern, $replacement, $value);
        }

        return $value;
    }

    private function maskArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = $this->maskSensitiveData($value);
            } elseif (is_array($value)) {
                $array[$key] = $this->maskArray($value);
            } elseif (is_object($value) && method_exists($value, '__toString')) {
                $array[$key] = $this->maskSensitiveData((string) $value);
            }
            
            // Also check if the key itself suggests sensitive data
            if (is_string($key) && $this->isSensitiveKey($key) && is_string($value)) {
                $array[$key] = '***REDACTED***';
            }
        }

        return $array;
    }

    private function isSensitiveKey(string $key): bool
    {
        $sensitiveKeys = [
            'password', 'senha', 'pwd', 'pass',
            'token', 'api_key', 'apikey', 'api_secret',
            'secret', 'private_key', 'client_secret',
            'authorization', 'auth', 'certificate',
            'ssn', 'cpf', 'rg', 'credit_card'
        ];

        $lowerKey = strtolower($key);
        foreach ($sensitiveKeys as $sensitive) {
            if (str_contains($lowerKey, $sensitive)) {
                return true;
            }
        }

        return false;
    }
}