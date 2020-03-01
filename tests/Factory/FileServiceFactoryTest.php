<?php

namespace ZeitBuchung\Tests\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Prophecy\Prophet;
use ZeitBuchung\Factory\FileServiceFactory;
use PHPUnit\Framework\TestCase;
use ZeitBuchung\Service\FileService;

/**
 * Class FileServiceFactoryTest
 *
 * @package ZeitBuchung\Tests\Factory
 */
class FileServiceFactoryTest extends TestCase
{
    /** @var FileServiceFactory */
    private $factory;

    /** @var Prophet */
    private $prophet;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->factory = new FileServiceFactory();
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
     * @return void
     * @throws ContainerException
     */
    public function testInit(): void
    {
        $serviceManager = $this->prophet->prophesize(ServiceManager::class);
        $serviceManager->willImplement(ContainerInterface::class);
        $serviceManager->get('Config')->willReturn(['savePath' => dirname(__DIR__, 2) . '/recordFiles']);

        $fileService = $this->factory->__invoke($serviceManager->reveal(), FileService::class);
        $this->assertInstanceOf(FileService::class, $fileService);
    }
}
