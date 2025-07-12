<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Filament\Exports\UserExporter;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Users';

    public static function canBeImpersonated($record): bool
    {
        return $record instanceof \App\Models\User && $record->getKey() !== Auth::user()->id;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Information Section

                Section::make('Profil')
                    ->description('Informasi profil pengguna')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\FileUpload::make('avatar_url')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->imageEditor()
                            ->maxSize(2048),

                        \Filament\Forms\Components\FileUpload::make('cover_photo_url')
                            ->label('Cover Photo')
                            ->image()
                            ->directory('covers')
                            ->imageEditor()
                            ->maxSize(5120),

                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-at-symbol')
                            ->placeholder('Masukkan username'),

                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(100)
                            ->prefixIcon('heroicon-o-user')
                            ->placeholder('Masukkan nama lengkap')
                            ->autocomplete('name'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(100)
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('email@example.com')
                            ->autocomplete('email'),

                        TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-o-phone')
                            ->placeholder('+62xxx')
                            ->mask('9999-9999-9999')
                            ->autocomplete('tel'),



                        Grid::make(2)
                            ->schema([
                                TextInput::make('profession')
                                    ->label('Profesi')
                                    ->maxLength(100)
                                    ->prefixIcon('heroicon-m-briefcase')
                                    ->placeholder('Masukkan profesi'),

                                Select::make('role')
                                    ->label('Hak Akses')
                                    ->relationship('roles', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->optionsLimit(10)
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),
                            ]),

                    ]),
            ])
            ->columns(['lg' => 3]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Profil User')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\ImageEntry::make('avatar_url')
                            ->label('Avatar')
                            ->circular()
                            ->state(fn(?object $record) => match (true) {
                                !$record => 'https://ui-avatars.com/api/?name=default',
                                empty($record->avatar_url) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name),
                                str_contains($record->avatar_url, '/storage/https://') => str_replace(config('app.url') . '/storage/', '', $record->avatar_url),
                                default => $record->avatar_url,
                            })
                            ->columnSpan(1),
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->icon('heroicon-o-user')
                            ->columnSpan(1),
                        TextEntry::make('is_verified')
                            ->label(' ')
                            ->icon(fn($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-x-circle')
                            ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Unverified')
                            ->columnSpan(1),
                        TextEntry::make('username')
                            ->label('Username')
                            ->icon('heroicon-m-at-symbol')
                            ->columnSpan(1),
                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->columnSpan(1),
                        TextEntry::make('phone')
                            ->label('No. HP')
                            ->icon('heroicon-o-phone')
                            ->columnSpan(1),
                        TextEntry::make('profession')
                            ->label('Profesi')
                            ->IconColor('warning')
                            ->badge()
                            ->icon('heroicon-m-briefcase')
                            ->columnSpan(1),
                        TextEntry::make('bio')
                            ->label('Bio')
                            ->icon('heroicon-o-document-text')
                            ->columnSpan(2),
                    ]),
                \Filament\Infolists\Components\Section::make('Informasi Detail')
                    ->icon('heroicon-o-information-circle')
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(2)
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('role')
                                    ->label('Role')
                                    ->badge()
                                    ->icon('heroicon-o-user-group'),

                                TextEntry::make('point')
                                    ->label('Poin')
                                    ->icon('heroicon-m-star'),

                                TextEntry::make('price')
                                    ->label('Harga Subscription')
                                    ->icon('heroicon-o-currency-dollar'),

                                TextEntry::make('lifetime_price')
                                    ->label('Harga Lifetime')
                                    ->icon('heroicon-o-currency-dollar'),
                            ]),

                        TextEntry::make('social_media')
                            ->label('Akun Social Media')
                            ->columnSpan(1)
                            ->icon('heroicon-o-hashtag')
                            ->formatStateUsing(fn($state) => collect($state)->map(fn($item) => ($item['platform'] ?? '-') . ': ' . ($item['url'] ?? '-'))->implode(', ')),
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table

            ->columns([

                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->disk(null)
                    ->tooltip('User Avatar')
                    ->state(fn(?object $record) => match (true) {
                        !$record => 'https://ui-avatars.com/api/?name=default',
                        empty($record->avatar_url) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name),
                        str_contains($record->avatar_url, '/storage/https://') => str_replace(config('app.url') . '/storage/', '', $record->avatar_url),
                        default => $record->avatar_url,
                    })
                    ->url(fn() => null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->toggleable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->tooltip('User Name'),

                Tables\Columns\TextColumn::make('preference')
                    ->label('Preference')
                    ->getStateUsing(function ($record) {
                        $kategori = Category::find($record->preference);
                        if ($kategori) {
                            return $kategori->name;
                        } else {
                            return 'Empty';
                        }
                    })
                    ->badge()
                    ->toggleable()
                    ->searchable()
                    ->icon('heroicon-m-briefcase')
                    ->IconColor('warning')
                    ->tooltip('Profession'),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->toggleable()
                    ->boolean()
                    ->trueIcon('heroicon-m-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip('Verification Status'),

                Tables\Columns\TextColumn::make('point')
                    ->toggleable()
                    ->numeric()
                    ->icon('heroicon-m-star')
                    ->IconColor('warning')
                    ->tooltip('User Points'),

                Tables\Columns\TextColumn::make('username')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->icon('heroicon-m-at-symbol')
                    ->IconColor('primary')
                    ->tooltip('Username'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role Label')
                    ->searchable()
                    ->badge()
                    ->tooltip('User Role'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable()
                    ->label('Haks Akses')
                    ->icon('heroicon-m-shield-check')
                    ->IconColor(fn($record) => $record->roles->first()?->name ? 'info' : 'gray')
                    ->getStateUsing(fn($record) => $record->roles->first()?->name ?? 'Empty')
                    ->tooltip('Haks Akses User Dalam Sistem'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->IconColor('info')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Email Address'),

                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->IconColor('success')
                    ->tooltip('Phone Number'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->icon('heroicon-o-clock')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->IconColor('gray')
                    ->tooltip('Last Login Time'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->icon('heroicon-o-envelope-open')
                    ->IconColor('success')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Email Verification Date'),

                Tables\Columns\TextColumn::make('mentor_subscription_price')
                    ->label('Mentor Price')
                    ->toggleable()
                    ->money('idr')
                    ->getStateUsing(fn($record) => $record->mentor_subscription_price ?? 'Empty')
                    ->icon('heroicon-o-currency-dollar')
                    ->tooltip('Harga yang ditentukan mentor untuk student melakukan subscription'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->deferLoading()
            ->heading('User Lists')
            ->paginated([5, 10, 15, 20, 25])
            ->defaultPaginationPageOption(10)
            ->filters([
                //
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Impersonate::make()
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->tooltip('Login Sebagai User Ini')
                    ->hidden(fn($record) => Auth::user()->id == $record->id || !(app(\App\Settings\KaidoSetting::class)->impersonation_enabled ?? true)),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Action::make('Set Role')
                        ->icon('heroicon-m-adjustments-vertical')
                        ->form([
                            Select::make('role')
                                ->relationship('roles', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->optionsLimit(10)
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name),
                        ])
                        ->action(function (User $record, array $data): void {
                            $roleName = \Spatie\Permission\Models\Role::find($data['role'])->name;
                            $record->roles()->sync([$data['role']]);
                            $record->update([
                                'role' => $roleName
                            ]);

                            // Send notification
                            Notification::make()
                                ->title('Role Updated')
                                ->success()
                                ->icon('heroicon-m-shield-check')
                                ->body("User {$record->name}'s role has been updated to {$roleName}")
                                ->send();
                        }),
                    ActivityLogTimelineTableAction::make('Activities')
                        ->icon('heroicon-m-queue-list')
                        ->label('Log Activities')
                        ->timelineIcons([
                            'created' => 'heroicon-m-check-badge',
                            'updated' => 'heroicon-m-pencil-square',
                        ])
                        ->timelineIconColors([
                            'created' => 'info',
                            'updated' => 'warning',
                        ])
                        ->withRelations([
                            'wallet',
                            'withdrawals',
                            'classesTaught',
                            'userCompletedContents',
                            'bookmarks',
                            'bookmarked',
                            'enrollments',
                            'subscriptions',
                            'orders',
                            'points',
                            'userMilestones',
                            'userBadges',
                            'certificates',
                            'feedbacks',
                            'supportTickets',
                            'ticketResponses',
                            'eventAttendances',
                            'auditLogs',
                            'promoUsages',
                            'commissionSetting',
                            'commissionEarnings'
                        ]),
                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UserExporter::class)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
