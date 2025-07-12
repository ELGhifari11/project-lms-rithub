<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommissionSettingResource\Pages;
use App\Filament\Resources\CommissionSettingResource\RelationManagers;
use App\Models\CommissionSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommissionSettingResource extends Resource
{

    /**
     ** PROTECTED PROPERTIES
     * * * * * * * * * * * * * *      * * * * * * * * * * * * *
     * * * * * * * * * * * * * *      * * * * * * * * * * * * *
     ** PROTECTED PROPERTIES
     */
    protected static ?string $model = CommissionSetting::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = "Commissions";
    protected static ?string $pluralLabel = 'Commissions';
    protected static ?string $slug = 'commissions';
    protected static ?string $singularLabel = 'Commission';
    protected static ?string $navigationIcon = 'heroicon-o-scale';


    /**
     ** FORM COLUMNS FOR THE FORM.     FORM COLUMNS FOR THE FORM.
     * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     ** FORM COLUMNS FOR THE FORM.     FORM COLUMNS FOR THE FORM.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Commission Settings')
                    ->description('Configure the commission settings for different item types')
                    ->schema([
                        Forms\Components\Select::make('item_type')
                            ->label('Item Type')
                            ->options([
                                'App\Models\Bundle' => 'Bundle',
                                'App\Models\ClassModel' => 'Course',
                                'App\Models\User' => 'Subscription Mentor',
                                'App\Models\WebinarRecording' => 'Webinar Record',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->prefixIcon('heroicon-o-shopping-bag')
                            ->helperText('Select the type of item for commission calculation'),

                        Forms\Components\Select::make('interval')
                            ->label('Subscription Interval')
                            ->options([
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                                'lifetime' => 'Lifetime',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->prefixIcon('heroicon-o-calendar')
                            ->helperText('Select the subscription interval'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('fixed_commission')
                                    ->label('Platform Share Fixed Commission')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->prefixIcon('heroicon-o-banknotes')
                                    ->required()
                                    ->reactive()
                                    ->helperText('Enter the platform share fixed commission amount')
                                    ->visible(fn (callable $get) => !$get('is_percentage')),

                                Forms\Components\TextInput::make('platform_share')
                                    ->label('Platform Share Percentage Commission')
                                    ->numeric()
                                    ->suffix('%')
                                    ->prefixIcon('heroicon-o-receipt-percent')
                                    ->required()
                                    ->reactive()
                                    ->helperText('Enter the platform share percentage')
                                    ->visible(fn (callable $get) => $get('is_percentage')),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_percentage')
                                    ->label('Use Percentage')
                                    ->onIcon('heroicon-o-receipt-percent')
                                    ->offIcon('heroicon-o-banknotes')
                                    ->reactive()
                                    ->helperText('Toggle between percentage or fixed commission'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active Status')
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-x-circle')
                                    ->helperText('Toggle the active status of this commission setting'),
                            ]),
                    ])
                    ->columns(1)
            ]);
    }


    /**
     ** TABLE COLUMNS FOR THE TABLE.     TABLE COLUMNS FOR THE TABLE.
     * * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     ** TABLE COLUMNS FOR THE TABLE.     TABLE COLUMNS FOR THE TABLE.
     */
    public static function table(Table $table): Table
    {
        return $table


            ->defaultSort('is_active', 'desc')
            ->searchable()
            ->paginated([5, 10, 15, 20, 25])
            ->defaultPaginationPageOption(10)
            ->deferLoading()
            ->poll('5s')
            ->striped()
            ->columns([

                Tables\Columns\TextColumn::make('item_type')
                    ->label('Item Type')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'App\Models\Bundle' => 'Bundle',
                            'App\Models\ClassModel' => 'Course',
                            'App\Models\User' => 'Subscription Mentor',
                            'App\Models\WebinarRecording' => 'Webinar Record',
                            default => $state
                        };
                    })
                    ->icon('heroicon-o-shopping-bag')
                    ->iconColor('warning') // Changed to warning for shopping related
                    ->tooltip('Type of Item'),

                Tables\Columns\TextColumn::make('interval')
                    ->label('Subscription')
                    ->searchable()
                    ->badge()
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary') // Changed to info for subscription/payment related
                    ->tooltip('Type of Subscription'),


                Tables\Columns\TextColumn::make('fixed_commission')
                    ->label('Fixed')
                    ->money('IDR')
                    ->icon(fn ($record) => $record->is_percentage ?  'heroicon-o-chart-pie' : 'heroicon-m-chart-pie')
                    ->color(fn ($record) => $record->is_percentage  ? 'gray' : 'info')
                    ->weight(fn($record) => $record->is_percentage ? FontWeight::Thin : FontWeight::Bold)
                    ->iconColor('info')
                    ->tooltip('Fixed Platform Share Amount'),

                Tables\Columns\TextColumn::make('platform_share')
                    ->label('Percentage')
                    ->icon(fn ($record) => $record->is_percentage ? 'heroicon-m-chart-pie' : 'heroicon-o-chart-pie')
                    ->iconColor('success')
                    ->color(fn ($record) => $record->is_percentage  ? 'success' : 'gray')
                    ->weight(fn($record) => $record->is_percentage ? FontWeight::Bold : FontWeight::Thin)
                    ->suffix('%')
                    ->alignCenter()
                    ->tooltip('Percentage Platform Share Amount'),

                Tables\Columns\ToggleColumn::make('is_percentage')
                    ->label('Fixed/Percentage')
                    ->alignCenter()
                    ->onIcon('heroicon-o-chart-pie')
                    ->offIcon('heroicon-o-chart-pie')
                    ->onColor('success') // Changed to emerald for percentage
                    ->offColor('info') // Changed to indigo for fixed amount
                    ->tooltip(fn (bool $state): string => $state ? 'Percentage Commission Active' : 'Fixed Commission Active'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->tooltip(fn (bool $state): string => $state ? 'Status Active' : 'Status Inactive')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('item_type')
                    ->label('Filter by Item Type'),
                Tables\Filters\SelectFilter::make('subscription_type')
                    ->label('Filter by Subscription'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Filter by Status'),
                Tables\Filters\TernaryFilter::make('is_percentage')
                    ->label('Filter by Commission Type'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->tooltip('Edit Commission Setting'),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->tooltip('Delete Commission Setting'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->tooltip('Delete Selected Items'),
                ]),
            ]);
    }


    /**
     ** GET RELATION FUNCTION()      GET RELATION FUNCTION()
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     ** GET RELATION FUNCTION()      GET RELATION FUNCTION()
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    /**
     ** GET PAGES FUNCTION()        GET PAGES FUNCTION()
     * * * * * * * * * * * * *      * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * *      * * * * * * * * * * * * * * * * * *
     ** GET PAGES FUNCTION()        GET PAGES FUNCTION()
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissionSettings::route('/'),
            'create' => Pages\CreateCommissionSetting::route('/create'),
            'edit' => Pages\EditCommissionSetting::route('/{record}/edit'),
        ];
    }
}
