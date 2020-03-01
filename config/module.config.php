<?php

use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;
use ZeitBuchung\Factory\CommandFactory;
use ZeitBuchung\Factory\FileServiceFactory;
use ZeitBuchung\Service\DateTimeService;
use ZeitBuchung\Service\FileService;

return [
    // user specific config
    'dateDefaultTimezone' => 'Europe/Berlin',
    'savePath' => ZEIT_BUCHUNG_MODULE_ROOT . '/recordFiles',

    // module config
    'service_manager' => [
        'factories' => [
            FileService::class => FileServiceFactory::class,
            Report::class => CommandFactory::class,
            Start::class => CommandFactory::class,
            Status::class => CommandFactory::class,
            Stop::class => CommandFactory::class,
        ],
        'invokables' => [
            DateTimeService::class => DateTimeService::class,
        ],
    ],
];
