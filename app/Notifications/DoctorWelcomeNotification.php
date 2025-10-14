<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DoctorWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user email address
     */
    protected string $email;

    /**
     * The user phone number (last 5 digits are used as password)
     */
    protected string $phone;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $email, string $phone)
    {
        $this->email = $email;
        $this->phone = $phone;
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
        $portalName = Config::get('app.name');
        $maskedEmail = Str::of($this->email)->mask('*', 4, -4);
        $maskedPhone = Str::of($this->phone)->mask('*', 2, -2);

        return (new MailMessage)
            ->subject("Welcome to {$portalName}")
            ->greeting("Hello Dr. {$notifiable->name}")
            ->line("We're excited to welcome you to the {$portalName}.")
            ->line("Your account has been created successfully, and you can now log in to access all features.")
            ->line("Here are your login details:")
            ->line("Email: {$maskedEmail}")
            ->line("Password: Last 5 digits of your registered phone number: {$maskedPhone}")
            ->line("For security, we recommend that you log in and change your password immediately after your first login.")
            ->action("Access the Portal", URL::to('/'))
            ->salutation("Best regards,  
            The {$portalName} Team");
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
