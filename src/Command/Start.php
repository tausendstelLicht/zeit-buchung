<?php


namespace ZeitBuchung\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Helper\RecordFile;
use ZeitBuchung\Style\CustomStyle;

/**
 * Class Start
 *
 * @package ZeitBuchung\Command
 */
class Start extends Command
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
        $this->setName('start');
        $this->setDescription('starts a new record session');
        $this->addArgument('message', InputArgument::REQUIRED, 'Time record message/description');
        $this->addArgument('time', InputArgument::OPTIONAL, 'Start time (possible format: hh:mm or hh:mm:ss)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new CustomStyle($input, $output);

        $time = $input->getArgument('time');

        try {
            $recordFile = new RecordFile($this->io);
            $recordFile->start($input->getArgument('message'), $time);
        } catch (ZeitBuchungException $e) {
            $this->io->error($e->getMessage());

            return $e->getCode();
        }

        return null;
    }
}
