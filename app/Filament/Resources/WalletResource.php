<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Wallet;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WalletResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WalletResource\RelationManagers;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Mentor Section';
    protected static ?string $navigationLabel = 'Wallets';
    protected static ?string $pluralLabel = 'Wallets';
    protected static ?string $slug = 'wallets';
    protected static ?string $singularLabel = 'Wallet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('mentor_id')
                    ->default(fn() => Auth::user()->id)
                    ->required(),

                Forms\Components\Section::make('Bank Account Details')
                    ->description('Please enter your bank account information carefully')
                    ->icon('heroicon-o-credit-card')
                    ->iconColor('primary')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('bank_name')
                            ->hintIcon('heroicon-o-question-mark-circle')
                            ->hintColor('primary')
                            ->hintIconTooltip('Account bank ini digunakan untuk melakukan disbursement dana ke akun bank anda')
                            ->required()
                            ->label('Bank Name')
                            ->placeholder('Select your bank')
                            ->columnSpanFull()
                            ->prefixIcon('heroicon-o-building-library')
                            ->prefixIconColor('primary')
                            ->options(function () {
                                $service = app()->make(\App\Http\Interface\PaymentGatewayInterface::class);
                                $banks = $service->getAllBanks();
                                // logger($banks);
                                return collect($banks)
                                    ->filter(fn($bank) => $bank['can_disburse'] === true)
                                    ->pluck('name', 'code')
                                    ->toArray();
                            })
                            ->searchable(),

                        Forms\Components\TextInput::make('account_holder_name')
                            ->required()
                            ->label('Account Holder Name')
                            ->placeholder('Enter account holder name')
                            ->prefixIcon('heroicon-o-user')
                            ->prefixIconColor('primary')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bank_account_number')
                            ->required()
                            ->label('Account Number')
                            ->placeholder('Enter account number')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->prefixIconColor('primary')
                            ->numeric()
                            ->maxLength(20)
                            ->minLength(8)
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (strlen($value) < 8 || strlen($value) > 20) {
                                            $fail("The account number must be between 8 and 20 digits.");
                                        }
                                        if (!preg_match('/^\d+$/', $value)) {
                                            $fail("The account number must contain only numbers.");
                                        }
                                    };
                                }
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Only show wallets belonging to the current user
        $table->modifyQueryUsing(function (Builder $query) {
            return $query->where('mentor_id', Auth::user()->id);
        });

        return $table
            ->description('Account Bank Details')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    // Header row - Bank name dan Account holder
                    Tables\Columns\Layout\Split::make([

                        Tables\Columns\TextColumn::make('bank_name')
                            ->label('')
                            ->icon('heroicon-o-building-library')
                            ->tooltip('Bank Name')
                            ->iconColor('primary')
                            ->limit(25)
                            ->copyable(),

                        Tables\Columns\TextColumn::make('bank_account_number')
                            ->label('')
                            ->icon('heroicon-o-credit-card')
                            ->tooltip('Account Number')
                            ->iconColor('amber')
                            ->copyable()
                            ->color('amber')
                    ]),

                    // Bottom row - Account number dan timestamps
                    Tables\Columns\Layout\Split::make([

                        Tables\Columns\TextColumn::make('account_holder_name')
                            ->label('')
                            ->icon('heroicon-o-user')
                            ->tooltip('Account Holder Name')
                            ->iconColor('gray')
                            ->copyable(),


                        Tables\Columns\Layout\Stack::make([

                            Tables\Columns\TextColumn::make('updated_at')
                                ->label('')
                                ->tooltip('Last Updated')
                                ->icon('heroicon-o-clock')
                                ->dateTime('d M Y, H:i')
                                ->color('gray'),

                        ])->space(3)
                            ->alignment(\Filament\Support\Enums\Alignment::End),
                    ]),
                ])
                    ->space(3),
            ])
            ->contentGrid([
                'sm' => 2,
                'md' => 2,
                'lg' => 2,
                'xl' => 2,
                '2xl' => 2,
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()

            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make()
                    ->visible(fn() => Auth::user()->role === 'mentor'),
            ])
            ->bulkActions([])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->createAnother(false)
                    ->visible(fn() => Auth::user()->role === 'mentor'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWallets::route('/'),
        ];
    }
}
