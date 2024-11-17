<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    public function toBlueskyPrivate(object $notifiable): BlueskyPrivateMessage
    {
        return BlueskyPrivateMessage::build(function (TextBuilder $builder) {
            $builder->text(text: '[AWSコスト]')
                ->newLine()
                ->text($this->start.' ~ '.$this->end)
                ->newLine()
                ->text($this->total.' USD');
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
