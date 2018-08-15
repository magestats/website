<?php
declare(strict_types=1);

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
    public function storeContributorsByRepository(string $repository, int $year)
    {
        $data = [
            'generator' => 'https://magestats.net/',
            'title' => sprintf('%s - %s', $repository, $year),
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
            ->orderBy('author', 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $monthCreated = $this->getMonth($item['created'] ?? '');
            $monthClosed = $this->getMonth($item['closed'] ?? '');
            $monthMerged = $this->getMonth($item['merged'] ?? '');
            $data[$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);

            $data[$item['author']]['total']['created'] ?? $data[$item['author']]['total']['created'] = 0;
            $data[$item['author']]['total']['created']++;

            $data[$item['author']]['month'][$monthCreated]['created'] ?? $data[$item['author']]['month'][$monthCreated]['created'] = 0;
            $data[$item['author']]['month'][$monthCreated]['created']++;

            $data[$item['author']]['repos'][$item['repo']]['total'] ?? $data[$item['author']]['repos'][$item['repo']]['total'] = 0;
            $data[$item['author']]['repos'][$item['repo']]['total']++;

            if ($item['closed'] && $year === Carbon::createFromTimeString($item['closed'])->year) {
                $data[$item['author']]['repos'][$item['repo']]['closed'] ?? $data[$item['author']]['repos'][$item['repo']]['closed'] = 0;
                $data[$item['author']]['repos'][$item['repo']]['closed']++;

                $data[$item['author']]['total']['closed'] ?? $data[$item['author']]['total']['closed'] = 0;
                $data[$item['author']]['total']['closed']++;

                $data[$item['author']]['month'][$monthClosed]['closed'] ?? $data[$item['author']]['month'][$monthClosed]['closed'] = 0;
                $data[$item['author']]['month'][$monthClosed]['closed']++;
            }
            if ($item['merged'] && $year === Carbon::createFromTimeString($item['merged'])->year) {
                $data[$item['author']]['repos'][$item['repo']]['merged'] ?? $data[$item['author']]['repos'][$item['repo']]['merged'] = 0;
                $data[$item['author']]['repos'][$item['repo']]['merged']++;

                $data[$item['author']]['total']['merged'] ?? $data[$item['author']]['total']['merged'] = 0;
                $data[$item['author']]['total']['merged']++;

                $data[$item['author']]['month'][$monthMerged]['merged'] ?? $data[$item['author']]['month'][$monthMerged]['merged'] = 0;
                $data[$item['author']]['month'][$monthMerged]['merged']++;
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
            ->orderBy('author', 'ASC')
            ->get()
            ->toArray();

        foreach ($result as $item) {
            $monthCreated = $this->getMonth($item['created'] ?? '');
            $monthClosed = $this->getMonth($item['closed'] ?? '');
            $monthMerged = $this->getMonth($item['merged'] ?? '');
            $data[$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);

            $data[$item['author']]['total']['created'] ?? $data[$item['author']]['total']['created'] = 0;
            $data[$item['author']]['total']['created']++;

            $data[$item['author']]['month'][$monthCreated]['created'] ?? $data[$item['author']]['month'][$monthCreated]['created'] = 0;
            $data[$item['author']]['month'][$monthCreated]['created']++;

            if ($item['closed'] && $year === Carbon::createFromTimeString($item['closed'])->year) {
                $data[$item['author']]['total']['closed'] ?? $data[$item['author']]['total']['closed'] = 0;
                $data[$item['author']]['total']['closed']++;

                $data[$item['author']]['month'][$monthClosed]['closed'] ?? $data[$item['author']]['month'][$monthClosed]['closed'] = 0;
                $data[$item['author']]['month'][$monthClosed]['closed']++;
            }
            if ($item['merged'] && $year === Carbon::createFromTimeString($item['merged'])->year) {
                $data[$item['author']]['total']['merged'] ?? $data[$item['author']]['total']['merged'] = 0;
                $data[$item['author']]['total']['merged']++;

                $data[$item['author']]['month'][$monthMerged]['merged'] ?? $data[$item['author']]['month'][$monthMerged]['merged'] = 0;
                $data[$item['author']]['month'][$monthMerged]['merged']++;
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

    private function getMonth(string $time): string
    {
        $timestamp = strtotime($time);
        if ($timestamp) {
            return date('m', $timestamp);
        }
        return '';
    }
}
