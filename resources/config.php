<?php

declare(strict_types=1);

use Nitotm\Eld\EldDataFile;
use Nitotm\Eld\EldFormat;

return [
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
];
