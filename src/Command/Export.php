<?php

namespace ZeitBuchung\Command;

use DateTime;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Helper\RecordStore;
use ZeitBuchung\Style\CustomStyle;

use Symfony\Component\Console\Helper\Table;

/**
 * Class Export
 *
 * @package ZeitBuchung\Command
 */
class Export extends Command
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
        $this->setName('export');
        $this->setDescription('exports the records to csv');
        $this->addArgument('startdate', InputArgument::REQUIRED, 'Date of the start of export (possible format: YYYY-MM-DD, DD.MM.YYYY, MM-DD or DD.MM.)');
        $this->addArgument('enddate', InputArgument::REQUIRED, 'Date of the end of export (possible format: YYYY-MM-DD, DD.MM.YYYY, MM-DD or DD.MM.)');
        $this->addArgument('file', InputArgument::OPTIONAL, 'Filename of exported file');
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

        $start = $this->checkInputDate($input->getArgument('startdate'));
        $end = $this->checkInputDate($input->getArgument('enddate'));
        $filename = $input->getArgument("file");
        if(empty($filename)) {
            $date = new DateTime();
            $filename = dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'exports' . DIRECTORY_SEPARATOR . 'export_'.$date->format('Ymd').'.csv';
        }

        //1. Get all records for time range
        $recordStore = new RecordStore(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'recordFiles' . DIRECTORY_SEPARATOR);
        /** @var RecordStructure[] $records */
        $records = $recordStore->getRecordsInTimerange($start, $end, $this->io);

        $csvRecords = [];
        $sumOfHours = 0;
        foreach($records as $record) {
            $csvRecords[] = [$record->getStart()->format('d.m.Y'), round($record->getTimeInMinutes() / 60, 2), $record->getTask(), $record->getMessage()];
            $sumOfHours += round($record->getTimeInMinutes() / 60, 2);
        }

        try {
            $this->io->note('Getting records from '.$start->format('Y-m-d').' to '.$end->format('Y-m-d'));
            $this->io->note('Writing file to: '.$filename);
            $out = fopen($filename, 'w+');
            fputcsv($out, ['Date', "Hours", "Task", "Message"]);
            foreach($csvRecords as $csv) {
                fputcsv($out, $csv);
            }
            fclose($out);
            if($input->getOption('verbose')) {
                $this->listEachDay($records);
            }
        } catch (ZeitBuchungException $e) {
            $this->io->error($e->getMessage());
            return $e->getCode();
        }

        return null;
    }

    private function printTable(array $records, $hours)
    {
        $table = new Table($this->io);

        $tableStyle = $table->getStyle();
        $tableStyle->setHeaderTitleFormat('<bg=black;fg=white;options=bold>%s</>');
        $table->setStyle($tableStyle);
        $table->setHeaderTitle("Exported records");
        $table->setHeaders(["Date", "Hours", "Task", "Message"]);
        $table->setRows($records);

        $table->render();

        $this->io->note("Sum of hours: ".$hours.'h');
    }

    private function listEachDay(array $records)
    {
        $sum = 0;
        $sumOfDay = 0;
        $dailyRecords = [];
        $lastDay = $records[0]->getStart()->format('d.m.Y');
        foreach($records as $record) {
            if($record->getStart()->format('d.m.Y') != $lastDay) {
                $this->printTable($dailyRecords, $sumOfDay);
                $sumOfDay = 0;
                $dailyRecords = [];
            }

            $dailyRecords[] = [$record->getStart()->format('d.m.Y'), round($record->getTimeInMinutes() / 60, 2), $record->getTask(), $record->getMessage()];
            $sumOfDay += round($record->getTimeInMinutes() / 60, 2);
            $sum += round($record->getTimeInMinutes() / 60, 2);
            $lastDay = $record->getStart()->format('d.m.Y');
        }

        $this->printTable($dailyRecords, $sumOfDay);

        $this->io->note("Sum of all days: ".$sum.'h');
    }

    /**
     * @param string $inputDate
     * @return DateTime
     */
    private function checkInputDate($inputDate): DateTime
    {
        $return = '';

        if (!empty($inputDate)) {
            switch (true) {
                case preg_match('/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[012])\.$/', $inputDate): // DD.MM.
                    $inputDate .= date('Y');
                    $return = new DateTime($inputDate);
                    break;
                case preg_match('/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[012])\.\d{4}$/', $inputDate): // DD.MM.YYYY
                    $return = new DateTime($inputDate);
                    break;
                case preg_match('/^\d{2}\-(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // YY-MM-DD
                    $return = new DateTime($inputDate);
                    break;
                case preg_match('/^(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // MM-DD
                    $inputDate = date('Y') . '-' . $inputDate;
                    $return = new DateTime($inputDate);
                    break;
                case preg_match('/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // YYYY-MM-DD
                    $return = new DateTime($inputDate);
                    break;
                default:
                    $this->io->warning('Input date string is invalid. "' . $inputDate . '"');
                    break;
            }
        }

        return $return;
    }
}
