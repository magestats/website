<?php
declare(strict_types=1);

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
            $this->getDataset('Merged', $merged[$repository]['total'], $this->mergedColor, 'line'),
            $this->getDataset('Created', $created[$repository]['total'], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository]['total'], $this->closedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear(sprintf('%s/%s', $repository, self::FILENAME), $year, $data);
    }

    public function storePullRequestsByRepositoryAndMonth(string $repository, int $month, int $year)
    {
        $data = [
            'title' => sprintf('%s - %s %s', $repository, Carbon::create($year, $month)->englishMonth, $year),
            'labels' => range(1, Carbon::create($year, $month)->daysInMonth),
            'generated' => Carbon::now(),
        ];
        $created = $this->fetchCreatedPullRequestsByYear($year);
        $closed = $this->fetchClosedPullRequestsByYear($year);
        $merged = $this->fetchMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Merged', $merged[$repository]['months'][$month], $this->mergedColor, 'line'),
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
        $data = $this->getRangeArray($year);
        $result = $this->pullRequests
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
