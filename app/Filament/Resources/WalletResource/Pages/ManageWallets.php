<?php

namespace App\Filament\Resources\WalletResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\WalletResource;
use App\Filament\Resources\WalletResource\Widgets\BankAccountWidget;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\WithdrawalResource\Widgets\WalletStatsOverview;

class ManageWallets extends ManageRecords
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            // ->createAnother(false),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        $widgets = [];
        $user = Auth::user();

        // WalletStatsOverview widget - only for mentors
        if ($user->role === 'mentor') {
            $widgets[] = WalletStatsOverview::class;
        }

        // TransactionStatsWidget - for mentors and admins
        // if (in_array($user->role, ['mentor', 'admin'])) {
        //     $widgets[] = TransactionStatsWidget::class;
        // }

        // // EarningsWidget - only for mentors with verified status
        // if ($user->role === 'mentor' && $user->is_verified) {
        //     $widgets[] = EarningsWidget::class;
        // }

        // // WithdrawalHistoryWidget - for mentors with minimum balance
        // if ($user->role === 'mentor' && $user->wallet_balance >= 100000) {
        //     $widgets[] = WithdrawalHistoryWidget::class;
        // }

        return $widgets;
    }
}
