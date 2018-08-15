<?php

namespace App\Statistics;

use App\Statistics;
use Carbon\Carbon;

class Contributors extends Statistics
{
    const FILENAME = 'contributors';

    private $authors = [];

    /**
     * @param int $year
     */
    public function storeContributors(int $year)
    {
        $data = [
            'generator' => 'https://magestats.net/',
            'title' => $year,
            'generated' => Carbon::now(),
        ];
        $contributors = $this->fetchContributorsByYear($year);
        $data['contributors'] = $contributors;
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
            'title' => $year,
            'generated' => Carbon::now(),
        ];
        $contributors = $this->fetchContributorsByYearAndRepo($year, $repository);
        $data['contributors'] = $contributors;
        $this->storeDataByYear(sprintf('%s/%s', $repository, self::FILENAME), $year, $data);
    }

    /**
     * @param int $year
     * @return array
     */
    private function fetchContributorsByYear(int $year): array
    {
        $data = [];
        $result = $this->pullRequests
            ->where('created', '>', Carbon::createFromDate($year)->firstOfYear())
            ->where('created', '<', Carbon::createFromDate($year)->lastOfYear())
            ->orderBy('merged', 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $monthCreated = date('m', strtotime($item['created']));
            $monthClosed = date('m', strtotime($item['closed']));
            $monthMerged = date('m', strtotime($item['merged']));
            $data[$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);
            $data[$item['author']]['total']['created'][] = [
                'number' => $item['number']
            ];
            $data[$item['author']]['month'][$monthCreated]['created'][] = [
                'number' => $item['number']
            ];
            $data[$item['author']]['repos'][$item['repo']]['total'][] = [
                'number' => $item['number']
            ];

            if (!$item['closed']) {
                $data[$item['author']]['repos'][$item['repo']]['open'][] = [
                    'number' => (int)$item['number']
                ];
                $data[$item['author']]['total']['open'][] = [
                    'number' => $item['number']
                ];
                $data[$item['author']]['month'][$monthCreated]['open'][] = [
                    'number' => $item['number']
                ];
            }
            if ($item['closed'] && $year === date('Y', $monthClosed)) {
                $data[$item['author']]['repos'][$item['repo']]['closed'][] = [
                    'number' => (int)$item['number']
                ];
                $data[$item['author']]['total']['closed'][] = [
                    'number' => $item['number']
                ];
                $data[$item['author']]['month'][$monthClosed]['closed'][] = [
                    'number' => $item['number']
                ];
            }
            if ($item['merged'] && $year === date('Y', $monthMerged)) {
                $data[$item['author']]['repos'][$item['repo']]['merged'][] = [
                    'number' => (int)$item['number']
                ];
                $data[$item['author']]['total']['merged'][] = [
                    'number' => $item['number']
                ];
                $data[$item['author']]['month'][$monthMerged]['merged'][] = [
                    'number' => $item['number']
                ];
            }
        }
        return $data;
    }

    private function fetchContributorsByYearAndRepo(int $year, string $repo): array
    {
        $data = [];
        $result = $this->pullRequests
            ->where('created', '>', Carbon::createFromDate($year)->firstOfYear())
            ->where('created', '<', Carbon::createFromDate($year)->lastOfYear())
            ->where('repo', '=', $repo)
            ->orderBy('merged', 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $monthCreated = date('m', strtotime($item['created']));
            $monthClosed = date('m', strtotime($item['closed']));
            $monthMerged = date('m', strtotime($item['merged']));
            $data[$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);
            $data[$item['author']]['total']['created'][] = [
                'number' => $item['number']
            ];
            $data[$item['author']]['month'][$monthCreated]['created'][] = [
                'number' => $item['number']
            ];

            if (!$item['closed']) {
                $data[$item['author']]['total']['open'][] = [
                    'number' => $item['number']
                ];
                $data[$item['author']]['month'][$monthCreated]['open'][] = [
                    'number' => $item['number']
                ];
            }
            if ($item['closed'] && $year === date('Y', $monthMerged)) {
                $data[$item['author']]['total']['closed'][] = [
                    'number' => $item['number']
                ];
                $data[$item['author']]['month'][$monthClosed]['closed'][] = [
                    'number' => $item['number']
                ];
            }
            if ($item['merged'] && $year === date('Y', $monthMerged)) {
                $data[$item['author']]['total']['merged'][] = [
                    'number' => $item['number']
                ];
                $data[$item['author']]['month'][$monthMerged]['merged'][] = [
                    'number' => $item['number']
                ];
            }
        }
        return $data;
    }

    private function getAvatarUrl(string $author, string $meta)
    {
        if (!isset($this->authors[$author])) {
            $data = json_decode($meta, true);
            if (isset($data['user']['avatar_url'])) {
                $this->authors[$author] = $data['user']['avatar_url'];
            }
        }
        return $this->authors[$author];
    }
}
