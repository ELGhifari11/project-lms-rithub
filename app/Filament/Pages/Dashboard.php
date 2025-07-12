<?php

namespace App\Filament\Pages;

use App\Models\Kantor; // Pastikan untuk mengimport model Kantor
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Contracts\View\View;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;


class Dashboard extends \Filament\Pages\Dashboard
{
    // use HasPageShield;

    // protected function getShieldRedirectPath(): string
    // {
    //     return '/admin/proses'; // Redirect jika user tidak memiliki akses
    // }

    protected static ?string $title = 'Dashboards';
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    use HasFiltersForm;

    public ?string $startDate = null;
    public ?string $endDate = null;

    // protected function getHeaderActions(): array
    // {
    //     return [

    //         Action::make('downloadPdf')
    //             ->label('Report Download')
    //             ->translateLabel()
    //             ->icon('heroicon-o-printer')
    //             ->modalHeading('Pilih Periode Laporan')
    //             ->modalDescription('Silahkan Pilih Rentang Waktu yang Anda Inginkan')
    //             ->modalSubmitActionLabel('Unduh Laporan')
    //             ->modalIcon('heroicon-o-printer')

    //             ->modalWidth('md')
    //             ->form([
    //                 DatePicker::make('startDate')
    //                     ->label('Start Date')
    //                     ->translateLabel(),
    //                 DatePicker::make('endDate')
    //                     ->label('End Date')
    //                     ->translateLabel(),
    //             ])
    //             ->action(function (array $data) {
    //                 // Redirect ke halaman unduh PDF dengan tanggal dari form modal
    //                 $this->redirectRoute('laporan.pdf.download', [
    //                     'startDate' => $data['startDate'],
    //                     'endDate' => $data['endDate'],
    //                 ]);
    //             })
    //             ->color('primary')
    //             ->modalAlignment(Alignment::Center),
    //         // ->slideOver(),

    //         // Notification::make()
    //         // ->title('Unduh Laporan')
    //     ];
    // }
    // public function filtersForm(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Fieldset::make('FILTER')
    //                 ->schema([
    //                     DatePicker::make('startDate')
    //                         ->prefixIcon('heroicon-m-check-circle')
    //                         ->prefixIconColor('primary')
    //                         ->label('Start Date')
    //                         ->translateLabel()
    //                         ->inlineLabel()
    //                         ->required(),
    //                     DatePicker::make('endDate')
    //                         ->prefixIcon('heroicon-m-check-circle')
    //                         ->prefixIconColor('primary')
    //                         ->label('End Date')
    //                         ->translateLabel()
    //                         ->inlineLabel()
    //                         ->required(),
    //                 ])->columns(2),
    //         ]);
    // }
}
