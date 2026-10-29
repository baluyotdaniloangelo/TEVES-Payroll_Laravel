<!doctype html><html><head><meta charset="utf-8"><style>
@page{margin:20px 24px}body{font-family:DejaVu Sans,sans-serif;font-size:10px;color:#222}table{width:100%;border-collapse:collapse}th,td{border:1px solid #777;padding:5px}th{background:#e9e9e9}.header td,.top td{border:0;padding:2px;vertical-align:middle}.logo-cell{width:16%;text-align:center}.logo{max-width:125px;max-height:74px}.branch-name{font-size:20px;font-weight:bold}.branch-details{font-size:10px;line-height:1.5}.title{background:#7738a8;color:white;text-align:center;font-size:18px;padding:10px;width:25%}.meta{margin-top:10px}.right{text-align:right}.center{text-align:center}.total{font-weight:bold}.muted{color:#555}
</style></head><body>
<table class="header"><tr>
    <td class="logo-cell">@if($logoPath)<img class="logo" src="{{ $logoPath }}" alt="{{ $branchInformation->branch_name }} logo">@endif</td>
    <td><div class="branch-name">{{ $branchInformation->branch_name ?? $payroll->branch_name }}</div>
        <div class="branch-details">
            @if($branchInformation && $branchInformation->branch_address){{ $branchInformation->branch_address }}<br>@endif
            @if($branchInformation && $branchInformation->branch_tin)VAT Reg. TIN: {{ $branchInformation->branch_tin }}<br>@endif
            @if($branchInformation && $branchInformation->branch_owner){{ $branchInformation->branch_owner }}@if($branchInformation->branch_owner_title) — {{ $branchInformation->branch_owner_title }}@endif<br>@endif
            {{ $branchInformation->branch_code ?? $payroll->branch_code }}
        </div>
    </td>
    <td class="title">DRIVER'S PAYROLL</td>
</tr></table>
<table class="top meta"><tr><td><strong>Driver's Name:</strong> {{ $payroll->employee_full_name }}</td><td><strong>Payroll No.:</strong> {{ $payroll->payroll_no }}</td></tr><tr><td><strong>Plate Number:</strong> {{ $items->pluck('plate_number')->filter()->unique()->implode(', ') }}</td><td><strong>Trip Ref. No.:</strong> {{ $payroll->trip_reference }}</td></tr><tr><td><strong>Cutoff:</strong> {{ $payroll->cutoff_start }} to {{ $payroll->cutoff_end }}</td><td><strong>Payroll Date:</strong> {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M j, Y') }}</td></tr><tr><td></td><td><strong>Rate per trip:</strong> {{ $rateLabel }}</td></tr></table>
<h3>Trip Earnings</h3><table><thead><tr><th>Travel Date</th><th>Loading Terminal</th><th>Destination</th><th>Volume</th><th>Rate / L</th><th>Gross Amount</th><th>Driver's Trip Pay</th></tr></thead><tbody>
@foreach($items as $item)<tr><td>{{ \Carbon\Carbon::parse($item->travel_date)->format('M d, Y') }}</td><td>{{ $item->loading_terminal }}</td><td>{{ $item->destination }}</td><td class="right">{{ number_format((float)$item->volume, 2) }}</td><td class="right">{{ number_format((float)$item->rate_per_liter, 2) }}</td><td class="right">{{ number_format((float)$item->gross_amount, 2) }}</td><td class="right">{{ number_format((float)$item->trip_pay, 2) }}</td></tr>@endforeach
<tr class="total"><td colspan="3" class="right">TOTAL</td><td class="right">{{ number_format((float)$payroll->total_volume, 2) }}</td><td></td><td class="right">{{ number_format((float)$payroll->total_gross_amount, 2) }}</td><td class="right">{{ number_format((float)$payroll->total_trip_pay, 2) }}</td></tr></tbody></table>
</body></html>
