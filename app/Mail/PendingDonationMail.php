<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingDonationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Donation $donation)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('تبرع جديد بانتظار المراجعة')
            ->view('emails.donations.pending')
            ->with([
                'donation' => $this->donation,
                'project' => $this->donation->project,
                'adminUrl' => route('admin.donations.review', $this->donation),
            ]);
    }
}
