<?php

declare(strict_types=1);

namespace App\Notifications\Core;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $password) {}

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
        return (new MailMessage)
            ->subject('Réinitialisation de mot de passe')
            ->greeting("Bonjour {$notifiable->name},")
            ->line('Votre mot de passe à été réinitialisé.')
            ->line('Voici vos nouvelles informations de connexion :')
            ->line("Email : {$notifiable->email}")
            ->line("Mot de passe : {$this->password}")
            ->line("N'oubliez pas de changer votre mot de passe une fois connecté.")
            ->line('Merci de ne pas répondre à ce mail.')
            ->action('Se connecter', url('/login'));
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
