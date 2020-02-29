<?php

namespace ZeitBuchung\Interfaces;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Interface SymfonyStyleInterface
 *
 * @package ZeitBuchung\Interfaces
 */
interface SymfonyStyleInterface
{
    /**
     * @param SymfonyStyle $symfonyStyle
     * @return void
     */
    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): void;

    /**
     * @return SymfonyStyle|null
     */
    public function getSymfonyStyle(): ?SymfonyStyle;
}
