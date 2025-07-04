<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Hasnayeen\Themes\Filament\Pages\Themes;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class UserChart extends ApexChartWidget
{
    use InteractsWithPageFilters, HasWidgetShield;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'userChart';
    protected static ?int $contentHeight = 275;
    protected static ?string $pollingInterval = '1000s';
    protected static ?string $subheading = 'This chart shows the total number of users registered in the last 6 months.';
    protected static ?int $sort = 1;
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'USER REGISTRATION';
    // protected int | string | array $columnSpan = 'full';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

     protected function getThemeColors(): array
     {
         $colorName = (new Themes())->getColor() ?? 'Pink';
         $palette = config('colors.palette2');

         return $palette[ucfirst($colorName)] ?? $palette['Pink'];
     }
    protected function getOptions(): array
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        // Ambil data total pendaftaran pengguna
        $totalData = $this->getTotalData($start, $end);

        $colors = $this->getThemeColors();

        $series = [];
        if ($totalData->isNotEmpty()) {
            $series[] = [
                'name' => 'TOTAL',
                'data' => $totalData->map(fn(TrendValue $value) => $value->aggregate),
                'color' => $colors, // Warna garis
            ];
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => true, // Menampilkan toolbar interaktif (zoom, pan, download SVG/PNG)
                ],
            ],
            'series' => $series,
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '60%',
                ],
            ],
            'xaxis' => [
                'categories' => $totalData->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M y')),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'title' => [ // Judul untuk sumbu X
                    'text' => 'Bulan/Tahun',
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'title' => [ // Judul untuk sumbu Y
                    'text' => 'Jumlah Pengguna',
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 1,
                    'gradientToColors' => $colors,
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100, 100, 100],
                ],
            ],
            // 'stroke' => [
            //     'curve' => 'smooth',
            //     'width' => 1,
            //     'colors' => [$colors[1]],
            // ],
            'grid' => [
                'show' => true, // Menampilkan grid secara keseluruhan
                'borderColor' =>  $colors[1], // Light gray color for subtle grid lines
                'strokeDashArray' => 1, // Gaya garis grid (garis putus-putus kecil)
                'xaxis' => [
                    'lines' => [
                        'show' => true, // Menampilkan garis grid vertikal untuk sumbu X
                    ],
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => true, // Menampilkan garis grid horizontal untuk sumbu Y
                    ],
                ],
            ],
            'tooltip' => [
                'y' => [
                    // Formatter untuk nilai Y di tooltip (menambahkan "Pengguna")
                    'formatter' => 'function (val) { return val + " Pengguna" }',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontFamily' => 'inherit',
                    'fontWeight' => 600,
                    'fontSize' => 8,
                ],
            ],
            'markers' => [
                'size' => 4, // Ukuran penanda (lingkaran) pada setiap titik data
                'colors' => $colors, // Marker color
                'strokeColors' => $colors, // Marker border color
                'strokeWidth' => 2, // Ketebalan border penanda
            ],

        ];
    }

    /**
     * Mengambil data total pengguna
     */
    private function getTotalData($start, $end)
    {
        $query = User::query();
        return Trend::query($query)
            ->dateColumn('created_at')
            ->between(
                start: $start ? Carbon::parse($start) : now()->subMonths(6),
                end: $end ? Carbon::parse($end) : now()
            )
            ->perMonth()
            ->count();
    }


}
