<?php

namespace App\Mail;

use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectRequest $request,
        public ?string $oldStatus = null
    ) {}

    public function build()
    {
        return $this
            ->subject('وضعیت درخواست پروژه شما — ' . $this->request->tracking_code)
            ->view('mail.customer.project-request-status');
    }
}