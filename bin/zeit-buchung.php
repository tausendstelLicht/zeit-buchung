#!/usr/bin/env php
<?php

use Laminas\Config\Config;
use Laminas\ServiceManager\ServiceManager;
use ZeitBuchung\Application\ZeitBuchung;

require __DIR__ . '/../vendor/autoload.php';

define('ZEIT_BUCHUNG_ROOT', dirname(__DIR__));

$config = new Config(require ZEIT_BUCHUNG_ROOT . '/config/module.config.php');
$serviceManager = new ServiceManager($config->get('service_manager')->toArray());
$serviceManager->setService('Config', $config);

date_default_timezone_set($config->get('dateDefaultTimezone'));

$zeitBuchung = new ZeitBuchung($serviceManager);
$zeitBuchung->run();
