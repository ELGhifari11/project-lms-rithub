<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use App\Models\ClassModel;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CourseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourseResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class CourseResource extends Resource
{

    /**
     ** PROTECTED PROPERTIES
     * * * * * * * * * * * * * *      * * * * * * * * * * * * *
     * * * * * * * * * * * * * *      * * * * * * * * * * * * *
     ** PROTECTED PROPERTIES
     */
    protected static ?string $model = ClassModel::class;
    protected static ?string $navigationGroup = 'Mentor Section';
    protected static ?string $navigationLabel = "Courses";
    protected static ?string $pluralLabel = 'Courses';
    protected static ?string $slug = 'course';
    protected static ?string $singularLabel = 'Course';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';



    /**
     ** FORM COLUMNS FOR THE FORM.     FORM COLUMNS FOR THE FORM.
     * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     ** FORM COLUMNS FOR THE FORM.     FORM COLUMNS FOR THE FORM.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ========================================================================
                // ** WIZARD SECTION
                // This section contains a wizard form with multiple steps for course creation
                // Step 1: Course Info - Basic course details, pricing, thumbnail
                // Step 2: Course Contents - Educational content management
                // ========================================================================

                Forms\Components\Wizard::make([

                    // ** STEP 1: COURSE INFO
                    // This step contains basic course details, pricing, and thumbnail
                    // ========================================================================
                    Forms\Components\Wizard\Step::make('Course Details')
                        ->icon('heroicon-o-academic-cap')
                        ->completedIcon('heroicon-o-check-circle')
                        ->description('Course information management')
                        ->schema([

                            Forms\Components\Grid::make(4)
                                ->schema([

                                    // ** SECTION: COURSE DETAILS
                                    // This section contains course details such as mentor, title, description, and status
                                    // ========================================================================
                                    Forms\Components\Section::make('Course Details')
                                        ->columnSpan(2)
                                        ->schema([

                                            Forms\Components\Grid::make(2)
                                                ->schema([

                                                    // ** SELECT: MENTOR
                                                    // This field allows the selection of a mentor for the course
                                                    // ========================================================================
                                                    Forms\Components\Select::make('mentor_id')
                                                        ->label('Mentor Course')
                                                        ->columnSpanFull()
                                                        ->prefixIcon('heroicon-o-user-circle')
                                                        ->prefixIconColor('primary')
                                                        ->relationship('mentor', 'name')
                                                        ->searchable()
                                                        ->preload()
                                                        ->default(function () {
                                                            $user = Auth::user();
                                                            if ($user && $user->role === 'mentor') {
                                                                return $user->id;
                                                            }
                                                            return null;
                                                        })
                                                        ->hidden(fn($context) => $context === 'edit' || Auth::user()->role === 'mentor')
                                                        ->dehydratedWhenHidden()
                                                        ->required(),

                                                    // ** TEXT INPUT: TITLE
                                                    // This field allows the user to enter the title of the course
                                                    // ========================================================================
                                                    Forms\Components\TextInput::make('title')
                                                        ->prefixIcon('heroicon-o-academic-cap')
                                                        ->prefixIconColor('primary')
                                                        ->required()
                                                        ->placeholder('PHP for Beginners')
                                                        ->columnSpanFull()
                                                        ->maxLength(255),

                                                    // ** SELECT: SUB CATEGORY ID
                                                    // This field allows the selection of a category for the course
                                                    // ========================================================================
                                                    Forms\Components\Select::make('sub_category_id')
                                                        ->label('Kategori Kelas')
                                                        ->prefixIcon('heroicon-o-tag')
                                                        ->prefixIconColor('primary')
                                                        ->columnSpanFull()
                                                        ->options(function () {
                                                            $subCategories = \App\Models\SubCategory::with('category')->get();
                                                            $groupedOptions = [];

                                                            foreach ($subCategories as $subCategory) {
                                                                $categoryName = $subCategory->category->name;
                                                                $groupedOptions[$categoryName][$subCategory->id] = $subCategory->name;
                                                            }

                                                            return $groupedOptions;
                                                        })
                                                        ->unique('sub_categories', 'name')
                                                        ->required()
                                                        ->preload()
                                                        ->createOptionForm([

                                                            Forms\Components\Select::make('category_id')
                                                                ->label('Category')
                                                                ->relationship('category', 'name')
                                                                ->preload()
                                                                ->createOptionForm([

                                                                    Forms\Components\TextInput::make('name')
                                                                        ->required()
                                                                        ->maxLength(255),

                                                                    Forms\Components\Textarea::make('description')
                                                                        ->maxLength(65535),

                                                                    Forms\Components\FileUpload::make('thumbnail_path')
                                                                        ->image()
                                                                        ->directory('categories')
                                                                        ->visibility('public')
                                                                        ->imageEditor()
                                                                        ->imageEditorAspectRatios([
                                                                            '16:9',
                                                                        ])
                                                                        ->imageEditorMode(2)
                                                                        ->visible(fn() => Auth::user()->role === 'admin')
                                                                        ->imageEditorViewportWidth('1920')
                                                                        ->imageEditorViewportHeight('1080')
                                                                ]),

                                                            Forms\Components\TextInput::make('name')
                                                                ->required()
                                                                ->maxLength(255),

                                                            Forms\Components\Textarea::make('description')
                                                                ->maxLength(65535),

                                                            Forms\Components\FileUpload::make('thumbnail_path')
                                                                ->image()
                                                                ->directory('sub_categories')
                                                                ->visibility('public')
                                                                ->visible(fn() => Auth::user()->role === 'admin')
                                                                ->imageEditor()
                                                                ->imageEditorAspectRatios([
                                                                    '16:9',
                                                                ])
                                                                ->imageEditorMode(2)
                                                                ->imageEditorViewportWidth('1920')
                                                                ->imageEditorViewportHeight('1080')
                                                        ]),
                                                ]),

                                            // ** TEXTAREA: DESCRIPTION
                                            // This field allows the user to enter a description for the course
                                            // ========================================================================
                                            Forms\Components\Textarea::make('description')
                                                ->required()
                                                ->placeholder('Masukkan deskripsi kelas')
                                                ->maxLength(65535),


                                            Forms\Components\Grid::make(2)
                                                ->schema([

                                                    // ** DATE TIME PICKER: CREATED AT
                                                    // This field displays the date and time when the course was created
                                                    // ========================================================================
                                                    Forms\Components\DateTimePicker::make('created_at')
                                                        ->label('Dibuat Pada')
                                                        ->disabled()
                                                        ->dehydrated(false)
                                                        ->visible(fn($context) => $context === 'edit'),

                                                    // ** DATE TIME PICKER: UPDATED AT
                                                    // This field displays the date and time when the course was last updated
                                                    // ========================================================================
                                                    Forms\Components\DateTimePicker::make('updated_at')
                                                        ->label('Diperbarui Pada')
                                                        ->disabled()
                                                        ->dehydrated(false)
                                                        ->visible(fn($context) => $context === 'edit'),

                                                    // ** TOGGLE: STATUS
                                                    // This field allows the user to enable or disable the course
                                                    // ========================================================================
                                                    Forms\Components\Toggle::make('status')
                                                        ->label('Aktifkan Kelas?')
                                                        ->required()
                                                        ->default(true),
                                                ]),
                                        ]),


                                    // ** SECTION: THUMBNAIL - DURATION - PRICE
                                    // This section contains thumbnail, duration, and price fields for the course
                                    // ========================================================================
                                    Forms\Components\Section::make('Thumbnail - Duration - Price')
                                        ->columnSpan(2)
                                        ->schema([

                                            // ** FILE UPLOAD: THUMBNAIL
                                            // This field allows the user to upload a thumbnail for the course
                                            // ========================================================================
                                            Forms\Components\FileUpload::make('thumbnail_path')
                                                ->image()
                                                ->required()
                                                ->label('Thumbnail Course')
                                                ->hintIcon('heroicon-o-question-mark-circle')
                                                ->hintColor('primary')
                                                ->hintIconTooltip('Thumbnail Course ini akan ditampilkan untuk preview pada halaman course.')
                                                ->directory('courses')
                                                ->visibility('public')
                                                ->imageEditor()
                                                ->imageEditorAspectRatios([
                                                    '16:9',
                                                ])
                                                ->imageEditorMode(2)
                                                ->imageResizeMode('cover')
                                                ->imageCropAspectRatio('16:9')
                                                ->imageResizeTargetWidth('1920')
                                                ->imageResizeTargetHeight('1080')
                                                ->imageEditorViewportWidth('1920')
                                                ->imageEditorViewportHeight('1080')
                                                ->loadStateFromRelationshipsUsing(function (Forms\Components\FileUpload $component, $record) {
                                                    // Load raw value untuk edit form
                                                    if ($record) {
                                                        $component->state($record->getAttributes()['thumbnail_path'] ?? null);
                                                    }
                                                }),

                                            Forms\Components\Grid::make(2)
                                                ->schema([

                                                    // ** TEXT INPUT: PRICE
                                                    // This field allows the user to enter the annual price for the course
                                                    // ========================================================================
                                                    Forms\Components\TextInput::make('price')
                                                        ->required()
                                                        ->prefixIconColor('primary')
                                                        ->placeholder('100.000')
                                                        ->mask(RawJs::make('$money($input)'))
                                                        ->dehydrateStateUsing(fn($state) => (int) str_replace([',', '.'], '', $state))
                                                        ->prefix('Rp')
                                                        ->minValue(0),

                                                    // ** TEXT INPUT: LIFETIME PRICE
                                                    // This field allows the user to select the currency for the course
                                                    Forms\Components\TextInput::make('lifetime_price')
                                                        ->required()
                                                        ->prefixIconColor('primary')
                                                        ->placeholder('100.000')
                                                        ->mask(RawJs::make('$money($input)'))
                                                        ->dehydrateStateUsing(fn($state) => (int) str_replace([',', '.'], '', $state))
                                                        ->prefix('Rp')
                                                        ->minValue(0),

                                                    // ** TEXT INPUT: DURATION MINUTES
                                                    // This field allows the user to enter the duration of the course in minutes
                                                    // ========================================================================
                                                    Forms\Components\TextInput::make('duration_minutes')
                                                        ->helperText('Masukkan durasi total playlist')
                                                        ->suffix('minutes')
                                                        ->prefixIcon('heroicon-o-clock')
                                                        ->prefixIconColor('primary')
                                                        ->placeholder('360')
                                                        ->required()
                                                        ->numeric()
                                                        // ->columnSpanFull()
                                                        ->minValue(1),
                                                ]),
                                        ]),
                                ]),
                        ]),

                    // ** STEP 2: COURSE CONTENTS
                    // This step contains educational content management for the course
                    // ========================================================================
                    Forms\Components\Wizard\Step::make('Module & Content Deatils')
                        ->icon('heroicon-o-academic-cap')
                        ->completedIcon('heroicon-o-check-circle')
                        ->description('Course content management')
                        ->schema([

                            // ** REPEATER: EDUCATIONAL CONTENTS
                            // This field allows the user to add educational contents for the course
                            // ========================================================================
                            Forms\Components\Repeater::make('modules')
                                ->label('')
                                ->relationship('modules')
                                ->hintIcon('heroicon-o-question-mark-circle')
                                ->hintColor('primary')
                                ->hintIconTooltip('Disini tempat pengisian konten materi yang akan anda sertakan pada kelas yang akan anda tawarkan ke dalan apliaksi.')
                                ->defaultItems(1)
                                ->reorderable()
                                ->reorderableWithButtons()
                                ->cloneable()
                                ->columnSpanFull()
                                ->addActionLabel('+ Add More Modules')
                                ->collapsible()
                                ->collapsed(fn(Forms\Components\Repeater $component): bool => $component->getItemsCount() > 3)
                                ->itemLabel(
                                    fn(array $state): ?string =>
                                    isset($state['title'], $state['order_index'])
                                        ? $state['order_index'] . ' - ' . $state['title']
                                        : 'New Module'
                                )

                                ->schema([

                                    Forms\Components\Grid::make(4)
                                        ->schema([

                                            // ** SECTION: MODULE INFORMATION
                                            // This section contains module information such as title and order index
                                            // ========================================================================
                                            Forms\Components\Section::make('Module Information')
                                                ->schema([
                                                    Forms\Components\Grid::make(3)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('Title Modul')
                                                                ->hintIcon('heroicon-o-question-mark-circle')
                                                                ->hintColor('primary')
                                                                ->hintIconTooltip('Title Modul Adalah untuk membagi konten menjadi beberapa part. Contoh : Introduction, Functions, Loops, etc.')
                                                                ->placeholder('Introduction to PHP')
                                                                ->prefixIcon('heroicon-o-list-bullet')
                                                                ->prefixIconColor('primary')
                                                                ->required()
                                                                ->reactive()
                                                                ->columnSpan(2)
                                                                ->maxLength(255)
                                                                ->live()
                                                                ->afterStateUpdated(function ($state, $set, $livewire) {
                                                                    session()->put('current_module_name', $state);
                                                                })
                                                                ->default('Title Module'),

                                                            Forms\Components\TextInput::make('order_index')
                                                                ->label('Module Urutan Ke?')
                                                                ->placeholder('1')
                                                                ->prefixIcon('heroicon-o-list-bullet')
                                                                ->prefixIconColor('primary')
                                                                ->reactive()
                                                                ->numeric()
                                                                ->columnSpan(1)
                                                                ->required()
                                                                ->minValue(1)
                                                                ->default(function ($get, $set) {
                                                                    $currentModuleName = $get('title');
                                                                    $existingContents = collect($get('../../modules') ?? []);
                                                                    $maxOrderIndex = $existingContents
                                                                        ->where('title', $currentModuleName)
                                                                        ->pluck('order_index')
                                                                        ->filter()
                                                                        ->max();
                                                                    return $maxOrderIndex ? $maxOrderIndex + 1 : 1;
                                                                })
                                                                ->afterStateUpdated(function ($state, $get, $set) {
                                                                    session()->put('last_order_index_' . $get('title'), $state);
                                                                })
                                                                ->live(),

                                                            Forms\Components\Textarea::make('description')
                                                                ->columnSpanFull(),
                                                        ]),

                                                ])
                                                ->columnSpanFull(),

                                            Forms\Components\Section::make('Course Content Details')
                                                // ->columnSpan(2)
                                                ->schema([
                                                    Forms\Components\Repeater::make('contents')
                                                        ->label('')
                                                        ->relationship('contents')
                                                        ->hintIcon('heroicon-o-question-mark-circle')
                                                        ->hintColor('primary')
                                                        ->hintIconTooltip('masukan konten yang ingin anda masuakn ke dalam module ini')
                                                        ->defaultItems(1)
                                                        ->reorderable()
                                                        ->reorderableWithButtons()
                                                        ->cloneable()
                                                        ->columnSpanFull()
                                                        ->grid(2)
                                                        ->addActionLabel('+ Add More Contents')
                                                        ->collapsible()
                                                        ->collapsed(fn(Forms\Components\Repeater $component): bool => $component->getItemsCount() > 2)
                                                        ->itemLabel(
                                                            fn(array $state): ?string =>
                                                            isset($state['title_content'], $state['order_index'])
                                                                ? "{$state['order_index']} - {$state['title_content']}"
                                                                : 'New Content'
                                                        )
                                                        ->schema([

                                                            // ** SECTION: COURSE CONTENT DETAILS
                                                            // This section contains course content details such as order index, title, and type
                                                            // ========================================================================
                                                            Forms\Components\Grid::make('Course Content Deetails')
                                                                ->columnSpan(2)
                                                                ->schema([

                                                                    // ** TEXT INPUT: TITLE CONTENT
                                                                    // This field allows the user to enter the title of the course content
                                                                    // ========================================================================
                                                                    Forms\Components\TextInput::make('title_content')
                                                                        ->required()
                                                                        ->placeholder('Function PHP Basics')
                                                                        ->prefixIcon('heroicon-o-list-bullet')
                                                                        ->prefixIconColor('primary')
                                                                        ->reactive()
                                                                        ->default('Title Content')
                                                                        ->maxLength(255)
                                                                        ->columnSpanFull(),

                                                                    Forms\Components\Grid::make(2)
                                                                        ->schema([

                                                                            // ** SELECT: TYPE
                                                                            // This field allows the user to select the type of the course content
                                                                            // ========================================================================
                                                                            Forms\Components\Select::make('type')
                                                                                ->label('Type')
                                                                                ->prefixIcon('heroicon-o-folder')
                                                                                ->prefixIconColor('primary')
                                                                                ->options([
                                                                                    'video' => 'Video',
                                                                                    'pdf' => 'PDF',
                                                                                ])
                                                                                ->default('video')
                                                                                ->disabled()
                                                                                ->required(),

                                                                            // ** TEXT INPUT: DURATION
                                                                            // This field allows the user to enter the duration of the course content in minutes
                                                                            // ========================================================================
                                                                            Forms\Components\TextInput::make('duration')
                                                                                ->numeric()
                                                                                ->suffix('min')
                                                                                ->placeholder('30')
                                                                                ->prefixIcon('heroicon-o-clock')
                                                                                ->prefixIconColor('primary')
                                                                                ->required()
                                                                                ->minValue(1),
                                                                        ]),


                                                                    // ** FILE UPLOAD: THUMBNAIL
                                                                    // This field allows the user to upload a thumbnail for the course content
                                                                    // ========================================================================
                                                                    Forms\Components\FileUpload::make('thumbnail_path')
                                                                        ->label('Thumbnail Content')
                                                                        ->hintIcon('heroicon-o-question-mark-circle')
                                                                        ->hintColor('primary')
                                                                        ->hintIconTooltip('Thumbnail Content ini akan ditampilkan untuk preview pada halaman course pada setiap contennya.')
                                                                        ->image()
                                                                        ->required()
                                                                        ->directory('content-thumbnails')
                                                                        ->imageEditor()
                                                                        ->imageEditorAspectRatios([
                                                                            '16:9',
                                                                        ])
                                                                        ->imageResizeMode('cover')
                                                                        ->imageCropAspectRatio('16:9')
                                                                        ->imageResizeTargetWidth('1920')
                                                                        ->imageResizeTargetHeight('1080')
                                                                        ->imageEditorViewportWidth('1920')
                                                                        ->imageEditorViewportHeight('1080')
                                                                        ->loadStateFromRelationshipsUsing(function (Forms\Components\FileUpload $component, $record) {
                                                                            // Load raw value untuk edit form
                                                                            if ($record) {
                                                                                $component->state($record->getAttributes()['thumbnail_path'] ?? null);
                                                                            }
                                                                        }),

                                                                    // ** TEXT INPUT: CONTENT PATH
                                                                    // This field allows the user to enter the URL of the course content
                                                                    // ========================================================================
                                                                    Forms\Components\TextInput::make('content_path')
                                                                        ->placeholder('https://www.youtube.com/watch?v=VIDEO_ID')
                                                                        ->label('YouTube URL')
                                                                        ->required()
                                                                        ->url()
                                                                        ->maxLength(255)
                                                                        ->prefixIcon('heroicon-o-link')
                                                                        ->prefixIconColor('primary')
                                                                        ->helperText('Enter the YouTube video ID or full URL'),

                                                                    Forms\Components\Grid::make(2)
                                                                        ->schema([
                                                                            Forms\Components\TextInput::make('order_index')
                                                                                ->label('Content Urutan Ke?')
                                                                                ->placeholder('1')
                                                                                ->prefixIcon('heroicon-o-list-bullet')
                                                                                ->prefixIconColor('primary')
                                                                                ->reactive()
                                                                                ->numeric()
                                                                                ->columnSpan(1)
                                                                                ->required()
                                                                                ->minValue(1)
                                                                                ->default(function ($get, $set) {
                                                                                    $currentModuleName = $get('title_content');
                                                                                    $existingContents = collect($get('../contents') ?? []);

                                                                                    // Get the last used order index from session
                                                                                    $lastOrderIndex = session()->get('last_order_index_content' . $currentModuleName, 0);

                                                                                    // Get max order index from existing contents
                                                                                    $maxOrderIndex = $existingContents
                                                                                        ->where('title_content', $currentModuleName)
                                                                                        ->pluck('order_index')
                                                                                        ->filter()
                                                                                        ->max();

                                                                                    // Use the higher value between session and existing contents
                                                                                    $nextOrderIndex = max($lastOrderIndex, $maxOrderIndex ?? 0) + 1;

                                                                                    // Store the new index in session
                                                                                    session()->put('last_order_index_content' . $currentModuleName, $nextOrderIndex);

                                                                                    return $nextOrderIndex;
                                                                                })
                                                                                ->afterStateUpdated(function ($state, $get, $set) {
                                                                                    session()->put('last_order_index_content' . $get('title_content'), $state);
                                                                                })
                                                                                ->live(),

                                                                            // ** TOGGLE: IS PREVIEW
                                                                            // This field allows the user to enable or disable the course content as a preview
                                                                            // ========================================================================
                                                                            Forms\Components\Toggle::make('is_preview')
                                                                                ->inline()
                                                                                ->hintIcon('heroicon-o-question-mark-circle')
                                                                                ->hintIconTooltip('Jika ini diaktifkan, maka konten ini akan ditampilkan sebagai preview pada halaman course dan itu bisa di konsumsi secara gratis.')
                                                                                ->hintColor('primary')
                                                                                ->default(false),
                                                                        ]),


                                                                ])
                                                        ]),
                                                ]),





                                        ]),
                                ]),

                        ]),

                    // ** STEP 3: COURSE MILESTONES
                    // This step contains course milestones management for the course
                    // ========================================================================
                    Forms\Components\Wizard\Step::make('Milestones')
                        ->label('Course Milestones')
                        ->icon('heroicon-o-flag')
                        ->completedIcon('heroicon-o-check-circle')
                        ->description('Course milestones management')
                        ->schema([

                            // ** REPEATER: MILESTONES
                            // This field allows the user to add milestones for the course
                            // ========================================================================
                            Forms\Components\Repeater::make('milestones')
                                ->label(' ')
                                ->relationship('milestones')
                                ->hintIcon('heroicon-o-question-mark-circle')
                                ->hintColor('primary')
                                ->hintIconTooltip('Buatlah Milestone dengan judul yang sesuai dengan part modul pada kelas yang anda buat. Milestone ini akan ditampilkan pada halaman course di setiap Module nya untuk membantu siswa mencapai tujuan pembelajaran. jadi misalnya jika anda membuat 3 part modul, maka anda bisa membuat 3 milestone dengan judul yang sesuai dengan part modul tersebut.')
                                ->defaultItems(1)
                                ->reorderable()
                                ->reorderableWithButtons()
                                ->cloneable()
                                ->addActionLabel('+ Add More Milestones')
                                ->collapsible()
                                ->collapsed(fn(Forms\Components\Repeater $component): bool => $component->getItemsCount() > 3)
                                ->columnSpanFull()
                                ->itemLabel(fn(array $state): ?string => 'Milestone Untuk Module -> ' . $state['title'] ?? null)
                                ->schema([

                                    Forms\Components\Grid::make(4)
                                        ->schema([

                                            // ** SECTION: MILESTONE INFORMATION
                                            // This section contains milestone information such as title and description
                                            // ========================================================================

                                            Forms\Components\Section::make('Milestone Details')
                                                ->columnSpanFull()
                                                ->schema([
                                                    // ** SELECT: TITLE
                                                    // This field allows the user to select the title of the milestone
                                                    // ========================================================================
                                                    Forms\Components\Grid::make(1)
                                                        ->schema([
                                                            Forms\Components\Select::make('title')
                                                                ->label('Title')
                                                                ->prefixIcon('heroicon-o-flag')
                                                                ->prefixIconColor('primary')
                                                                ->options(function ($get) {
                                                                    return collect($get('../../modules'))
                                                                        ->pluck('title')
                                                                        ->unique()
                                                                        ->filter()
                                                                        ->mapWithKeys(function ($name) {
                                                                            return [$name => $name] ?? null;
                                                                        });
                                                                })
                                                                ->required()
                                                                ->searchable()
                                                                ->reactive()
                                                                ->preload()
                                                                ->helperText('Select a module name as the milestone title'),

                                                            Forms\Components\Textarea::make('description')
                                                                ->placeholder('Masukkan deskripsi milestone')
                                                                ->required()
                                                                ->maxLength(65535),

                                                            Forms\Components\Textarea::make('learning_objectives')
                                                                ->placeholder('Learning objectives for this milestone')
                                                                ->label('Learning Objectives')
                                                                ->helperText('What students will learn in this milestone')
                                                                ->maxLength(65535),

                                                            Forms\Components\Grid::make(3)
                                                                ->schema([
                                                                    Forms\Components\TextInput::make('required_progress_percentage')
                                                                        ->required()
                                                                        ->placeholder('100')
                                                                        ->prefixIcon('heroicon-o-receipt-percent')
                                                                        ->prefixIconColor('primary')
                                                                        ->numeric()
                                                                        ->minValue(0)
                                                                        ->maxValue(100)
                                                                        ->suffix('%')
                                                                        ->helperText('Required progress %'),

                                                                    Forms\Components\TextInput::make('estimated_hours')
                                                                        ->label('Estimated Hours To Learn')
                                                                        ->numeric()
                                                                        ->placeholder('10')
                                                                        ->prefixIcon('heroicon-o-clock')
                                                                        ->prefixIconColor('primary')
                                                                        ->suffix('hours')
                                                                        ->minValue(1)
                                                                        ->helperText('Time to complete'),

                                                                    Forms\Components\Select::make('difficulty_level')
                                                                        ->label('Dificulty Level')
                                                                        ->prefixIcon('heroicon-o-puzzle-piece')
                                                                        ->prefixIconColor('primary')
                                                                        ->options([
                                                                            'beginner' => 'Beginner',
                                                                            'intermediate' => 'Intermediate',
                                                                            'advanced' => 'Advanced'
                                                                        ])
                                                                        ->default('beginner')
                                                                        ->required(),
                                                                ]),



                                                            Forms\Components\Textarea::make('requirements')
                                                                ->placeholder('Requirements for this milestone')
                                                                ->label('Prerequisites')
                                                                ->helperText('What students should know before starting this milestone')
                                                                ->maxLength(65535),

                                                            Forms\Components\Grid::make(2)
                                                                ->schema([
                                                                    Forms\Components\Toggle::make('is_mandatory')
                                                                        ->label('Mandatory Milestone')
                                                                        ->default(true)
                                                                        ->helperText('Required to complete?'),

                                                                    Forms\Components\Toggle::make('is_active')
                                                                        ->label('Active')
                                                                        ->default(true)
                                                                        ->helperText('Visible to students?'),
                                                                ]),
                                                        ]),
                                                ]),

                                            // ** SECTION: ADDITIONAL RESOURCES
                                            // This section contains additional resources for the milestone
                                            // =======================================================================

                                            Forms\Components\Section::make('Additional Resources')
                                                // ->columnSpan(2)
                                                ->schema([
                                                    Forms\Components\Repeater::make('resources')
                                                        ->label('')
                                                        ->addActionLabel('+ Add More Resources')
                                                        ->defaultItems(1)
                                                        ->reorderable()
                                                        ->grid(2)
                                                        ->collapsible()
                                                        ->collapsed(fn(Forms\Components\Repeater $component): bool => $component->getItemsCount() > 3)
                                                        ->columnSpanFull()
                                                        ->schema([
                                                            Forms\Components\Grid::make(1)
                                                                ->schema([
                                                                    // ** TEXT INPUT: TITLE
                                                                    // This field allows the user to enter the title of the resource
                                                                    // ========================================================================
                                                                    Forms\Components\TextInput::make('title')
                                                                        ->required()
                                                                        ->placeholder('Resource Title')
                                                                        ->columnSpanFull()
                                                                        ->prefixIcon('heroicon-o-clipboard-document-list')
                                                                        ->prefixIconColor('primary')
                                                                        ->helperText('Enter a title for this resource'),

                                                                    // ** TEXT INPUT: URL
                                                                    // This field allows the user to enter the URL of the resource
                                                                    // ========================================================================
                                                                    Forms\Components\TextInput::make('url')
                                                                        ->required()
                                                                        ->url()
                                                                        ->placeholder('https://example.com')
                                                                        ->helperText('Enter the resource URL')
                                                                        ->columnSpanFull()
                                                                        ->prefixIcon('heroicon-o-link')
                                                                        ->prefixIconColor('primary'),
                                                                ])
                                                        ])
                                                ]),

                                        ]),
                                ]),

                        ]),
                ])
                    ->columnSpanFull()
                    ->skippable()
            ]);
    }

    /**
     ** TABLE COLUMNS FOR THE TABLE.     TABLE COLUMNS FOR THE TABLE.
     * * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * * * * *     * * * * * * * * * * * * * * * * * *
     ** TABLE COLUMNS FOR THE TABLE.     TABLE COLUMNS FOR THE TABLE.
     */
    public static function table(Table $table): Table
    {
        return $table

            // ** TABLE SETTINGS FUNCTIONS
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->searchable()
            ->deferLoading()
            ->heading('Courses')
            ->paginated([5, 10, 15, 20, 25])
            ->defaultPaginationPageOption(10)
            ->poll('5s')
            ->query(function () {
                $query = ClassModel::query();
                if (Auth::user()->role === 'admin') {
                    return $query;
                }
                return $query->where('mentor_id', Auth::user()->id);
            })

            // ** TABLE COLUMNS
            // This function contains the columns for the table
            ->columns([
                // ** TEXT COLUMN: TITLE  ===============================================
                Tables\Columns\TextColumn::make('title')
                    ->label('Course Title')
                    ->icon('heroicon-o-academic-cap')
                    ->iconColor(fn($record) => $record->status ? 'info' : 'gray')
                    ->color(fn($record) => $record->status ? 'black' : 'gray')
                    ->weight(fn($record) => $record->status ? FontWeight::Medium : FontWeight::Light)
                    ->tooltip('Course title')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Course title copied')
                    ->copyMessageDuration(1500),

                // ** TEXT COLUMN: MENTOR NAME  =========================================
                Tables\Columns\TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->icon('heroicon-o-user')
                    ->iconColor(fn($record) => $record->status ? 'primary' : 'gray')
                    ->color(fn($record) => $record->status ? 'black' : 'gray')
                    ->tooltip('Course mentor name')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn() => Auth::user()->role === 'mentor'),

                // ** TEXT COLUMN: SUB CATEGORY NAME ==========================================
                Tables\Columns\TextColumn::make('subCategory.name')
                    ->label('Category')
                    ->icon('heroicon-o-tag')
                    ->iconColor(fn($record) => $record->status ? 'success' : 'gray')
                    ->color(fn($record) => $record->status ? 'black' : 'gray')
                    ->tooltip('Course category')
                    ->searchable()
                    ->sortable(),

                // ** TEXT COLUMN: PRICE ==========================================
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->icon('heroicon-o-banknotes')
                    ->iconColor(fn($record) => $record->status ? 'success' : 'gray')
                    ->color(fn($record) => $record->status ? 'black' : 'gray')
                    ->tooltip('Annual subscription price')
                    ->money('IDR')
                    ->sortable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                // ** TOTAL ENROLLMENTS COLUMN ==========================================
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Enrollments')
                    ->counts('enrollments')
                    ->alignCenter()
                    ->icon('heroicon-o-users')
                    ->iconColor(fn($record) => $record->status ? 'success' : 'gray')
                    ->color(fn($record) => $record->status ? 'black' : 'gray')
                    ->tooltip('Total enrollments')
                    ->sortable(),

                // ** ICON COLUMN: STATUS  ==============================================
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status')
                    ->tooltip('Course status')
                    ->onColor('success')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->offColor('gray')
                    ->sortable(),

                // ** TEXT COLUMN: CREATED AT ============================================
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray')
                    ->tooltip('Creation date and time')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ** TEXT COLUMN: UPDATED AT  ============================================
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->tooltip('Last update date and time')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // ** TABLE FILTERS
            // This function contains the filters for the table
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Add Course')
                ->icon('heroicon-o-academic-cap')
                    ,
            ])

            // ** TABLE ACTIONS
            // This function contains the actions for the table
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    ActivityLogTimelineTableAction::make('Activities')
                        ->icon('heroicon-o-queue-list')
                        ->label('Log Activities')
                        ->visible(fn() => Auth::user()->role === 'admin')
                        ->timelineIcons([
                            'created' => 'heroicon-o-check-badge',
                            'updated' => 'heroicon-o-pencil-square',
                        ])
                        ->timelineIconColors([
                            'created' => 'info',
                            'updated' => 'warning',
                        ])
                        ->withRelations(['mentor', 'bookmarks', 'subCategory', 'enrollments', 'educationalContents', 'feedbacks', 'certificates', 'milestones']),

                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->label('View Course')
                        ->openUrlInNewTab(),

                ]),


            ])

            // ** TABLE BULK ACTIONS
            // This function contains the bulk actions for the table
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    /**
     ** GET INFOLIST FUNCTION()      GET INFOLIST FUNCTION()
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     ** GET INFOLIST FUNCTION()      GET INFOLIST FUNCTION()
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Course Overview Section
                \Filament\Infolists\Components\Section::make('Course Overview')
                    ->icon('heroicon-o-academic-cap')
                    ->iconColor('info')
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(3)
                            ->schema([

                                \Filament\Infolists\Components\TextEntry::make('title')
                                    ->label('Course Title')
                                    ->icon('heroicon-o-bookmark')
                                    ->fontFamily(FontFamily::Mono)
                                    ->iconColor('primary')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                                \Filament\Infolists\Components\TextEntry::make('mentor.name')
                                    ->label('Mentor')
                                    ->icon('heroicon-o-user')
                                    ->visible(fn() => Auth::user()->role === 'mentor' ? false : true)
                                    ->fontFamily(FontFamily::Mono)
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                    ->iconColor('primary'),


                                \Filament\Infolists\Components\TextEntry::make('subCategory.name')
                                    ->label('Category')
                                    ->fontFamily(FontFamily::Mono)
                                    ->icon('heroicon-o-tag')
                                    ->badge()
                                    ->color('success'),

                                \Filament\Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->fontFamily(FontFamily::Mono)
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                            ]),

                        \Filament\Infolists\Components\ImageEntry::make('thumbnail_path')
                            ->label('')
                            ->url(fn($state) => $state)
                            ->height(250)
                            ->extraAttributes(['class' => 'rounded-xl']),



                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('Course Description')
                            ->fontFamily(FontFamily::Mono)
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                // Course Details Section
                \Filament\Infolists\Components\Section::make('Course Details')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->iconColor('info')
                    ->columns(3)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('duration_minutes')
                            ->label('Duration')
                            ->icon('heroicon-o-clock')
                            ->fontFamily(FontFamily::Mono)
                            ->iconColor('primary')
                            ->suffix(' minutes')
                            ->numeric(),

                        \Filament\Infolists\Components\TextEntry::make('price')
                            ->label('Annual Price')
                            ->icon('heroicon-o-banknotes')
                            ->fontFamily(FontFamily::Mono)
                            ->iconColor('success')
                            ->money('IDR'),

                        \Filament\Infolists\Components\TextEntry::make('lifetime_price')
                            ->label('Lifetime Price')
                            ->icon('heroicon-o-banknotes')
                            ->fontFamily(FontFamily::Mono)
                            ->iconColor('success')
                            ->money('IDR'),
                    ]),

                // Educational Content Section
                \Filament\Infolists\Components\Section::make('Content Overview')
                    ->icon('heroicon-o-book-open')
                    ->iconColor('info')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('modules')
                            ->label('')
                            ->schema([
                                // Module Information Grid
                                \Filament\Infolists\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('title')
                                            ->label('Module')
                                            ->icon('heroicon-o-folder')
                                            ->fontFamily(FontFamily::Mono)
                                            ->weight(FontWeight::Bold)
                                            ->iconColor('warning'),

                                        \Filament\Infolists\Components\TextEntry::make('description')
                                            ->label('Description')
                                            ->fontFamily(FontFamily::Mono)
                                            ->columnSpan(2),
                                    ]),

                                // Module Contents Section
                                \Filament\Infolists\Components\Section::make('Detail Contents')
                                    ->schema([
                                        \Filament\Infolists\Components\RepeatableEntry::make('contents')

                                            ->label('')
                                            ->grid(3)
                                            ->schema([
                                                \Filament\Infolists\Components\Grid::make(2)
                                                    ->schema([

                                                        \Filament\Infolists\Components\ImageEntry::make('thumbnail_path')
                                                            ->label('')
                                                            ->height(325)
                                                            ->alignCenter()
                                                            ->square()
                                                            ->tooltip('Thumbnail Content')
                                                            ->columnSpanFull()
                                                            ->url(fn($state) => $state)
                                                            ->extraAttributes(['class' => 'rounded-xl']),

                                                        \Filament\Infolists\Components\TextEntry::make('title_content')
                                                            ->label('Content Title')
                                                            ->limit(30)
                                                            ->icon('heroicon-o-document-text')
                                                            ->fontFamily(FontFamily::Mono)
                                                            ->columnSpanFull()
                                                            ->weight(FontWeight::Medium)
                                                            ->iconColor('primary'),

                                                        \Filament\Infolists\Components\TextEntry::make('order_index')
                                                            ->badge()
                                                            ->icon('heroicon-o-hashtag')
                                                            ->fontFamily(FontFamily::Mono),

                                                        \Filament\Infolists\Components\TextEntry::make('is_preview')
                                                            ->label('Preview Status')
                                                            ->badge()
                                                            ->fontFamily(FontFamily::Mono)
                                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Available' : 'Not Available')
                                                            ->color(fn(bool $state): string => $state ? 'success' : 'warning')
                                                            ->icon(fn(bool $state): string => $state ? 'heroicon-o-eye' : 'heroicon-o-eye-slash'),



                                                        \Filament\Infolists\Components\TextEntry::make('type')
                                                            ->badge()
                                                            ->icon('heroicon-o-film')
                                                            ->fontFamily(FontFamily::Mono),

                                                        \Filament\Infolists\Components\TextEntry::make('duration')
                                                            ->label('Duration')
                                                            ->suffix(' minutes')
                                                            ->icon('heroicon-o-clock')
                                                            ->fontFamily(FontFamily::Mono)
                                                            ->iconColor('primary'),

                                                        \Filament\Infolists\Components\TextEntry::make('content_path')
                                                            ->fontFamily(FontFamily::Mono)
                                                            ->limit(30)
                                                            ->url(fn($state) => $state),

                                                    ])
                                            ])
                                    ]),

                            ])
                    ]),

                // Milestones Section
                \Filament\Infolists\Components\Section::make('Course Milestones')
                    ->icon('heroicon-o-trophy')
                    ->iconColor('warning')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('milestones')
                            ->label('')
                            ->schema([

                                \Filament\Infolists\Components\TextEntry::make('title')
                                    ->label('Milestone Title')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                                \Filament\Infolists\Components\TextEntry::make('description')
                                    ->markdown(),

                                \Filament\Infolists\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('difficulty_level')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'beginner' => 'info',
                                                'intermediate' => 'warning',
                                                'advanced' => 'danger',
                                            }),

                                        \Filament\Infolists\Components\TextEntry::make('required_progress_percentage')
                                            ->label('Required Progress')
                                            ->suffix('%')
                                            ->icon('heroicon-o-chart-bar')
                                            ->iconColor('primary'),

                                        \Filament\Infolists\Components\TextEntry::make('estimated_hours')
                                            ->label('Estimated Hours')
                                            ->suffix(' hours')
                                            ->icon('heroicon-o-clock')
                                            ->iconColor('primary'),
                                    ]),

                                \Filament\Infolists\Components\TextEntry::make('learning_objectives')
                                    ->label('Learning Objectives')
                                    ->listWithLineBreaks()
                                    ->bulleted(),

                                \Filament\Infolists\Components\TextEntry::make('requirements')
                                    ->label('Prerequisites')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                            ]),
                    ]),
            ]);
    }

    /**
     ** GET RELATION FUNCTION()      GET RELATION FUNCTION()
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * *       * * * * * * * * * * * * * * * * * *
     ** GET RELATION FUNCTION()      GET RELATION FUNCTION()
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     ** GET PAGES FUNCTION()        GET PAGES FUNCTION()
     * * * * * * * * * * * * *      * * * * * * * * * * * * * * * * * *
     * * * * * * * * * * * * *      * * * * * * * * * * * * * * * * * *
     ** GET PAGES FUNCTION()        GET PAGES FUNCTION()
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
            'view' => Pages\ViewCourse::route('/{record}'),
        ];
    }
}
