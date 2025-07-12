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
    // protected static string $view = 'livewire.custom-profile-info';

    // Daftar kolom yang ingin diambil dari user
    protected $only = [
        'avatar_url',
        'cover_photo_url',
        'name',
        'email',
        'username',
        'phone',
        'bio',
        'role',
        'profession',
        'is_verified',
        'point',
        'preference',
        'price',
        'lifetime_price',
        'social_media',
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
                            ->hintIcon(function () {
                                if ($this->user->role === 'admin') {
                                    return 'heroicon-m-star';
                                } elseif ($this->user->role === 'mentor') {
                                    return 'heroicon-m-academic-cap';
                                } else {
                                    return 'heroicon-m-user';
                                }
                            })
                            ->hintIconTooltip(function () {
                                if ($this->user->role === 'admin') {
                                    return 'You are an Admin';
                                } elseif ($this->user->role === 'mentor') {
                                    return 'You are a Mentor';
                                } else {
                                    return 'You are a Student';
                                }
                            })
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

                        FileUpload::make('cover_photo_url')
                            ->image()
                            ->columnSpanFull()
                            ->hintIcon('heroicon-o-information-circle')
                            ->hintColor('primary')
                            ->hintIconTooltip('Cover photo ini akan di tampilkan sebagai background pada profile mentor di Aplikasi.')
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->directory('covers')
                            ->disk('public')
                            ->visibility('public')
                            ->label('Cover Photo')
                            ->columnSpanFull(),
                    ]),
                Grid::make(2)
                    ->schema([
                        TextInput::make('price')
                            ->visible(fn($get) => $this->user->role === 'mentor')
                            ->hintIcon('heroicon-o-information-circle')
                            ->hintIconTooltip(function (callable $get) {
                                $totalPriceMonthly = $this->user->classesTaught->sum('price');
                                $totalCourse = $this->user->classesTaught->count();
                                return "Anda memiliki {$totalCourse} course" . ($totalCourse > 1 ? 's' : '') . " dengan total harga Rp " . number_format($totalPriceMonthly, 0, ',', '.') . " /bulan.\n\n" .
                                    "Harga berlangganan yang Anda tetapkan akan menjadi biaya bulanan bagi student untuk mengakses seluruh course Anda. Silakan tentukan harga yang sesuai yang akan Anda tawarkan.";
                            })
                            ->hintColor('primary')
                            ->label('Monthly subscription')
                            ->placeholder(function (callable $get) {
                                $totalPriceMonthly = $this->user->classesTaught->sum('price');
                                return number_format($totalPriceMonthly, 0, ',', '.');
                            })
                            // ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->maxValue(1000000)
                            ->mask(RawJs::make('$money($input)'))
                            ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                            ->helperText('Harga ini menentukan harga yang ditawarkan oleh mentor untuk student berlangganan bulanan.'),

                        TextInput::make('lifetime_price')
                            ->visible(fn($get) => $this->user->role === 'mentor')
                            ->hintIcon('heroicon-o-information-circle')
                            ->hintIconTooltip(function (callable $get) {
                                $totalPriceLifetime = $this->user->classesTaught->sum('lifetime_price');
                                $totalCourse = $this->user->classesTaught->count();
                                return "Anda memiliki {$totalCourse} course" . ($totalCourse > 1 ? 's' : '') . " dengan total harga Rp " . number_format($totalPriceLifetime, 0, ',', '.') . " untuk akses seumur hidup.\n\n" .
                                    "Harga berlangganan yang Anda tetapkan akan menjadi biaya sekali bayar bagi student untuk mengakses seluruh course Anda selamanya. Silakan tentukan harga yang sesuai yang akan Anda tawarkan.";
                            })
                            ->hintColor('primary')
                            ->label('Lifetime Subscription')
                            ->placeholder(function (callable $get) {
                                $totalPriceLifetime = $this->user->classesTaught->sum('lifetime_price');
                                return number_format($totalPriceLifetime, 0, ',', '.');
                            })
                            // ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                            ->minValue(0)
                            ->maxValue(10000000)
                            ->helperText('Harga ini menentukan harga yang ditawarkan oleh mentor untuk student berlangganan seumur hidup.')
                    ]),

                Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->prefixIcon('heroicon-o-user')
                            ->prefixIconColor('primary')
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
                            ->label('Nama Lengkap'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('username')
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-user')
                            ->prefixIconColor('primary')
                            ->required()
                            ->label('Username'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-envelope')
                            ->prefixIconColor('primary')
                            ->label('Email'),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('profession')
                            ->label('Profesi')
                            ->prefixIcon('heroicon-o-briefcase')
                            ->prefixIconColor('primary')
                            ->required()
                            ->options(function () {
                                return Category::pluck('name', 'name')->toArray();
                            }),

                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->prefixIcon('heroicon-o-phone')
                            ->prefixIconColor('primary')
                            ->label('Nomor Telepon'),
                    ]),

                Textarea::make('bio')
                    ->label('BIO')
                    ->rows(3)
                    ->visible(fn($get) => $this->user->role === 'mentor')
                    ->placeholder('Ceritakan sedikit tentang diri Anda...'),

                Section::make('Social Media')
                    ->description('Tambahkan tautan ke akun sosial Anda untuk mempromosikan diri Anda.')
                    ->visible(fn($get) => $this->user->role === 'mentor')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('social_media.linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->placeholder('https://URL_ADDRESS')
                                    ->prefixIcon('heroicon-o-link')
                                    ->prefixIconColor('primary')
                                    ->placeholder('linkedin.com/in/username'),

                                TextInput::make('social_media.github')
                                    ->label('GitHub')
                                    ->url()
                                    ->placeholder('https://URL_ADDRESS')
                                    ->prefixIcon('heroicon-o-link')
                                    ->prefixIconColor('primary')
                                    ->placeholder('github.com/username'),

                                TextInput::make('social_media.instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://URL_ADDRESS')
                                    ->prefixIcon('heroicon-o-link')
                                    ->prefixIconColor('primary')
                                    ->placeholder('instagram.com/username'),

                                TextInput::make('social_media.facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->placeholder('https://URL_ADDRESS')
                                    ->prefixIcon('heroicon-o-link')
                                    ->prefixIconColor('primary')
                                    ->placeholder('facebook.com/username'),

                                TextInput::make('social_media.twitter')
                                    ->label('Twitter')
                                    ->url()
                                    ->placeholder('https://URL_ADDRESS')
                                    ->prefixIcon('heroicon-o-link')
                                    ->prefixIconColor('primary')
                                    ->placeholder('twitter.com/username'),

                                TextInput::make('social_media.website')
                                    ->label('Personal Website')
                                    ->url()
                                    ->placeholder('https://URL_ADDRESS')
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->prefixIconColor('primary')
                                    ->placeholder('example.com'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        // Validasi data
        $state = $this->form->getState();

        // Update user dengan data baru
        $this->user->update($state);

        // Kirim notifikasi
        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui')
            ->send();
    }
}
