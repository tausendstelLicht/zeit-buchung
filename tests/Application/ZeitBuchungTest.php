<?php

namespace ZeitBuchung\Tests\Application;

use Symfony\Component\Console\Application;
use ZeitBuchung\Application\ZeitBuchung;
use PHPUnit\Framework\TestCase;
use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;

/**
 * Class ZeitBuchungTest
 *
 * @package ZeitBuchung\Tests\Application
 */
class ZeitBuchungTest extends TestCase
{
    /** @var ZeitBuchung */
    private $testClass;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->testClass = new ZeitBuchung();
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
