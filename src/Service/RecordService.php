<?php

namespace ZeitBuchung\Service;

use DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZeitBuchung\Interfaces\SymfonyStyleInterface;

/**
 * Class RecordService
 *
 * @package ZeitBuchung\Service
 */
class RecordService implements SymfonyStyleInterface
{
    /** @var SymfonyStyle */
    private $symfonyStyle;

    /**
     * @return void
     */
    public function start(): void
    {
    }

    /**
     * @return void
     */
    public function stop(): void
    {
    }

    /**
     * @return void
     */
    public function report(): void
    {
    }

    /**
     * @return void
     */
    public function status(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): void
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @inheritDoc
     */
    public function getSymfonyStyle(): ?SymfonyStyle
    {
        return $this->symfonyStyle;
    }
}
