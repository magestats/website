<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Statistics
{
    const DATASET_FILL = 'false';

    /**
     * @var PullRequests
     */
    private $pullRequests;
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
     * @var array
     */
    private $createdColor = [235,227,170];
    /**
     * @var array
     */
    private $closedColor = [131,134,137];
    /**
     * @var array
     */
    private $mergedColor = [202,215,178];

    public function __construct(PullRequests $pullRequests)
    {
        $this->pullRequests = $pullRequests;
    }

    /**
     * @param int $year
     */
    public function storePullRequests(int $year)
    {
        $data = [
            'title' => $year,
            'labels' => $this->getMonthRange($year),
        ];
        $created = $this->fetchCreatedPullRequestsByYear($year);
        $closed = $this->fetchClosedPullRequestsByYear($year);
        $merged = $this->fetchMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Created', $created['total'], $this->createdColor),
            $this->getDataset('Closed', $closed['total'], $this->closedColor),
            $this->getDataset('Merged', $merged['total'], $this->mergedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear('year', $year, $data);
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
        ];
        $created = $this->fetchCreatedPullRequestsByYear($year);
        $closed = $this->fetchClosedPullRequestsByYear($year);
        $merged = $this->fetchMergedPullRequestsByYear($year);

        $datasets = [
            $this->getDataset('Created', $created[$repository], $this->createdColor),
            $this->getDataset('Closed', $closed[$repository], $this->closedColor),
            $this->getDataset('Merged', $merged[$repository], $this->mergedColor),
        ];

        $data['datasets'] = $datasets;
        $this->storeDataByYear(sprintf('%s/year', $repository), $year, $data);
    }

    /**
     * @param int $year
     * @return array
     */
    private function fetchCreatedPullRequestsByYear(int $year): array
    {
        if (!$this->created) {
            $this->created = $this->fetchPullRequestsByStatusAndYear('created', $year);
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
            $this->closed = $this->fetchPullRequestsByStatusAndYear('closed', $year);
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
            $this->merged = $this->fetchPullRequestsByStatusAndYear('merged', $year);
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

    /**
     * @param int $start
     * @param int $end
     * @return array
     */
    private function getRangeArray(int $start, int $end): array
    {
        $data = [];
        $publicRepos = explode(',', getenv('MAGENTO_REPOS'));

        foreach (range($start, $end) as $item) {
            foreach ($publicRepos as $repo) {
                $data[$repo][$item] = 0;
            }
            $data['total'][$item] = 0;
        }
        return $data;
    }

    /**
     * @param int $year
     * @return array
     */
    private function getMonthRange(int $year): array
    {
        $data = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        if (Carbon::create($year)->isCurrentYear()) {
            foreach ($data as $month => $row) {
                if ($month > date('n')) {
                    unset($data[$month]);
                }
            }
        }

        return array_values($data);
    }

    private function getDataset(string $label, array $data, array $color)
    {
        return [
            'label' => $label,
            'data' => array_values($data),
            "fill" => self::DATASET_FILL,
            'backgroundColor' => sprintf('rgba(%s, 0.8)', implode(',', $color)),
            'borderColor' => sprintf('rgba(%s, 0.8)', implode(',', $color)),
            'pointBackgroundColor' => sprintf('rgba(%s, 0.8)', implode(',', $color)),
            'pointBorderColor' => sprintf('rgba(%s, 0.8)', implode(',', $color)),
            'pointHoverBackgroundColor' => sprintf('rgba(%s, 0.8)', implode(',', $color)),
        ];
    }

    /**
     * @param string $filename
     * @param int $year
     * @param array $data
     */
    private function storeDataByYear(string $filename, int $year, array $data)
    {
        $json = json_encode($data, true);
        Storage::put(sprintf('public/%d/%s.json', $year, $filename), $json, 'public');
    }
}
