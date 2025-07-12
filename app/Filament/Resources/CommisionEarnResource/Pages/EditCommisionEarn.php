<?php

namespace App\Filament\Resources\CommisionEarnResource\Pages;

use App\Filament\Resources\CommisionEarnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommisionEarn extends EditRecord
{
    protected static string $resource = CommisionEarnResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
