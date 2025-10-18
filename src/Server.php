<?php

/** @noinspection PhpObjectFieldsAreOnlyWrittenInspection */

declare(strict_types=1);

namespace Keevitaja\LanguageDetectorSocket;

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
        $server = new Worker($this->socket());

        $server->count = $this->config->processes;

        $server->onMessage = function (TcpConnection $connection, string $text): void {
            $scores = $this->detector->detect($text)->scores();

            $connection->send(json_encode($scores));
        };

        $server->onError = function (TcpConnection $connection, int $code, string $message): void {
            $message = "Socket error: $message ($code)";

            throw new LanguageSocketException($message);
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

        if (file_exists($path)) {
            unlink($path);
        }

        return 'unix://'.$path;
    }
}
