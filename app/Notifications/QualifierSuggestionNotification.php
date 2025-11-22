<?php

namespace App\Notifications;

use App\Models\QualifiersReservation;
use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QualifierSuggestionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Tournament $tournament,
        public QualifiersReservation $reservation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $participantName = $this->reservation->team
            ? $this->reservation->team->teamname
            : $this->reservation->user->username;

        return [
            'tournament_id' => $this->tournament->id,
            'tournament_name' => $this->tournament->name,
            'reservation_id' => $this->reservation->id,
            'participant_name' => $participantName,
            'suggested_time' => $this->reservation->suggested_time->toIso8601String(),
            'suggested_time_formatted' => $this->reservation->suggested_time->format('M j, Y @ g:i A'),
            'comment' => $this->reservation->comment,
            'url' => route('dashboard.tournaments.qualifiers', $this->tournament),
        ];
    }
}
