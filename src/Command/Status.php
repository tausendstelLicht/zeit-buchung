<?php

namespace ZeitBuchung\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Helper\RecordFile;
use ZeitBuchung\Style\CustomStyle;

/**
 * Class Status
 *
 * @package ZeitBuchung\Command
 */
class Status extends Command
{
    /** @var CustomStyle */
    protected $io;

    /**
     * configures the command (name, description, help)
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('status');
        $this->setDescription('outputs the status of the record file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new CustomStyle($input, $output);

        try {
            $recordFile = new RecordFile($this->io);
            $recordFile->status();
        } catch (ZeitBuchungException $e) {
            $this->io->error($e->getMessage());

            return $e->getCode();
        }

        return null;
    }
}
