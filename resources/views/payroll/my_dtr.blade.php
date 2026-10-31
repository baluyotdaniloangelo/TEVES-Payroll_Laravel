@extends('layouts.employee')

@push('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/my-dtr.css') }}?v=20261030-table-review">
@endpush

@section('content')
@php
    $firstPending = $items->firstWhere('status', 'employee_pending');
    $activeId = (int) (old('dtr_row') ?: data_get($firstPending, 'id', data_get($items->first(), 'id', 0)));
    $pendingCount = $items->where('status', 'employee_pending')->count();
    $submittedCount = $items->where('status', 'employee_submitted')->count();
    $hasReturnedDtr = $items->contains(fn ($item) => !empty($item['allow_employee_correction']));
    $formatPunch = static fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('M d, Y · h:i A') : '—';
    $formatTime = static fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('h:i A') : '—';
@endphp
<div class="employee-dtr" data-my-dtr data-dtr-old-row="{{ old('dtr_row') }}">
    <div class="dtr-page-heading">
        <div><span class="dtr-eyebrow">Employee portal</span><h1>Complete your DTR</h1><p>Review the device punches and complete only the details that are missing.</p></div>
        <div class="dtr-queue-count">{{ $pendingCount }} awaiting completion · {{ $submittedCount }} submitted · {{ $items->count() }} total</div>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Please check this date.</strong> {{ $errors->first() }}</div>@endif

    <div class="dtr-info" role="note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>{{ $hasReturnedDtr ? 'Payroll returned a posted DTR for correction. Review the current punches, correct what is needed, and submit it again.' : 'Device punches are locked. You can complete only missing times and break details. Payroll reviews your submission before finalizing.' }}</span></div>

    @if($items->isEmpty())
        <div class="dtr-empty"><i class="fa-regular fa-circle-check" aria-hidden="true"></i><h2>All caught up</h2><p>There are no DTR dates waiting for your completion.</p></div>
    @else
        <div class="dtr-review-table-wrap">
            <table class="dtr-review-table">
                <thead><tr><th>Date</th><th>Time in</th><th>Break out</th><th>Break in</th><th>Time out</th><th>Work hours</th><th>Status</th><th>Classification</th><th>Details</th></tr></thead>
                <tbody>
                @foreach($items as $item)
                    @php
                        $proposal=$item['proposal']??[];
                        $breakPolicy=$proposal['break_policy']??'review';
                        $classification=($breakPolicy==='none'?'No break':($breakPolicy==='recorded'?'Recorded break':'Break to review'))
                            .' · '.(($proposal['overtime_status']??'No')==='Yes'?'Overtime':'Regular / rest day')
                            .' · '.(($proposal['override_default_shift']??'No')==='Yes'?'Shift override':'Employee shift');
                        $status=$item['status']==='employee_submitted'?'Submitted to payroll':(!empty($item['allow_employee_correction'])?'Correction requested':(count($item['missing'])?count($item['missing']).' missing':'Ready to confirm'));
                        $statusClass=$item['status']==='employee_submitted'?'is-submitted':(!empty($item['allow_employee_correction'])?'is-returned':'is-pending');
                    @endphp
                    <tr>
                        <td data-label="Date"><div class="dtr-date-cell"><span class="dtr-date-badge"><strong>{{ \Carbon\Carbon::parse($item['date'])->format('d') }}</strong><small>{{ strtoupper(\Carbon\Carbon::parse($item['date'])->format('M')) }}</small></span><span><strong>{{ \Carbon\Carbon::parse($item['date'])->format('l') }}</strong><small>{{ $item['cutoff_start'] }} to {{ $item['cutoff_end'] }}</small></span></div></td>
                        <td data-label="Time in">{{ $formatTime($proposal['log_in']??null) }}</td>
                        <td data-label="Break out">{{ $breakPolicy==='none'?'No break':($breakPolicy==='recorded'?$formatTime($proposal['breaktime_start']??null):'—') }}</td>
                        <td data-label="Break in">{{ $breakPolicy==='recorded'?$formatTime($proposal['breaktime_end']??null):'—' }}</td>
                        <td data-label="Time out">{{ $formatTime($proposal['log_out']??null) }}</td>
                        <td data-label="Work hours"><strong class="dtr-hours">{{ is_numeric($item['work_hours']??null)?number_format((float)$item['work_hours'],2).' hrs':'—' }}</strong></td>
                        <td data-label="Status"><span class="dtr-status {{ $statusClass }}">{{ $status }}</span></td>
                        <td data-label="Classification" class="dtr-classification">{{ $classification }}</td>
                        <td data-label="Details"><button type="button" class="btn btn-sm btn-outline-secondary dtr-open-review" data-dtr-open="{{ $item['id'] }}">{{ $item['status']==='employee_submitted'?'View':'Review' }}</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @foreach($items as $item)
            @php
                $proposal=$item['proposal']??[];
                $isSubmitted=$item['status']==='employee_submitted';
                $allowCorrection=(bool)($item['allow_employee_correction']??false);
                $isOldRow=(int)old('dtr_row')===(int)$item['id'];
                $value=static fn($key)=>$isOldRow?old('proposed.'.$key,$proposal[$key]??''):($proposal[$key]??'');
                $dateValue=static fn($key)=>str_replace(' ','T',substr((string)$value($key),0,16));
                $breakPolicy=(string)$value('break_policy');
                $breakPolicyEditable=$allowCorrection||(bool)($item['break_policy_editable']??(($proposal['break_policy']??'review')==='review'));
                $savedPolicy=$proposal['break_policy']??'review';
                $classification=($savedPolicy==='none'?'No break':($savedPolicy==='recorded'?'Recorded break':'Break to review'))
                    .' · '.(($proposal['overtime_status']??'No')==='Yes'?'Overtime':'Regular / rest day')
                    .' · '.(($proposal['override_default_shift']??'No')==='Yes'?'Shift override':'Employee shift');
            @endphp
            <dialog class="dtr-review-dialog" id="dtr-dialog-{{ $item['id'] }}" aria-labelledby="dtr-dialog-title-{{ $item['id'] }}">
                <div class="dtr-dialog-heading">
                    <div><span class="dtr-eyebrow">{{ $allowCorrection?'Correction requested':'DTR review' }}</span><h2 id="dtr-dialog-title-{{ $item['id'] }}">{{ \Carbon\Carbon::parse($item['date'])->format('l, F j, Y') }}</h2><p>{{ $item['branch_code'] }} · {{ $item['branch_name'] }} · {{ $item['filename'] }}</p></div>
                    <button type="button" class="btn-close dtr-close-review" aria-label="Close"></button>
                </div>
                <div class="dtr-dialog-body">
                    <div class="dtr-review-summary">
                        <div><span>Time in</span><strong>{{ $formatPunch($proposal['log_in']??null) }}</strong><small>{{ $allowCorrection?'Editable after return':'Device punch locked' }}</small></div>
                        <div><span>Break out</span><strong>{{ $savedPolicy==='none'?'No break':$formatPunch($proposal['breaktime_start']??null) }}</strong><small>{{ $savedPolicy==='none'?'No break recorded':'Device/review value' }}</small></div>
                        <div><span>Break in</span><strong>{{ $savedPolicy==='none'?'—':$formatPunch($proposal['breaktime_end']??null) }}</strong><small>{{ $savedPolicy==='none'?'No break recorded':'Device/review value' }}</small></div>
                        <div><span>Time out</span><strong>{{ $formatPunch($proposal['log_out']??null) }}</strong><small>{{ $allowCorrection?'Editable after return':'Device punch locked' }}</small></div>
                        <div><span>Work hours</span><strong>{{ is_numeric($item['work_hours']??null)?number_format((float)$item['work_hours'],2).' hrs':'—' }}</strong><small>Payroll-adjusted estimate</small></div>
                        <div><span>Classification</span><strong>{{ $classification }}</strong></div>
                    </div>
                    @if(!empty($item['raw']['punches']))<p class="dtr-source">Device punches: {{ implode(', ',$item['raw']['punches']) }}</p>@endif
                    @if(!empty($item['return_note']))<div class="alert alert-warning" role="note"><strong>Payroll requested a correction:</strong> {{ $item['return_note'] }}</div>@endif
                    @if($isSubmitted)
                        <div class="dtr-submitted"><i class="fa-solid fa-circle-check" aria-hidden="true"></i><div><strong>Submitted to payroll</strong><p>Payroll will review this date before finalizing your attendance.</p>@if($item['note'])<p><strong>Your note:</strong> {{ $item['note'] }}</p>@endif</div></div>
                    @else
                        <form method="post" action="{{ route('my-dtr.submit',$item['id']) }}" data-dtr-form data-dtr-correction="{{ $allowCorrection?'1':'0' }}">
                            @csrf
                            <input type="hidden" name="version" value="{{ $item['version'] }}"><input type="hidden" name="dtr_row" value="{{ $item['id'] }}">
                            @foreach(['log_in','log_out','breaktime_start','breaktime_end'] as $field)
                                @if(!empty($proposal[$field])&&!$allowCorrection)<input type="hidden" name="proposed[{{ $field }}]" value="{{ $dateValue($field) }}">@endif
                            @endforeach
                            @unless($breakPolicyEditable)<input type="hidden" name="proposed[break_policy]" value="{{ $savedPolicy }}">@endunless
                            <div class="dtr-section-title">{{ $allowCorrection?'Correct this DTR':'Complete missing details' }}</div>
                            @if($allowCorrection&&!empty($item['return_note']))<div class="dtr-warning" role="note">{{ $item['return_note'] }}</div>@endif
                            @if($item['warnings'])<div class="dtr-warning" role="note">{{ implode(' ',$item['warnings']) }}</div>@endif
                            <div class="dtr-form-grid">
                                @foreach(['log_in'=>'Time in','log_out'=>'Time out'] as $field=>$label)
                                    @if(empty($proposal[$field])||$allowCorrection)
                                        <div class="dtr-form-field"><label for="dtr-{{ $field }}-{{ $item['id'] }}">{{ $label }} <span>Required</span></label><input id="dtr-{{ $field }}-{{ $item['id'] }}" type="datetime-local" class="form-control" name="proposed[{{ $field }}]" value="{{ $dateValue($field) }}" required></div>
                                    @endif
                                @endforeach
                                @if($breakPolicyEditable)
                                    <fieldset class="dtr-form-field dtr-break-choice"><legend>Break treatment <span>Required</span></legend><div class="dtr-choice-list"><label><input type="radio" name="proposed[break_policy]" value="none" @checked($breakPolicy==='none') required><span>No break</span></label><label><input type="radio" name="proposed[break_policy]" value="recorded" @checked($breakPolicy==='recorded') required><span>Recorded break</span></label></div></fieldset>
                                @endif
                            </div>
                            @if($allowCorrection||($savedPolicy!=='none'&&(empty($proposal['breaktime_start'])||empty($proposal['breaktime_end']))))
                                <div class="dtr-break-fields {{ $breakPolicy==='recorded'?'':'is-inactive' }}" data-break-fields><p data-break-hint>{{ $breakPolicy==='recorded'?($allowCorrection?'Check or correct the recorded break times.':'Enter only the missing break times.'):'Select Recorded break above to enter break times. Choose No break if you did not take a recorded break.' }}</p><div class="dtr-form-grid">
                                    @foreach(['breaktime_start'=>'Break out','breaktime_end'=>'Break in'] as $field=>$label)
                                        @if(empty($proposal[$field])||$allowCorrection)
                                            <div class="dtr-form-field"><div class="dtr-break-label">{{ $label }} <span>Required for recorded break</span></div><div class="dtr-break-time-pair">
                                                <label for="dtr-{{ $field }}-date-{{ $item['id'] }}">Date<input id="dtr-{{ $field }}-date-{{ $item['id'] }}" type="date" class="form-control" name="break_dates[{{ $field }}]" value="{{ $isOldRow?old('break_dates.'.$field,($savedPolicy!=='none'&&!empty($value($field)))?substr((string)$value($field),0,10):$item['date']):(($savedPolicy!=='none'&&!empty($value($field)))?substr((string)$value($field),0,10):$item['date']) }}" data-break-input @disabled($breakPolicy!=='recorded')></label>
                                                <label for="dtr-{{ $field }}-time-{{ $item['id'] }}">Time<input id="dtr-{{ $field }}-time-{{ $item['id'] }}" type="time" class="form-control" name="break_times[{{ $field }}]" value="{{ $isOldRow?old('break_times.'.$field,($savedPolicy!=='none'&&!empty($value($field)))?substr((string)$value($field),11,5):''):(($savedPolicy!=='none'&&!empty($value($field)))?substr((string)$value($field),11,5):'') }}" data-break-input data-break-time @disabled($breakPolicy!=='recorded') @required($breakPolicy==='recorded')></label>
                                            </div></div>
                                        @endif
                                    @endforeach
                                </div></div>
                            @endif
                            <div class="dtr-form-field dtr-note"><label for="dtr-note-{{ $item['id'] }}">Note for payroll <em>(optional)</em></label><textarea id="dtr-note-{{ $item['id'] }}" name="note" class="form-control" rows="2" maxlength="1000" placeholder="Add context for a missing punch">{{ $isOldRow?old('note'):'' }}</textarea></div>
                            <label class="dtr-confirm"><input type="checkbox" name="acknowledged" value="1" @checked($isOldRow&&old('acknowledged')) required><span>I confirm these are my actual attendance details.</span></label>
                            <div class="dtr-form-actions"><small>Payroll reviews your submission before finalizing.</small><button type="submit" class="btn dtr-submit">Submit this date to payroll <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button></div>
                        </form>
                    @endif
                </div>
            </dialog>
        @endforeach
    @endif
</div>
<script src="{{ asset('js/my-dtr.js') }}?v=20261030-table-review" defer></script>
@endsection
