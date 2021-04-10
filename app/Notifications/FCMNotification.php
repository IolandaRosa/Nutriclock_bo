<?php

namespace App\Notifications;

use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FCMNotification extends Notification
{
    use Queueable;

    private $fcmToken;
    private $title;
    private $body;

    public function __construct($fcmToken, $title, $body)
    {
        $this->fcmToken = $fcmToken;
        $this->title = $title;
        $this->body = $body;
    }


    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        $message = new FcmMessage();
        $message
            ->to($this->fcmToken)
            ->content([
                'title' => ''.$this->title,
                'body' => ''.$this->body,
                'sound' => '',
                'click_action' => 'OPEN_ACTIVITY_1'
            ])->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
    }
}
