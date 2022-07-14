<?php

declare(strict_types=1);

use OneBot\Driver\Workerman\WorkermanDriver;
use OneBot\V12\OneBotBuilder;
use ZM\Logger\ConsoleLogger;

require_once __DIR__ . '/../vendor/autoload.php';

OneBotBuilder::factory()
    ->setName('test')
    ->setPlatform('testarea')
    ->setSelfId('t001')
    ->useLogger(ConsoleLogger::class)
    ->useDriver(WorkermanDriver::class)
    ->setCommunicationsProtocol([['http' => ['host' => '0.0.0.0', 'port' => 20001]]])
    ->build();
