<?php

declare(strict_types=1);

use Nitotm\Eld\EldDataFile;
use Nitotm\Eld\EldFormat;

return [
    'socket' => '/tmp/language-detector-socket.sock',
    'processes' => 1,
    'timeout' => 10,
    'locales' => null,
    'eldDataFile' => EldDataFile::SMALL,
    'eldFormat' => EldFormat::ISO639_1,
];
