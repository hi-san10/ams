<?php

namespace App\Services;

class CsvService
{
    public function format($attendances) :string
    {
        $head = ['日付', '出勤', '退勤', '休憩', '合計'];

        $temps = [];
        array_push($temps, $head);

        foreach ($attendances as $attendance) {
            $temp = [
                $attendance->date->format('Y/m/d'),
                $attendance->start_time->format('H:i'),
                $attendance->end_time->format('H:i'),
                substr($attendance->total_rest_time, 0, 5),
                substr($attendance->total_work_time, 0, 5),
            ];
            array_push($temps, $temp);
        }
        $f = fopen('php://temp', 'r+b');
        foreach ($temps as $temp) {
            fputcsv($f, $temp);
        }

        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);
        $csv = str_replace(PHP_EOL, "\r\n", $csv);
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');

        return $csv;
    }
}


