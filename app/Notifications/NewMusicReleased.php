<?php

namespace App\Notifications;

use App\Models\Music;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMusicReleased extends Notification
{
    use Queueable;

    public $music;

    /**
     * Create a new notification instance.
     */
    public function __construct(Music $music)
    {
        $this->music = $music;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'music_id' => $this->music->id,
            'music_title' => $this->music->title,
            'artist_name' => $this->music->artist->name,
            'message' => $this->music->artist->name . ' has released a new song: ' . $this->music->title,
        ];
    }
}
