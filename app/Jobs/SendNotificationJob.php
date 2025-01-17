<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Notification $notification;
    public User $notifiable;
    public string $notifiableMail;
    public string $type;

    public function __construct($notification, $notifiable, $type)
    {
        $this->notification = $notification;
        $this->notifiable = $notifiable;
        $this->notifiableMail = $notifiable->getUserEmail();
        $this->type = $type;
    }

    public function handle(): void
    {
        $this->notifiable->notify($this->notification);
    }

    public function failed(\Exception $exception): void
    {
        DB::table('failed_jobs')->where('id', $this->job->getJobId())->update([
            'user_email' => $this->notifiableMail,
            'notification_type' => $this->type
        ]);
    }
}
