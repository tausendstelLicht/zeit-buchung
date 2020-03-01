<?php

namespace ZeitBuchung\Factory;

use DateTime;
use Exception;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ZeitBuchung\Service\FileService;

/**
 * Class FileServiceFactory
 *
 * @package ZeitBuchung\Factory
 */
class FileServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileService
    {
        $config = $container->get('Config');

        return new FileService($config['savePath'], new DateTime());
    }
}
