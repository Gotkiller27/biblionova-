<?php

namespace App\Notifications;

use App\Models\Reference;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ReferencePublishedNotification extends Notification
{
    use Queueable;

    public $reference;

    public function __construct(Reference $reference)
    {
        $this->reference = $reference;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Une référence a été publiée !')
                    ->line('Titre : ' . $this->reference->title)
                    ->line('Merci !');
    }

    public function toDatabase($notifiable)
    {
        return [
            'reference_id' => $this->reference->id,
            'title' => $this->reference->title,
            'message' => 'Une référence a été publiée !',
        ];
    }
}
