<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Revolution\Bluesky\Notifications\BlueskyPrivateChannel;
use Revolution\Bluesky\Notifications\BlueskyPrivateMessage;
use Revolution\Bluesky\RichText\TextBuilder;

class AwsCostNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $start,
        protected string $end,
        protected string $total)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [BlueskyPrivateChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[AWSコスト] '.$this->total.' USD')
            ->greeting($this->start.' ~ '.$this->end)
            ->line($this->total.' USD');
    }

    public function toBlueskyPrivate(object $notifiable): BlueskyPrivateMessage
    {
        return BlueskyPrivateMessage::build(function (TextBuilder $builder) {
            $builder->text(text: '[AWSコスト] '.$this->total.' USD')
                ->newLine()
                ->text($this->start.' ~ '.$this->end);
        });
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
