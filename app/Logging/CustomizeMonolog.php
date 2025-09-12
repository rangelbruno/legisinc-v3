<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class CustomizeMonolog
{
    /**
     * Customize the given Monolog instance.
     */
    public function __invoke($logger)
    {
        // Set JSON formatter for all handlers
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter());
        }
        
        // Add sensitive data masking processor
        $logger->pushProcessor(new MaskSensitiveProcessor());
        
        // Add request context processor
        $logger->pushProcessor(function (LogRecord $record) {
            // Add request ID
            $record['context']['request_id'] = request()->headers->get('X-Request-Id') 
                ?? app()->bound('request_id') ? app('request_id') : null;
            
            // Add route name
            $record['context']['route'] = optional(request()->route())->getName();
            
            // Add user context
            if (auth()->check()) {
                $record['context']['user_id'] = auth()->id();
                $record['context']['user_email'] = auth()->user()->email;
            }
            
            // Add request method and URL
            $record['context']['method'] = request()->method();
            $record['context']['url'] = request()->fullUrl();
            
            // Add client IP
            $record['context']['ip'] = request()->ip();
            
            // Add timestamp in ISO format
            $record['context']['timestamp'] = now()->toIso8601String();
            
            return $record;
        });
        
        // Add performance metrics processor
        $logger->pushProcessor(function (LogRecord $record) {
            // Add memory usage
            $record['extra']['memory_mb'] = round(memory_get_usage(true) / 1024 / 1024, 2);
            
            // Add execution time if available
            if (defined('LARAVEL_START')) {
                $record['extra']['execution_time_ms'] = round((microtime(true) - LARAVEL_START) * 1000, 2);
            }
            
            return $record;
        });
    }
}