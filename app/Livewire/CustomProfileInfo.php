<?php

namespace App\Livewire;

use Filament\Forms;
use App\Models\Category;
use Filament\Support\RawJs;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class CustomProfileInfo extends MyProfileComponent
{
    public ?array $data = [];
    public $user;

    // Daftar kolom yang ingin diambil dari user
    protected $only = [
        'avatar_url',
        'name',
        'email',
        'role',
    ];

     public function mount()
    {
        // Ambil user saat ini
        $this->user = Auth::user();

        // Isi data form dengan data user yang sudah ada
        $userData = [];
        foreach ($this->only as $field) {
            if ($field === 'avatar_url') {
                // Ambil raw value dari database, bypass accessor

                $userData[$field] = $this->user->getAttributes()['avatar_url'];
            } else {
                $userData[$field] = $this->user->$field;
            }
        }

        // Set data form
        $this->form->fill($userData);
    }



    public function render()
    {
        return view('livewire.custom-profile-info');
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->image()
                            ->hintColor('primary')
                            ->avatar()
                            ->alignCenter()
                            ->columnSpanFull()
                            ->imageEditor()
                            ->directory('avatars')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->label(''),
                    ]),

                Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->prefixIcon('heroicon-o-user')
                            ->prefixIconColor('primary')
                            ->label('Nama Lengkap')
                            ->hintColor('primary')
                            ->hintIcon(function () {
                                if ($this->user->role === 'mentor' && $this->user->is_verified) {
                                    return 'heroicon-o-information-circle';
                                }
                                return null;
                            })
                            ->hintIconTooltip('You are a verified Mentor')
                            ->suffixIcon(function () {
                                if ($this->user->role === 'mentor' && $this->user->is_verified) {
                                    return 'heroicon-m-check-badge';
                                }
                                return null;
                            })
                            ->suffixIconColor(function ($get) {
                                if ($get('is_verified')) {
                                    return 'info';
                                } else {
                                    return 'gray';
                                }
                            })
                            ->required()
                            ,
                    ]),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-envelope')
                            ->prefixIconColor('primary')
                            ->label('Email'),

            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        // Validasi data
        $state = $this->form->getState();

        $before = $this->user->attributesToArray();
        $this->user->update($state);

        // Kirim notifikasi
        Notification::make()
            ->success()
            ->icon('heroicon-s-identification')
            ->iconColor('success')
            ->title('Profil berhasil diperbarui')
            ->body('From ' . $before['name'] . ' to ' . $this->user->name)
            ->send();
    }
}
