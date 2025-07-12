<?php

namespace App\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Spatie\LaravelSettings\Settings;

class KaidoSetting extends Settings
{
    public string $site_name;
    public bool $site_active;

    // public ?string $app_domain = null;

    public bool $registration_enabled;
    public bool $login_enabled;
    public bool $password_reset_enabled;
    public bool $sso_enabled;

    // public bool $wa_notification_enabled = false;
    // public bool $mail_notification_enabled = false;

    // public ?string $timezone = null;
    // public ?string $locale = null;

    public ?string $logo_url = null;
    public ?string $logo_dark_url = null;
    public ?string $favicon_url = null;

    public bool $email_verification_enabled = false;
    public bool $impersonation_enabled = true;
    public bool $maintenance_mode = false;

    // public ?string $mail_host = null;
    // public ?string $mail_port = null;
    // public ?string $mail_username = null;
    // public ?string $mail_password = null;

    // public ?string $google_client_id = null;
    // public ?string $google_client_secret = null;
    // public ?string $google_redirect_url = null;

    // public ?string $github_client_id = null;
    // public ?string $github_client_secret = null;

    // public ?string $wa_api_url = null;
    // public ?string $wa_api_token = null;

    // public ?string $xendit_api_key = null;
    // public ?string $xendit_webhook_token = null;

    public static function group(): string
    {
        return 'KaidoSetting';
    }
}
