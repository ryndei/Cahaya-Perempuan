<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendEmailOtp extends Notification
{
    use Queueable;

    public function __construct(public string $otp, public int $minutes = 10) {}

    public function via($notifiable): array {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Kode Verifikasi Email')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Gunakan kode berikut untuk verifikasi email Anda:')
            ->line('**'.$this->otp.'**')
            ->line('Kode berlaku '.$this->minutes.' menit.')
            ->line('Jika Anda tidak merasa membuat akun, abaikan email ini.');
    }
}
