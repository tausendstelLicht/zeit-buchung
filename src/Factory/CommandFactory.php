<?php

namespace ZeitBuchung\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ZeitBuchung\Command\Command;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Service\DateTimeService;
use ZeitBuchung\Service\FileService;
use ZeitBuchung\Service\RecordService;

/**
 * Class CommandFactory
 *
 * @package ZeitBuchung\Factory
 */
class CommandFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     * @throws ZeitBuchungException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Command
    {
        if (false === strpos($requestedName, 'ZeitBuchung\Command')) {
            throw new ZeitBuchungException('Requested service must be in namespace "ZeitBuchung\Command"');
        }

        /** @var RecordService $recordService */
        $recordService = $container->get(RecordService::class);
        /** @var FileService $fileService */
        $fileService = $container->get(FileService::class);
        /** @var DateTimeService $dateTimeService */
        $dateTimeService = $container->get(DateTimeService::class);

        return new $requestedName($recordService, $fileService, $dateTimeService);
    }
}
