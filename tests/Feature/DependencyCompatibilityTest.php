<?php

namespace Tests\Feature;

use App\Http\Controllers\CSVImportController;
use App\Models\DeductionTypeModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\Csv\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class DependencyCompatibilityTest extends TestCase
{
    public function test_registered_routes_resolve_to_existing_controller_methods(): void
    {
        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getActionName();
            if ($action === 'Closure') {
                continue;
            }
            [$class, $method] = array_pad(explode('@', $action, 2), 2, '__invoke');
            $this->assertTrue(class_exists($class), $route->uri() . ': ' . $class);
            $this->assertTrue(method_exists($class, $method), $route->uri() . ': ' . $method);
        }
    }

    public function test_application_classes_can_be_autoloaded_with_preserved_audit_options(): void
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\HolidayController::class));
        $this->assertTrue(class_exists(\App\Mail\WelcomeMail::class));
        foreach (glob(app_path('Models/*Model.php')) as $file) {
            if (basename($file) === 'SiteModel.php') {
                continue;
            }
            $class = 'App\\Models\\' . basename($file, '.php');
            $defaults = (new \ReflectionClass($class))->getDefaultProperties();
            if (!isset($defaults['logAttributes'])) {
                continue;
            }
            $options = (new $class)->getActivitylogOptions();
            $this->assertSame($defaults['logAttributes'], $options->logAttributes);
            $this->assertSame($defaults['logName'], $options->logName);
            $this->assertSame($defaults['logOnlyDirty'], $options->logOnlyDirty);
        }
    }

    public function test_payroll_pages_keep_their_login_redirect(): void
    {
        $this->get('/employee')->assertRedirect('/');
        $this->get('/employee-attendance-logs')->assertRedirect('/');
    }

    public function test_existing_pdf_alias_can_render_a_view_and_download(): void
    {
        view()->addNamespace('dependency-test', __DIR__ . '/../Fixtures');
        $pdf = \PDF::loadView('dependency-test::payslip', ['employee' => 'Sample Employee', 'net' => '12,500.00'])->setPaper('a4');
        $this->assertStringStartsWith('%PDF-', $pdf->output());
        $response = $pdf->download('payslip.pdf');
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('payslip.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_csv_headers_and_existing_time_parser_still_work(): void
    {
        $csv = Reader::createFromString("employee_no,log_date,time_in,time_out\nEMP001,2026-10-26,08:00:00,17:00:00\n");
        $csv->setHeaderOffset(0);
        $row = iterator_to_array($csv)[1];
        $this->assertSame('EMP001', $row['employee_no']);
        $parser = app(CSVImportController::class);
        $this->assertSame('13:04:00', $parser->normalizeTimeString('13:04:00 PM'));
        $this->assertSame('2026-10-26 08:00:00', $parser->parseDateTimeSafe('2026-10-26 08:00:00')->format('Y-m-d H:i:s'));
    }

    public function test_spreadsheet_export_can_be_read_back(): void
    {
        $file = tempnam(storage_path('framework'), 'payroll-sheet-');
        try {
            $sheet = new Spreadsheet();
            $sheet->getActiveSheet()->setCellValue('A1', 'Employee')->setCellValue('B1', 12500);
            (new Xlsx($sheet))->save($file);
            $restored = IOFactory::load($file);
            $this->assertSame('Employee', $restored->getActiveSheet()->getCell('A1')->getValue());
            $this->assertSame(12500, $restored->getActiveSheet()->getCell('B1')->getValue());
        } finally {
            if (is_file($file)) unlink($file);
        }
    }

    public function test_model_activity_options_and_session_causer_are_preserved(): void
    {
        Schema::create('teves_payroll_deduction_type_table', function (Blueprint $table) {
            $table->increments('deduction_id');
            $table->string('deduction_description');
            $table->integer('created_by_user_idx')->nullable();
            $table->integer('updated_by_user_idx')->nullable();
            $table->timestamps();
        });
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->timestamps();
        });
        $migration = require database_path('migrations/2026_10_27_000001_add_activitylog_v4_metadata.php');
        $migration->up();
        $migration->up();
        $this->assertTrue(Schema::hasColumns('activity_log', ['event', 'batch_uuid']));
        session(['loginID' => 42]);
        $model = DeductionTypeModel::create(['deduction_description' => 'Sample deduction']);
        $model->update(['deduction_description' => 'Updated deduction']);
        $events = \Spatie\Activitylog\Models\Activity::orderBy('id')->get();
        $this->assertCount(2, $events);
        $this->assertSame('Deduction Type Details', $events[1]->log_name);
        $this->assertSame('updated', $events[1]->event);
        $this->assertSame(42, (int) $events[1]->causer_id);
        $this->assertSame('Updated deduction', $events[1]->properties['attributes']['deduction_description']);
        $this->assertSame('Sample deduction', $events[1]->properties['old']['deduction_description']);
    }
}
