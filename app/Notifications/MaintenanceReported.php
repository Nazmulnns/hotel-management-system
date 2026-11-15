<?php

namespace App\Notifications;

use App\Enums\MaintenanceStatus;
use App\Models\RoomMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceReported extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RoomMaintenance $maintenance)
    {
        $this->afterCommit();
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $room = $this->maintenance->room;

        return (new MailMessage)
            ->subject("New maintenance issue in room {$room->floor}{$room->room_number}")
            ->greeting('Hello Manager,')
            ->line("A new issue was reported in room **{$room->floor}{$room->room_number}**:")
            ->line($this->maintenance->issue_description)
            ->action('Open maintenance list', config('app.frontend_url').'/maintenances')
            ->line('Please assign someone to fix it.');
    }

    /**
     * Only send if the issue is still pending when the worker runs.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $this->maintenance->fresh()?->maintenance_status === MaintenanceStatus::Pending;
    }
}