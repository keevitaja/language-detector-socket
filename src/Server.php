<?php

/** @noinspection PhpObjectFieldsAreOnlyWrittenInspection */

declare(strict_types=1);

namespace Keevitaja\LanguageDetectorSocket;

use Keevitaja\LanguageDetectorSocket\Exceptions\SocketErrorException;
use Nitotm\Eld\LanguageDetector;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Server
{
    protected Config $config;

    protected LanguageDetector $detector;

    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
        $this->detector = $this->detector();
    }

    public function run(): void
    {
        Worker::$pidFile = $this->config->workerPidFile;
        Worker::$logFile = $this->config->workerLogFile;
        Worker::$stdoutFile = $this->config->workerStdoutFile;
        Worker::$daemonize = $this->config->workerDemonize;

        $server = new Worker($this->socket());

        $server->count = $this->config->processes;

        $server->onMessage = function (TcpConnection $connection, string $text): void {
            $scores = $this->detector->detect($text)->scores();

            $connection->send(json_encode($scores));
        };

        $server->onError = function (TcpConnection $connection, int $code, string $message): void {
            throw new SocketErrorException("[$code] $message");
        };

        Worker::runAll();
    }

    protected function detector(): LanguageDetector
    {
        $detector = new LanguageDetector($this->config->eldDataFile, $this->config->eldFormat);

        if (! is_null($this->config->locales)) {
            $detector->langSubset($this->config->locales);
        }

        return $detector;
    }

    protected function socket(): string
    {
        $path = $this->config->socket;
        $pid = $this->config->workerPidFile;

        if (file_exists($path)) {
            unlink($path);
        }

        if (file_exists($pid)) {
            unlink($pid);
        }

        return 'unix://'.$path;
    }
}
