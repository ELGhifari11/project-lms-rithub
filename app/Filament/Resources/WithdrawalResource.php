<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Withdrawal;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WithdrawalResource\Pages;
use App\Filament\Resources\WithdrawalResource\RelationManagers;
use App\Filament\Resources\WithdrawalResource\Pages\ManageWithdrawals;
use Illuminate\Support\Facades\Http;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationGroup = 'Mentor Section';
    protected static ?string $navigationLabel = "Withdrawals";
    protected static ?string $pluralLabel = 'Withdrawals';
    protected static ?string $slug = 'withdrawal';
    protected static ?string $singularLabel = 'withdrawal';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('bank_info')
                    ->label(function () {
                        $wallet = Auth::user()->wallet;
                        if (!$wallet) return '';

                        return
                            'A/N ' . $wallet->account_holder_name;
                    })
                    ->prefixIcon('heroicon-o-building-office-2')
                    ->hintIcon('heroicon-o-question-mark-circle')
                    ->hintIconTooltip('Please make sure your bank account information is correct before proceeding with the withdrawal')
                    ->hintColor('danger')
                    ->prefixIconColor('primary')
                    ->default(function () {
                        $wallet = Auth::user()->wallet;
                        if (!$wallet) return '';

                        return sprintf(
                            '%s - %s',
                            Str::limit($wallet->bank_name, 20),
                            '( ' . $wallet->bank_account_number . ' )'
                        );
                    })
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('mentor_id')
                    ->label('Mentor ID')
                    ->prefix('ID:')
                    ->required()
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->default(fn() => Auth::user()->id)
                    ->numeric(),

                Forms\Components\TextInput::make('wallet_id')
                    ->label('Wallet ID')
                    ->prefix('ID:')
                    ->prefixIcon('heroicon-o-credit-card')
                    ->default(fn($get) => optional(User::find($get('mentor_id'))?->wallet)->id)
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->required()
                    ->numeric(),

                // Main withdrawal form fields
                Forms\Components\TextInput::make('amount')
                    ->label('Withdrawal Amount')
                    ->prefix('Rp.')
                    ->prefixIcon('heroicon-o-banknotes')
                    ->prefixIconColor('primary')
                    ->columnSpanFull()
                    ->numeric()
                    ->default(52500)
                    ->required()
                    ->live()
                    ->reactive()
                    ->placeholder('000.000 + 2.500 admin fee')

                    ->suffix(function ($get) {
                        $amount = (int) $get('amount') - 2500;
                        return $amount > 0 ? 'Net: Rp. ' . number_format($amount, 0, ',', '.') : '';
                    })
                    ->hint(function ($get) {
                        $wallet = User::find($get('mentor_id'))->wallet ?? null;
                        return 'Balance: Rp. ' . number_format($wallet?->balance ?? 0, 0, ',', '.');
                    })
                    ->rules([
                        fn($get) => function ($attribute, $value, $fail) use ($get) {
                            $wallet = optional(User::find($get('mentor_id'))?->wallet);

                            if (!$wallet) {
                                $fail("Wallet not found for this user.");
                                return;
                            }
                            if ($value < 52500) {
                                $fail("Minimum withdrawal (after admin fee) is Rp. 52.500");
                                return;
                            }
                            if ($value > $wallet->balance) {
                                $fail("Insufficient balance. Your current balance is Rp. " . number_format($wallet->balance, 0, ',', '.'));
                            }
                        }
                    ])
                    ->helperText('(Admin fee Rp. 2.500 will be included)'),

                Forms\Components\Toggle::make('process_immediately')
                    ->label('Process Immediately')
                    ->default(false)
                    ->helperText('Enable to process this withdrawal request immediately')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-bolt')
                    ->offIcon('heroicon-o-clock')
                    ->reactive()
                    ->columnSpanFull()
                    ->visible(fn() => Auth::user()->role === 'mentor'),

                Forms\Components\Checkbox::make('confirmed_immediate')
                    ->columnSpanFull()
                    ->label('Saya menyetujui bahwa permintaan ini akan langsung diproses dan tidak dapat dibatalkan.')
                    ->visible(fn($get) => $get('process_immediately') === true)
                    ->required(fn($get) => $get('process_immediately') === true),

                // Timestamp fields
                Forms\Components\DateTimePicker::make('requested_at')
                    ->label('Request Date')
                    ->prefixIcon('heroicon-o-calendar')
                    ->prefixIconColor('primary')
                    ->helperText('Choose the date and time when you want this withdrawal request to be processed (minimum 30 Seconds from now)')
                    ->columnSpanFull()
                    ->default(fn($get) => $get('process_immediately') ? now() : now()->addHour())
                    ->reactive()
                    ->native(false)
                    ->rules([
                        'required',
                        'date',
                        fn($get) => function ($attribute, $value, $fail) use ($get) {
                            $minTime = now()->addMinutes(30);
                            if (strtotime($value) < $minTime->timestamp) {
                                $fail("The request date must be at least 30 Seconds from now.");
                            }
                        }
                    ])
                    ->hidden(fn($get) => $get('process_immediately'))
                    ->dehydratedWhenHidden()
                    ->required(fn($get) => !$get('process_immediately')),

                Forms\Components\Textarea::make('note')
                    ->label('Notes')
                    ->placeholder('Enter Your Notes Here...')
                    ->columnSpanFull()
                    ->visible(fn() => Auth::user()->role === 'mentor'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->searchable()
            ->deferLoading()
            ->heading('Withdrawal Lists')
            ->paginated([5, 10, 15, 20, 25])
            ->defaultPaginationPageOption(10)
            ->poll('5s')
            ->query(function () {
                $query = Withdrawal::query();

                if (Auth::user()->role === 'admin') {
                    return $query;
                }

                return $query->where('mentor_id', Auth::user()->id);
            })

            ->columns([
                Tables\Columns\TextColumn::make('mentor.name')
                    ->label('Mentor Name')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->searchable()
                    ->visible(fn() => Auth::user()->role === 'admin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mentor.wallet.balance')
                    ->label('Wallet Balance')
                    ->icon('heroicon-o-credit-card')
                    ->iconColor('success')
                    ->prefix('Rp.')
                    ->visible(fn() => Auth::user()->role === 'admin')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Request Amount')
                    ->icon('heroicon-o-banknotes')
                    ->iconColor('success')
                    ->prefix('Rp.')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'PROCESSING' => 'info',
                        'COMPLETED' => 'success',
                        'FAILED' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'PENDING' => 'heroicon-o-clock',
                        'PROCESSING' => 'heroicon-o-arrow-path',
                        'COMPLETED' => 'heroicon-o-check-circle',
                        'FAILED' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('note')
                    ->label('Notes')
                    ->icon('heroicon-o-queue-list')
                    ->limit(40)
                    ->iconColor('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Request Date')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Process Date')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('success')
                    ->dateTime()
                    ->sortable(),



                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->icon('heroicon-o-clock')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->icon('heroicon-o-arrow-path')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(
                            fn($record) => (Auth::user()->role === 'mentor' && $record->status === 'PENDING')
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->visible(
                            fn($record) => (Auth::user()->role === 'mentor' && $record->status === 'PENDING')
                        ),
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                ]),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->requiresConfirmation()
                    ->modalHeading('Request Withdrawal')
                    ->modalDescription('')
                    ->modalSubmitActionLabel('Withdraw')
                    ->modalIcon('heroicon-o-paper-airplane')
                    ->modalIconColor('primary')
                    ->icon('heroicon-o-paper-airplane')
                    ->label('Withdraw')
                    ->visible(fn() => Auth::user()->role === 'mentor')
                    ->successNotification(null)
                    // ->mutateFormDataUsing(function (array $data): array {

                    //     if ($data['process_immediately'] === true || $data['process_immediately'] === 1) {
                    //         $data['status'] = 'PROCESSING';
                    //         $data['processed_at'] = now();
                    //     }
                    //     return $data;
                    // })
                    ->before(function (Tables\Actions\CreateAction $action, array &$data) {
                        $user = Auth::user();

                        // Validate user role
                        if ($user->role !== 'mentor') {
                            Notification::make()
                                ->title('Error')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->body('Hanya Mentor Yang Bisa Membuat Withdrawal!')
                                ->danger()
                                ->send();

                            $action->halt();
                            return;
                        }

                        // Validate bank account information
                        if (
                            !$user->wallet ||
                            !$user->wallet->bank_name ||
                            !$user->wallet->bank_account_number ||
                            !$user->wallet->account_holder_name
                        ) {
                            Notification::make()
                                ->danger()
                                ->title('Incomplete Bank Information')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->body('Please complete your bank account details before making a withdrawal.')
                                ->send();

                            $action->halt();
                            return;
                        }
                    })
                    ->after(function ($record, $data) {
                        // $record adalah instance dari model Withdrawal yang baru saja dibuat oleh Filament
                        // $data adalah array data dari form Filament

                        // Cek apakah ini immediate withdrawal
                        $isImmediate = $data['process_immediately'] === true || $data['process_immediately'] === 1;

                        // Format jumlah untuk notifikasi
                        $amountFormatted = number_format($record->amount, 0, ',', '.');

                        try {
                            // Tentukan pesan waktu pemrosesan untuk notifikasi
                            $processTimeMessage = $isImmediate
                                ? '<b>SEGERA</b>'
                                : 'pada <b>' . (is_string($record->requested_at) ? $record->requested_at : $record->requested_at->format('d M Y H:i')) . '</b>';

                            // --- Perubahan Inti di sini ---
                            if ($isImmediate) {
                                // Panggil endpoint backend untuk memicu pemrosesan immediate withdrawal
                                // Asumsi: Anda memiliki route API yang mengarah ke DisbursementController::triggerWithdrawalProcessing
                                // dan api token/sanctum sudah disiapkan untuk otentikasi jika diperlukan.
                                // Gunakan base URL API Anda.
                                $response = Http::withHeaders([
                                    'Accept' => 'application/json',
                                    // 'Authorization' => 'Bearer ' . Auth::user()->createToken('filament-withdrawal-trigger')->plainTextToken, // Jika Anda menggunakan Sanctum dan perlu auth
                                ])->post(route('api.disbursements.trigger-processing'), [ // Pastikan Anda punya named route ini
                                    'withdrawal_id' => $record->id,
                                ]);

                                if ($response->successful()) {
                                    $responseBody = $response->json();
                                    Notification::make()
                                        ->success()
                                        ->title('Penarikan Dibuat & Diproses')
                                        ->icon('heroicon-o-paper-airplane')
                                        ->body(
                                            "Penarikan <b>Rp.{$amountFormatted}</b> Created.\n\n" .
                                                "Akan diproses {$processTimeMessage}.\n" .
                                                "<b>selama 1-3 hari kerja</b>.\n\n" .
                                                'Status akan diperbarui setelah diproses dan tidak bisa mengubah kembali apabila status telah berubah.'
                                        )
                                        ->persistent()
                                        ->send();
                                } else {
                                    // Jika API backend mengembalikan error
                                    $errorMessage = $response->json('message', 'Terjadi kesalahan saat memicu pemrosesan penarikan.');
                                    Notification::make()
                                        ->danger()
                                        ->title('Error Memicu Penarikan')
                                        ->persistent()
                                        ->body("Failed to trigger immediate withdrawal: {$errorMessage} (HTTP Status: {$response->status()})")
                                        ->send();
                                    // Opsional: Anda mungkin ingin log error ini di Filament admin panel juga
                                    logger()->error("Filament: Failed to trigger immediate withdrawal for ID {$record->id}", [
                                        'response_status' => $response->status(),
                                        'response_body' => $response->body(),
                                        'data' => $data,
                                    ]);
                                }
                            } else {
                                // Untuk scheduled withdrawal, tidak ada panggilan API langsung dari frontend
                                // Cukup tampilkan notifikasi bahwa scheduled withdrawal berhasil dibuat
                                Notification::make()
                                    ->success()
                                    ->title('Penarikan Terjadwal Dibuat')
                                    ->icon('heroicon-o-calendar')
                                    ->body(
                                        "Penarikan <b>Rp.{$amountFormatted}</b> Created.\n\n" .
                                            "Akan diproses {$processTimeMessage}.\n" .
                                            "<b>selama 1-3 hari kerja</b>.\n\n" .
                                            'Status akan diperbarui setelah diproses pada jadwal yang ditentukan.'
                                    )
                                    ->persistent()
                                    ->send();
                            }
                        } catch (\Throwable $th) {
                            // Ini menangani error di level Filament (misalnya jaringan, atau jika `Http::post` gagal)
                            $errorMessage = (string) $th->getMessage();
                            Notification::make()
                                ->danger()
                                ->title('Terjadi Error')
                                ->persistent()
                                ->body("Terjadi kesalahan tak terduga: {$errorMessage}")
                                ->send();
                            logger()->error("Filament: Uncaught error in afterCreate hook for withdrawal ID {$record->id}", [
                                'error' => $errorMessage,
                                'trace' => $th->getTraceAsString(),
                                'data' => $data,
                            ]);
                        }
                    })

            ])
            // ;
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Withdrawal Details')
                ->icon('heroicon-o-document-text')->iconColor('primary')
                ->description('Detailed information about this withdrawal request')
                ->schema([
                    TextEntry::make('mentor.name')->label('Mentor Name')
                        ->icon('heroicon-o-user')->iconColor('info')
                        ->tooltip('Name of the mentor requesting withdrawal'),
                    TextEntry::make('amount')->label('Withdrawal Amount')
                        ->icon('heroicon-o-banknotes')->iconColor('warning')
                        ->tooltip('Requested withdrawal amount')->money('IDR'),
                    TextEntry::make('status')->label('Status')->badge()
                        ->icon(fn(string $state): string => match ($state) {
                            'PENDING' => 'heroicon-o-clock',
                            'PROCESSING' => 'heroicon-o-arrow-path',
                            'COMPLETED' => 'heroicon-o-check-circle',
                            'FAILED' => 'heroicon-o-x-circle',
                            default => 'heroicon-o-question-mark-circle',
                        })
                        ->iconColor(fn(string $state): string => match ($state) {
                            'PENDING' => 'warning',
                            'PROCESSING' => 'info',
                            'COMPLETED' => 'success',
                            'FAILED' => 'danger',
                            default => 'gray',
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'PENDING' => 'warning',
                            'PROCESSING' => 'info',
                            'COMPLETED' => 'success',
                            'FAILED' => 'danger',
                            default => 'gray',
                        })
                        ->tooltip('Current status of the withdrawal request'),
                ])->columns(2),

            Section::make('Additional Information')
                ->icon('heroicon-o-information-circle')->iconColor('info')
                ->description('Timestamps and notes related to this withdrawal')
                ->schema([
                    TextEntry::make('note')->label('Notes')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('success')
                        ->markdown()->columnSpanFull(),
                    TextEntry::make('requested_at')->label('Request Date')
                        ->icon('heroicon-o-calendar')->iconColor('warning')
                        ->tooltip('When the withdrawal was requested')->dateTime(),
                    TextEntry::make('processed_at')->label('Process Date')
                        ->icon('heroicon-o-calendar-days')->iconColor('success')
                        ->tooltip('When the withdrawal was processed')->dateTime(),
                    TextEntry::make('created_at')->label('Created At')
                        ->icon('heroicon-o-clock')->iconColor('primary')
                        ->tooltip('When the record was created')->dateTime(),
                    TextEntry::make('updated_at')->label('Last Update')
                        ->icon('heroicon-o-arrow-path')->iconColor('info')
                        ->tooltip('When the record was last updated')->dateTime(),
                ])->columns(2),

            Section::make('Bank Information')
                ->icon('heroicon-o-building-library')->iconColor('success')
                ->description('Bank account details for this withdrawal')
                ->schema([
                    TextEntry::make('mentor.wallet.bank_name')->label('Bank Name')
                        ->icon('heroicon-o-building-office-2')->iconColor('primary')
                        ->limit(20)
                        ->tooltip('Name of the bank'),
                    TextEntry::make('mentor.wallet.bank_account_number')->label('Account Number')
                        ->icon('heroicon-o-identification')->iconColor('warning')
                        ->tooltip('Bank account number'),
                    TextEntry::make('mentor.wallet.account_holder_name')->label('Account Holder')
                        ->icon('heroicon-o-user-circle')->iconColor('info')
                        ->tooltip('Name of the account holder'),
                ])->columns(3),
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
            'index' => Pages\ManageWithdrawals::route('/'),
            // 'create' => Pages\CreateWithdrawal::route('/create'),
        ];
    }
}
