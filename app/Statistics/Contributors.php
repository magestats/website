<?php
declare(strict_types=1);

namespace App\Statistics;

use App\Statistics;
use Carbon\Carbon;

class Contributors extends Statistics
{
    const FILENAME = 'contributors';
    const GENERATOR = 'https://magestats.net/';
    /**
     * @var array
     */
    private $processedContributors = [];

    /**
     * @var array
     */
    private $authors = [];

    /**
     * @param int $year
     */
    public function storeContributors(int $year)
    {
        $data = [
            'generator' => self::GENERATOR,
            'title' => $year,
            'generated' => Carbon::now(),
        ];
        $contributors = $this->fetchContributorsByYear($year);
        $this->storeContributorDetails($year, $contributors['total']);
        $data['contributors'] = $this->getSortedContributors($contributors['total']);
        $this->storeDataByYear(self::FILENAME, $year, $data);
    }

    /**
     * @param string $repository
     * @param int $year
     */
    public function storeContributorsByRepository(string $repository, int $year)
    {
        $data = [
            'generator' => self::GENERATOR,
            'title' => sprintf('%s - %s', $repository, $year),
            'generated' => Carbon::now(),
        ];
        $contributors = $this->fetchContributorsByYear($year);
        if (isset($contributors['byRepo'][$repository])) {
            $data['contributors'] = $this->getSortedContributors($contributors['byRepo'][$repository]);
            $this->storeDataByYear(sprintf('%s/%s', $repository, self::FILENAME), $year, $data);
        }
    }

    /**
     * @param string $repository
     * @param int $month
     * @param int $year
     */
    public function storeContributorsByRepositoryAndMonth(string $repository, int $month, int $year)
    {
        $data = [
            'generator' => self::GENERATOR,
            'title' => sprintf('%s - %s %s', $repository, Carbon::create($year, $month)->format('F'), $year),
            'generated' => Carbon::now(),
        ];
        $contributors = $this->fetchContributorsByYear($year);
        if (isset($contributors['byRepoAndMonth'][$repository][$month])) {
            $data['contributors'] = $this->getSortedContributors($contributors['byRepoAndMonth'][$repository][$month]);
            $this->storeDataByYear(sprintf('%s/%s/%d', $repository, self::FILENAME, $month), $year, $data);
        }
    }

    private function fetchContributorsByYear(int $year): array
    {
        if (!$this->processedContributors) {
            $result = $this->pullRequests
                ->where('created', '>', Carbon::createFromDate($year)->firstOfYear())
                ->where('created', '<', Carbon::createFromDate($year)->lastOfYear())
                ->orderBy('created', 'DESC')
                ->get()
                ->toArray();


            $firstTimeContributors = $this->getFirstTimeContributorsByYear($year);

            $total = [];
            $byRepo = [];
            $byRepoAndMonth = [];
            foreach ($result as $item) {
                $month = Carbon::createFromTimeString($item['created'])->month;
                $total[$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);
                $total[$item['author']]['author'] = $item['author'];
                $total[$item['author']]['created'] ?? $total[$item['author']]['created'] = 0;
                $total[$item['author']]['closed'] ?? $total[$item['author']]['closed'] = 0;
                $total[$item['author']]['merged'] ?? $total[$item['author']]['merged'] = 0;
                $total[$item['author']]['rejected'] ?? $total[$item['author']]['rejected'] = 0;
                $total[$item['author']]['acceptance_rate'] ?? $total[$item['author']]['acceptance_rate'] = 0;
                $total[$item['author']]['first_time'] ?? $total[$item['author']]['first_time'] = isset($firstTimeContributors[$item['author']]);
                $total[$item['author']]['_pull_requests'] ?? $total[$item['author']]['_pull_requests'] = [];
                $total[$item['author']]['created']++;


                $byRepo[$item['repo']][$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);
                $byRepo[$item['repo']][$item['author']]['created'] ?? $byRepo[$item['repo']][$item['author']]['created'] = 0;
                $byRepo[$item['repo']][$item['author']]['closed'] ?? $byRepo[$item['repo']][$item['author']]['closed'] = 0;
                $byRepo[$item['repo']][$item['author']]['merged'] ?? $byRepo[$item['repo']][$item['author']]['merged'] = 0;
                $byRepo[$item['repo']][$item['author']]['rejected'] ?? $byRepo[$item['repo']][$item['author']]['rejected'] = 0;
                $byRepo[$item['repo']][$item['author']]['created']++;

                $byRepoAndMonth[$item['repo']][$month][$item['author']]['avatar_url'] = $this->getAvatarUrl($item['author'], $item['meta']);
                $byRepoAndMonth[$item['repo']][$month][$item['author']]['created'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['created'] = 0;
                $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed'] = 0;
                $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged'] = 0;
                $byRepoAndMonth[$item['repo']][$month][$item['author']]['rejected'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['rejected'] = 0;
                $byRepoAndMonth[$item['repo']][$month][$item['author']]['created']++;

                if ($item['closed'] && $year === Carbon::createFromTimeString($item['closed'])->year) {
                    $month = Carbon::createFromTimeString($item['closed'])->month;
                    $total[$item['author']]['closed']++;
                    $byRepo[$item['repo']][$item['author']]['closed']++;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['created'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['created'] = 0;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed'] = 0;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged'] = 0;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed']++;
                }
                if ($item['merged'] && $year === Carbon::createFromTimeString($item['merged'])->year) {
                    $month = Carbon::createFromTimeString($item['merged'])->month;
                    $total[$item['author']]['merged']++;
                    $byRepo[$item['repo']][$item['author']]['merged']++;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['created'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['created'] = 0;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['closed'] = 0;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged'] ?? $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged'] = 0;
                    $byRepoAndMonth[$item['repo']][$month][$item['author']]['merged']++;
                }

                $total[$item['author']]['rejected'] = $this->getRejected([$total[$item['author']]['closed']], [$total[$item['author']]['merged']])[0];
                $total[$item['author']]['acceptance_rate'] = $this->getAcceptanceRate([$total[$item['author']]['closed']], [$total[$item['author']]['merged']])[0];
                $total[$item['author']]['_pull_requests'][$item['number']] = [
                    'created' => $item['created'],
                    'author' => $item['author'],
                    'html_url' => $item['html_url'],
                    'repo' => $item['repo'],
                    'state' => (isset($item['merged'])) ? 'merged' : $item['state'],
                    'title' => $item['title']
                ];

                $byRepo[$item['repo']][$item['author']]['rejected'] = $this->getRejected([$byRepo[$item['repo']][$item['author']]['closed']], [$byRepo[$item['repo']][$item['author']]['merged']])[0];
                $byRepo[$item['repo']][$item['author']]['acceptance_rate'] = $this->getAcceptanceRate([$byRepo[$item['repo']][$item['author']]['closed']], [$byRepo[$item['repo']][$item['author']]['merged']])[0];

                $byRepoAndMonth[$item['repo']][$month][$item['author']]['rejected'] = $this->getRejected([$byRepoAndMonth[$item['repo']][$month][$item['author']]['closed']], [$byRepoAndMonth[$item['repo']][$month][$item['author']]['merged']])[0];
                $byRepoAndMonth[$item['repo']][$month][$item['author']]['acceptance_rate'] = $this->getAcceptanceRate([$byRepoAndMonth[$item['repo']][$month][$item['author']]['closed']], [$byRepoAndMonth[$item['repo']][$month][$item['author']]['merged']])[0];
            }
            $this->processedContributors = ['total' => $total, 'byRepo' => $byRepo, 'byRepoAndMonth' => $byRepoAndMonth];
        }
        return $this->processedContributors;
    }

    private function getSortedContributors(array $contributors): array
    {
        $data = [];
        $byMerged = [];
        foreach ($contributors as $author => $contributor) {
            $merged = $contributor['merged'] ?? 0;
            $byMerged[$merged][$author] = $contributor;
        }
        krsort($byMerged);
        foreach ($byMerged as $authors) {
            foreach ($authors as $author => $values) {
                $data[$author] = $values;
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

    private function storeContributorDetails(int $year, array &$data)
    {
        foreach ($data as $author => $row) {
            $issuesData = [];
            $issues = \App\Issues::select(
                [
                    'number',
                    'created',
                    'html_url',
                    'repo',
                    'state',
                    'title'
                ]
            )
                ->where('author', $author)
                ->where('created', '>', Carbon::createFromDate($year)->firstOfYear())
                ->where('created', '<', Carbon::createFromDate($year)->lastOfYear())
                ->orderBy('created', 'DESC')
                ->get()
                ->toArray();

            foreach ($issues as $issue) {
                $item = $issue;
                unset($item['number']);
                $issuesData[$issue['number']] = $item;
            }

            $row['_issues'] = $issuesData;

            $this->storeDataByYear(sprintf('%s/%s', self::FILENAME, strtolower($author)), $year, $row);
            unset($data[$author]['_pull_requests']);
        }
    }

    /**
     * @param int $year
     * @return array
     */
    private function getFirstTimeContributorsByYear(int $year): array
    {
        $contributors = $this->contributors
            ->where('first_contribution', '>', Carbon::createFromDate($year)->firstOfYear())
            ->where('first_contribution', '<', Carbon::createFromDate($year)->lastOfYear())
            ->orderBy('first_contribution', 'DESC')
            ->get()
            ->toArray();
        foreach ($contributors as $contributor) {
            $data[$contributor['author']] = $contributor['first_contribution'];
        }
        return $data;
    }
}
