<?php

namespace App\Console\Components\Magento;

use App\PullRequests;
use App\Statistics;
use Illuminate\Console\Command;
use App\Services\GitHub\Api;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateStatistics extends Command
{
    const OPTION_ALL = 'all';
    protected $signature = 'magento:generate:statistics {--all}';
    protected $description = 'Generate Statistics';

    public function handle(PullRequests $pullRequests)
    {
        $data = [];
        $all = $this->input->getOption(self::OPTION_ALL);
        foreach ($pullRequests::all() as $item) {
            $year = date('Y', strtotime($item->getOriginal('created')));
            if(!$all && $year !== date('Y')) {
                    continue;
            }
            $month = date('m', strtotime($item->getOriginal('created')));
            $data[$year][$item->getOriginal('author')]['total']['created'][] = [
                'title' => $item->getOriginal('title'),
                'number' => $item->getOriginal('number')
            ];
            $data[$year][$item->getOriginal('author')]['month'][$month]['created'][] = [
                'title' => $item->getOriginal('title'),
                'number' => $item->getOriginal('number')
            ];
            $data[$year][$item->getOriginal('author')]['repos'][$item->getOriginal('repo')]['total'][] = [
                'title' => $item->getOriginal('title'),
                'number' => $item->getOriginal('number')
            ];

            if (!$item->getOriginal('closed')) {
                $data[$year][$item->getOriginal('author')]['repos'][$item->getOriginal('repo')]['open'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => (int)$item->getOriginal('number')
                ];
                $data[$year][$item->getOriginal('author')]['total']['open'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => $item->getOriginal('number')
                ];
                $data[$year][$item->getOriginal('author')]['month'][$month]['open'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => $item->getOriginal('number')
                ];
            }
            if ($item->getOriginal('closed')) {
                $data[$year][$item->getOriginal('author')]['repos'][$item->getOriginal('repo')]['closed'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => (int)$item->getOriginal('number')
                ];
                $data[$year][$item->getOriginal('author')]['total']['closed'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => $item->getOriginal('number')
                ];
                $data[$year][$item->getOriginal('author')]['month'][$month]['closed'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => $item->getOriginal('number')
                ];
            }
            if ($item->getOriginal('merged')) {
                $data[$year][$item->getOriginal('author')]['repos'][$item->getOriginal('repo')]['merged'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => (int)$item->getOriginal('number')
                ];
                $data[$year][$item->getOriginal('author')]['total']['merged'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => $item->getOriginal('number')
                ];
                $data[$year][$item->getOriginal('author')]['month'][$month]['merged'][] = [
                    'title' => $item->getOriginal('title'),
                    'number' => $item->getOriginal('number')
                ];
            }
        }

        $this->info('Store statistics by year');
        $this->storeStatisticsByYear($data);
    }

    private function storeStatisticsByYear(array $input)
    {
        $statisticsByYear = new Statistics\StatisticsByYear();
        foreach ($input as $year => $item) {
            foreach ($item as $author => $row) {
                $data = [
                    'year' => $year,
                    'author' => $author,
                    'created' => (isset($row['total']['created'])) ? count($row['total']['created']) : 0,
                    'open' => (isset($row['total']['open'])) ? count($row['total']['open']) : 0,
                    'closed' => (isset($row['total']['closed'])) ? count($row['total']['closed']) : 0,
                    'merged' => (isset($row['total']['merged'])) ? count($row['total']['merged']) : 0,
                ];
                $statisticsByYear->store($data);
            }
        }
    }
}
