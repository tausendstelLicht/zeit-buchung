<?php

namespace ZeitBuchung\Application;


use Symfony\Component\Console\Application;
use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;

/**
 * Class ZeitBuchung
 * @package ZeitBuchung\Application
 */
class ZeitBuchung extends Application
{
    /**
     * ZeitBuchung constructor.
     *
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->add(new Start());
        $this->add(new Stop());
        $this->add(new Report());
        $this->add(new Status());
    }
}
