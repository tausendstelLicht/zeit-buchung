<?php


namespace ZeitBuchung\Helper;

use DateTime;
use Exception;
use Symfony\Component\Console\Helper\Table;
use ZeitBuchung\Exception\ZeitBuchungException;
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

    /** @var array */
    private $contentArray = [];

    /** @var CustomStyle */
    private $io;

    /**
     * RecordFile constructor.
     *
     * @param CustomStyle $io
     * @param string $fileName - e.g. "fileName.log"
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
            $this->fileName = date('Ymd') . '.log';
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
        if (!empty($this->contentString)) {
            return substr_count($this->contentString, ';started;', -9) === 1;
        }

        return false;
    }

    /**
     * calculates the time difference between start and stop, minimum time is 1 minute
     *
     * @param string $start
     * @param string $stop
     * @return string
     */
    private function calculateTime(string $start, string $stop): string
    {
        $return = '1m';

        $timeInSeconds = strtotime($stop) - strtotime($start);
        $timeInMinutes = round($timeInSeconds / 60);
        if (1 < $timeInMinutes) {
            $return = $timeInMinutes . 'm';

            if (15 <= $timeInMinutes) {
                $timeInHours = round($timeInMinutes / 60, 2);
                $return .= ' / ' . $timeInHours . 'h';
            }
        }

        return $return;
    }

    /**
     * stops the last record session, outputs a note, if no active record session exists
     *
     * @param string $inputTime
     * @return void
     * @throws ZeitBuchungException
     */
    public function stop(?string $inputTime = ''): void
    {
        if (!$this->checkForUnstoppedRecord()) {
            $this->io->note('No active record session available.');
            exit(0);
        }

        $reversedContentArray = array_reverse($this->contentArray);
        $lastRecord = $reversedContentArray[0];
        $checkedInputTime = $this->checkInputTime($inputTime);

        if (null !== $checkedInputTime) {
            $lastRecord['stop'] = date('H:i:s', $checkedInputTime->getTimestamp());
            if (strtotime($lastRecord['start']) > strtotime($lastRecord['stop'])) {
                $this->io->warning('Input time is not valid.');
                $lastRecord['stop'] = date('H:i:s');
            }
        } else {
            $lastRecord['stop'] = date('H:i:s');
        }

        $lastRecord['time'] = $this->calculateTime($lastRecord['start'], $lastRecord['stop']);

        $writeResult = file_put_contents($this->path . $this->fileName, $lastRecord['stop'] . ';' . $lastRecord['time'] . ';stopped;' . PHP_EOL, FILE_APPEND);

        if (false === $writeResult) {
            throw new ZeitBuchungException('Cannot write content to record file! "' . $this->path . $this->fileName . '"', 102);
        }

        $this->io->text([
            'Stopped last record:',
            $lastRecord['message'],
            $lastRecord['start'] . ' - ' . $lastRecord['stop'],
            $lastRecord['time'],
        ]);
    }

    /**
     * starts a new record session, unstopped record sessions will be stopped before
     *
     * @param string $message
     * @param string $inputTime
     * @return void
     * @throws ZeitBuchungException
     */
    public function start(string $message, ?string $inputTime = ''): void
    {
        if ($this->checkForUnstoppedRecord()) {
            $this->stop();
            $this->io->newLine(2);
        }

        $checkedInputTime = $this->checkInputTime($inputTime);

        if (null !== $checkedInputTime) {
            $start = date('H:i:s', $checkedInputTime->getTimestamp());
        } else {
            $start = date('H:i:s');
        }

        $message = str_replace(';', ',', $message);
        $recordRow = $start . ';' . $message . ';started;';
        $writeResult = file_put_contents($this->path . $this->fileName, $recordRow, FILE_APPEND);

        if (false === $writeResult) {
            throw new ZeitBuchungException('Cannot write content to record file! "' . $this->path . $this->fileName . '"', 102);
        }

        $this->io->text([
            'Started new record:',
            $start . ' - ' . $message,
        ]);
    }

    /**
     * outputs the whole record file as table
     *
     * @return void
     */
    public function listing(): void
    {
        $table = new Table($this->io);

        $tableStyle = $table->getStyle();
        $tableStyle->setHeaderTitleFormat('<bg=black;fg=white;options=bold>%s</>');
        $table->setStyle($tableStyle);

        $table->setHeaderTitle($this->fileName);
        $table->setHeaders([
            'start',
            'stop',
            'message',
            'time',
        ]);
        $table->addRows($this->contentArray);

        $table->render();

        $this->io->newLine();
        $sum = $this->getSum();
        $this->io->text('Sum: ' . $sum['readable']);
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

        $reversedContentArray = array_reverse($this->contentArray);
        $lastRecord = $reversedContentArray[0];
        $lastRecord['stop'] = date('H:i:s');
        $lastRecord['time'] = $this->calculateTime($lastRecord['start'], $lastRecord['stop']);

        $this->io->text([
            'Active record:',
            $lastRecord['message'],
            $lastRecord['start'] . ' (' . $lastRecord['time'] . ')',
        ]);
        $this->io->newLine();

        preg_match('/^\d*/', $lastRecord['time'], $timeInMinutes);
        $sum = $this->getSum();
        $this->io->text('Sum: ' . $this->getHumanReadableSum($sum['minutes'] + $timeInMinutes[0]));
    }

    /**
     * returns the time sum of the day
     *
     * @return array
     */
    private function getSum(): array
    {
        $sumInMinutes = 0;

        foreach ($this->contentArray as $row) {
            if (!empty($row['time'])) {
                preg_match('/^\d*/', $row['time'], $timeInMinutes);
                $sumInMinutes += (int)$timeInMinutes[0];
            }
        }

        $return['minutes'] = $sumInMinutes;
        $return['readable'] = $this->getHumanReadableSum($sumInMinutes);

        return $return;
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
     */
    private function setContentArray(string $contentString): void
    {
        $contentArray = [];

        if (!empty($contentString)) {
            $rows = explode(PHP_EOL, $contentString);

            foreach ($rows as $row) {
                $rowArray = explode(';', $row);

                if (!empty($rowArray[2]) && 'started' === $rowArray[2]) {
                    $contentArray[] = [
                        'start' => !empty($rowArray[0]) ? $rowArray[0] : '',
                        'stop' => !empty($rowArray[3]) ? $rowArray[3] : '',
                        'message' => !empty($rowArray[1]) ? $rowArray[1] : '',
                        'time' => !empty($rowArray[4]) ? $rowArray[4] : '',
                    ];
                }
            }
        }

        $this->contentArray = $contentArray;
    }
}
