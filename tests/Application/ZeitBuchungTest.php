<?php

namespace ZeitBuchung\Tests\Application;

use Laminas\ServiceManager\ServiceManager;
use Prophecy\Prophet;
use Symfony\Component\Console\Application;
use ZeitBuchung\Application\ZeitBuchung;
use PHPUnit\Framework\TestCase;
use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;
use ZeitBuchung\Service\DateTimeService;
use ZeitBuchung\Service\FileService;

/**
 * Class ZeitBuchungTest
 *
 * @package ZeitBuchung\Tests\Application
 */
class ZeitBuchungTest extends TestCase
{
    /** @var ZeitBuchung */
    private $testClass;

    /** @var Prophet */
    private $prophet;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->prophet = new Prophet();
        $serviceManager = $this->prophet->prophesize(ServiceManager::class);
        $fileService = $this->prophet->prophesize(FileService::class)->reveal();
        $dateTimeService = $this->prophet->prophesize(DateTimeService::class)->reveal();
        $serviceManager->get(Report::class)->willReturn(new Report($fileService, $dateTimeService));
        $serviceManager->get(Start::class)->willReturn(new Start($fileService, $dateTimeService));
        $serviceManager->get(Stop::class)->willReturn(new Stop($fileService, $dateTimeService));
        $serviceManager->get(Status::class)->willReturn(new Status($fileService, $dateTimeService));
        $this->testClass = new ZeitBuchung($serviceManager->reveal());
    }

    /**
     * @return void
     */
    public function testConstruct(): void
    {
        $this->assertInstanceOf(Start::class, $this->testClass->get('start'));
        $this->assertInstanceOf(Stop::class, $this->testClass->get('stop'));
        $this->assertInstanceOf(Status::class, $this->testClass->get('status'));
        $this->assertInstanceOf(Report::class, $this->testClass->get('report'));
    }

    /**
     * @return void
     */
    public function testIsInstanceOfApplication(): void
    {
        $this->assertInstanceOf(Application::class, $this->testClass);
    }
}
