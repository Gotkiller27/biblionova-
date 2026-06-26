<?php

namespace App\Notifications;

use App\Models\DepositRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SecondReviewNotification extends Notification
{
    use Queueable;

    public $depositRequest;

    public function __construct(DepositRequest $depositRequest)
    {
        $this->depositRequest = $depositRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Une demande de dépôt nécessite un second avis.')
                    ->line('Titre : ' . $this->depositRequest->title)
                    ->line('Merci !');
    }

    public function toDatabase($notifiable)
    {
        return [
            'deposit_request_id' => $this->depositRequest->id,
            'title' => $this->depositRequest->title,
            'message' => 'Une demande de dépôt nécessite un second avis.',
        ];
    }
}
