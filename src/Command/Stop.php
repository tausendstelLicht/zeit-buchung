<?php

namespace ZeitBuchung\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Helper\RecordFile;
use ZeitBuchung\Style\CustomStyle;

/**
 * Class Stop
 *
 * @package ZeitBuchung\Command
 */
class Stop extends Command
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
        $this->setName('stop');
        $this->setDescription('stops an active record session');
        $this->addArgument('time', InputArgument::OPTIONAL, 'Stop time (possible format: hh:mm or hh:mm:ss)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new CustomStyle($input, $output);

        $time = $input->getArgument('time');

        try {
            $recordFile = new RecordFile($this->io);
            $recordFile->stop($time);
        } catch (ZeitBuchungException $e) {
            $this->io->error($e->getMessage());

            return $e->getCode();
        }

        return null;
    }
}
