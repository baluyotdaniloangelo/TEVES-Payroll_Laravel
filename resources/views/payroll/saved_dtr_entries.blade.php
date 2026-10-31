@extends('layouts.layout')

@section('content')
@php
    $formatTime = static fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('h:i A') : '—';
    $formatDateTimeLocal = static fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i') : '';
    $canManageDtr = in_array($data->user_type, ['Admin','Supervisor','Accounting_Staff'], true);
    $latestPeriod = $periods->first();
    $latestPeriodKey = $latestPeriod ? $latestPeriod->cutoff_start.'|'.$latestPeriod->cutoff_end : '';
    $isHistorical = $period !== '' && $latestPeriodKey !== '' && $period !== $latestPeriodKey;
    [$cutoffStart, $cutoffEnd] = array_pad(explode('|', $period, 2), 2, null);
@endphp
<div class="page-wrapper">
    <div class="content container-fluid saved-dtr-page">
        <div class="page-header d-flex align-items-center justify-content-between flex-wrap">
            <div><h3 class="page-title">Saved DTR Entries</h3><p class="text-muted mb-0">View saved attendance by employee and cutoff. Daily logs stay hidden until you open a cutoff.</p></div>
            <a class="btn btn-outline-primary mt-2" href="{{ route('attendance-import.index') }}">Open DTR Plotting</a>
        </div>

        @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif

        <form method="get" action="{{ route('attendance-import.saved') }}" class="saved-dtr-filters card card-body" data-saved-dtr-navigation>
            <div class="row align-items-end g-3">
                <div class="col-md-5">
                    <label for="saved-dtr-employee" class="form-label">Employee</label>
                    <input class="form-control" id="saved-dtr-employee" name="employee" value="{{ $employeeSearch }}" placeholder="Name or employee number">
                </div>
                <div class="col-md-5">
                    <label for="saved-dtr-branch" class="form-label">Branch</label>
                    <select class="form-control" id="saved-dtr-branch" name="branch_id">
                        <option value="">All accessible branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->branch_id }}" @selected((int)$branchId === (int)$branch->branch_id)>{{ $branch->branch_code }} · {{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Apply</button></div>
            </div>
        </form>

        <div id="saved-dtr-results" aria-live="polite">
        @if($periods->isNotEmpty())
            <div class="saved-dtr-period-bar">
                <div class="saved-dtr-current-period">
                    <span class="saved-dtr-eyebrow">{{ $isHistorical ? 'Selected previous cutoff' : 'Latest cutoff' }}</span>
                    <strong>{{ \Carbon\Carbon::parse($cutoffStart)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($cutoffEnd)->format('M d, Y') }}</strong>
                    @unless($isHistorical)<small>Older cutoffs are hidden by default.</small>@endunless
                </div>
                @if($periods->count() > 1)
                    <details class="saved-dtr-history" @if($isHistorical) open @endif>
                        <summary>Previous cutoffs</summary>
                        <form method="get" action="{{ route('attendance-import.saved') }}" class="saved-dtr-history-form" data-saved-dtr-navigation>
                            <input type="hidden" name="branch_id" value="{{ $branchId }}">
                            <input type="hidden" name="employee" value="{{ $employeeSearch }}">
                            <label for="saved-dtr-period" class="form-label">Choose a cutoff to view</label>
                            <div class="saved-dtr-history-controls">
                                <select class="form-control" id="saved-dtr-period" name="period">
                                    @foreach($periods as $availablePeriod)
                                        @php $periodKey=$availablePeriod->cutoff_start.'|'.$availablePeriod->cutoff_end; @endphp
                                        <option value="{{ $periodKey }}" @selected($period === $periodKey)>
                                            {{ \Carbon\Carbon::parse($availablePeriod->cutoff_start)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($availablePeriod->cutoff_end)->format('M d, Y') }}
                                            @if($periodKey === $latestPeriodKey) · Latest @endif
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-primary" type="submit">View cutoff</button>
                            </div>
                        </form>
                    </details>
                @endif
            </div>
        @endif

        <div class="saved-dtr-summary text-muted mb-3">
            {{ $employeeGroups->count() }} employees · {{ $employeeGroups->sum(fn($group) => $group->count()) }} entries in this cutoff
            @if($isHistorical)<span class="saved-dtr-history-notice">Showing a previous cutoff selected from history.</span>@endif
        </div>

        @forelse($employeeGroups as $groupKey => $employeeEntries)
            @php
                $employee=$employeeEntries->first();
                $dateCount=$employeeEntries->pluck('attendance_date')->unique()->count();
                $postedCount=$employeeEntries->where('status','saved')->count();
                $awaitingCount=$employeeEntries->where('status','employee_pending')->count();
                $submittedCount=$employeeEntries->where('status','employee_submitted')->count();
                $failedImports=$employeeEntries->where('status','employee_pending')->pluck('import_id')->unique()
                    ->filter(fn($importId)=>$failedNotices->has($importId.'|'.$employee->employee_idx));
            @endphp
            <section class="saved-dtr-employee">
                <header class="saved-dtr-employee-heading">
                    <div class="saved-dtr-person">
                        <span class="saved-dtr-avatar" aria-hidden="true">{{ strtoupper(substr($employee->employee_name ?: $employee->external_name ?: 'E', 0, 1)) }}</span>
                        <div>
                            <strong>{{ $employee->employee_name ?: $employee->external_name ?: 'Employee '.$employee->employee_idx }}</strong>
                            <small>{{ $employee->employee_number ?: 'No employee number' }} · {{ $employee->branch_code }} — {{ $employee->branch_name }}</small>
                        </div>
                    </div>
                    <div class="saved-dtr-employee-tools">
                        @foreach($failedImports as $failedImport)
                            <form method="post" action="{{ route('attendance-import.resend-email',[$failedImport,$employee->employee_idx]) }}" class="saved-dtr-resend-form">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning" type="submit">Resend DTR email</button>
                            </form>
                        @endforeach
                        <div class="saved-dtr-employee-count"><strong>{{ $dateCount }} DTR dates</strong><span>in this cutoff</span></div>
                    </div>
                </header>

                <div class="table-responsive saved-dtr-cutoff-wrap">
                    <table class="table saved-dtr-cutoff-table">
                        <thead><tr><th>Cutoff</th><th>Coverage</th><th>DTR dates</th><th>Status</th><th>Details</th></tr></thead>
                        <tbody><tr>
                            <td data-label="Cutoff"><strong>{{ $isHistorical ? 'Previous cutoff' : 'Latest cutoff' }}</strong></td>
                            <td data-label="Coverage">{{ \Carbon\Carbon::parse($cutoffStart)->format('M d') }} – {{ \Carbon\Carbon::parse($cutoffEnd)->format('M d, Y') }}</td>
                            <td data-label="DTR dates">{{ $dateCount }} date{{ $dateCount === 1 ? '' : 's' }}</td>
                            <td data-label="Status">
                                <div class="saved-dtr-status-list">
                                    @if($postedCount)<span class="saved-dtr-status is-posted">{{ $postedCount }} posted</span>@endif
                                    @if($awaitingCount)<span class="saved-dtr-status is-pending">{{ $awaitingCount }} awaiting employee</span>@endif
                                    @if($submittedCount)<span class="saved-dtr-status is-submitted">{{ $submittedCount }} submitted</span>@endif
                                </div>
                            </td>
                            <td data-label="Details"><span class="saved-dtr-open-hint">Open daily logs in a review window</span></td>
                        </tr></tbody>
                    </table>
                </div>

                <div class="saved-dtr-daily">
                    <button class="saved-dtr-daily-trigger" type="button" data-saved-dtr-open="saved-dtr-review-{{ $loop->index }}">
                        <span>View daily logs</span><small>{{ $dateCount }} date{{ $dateCount === 1 ? '' : 's' }}</small><span aria-hidden="true">＋</span>
                    </button>
                    <dialog class="saved-dtr-review-dialog" id="saved-dtr-review-{{ $loop->index }}" aria-labelledby="saved-dtr-review-title-{{ $loop->index }}">
                        <header class="saved-dtr-review-heading">
                            <div>
                                <span class="saved-dtr-eyebrow">DTR review</span>
                                <h4 id="saved-dtr-review-title-{{ $loop->index }}">{{ $employee->employee_name ?: $employee->external_name ?: 'Employee '.$employee->employee_idx }}</h4>
                                <p>{{ $employee->employee_number ?: 'No employee number' }} · {{ $employee->branch_code }} · {{ \Carbon\Carbon::parse($cutoffStart)->format('M d') }} – {{ \Carbon\Carbon::parse($cutoffEnd)->format('M d, Y') }}</p>
                            </div>
                            <button class="btn-close" type="button" aria-label="Close DTR review" data-saved-dtr-close></button>
                        </header>
                        <div class="saved-dtr-review-status saved-dtr-status-list">
                            @if($postedCount)<span class="saved-dtr-status is-posted">{{ $postedCount }} posted</span>@endif
                            @if($awaitingCount)<span class="saved-dtr-status is-pending">{{ $awaitingCount }} awaiting employee</span>@endif
                            @if($submittedCount)<span class="saved-dtr-status is-submitted">{{ $submittedCount }} submitted</span>@endif
                        </div>
                        <p class="saved-dtr-review-help">Review each date. Edit a posted entry, send it to the employee for correction, or complete a pending entry as admin.</p>
                    <div class="table-responsive saved-dtr-log-wrap">
                        <table class="table table-hover saved-dtr-log-table">
                            <thead><tr><th>Date</th><th>Time in</th><th>Break out</th><th>Break in</th><th>Time out</th><th>Work hours</th><th>Source</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                            @foreach($employeeEntries as $entry)
                                @php
                                    $review=json_decode($entry->review,true) ?: [];
                                    $isCorrection=!empty($review['allow_employee_correction']);
                                    $proposal=$review['final_proposal']??null;
                                    if(!$proposal && $entry->status === 'employee_submitted')$proposal=$review['employee_proposal']??null;
                                    if(!$proposal)$proposal=$review['admin_proposal']??(json_decode($entry->proposed??'[]',true)?:[]);
                                    $proposal=is_array($proposal)?$proposal:[];
                                    $breakPolicy=$proposal['break_policy']??'';
                                    $workHours=match($entry->log_type??null){
                                        'Regular'=>$entry->total_regular_hours,
                                        'RegularOT'=>$entry->total_regular_overtime_hours,
                                        'RestDay'=>$entry->total_restday_hours,
                                        'RestDayOT'=>$entry->total_restday_overtime_hours,
                                        default=>null,
                                    };
                                @endphp
                                <tr>
                                    <td data-label="Date"><strong>{{ $entry->attendance_date ? \Carbon\Carbon::parse($entry->attendance_date)->format('M d, Y') : '—' }}</strong></td>
                                    <td data-label="Time in">{{ $formatTime($entry->log_in ?: ($proposal['log_in'] ?? null)) }}</td>
                                    <td data-label="Break out">{{ $breakPolicy === 'none' ? 'No break' : ($breakPolicy === 'recorded' ? $formatTime($entry->breaktime_start ?: ($proposal['breaktime_start'] ?? null)) : '—') }}</td>
                                    <td data-label="Break in">{{ $breakPolicy === 'recorded' ? $formatTime($entry->breaktime_end ?: ($proposal['breaktime_end'] ?? null)) : '—' }}</td>
                                    <td data-label="Time out">{{ $formatTime($entry->log_out ?: ($proposal['log_out'] ?? null)) }}</td>
                                    <td data-label="Work hours">{{ $workHours===null ? '—' : number_format((float)$workHours,2).' hrs' }}</td>
                                    <td data-label="Source"><small>{{ $entry->device_name }}<br>{{ $entry->filename }}</small></td>
                                    <td data-label="Status">
                                        @if($entry->status === 'saved')
                                            @if($entry->cutoff_idx !== null)
                                                <span class="saved-dtr-status is-locked">Payroll locked</span>
                                            @else
                                                <span class="saved-dtr-status is-posted">Posted</span>
                                            @endif
                                        @elseif($entry->status === 'employee_submitted')
                                            <span class="saved-dtr-status is-submitted">Submitted to payroll</span>
                                        @elseif($isCorrection)
                                            <span class="saved-dtr-status is-returned">Returned for correction</span>
                                        @elseif($entry->status === 'employee_pending')
                                            <span class="saved-dtr-status is-pending">Awaiting employee</span>
                                        @else
                                            <span class="saved-dtr-status is-pending">Awaiting employee</span>
                                        @endif
                                    </td>
                                    <td data-label="Action">
                                        @if($canManageDtr && $entry->status === 'saved' && $entry->cutoff_idx === null)
                                            <div class="saved-dtr-actions">
                                                <button class="btn btn-sm btn-outline-primary" type="button" data-saved-dtr-open="saved-dtr-dialog-{{ $entry->saved_log_id }}">Edit</button>
                                                <form method="post" action="{{ route('attendance-import.return-log',$entry->saved_log_id) }}" class="saved-dtr-return-form">
                                                    @csrf
                                                    <input type="hidden" name="period" value="{{ $period }}"><input type="hidden" name="branch_id" value="{{ $branchId }}"><input type="hidden" name="employee" value="{{ $employeeSearch }}">
                                                    <button class="btn btn-sm btn-outline-warning" type="submit">Send for correction</button>
                                                </form>
                                            </div>
                                        @elseif($canManageDtr && $entry->status === 'employee_pending')
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-saved-dtr-open="saved-dtr-pending-dialog-{{ $entry->import_row_id }}">Admin edit &amp; post</button>
                                        @elseif($canManageDtr && $entry->status === 'employee_submitted')
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-saved-dtr-open="saved-dtr-submitted-dialog-{{ $entry->import_row_id }}">Review submission</button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                        <footer class="saved-dtr-review-footer">
                            <span>Showing {{ $dateCount }} date{{ $dateCount === 1 ? '' : 's' }} in this cutoff</span>
                            <button class="btn btn-light" type="button" data-saved-dtr-close>Close</button>
                        </footer>
                    </dialog>
                    @if($canManageDtr)
                        @foreach($employeeEntries->where('status','saved') as $entry)
                            @if($entry->cutoff_idx === null && $entry->saved_log_id)
                                @php
                                    $review=json_decode($entry->review,true)?:[];
                                    $savedProposal=$review['final_proposal']??$review['admin_proposal']??[];
                                    $defaultBreakPolicy=$savedProposal['break_policy']??(
                                        $entry->breaktime_start===$entry->log_in && $entry->breaktime_end===$entry->log_in ? 'none' : 'recorded'
                                    );
                                    $isOldEdit=(int)old('saved_dtr_row')===(int)$entry->saved_log_id;
                                    $editValue=static fn($key,$fallback)=>$isOldEdit?old($key,$fallback):$fallback;
                                    $editBreakPolicy=$isOldEdit?old('break_policy',$defaultBreakPolicy):$defaultBreakPolicy;
                                @endphp
                                <dialog class="saved-dtr-edit-dialog" id="saved-dtr-dialog-{{ $entry->saved_log_id }}" data-reopen="{{ $isOldEdit?'true':'false' }}">
                                    <form method="post" action="{{ route('attendance-import.update-log',$entry->saved_log_id) }}" class="saved-dtr-edit-form">
                                        @csrf
                                        <input type="hidden" name="period" value="{{ $period }}"><input type="hidden" name="branch_id" value="{{ $branchId }}"><input type="hidden" name="employee" value="{{ $employeeSearch }}">
                                        <input type="hidden" name="saved_dtr_row" value="{{ $entry->saved_log_id }}">
                                        <header class="saved-dtr-edit-heading">
                                            <div><span class="saved-dtr-eyebrow">Admin edit</span><h4>Edit DTR entry</h4><p>{{ $employee->employee_name ?: $employee->external_name }} · {{ \Carbon\Carbon::parse($entry->attendance_date)->format('M d, Y') }}</p></div>
                                            <button class="btn-close" type="button" aria-label="Close" data-saved-dtr-close></button>
                                        </header>
                                        @if($isOldEdit && $errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
                                        <div class="saved-dtr-edit-grid">
                                            <div><label for="saved-dtr-in-{{ $entry->saved_log_id }}">Time in</label><input class="form-control" id="saved-dtr-in-{{ $entry->saved_log_id }}" type="datetime-local" name="log_in" value="{{ $editValue('log_in',$formatDateTimeLocal($entry->log_in)) }}" required></div>
                                            <div><label for="saved-dtr-out-{{ $entry->saved_log_id }}">Time out</label><input class="form-control" id="saved-dtr-out-{{ $entry->saved_log_id }}" type="datetime-local" name="log_out" value="{{ $editValue('log_out',$formatDateTimeLocal($entry->log_out)) }}" required></div>
                                            <div><label for="saved-dtr-break-policy-{{ $entry->saved_log_id }}">Break treatment</label><select class="form-control" id="saved-dtr-break-policy-{{ $entry->saved_log_id }}" name="break_policy" data-saved-dtr-break-policy required><option value="none" @selected($editBreakPolicy==='none')>No break</option><option value="recorded" @selected($editBreakPolicy==='recorded')>Recorded break</option></select></div>
                                            <div><label for="saved-dtr-shift-{{ $entry->saved_log_id }}">Shift treatment</label><select class="form-control" id="saved-dtr-shift-{{ $entry->saved_log_id }}" name="override_default_shift" required><option value="No" @selected($editValue('override_default_shift',$entry->override_shift??'No')==='No')>Use employee shift</option><option value="Yes" @selected($editValue('override_default_shift',$entry->override_shift??'No')==='Yes')>Disregard shift timing</option></select><small class="text-muted">Attendance type: {{ str_ends_with(strtolower($entry->log_type),'ot')?'Overtime':'Regular / rest day' }}</small></div>
                                            <div class="saved-dtr-edit-break-fields" data-saved-dtr-break-fields @if($editBreakPolicy!=='recorded') hidden @endif>
                                                <div><label for="saved-dtr-break-out-{{ $entry->saved_log_id }}">Break out</label><input class="form-control" id="saved-dtr-break-out-{{ $entry->saved_log_id }}" type="datetime-local" name="breaktime_start" value="{{ $editValue('breaktime_start',$formatDateTimeLocal($entry->breaktime_start)) }}" @required($editBreakPolicy==='recorded')></div>
                                                <div><label for="saved-dtr-break-in-{{ $entry->saved_log_id }}">Break in</label><input class="form-control" id="saved-dtr-break-in-{{ $entry->saved_log_id }}" type="datetime-local" name="breaktime_end" value="{{ $editValue('breaktime_end',$formatDateTimeLocal($entry->breaktime_end)) }}" @required($editBreakPolicy==='recorded')></div>
                                            </div>
                                        </div>
                                        <footer class="saved-dtr-edit-actions"><span>Payroll will recalculate work hours and pay from these punches.</span><button class="btn btn-primary" type="submit">Save DTR changes</button></footer>
                                    </form>
                                </dialog>
                            @endif
                        @endforeach
                        @foreach($employeeEntries->where('status','employee_pending') as $entry)
                            @php
                                $review=json_decode($entry->review,true)?:[];
                                $pendingProposal=$review['admin_proposal']??(json_decode($entry->proposed??'[]',true)?:[]);
                                $pendingPolicy=in_array($pendingProposal['break_policy']??'', ['none','recorded'],true)?$pendingProposal['break_policy']:'';
                                $isOldPending=(int)old('pending_dtr_row')===(int)$entry->import_row_id;
                                $pendingValue=static fn($key,$fallback)=>$isOldPending?old($key,$fallback):$fallback;
                                $editPendingPolicy=$isOldPending?old('break_policy',$pendingPolicy):$pendingPolicy;
                            @endphp
                            <dialog class="saved-dtr-edit-dialog" id="saved-dtr-pending-dialog-{{ $entry->import_row_id }}" data-reopen="{{ $isOldPending?'true':'false' }}">
                                <form method="post" action="{{ route('attendance-import.complete-pending',$entry->import_row_id) }}" class="saved-dtr-edit-form">
                                    @csrf
                                    <input type="hidden" name="period" value="{{ $period }}"><input type="hidden" name="branch_id" value="{{ $branchId }}"><input type="hidden" name="employee" value="{{ $employeeSearch }}">
                                    <input type="hidden" name="version" value="{{ $entry->version }}"><input type="hidden" name="pending_dtr_row" value="{{ $entry->import_row_id }}">
                                    <header class="saved-dtr-edit-heading">
                                        <div><span class="saved-dtr-eyebrow">Admin completion</span><h4>Complete and post DTR</h4><p>{{ $employee->employee_name ?: $employee->external_name }} · {{ \Carbon\Carbon::parse($entry->attendance_date)->format('M d, Y') }}</p></div>
                                        <button class="btn-close" type="button" aria-label="Close" data-saved-dtr-close></button>
                                    </header>
                                    @if($isOldPending && $errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
                                    <div class="saved-dtr-edit-grid">
                                        <div><label for="saved-dtr-pending-in-{{ $entry->import_row_id }}">Time in</label><input class="form-control" id="saved-dtr-pending-in-{{ $entry->import_row_id }}" type="datetime-local" name="log_in" value="{{ $pendingValue('log_in',$formatDateTimeLocal($pendingProposal['log_in']??null)) }}" required></div>
                                        <div><label for="saved-dtr-pending-out-{{ $entry->import_row_id }}">Time out</label><input class="form-control" id="saved-dtr-pending-out-{{ $entry->import_row_id }}" type="datetime-local" name="log_out" value="{{ $pendingValue('log_out',$formatDateTimeLocal($pendingProposal['log_out']??null)) }}" required></div>
                                        <div><label for="saved-dtr-pending-break-policy-{{ $entry->import_row_id }}">Break treatment</label><select class="form-control" id="saved-dtr-pending-break-policy-{{ $entry->import_row_id }}" name="break_policy" data-saved-dtr-break-policy required><option value="" disabled @selected($editPendingPolicy==='')>Choose break treatment</option><option value="none" @selected($editPendingPolicy==='none')>No break</option><option value="recorded" @selected($editPendingPolicy==='recorded')>Recorded break</option></select></div>
                                        <div><label for="saved-dtr-pending-shift-{{ $entry->import_row_id }}">Shift treatment</label><select class="form-control" id="saved-dtr-pending-shift-{{ $entry->import_row_id }}" name="override_default_shift" required><option value="No" @selected($pendingValue('override_default_shift',$pendingProposal['override_default_shift']??'No')==='No')>Use employee shift</option><option value="Yes" @selected($pendingValue('override_default_shift',$pendingProposal['override_default_shift']??'No')==='Yes')>Disregard shift timing</option></select><small class="text-muted">Attendance type: {{ ($pendingProposal['overtime_status']??'No')==='Yes'?'Overtime':'Regular / rest day' }}</small></div>
                                        <div class="saved-dtr-edit-break-fields" data-saved-dtr-break-fields @if($editPendingPolicy!=='recorded') hidden @endif>
                                            <div><label for="saved-dtr-pending-break-out-{{ $entry->import_row_id }}">Break out</label><input class="form-control" id="saved-dtr-pending-break-out-{{ $entry->import_row_id }}" type="datetime-local" name="breaktime_start" value="{{ $pendingValue('breaktime_start',$formatDateTimeLocal($pendingProposal['breaktime_start']??null)) }}" @required($editPendingPolicy==='recorded')></div>
                                            <div><label for="saved-dtr-pending-break-in-{{ $entry->import_row_id }}">Break in</label><input class="form-control" id="saved-dtr-pending-break-in-{{ $entry->import_row_id }}" type="datetime-local" name="breaktime_end" value="{{ $pendingValue('breaktime_end',$formatDateTimeLocal($pendingProposal['breaktime_end']??null)) }}" @required($editPendingPolicy==='recorded')></div>
                                        </div>
                                    </div>
                                    <footer class="saved-dtr-edit-actions"><span>Saving will post and approve this DTR; payroll will recalculate hours and pay.</span><button class="btn btn-primary" type="submit">Save &amp; post DTR</button></footer>
                                </form>
                            </dialog>
                        @endforeach
                        @foreach($employeeEntries->where('status','employee_submitted') as $entry)
                            @php
                                $submittedReview=json_decode($entry->review,true)?:[];
                                $submittedProposal=$submittedReview['employee_proposal']??[];
                            @endphp
                            <dialog class="saved-dtr-edit-dialog" id="saved-dtr-submitted-dialog-{{ $entry->import_row_id }}">
                                <div class="saved-dtr-edit-form">
                                    <header class="saved-dtr-edit-heading">
                                        <div><span class="saved-dtr-eyebrow">Employee submission</span><h4>Review submitted DTR</h4><p>{{ $employee->employee_name ?: $employee->external_name }} · {{ \Carbon\Carbon::parse($entry->attendance_date)->format('M d, Y') }}</p></div>
                                        <button class="btn-close" type="button" aria-label="Close" data-saved-dtr-close></button>
                                    </header>
                                    <div class="saved-dtr-submitted-grid">
                                        <div><span>Time in</span><strong>{{ $formatTime($submittedProposal['log_in']??null) }}</strong></div>
                                        <div><span>Time out</span><strong>{{ $formatTime($submittedProposal['log_out']??null) }}</strong></div>
                                        <div><span>Break treatment</span><strong>{{ ($submittedProposal['break_policy']??'')==='none'?'No break':'Recorded break' }}</strong></div>
                                        <div><span>Break out / in</span><strong>{{ $formatTime($submittedProposal['breaktime_start']??null) }} / {{ $formatTime($submittedProposal['breaktime_end']??null) }}</strong></div>
                                        <div><span>Shift treatment</span><strong>{{ ($submittedProposal['override_default_shift']??'No')==='Yes'?'Disregard shift timing':'Use employee shift' }}</strong></div>
                                        <div><span>Employee note</span><strong>{{ $submittedReview['employee_note']??'—' }}</strong></div>
                                    </div>
                                    <div class="saved-dtr-decision-actions">
                                        <form method="post" action="{{ route('attendance-import.approve',[$entry->import_id,$entry->import_row_id]) }}" class="saved-dtr-approve-form">
                                            @csrf
                                            <button class="btn btn-primary" type="submit">Approve &amp; post</button>
                                        </form>
                                        <form method="post" action="{{ route('attendance-import.return-submitted',$entry->import_row_id) }}" class="saved-dtr-return-submission-form">
                                            @csrf
                                            <label for="saved-dtr-return-reason-{{ $entry->import_row_id }}">Reason for correction</label>
                                            <textarea class="form-control" id="saved-dtr-return-reason-{{ $entry->import_row_id }}" name="reason" maxlength="500" required placeholder="Tell the employee what to correct"></textarea>
                                            <button class="btn btn-outline-warning" type="submit">Return for correction</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @endforeach
                    @endif
                </div>
            </section>
        @empty
            <div class="card card-body text-center text-muted py-5">No DTR entries found for this cutoff and branch.</div>
        @endforelse
        </div>
        <dialog class="saved-dtr-feedback-dialog" id="saved-dtr-feedback" aria-labelledby="saved-dtr-feedback-title">
            <div class="saved-dtr-feedback-content">
                <span class="saved-dtr-eyebrow">DTR update</span>
                <h4 id="saved-dtr-feedback-title">Status</h4>
                <div id="saved-dtr-feedback-message" role="status"></div>
                <div class="saved-dtr-feedback-actions">
                    <button class="btn btn-light" type="button" id="saved-dtr-feedback-cancel" hidden>Cancel</button>
                    <button class="btn btn-primary" type="button" id="saved-dtr-feedback-confirm">OK</button>
                </div>
            </div>
        </dialog>
    </div>
</div>

<style>
.saved-dtr-page{padding-bottom:40px}
.saved-dtr-filters{border:1px solid #e6eaf0;border-radius:14px;margin-bottom:15px}
.saved-dtr-summary{font-size:13px}.saved-dtr-history-notice{display:block;margin-top:4px;color:#b26020}
.saved-dtr-period-bar{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:14px 17px;margin-bottom:14px;border:1px solid #e5e9ef;border-radius:12px;background:#fff}
.saved-dtr-current-period{display:flex;flex-direction:column;gap:2px}.saved-dtr-eyebrow{font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:700;color:#f07824}
.saved-dtr-current-period strong{font-size:16px;color:#263744}.saved-dtr-current-period small{font-size:11px;color:#718096}
.saved-dtr-history{position:relative}.saved-dtr-history>summary{list-style:none;cursor:pointer;border:1px solid #dce4ea;border-radius:8px;padding:9px 12px;color:#526675;font-size:12px;font-weight:700;white-space:nowrap}
.saved-dtr-history>summary::-webkit-details-marker{display:none}.saved-dtr-history>summary::after{content:"⌄";margin-left:10px;color:#f07824}
.saved-dtr-history[open]>summary::after{content:"⌃"}
.saved-dtr-history-form{position:absolute;z-index:10;right:0;top:calc(100% + 7px);width:min(360px,calc(100vw - 32px));padding:14px;background:#fff;border:1px solid #e1e7eb;border-radius:10px;box-shadow:0 8px 26px #22334422}
.saved-dtr-history-form .form-label{font-size:11px;font-weight:700}.saved-dtr-history-controls{display:flex;gap:8px}.saved-dtr-history-controls select{min-width:0}.saved-dtr-history-controls .btn{white-space:nowrap}
.saved-dtr-employee{margin-bottom:13px;background:#fff;border:1px solid #e5e9ef;border-radius:13px;overflow:hidden}
.saved-dtr-employee-heading{padding:15px 18px;display:flex;align-items:center;justify-content:space-between;gap:14px;border-bottom:1px solid #e9edf2}
.saved-dtr-person{display:flex;align-items:center;gap:11px;min-width:0}.saved-dtr-avatar{display:grid;place-items:center;width:38px;height:38px;flex:none;border-radius:50%;background:#fff0e4;color:#c65e1b;font-size:14px;font-weight:800}
.saved-dtr-person>div{min-width:0}.saved-dtr-person strong{display:block;font-size:13px;color:#263744}.saved-dtr-person small{display:block;margin-top:3px;font-size:11px;color:#718096;overflow-wrap:anywhere}
.saved-dtr-employee-count{text-align:right;white-space:nowrap;color:#718096;font-size:11px}.saved-dtr-employee-count strong{display:block;color:#263744;font-size:13px}
.saved-dtr-employee-tools{display:flex;align-items:center;gap:12px}.saved-dtr-resend-form{margin:0}
.saved-dtr-cutoff-wrap{overflow:visible;margin:0;padding:10px 16px 0}.saved-dtr-cutoff-table,.saved-dtr-log-table{margin:0}
.saved-dtr-cutoff-table th,.saved-dtr-log-table th{white-space:nowrap;background:#f6f8fa;color:#5b6b78;font-size:11px}
.saved-dtr-cutoff-table td,.saved-dtr-log-table td{vertical-align:middle;font-size:12px}.saved-dtr-cutoff-table tbody td{border-bottom:0}
.saved-dtr-status-list{display:flex;flex-wrap:wrap;gap:5px}.saved-dtr-status{display:inline-flex;padding:4px 8px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap}
.is-posted,.is-submitted{background:#e7f8ef;color:#14804a}.is-pending{background:#fff4df;color:#a86800}.is-returned{background:#fff0e8;color:#c4540b}.is-locked{background:#edf0f3;color:#626f7d}
.saved-dtr-open-hint{font-size:11px;color:#758491}.saved-dtr-daily{margin:0 16px 14px;border-top:1px solid #e9edf2}
.saved-dtr-daily-trigger{display:flex;align-items:center;gap:10px;width:100%;min-height:44px;padding:10px 2px;border:0;background:transparent;color:#d96b25;text-align:left;font-size:12px;font-weight:700;cursor:pointer}
.saved-dtr-daily-trigger small{margin-left:auto;color:#718096;font-size:11px;font-weight:500}.saved-dtr-daily-trigger span:last-child{font-size:16px}
.saved-dtr-daily-trigger:focus-visible{outline:2px solid #e67c2b;outline-offset:2px}
.saved-dtr-review-dialog{width:min(1210px,calc(100vw - 32px));max-width:none;height:min(82vh,760px);max-height:none;padding:0;border:0;border-radius:16px;box-shadow:0 24px 80px #101b2a55;color:#253443;overflow:hidden}
.saved-dtr-review-dialog[open]{display:flex;flex-direction:column}
.saved-dtr-review-dialog::backdrop{background:#17212bcc;backdrop-filter:blur(2px)}
.saved-dtr-review-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:20px 24px 12px}
.saved-dtr-review-heading h4{margin:3px 0;font-size:20px;font-weight:750}.saved-dtr-review-heading p{margin:0;color:#748292;font-size:12px}
.saved-dtr-review-status{padding:0 24px 11px}.saved-dtr-review-help{margin:0;padding:0 24px 15px;color:#687a89;font-size:12px}
.saved-dtr-review-dialog .saved-dtr-log-wrap{flex:1;min-height:0;margin:0 24px;overflow:auto}
.saved-dtr-review-dialog .saved-dtr-log-table{min-width:1000px}
.saved-dtr-review-dialog .saved-dtr-log-table thead th{position:sticky;top:0;z-index:1}
.saved-dtr-review-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:13px 24px;border-top:1px solid #e9edf2;color:#718096;font-size:11px}
.saved-dtr-review-footer .btn{min-width:90px}
.saved-dtr-log-wrap{overflow-x:auto}.saved-dtr-log-table{min-width:1000px}.saved-dtr-log-table td small{color:#738092}
.saved-dtr-log-table td[data-label="Action"]{min-width:215px;white-space:nowrap}
.saved-dtr-actions{display:flex;align-items:center;gap:7px}
.saved-dtr-actions .btn{min-height:34px;white-space:nowrap}
.saved-dtr-return-form{display:inline-flex;margin:0}.saved-dtr-return-form .btn{font-size:11px}
.saved-dtr-edit-dialog{width:min(680px,calc(100vw - 28px));max-width:none;max-height:min(90vh,760px);padding:0;border:0;border-radius:15px;box-shadow:0 24px 80px #101b2a55;color:#253443}
.saved-dtr-edit-dialog::backdrop{background:#17212bcc;backdrop-filter:blur(2px)}
.saved-dtr-edit-form{padding:22px}.saved-dtr-edit-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:15px;margin-bottom:20px}
.saved-dtr-edit-heading h4{margin:3px 0;font-size:20px;font-weight:750}.saved-dtr-edit-heading p{margin:0;color:#748292;font-size:12px}
.saved-dtr-edit-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.saved-dtr-edit-grid label{display:block;margin-bottom:6px;font-size:12px;font-weight:700}
.saved-dtr-edit-grid .form-control{min-height:43px}
.saved-dtr-edit-classification{display:flex;flex-direction:column;justify-content:center;gap:3px;padding:10px 12px;border:1px solid #e5eaf0;border-radius:9px;background:#f8fafb;font-size:11px}
.saved-dtr-edit-classification span,.saved-dtr-edit-classification small{color:#788795}.saved-dtr-edit-classification strong{color:#314454}
.saved-dtr-edit-break-fields{display:grid;grid-column:1/-1;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;padding:14px;border:1px solid #f1dfcf;border-radius:10px;background:#fffaf5}
.saved-dtr-edit-break-fields[hidden]{display:none}
.saved-dtr-edit-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:20px;padding-top:16px;border-top:1px solid #edf0f3}
.saved-dtr-edit-actions span{color:#718096;font-size:11px}
.saved-dtr-edit-actions .btn{min-height:42px;white-space:nowrap}
.saved-dtr-submitted-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.saved-dtr-submitted-grid>div{display:grid;gap:4px;padding:11px;border:1px solid #e4eaf0;border-radius:8px;min-width:0}
.saved-dtr-submitted-grid span{color:#718091;font-size:11px}.saved-dtr-submitted-grid strong{font-size:12px;overflow-wrap:anywhere}
.saved-dtr-decision-actions{display:grid;gap:14px;margin-top:18px;padding-top:16px;border-top:1px solid #edf0f3}
.saved-dtr-approve-form,.saved-dtr-return-submission-form{margin:0}
.saved-dtr-return-submission-form{display:grid;gap:8px}.saved-dtr-return-submission-form label{font-size:12px;font-weight:700}
.saved-dtr-return-submission-form textarea{min-height:72px}.saved-dtr-return-submission-form .btn{justify-self:start}
.saved-dtr-feedback-dialog{width:min(460px,calc(100vw - 28px));max-width:none;padding:0;border:0;border-radius:14px;box-shadow:0 24px 80px #101b2a55;color:#253443}
.saved-dtr-feedback-dialog::backdrop{background:#17212bcc}
.saved-dtr-feedback-content{padding:22px}.saved-dtr-feedback-content h4{margin:5px 0 12px;font-size:20px}
.saved-dtr-feedback-content #saved-dtr-feedback-message{font-size:13px;line-height:1.5;white-space:pre-line;overflow-wrap:anywhere}
.saved-dtr-feedback-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:20px}
.saved-dtr-page.is-loading #saved-dtr-results{opacity:.55;pointer-events:none}
@media(max-width:767px){
 .saved-dtr-page .page-header{align-items:flex-start!important;gap:10px}.saved-dtr-page .page-header .btn{margin-top:0!important}
 .saved-dtr-filters{padding:13px}.saved-dtr-filters .row>div{width:100%}
 .saved-dtr-period-bar{align-items:flex-start;flex-direction:column}.saved-dtr-history{width:100%}.saved-dtr-history>summary{width:100%;min-height:42px}
 .saved-dtr-history-form{position:static;width:100%;margin-top:8px;box-shadow:none}.saved-dtr-history-controls{flex-direction:column}.saved-dtr-history-controls .btn{min-height:42px}
 .saved-dtr-employee-heading{align-items:flex-start;padding:13px}.saved-dtr-person{align-items:flex-start}.saved-dtr-avatar{width:34px;height:34px}
 .saved-dtr-employee-count{font-size:10px}.saved-dtr-employee-count strong{font-size:12px}
 .saved-dtr-employee-tools{align-items:flex-end;flex-direction:column;gap:8px}
 .saved-dtr-submitted-grid{grid-template-columns:1fr}
 .saved-dtr-cutoff-wrap{padding:8px 10px 0}.saved-dtr-cutoff-table{border-collapse:separate;border-spacing:0 7px}
 .saved-dtr-cutoff-table thead{position:absolute;width:1px;height:1px;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap}
 .saved-dtr-cutoff-table tbody tr{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 8px;padding:7px 9px;border:1px solid #e4eaf0;border-radius:10px}
 .saved-dtr-cutoff-table tbody td{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;min-width:0;padding:7px 3px;border:0;border-bottom:1px solid #edf1f3;overflow-wrap:anywhere}
 .saved-dtr-cutoff-table tbody td::before{content:attr(data-label);margin-bottom:4px;color:#82919d;font-size:9px;font-weight:800;text-transform:uppercase}
 .saved-dtr-cutoff-table tbody td[data-label="Cutoff"],.saved-dtr-cutoff-table tbody td[data-label="Coverage"],.saved-dtr-cutoff-table tbody td[data-label="Status"],.saved-dtr-cutoff-table tbody td[data-label="Details"]{grid-column:1/-1}
 .saved-dtr-cutoff-table tbody td[data-label="Details"]{border-bottom:0}
 .saved-dtr-daily{margin:0 10px 12px}
 .saved-dtr-review-dialog{width:100vw;height:100dvh;max-height:100dvh;margin:0;border-radius:0}
 .saved-dtr-review-heading{padding:18px 15px 10px}.saved-dtr-review-heading h4{font-size:18px}
 .saved-dtr-review-status{padding:0 15px 10px}.saved-dtr-review-help{padding:0 15px 12px}
 .saved-dtr-review-dialog .saved-dtr-log-wrap{margin:0 12px;overflow-y:auto;overflow-x:hidden}
 .saved-dtr-review-dialog .saved-dtr-log-table{min-width:0}
 .saved-dtr-review-footer{padding:11px 15px calc(11px + env(safe-area-inset-bottom))}
 .saved-dtr-log-wrap{overflow:visible}.saved-dtr-log-table{min-width:0;border-collapse:separate;border-spacing:0 8px}
 .saved-dtr-log-table thead{position:absolute;width:1px;height:1px;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap}
 .saved-dtr-log-table tbody tr{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 9px;margin:0 0 9px;padding:7px 10px;border:1px solid #e4eaf0;border-radius:10px}
 .saved-dtr-log-table td{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;min-width:0;padding:7px 3px;border:0;border-bottom:1px solid #edf1f3;overflow-wrap:anywhere}
 .saved-dtr-log-table td::before{content:attr(data-label);margin-bottom:4px;color:#82919d;font-size:9px;font-weight:800;text-transform:uppercase}
 .saved-dtr-log-table td[data-label="Date"],.saved-dtr-log-table td[data-label="Source"]{grid-column:1/-1}
 .saved-dtr-log-table td[data-label="Status"],.saved-dtr-log-table td[data-label="Action"]{border-bottom:0}
 .saved-dtr-log-table td[data-label="Action"]{min-width:0;white-space:normal}
 .saved-dtr-log-table td[data-label="Action"] .btn{width:100%;white-space:normal}
 .saved-dtr-actions{width:100%;flex-direction:column;align-items:stretch}
 .saved-dtr-actions .btn{min-height:40px}
 .saved-dtr-return-form{display:block;width:100%}
 .saved-dtr-return-form .btn{min-height:40px}
 .saved-dtr-edit-dialog{width:100vw;height:100dvh;max-height:100dvh;margin:0;border-radius:0}
 .saved-dtr-edit-form{min-height:100%;display:flex;flex-direction:column;padding:18px 15px calc(18px + env(safe-area-inset-bottom))}
 .saved-dtr-edit-heading h4{font-size:18px}.saved-dtr-edit-grid{grid-template-columns:1fr;gap:12px}
 .saved-dtr-edit-break-fields{grid-template-columns:1fr;gap:12px}
 .saved-dtr-edit-actions{align-items:stretch;flex-direction:column;margin-top:auto;padding-top:14px}
 .saved-dtr-edit-actions .btn{width:100%;min-height:48px}
}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){
    var page=document.querySelector('.saved-dtr-page');
    var results=document.getElementById('saved-dtr-results');
    var feedback=document.getElementById('saved-dtr-feedback');
    var feedbackTitle=document.getElementById('saved-dtr-feedback-title');
    var feedbackMessage=document.getElementById('saved-dtr-feedback-message');
    var feedbackCancel=document.getElementById('saved-dtr-feedback-cancel');
    var feedbackConfirm=document.getElementById('saved-dtr-feedback-confirm');
    var pendingConfirmation=null;
    var cleanUrl=location.pathname;
    var currentUrl=location.href;
    var selectionKey='saved-dtr-selection:'+cleanUrl;
    function rememberSelection(url){
        try{
            var params=new URL(url,location.href).searchParams;
            var saved=new URLSearchParams();
            ['branch_id','employee','period'].forEach(function(key){
                if(params.has(key))saved.set(key,params.get(key));
            });
            sessionStorage.setItem(selectionKey,saved.toString());
        }catch(error){}
    }
    if(location.search){
        rememberSelection(currentUrl);
        history.replaceState(history.state,'',cleanUrl);
    }else{
        try{
            var navigation=performance.getEntriesByType('navigation')[0];
            if(navigation?.type==='reload'){
                var previousSelection=sessionStorage.getItem(selectionKey);
                if(previousSelection){
                    currentUrl=cleanUrl+'?'+previousSelection;
                    loadResults(currentUrl);
                }
            }else{
                sessionStorage.removeItem(selectionKey);
            }
        }catch(error){}
    }
    function showFeedback(title,message,confirmLabel,onConfirm){
        feedbackTitle.textContent=title;
        feedbackMessage.textContent=message;
        pendingConfirmation=onConfirm||null;
        feedbackCancel.hidden=!onConfirm;
        feedbackConfirm.textContent=confirmLabel||'OK';
        if(!feedback.open)feedback.showModal();
    }
    feedbackCancel.addEventListener('click',function(){pendingConfirmation=null;feedback.close();});
    feedbackConfirm.addEventListener('click',function(){
        var action=pendingConfirmation;
        pendingConfirmation=null;
        feedback.close();
        if(action)action();
    });
    function syncBreakFields(dialog){
        if(!dialog)return;
        var policy=dialog.querySelector('[data-saved-dtr-break-policy]');
        var fields=dialog.querySelector('[data-saved-dtr-break-fields]');
        if(!policy||!fields)return;
        var recorded=policy.value==='recorded';
        fields.hidden=!recorded;
        fields.querySelectorAll('input').forEach(function(input){input.disabled=!recorded;input.required=recorded;});
    }
    async function loadResults(url,reopenId){
        page.classList.add('is-loading');
        try{
            var response=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'text/html'},credentials:'same-origin'});
            if(!response.ok)throw new Error('Unable to load the selected cutoff. Please try again.');
            var html=await response.text();
            var next=new DOMParser().parseFromString(html,'text/html').getElementById('saved-dtr-results');
            if(!next)throw new Error('Unable to display the selected cutoff. Please try again.');
            results.replaceChildren(...Array.from(next.childNodes).map(function(node){return document.importNode(node,true);}));
            currentUrl=url;
            rememberSelection(url);
            var period=new URL(url,location.href).searchParams;
            var employee=document.getElementById('saved-dtr-employee');
            var branch=document.getElementById('saved-dtr-branch');
            if(employee)employee.value=period.get('employee')||'';
            if(branch)branch.value=period.get('branch_id')||'';
            if(reopenId){
                var review=document.getElementById(reopenId);
                if(review)review.showModal();
            }
            return true;
        }catch(error){
            showFeedback('Could not load DTR',error.message);
            return false;
        }finally{
            page.classList.remove('is-loading');
        }
    }
    async function saveForm(form){
        var submit=form.querySelector('[type="submit"]');
        if(submit)submit.disabled=true;
        try{
            var response=await fetch(form.action,{
                method:'POST',body:new FormData(form),credentials:'same-origin',
                headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
            });
            var payload=await response.json().catch(function(){return {};});
            if(!response.ok){
                var details=payload.errors?Object.values(payload.errors).flat().join('\n'):payload.message;
                throw new Error(details||'Unable to save this DTR. Please try again.');
            }
            var review=form.closest('.saved-dtr-daily')?.querySelector('.saved-dtr-review-dialog');
            var reviewId=review?.id;
            var editDialog=form.closest('dialog');
            if(editDialog?.open)editDialog.close();
            if(review?.open)review.close();
            if(await loadResults(currentUrl,reviewId)){
                var title=form.matches('.saved-dtr-return-form,.saved-dtr-return-submission-form')
                    ?'Returned for correction'
                    :(form.matches('.saved-dtr-resend-form')?'Email status'
                    :(payload.status==='kept'?'Existing log kept'
                    :(form.matches('.saved-dtr-approve-form')?'DTR approved':'DTR updated')));
                showFeedback(title,payload.message||(payload.status==='kept'
                    ?'An existing attendance log was kept. Review Employee Logs for this date.'
                    :'The DTR change was saved.'));
            }
        }catch(error){
            showFeedback('DTR could not be saved',error.message);
        }finally{
            if(submit)submit.disabled=false;
        }
    }
    page.addEventListener('click',function(event){
        var open=event.target.closest('[data-saved-dtr-open]');
        if(open){
            var dialog=document.getElementById(open.dataset.savedDtrOpen);
            if(dialog){
                syncBreakFields(dialog);
                dialog.showModal();
            }
            return;
        }
        var close=event.target.closest('[data-saved-dtr-close]');
        if(close)close.closest('dialog')?.close();
    });
    page.addEventListener('change',function(event){
        if(event.target.matches('[data-saved-dtr-break-policy]'))syncBreakFields(event.target.closest('dialog'));
    });
    page.addEventListener('submit',function(event){
        var form=event.target;
        if(form.matches('[data-saved-dtr-navigation]')){
            event.preventDefault();
            var url=new URL(form.action,location.href);
            url.search=new URLSearchParams(new FormData(form)).toString();
            loadResults(url.toString());
            form.closest('details')?.removeAttribute('open');
        }else if(form.matches('.saved-dtr-edit-form')){
            event.preventDefault();
            if(!form.reportValidity())return;
            saveForm(form);
        }else if(form.matches('.saved-dtr-return-form')){
            event.preventDefault();
            showFeedback('Send for correction?','The posted DTR will return to the employee for correction.','Send',function(){saveForm(form);});
        }else if(form.matches('.saved-dtr-approve-form')){
            event.preventDefault();
            showFeedback('Approve and post DTR?','The submitted punches will be saved to Employee Logs.','Approve',function(){saveForm(form);});
        }else if(form.matches('.saved-dtr-return-submission-form')){
            event.preventDefault();
            if(!form.reportValidity())return;
            showFeedback('Return to employee?','The employee will be asked to correct this DTR.','Return',function(){saveForm(form);});
        }else if(form.matches('.saved-dtr-resend-form')){
            event.preventDefault();
            saveForm(form);
        }
    });
    document.querySelectorAll('.saved-dtr-edit-dialog[data-reopen="true"]').forEach(function(dialog){
        syncBreakFields(dialog);
        dialog.showModal();
    });
});
</script>
@endsection
