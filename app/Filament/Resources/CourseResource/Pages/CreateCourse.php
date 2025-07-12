<?php

namespace App\Filament\Resources\CourseResource\Pages;

use Filament\Actions;
use App\Models\Milestone;
use App\Models\ClassModel;
use Filament\Actions\Action;
use App\Models\ModuleOfCourse;
use App\Models\EducationalContent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
    protected static ?string $title = 'Create Course';
    protected static ?string $breadcrumb = 'Create Course';
    protected static ?string $slug = 'create-course';
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function beforeFill(): void
    {
        // Runs before the form fields are populated with their default values.
    }

    protected function afterFill(): void
    {
        // Runs after the form fields are populated with their default values.
    }

    protected function beforeValidate(): void
    {
        // Runs before the form fields are validated when the form is submitted.
    }

    protected function afterValidate(): void
    {
        // Runs after the form fields are validated when the form is submitted.
    }

    protected function beforeCreate(): void
    {
        // $formData = $this->form->getState();
        // logger()->info($formData);

    }


    protected function afterCreate(): void
    {
        // $record = $this->getRecord();

        // // Load all relationships for the course
        // $courseWithRelations = $record->loadMissing([
        //     'modules' => function($query) {
        //         $query->orderBy('order')
        //               ->with('contents');
        //     },
        //     'milestones'
        // ]);
        // // Log the complete course data with all relationships
        // logger()->info('Created Course Data:', [
        //     'course' => $courseWithRelations->toArray()
        // ]);
    }

    protected function getFormActions(): array
    {
        return [
            // ...parent::getFormActions(),
            Action::make('create')->action('create')
                ->icon('heroicon-o-plus')
                ,
        ];
    }


}
