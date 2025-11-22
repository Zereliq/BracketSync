<?php

namespace App\Notifications;

use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketReplyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TicketReply $reply) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->reply->ticket_id,
            'reply_id' => $this->reply->id,
            'reply_user_id' => $this->reply->user_id,
            'reply_user_name' => $this->reply->user->name,
            'message' => $this->reply->message,
            'is_staff_reply' => $this->reply->is_staff_reply,
            'ticket_subject' => $this->reply->ticket->subject,
        ];
    }
}
