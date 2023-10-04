<?php

namespace App\Notifications;

use Ichtrojan\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class resetpasswordotp extends Notification
{
    use Queueable;
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    public $otp;
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
        $this->message='use the below code for reset the password';
        $this->subject='Verification needed';
        $this->fromEmail='eng.omar.control@gmail.com';
        $this->otp=new Otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $otp=$this->otp->generate($notifiable->email,6,60);
        return (new MailMessage)
                ->mailer('smtp')
                ->subject($this->subject)
                ->greeting('hello '.$notifiable->name)
                ->line('Someone called to reset the password for you account')
                ->line($this->message)
                ->line('code: ' . $otp->token)
                ->line('we will not ask you for the password so don\'t give the code to anyone ')
                ->line('if you haven\'t register in our app pls  ');


    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
