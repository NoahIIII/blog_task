<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificationSender extends Notification
{
    use Queueable;

    private $mailData;
    // $mailData --> [ "subject", "view", "data" ]

    private $databaseData;
    // $databaseData --> [ "title_ar", "title_en", "body_ar", "body_en", "img_url", "context" ]

    // methods
    public function __construct($notificationData)
    {
        // initialize
        if (isset($notificationData['mailData'])) {
            $this->mailData = $notificationData['mailData'];
        }

        if (isset($notificationData['databaseData'])) {
            $this->databaseData = $notificationData['databaseData'];
        }
    }

    public function via(object $notifiable): array
    {
        $sendTo = [];

        // sent to mail
        if (!is_null($this->mailData)) {
            $sendTo[] = 'mail';
        }

        // sent to mail
        if (!is_null($this->databaseData)) {
            $sendTo[] = 'database';
        }

        return $sendTo;
    }

    // send an email to notify user
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->mailData["subject"])
            ->view('emails.' . $this->mailData["view"], ['data' => $this->mailData["data"]]);
    }

    // save notification in database
    public function toDatabase(object $notifiable)
    {
        return $this->databaseData;
    }
}
