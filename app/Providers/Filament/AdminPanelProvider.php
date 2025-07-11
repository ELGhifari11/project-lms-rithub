<?php

namespace App\Providers\Filament;

use Filament\Panel;
use App\Models\User;
use Filament\PanelProvider;
use App\Filament\Pages\Login;
use Filament\Enums\ThemeMode;
use App\Settings\KaidoSetting;
use App\Filament\Pages\Register;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Support\Facades\DB;
use App\Livewire\CustomProfileInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Filament\Resources\LogResource;
use Awcodes\LightSwitch\Enums\Alignment;
use Filament\Forms\Components\FileUpload;
use Rupadana\ApiService\ApiServicePlugin;
use Awcodes\LightSwitch\LightSwitchPlugin;

use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Hasnayeen\Themes\Filament\Pages\Themes;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use DutchCodingCompany\FilamentSocialite\Provider;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Njxqlus\FilamentProgressbar\FilamentProgressbarPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;

class AdminPanelProvider extends PanelProvider
{


    private ?KaidoSetting $settings = null;
    //constructor
    public function __construct()
    {

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $this->settings = app(KaidoSetting::class);
            }
        } catch (\Exception $e) {
            $this->settings = null;
        }
    }

    private function getPrimaryColor($default = 'Pink')
    {
        try {
            $colorName = (new Themes())->getColor() ?? $default;

            if (str_starts_with($colorName, '#')) {
                return $default;
            }

            $colorName = ucfirst(strtolower($colorName));

            $colorClass = \Filament\Support\Colors\Color::class;

            return defined("$colorClass::$colorName")
                ? constant("$colorClass::$colorName")
                : constant("$colorClass::$default");

        } catch (\Exception $e) {
            return constant("\Filament\Support\Colors\Color::$default");
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->when($this->settings->login_enabled ?? true, fn($panel) => $panel->login(Login::class))
            ->when($this->settings->registration_enabled ?? true, fn($panel) => $panel->registration(Register::class))
            ->when($this->settings->password_reset_enabled ?? true, fn($panel) => $panel->passwordReset())
            ->when($this->settings->email_verification_enabled ?? true, fn($panel) => $panel->emailVerification())
            ->colors([
                'primary' => Color::Pink,
                'secondary' => Color::Gray,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultThemeMode(ThemeMode::System)
            ->favicon(fn () => $this->settings?->favicon_url ? asset($this->settings->favicon_url) : asset('images/rithub-favicon.png'))
            ->brandLogo(fn () => $this->settings?->logo_url ? asset($this->settings->logo_url) : asset('images/rithub-logo.png'))
            ->darkModeBrandLogo(fn () => $this->settings?->logo_dark_url ? asset($this->settings->logo_dark_url) : asset('images/rithub-logo-dark.png'))
            ->brandName($this->settings->site_name ?? 'Site Name')
            ->brandLogoHeight('4rem')
            ->sidebarCollapsibleOnDesktop(true)
            ->sidebarFullyCollapsibleOnDesktop(true)
            ->sidebarWidth('16rem')
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->middleware([
                SetTheme::class
            ])
            ->plugins(
                $this->getPlugins()
            )
            ->when($this->settings->maintenance_mode ?? false, function ($panel) {
                abort(503, 'Maintenance Mode Aktif');
            })
            ->databaseNotifications();
    }

    private function getPlugins(): array
    {
        $plugins = [
            FilamentProgressbarPlugin::make()
                ->color((new Themes())->getColor() ?? 'pink'),
            FilamentApexChartsPlugin::make(),
            LightSwitchPlugin::make()
                ->position(Alignment::TopCenter),
            ActivitylogPlugin::make()
                ->resource(LogResource::class)
                ->navigationGroup('Settings')
                ->navigationIcon('heroicon-m-document-magnifying-glass'),
            ThemesPlugin::make()
                ,
            FilamentShieldPlugin::make()
            // ->gridColumns([
            //     'default' => 1,
            //     'sm' => 2,
            //     'lg' => 3
            // ])
            // ->sectionColumnSpan(1)
            // ->checkboxListColumns([
            //     'default' => 1,
            //     'sm' => 2,
            //     'lg' => 4,
            // ])
            // ->resourceCheckboxListColumns([
            //     'default' => 1,
            //     'sm' => 2,
            // ])
            ,
            BreezyCore::make()
                ->myProfile(
                    shouldRegisterUserMenu: true,
                    shouldRegisterNavigation: true,
                    navigationGroup: 'Settings',
                    hasAvatars: true,
                    slug: 'profile'
                )
                ->myProfileComponents([
                    'personal_info' => CustomProfileInfo::class,
                ])
                // ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())
                // OR, replace with your own component
                ->avatarUploadComponent(
                    fn() => FileUpload::make('avatar_url')
                        ->disk('public')
                        ->directory('avatars')
                        ->visibility('public')
                        ->image()
                        ->maxSize(1024)
                        ->label('Foto Profil')
                )
                ->enableBrowserSessions()
                ->enableTwoFactorAuthentication(
                    force: false, // Jika true, pengguna harus mengaktifkan 2FA sebelum bisa menggunakan aplikasi
                )
            // ->passwordUpdateRules(
            //     rules: [Password::default()->mixedCase()->numbers()->symbols()->uncompromised(3)],
            //     requiresCurrentPassword: true
            // )
            // ->enableSanctumTokens(
            //     permissions: ['read', 'create', 'update', 'delete']
            // )
        ];

        if ($this->settings->sso_enabled ?? true) {
            $plugins[] =
                FilamentSocialitePlugin::make()
                ->providers([
                    Provider::make('github')
                        ->label('Github')
                        ->icon('fab-github')
                        ->color(
                            (function () {
                                return self::getPrimaryColor();
                            })()
                        )
                        ->outlined(true)
                        ->stateless(false),

                    Provider::make('google')
                        ->label('Google')
                        ->icon('fab-google')
                        ->color(
                            (function () {
                                return self::getPrimaryColor();
                            })()
                        )
                        ->outlined(true)
                        ->stateless(false)
                ])
                ->registration(true)
                ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
                    $user = User::firstOrNew([
                        'email' => $oauthUser->getEmail(),
                    ]);
                    $user->name = $oauthUser->getName();
                    $user->email = $oauthUser->getEmail();
                    $user->username = $oauthUser->getNickname() ?? explode('@', $oauthUser->getEmail())[0];
                    $user->avatar_url = $oauthUser->getAvatar();
                    $user->social_media = match ($provider) {
                        'github' => ['github' => $oauthUser->user['html_url'] ?? null],
                        default => []
                    };
                    $user->email_verified_at = now();

                    // Save user first to get ID
                    $user->save();

                    DB::transaction(function () use ($user) {

                        $user->assignRole('mentor');
                        $user->update(['role' => 'mentor']);

                        // $user->wallet()->create([
                            //     'balance' => 0,
                            //     'mentor_id' => $user->id
                            // ]);
                        });
                        
                        logger(json_decode(json_encode($oauthUser), true));

                    event(new Registered($user));

                    return $user;
                });
        }
        return $plugins;
    }
}
