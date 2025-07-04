<?php

namespace App\Filament\Pages;

use App\Settings\KaidoSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSetting extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = KaidoSetting::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Web Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Site Settings')
                    ->columns(3)
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->columnSpan(1)
                            ->hintIcon('heroicon-o-information-circle')
                            ->hintIconTooltip('The name that will be displayed across the site'),

                        \Filament\Forms\Components\Grid::make(4)
                            ->schema([
                                Toggle::make('site_active')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('Site Active')
                                    ->hintIconTooltip('Enable/disable the entire site'),
                                Toggle::make('registration_enabled')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('Registration Enabled')
                                    ->hintIconTooltip('Allow new users to register accounts'),
                                Toggle::make('password_reset_enabled')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('Password Reset Enabled')
                                    ->hintIconTooltip('Allow users to reset their passwords'),
                                Toggle::make('sso_enabled')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('SSO Enabled')
                                    ->hintIconTooltip('Enable Single Sign-On authentication'),
                                Toggle::make('impersonation_enabled')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('Enable User Impersonation')
                                    ->hintIconTooltip('Allow administrators to impersonate other users'),
                                Toggle::make('maintenance_mode')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('Enable Maintenance Mode')
                                    ->hintIconTooltip('Put the site in maintenance mode - only admins can access'),
                                Toggle::make('email_verification_enabled')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->label('Enable Email Verification')
                                    ->hintIconTooltip('Require email verification for new accounts'),
                            ]),
                        \Filament\Forms\Components\Grid::make(3)
                            ->schema([
                                \Filament\Forms\Components\FileUpload::make('logo_url')
                                    ->label('Logo')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->directory('settings/logo')
                                    ->hintIconTooltip('Upload your main site logo - recommended size 200x50px'),
                                \Filament\Forms\Components\FileUpload::make('logo_dark_url')
                                    ->label('Logo (Dark Mode)')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->directory('settings/logo')
                                    ->hintIconTooltip('Upload a logo optimized for dark mode display'),
                                \Filament\Forms\Components\FileUpload::make('favicon_url')
                                    ->label('Favicon')
                                    ->image()
                                    ->imageCropAspectRatio('1:1')
                                    ->disk('public')
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->directory('settings/favicon')
                                    ->hintIconTooltip('Upload your site favicon - will be cropped to square'),
                            ]),

                    ]),

            ]);
    }
}
