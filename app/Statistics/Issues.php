<?php
declare(strict_types=1);

namespace App\Statistics;

use App\Statistics;
use Carbon\Carbon;

class Issues extends Statistics
{
    const FILENAME = 'issues';

    /**
     * @var array
     */
    private $created;
    /**
     * @var array
     */
    private $closed;

    /**
     * @param int $year
     */
    public function storeIssues(int $year)
    {
        $data = [
            'generator' => 'https://magestats.net/',
            'title' => $year,
            'generated' => Carbon::now(),
            'labels' => $this->getMonthRange($year),
        ];
        $created = $this->fetchCreatedIssuesByYear($year);
        $closed = $this->fetchClosedIssuesByYear($year);

        $datasets = [
            $this->getDataset('Created', $created['total'], $this->createdColor),
            $this->getDataset('Closed', $closed['total'], $this->closedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear(self::FILENAME, $year, $data);
    }

    /**
     * @param string $repository
     * @param int $year
     */
    public function storeIssuesByRepository(string $repository, int $year)
    {
        $data = [
            'generator' => 'https://magestats.net/',
            'title' => sprintf('%s - %s', $repository, $year),
            'generated' => Carbon::now(),
            'labels' => $this->getMonthRange($year),
        ];
        $created = $this->fetchCreatedIssuesByYear($year);
        $closed = $this->fetchClosedIssuesByYear($year);

        $datasets = [
            $this->getDataset('Created', $created[$repository]['total'], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository]['total'], $this->closedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear(sprintf('%s/%s', $repository, self::FILENAME), $year, $data);
    }

    /**
     * @param string $repository
     * @param int $year
     */
    public function storeIssuesByRepositoryAndMonth(string $repository, int $month, int $year)
    {
        $data = [
            'generator' => 'https://magestats.net/',
            'title' => sprintf('%s - %s %s', $repository, Carbon::create($year, $month)->englishMonth, $year),
            'generated' => Carbon::now(),
            'labels' => range(1, Carbon::create($year, $month)->daysInMonth),
        ];
        $created = $this->fetchCreatedIssuesByYear($year);
        $closed = $this->fetchClosedIssuesByYear($year);

        $datasets = [
            $this->getDataset('Created', $created[$repository]['months'][$month], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository]['months'][$month], $this->closedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear(sprintf('%s/%s/%d', $repository, self::FILENAME, $month), $year, $data);
    }

    /**
     * @param int $year
     * @return array
     */
    private function fetchCreatedIssuesByYear(int $year): array
    {
        if (!$this->created) {
            $this->created = $this->fetchIssuesByStatusAndYear(parent::STATUS_CREATED, $year);
        }
        return $this->created;
    }

    /**
     * @param int $year
     * @return array
     */
    private function fetchClosedIssuesByYear(int $year): array
    {
        if (!$this->closed) {
            $this->closed = $this->fetchIssuesByStatusAndYear(parent::STATUS_CLOSED, $year);
        }
        return $this->closed;
    }

    /**
     * @param string $status
     * @param int $year
     * @return array
     */
    private function fetchIssuesByStatusAndYear(string $status, int $year): array
    {
        $data = $this->getRangeArray($year);
        $result = $this->issues
            ->where($status, '>', Carbon::createFromDate($year)->firstOfYear())
            ->where($status, '<', Carbon::createFromDate($year)->lastOfYear())
            ->orderBy($status, 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $month = Carbon::createFromTimestamp(strtotime($item[$status]))->month;
            $day = Carbon::createFromTimestamp(strtotime($item[$status]))->day;
            $data[$item['repo']]['total'][$month]++;
            $data[$item['repo']]['months'][$month][$day]++;
            $data['total'][$month]++;
        }
        return $data;
    }
}
