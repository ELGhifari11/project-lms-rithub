<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Commands;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Activity::class, ActivityPolicy::class);

        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('google', \SocialiteProviders\Google\Provider::class);
        });


        FilamentShield::prohibitDestructiveCommands(config('app.env') === 'production');
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
