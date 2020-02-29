<?php

namespace ZeitBuchung\Service;

use DateTime;
use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Interfaces\SymfonyStyleInterface;

/**
 * Class DateTimeService
 *
 * @package ZeitBuchung\Service
 */
class DateTimeService implements SymfonyStyleInterface
{
    /** @var SymfonyStyle */
    private $symfonyStyle;

    /**
     * @param string $inputDate
     * @return DateTime|null
     * @throws ZeitBuchungException
     */
    public function getDateTimeObjectByInputDate(string $inputDate): ?DateTime
    {
        if (!empty($inputDate)) {
            switch (true) {
                case preg_match('/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[012])\.$/', $inputDate): // DD.MM.
                    $inputDate .= date('Y');
                    $return = $this->createDateTimeObjectByDateString($inputDate);
                    break;
                case preg_match('/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[012])\.\d{4}$/', $inputDate): // DD.MM.YYYY
                    $return = $this->createDateTimeObjectByDateString($inputDate);
                    break;
                case preg_match('/^\d{2}\-(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // YY-MM-DD
                    $return = $this->createDateTimeObjectByDateString($inputDate);
                    break;
                case preg_match('/^(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // MM-DD
                    $inputDate = date('Y') . '-' . $inputDate;
                    $return = $this->createDateTimeObjectByDateString($inputDate);
                    break;
                case preg_match('/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12]\d|3[01])$/', $inputDate): // YYYY-MM-DD
                    $return = $this->createDateTimeObjectByDateString($inputDate);
                    break;
                default:
                    $return = null;
                    $this->symfonyStyle->warning('Input date string is invalid. "' . $inputDate . '"');
                    break;
            }

            return $return;
        }

        return null;
    }

    /**
     * @param string $inputDate
     * @return DateTime
     * @throws ZeitBuchungException
     */
    private function createDateTimeObjectByDateString(string $inputDate): DateTime
    {
        try {
            return new DateTime($inputDate);
        } catch (Exception $e) {
            throw new ZeitBuchungException($e->getMessage(), 102, $e);
        }
    }

    /**
     * @param string $inputTime
     * @return DateTime|null
     * @throws ZeitBuchungException
     */
    public function getDateTimeObjectByInputTime(string $inputTime): ?DateTime
    {
        if (!empty($inputTime)) {
            switch (true) {
                case 4 === strlen($inputTime) && preg_match('/^\d\:[0-5]\d$/', $inputTime):
                    $return = $this->createDateTimeObjectByTimeString('0' . $inputTime . ':00');
                    break;
                case 5 === strlen($inputTime) && preg_match('/^([01]\d|2[0-4])\:[0-5]\d$/', $inputTime):
                    $return = $this->createDateTimeObjectByTimeString($inputTime . ':00');
                    break;
                case 7 === strlen($inputTime) && preg_match('/^\d\:[0-5]\d\:[0-5]\d$/', $inputTime):
                    $return = $this->createDateTimeObjectByTimeString('0' . $inputTime);
                    break;
                case 8 === strlen($inputTime) && preg_match('/^([01]\d|2[0-4])\:[0-5]\d\:[0-5]\d$/', $inputTime):
                    $return = $this->createDateTimeObjectByTimeString($inputTime);
                    break;
                default:
                    $return = null;
                    $this->symfonyStyle->warning('Input time string is invalid. "' . $inputTime . '"');
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
    private function createDateTimeObjectByTimeString(string $inputTime): DateTime
    {
        try {
            return new DateTime(date('Y-m-d') . ' ' . $inputTime);
        } catch (Exception $e) {
            throw new ZeitBuchungException($e->getMessage(), 101, $e);
        }
    }

    /**
     * @return SymfonyStyle|null
     */
    public function getSymfonyStyle(): ?SymfonyStyle
    {
        return $this->symfonyStyle;
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     * @return void
     */
    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): void
    {
        $this->symfonyStyle = $symfonyStyle;
    }
}
