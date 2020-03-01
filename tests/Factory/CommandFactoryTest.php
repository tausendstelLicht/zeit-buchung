<?php

namespace ZeitBuchung\Tests\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Prophecy\Prophet;
use ZeitBuchung\Command\Command;
use ZeitBuchung\Command\Report;
use ZeitBuchung\Command\Start;
use ZeitBuchung\Command\Status;
use ZeitBuchung\Command\Stop;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Factory\CommandFactory;
use PHPUnit\Framework\TestCase;
use ZeitBuchung\Service\DateTimeService;
use ZeitBuchung\Service\FileService;

/**
 * Class CommandFactoryTest
 *
 * @package ZeitBuchung\Tests\Factory
 */
class CommandFactoryTest extends TestCase
{
    /** @var CommandFactory */
    private $factory;

    /** @var Prophet */
    private $prophet;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->factory = new CommandFactory();
        $this->prophet = new Prophet();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    /**
     * @return void
     */
    public function testFactoryClass(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    /**
     * @return string[]
     */
    public function commandClassesDataProvider(): array
    {
        return [
            [Report::class],
            [Start::class],
            [Status::class],
            [Stop::class],
        ];
    }

    /**
     * @dataProvider commandClassesDataProvider
     * @param string $commandClassName
     * @return void
     * @throws ContainerException
     * @throws ZeitBuchungException
     */
    public function testInvoke(string $commandClassName): void
    {
        $fileService = $this->prophet->prophesize(FileService::class)->reveal();
        $dateTimeService = $this->prophet->prophesize(DateTimeService::class)->reveal();
        $serviceManager = $this->prophet->prophesize(ServiceManager::class);
        $serviceManager->willImplement(ContainerInterface::class);
        $serviceManager->get(FileService::class)->willReturn($fileService);
        $serviceManager->get(DateTimeService::class)->willReturn($dateTimeService);

        $commandClass = $this->factory->__invoke($serviceManager->reveal(), $commandClassName);
        $this->assertInstanceOf(Command::class, $commandClass);
    }

    /**
     * @return void
     * @throws ContainerException
     * @throws ZeitBuchungException
     */
    public function testInvokeException(): void
    {
        $serviceManager = $this->prophet->prophesize(ServiceManager::class);
        $serviceManager->willImplement(ContainerInterface::class);

        $this->expectException(ZeitBuchungException::class);
        $this->expectExceptionMessage('Requested service must be in namespace "ZeitBuchung\Command"');

        $this->factory->__invoke($serviceManager->reveal(), 'False\Namespace');
    }
}
