<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CommisionEarn;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\CommissionEarning;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CommisionEarnResource\Pages;
use App\Filament\Resources\CommisionEarnResource\RelationManagers;
use Filament\Support\Enums\FontWeight;

class CommisionEarnResource extends Resource
{

    /**
     ** PROTECTED PROPERTIES
     * * * * * * * * * * * * * *      * * * * * * * * * * * * *
     * * * * * * * * * * * * * *      * * * * * * * * * * * * *
     ** PROTECTED PROPERTIES
     */
    protected static ?string $model = CommissionEarning::class;
    protected static ?string $navigationGroup = 'Mentor Section';
    protected static ?string $navigationLabel = "Commission";
    protected static ?string $pluralLabel = 'Commission';
    protected static ?string $slug = 'commission-earnings';
    protected static ?string $singularLabel = 'Commission';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $increment = 1;

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
                //
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

            ->defaultSort('created_at', 'desc')
            ->striped()
            ->searchable()
            ->deferLoading()
            ->paginated([5, 10, 15, 20, 25])
            ->defaultPaginationPageOption(10)
            ->poll('5s')
            ->query(function (Builder $query) {
                $query = CommissionEarning::query();
                if (Auth::user()->roles->contains('name', 'super_admin') && Auth::user()->role === 'admin') {
                    return $query;
                } else {
                    return $query->where('mentor_id', Auth::user()->id);
                }
            })
            ->columns([

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/y/H:i', 'Asia/Jakarta')
                    ->sortable()
                    ->iconColor('info') // Calendar icon in blue
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('mentor.name')
                    ->label('Mentor Name')
                    ->sortable()
                    ->visible(fn() => Auth::user()->roles->contains('name', 'super_admin') && Auth::user()->role === 'admin')
                    ->iconColor('primary') // User icon in green
                    ->icon('heroicon-o-user'),

                // Total order items
                Tables\Columns\TextColumn::make('order.items')
                    ->label('Items')
                    ->sortable()
                    ->getStateUsing(function (Model $record) {
                        return $record->order->items->count();
                    })
                    ->iconColor('warning') // Stack icon in yellow/orange
                    ->icon('heroicon-o-rectangle-stack'),

                // Total order items
                Tables\Columns\TextColumn::make('order.final_amount')
                    ->label('GMV')
                    ->prefix('Rp.')
                    ->columnStart('1')
                    ->sortable()
                    ->iconColor('danger') // Money icon in red
                    ->icon('heroicon-o-banknotes'),

                Tables\Columns\TextColumn::make('mentor_commission')
                    ->label('Commission Earn')
                    ->columnStart('1')
                    ->prefix('Rp.')
                    ->sortable()
                    ->iconColor('success') // Commission icon in green
                    ->icon('heroicon-o-banknotes'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     ** GET INFOLIST FUNCTION()      GET INFOLIST FUNCTION()
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     ** GET INFOLIST FUNCTION()      GET INFOLIST FUNCTION()
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Commission Details')
                    ->icon('heroicon-o-banknotes')
                    ->description('Details about the commission transaction')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Transaction Date')
                            ->dateTime('d/m/Y H:i', 'Asia/Jakarta')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('info')
                            ->tooltip('When this commission was earned'),

                        TextEntry::make('mentor.name')
                            ->label('Mentor')
                            ->icon('heroicon-o-user')
                            ->iconColor('primary')
                            ->tooltip('Mentor who earned this commission'),

                        TextEntry::make('mentor_commission')
                            ->label('Commission Amount')
                            ->money('IDR')
                            ->icon('heroicon-o-currency-dollar')
                            ->iconColor('success')
                            ->tooltip('Total commission earned')
                            ->color('success'),

                        TextEntry::make('order.items_count')
                            ->label('Total Orders')
                            ->icon('heroicon-o-shopping-cart')
                            ->iconColor('warning')
                            ->tooltip('Total number of items in this order')
                            ->getStateUsing(function (Model $record) {
                                return $record->order->loadCount('items')->items_count;
                            })
                    ])->columns(2),

                Section::make('Order Information')
                    ->icon('heroicon-o-shopping-cart')
                    ->description('Financial details of the order')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('order.user.name')
                            ->label('Order Name')
                            ->icon('heroicon-o-user')
                            ->iconColor('primary')
                            ->tooltip('Unique order identifier'),

                        TextEntry::make('order.total_amount')
                            ->label('Amount')
                            ->money('IDR')
                            ->icon('heroicon-o-banknotes')
                            ->iconColor('danger')
                            ->tooltip('Original order amount before discounts'),

                        TextEntry::make('order.discount_amount')
                            ->label('Discount')
                            ->money('IDR')
                            ->icon('heroicon-o-gift')
                            ->iconColor('warning')
                            ->tooltip('Applied discount amount'),

                        TextEntry::make('order.final_amount')
                            ->label('Final Amount')
                            ->money('IDR')
                            ->icon('heroicon-o-calculator')
                            ->iconColor('success')
                            ->tooltip('Final order amount after discounts')
                            ->color('success'),
                    ])->columns(2),

                Section::make('Order Items')
                    ->icon('heroicon-o-shopping-bag')
                    ->description('Detailed list of items in this order')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('order.items')
                            ->label('')
                            ->schema([
                                TextEntry::make('increment')
                                    ->label('')
                                    ->numeric()
                                    ->columnStart(1)
                                    ->state(function ($record) {
                                        static::$increment++;
                                        return static::$increment - 1;
                                    })
                                    ->icon('heroicon-o-hashtag')
                                    ->weight(FontWeight::Bold)
                                    ->iconColor('white')
                                    ->tooltip('Order number'),

                                TextEntry::make('item_type')
                                    ->label('Item Type')
                                    ->icon('heroicon-o-tag')
                                    ->iconColor('primary')
                                    ->getStateUsing(function ($record) {
                                        if (!$record->item_type) {
                                            return 'Unknown Type';
                                        }

                                        $record->load('item');

                                        if (!$record->item) {
                                            return 'Item Not Found';
                                        }

                                        $types = [
                                            'App\\Models\\ClassModel' => ['prefix' => 'Course', 'field' => 'title'],
                                            'App\\Models\\WebinarRecording' => ['prefix' => 'Webinar', 'field' => 'title'],
                                            'App\\Models\\User' => ['prefix' => 'Mentor Subscription', 'field' => 'name'],
                                            'App\\Models\\Bundle' => ['prefix' => 'Bundle', 'field' => 'name']
                                        ];

                                        if (isset($types[$record->item_type])) {
                                            $type = $types[$record->item_type];
                                            $value = $record->item->{$type['field']} ?? 'Untitled';
                                            return $type['prefix'] . ' : ' . $value;
                                        }

                                        return $record->item_type;
                                    }),


                                TextEntry::make('interval')
                                    ->label('Subscription Type')
                                    ->icon('heroicon-o-tag')
                                    ->iconColor('info'),

                                TextEntry::make('price')
                                    ->label('Price')
                                    ->money('IDR')
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('danger'),

                                // TextEntry::make('amount')
                                //     ->label('Quantity')
                                //     ->icon('heroicon-o-calculator')
                                //     ->iconColor('warning'),

                                // TextEntry::make('platform_share')
                                //     ->label('Platform Share')
                                //     ->icon('heroicon-o-chart-pie')
                                //     ->iconColor('success'),
                            ])
                            ->columns(4)
                            ->contained()
                            ->tooltip('List of items in this order'),
                    ])->columns(1),
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
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
            'index' => Pages\ListCommisionEarns::route('/'),
            'create' => Pages\CreateCommisionEarn::route('/create'),
            'view' => Pages\ViewCommisionEarn::route('/{record}'),
        ];
    }
}
