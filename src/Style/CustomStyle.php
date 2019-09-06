<?php


namespace ZeitBuchung\Style;


use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CustomStyle
 *
 * @package ZeitBuchung\Style
 */
class CustomStyle extends SymfonyStyle
{
    /**
     * {@inheritdoc}
     */
    public function note($message): void
    {
        $this->block($message, 'NOTE', 'fg=white;bg=blue', ' ! ');
    }
}
