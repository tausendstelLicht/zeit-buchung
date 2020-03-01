<?php

use ZeitBuchung\Service\DateTimeService;

return [
    // user specific config
    'dateDefaultTimezone' => 'Europe/Berlin',
    'savePath' => ZEIT_BUCHUNG_MODULE_ROOT . '/recordFiles',

    // module config
    'service_manager' => [
        'factories' => [
            //
        ],
        'invokables' => [
            DateTimeService::class => DateTimeService::class
        ],
    ],
];
