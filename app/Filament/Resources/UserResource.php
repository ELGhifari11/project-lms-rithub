<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Filament\Exports\UserExporter;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
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
            Forms\Components\Section::make('User Information')
                ->description('Manage user personal information')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\FileUpload::make('avatar')
                        ->label('Profile Picture')
                        ->image()
                            ->avatar()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('avatars')
                        ->circleCropper()
                        ->columnSpan(['default' => 2, 'sm' => 2]),
                    // Grid
                    Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->placeholder('Enter full name')
                            ->prefixIcon('heroicon-o-user')
                            ->columnSpan(['default' => 2, 'sm' => 1]),

                    Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('Enter email address')
                        ->prefixIcon('heroicon-o-envelope')
                        ,
                         ]),
                ])
                ->columns(['default' => 2, 'sm' => 2]),

            Forms\Components\Section::make('Authentication')
                ->description('Manage user authentication details')
                ->icon('heroicon-o-key')
                ->schema([

                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->columnSpan(1)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->placeholder('Enter password')
                        ->prefixIcon('heroicon-o-lock-closed')
                        ,

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->columnSpan(1)
                        ->dehydrated(false)
                        ->required(fn (string $context): bool => $context === 'create')
                        ->placeholder('Confirm password')
                        ->prefixIcon('heroicon-o-lock-closed')
                        ->same('password'),

                     Forms\Components\Grid::make(2)
                    ->schema([

                    Forms\Components\Select::make('roles')
                        ->label('User Role')
                        ->relationship('roles', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->prefixIcon('heroicon-o-shield-check')
                        ->columnSpan(1),
                    ]),
                ])
                ->columns(['default' => 1, 'sm' => 1])
                ->collapsible(),
        ]);
}

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            \Filament\Infolists\Components\Section::make('Personal Information')
                ->icon('heroicon-o-user')
                ->schema([
                    \Filament\Infolists\Components\ImageEntry::make('avatar')
                        ->label('Profile Picture')
                        ->circular()
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name))
                        ->columnSpanFull(),

                    \Filament\Infolists\Components\Grid::make(2)
                        ->schema([
                            \Filament\Infolists\Components\TextEntry::make('name')
                                ->label('Full Name')
                                ->icon('heroicon-o-user-circle')
                                ->weight('bold'),

                            \Filament\Infolists\Components\TextEntry::make('email')
                                ->label('Email Address')
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->copyMessage('Email copied')
                                ->copyMessageDuration(1500),
                        ]),
                ])
                ->collapsible(),

            \Filament\Infolists\Components\Section::make('Role & Permissions')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('roles.name')
                        ->label('User Role')
                        ->icon('heroicon-o-shield-check')
                        ->badge()
                        ->color('success'),

                    \Filament\Infolists\Components\TextEntry::make('created_at')
                        ->label('Member Since')
                        ->icon('heroicon-o-calendar')
                        ->date('F j, Y'),

                    \Filament\Infolists\Components\TextEntry::make('updated_at')
                        ->label('Last Updated')
                        ->icon('heroicon-o-clock')
                        ->dateTime('F j, Y - H:i'),
                ])
                ->collapsible(),
        ])
        ->columns(1);
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
                    ->state(fn (?object $record) => match(true) {
                        !$record => 'https://ui-avatars.com/api/?name=default',
                        empty($record->avatar_url) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name),
                        str_contains($record->avatar_url, '/storage/https://') => str_replace(config('app.url').'/storage/', '', $record->avatar_url),
                        default => $record->avatar_url,
                    })
                    ->url(fn () => null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->toggleable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->tooltip('User Name'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->IconColor('info')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Email Address'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable()
                    ->label('Haks Akses')
                    ->icon('heroicon-m-shield-check')
                    ->IconColor(fn($record) => $record->roles->first()?->name ? 'info' : 'gray')
                    ->getStateUsing(fn($record) => $record->roles->first()?->name ?? 'Empty')
                    ->tooltip('Haks Akses User Dalam Sistem'),

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
                        ->icon('heroicon-m-document-magnifying-glass')
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
                           'roles',
                        ]),
                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->headerActions([
                ExportAction::make()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(UserExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UserExporter::class)
            ])

            ->recordUrl(function ($record) {
                if ($record) {
                    return route('filament.admin.resources.users.view', ['record' => $record->id]);
                }
            })
            ;

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
