<?php

namespace App\Services\Attendance;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;

/** Read device exports as evidence. Never evaluate uploaded spreadsheet formulas. */
class AttendanceFileParser
{
    public function parse(string $path, string $extension): array
    {
        if (in_array(strtolower($extension), ['csv', 'txt'], true)) {
            return $this->csv($path);
        }
        $reader = IOFactory::createReaderForFile($path);
        if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Xls) $reader = new DeviceXlsReader();
        $reader->setReadDataOnly(true);
        $book = $reader->load($path);
        try {
            foreach ($book->getWorksheetIterator() as $sheet) {
                if (preg_match('/^Attend(?:ance|\.)\s*Logs$/i', trim($sheet->getTitle()))) {
                    return ['format' => 'daily-punches', 'rows' => $this->logs($sheet->toArray(null, false, false, false), $sheet->getTitle())];
                }
            }
            $rows = [];
            foreach ($book->getWorksheetIterator() as $sheet) {
                $rows = array_merge($rows, $this->monthly($sheet->toArray(null, false, false, false), $sheet->getTitle()));
            }
            if (!$rows) {
                throw new InvalidArgumentException('No supported attendance punches found. Use an attendance export, not an employee list or summary only.');
            }
            return ['format' => 'monthly-in-out', 'rows' => $rows];
        } finally {
            $book->disconnectWorksheets();
        }
    }

    private function period(array $rows): array
    {
        foreach (array_slice($rows, 0, 8) as $row) {
            foreach ($row as $cell) {
                $text = (string) $cell;
                foreach (['/\b(20\d{2})-(\d{2})-(\d{2})/', '/\b(\d{2})\/(\d{2})\/(20\d{2})/', '/\b(\d{2})\.(\d{2})\.(\d{2})/'] as $i => $pattern) {
                    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER) && count($matches) >= 2) {
                        $dates = array_map(function ($m) use ($i) {
                            return $this->date($i === 1 ? "$m[3]-$m[1]-$m[2]" : ($i === 2 ? '20'.$m[1]."-$m[2]-$m[3]" : "$m[1]-$m[2]-$m[3]"));
                        }, array_slice($matches, 0, 2));
                        return [$dates[0], $dates[1]];
                    }
                }
            }
        }
        throw new InvalidArgumentException('The report date range could not be read.');
    }

    private function date(string $value): string
    {
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException('Invalid report date: '.$value);
        }
        return $value;
    }

    private function day(string $day, array $period): string
    {
        $start = new DateTimeImmutable($period[0]);
        $end = new DateTimeImmutable($period[1]);
        for ($d = $start; $d <= $end; $d = $d->modify('+1 day')) {
            if ((string)(int)$d->format('d') === (string)(int)$day) return $d->format('Y-m-d');
        }
        throw new InvalidArgumentException('Day is outside the report period: '.$day);
    }

    private function logs(array $rows, string $sheet): array
    {
        $period = $this->period($rows);
        if ((new DateTimeImmutable($period[0]))->diff(new DateTimeImmutable($period[1]))->days > 30) {
            throw new InvalidArgumentException('Daily punch grids longer than 31 days need a full-date header.');
        }
        $days = []; $employee = null; $out = [];
        foreach ($rows as $index => $row) {
            $first = trim((string)($row[0] ?? ''));
            if (preg_match('/^ID\s*:?$/i', $first)) {
                $id = trim((string)($row[2] ?? ''));
                $name = '';
                foreach ($row as $c => $v) {
                    if (preg_match('/^Name\s*:?$/i', trim((string)$v))) {
                        for ($n = $c+1; $n < count($row); $n++) {
                            if (trim((string)$row[$n]) !== '') { $name = trim((string)$row[$n]); break; }
                        }
                    }
                }
                $employee = [$id, $name];
                continue;
            }
            $numeric = array_filter($row, fn($v) => is_numeric($v) && (int)$v >= 1 && (int)$v <= 31);
            if (count($numeric) >= 2) {
                $days = [];
                foreach ($row as $c => $v) if (is_numeric($v) && $v >= 1 && $v <= 31) $days[$c] = (int)$v;
                continue;
            }
            if (!$employee) continue;
            foreach ($days as $c => $day) {
                $text = $this->cellText($row[$c] ?? null);
                $times = $this->times($text);
                if (!$times) continue;
                $out[] = $this->record($employee, $this->day((string)$day, $period), $times, $sheet, $index+1, $c+1, 'No', ['cell_text' => $text, 'period'=>$period]);
            }
        }
        if (!$out) throw new InvalidArgumentException('No punches found in the daily attendance grid.');
        return $out;
    }

    private function monthly(array $rows, string $sheet): array
    {
        $employee = null; $period = null; $out = [];
        foreach ($rows as $r => $row) {
            $name = null; $id = null;
            foreach ($row as $v) {
                if (preg_match('/^Name\s*:\s*(.+)$/i', trim((string)$v), $m)) $name = $m[1];
                if (preg_match('/^ID\s*:\s*(.+)$/i', trim((string)$v), $m)) $id = $m[1];
            }
            if ($name !== null && $id !== null) {
                $employee = [$id, $name]; $period = $this->period([$row]);
                continue;
            }
            if (!$employee) continue;
            foreach ([0, 8] as $offset) {
                if (!preg_match('/^(\d{2})[.\/-](\d{2})$/', trim((string)($row[$offset] ?? '')), $m)) continue;
                $date = $this->day($m[2], $period);
                if ((int)substr($date,5,2) !== (int)$m[1]) throw new InvalidArgumentException('Report month mismatch.');
                $slots = array_map(fn($i) => $this->cellText($row[$offset+$i] ?? null), range(2,7));
                foreach (['No' => array_slice($slots,0,4), 'Yes' => array_slice($slots,4,2)] as $ot => $group) {
                    $times = $this->times(implode(' ', $group));
                    if (!$times) continue;
                    $record = $this->record($employee,$date,$times,$sheet,$r+1,$offset+3,$ot,['slots'=>$slots,'period'=>$period]);
                    // An OUT-only slot must never become a fabricated IN.
                    $ins = $this->times(implode(' ', $ot === 'Yes' ? [$group[0]] : [$group[0],$group[2]]));
                    $outs = $this->times(implode(' ', $ot === 'Yes' ? [$group[1]] : [$group[1],$group[3]]));
                    if (!$ins) {
                        if (count($times) === 1) $record['proposed']['log_out'] = $record['proposed']['log_in'];
                        $record['proposed']['log_in'] = null;
                    }
                    if (!$outs) $record['proposed']['log_out'] = null;
                    $out[] = $record;
                }
            }
        }
        return $out;
    }

    private function times(string $text): array
    {
        preg_match_all('/(?<!\d)([01]?\d|2[0-3]):([0-5]\d)(?::[0-5]\d)?(?!\d)/', $text, $matches, PREG_SET_ORDER);
        return array_map(fn($m) => sprintf('%02d:%s', (int)$m[1], $m[2]), $matches);
    }

    private function cellText($value): string
    {
        // Some XML exports mix strings and typed Excel time fractions in the same column.
        if ((is_int($value) || is_float($value)) && $value >= 0 && $value < 2) {
            $minutes=(int)round($value*1440)%1440;
            return sprintf('%02d:%02d',intdiv($minutes,60),$minutes%60);
        }
        return trim((string)$value);
    }

    private function record(array $employee,string $date,array $times,string $sheet,int $row,int $col,string $ot,array $raw): array
    {
        $stamps=[]; $day = new DateTimeImmutable($date); $previous=null;
        foreach ($times as $time) {
            $stamp = $day->format('Y-m-d').'T'.$time;
            if ($previous !== null && $stamp < $previous) {
                $day=$day->modify('+1 day'); $stamp=$day->format('Y-m-d').'T'.$time;
            }
            $stamps[]=$stamp; $previous=$stamp;
        }
        return ['external_id'=>$employee[0], 'external_name'=>$employee[1], 'attendance_date'=>$date,
            'raw'=>array_merge($raw,['punches'=>$times,'sheet'=>$sheet,'row'=>$row,'column'=>$col]),
            'proposed'=>['log_in'=>$stamps[0]??null,'log_out'=>count($stamps)>=2?end($stamps):null,
                'breaktime_start'=>count($stamps)===4?$stamps[1]:null,'breaktime_end'=>count($stamps)===4?$stamps[2]:null,
                'break_policy'=>count($stamps)===4?'recorded':'review','overtime_status'=>$ot,'override_default_shift'=>'No'],
            'warnings'=>array_values(array_filter([count($times)%2?'Odd number of punches: review missing or extra scans.':null,
                count($times)>4?'More than four punches: assign the working interval and break explicitly.':null,
                $day->format('Y-m-d')!==$date?'Overnight or out-of-order punches: confirm the dates.':null,
                $ot==='Yes'?'Device labels this interval as overtime. Confirm whether it is overtime or a regular night shift.':null]))];
    }

    private function csv(string $path): array
    {
        $handle = fopen($path, 'r');
        try {
            $header = fgetcsv($handle, 0, ',', '"', '');
            if (!$header) throw new InvalidArgumentException('Empty CSV.');
            $header = array_map(fn($v)=>trim(ltrim($v,"\xEF\xBB\xBF")), $header);
            foreach (['employee_no','log_date','time_in','time_out'] as $key) {
                if (!in_array($key,$header,true)) throw new InvalidArgumentException('CSV is missing column: '.$key);
            }
            $out=[]; $line=1;
            while (($values=fgetcsv($handle,0,',','"',''))!==false) {
                $line++; if ($values===[null]) continue;
                if (count($values)!==count($header)) throw new InvalidArgumentException('CSV column count mismatch on row '.$line);
                $v=array_combine($header,$values); $date=trim($v['log_date']);
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/',$date,$m)) $date=sprintf('%s-%02d-%02d',$m[3],$m[1],$m[2]);
                $this->date($date);
                $record=$this->record([$v['employee_no'],$v['employee_name']??''],$date,[],'CSV',$line,1,'No',['columns'=>$v]);
                foreach (['log_in'=>'time_in','log_out'=>'time_out','breaktime_start'=>'breaktime_start','breaktime_end'=>'breaktime_end'] as $key=>$col) {
                    $text=trim($v[$col]??'');
                    if ($text==='') continue;
                    // Preserve explicit next-day dates supplied in the CSV.
                    $parsed=(new \App\Http\Controllers\CSVImportController())->parseDateTimeSafe(preg_match('/^\d{1,2}:\d{2}/',$text)?$date.' '.$text:$text);
                    if (!$parsed) throw new InvalidArgumentException('Invalid CSV time on row '.$line);
                    $record['proposed'][$key]=$parsed->format('Y-m-d\TH:i');
                }
                $record['raw']['punches']=array_values(array_filter(array_intersect_key($v,array_flip(['time_in','breaktime_start','breaktime_end','time_out']))));
                $record['warnings']=[];
                $record['proposed']['break_policy']=!empty($v['breaktime_start'])&&!empty($v['breaktime_end'])?'recorded':'review';
                $out[]=$record;
                if (!empty($v['time_in_ot']) || !empty($v['time_out_ot'])) {
                    $ot=$record; $ot['proposed']['overtime_status']='Yes'; $ot['proposed']['break_policy']='none';
                    foreach (['log_in'=>'time_in_ot','log_out'=>'time_out_ot'] as $key=>$col) {
                        $text=trim($v[$col]??'');
                        $parsed=$text!==''?(new \App\Http\Controllers\CSVImportController())->parseDateTimeSafe(preg_match('/^\d{1,2}:\d{2}/',$text)?$date.' '.$text:$text):null;
                        $ot['proposed'][$key]=$parsed?->format('Y-m-d\TH:i');
                    }
                    $ot['proposed']['breaktime_start']=null; $ot['proposed']['breaktime_end']=null;
                    $out[]=$ot;
                }
                if (count($out)>5000) throw new InvalidArgumentException('Import up to 5,000 daily records at a time.');
            }
            if (!$out) throw new InvalidArgumentException('No CSV attendance records found.');
            return ['format'=>'standard-csv','rows'=>$out];
        } finally { fclose($handle); }
    }
}
