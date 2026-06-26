<?php

namespace App\Notifications;

use App\Models\DepositRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewDepositRequestNotification extends Notification
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
                    ->line('Une nouvelle demande de dépôt a été soumise.')
                    ->line('Titre : ' . $this->depositRequest->title)
                    ->action('Voir la demande', url('/deposit-requests/' . $this->depositRequest->id))
                    ->line('Merci !');
    }

    public function toDatabase($notifiable)
    {
        return [
            'deposit_request_id' => $this->depositRequest->id,
            'title' => $this->depositRequest->title,
            'message' => 'Une nouvelle demande de dépôt a été soumise.',
        ];
    }
}
