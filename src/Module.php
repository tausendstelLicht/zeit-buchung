<?php

namespace ZeitBuchung;

use Laminas\Config\Factory;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;

/**
 * Class Module
 *
 * @package ZeitBuchung
 */
class Module implements ConfigProviderInterface, InitProviderInterface
{
    /**
     * @param ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager): void
    {
        define('ZEIT_BUCHUNG_MODULE_ROOT', dirname(__DIR__));
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return Factory::fromFiles(
            glob(ZEIT_BUCHUNG_MODULE_ROOT . '/config/{,*.}config.php', GLOB_BRACE)
        );
    }
}
