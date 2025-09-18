<?php

namespace App\Logging;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class DatabaseLogChannelFactory
{
    /**
     * Create a custom Monolog instance for database logging.
     */
    public function __invoke(array $config): LoggerInterface
    {
        $logger = new Logger('database');

        // Adicionar o nosso handler personalizado
        $handler = new DatabaseLogHandler();

        // Configurar nível do handler se especificado
        if (isset($config['level'])) {
            $level = Logger::toMonologLevel($config['level']);
            $handler->setLevel($level);
        }

        $logger->pushHandler($handler);

        return $logger;
    }
}