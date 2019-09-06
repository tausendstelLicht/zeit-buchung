<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;

date_default_timezone_set('Europe/Berlin');
$application = new Application();

$application->add(new Start());
$application->add(new Stop());
$application->add(new Report());
$application->add(new Status());

try {
    $application->run();
} catch (Exception $e) {
    die($e);
}
