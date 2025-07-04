<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Fieldset;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Models\Kantor; // Pastikan untuk mengimport model Kantor


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

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('downloadPdf')
            //     ->label('Unduh PDF')
            //     ->translateLabel()
            //     ->icon('heroicon-o-printer')
            //     ->modalHeading('Pilih Periode Laporan')
            //     ->modalDescription('Silahkan Pilih Rentang Waktu yang Anda Inginkan')
            //     ->modalSubmitActionLabel('Unduh PDF')
            //     ->modalIcon('heroicon-o-printer')
            //     ->modalWidth('md')
            //     ->form([
            //         DatePicker::make('startDate')
            //             ->label('Tanggal Mulai')
            //             ->translateLabel()
            //             ->required(),
            //         DatePicker::make('endDate')
            //             ->label('Tanggal Akhir')
            //             ->translateLabel()
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         // Logic untuk generate PDF
            //         return response()->streamDownload(function () use ($data) {
            //             // Logic untuk generate PDF
            //         }, 'laporan.pdf');
            //     })
            //     ->color('primary')
            //     ->modalAlignment(Alignment::Center)
        ];
    }
}
