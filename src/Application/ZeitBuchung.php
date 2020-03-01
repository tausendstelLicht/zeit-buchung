<?php

namespace ZeitBuchung\Application;

use Laminas\ServiceManager\ServiceManager;
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
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager, string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->add($serviceManager->get(Start::class));
        $this->add($serviceManager->get(Stop::class));
        $this->add($serviceManager->get(Report::class));
        $this->add($serviceManager->get(Status::class));
    }
}
