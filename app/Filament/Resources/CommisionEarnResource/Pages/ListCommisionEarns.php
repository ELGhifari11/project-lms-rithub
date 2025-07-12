<?php

namespace App\Filament\Resources\CommisionEarnResource\Pages;

use App\Filament\Resources\CommisionEarnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommisionEarns extends ListRecords
{
    protected static string $resource = CommisionEarnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
