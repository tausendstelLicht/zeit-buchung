<?php

namespace ZeitBuchung\Service;

use DateTime;
use Exception;
use ZeitBuchung\Exception\ZeitBuchungException;

/**
 * Class FileService
 *
 * @package ZeitBuchung\Service
 */
class FileService
{
    /** @var string */
    private $fileName;

    /** @var string */
    private $savePath;

    /** @var string */
    private $dateDirectory;

    /**
     * FileService constructor.
     *
     * @param string $filePath
     * @param DateTime $date
     * @throws Exception
     */
    public function __construct(string $filePath, DateTime $date)
    {
        $this->setSavePath($filePath);
        $this->setFileNameByDate($date);
        $this->setDateDirectory($date);
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param DateTime $date
     * @return void
     */
    public function setFileNameByDate(DateTime $date): void
    {
        $this->fileName = $date->format('Ymd') . '.json';
    }

    /**
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->savePath;
    }

    /**
     * @param string $savePath
     * @return void
     * @throws ZeitBuchungException
     */
    public function setSavePath(string $savePath): void
    {
        if (!is_dir($savePath)) {
            throw new ZeitBuchungException(sprintf('Given save path does not exist! "%s"', $savePath));
        }

        $this->savePath = $savePath;
    }

    /**
     * @return string
     */
    public function getDateDirectory(): string
    {
        return $this->dateDirectory;
    }

    /**
     * set today, if $date is null
     *
     * @param DateTime|null $date
     * @return void
     * @throws Exception
     */
    public function setDateDirectory(?DateTime $date): void
    {
        if (null === $date) {
            $date = new DateTime();
        }

        $dateString = $date->format('Y/m');
        $dateString = str_replace('/', DIRECTORY_SEPARATOR, $dateString);
        $this->dateDirectory = $dateString;
    }

    /**
     * @return string
     */
    public function getFullSavePath(): string
    {
        return $this->savePath . DIRECTORY_SEPARATOR . $this->dateDirectory;
    }

    /**
     * @return bool
     */
    public function fileExists(): bool
    {
        $return = false;

        if (is_file($this->getFullSavePath() . DIRECTORY_SEPARATOR . $this->fileName)) {
            $return = true;
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function dateDirectoryExists(): bool
    {
        $return = false;

        if (is_dir($this->getFullSavePath())) {
            $return = true;
        }

        return $return;
    }

    /**
     * @return void
     * @throws ZeitBuchungException
     */
    public function createDateDirectory(): void
    {
        if (!mkdir($concurrentDirectory = $this->getFullSavePath(), 0775, true) && !is_dir($concurrentDirectory)) {
            throw new ZeitBuchungException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    /**
     * @return string
     * @throws ZeitBuchungException
     */
    public function getFileContent(): string
    {
        $content = file_get_contents($this->getFullSavePath() . DIRECTORY_SEPARATOR . $this->fileName);

        if (false === $content) {
            throw new ZeitBuchungException('Cannot get file contents!');
        }

        return $content;
    }

    /**
     * @param string $content
     * @return void
     * @throws ZeitBuchungException
     */
    public function saveContentToFile(string $content): void
    {
        $saved = file_put_contents($this->getFullSavePath() . DIRECTORY_SEPARATOR . $this->fileName, $content);

        if (false === $saved) {
            throw new ZeitBuchungException('Cannot save contents to file!');
        }
    }
}
