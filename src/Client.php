<?php

declare(strict_types=1);

namespace Keevitaja\LanguageDetectorSocket;

use Keevitaja\LanguageDetectorSocket\Exceptions\SocketConnectException;
use Keevitaja\LanguageDetectorSocket\Exceptions\SocketResourceException;
use Keevitaja\LanguageDetectorSocket\Exceptions\SocketWriteException;

class Client
{
    protected Config $config;

    protected mixed $socket;

    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
    }

    public function make(): self
    {
        $this->socket = stream_socket_client($this->socket(), $code, $message, $this->config->timeout);

        if (! $this->socket) {
            throw new SocketConnectException("[$code] $message");
        }

        stream_set_timeout($this->socket, $this->config->timeout);

        return $this;
    }

    public function detect(string $text): array
    {
        $text = trim($text);

        // Writing an empty string to the socket won't send any data,
        // causing fread() to hang waiting for a response that will never come.
        // Return early with an empty result as there's no language to detect anyway.
        if (empty($text)) {
            return [];
        }

        if (! is_writable($this->config->socket) || ! is_resource($this->socket)) {
            throw new SocketResourceException('Socket is not a resource');
        }

        fwrite($this->socket, $text);

        return json_decode(fread($this->socket, 8192), true);
    }

    public function close(): void
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
    }

    protected function socket(): string
    {
        if (! is_writable($this->config->socket)) {
            throw new SocketWriteException('Socket file is not writable');
        }

        return 'unix://'.$this->config->socket;
    }
}
