<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $isApiRequest = request()->is('api/v1/*');
            if ($isApiRequest) {
                $parts = parse_url($url);
                $verifyEmailUrl = config('app.url')
                    . '/v1/verified-email?id=' . $notifiable->getKey()
                    . '&hash=' . sha1($notifiable->getEmailForVerification())
                    . '&' . $parts['query'];

                return (new MailMessage)
                    ->subject('Verify Email Address')
                    ->greeting('Hello ' . ($notifiable->name ?? $notifiable->username))
                    ->line('Click the button below to verify your email address')
                    ->action('Verify Email Address', $verifyEmailUrl)
                    ->line('If you did not create this account, no further action is required.');
            }

            return (new MailMessage)
            ->subject('Verify Email Address')
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->username))
            ->line('Click the button below to verify your email address')
            ->action('Verify Email Address', $url)
            ->line('If you did not create this account, no further action is required.');
        });
    }
}
