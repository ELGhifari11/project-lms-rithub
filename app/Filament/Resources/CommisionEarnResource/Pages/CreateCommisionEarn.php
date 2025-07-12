<?php

namespace App\Filament\Resources\CommisionEarnResource\Pages;

use App\Filament\Resources\CommisionEarnResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCommisionEarn extends CreateRecord
{
    protected static string $resource = CommisionEarnResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
