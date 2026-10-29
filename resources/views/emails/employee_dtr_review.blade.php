<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>DTR needs your review</title></head>
<body style="margin:0;padding:24px;background:#f3f5f7;color:#28313b;font-family:Arial,Helvetica,sans-serif;line-height:1.55">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="width:100%;max-width:620px;background:#fff;border:1px solid #e3e7eb;border-radius:8px">
      <tr><td style="padding:24px 28px;background:#ff8734;color:#fff;font-size:18px;font-weight:bold">Payroll · DTR Review</td></tr>
      <tr><td style="padding:26px 28px">
        <p>Hello {{ $employeeName }},</p>
        <p>Your attendance records for <strong>{{ $cutoffStart }} to {{ $cutoffEnd }}</strong> are ready for your review. Please complete any missing punches and submit your DTR.</p>
        <p style="margin:24px 0"><a href="{{ $reviewUrl }}" style="display:inline-block;padding:12px 18px;background:#ff8734;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold">Open My DTR</a></p>
        <p>Sign in to the payroll system with your Employee account. Your submitted DTR will be reviewed by payroll before it is finalized.</p>
        <p style="color:#66717d;font-size:12px">If you already completed this DTR, you can ignore this email.</p>
      </td></tr>
    </table>
  </td></tr></table>
</body></html>
