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
    private $start;

    /** @var DateTime */
    private $end;

    /** @var string */
    private $message;

    /** @var int */
    private $timeInMinutes;

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
     * @return DateTime
     */
    public function getEnd(): DateTime
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
}
