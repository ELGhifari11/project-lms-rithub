<?php

namespace App\Filament\Resources\CommissionSettingResource\Pages;

use App\Filament\Resources\CommissionSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCommissionSetting extends CreateRecord
{
    protected static string $resource = CommissionSettingResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
