<?php

namespace App\Service;

use App\Entity\Deal;
use App\Entity\Workspace;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Repository\DealRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class ChartService
{
    private const PRIMARY = '#2600bc';
    private const SECONDARY = '#ef6e4b';
    private const TERTIARY = '#0fcce3';

    private const MONTHS = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

    public function __construct(
        private CompanyRepository $companyRepository,
        private ContactRepository $contactRepository,
        private DealRepository $dealRepository,
        private ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function createLastYearActivityChart(Workspace $workspace): Chart
    {
        $dealsByMonth = $this->dealRepository->findCountFromLastYearByMonth($workspace);
        $companiesByMonth = $this->companyRepository->findCountFromLastYearByMonth($workspace);
        $contactsByMonth = $this->contactRepository->findCountFromLastYearByMonth($workspace);

        $dealData = $this->setLastYearData($dealsByMonth);
        $companyData = $this->setLastYearData($companiesByMonth);
        $contactData = $this->setLastYearData($contactsByMonth);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $labels = $this->getMonthsNames();

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Contacts',
                    'fill' => false,
                    'backgroundColor' => self::PRIMARY,
                    'borderColor' => self::PRIMARY,
                    'data' => array_values($contactData),
                ],
                [
                    'label' => 'Companies',
                    'fill' => false,
                    'backgroundColor' => self::SECONDARY,
                    'borderColor' => self::SECONDARY,
                    'data' => array_values($companyData),
                ],
                [
                    'label' => 'Deals',
                    'fill' => false,
                    'backgroundColor' => self::TERTIARY,
                    'borderColor' => self::TERTIARY,
                    'data' => array_values($dealData),
                ],
            ],
        ]);

        $maxVal = max([...$dealData, ...$companyData, ...$contactData]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' =>
                        [
                            'min' => 0,
                            'max' => $maxVal > 0 ? ceil($maxVal / 10) * 10 : 10,
                        ]
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'tension' => 0,
                ],
            ],
        ]);

        return $chart;
    }

    public function createActiveDealsChart(Workspace $workspace): Chart
    {
        $dealGroups = $this->dealRepository->findCountGroupByStage($workspace);

        $chartData = $this->getDealStagesData($dealGroups);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => array_map(fn ($stage) => ucwords($stage), Deal::ACTIVE_STAGES),
            'datasets' => [
                [
                    'backgroundColor' => [self::PRIMARY, self::SECONDARY, self::TERTIARY],
                    'data' => $chartData,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' =>
                    [
                        'min' => 0,
                        'max' => max($chartData) > 0 ? ceil(max($chartData) / 10) * 10 : 10,
                    ]],
                ],
            ],
            'legend' => [
                'display' => false,
            ],
        ]);


        return $chart;
    }

    private function getLastYearMonths(): array
    {
        $currentMonth = date('m');

        $counter = (int) $currentMonth;

        $lastYearMonths = [(string) $counter];

        for ($i = 0; $i < 11; $i++) {
            $counter--;

            if ($counter === 0) {
                $counter = 12;
            }

            $lastYearMonths[] = (string) $counter;
        }

        $lastYearMonths = array_reverse($lastYearMonths);

        return $lastYearMonths;
    }

    public function getMonthsNames(): array
    {
        $months = $this->getLastYearMonths();

        $names = [];

        foreach ($months as $m) {
            $names[] = self::MONTHS[$m - 1];
        }

        return $names;
    }

    public function setLastYearData(array $items): array
    {

        $dealMonths = [];

        foreach ($items as $d) {
            $dealMonths[$d['dMonth']] = $d['dCount'];
        }

        $lastYearMonths = $this->getLastYearMonths();

        $data = [];

        foreach ($lastYearMonths as $m) {
            $data[$m] = $dealMonths[(int) $m] ?? 0;
        }

        return $data;
    }

    private function getDealStagesData(array $dealGroups): array
    {

        $dealsGroupsAssociative = [];

        foreach ($dealGroups as $d) {
            $dealsGroupsAssociative[$d['stage']] = $d['dCount'];
        }

        $chartData = [];

        foreach (Deal::ACTIVE_STAGES as $stage) {
            $chartData[] = $dealsGroupsAssociative[$stage] ?? 0;
        }

        return $chartData;
    }
}
