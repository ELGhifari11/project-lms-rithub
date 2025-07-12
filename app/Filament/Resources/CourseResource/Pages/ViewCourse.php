<?php

namespace App\Filament\Resources\CourseResource\Pages;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CourseResource;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

}
