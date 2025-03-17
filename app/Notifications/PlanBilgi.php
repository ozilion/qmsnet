<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanBilgi extends Notification
{
    use Queueable;
    private $planData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($planData)
    {
        $this->planData = $planData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
      return ['broadcast'];
//      return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Merhaba')
                    ->subject('Planlama Bildirimleri')
                    ->from('ozcanarslan@aliment.com.tr', 'Planlama')
                    ->line($this->planData["firma"])
                    ->line($this->planData["metin"])
                    ->line('Bilgileri incelemek ve formu görüntülemek için aşağıdaki düğmeyi kullanabilirsiniz.')
                    ->action('Formu görüntüleyin', $this->planData["planUrl"])
                    ->salutation("Saygılarımla,\n\rPlanlama Sorumlusu");
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
            'planno' => $this->planData['planno']
        ];
    }
}
