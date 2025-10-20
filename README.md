# Language Detector Socket

A high-performance language detection service written in PHP using Unix sockets and [Efficient Language Detector](https://github.com/nitotm/efficient-language-detector).

## Installation
```bash
composer require keevitaja/language-detector-socket
```

> [!IMPORTANT]
> **Enable OPcache for CLI** to avoid excessive CPU usage. The LanguageDetector library loads and compiles large language model data files on every request. Without OPcache, PHP will recompile these files repeatedly, causing significant CPU overhead.
>
> ```ini
> opcache.enable_cli=1
> ```

## Usage

### Start the Server
```php
use Keevitaja\LanguageDetectorSocket\Server;

$server = new Server([
    'socket' => '/tmp/language-detector.sock',
]);

$server->run();
```

### Use the Client
```php
use Keevitaja\LanguageDetectorSocket\Client;

$client = new Client([
    'socket' => '/tmp/language-detector.sock',
])->make();

$scores = $client->detect('Hello world');
// ['en' => 0.95, 'nl' => 0.03, ...]

$client->close();
```

## Configuration defaults
```php
[
    // Unix socket path
    'socket' => '/tmp/language.detector.sock',

    // Number of worker processes
    'processes' => 1,

    // Connection timeout in seconds
    'timeout' => 10,

    // Limit detection to specific languages (null = all or ['en', 'nl'])
    'locales' => null,

    // Language model size (SMALL, MEDIUM, LARGE)
    'eldDataFile' => EldDataFile::SMALL,

    // Language code format (ISO639_1, ISO639_3)
    'eldFormat' => EldFormat::ISO639_1,

    // Path to store the worker process ID file
    'workerPidFile' => '/tmp/language.detector.worker.pid',

    // Path to store the worker log file
    'workerLogFile' => '/tmp/language.detector.worker.log',

    // Path to store the worker an stdout log file
    'workerStdoutFile' => '/tmp/language.detector.worker.stdout.log',

    // Whether to run the worker as a daemon process
    'workerDemonize' => false,
]
```
