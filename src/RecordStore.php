<?php

namespace ZeitBuchung\Helper;

use DateTime;
use ZeitBuchung\Style\CustomStyle;

class RecordStore {

    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getRecordsInTimerange(DateTime $from, DateTime $to, CustomStyle $io)
    {
        $recordFiles = scandir($this->path);
        $recordedTimes = [];
        foreach($recordFiles as $file) {
            $parsedDate = $this->parseFilenameToDate($file);
            if($parsedDate != null) {
                $recordedTimes[] = $parsedDate;
            }
        }

        $recordedTimes = array_filter($recordedTimes, function (DateTime $time) use ($from, $to) {
            return $time >= $from && $time <= $to;
        });

        $recordedTimes = array_map(function (DateTime $date) use ($io) { 
            $file = new RecordFile($io, $date->format('Ymd').'.json');
            return $file->getContentArray();
        }, $recordedTimes);

        return array_merge(...$recordedTimes);
    }

    private function parseFilenameToDate(string $filename): ?DateTime
    {
        $matches = [];
        preg_match("/(\d{4})(\d{2})(\d{2})\.json/", $filename, $matches);
        if(count($matches) > 0) {
            return new DateTime($matches[1]."-".$matches[2]."-".$matches[3]);
        }

        return null;
    }

}