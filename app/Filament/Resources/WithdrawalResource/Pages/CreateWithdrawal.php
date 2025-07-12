<?php
namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
class CreateWithdrawal extends CreateRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function beforeCreate(): void
    {
        $formData = $this->form->getState();
        logger()->info($formData);

    }
}
