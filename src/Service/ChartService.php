<?php

namespace App\Service;

use App\Entity\Workspace;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Repository\DealRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class ChartService
{
    private const BLUE = '#bacadb';
    private const GREEN = '#a7d1ae';
    private const GRAY = '#d0d0e8';

    public function __construct(
        private CompanyRepository $companyRepository,
        private ContactRepository $contactRepository,
        private DealRepository $dealRepository,
        private ChartBuilderInterface $chartBuilder,
    ) {
    }

    /** @param int[] $data */
    private function calcMaxRange(array $data): int {
        return  ceil(max($data) / 20) * 20;
    }

    public function createEntityCountChart(Workspace $workspace): Chart
    {
        $companyCount = $this->companyRepository->findAllCountByWorkspace($workspace);
        $contactCount = $this->contactRepository->findAllCountByWorkspace($workspace);
        $dealCount = $this->dealRepository->findAllCountByWorkspace($workspace);

        $chartData = [$companyCount, $contactCount, $dealCount];

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => ['Companies', 'Contacts', 'Deals'],
            'datasets' => [
                [
                    'legend' => [
                        'display' => false,
                    ],
                    'backgroundColor' => [self::BLUE, self::GREEN, self::GRAY],
                    // 'borderColor' => [],
                    'data' => $chartData,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' =>
                        [
                            'min' => 0,
                            'max' => $this->calcMaxRange($chartData),
                        ]
                    ],
                ],
            ],
        ]);

        return $chart;
    }
}
