<?php


namespace ZeitBuchung\Structure;

use DateTime;

/**
 * Class RecordStructure
 *
 * @package ZeitBuchung\Structure
 */
class RecordStructure
{
    /** @var DateTime */
    public $start;

    /** @var DateTime */
    public $end;

    /** @var string */
    public $message;

    /** @var int */
    public $timeInMinutes;

    /**
     * RecordStructure constructor.
     *
     * @param DateTime $start
     * @param null|DateTime $end
     * @param string $message
     * @param int $timeInMinutes
     */
    public function __construct(DateTime $start, ?DateTime $end, string $message, int $timeInMinutes)
    {
        $this->start = $start;
        $this->end = $end;
        $this->message = $message;
        $this->timeInMinutes = $timeInMinutes;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return void
     */
    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    /**
     * @return null|DateTime
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return void
     */
    public function setEnd(DateTime $end): void
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getTimeInMinutes(): int
    {
        return $this->timeInMinutes;
    }

    /**
     * @param int $timeInMinutes
     * @return void
     */
    public function setTimeInMinutes(int $timeInMinutes): void
    {
        $this->timeInMinutes = $timeInMinutes;
    }

    /**
     * @param null|DateTime $dateTime
     * @return string
     */
    private function getHumanReadableTime(?DateTime $dateTime): string
    {
        if (null !== $dateTime) {
            return date('H:i:s', $dateTime->getTimestamp());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getHumanReadableStartTime(): string
    {
        return $this->getHumanReadableTime($this->start);
    }

    /**
     * @return string
     */
    public function getHumanReadableEndTime(): string
    {
        return $this->getHumanReadableTime($this->end);
    }

    /**
     * @return string
     */
    public function getHumanReadableTimePeriod(): string
    {
        if (15 <= $this->timeInMinutes) {
            $sumInHours = round($this->timeInMinutes / 60, 2);
            $return = $sumInHours . 'h';
        } else {
            $return = $this->timeInMinutes . 'm';
        }

        return $return;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'start' => $this->getHumanReadableStartTime(),
            'end' => $this->getHumanReadableEndTime(),
            'message' => $this->getMessage(),
            'time' => $this->getHumanReadableTimePeriod(),
        ];
    }
}
