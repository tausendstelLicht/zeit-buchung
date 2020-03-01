<?php

namespace ZeitBuchung\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Helper\RecordFile;
use ZeitBuchung\Style\CustomStyle;

/**
 * Class Report
 *
 * @package ZeitBuchung\Command
 */
class Report extends Command
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
        $this->setName('report');
        $this->setDescription('reports the records of the day');
        $this->addArgument('date', InputArgument::OPTIONAL, 'Date of the record file (possible format: YYYY-MM-DD, DD.MM.YYYY, MM-DD or DD.MM.)');
        $this->addOption('sort', 's', InputOption::VALUE_NONE, 'Records will be sorted by tasks and messages.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new CustomStyle($input, $output);

        $inputDate = $this->checkInputDate($input->getArgument('date'));

        try {
            $recordFile = new RecordFile($this->io, $inputDate);
            $recordFile->listing($input->getOption('sort'));
        } catch (ZeitBuchungException $e) {
            $this->io->error($e->getMessage());

            return $e->getCode();
        }

        return null;
    }

    /**
     * @param string $inputDate
     * @return string
     */
    private function checkInputDate($inputDate): string
    {
        $return = '';

        if (!empty($inputDate)) {
            switch (true) {
                case preg_match('/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[012])\.$/', $inputDate): // DD.MM.
                    $inputDate .= date('Y');
                    $return = date('Ymd', strtotime($inputDate)) . '.json';
                    break;
                case preg_match('/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[012])\.\d{4}$/', $inputDate): // DD.MM.YYYY
                    $return = date('Ymd', strtotime($inputDate)) . '.json';
                    break;
                case preg_match('/^\d{2}\-(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // YY-MM-DD
                    $return = date('Ymd', strtotime($inputDate)) . '.json';
                    break;
                case preg_match('/^(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // MM-DD
                    $inputDate = date('Y') . '-' . $inputDate;
                    $return = date('Ymd', strtotime($inputDate)) . '.json';
                    break;
                case preg_match('/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // YYYY-MM-DD
                    $return = date('Ymd', strtotime($inputDate)) . '.json';
                    break;
                default:
                    $this->io->warning('Input date string is invalid. "' . $inputDate . '"');
                    break;
            }
        }

        return $return;
    }
}
