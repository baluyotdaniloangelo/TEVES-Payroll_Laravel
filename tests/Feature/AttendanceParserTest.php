<?php

namespace Tests\Feature;

use App\Services\Attendance\AttendanceFileParser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class AttendanceParserTest extends TestCase
{
    public function test_daily_grid_uses_the_header_day_and_keeps_an_incomplete_record(): void
    {
        $book=new Spreadsheet();$s=$book->getActiveSheet();$s->setTitle('Attend. Logs');
        $s->fromArray([['Attend. Logs'],['Date :',null,'2026-10-19 ~ 2026-10-25'],[19,20,21,22,23,24,25],['ID :',null,'00004',null,null,null,null,'Name :',null,'Sample'],["08:00\n17:00",'08:11']]);
        $path=tempnam(sys_get_temp_dir(),'attendance-');
        try {
            (new Xlsx($book))->save($path);$data=app(AttendanceFileParser::class)->parse($path,'xlsx');
            $this->assertCount(2,$data['rows']);$this->assertSame('00004',$data['rows'][0]['external_id']);
            $this->assertSame('2026-10-19',$data['rows'][0]['attendance_date']);$this->assertNull($data['rows'][1]['proposed']['log_out']);
            $this->assertSame('review',$data['rows'][0]['proposed']['break_policy']);
            $this->assertSame(['2026-10-19','2026-10-25'],$data['rows'][0]['raw']['period']);
        } finally {unlink($path);$book->disconnectWorksheets();}
    }

    public function test_monthly_both_halves_typed_time_and_device_ot_are_preserved(): void
    {
        $book=new Spreadsheet();$s=$book->getActiveSheet();
        $s->fromArray([['Attendance Summary'],['Name:Night Employee',null,'ID:00007',null,'Date:26.10.01～26.10.31'],
            ['10.01','Thu',null,null,null,null,'19:52','06:35','10.17','Sat',1.5,'17:00']]);
        $path=tempnam(sys_get_temp_dir(),'attendance-');
        try {
            (new Xlsx($book))->save($path);$data=app(AttendanceFileParser::class)->parse($path,'xlsx');
            $this->assertCount(2,$data['rows']);$ot=$data['rows'][0];$this->assertSame('Yes',$ot['proposed']['overtime_status']);
            $this->assertSame('2026-10-02T06:35',$ot['proposed']['log_out']);
            $this->assertSame('2026-10-17T12:00',$data['rows'][1]['proposed']['log_in']);
            $this->assertSame(['2026-10-01','2026-10-31'],$ot['raw']['period']);
        } finally {unlink($path);$book->disconnectWorksheets();}
    }

    public function test_explicit_out_only_does_not_become_a_time_in(): void
    {
        $book=new Spreadsheet();$s=$book->getActiveSheet();
        $s->fromArray([['Name:Sample','ID:4','Date:26.10.01～26.10.31'],['10.01','Thu',null,'13:00']]);
        $path=tempnam(sys_get_temp_dir(),'attendance-');
        try{(new Xlsx($book))->save($path);$row=app(AttendanceFileParser::class)->parse($path,'xlsx')['rows'][0];$this->assertNull($row['proposed']['log_in']);$this->assertSame('2026-10-01T13:00',$row['proposed']['log_out']);}
        finally{unlink($path);$book->disconnectWorksheets();}
    }
}
