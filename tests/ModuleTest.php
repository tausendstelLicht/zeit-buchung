<?php

namespace ZeitBuchung\Tests;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManager;
use Prophecy\Prophet;
use ZeitBuchung\Module;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleTest
 *
 * @package ZeitBuchung\Tests
 */
class ModuleTest extends TestCase
{
    /** @var Module */
    private $module;

    /** @var Prophet */
    private $prophet;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->prophet = new Prophet();
        $this->module = new Module();
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
    public function testModuleClass(): void
    {
        $this->assertInstanceOf(Module::class, $this->module);
        $this->assertInstanceOf(ConfigProviderInterface::class, $this->module);
        $this->assertInstanceOf(InitProviderInterface::class, $this->module);
    }

    /**
     * @return void
     */
    public function testInit(): void
    {
        $moduleManager = $this->prophet->prophesize(ModuleManager::class);
        $this->module->init($moduleManager->reveal());
        $this->assertNotEmpty(ZEIT_BUCHUNG_MODULE_ROOT);
        $this->assertDirectoryExists(ZEIT_BUCHUNG_MODULE_ROOT);
    }

    /**
     * @return void
     */
    public function testGetConfig(): void
    {
        if (false === defined('ZEIT_BUCHUNG_MODULE_ROOT')) {
            $moduleManager = $this->prophet->prophesize(ModuleManager::class);
            $this->module->init($moduleManager->reveal());
        }

        $this->assertNotEmpty($this->module->getConfig());
        $this->assertArrayHasKey('dateDefaultTimezone', $this->module->getConfig());
        $this->assertArrayHasKey('savePath', $this->module->getConfig());
        $this->assertArrayHasKey('service_manager', $this->module->getConfig());
    }
}
