<?php


namespace ZeitBuchung\Helper;

use DateTime;
use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Structure\RecordStructure;
use ZeitBuchung\Style\CustomStyle;

/**
 * Class RecordFile
 *
 * @package ZeitBuchung\Helper
 */
class RecordFile
{
    /** @var string */
    private $path;

    /** @var string */
    private $fileName;

    /** @var string */
    private $contentString = '';

    /** @var RecordStructure[] */
    private $contentArray = [];

    /** @var CustomStyle */
    private $io;

    /**
     * RecordFile constructor.
     *
     * @param CustomStyle $io
     * @param string $fileName - e.g. "fileName.json"
     * @param string $path - e.g. "/path/for/record/files/"
     * @throws ZeitBuchungException
     */
    public function __construct(CustomStyle $io, string $fileName = '', string $path = '')
    {
        $this->io = $io;

        if (!empty($path)) {
            $this->path = $path;
        } else {
            $this->path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'recordFiles' . DIRECTORY_SEPARATOR;
        }

        if (!empty($fileName)) {
            $this->fileName = $fileName;
        } else {
            $this->fileName = date('Ymd') . '.json';
        }

        $this->checkFile();
        $this->readFile();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getContentString(): string
    {
        return $this->contentString;
    }

    /**
     * @return array
     */
    public function getContentArray(): array
    {
        return $this->contentArray;
    }

    /**
     * outputs error message, if record file does not exist
     *
     * @return void
     * @throws ZeitBuchungException
     */
    private function checkFile(): void
    {
        if (!is_file($this->path . $this->fileName)) {
            $this->io->note('Record file does not exist! "' . $this->path . $this->fileName . '"');
            $this->createFile();
        }
    }

    /**
     * fills class attributes $contentString and $contentArray, outputs error message, if cannot read record file
     *
     * @return void
     * @throws ZeitBuchungException
     * @throws Exception
     */
    private function readFile(): void
    {
        $fileContent = file_get_contents($this->path . $this->fileName);

        if (false === $fileContent) {
            throw new ZeitBuchungException('Cannot read record file! "' . $this->path . $this->fileName . '"', 100);
        }

        $this->setContentString($fileContent);
        $this->setContentArray($fileContent);
    }

    /**
     * @return bool
     */
    private function checkForUnstoppedRecord(): bool
    {
        if (!empty($this->contentArray)) {
            $lastRecordKey = array_key_last($this->contentArray);

            return null === $this->contentArray[$lastRecordKey]->getEnd();
        }

        return false;
    }

    /**
     * calculates the time difference between start and stop, minimum time is 1 minute
     *
     * @param int $start
     * @param int $stop
     * @return int
     */
    private function calculateTime(int $start, int $stop): int
    {
        $return = '1';

        $timeInSeconds = $stop - $start;
        $timeInMinutes = round($timeInSeconds / 60);
        if (1 < $timeInMinutes) {
            $return = $timeInMinutes;
        }

        return $return;
    }

    /**
     * stops the last record session, outputs a note, if no active record session exists
     *
     * @param string $inputTime
     * @return void
     * @throws ZeitBuchungException
     * @throws Exception
     */
    public function stop(?string $inputTime = ''): void
    {
        if (!$this->checkForUnstoppedRecord()) {
            $this->io->note('No active record session available.');
            exit(0);
        }

        $lastRecordKey = array_key_last($this->contentArray);
        $lastRecord = $this->contentArray[$lastRecordKey];
        $checkedInputTime = $this->checkInputTime($inputTime);

        if (null !== $checkedInputTime) {
            $stop = $checkedInputTime;
            if (strtotime($lastRecord->getHumanReadableStartTime())
                > strtotime(date('H:i:s', $checkedInputTime->getTimestamp()))
            ) {
                $this->io->warning('Input time is not valid.');
                $stop = new DateTime();
            }
        } else {
            $stop = new DateTime();
        }

        $lastRecord->setEnd($stop);

        $lastRecord->setTimeInMinutes(
            $this->calculateTime(
                $lastRecord->getStart()->getTimestamp(),
                $lastRecord->getEnd()->getTimestamp()
            )
        );

        $this->contentArray[$lastRecordKey] = $lastRecord;

        $writeResult = file_put_contents($this->path . $this->fileName, json_encode($this->contentArray));

        if (false === $writeResult) {
            throw new ZeitBuchungException('Cannot write content to record file! "' . $this->path . $this->fileName . '"', 102);
        }

        $this->io->text([
            'Stopped last record:',
            $lastRecord->getMessage(),
            $lastRecord->getHumanReadableStartTime() . ' - ' . $lastRecord->getHumanReadableEndTime(),
            $lastRecord->getHumanReadableTimePeriod(),
        ]);
    }

    /**
     * starts a new record session, unstopped record sessions will be stopped before
     *
     * @param string $message
     * @param string $inputTime
     * @return void
     * @throws ZeitBuchungException
     * @throws Exception
     */
    public function start(string $message, ?string $inputTime = ''): void
    {
        if ($this->checkForUnstoppedRecord()) {
            $this->stop();
            $this->io->newLine(2);
        }

        $checkedInputTime = $this->checkInputTime($inputTime);

        if (null !== $checkedInputTime) {
            $start = $checkedInputTime;
        } else {
            $start = new DateTime();
        }

        $this->contentArray[] = new RecordStructure($start, null, $message, 0);

        $writeResult = file_put_contents($this->path . $this->fileName, json_encode($this->contentArray));

        if (false === $writeResult) {
            throw new ZeitBuchungException('Cannot write content to record file! "' . $this->path . $this->fileName . '"', 102);
        }

        $this->io->text([
            'Started new record:',
            date('H:i:s', $start->getTimestamp()) . ' - ' . $message,
        ]);
    }

    /**
     * outputs the whole record file as table
     *
     * @param bool $sort
     * @return void
     */
    public function listing(bool $sort = false): void
    {
        $tableContent = $this->getListingTable($sort);

        $table = new Table($this->io);

        $tableStyle = $table->getStyle();
        $tableStyle->setHeaderTitleFormat('<bg=black;fg=white;options=bold>%s</>');
        $table->setStyle($tableStyle);
        $table->setHeaderTitle($this->fileName);
        $table->setHeaders($tableContent['headers']);
        $table->setRows($tableContent['rows']);

        $table->render();

        $this->io->newLine();
        $sum = $this->getSum();
        $this->io->text('Sum: ' . $this->getHumanReadableSum($sum));
    }

    /**
     * outputs the active record session, outputs a note, if no active record session exists
     *
     * @return void
     */
    public function status(): void
    {
        if (!$this->checkForUnstoppedRecord()) {
            $this->io->note('No active record session available.');
            exit(0);
        }

        $lastRecordKey = array_key_last($this->contentArray);
        $lastRecord = $this->contentArray[$lastRecordKey];

        $calculatedTime = $this->calculateTime(
            strtotime($lastRecord->getHumanReadableStartTime()),
            strtotime(date('H:i:s'))
        );

        $this->io->text([
            'Active record:',
            $lastRecord->getMessage(),
            $lastRecord->getHumanReadableStartTime()
            . ' (' . $this->getHumanReadableSum($calculatedTime) . ')',
        ]);
        $this->io->newLine();

        $sum = $this->getSum();
        $this->io->text('Sum: ' . $this->getHumanReadableSum($sum + $calculatedTime));
    }

    /**
     * returns the time sum of the day
     *
     * @return int
     */
    private function getSum(): int
    {
        $sumInMinutes = 0;

        foreach ($this->contentArray as $record) {
            if (null !== $record->getTimeInMinutes()) {
                $sumInMinutes += $record->getTimeInMinutes();
            }
        }

        return $sumInMinutes;
    }

    /**
     * @param int $sumInMinutes
     * @return string
     */
    private function getHumanReadableSum(int $sumInMinutes): string
    {
        if (15 <= $sumInMinutes) {
            $sumInHours = round($sumInMinutes / 60, 2);
            $return = $sumInHours . 'h';
        } else {
            $return = $sumInMinutes . 'm';
        }

        return $return;
    }

    /**
     * creates record file
     *
     * @return void
     * @throws ZeitBuchungException
     */
    private function createFile(): void
    {
        $result = file_put_contents($this->path . $this->fileName, '');

        if (false === $result) {
            throw new ZeitBuchungException('Cannot create file! "' . $this->path . $this->fileName . '"', 102);
        }
    }

    /**
     * @param string $inputTime
     * @return null|DateTime
     * @throws ZeitBuchungException
     */
    private function checkInputTime(?string $inputTime): ?DateTime
    {
        if (!empty($inputTime)) {
            switch (true) {
                case 4 === strlen($inputTime) && preg_match('/^\d\:[0-5]\d$/', $inputTime):
                    $return = $this->getDateTimeObject('0' . $inputTime . ':00');
                    break;
                case 5 === strlen($inputTime) && preg_match('/^([01]\d|2[0-4])\:[0-5]\d$/', $inputTime):
                    $return = $this->getDateTimeObject($inputTime . ':00');
                    break;
                case 7 === strlen($inputTime) && preg_match('/^\d\:[0-5]\d\:[0-5]\d$/', $inputTime):
                    $return = $this->getDateTimeObject('0' . $inputTime);
                    break;
                case 8 === strlen($inputTime) && preg_match('/^([01]\d|2[0-4])\:[0-5]\d\:[0-5]\d$/', $inputTime):
                    $return = $this->getDateTimeObject($inputTime);
                    break;
                default:
                    $return = null;
                    $this->io->warning('Input time string is invalid. "' . $inputTime . '"');
                    break;
            }

            return $return;
        }

        return null;
    }

    /**
     * @param string $inputTime
     * @return DateTime
     * @throws ZeitBuchungException
     */
    private function getDateTimeObject(string $inputTime): ?DateTime
    {
        try {
            return new DateTime(date('Y-m-d') . ' ' . $inputTime);
        } catch (Exception $e) {
            throw new ZeitBuchungException($e->getMessage(), 101, $e);
        }
    }

    /**
     * @param string $contentString
     * @return void
     */
    private function setContentString(string $contentString): void
    {
        $this->contentString = $contentString;
    }

    /**
     * @param string $contentString
     * @return void
     * @throws Exception
     */
    private function setContentArray(string $contentString): void
    {
        $contentArray = [];

        if (!empty($contentString)) {
            $jsonArray = json_decode($contentString, false);

            if (!empty($jsonArray)) {
                foreach ($jsonArray as $record) {
                    if (null === $record->end) {
                        $end = null;
                    } else {
                        $end = new DateTime($record->end->date);
                    }

                    $contentArray[] = new RecordStructure(
                        new DateTime($record->start->date),
                        $end,
                        $record->message,
                        $record->timeInMinutes
                    );
                }
            }
        }

        $this->contentArray = $contentArray;
    }

    /**
     * @param bool $sort
     * @return array
     */
    private function getListingTable(bool $sort = false): array
    {
        $return = [
            'headers' => [
                'start',
                'stop',
                'message',
                'time',
            ],
            'rows' => [],
        ];

        if (!empty($this->contentArray)) {
            $rows = [];

            if ($sort) {
                $recordsSortedByMessage = [];
                $timeSumInMinutesByMessage = [];

                foreach ($this->contentArray as $row) {
                    $recordsSortedByMessage[$row->getMessage()][] = $row;

                    if (!isset($timeSumInMinutesByMessage[$row->getMessage()])) {
                        $timeSumInMinutesByMessage[$row->getMessage()] = 0;
                    }

                    $timeSumInMinutesByMessage[$row->getMessage()] += $row->getTimeInMinutes();
                }

                $count = 0;

                foreach ($recordsSortedByMessage as $message => $messageRecords) {
                    $count++;

                    foreach ($messageRecords as $record) {
                        $rows[] = $record->toArray();
                    }

                    $rows[] = [
                        new TableCell('', ['colspan' => 3]),
                        '-----> ' . $this->getHumanReadableSum($timeSumInMinutesByMessage[$message]),
                    ];

                    if ($count < count($recordsSortedByMessage)) {
                        $rows[] = new TableSeparator();
                    }
                }
            } else {
                foreach ($this->contentArray as $row) {
                    $rows[] = $row->toArray();
                }
            }

            $return['rows'] = $rows;
        }

        return $return;
    }
}
