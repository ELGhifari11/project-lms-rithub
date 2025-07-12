<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\Categories;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;

class CategoriesResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $pluralLabel = 'Categories';
    protected static ?string $singularLabel = 'Category';
    protected static ?string $slug = 'categories';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Basic Information')
                        ->description('Basic details for this category')
                        ->completedIcon('heroicon-o-check-circle')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Category Name')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->placeholder('Enter category name')
                                ->hint('Must be unique')
                                ->hintIcon('heroicon-m-exclamation-circle')
                                ->hintColor('primary')
                                ->prefixIcon('heroicon-m-tag')
                                ->prefixIconColor('primary'),

                            Forms\Components\RichEditor::make('description')
                                ->label('Description')
                                ->required()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'link',
                                    'bulletList',
                                    'orderedList',
                                    'undo',
                                    'redo',
                                ])
                                ->placeholder('Enter category description'),

                            Forms\Components\FileUpload::make('thumbnail_path')
                                ->label('Thumbnail')
                                ->image()
                                ->imageEditor()
                                ->imageCropAspectRatio('16:9')
                                ->imageResizeTargetWidth('1920')
                                ->imageResizeTargetHeight('1080')
                                ->directory('categories/thumbnails')
                                ->preserveFilenames()
                                ->maxSize(5120) // 5MB
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->hint('Recommended size: 1920x1080px (16:9). Max 5MB')
                                ->hintIcon('heroicon-m-photo'),
                        ]),

                    Forms\Components\Wizard\Step::make('Sub-Categories')
                        ->description('Manage related sub-categories')
                        ->icon('heroicon-o-squares-2x2')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Repeater::make('subCategories')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Grid::make(1)
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->reactive()
                                                ->prefixIconColor('primary')
                                                ->placeholder('Enter sub-category name')
                                                ->prefixIcon('heroicon-m-tag'),

                                            Forms\Components\Textarea::make('description')
                                                ->required()
                                                ->placeholder('Enter sub-category description')
                                                ->rows(3),

                                            Forms\Components\FileUpload::make('thumbnail_path')
                                                ->image()
                                                ->directory('sub-categories/thumbnails')
                                                ->preserveFilenames()
                                                ->maxSize(2048) // 2MB
                                        ]),
                                ])
                                ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                ->collapsible()
                                ->collapsed(true)
                                ->addActionLabel(' + Add Sub-Category')
                                ->grid(3)
                                ->collapseAllAction(
                                    fn(Forms\Components\Actions\Action $action) => $action->label('Collapse All')
                                )
                                ->deleteAction(
                                    fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()
                                )
                                ->reorderableWithButtons(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->skippable()
                    ->persistStepInQueryString()
                    ->submitAction(new HtmlString('<button type="submit" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Create Category</button>')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->searchable()
            ->deferLoading()
            ->heading('Categories')
            ->paginated([5, 10, 15, 20, 25])
            ->defaultPaginationPageOption(10)
            ->poll('5s')
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('No Categories Yet')
            ->emptyStateDescription('Categories will appear here once created.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create Category')
                    ->url(route('filament.admin.resources.categories.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ])
            ->columns([

                // Name Column
                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag')
                    ->iconColor('success')
                    ->tooltip('Name of the category')
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Category name copied!'),

                // Thumbnail Preview
                Tables\Columns\ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->circular()
                    ->defaultImageUrl(url('/default-thumbnail.jpg'))
                    ->tooltip('Category thumbnail image')
                    ->alignCenter(),

                // Sub-Categories Count
                Tables\Columns\TextColumn::make('sub_categories_count')
                    ->label('Sub-Categories')
                    ->counts('subCategories')
                    ->icon('heroicon-o-tag')
                    ->iconColor('warning')
                    ->tooltip('Number of sub-categories')   
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->size('sm')
                    ->formatStateUsing(fn(string $state): string =>
                    "{$state} " . str($state == 1 ? 'Sub-Category' : 'Sub-Categories')->lower()),

                // Created Date
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray')
                    ->tooltip(fn($record): string => 'Created on: ' . $record->created_at->format('d M Y H:i:s'))
                    ->alignCenter(),

                // Updated Date
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->tooltip(fn($record): string => 'Last updated on: ' . $record->updated_at->format('d M Y H:i:s'))
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('has_subcategories')
                    ->label('Filter by Sub-Categories')
                    ->options([
                        'with' => 'With Sub-Categories',
                        'without' => 'Without Sub-Categories',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            if ($data['value'] === 'with') {
                                return $query->whereHas('subCategories');
                            }
                            if ($data['value'] === 'without') {
                                return $query->whereDoesntHave('subCategories');
                            }
                        }
                    })
                    ->indicator(function (array $data): ?string {
                        if (isset($data['value'])) {
                            if ($data['value'] === 'with') {
                                return 'With Sub-Categories';
                            }
                            if ($data['value'] === 'without') {
                                return 'Without Sub-Categories';
                            }
                        }
                        return null;
                    }),

                // Date range filter
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // View Sub-Categories Action
                    Tables\Actions\ViewAction::make()
                        ->tooltip('View sub-categories')
                        ->icon('heroicon-o-squares-2x2'),

                    // Edit Action
                    Tables\Actions\EditAction::make()
                        ->tooltip('Edit this category'),

                    // Delete Action
                    Tables\Actions\DeleteAction::make()
                        ->tooltip('Delete this category')
                ])->tooltip('Actions')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip('Delete selected categories'),

                    // Export selected categories
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Implement your export logic here
                            // Example: return response()->streamDownload(function () use ($records) {...})
                        })
                        ->tooltip('Export selected categories'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('subCategories');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategories::route('/create'),
            'edit' => Pages\EditCategories::route('/{record}/edit'),
        ];
    }
}
