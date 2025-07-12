<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;
    protected static ?string $title = 'List Courses';

    protected function getHeaderActions(): array
    {
    return [
            // Actions\CreateAction::make()
            //     ->label('Add Course')
            //     ->icon('heroicon-o-academic-cap')
            //     ,
        ];
    }
}
