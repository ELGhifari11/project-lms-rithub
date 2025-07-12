<?php

namespace App\Filament\Resources\WalletResource\Widgets;

use App\Filament\Resources\WithdrawalResource\Widgets\WalletStatsOverview;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class BankAccountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        return [
            Stat::make('Bank Account Details', \Illuminate\Support\Str::limit($user->wallet?->bank_name, 15))
            ->description(sprintf(
                '%s -> %s',
                'A/N '.$user->wallet?->account_holder_name,
                '( '.$user->wallet?->bank_account_number . ' )'
            ))
            ->url(route('filament.admin.resources.wallets.index'))
            ->descriptionIcon('heroicon-m-building-library')
            ->color('info')
            ->chart([0, 10, 20, 30, 40, 50, 60, 70])
            ->icon('heroicon-o-credit-card')
            ->extraAttributes([
                'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition',
                'wire:click' => '$dispatch("openModal", "wallet-details")'
            ]),
            ...(new WalletStatsOverview)->getStats()

        ];
    }
}
