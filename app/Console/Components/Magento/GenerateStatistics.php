<?php

namespace App\Console\Components\Magento;

use App\Statistics;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateStatistics extends Command
{
    const ARGUMENT_YEAR = 'year';
    protected $signature = 'magento:generate:statistics {year=current}';
    protected $description = 'Generate Statistics';

    public function handle(Statistics $statistics)
    {
        $year = $this->input->getArgument(self::ARGUMENT_YEAR);
        $publicRepos = explode(',', getenv('MAGENTO_REPOS'));

        if ($year && $year === 'current') {
            $year = date('Y');
        }

        $this->output->title(sprintf('From: %s to: %s', Carbon::createFromDate($year)->firstOfYear(), Carbon::createFromDate($year)->lastOfYear()));
        $statistics->storePullRequests($year);
        foreach ($publicRepos as $repo) {
            $statistics->storePullRequestsByRepository($repo, $year);
        }
        $this->output->writeln(sprintf('Memory usage: %s', $this->convert(memory_get_usage(true))));
    }

    private function convert(int $size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];
    }
}
