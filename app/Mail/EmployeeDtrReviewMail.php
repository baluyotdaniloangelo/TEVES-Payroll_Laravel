<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmployeeDtrReviewMail extends Mailable
{
    public function __construct(
        public string $employeeName,
        public string $cutoffStart,
        public string $cutoffEnd,
        public string $reviewUrl,
    ) {}

    public function build()
    {
        return $this->subject('Please complete your DTR: ' . $this->cutoffStart . ' to ' . $this->cutoffEnd)
            ->view('emails.employee_dtr_review');
    }
}
