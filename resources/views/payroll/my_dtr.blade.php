@extends('layouts.employee')

@push('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/my-dtr.css') }}?v=20261030">
@endpush

@section('content')
@php
    $firstPending = $items->firstWhere('status', 'employee_pending');
    $activeId = (int) (old('dtr_row') ?: data_get($firstPending, 'id', data_get($items->first(), 'id', 0)));
    $pendingCount = $items->where('status', 'employee_pending')->count();
    $formatPunch = static fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('M d, Y · h:i A') : null;
@endphp
<div class="employee-dtr" data-my-dtr>
    <div class="dtr-page-heading">
        <div>
            <span class="dtr-eyebrow">Employee portal</span>
            <h1>Complete your DTR</h1>
            <p>Review one date at a time and submit only the details that are missing.</p>
        </div>
        <div class="dtr-queue-count">{{ $pendingCount }} awaiting completion · {{ $items->count() }} total</div>
    </div>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please check this date.</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="dtr-info" role="note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>Saved punches are locked. You can complete only the missing times and break details. Payroll reviews your submission before finalizing.</span></div>

    @if($items->isEmpty())
        <div class="dtr-empty"><i class="fa-regular fa-circle-check" aria-hidden="true"></i><h2>All caught up</h2><p>There are no DTR dates waiting for your completion.</p></div>
    @else
        <div class="dtr-workspace">
            <aside class="dtr-date-panel" aria-label="DTR dates">
                <div class="dtr-panel-heading"><strong>Dates to review</strong><small>{{ $items->count() }} dates</small></div>
                <div class="dtr-date-list" role="tablist" aria-label="Choose a DTR date">
                    @foreach($items as $item)
                        @php($selected = $activeId === (int) $item['id'])
                        <button type="button" class="dtr-date-tab {{ $selected ? 'is-active' : '' }}" id="dtr-tab-{{ $item['id'] }}" role="tab" aria-selected="{{ $selected ? 'true' : 'false' }}" aria-controls="dtr-panel-{{ $item['id'] }}" data-dtr-tab="{{ $item['id'] }}">
                            <span class="dtr-date-icon"><strong>{{ \Carbon\Carbon::parse($item['date'])->format('d') }}</strong><small>{{ strtoupper(\Carbon\Carbon::parse($item['date'])->format('M')) }}</small></span>
                            <span class="dtr-date-copy"><strong>{{ \Carbon\Carbon::parse($item['date'])->format('l') }}</strong><small>{{ $item['cutoff_start'] }} to {{ $item['cutoff_end'] }}</small></span>
                            <span class="dtr-date-status {{ $item['status'] === 'employee_submitted' ? 'is-done' : '' }}">{{ $item['status'] === 'employee_submitted' ? 'Submitted' : (count($item['missing']) ? count($item['missing']).' missing' : 'Confirm') }}</span>
                        </button>
                    @endforeach
                </div>
            </aside>

            <div class="dtr-detail-panels">
                @foreach($items as $item)
                    @php
                        $proposal = $item['proposal'] ?? [];
                        $isSubmitted = $item['status'] === 'employee_submitted';
                        $isOldRow = (int) old('dtr_row') === (int) $item['id'];
                        $value = static fn ($key) => $isOldRow ? old('proposed.'.$key, $proposal[$key] ?? '') : ($proposal[$key] ?? '');
                        $dateValue = static fn ($key) => str_replace(' ', 'T', substr((string) $value($key), 0, 16));
                        $breakPolicy = (string) $value('break_policy');
                        $savedPolicy = $proposal['break_policy'] ?? 'review';
                        $selected = $activeId === (int) $item['id'];
                    @endphp
                    <section class="dtr-detail-panel {{ $selected ? 'is-active' : '' }}" id="dtr-panel-{{ $item['id'] }}" role="tabpanel" aria-labelledby="dtr-tab-{{ $item['id'] }}" data-dtr-panel="{{ $item['id'] }}" @unless($selected) hidden @endunless>
                        <div class="dtr-detail-heading">
                            <div><h2>{{ \Carbon\Carbon::parse($item['date'])->format('l, F j, Y') }}</h2><p>{{ $item['branch_code'] }} · {{ $item['branch_name'] }} · {{ $item['filename'] }}</p></div>
                            <span class="dtr-pill {{ $isSubmitted ? 'is-done' : '' }}">{{ $isSubmitted ? 'Submitted to payroll' : (count($item['missing']) ? count($item['missing']).' details missing' : 'Ready to confirm') }}</span>
                        </div>

                        <div class="dtr-section-title">Saved punches</div>
                        <div class="dtr-saved-grid">
                            @foreach(['log_in' => 'Time in', 'log_out' => 'Time out', 'breaktime_start' => 'Break out', 'breaktime_end' => 'Break in'] as $field => $label)
                                @if(!empty($proposal[$field]))
                                    <div class="dtr-saved-field"><span>{{ $label }}</span><strong>{{ $formatPunch($proposal[$field]) }}</strong><small><i class="fa-solid fa-lock" aria-hidden="true"></i> Locked</small></div>
                                @endif
                            @endforeach
                            @if($savedPolicy !== 'review')
                                <div class="dtr-saved-field"><span>Break treatment</span><strong>{{ $savedPolicy === 'none' ? 'No break' : 'Recorded break' }}</strong><small><i class="fa-solid fa-lock" aria-hidden="true"></i> Locked</small></div>
                            @endif
                        </div>
                        @if(!empty($item['raw']['punches']))<p class="dtr-source">Device punches: {{ implode(', ', $item['raw']['punches']) }}</p>@endif

                        @if($isSubmitted)
                            <div class="dtr-submitted"><i class="fa-solid fa-circle-check" aria-hidden="true"></i><div><strong>Submitted to payroll</strong><p>Payroll will review this date before finalizing your attendance.</p>@if($item['note'])<p><strong>Your note:</strong> {{ $item['note'] }}</p>@endif</div></div>
                        @else
                            <form method="post" action="{{ route('my-dtr.submit', $item['id']) }}" data-dtr-form>
                                @csrf
                                <input type="hidden" name="version" value="{{ $item['version'] }}">
                                <input type="hidden" name="dtr_row" value="{{ $item['id'] }}">
                                @foreach(['log_in', 'log_out', 'breaktime_start', 'breaktime_end'] as $field)
                                    @if(!empty($proposal[$field]))<input type="hidden" name="proposed[{{ $field }}]" value="{{ $dateValue($field) }}">@endif
                                @endforeach
                                @if($savedPolicy !== 'review')<input type="hidden" name="proposed[break_policy]" value="{{ $savedPolicy }}">@endif

                                <div class="dtr-section-title">Complete missing details</div>
                                @if($item['warnings'])<div class="dtr-warning" role="note">{{ implode(' ', $item['warnings']) }}</div>@endif
                                @if(!$item['missing'])<p class="dtr-ready">All punch details are present. Confirm this date and submit it to payroll.</p>@endif
                                <div class="dtr-form-grid">
                                    @foreach(['log_in' => 'Time in', 'log_out' => 'Time out'] as $field => $label)
                                        @if(empty($proposal[$field]))
                                            <div class="dtr-form-field"><label for="dtr-{{ $field }}-{{ $item['id'] }}">{{ $label }} <span>Required</span></label><input id="dtr-{{ $field }}-{{ $item['id'] }}" type="datetime-local" class="form-control" name="proposed[{{ $field }}]" value="{{ $dateValue($field) }}" required></div>
                                        @endif
                                    @endforeach
                                    @if($savedPolicy === 'review')
                                        <fieldset class="dtr-form-field dtr-break-choice"><legend>Break treatment <span>Required</span></legend><div class="dtr-choice-list"><label><input type="radio" name="proposed[break_policy]" value="none" @checked($breakPolicy === 'none') required><span>No break</span></label><label><input type="radio" name="proposed[break_policy]" value="recorded" @checked($breakPolicy === 'recorded') required><span>Recorded break</span></label></div></fieldset>
                                    @endif
                                </div>
                                @if($savedPolicy !== 'none' && (empty($proposal['breaktime_start']) || empty($proposal['breaktime_end'])))
                                    <div class="dtr-break-fields" data-break-fields @if($breakPolicy !== 'recorded') hidden @endif>
                                        <p>Enter only the missing break times if you had a recorded break.</p><div class="dtr-form-grid">
                                            @foreach(['breaktime_start' => 'Break out', 'breaktime_end' => 'Break in'] as $field => $label)
                                                @if(empty($proposal[$field]))<div class="dtr-form-field"><label for="dtr-{{ $field }}-{{ $item['id'] }}">{{ $label }} <span>Required for recorded break</span></label><input id="dtr-{{ $field }}-{{ $item['id'] }}" type="datetime-local" class="form-control" name="proposed[{{ $field }}]" value="{{ $dateValue($field) }}" data-break-time @if($breakPolicy !== 'recorded') disabled @else required @endif></div>@endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="dtr-form-field dtr-note"><label for="dtr-note-{{ $item['id'] }}">Note for payroll <em>(optional)</em></label><textarea id="dtr-note-{{ $item['id'] }}" name="note" class="form-control" rows="2" maxlength="1000" placeholder="Add context for a missing punch">{{ $isOldRow ? old('note') : '' }}</textarea></div>
                                <label class="dtr-confirm"><input type="checkbox" name="acknowledged" value="1" @checked($isOldRow && old('acknowledged')) required><span>I confirm these are my actual attendance details.</span></label>
                                <div class="dtr-form-actions"><small>Payroll will review your submission before finalizing.</small><button type="submit" class="btn dtr-submit">Submit this date to payroll <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button></div>
                            </form>
                        @endif
                    </section>
                @endforeach
            </div>
        </div>
    @endif
</div>
<script src="{{ asset('js/my-dtr.js') }}?v=20261030" defer></script>
@endsection
