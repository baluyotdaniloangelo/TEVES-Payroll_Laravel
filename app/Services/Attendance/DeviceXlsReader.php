<?php

namespace App\Services\Attendance;

use PhpOffice\PhpSpreadsheet\Reader\Xls;

/** Some device exports have malformed document metadata but valid attendance cells. */
class DeviceXlsReader extends Xls
{
    protected function readSummaryInformation(): void {}
    protected function readDocumentSummaryInformation(): void {}
}
