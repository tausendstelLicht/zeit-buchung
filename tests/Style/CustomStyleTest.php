<?php

namespace ZeitBuchung\Tests\Style;

use Prophecy\Argument;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZeitBuchung\Style\CustomStyle;
use PHPUnit\Framework\TestCase;

/**
 * Class CustomStyleTest
 *
 * @package ZeitBuchung\Tests\Style
 */
class CustomStyleTest extends TestCase
{
    /** @var CustomStyle */
    private $testClass;

    /** @var OutputInterface */
    private $outputInterface;

    /** @var InputInterfacethis-> */
    private $inputInterface;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $formatterInterface = $this->prophesize(OutputFormatterInterface::class)->reveal();
        $this->inputInterface = $this->prophesize(InputInterface::class);
        $this->outputInterface = $this->prophesize(OutputInterface::class);
        $this->outputInterface->getFormatter()->willReturn($formatterInterface);
        $this->outputInterface->getVerbosity()->willReturn(OutputInterface::VERBOSITY_QUIET);
        $this->testClass = new CustomStyle($this->inputInterface->reveal(), $this->outputInterface->reveal());
    }

    /**
     * @return void
     */
    public function testNote(): void
    {
        $this->outputInterface->write(Argument::type('string'))->shouldBeCalled();
        $this->outputInterface->writeln(Argument::type('string'), 1)->shouldBeCalled();
        $this->testClass->note('message');
    }

    /**
     * @return void
     */
    public function testIsInstanceOfSymfonyStyle(): void
    {
        $this->assertInstanceOf(SymfonyStyle::class, $this->testClass);
    }
}
