<?php

namespace App\Mail;

use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProjectRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectRequest $request
    ) {}

    public function build()
    {
        return $this
            ->subject('درخواست پروژه جدید — ' . $this->request->tracking_code)
            ->view('mail.admin.new-project-request');
    }
}