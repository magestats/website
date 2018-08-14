<?php

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
            'title' => $repository,
            'generated' => Carbon::now(),
            'labels' => $this->getMonthRange($year),
        ];
        $created = $this->fetchCreatedIssuesByYear($year);
        $closed = $this->fetchClosedIssuesByYear($year);

        $datasets = [
            $this->getDataset('Created', $created[$repository], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository], $this->closedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear(sprintf('%s/%s', $repository, self::FILENAME), $year, $data);
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
        $data = $this->getRangeArray(1, 12);
        if (Carbon::create($year)->isCurrentYear()) {
            $data = $this->getRangeArray(1, date('n'));
        }
        $result = $this->issues
            ->where($status, '>', Carbon::createFromDate($year)->firstOfYear())
            ->where($status, '<', Carbon::createFromDate($year)->lastOfYear())
            ->orderBy($status, 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $month = Carbon::createFromTimestamp(strtotime($item[$status]))->month;
            $data[$item['repo']][$month]++;
            $data['total'][$month]++;
        }
        return $data;
    }
}
