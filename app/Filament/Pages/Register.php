<?php

namespace App\Filament\Pages;

use Filament\Pages;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Password;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getUsernameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getPreferenceFormComponent(),
                        $this->getProfessionFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    // Form for Phone
    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Nomor Telepon')
            ->placeholder('Masukan Nomor WA Aktif Anda')
            ->hint('Example (08123456789)')
            ->prefixIcon('heroicon-o-phone')
            ->prefixIconColor('primary')
            ->suffixIcon(
                fn (callable $get) => User::where('phone', $get('phone'))->exists() ? 'heroicon-o-exclamation-circle' : null
            )
            ->suffixIconColor('danger')
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    // Form for Preference (Dropdown of Category)
    protected function getPreferenceFormComponent(): Component
    {
        $categories = Category::all()->pluck('name', 'id');

        return Select::make('preference')
            ->label('Kategori Preferensi')
            ->placeholder('Pilih Kategori Preferensi Anda')
            ->prefixIcon('heroicon-o-tag')
            ->prefixIconColor('primary')
            ->required()
            ->live()
            ->afterStateUpdated(fn (callable $set) => $set('profession', null))
            ->options($categories);
    }

    // Form for Profession (Dropdown of Subcategory)
    protected function getProfessionFormComponent(): Component {
        return Select::make('profession')
            ->label('Profesi')
            ->placeholder('Pilih Berdasarkan Preferensi Anda')
            ->prefixIcon('heroicon-o-user-group')
            ->prefixIconColor('primary')
            ->required()
            ->options(function (callable $get) {
                $categoryId = $get('preference');

                if (!$categoryId) {
                    return [];
                }

                return SubCategory::where('category_id', $categoryId)
                    ->pluck('name', 'name')
                    ->toArray();
            });
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->placeholder('Masukan Gmail Aktif Anda')
            ->prefixIcon('heroicon-o-envelope')
            ->prefixIconColor('primary')
            ->suffixIcon(
                fn (callable $get) => User::where('email', $get('email'))->exists() ? 'heroicon-o-exclamation-circle' : null
            )
            ->suffixIconColor('danger')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->hint('Password Minimal 8 Karakter')
            ->prefixIcon('heroicon-o-lock-closed')
            ->placeholder('Masukan Password Anda')
            ->prefixIconColor('primary')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->placeholder('Ulangi Password Anda')
            ->prefixIcon('heroicon-o-lock-closed')
            ->prefixIconColor('primary')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }



    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/register.form.name.label'))
            ->placeholder('Masukan Nama lengkap Anda')
            ->prefixIcon('heroicon-o-user')
            ->prefixIconColor('primary')
            ->hint('Nama Lengkap Sesuai KTP')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }


    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->placeholder('Nama Pengguna Anda Mesti Unique')
            ->prefixIcon('heroicon-o-user')
            ->prefixIconColor('primary')
            ->suffixIcon(
                fn (callable $get) => User::where('username', $get('username'))->exists() ? 'heroicon-o-exclamation-circle' : null
            )
            ->suffixIconColor('danger')
            ->required()
            ->maxLength(255)
            ->unique(table: User::class);
    }

    protected function handleRegistration(array $data): Model
    {
        try {
            $user = $this->getUserModel()::create($data);

            DB::transaction(function() use ($user) {
                // Assign mentor role
                $user->assignRole('mentor');
                $user->update(['role' => 'mentor']);

                // Create wallet for the user with 0 initial balance
                $user->wallet()->create([
                    'balance' => 0,
                ]);
            });

            event(new Registered($user));

            return $user;

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            throw $e;
        }
    }

}
