<?php

namespace App\Statistics;

use App\Statistics;
use Carbon\Carbon;

class PullRequests extends Statistics
{
    const FILENAME = 'pullrequests';

    /**
     * @var array
     */
    private $created;
    /**
     * @var array
     */
    private $closed;
    /**
     * @var array
     */
    private $merged;

    /**
     * @param int $year
     */
    public function storePullRequests(int $year)
    {
        $data = [
            'title' => $year,
            'labels' => $this->getMonthRange($year),
            'generated' => Carbon::now(),
        ];
        $created = $this->fetchCreatedPullRequestsByYear($year);
        $closed = $this->fetchClosedPullRequestsByYear($year);
        $merged = $this->fetchMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Merged', $merged['total'], $this->mergedColor, 'line'),
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
    public function storePullRequestsByRepository(string $repository, int $year)
    {
        $data = [
            'title' => sprintf('%s - %s', $repository, $year),
            'labels' => $this->getMonthRange($year),
            'generated' => Carbon::now(),
        ];
        $created = $this->fetchCreatedPullRequestsByYear($year);
        $closed = $this->fetchClosedPullRequestsByYear($year);
        $merged = $this->fetchMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Merged', $merged[$repository], $this->mergedColor, 'line'),
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
    private function fetchCreatedPullRequestsByYear(int $year): array
    {
        if (!$this->created) {
            $this->created = $this->fetchPullRequestsByStatusAndYear(parent::STATUS_CREATED, $year);
        }
        return $this->created;
    }

    /**
     * @param int $year
     * @return array
     */
    private function fetchClosedPullRequestsByYear(int $year): array
    {
        if (!$this->closed) {
            $this->closed = $this->fetchPullRequestsByStatusAndYear(parent::STATUS_CLOSED, $year);
        }
        return $this->closed;
    }

    /**
     * @param int $year
     * @return array
     */
    private function fetchMergedPullRequestsByYear(int $year): array
    {
        if (!$this->merged) {
            $this->merged = $this->fetchPullRequestsByStatusAndYear(parent::STATUS_MERGED, $year);
        }
        return $this->merged;
    }

    /**
     * @param string $status
     * @param int $year
     * @return array
     */
    private function fetchPullRequestsByStatusAndYear(string $status, int $year): array
    {
        $data = $this->getRangeArray(1, 12);
        if (Carbon::create($year)->isCurrentYear()) {
            $data = $this->getRangeArray(1, date('n'));
        }
        $result = $this->pullRequests
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
