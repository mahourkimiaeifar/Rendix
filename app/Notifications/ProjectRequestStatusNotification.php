<?php

namespace App\Notifications;

use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectRequestStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProjectRequest $request,
        public ?string $oldStatus = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'project_request.status_changed',
            'title' => 'وضعیت درخواست پروژه تغییر کرد',
            'tracking_code' => $this->request->tracking_code,
            'status' => $this->request->status,
            'status_label' => $this->request->statusLabel(),
            'url' => route('customer.requests.show', $this->request->tracking_code),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('وضعیت درخواست پروژه شما — ' . $this->request->tracking_code)
            ->view('mail.customer.project-request-status', [
                'request' => $this->request,
                'oldStatus' => $this->oldStatus,
            ]);
    }
}