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
        $this->reset();
        $data = [
            'title' => $year,
            'labels' => $this->getMonthRange($year),
            'generated' => Carbon::now(),
        ];
        $created = $this->getCreatedPullRequestsByYear($year);
        $closed = $this->getClosedPullRequestsByYear($year);
        $merged = $this->getMergedPullRequestsByYear($year);
        $firstTimeContributors = $this->getFirstTimeContributorsYear($year);
        $totalContributors = $this->getTotalContributorsByYearAndMonth($year);

        $datasets = [
            $this->getDataset('Merged', $merged['total'], $this->mergedColor, 'line'),
            $this->getDataset('Created', $created['total'], $this->createdColor),
            $this->getDataset('Closed', $closed['total'], $this->closedColor),
        ];

        $data['total'] = [
            'merged' => $this->countTotals($merged['total']),
            'created' => $this->countTotals($created['total']),
            'closed' => $this->countTotals($closed['total']),
            'rejected' => (int)implode('', $this->getRejected([$this->countTotals($closed['total'])], [$this->countTotals($merged['total'])])),
            'acceptance_rate' => implode('', $this->getAcceptanceRate([$this->countTotals($closed['total'])], [$this->countTotals($merged['total'])])),
            'first_time_contributors' => $this->countTotals($firstTimeContributors['total']),
            'total_contributors' => $this->countTotals($totalContributors['contributors']['total']),
        ];
        $data['datasets'] = $datasets;
        $data['_data'] = [
            'Merged' => $merged['total'],
            'Created' => $created['total'],
            'Closed' => $closed['total'],
            'Rejected' => $this->getRejected($closed['total'], $merged['total']),
            'Acceptance Rate' => $this->getAcceptanceRate($closed['total'], $merged['total']),
            'First Time Contributors' => $firstTimeContributors['total'],
            'Total Contributors' => $totalContributors['total'],
        ];
        $this->storeDataByYear(self::FILENAME, $year, $data);
    }

    /**
     * @param string $repository
     * @param int $year
     */
    public function storePullRequestsByRepository(string $repository, int $year)
    {
        $this->reset();
        $data = [
            'title' => sprintf('%s - %s', $repository, $year),
            'labels' => $this->getMonthRange($year),
            'generated' => Carbon::now(),
        ];
        $created = $this->getCreatedPullRequestsByYear($year);
        $closed = $this->getClosedPullRequestsByYear($year);
        $merged = $this->getMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Merged', $merged[$repository]['total'], $this->mergedColor, 'line'),
            $this->getDataset('Created', $created[$repository]['total'], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository]['total'], $this->closedColor),
        ];

        $data['total'] = [
            'merged' => $this->countTotals($merged[$repository]['total']),
            'created' => $this->countTotals($created[$repository]['total']),
            'closed' => $this->countTotals($closed[$repository]['total']),
            'rejected' => (int)implode('', $this->getRejected([$this->countTotals($closed[$repository]['total'])], [$this->countTotals($merged[$repository]['total'])])),
            'acceptance_rate' => implode('', $this->getAcceptanceRate([$this->countTotals($closed[$repository]['total'])], [$this->countTotals($merged[$repository]['total'])])),
        ];
        $data['datasets'] = $datasets;
        $data['_data'] = [
            'Merged' => $merged[$repository]['total'],
            'Created' => $created[$repository]['total'],
            'Closed' => $closed[$repository]['total'],
            'Rejected' => $this->getRejected($closed[$repository]['total'], $merged[$repository]['total']),
            'Acceptance Rate' => $this->getAcceptanceRate($closed[$repository]['total'], $merged[$repository]['total']),
        ];
        $this->storeDataByYear(sprintf('%s/%s', $repository, self::FILENAME), $year, $data);
    }

    public function storePullRequestsByRepositoryAndMonth(string $repository, int $month, int $year)
    {
        $this->reset();
        $data = [
            'title' => sprintf('%s - %s %s', $repository, Carbon::create($year, $month)->format('F'), $year),
            'labels' => range(1, Carbon::create($year, $month)->daysInMonth),
            'generated' => Carbon::now(),
        ];
        $created = $this->getCreatedPullRequestsByYear($year);
        $closed = $this->getClosedPullRequestsByYear($year);
        $merged = $this->getMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Merged', $merged[$repository]['months'][$month], $this->mergedColor, 'line'),
            $this->getDataset('Created', $created[$repository]['months'][$month], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository]['months'][$month], $this->closedColor),
        ];

        $data['total'] = [
            'merged' => $this->countTotals($merged[$repository]['months'][$month]),
            'created' => $this->countTotals($created[$repository]['months'][$month]),
            'closed' => $this->countTotals($closed[$repository]['months'][$month]),
            'rejected' => (int)implode('', $this->getRejected([$this->countTotals($closed[$repository]['months'][$month])], [$this->countTotals($merged[$repository]['months'][$month])])),
            'acceptance_rate' => implode('', $this->getAcceptanceRate([$this->countTotals($closed[$repository]['months'][$month])], [$this->countTotals($merged[$repository]['months'][$month])])),
        ];
        $data['datasets'] = $datasets;
        $data['_data'] = [
            'Merged' => $merged[$repository]['months'][$month],
            'Created' => $created[$repository]['months'][$month],
            'Closed' => $closed[$repository]['months'][$month],
            'Rejected' => $this->getRejected($closed[$repository]['months'][$month], $merged[$repository]['months'][$month]),
            'Acceptance Rate' => $this->getAcceptanceRate($closed[$repository]['months'][$month], $merged[$repository]['months'][$month]),
        ];
        $this->storeDataByYear(sprintf('%s/%s/%d', $repository, self::FILENAME, $month), $year, $data);
    }

    /**
     * @param int $year
     * @return array
     */
    private function getCreatedPullRequestsByYear(int $year): array
    {
        if (!$this->created) {
            $this->created = $this->getPullRequestsByStatusAndYear(parent::STATUS_CREATED, $year);
        }
        return $this->created;
    }

    /**
     * @param int $year
     * @return array
     */
    private function getClosedPullRequestsByYear(int $year): array
    {
        if (!$this->closed) {
            $this->closed = $this->getPullRequestsByStatusAndYear(parent::STATUS_CLOSED, $year);
        }
        return $this->closed;
    }

    /**
     * @param int $year
     * @return array
     */
    private function getMergedPullRequestsByYear(int $year): array
    {
        if (!$this->merged) {
            $this->merged = $this->getPullRequestsByStatusAndYear(parent::STATUS_MERGED, $year);
        }
        return $this->merged;
    }

    /**
     * @param string $status
     * @param int $year
     * @return array
     */
    private function getPullRequestsByStatusAndYear(string $status, int $year): array
    {
        $data = $this->getRangeArray($year);
        $result = $this->fetchPullRequestsByStatusAndYear($status, $year);
        foreach ($result as $item) {
            $month = Carbon::createFromTimestamp(strtotime($item[$status]))->month;
            $day = Carbon::createFromTimestamp(strtotime($item[$status]))->day;
            $data[$item['repo']]['total'][$month]++;
            $data[$item['repo']]['months'][$month][$day]++;
            $data['total'][$month]++;
        }
        return $data;
    }

    /**
     * @param string $status
     * @param int $year
     * @return array
     */
    private function fetchPullRequestsByStatusAndYear(string $status, int $year): array
    {
        return $this->pullRequests
            ->where($status, '>', Carbon::createFromDate($year)->firstOfYear())
            ->where($status, '<', Carbon::createFromDate($year)->lastOfYear())
            ->orderBy($status, 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * @param int $year
     * @return array
     */
    private function getFirstTimeContributorsYear(int $year): array
    {
        $data = $this->getRangeArray($year);
        $result = $this->contributors
            ->where('first_contribution', '>', Carbon::createFromDate($year)->firstOfYear())
            ->where('first_contribution', '<', Carbon::createFromDate($year)->lastOfYear())
            ->orderBy('first_contribution', 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $data['total'][Carbon::createFromTimestamp(strtotime($item['first_contribution']))->month]++;
        }
        return $data;
    }

    private function getTotalContributorsByYearAndMonth(int $year): array
    {
        $data = [
            'contributors' => [],
            'total' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0,
            ]];
        $result = $this->fetchPullRequestsByStatusAndYear('created', $year);
        foreach ($result as $item) {
            $month = Carbon::createFromTimestamp(strtotime($item['created']))->month;
            if (!isset($data['contributors'][$month][$item['author']])) {
                $data['contributors'][$month][$item['author']] = 1;
                $data['total'][$month]++;
            }
            if (!isset($data['contributors_total'][$item['author']])) {
                $data['contributors']['total'][$item['author']] = 1;
            }
        }
        return $data;
    }
}
