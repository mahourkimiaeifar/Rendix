<?php

namespace App\Notifications;

use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewProjectRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProjectRequest $request
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'project_request.new',
            'title' => 'درخواست پروژه جدید',
            'tracking_code' => $this->request->tracking_code,
            'customer_name' => $this->request->name,
            'service' => $this->request->service,
            'url' => route('admin.project-requests.show', ['id' => $this->request->id]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('درخواست پروژه جدید — ' . $this->request->tracking_code)
            ->view('mail.admin.new-project-request', [
                'request' => $this->request,
            ]);
    }
}