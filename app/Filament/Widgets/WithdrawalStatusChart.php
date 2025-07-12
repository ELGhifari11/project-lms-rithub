<?php

namespace App\Filament\Widgets;

use App\Models\Withdrawal;
use Illuminate\Contracts\View\View;
use Hasnayeen\Themes\Filament\Pages\Themes;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class WithdrawalStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'WithdrawalStatusCharT';
    protected static ?int $contentHeight = 219;
    protected static ?int $sort = 3;
    protected function getThemeColors(): array
    {
        $colorName = (new Themes())->getColor() ?? 'Pink';
        $palette = config('colors.palette2');

        return $palette[ucfirst($colorName)] ?? $palette['Pink'];
    }
    protected function getFooter(): string | View
    {
        $data = [
            'PENDING' => Withdrawal::where('status', 'PENDING')->count(),
            'PROCESSING' => Withdrawal::where('status', 'PROCESSING')->count(),
            'COMPLETED' => Withdrawal::where('status', 'COMPLETED')->count(),
            'FAILED' => Withdrawal::where('status', 'FAILED')->count(),
        ];

        return view('charts.withdrawals-status.footer', ['data' => $data]);
    }

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'WITHDRAWAL STATUS';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $total = Withdrawal::count();
        $completed = Withdrawal::where('status', 'COMPLETED')->count();
        $percentage = $total > 0 ? number_format(($completed / $total) * 100, 2) : 0.00;
        $colors = $this->getThemeColors();

        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 280,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$percentage],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -140,
                    'endAngle' => 130,
                    'hollow' => [
                        'size' => '60%',
                        'background' => 'transparent',
                    ],
                    'track' => [
                        'background' => 'transparent',
                        'strokeWidth' => '100%',
                    ],
                    'dataLabels' => [
                        'show' => true,
                        'name' => [
                            'show' => true,
                            'offsetY' => -10,
                            'fontWeight' => 600,
                            'fontFamily' => 'inherit',
                        ],
                        'value' => [
                            'show' => true,
                            'fontWeight' => 600,
                            'fontSize' => '24px',
                            'fontFamily' => 'inherit',
                        ],
                    ],

                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'horizontal',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => [$colors[0], $colors[1]],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 0.6,
                    'stops' => [0, 100, 100, 100],
                ],
            ],
            'stroke' => [
                'dashArray' => 10,
            ],
            'labels' => ['COMPLETED'],
            'colors' => ['#28a745'],

        ];
    }
}
