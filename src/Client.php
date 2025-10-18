<?php

declare(strict_types=1);

namespace Keevitaja\LanguageDetectorSocket;

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
            $message = "Failed to connect to socket: $message ($code)";

            throw new LanguageSocketException($message);
        }

        stream_set_timeout($this->socket, $this->config->timeout);

        return $this;
    }

    public function detect(string $text): array
    {
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
        return 'unix://'.$this->config->socket;
    }
}
