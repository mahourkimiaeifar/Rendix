<?php

namespace App\Support;

use App\Mail\NewProjectRequestMail;
use App\Mail\ProjectRequestStatusMail;
use App\Models\ProjectRequest;
use App\Models\User;
use App\Notifications\AdminNewProjectRequestNotification;
use App\Notifications\ProjectRequestStatusNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ProjectRequestNotifier
{
    public static function created(ProjectRequest $request): void
    {
        $admins = User::query()
            ->whereIn('role', ['super_admin', 'admin'])
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminNewProjectRequestNotification($request));
        } else {
            $adminEmail = setting('contact.email');

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewProjectRequestMail($request));
            }
        }

        self::notifyCustomerStatus($request, null);
    }

    public static function statusChanged(ProjectRequest $request, ?string $oldStatus): void
    {
        self::notifyCustomerStatus($request, $oldStatus);
    }

    protected static function notifyCustomerStatus(ProjectRequest $request, ?string $oldStatus): void
    {
        if ($request->user) {
            $request->user->notify(new ProjectRequestStatusNotification($request, $oldStatus));

            return;
        }

        if ($request->email) {
            Mail::to($request->email)->send(new ProjectRequestStatusMail($request, $oldStatus));
        }
    }
}