<?php

declare(strict_types=1);

namespace Keevitaja\LanguageDetectorSocket;

/**
 * @property $socket
 * @property $processes
 * @property $timeout
 * @property $locales
 * @property $eldDataFile
 * @property $eldFormat
 * @property $workerPidFile
 * @property $workerLogFile
 * @property $workerStdoutFile
 * @property $workerDemonize
 */
class Config
{
    protected array $defaults;

    public function __construct(protected array $config)
    {
        $this->defaults = require __DIR__.'/../resources/config.php';
    }

    public function __get(string $name): mixed
    {
        return $this->config[$name] ?? $this->defaults[$name];
    }
}
