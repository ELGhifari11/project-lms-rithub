<?php

namespace App\Filament\Resources\WithdrawalResource\Widgets;

use App\Filament\Resources\WalletResource\Widgets\BankAccountWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class WalletStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        $balance = $user->wallet?->balance ?? 0;

        return [
            Stat::make('Commission Earn Balance', 'Rp.' . number_format($balance, 2))
                ->description(
                    $user->wallet
                        ? ($user->wallet->bank_name && $user->wallet->account_holder_name && $user->wallet->bank_account_number
                            ? 'Your Current Available Balance.'
                            : 'Please complete your bank account details first.')
                        : 'No wallet data available.'
                )
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->url(route('filament.admin.resources.commission-earnings.index'))
                ->chart($user->wallet?->withdrawals()
                    ->latest()
                    ->take(9)
                    ->pluck('amount')
                    ->toArray() ?? [])
                ->color($balance > 0 ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("openModal", "wallet-details")'
                ]),

            // ...(new BankAccountWidget())->getStats(),
        ];
    }
}
