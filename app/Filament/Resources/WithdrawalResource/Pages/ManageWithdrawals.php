<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\WithdrawalResource;
use App\Filament\Resources\WalletResource\Widgets\BankAccountWidget;
use App\Filament\Resources\WithdrawalResource\Widgets\WalletStatsOverview;

class ManageWithdrawals extends ManageRecords
{
    protected static string $resource = WithdrawalResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function disableCreateAnother(): bool
    {
        return false;
    }


    protected function authorizeAccess(): void
    {
        $user = Auth::user();

        $canAccess = $user->role === 'admin' || $user->role === 'mentor';

        abort_unless($canAccess, 403);
    }


    protected function getHeaderWidgets(): array
    {
        $widgets = [];
        $user = Auth::user();

        // WalletStatsOverview widget - only for mentors
        if ($user->role === 'mentor') {
            $widgets = [
                BankAccountWidget::class,
            ];
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
