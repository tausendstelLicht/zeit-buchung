<?php

namespace ZeitBuchung\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZeitBuchung\Interfaces\SymfonyStyleInterface;
use ZeitBuchung\Service\DateTimeService;
use ZeitBuchung\Service\FileService;
use ZeitBuchung\Service\RecordService;
use ZeitBuchung\Style\CustomStyle;

/**
 * Class Command
 *
 * @package ZeitBuchung\Command
 */
abstract class Command extends SymfonyCommand implements SymfonyStyleInterface
{
    /** @var RecordService */
    private $recordService;

    /** @var FileService */
    protected $fileService;

    /** @var DateTimeService */
    protected $dateTimeService;

    /** @var SymfonyStyle */
    protected $symfonyStyle;

    /**
     * Command constructor.
     *
     * @param RecordService $recordService
     * @param FileService $fileService
     * @param DateTimeService $dateTimeService
     * @param string|null $name
     */
    public function __construct(
        RecordService $recordService,
        FileService $fileService,
        DateTimeService $dateTimeService,
        string $name = null
    ) {
        $this->recordService = $recordService;
        $this->fileService = $fileService;
        $this->dateTimeService = $dateTimeService;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle = new CustomStyle($input, $output);
        $this->prepareServices($this->symfonyStyle);
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     * @return void
     */
    protected function prepareServices(SymfonyStyle $symfonyStyle): void
    {
        $this->recordService->setSymfonyStyle($symfonyStyle);
        $this->fileService->setSymfonyStyle($symfonyStyle);
        $this->dateTimeService->setSymfonyStyle($symfonyStyle);
    }

    /**
     * @return SymfonyStyle|null
     */
    public function getSymfonyStyle(): ?SymfonyStyle
    {
        return $this->symfonyStyle;
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     * @return void
     */
    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): void
    {
        $this->symfonyStyle = $symfonyStyle;
    }
}
