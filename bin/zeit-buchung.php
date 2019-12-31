#!/usr/bin/env php
<?php

use ZeitBuchung\Application\ZeitBuchung;

require __DIR__ . '/../vendor/autoload.php';

$zeitBuchung = new ZeitBuchung();
$zeitBuchung->run();
