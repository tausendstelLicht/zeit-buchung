<?php

namespace ZeitBuchung\Application;


use Symfony\Component\Console\Application;
use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;

class ZeitBuchung extends Application
{

    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->add(new Start());
        $this->add(new Stop());
        $this->add(new Report());
        $this->add(new Status());
    }

}