<?php
namespace App\Filament\Widgets;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Filament\Support\RawJs;
use Flowframe\Trend\TrendValue;
use App\Models\CommissionEarning;
use Hasnayeen\Themes\Filament\Pages\Themes;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommissionEarningsPerMonthChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commissionEarningsPerMonthChart';
    protected static ?string $heading = 'GMV';
    protected static ?string $subheading = 'This chart displays the total GMV earned by the total amount of the orders.';

    protected static ?int $contentHeight = 275;

    protected static ?int $sort = 4;

    protected function getThemeColors(): array
    {
        $colorName = (new Themes())->getColor() ?? 'Pink';
        $palette = config('colors.palette2');

        return $palette[ucfirst($colorName)] ?? $palette['Pink'];
    }


    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        $totalDataMentor = $this->getTotalDataMentor($start, $end);
        $totalDataPlatform = $this->getTotalDataPlatform($start, $end);

        $seriesMentor = [];
        if ($totalDataMentor->isNotEmpty()) {
            $seriesMentor[] = [
                'name' => 'Mentor Earning',
                'data' => $totalDataMentor->map(fn(TrendValue $value) => $value->aggregate),
            ];
        }

        $seriesPlatform = [];
        if ($totalDataPlatform->isNotEmpty()) {
            $seriesPlatform[] = [
                'name' => 'Platform Earning',
                'data' => $totalDataPlatform->map(fn(TrendValue $value) => $value->aggregate),
            ];
        }

        $colors = $this->getThemeColors();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 260,
                // 'parentHeightOffset' => 2,
                'stacked' => true,
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Mentor',
                    'data' => $totalDataMentor->map(fn(TrendValue $value) => $value->aggregate),
                ],
                [
                    'name' => 'Platform',
                    'data' => $totalDataPlatform->map(fn(TrendValue $value) => $value->aggregate),
                ],


            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '60%',
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'show' => true,
                'horizontalAlign' => 'left',
                'position' => 'top',
                'fontFamily' => 'inherit',
                'markers' => [
                    'height' => 12,
                    'width' => 12,
                    'radius' => 12,
                    'offsetX' => -3,
                    'offsetY' => 2,
                ],
                'itemMargin' => [
                    'horizontal' => 10,
                ],
            ],

            'xaxis' => [
                'categories' => $totalDataPlatform->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M y')),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'axisTicks' => [
                    'show' => true,
                ],
                'axisBorder' => [
                    'show' => true,
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
                'offsetX' => -16,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                // 'min' => -200,
                // 'max' => 300,
                'tickAmount' => 6,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 1,
                    'gradientToColors' => [[$colors[1],$colors[1]], $colors[0]],
                    'inverseColors' => false,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100, 100, 100],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                // 'width' => 3,
                'lineCap' => 'round',
            ],
            'grid' => [
                'show' => true,
                'borderColor' =>  $colors[1],
                'strokeDashArray' => 1,
                'xaxis' => [
                    'lines' => [
                        'show' => true,
                    ],
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => true,
                    ],
                ],
            ],
            'colors' => [$colors[1], $colors[0]],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    formatter: function (val, timestamp, opts) {
                        return val
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(val)
                    }
                }
            },
            // tooltip: {
            //     x: {
            //         formatter: function (val) {
            //             return val + ' /23'
            //         }
            //     }
            // }
        }
    JS);
    }

    private function getTotalDataMentor($start, $end)
    {
        $startDate = $start ? Carbon::parse($start) : now()->subMonths(6);
        $endDate = $end ? Carbon::parse($end) : now();

        return Trend::model(CommissionEarning::class)
            ->between(
                start: $startDate,
                end: $endDate
            )
            ->perMonth()
            ->sum('mentor_commission');
    }

    private function getTotalDataPlatform($start, $end)
    {
        $startDate = $start ? Carbon::parse($start) : now()->subMonths(6);
        $endDate = $end ? Carbon::parse($end) : now();

        return Trend::model(CommissionEarning::class)
            ->between(
                start: $startDate,
                end: $endDate
            )
            ->perMonth()
            ->sum('platform_fees');
    }
}
