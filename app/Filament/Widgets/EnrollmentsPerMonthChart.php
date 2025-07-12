<?php
namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Enrollment;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Hasnayeen\Themes\Filament\Pages\Themes;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EnrollmentsPerMonthChart extends ApexChartWidget
{
    use InteractsWithPageFilters;


    protected static ?string $chartId = 'enrollmentsPerMonth';
    protected static ?string $heading = 'ENROLLMENTS';
    protected static ?string $pollingInterval = '1000s';
    protected static ?string $subheading = 'This chart shows the total number of users enrolled in the last 6 months.';
    // protected int | string | array $columnSpan = 'full';
    protected static ?int $contentHeight = 275;
    protected static ?int $sort = 2;
    
    protected function getThemeColors(): array
    {
        $colorName = (new Themes())->getColor() ?? 'Pink';
        $palette = config('colors.palette2');

        return $palette[ucfirst($colorName)] ?? $palette['Pink'];
    }

    protected function getOptions(): array
    {
        $colors = $this->getThemeColors();

        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        $totalData = $this->getTotalData($start, $end);

        // Format data for chart series
        $series = [];
        if ($totalData->isNotEmpty()) {
            // Class enrollments
            $series[] = [
                'name' => 'Class',
                'data' => $totalData['class']->map(fn(TrendValue $value) => $value->aggregate),
            ];

            // Webinar enrollments
            $series[] = [
                'name' => 'Webinar',
                'data' => $totalData['webinar']->map(fn(TrendValue $value) => $value->aggregate),
            ];

            // Mentor enrollments
            $series[] = [
                'name' => 'Subscription Mentor',
                'data' => $totalData['mentor']->map(fn(TrendValue $value) => $value->aggregate),
            ];

            // Bundle enrollments
            $series[] = [
                'name' => 'Bundle',
                'data' => $totalData['bundle']->map(fn(TrendValue $value) => $value->aggregate),
            ];

            // // Total enrollments
            // $series[] = [
            //     'name' => 'Total',
            //     'type' => 'line',
            //     'data' => $totalData['total']->map(fn(TrendValue $value) => $value->aggregate),
            // ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 260,
                'stacked' => false,
                'toolbar' => [
                    'show' => true,
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
                'categories' => $totalData['total']->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M y')),
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
                    'text' => 'Jumlah Enrollment',
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
            //     'lineCap' => 'round',
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
                'enabled' => false,
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

private function getTotalData($start, $end)
{
    $startDate = $start ? Carbon::parse($start) : now()->subMonths(6);
    $endDate = $end ? Carbon::parse($end) : now();

    // Get enrollments grouped by type
    $classEnrollments = Trend::query(
        Enrollment::query()->where('enrollable_type', 'App\Models\ClassModel')
    )
        ->dateColumn('created_at')
        ->between(start: $startDate, end: $endDate)
        ->perMonth()
        ->count();

    $webinarEnrollments = Trend::query(
        Enrollment::query()->where('enrollable_type', 'App\Models\WebinarRecording')
    )
        ->dateColumn('created_at')
        ->between(start: $startDate, end: $endDate)
        ->perMonth()
        ->count();

    // Mentor
    $mentorEnrollments = Trend::query(Enrollment::query()->where('enrollable_type', 'App\Models\User'))
        ->dateColumn('created_at')
        ->between(start: $startDate, end: $endDate)
        ->perMonth()
        ->count();

    // Bundle
    $bundleEnrollments = Trend::query(Enrollment::query()->where('enrollable_type', 'App\Models\Bundle'))
        ->dateColumn('created_at')
        ->between(start: $startDate, end: $endDate)
        ->perMonth()
        ->count();

    $totalEnrollments = Trend::query(Enrollment::query())
        ->dateColumn('created_at')
        ->between(start: $startDate, end: $endDate)
        ->perMonth()
        ->count();

    // Combine all data into a collection
    $result = collect([
        'class' => $classEnrollments,
        'webinar' => $webinarEnrollments,
        'mentor' => $mentorEnrollments,
        'bundle' => $bundleEnrollments,
        'total' => $totalEnrollments
    ]);

    return $result;
}
}
