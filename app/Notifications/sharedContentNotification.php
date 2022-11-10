<?php

namespace App\Notifications;

use App\Mail\ContentSharedMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sharedContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $sharedUser;
    public User $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sharedUser,$user)
    {

        $this->user= $user;

        $this->sharedUser = $sharedUser;


    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     //* @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new ContentSharedMail($this->sharedUser, $this->user))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
