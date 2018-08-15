<?php

namespace App\Console\Components\Magento;

use App\Statistics;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;

class GenerateStatistics extends Command
{
    const ARGUMENT_YEAR = 'year';
    const OPTION_ONLINE = 'online';
    const OPTION_ALL_MONTHS = 'all-months';

    protected $signature = 'magento:generate:statistics {year=current} {--online} {--all-months}';
    protected $description = 'Generate Statistics';

    public function handle(
        Statistics\PullRequests $pullRequests,
        Statistics\Issues $issues,
        Statistics\Contributors $contributors
    ) {
        $online = $this->input->getOption(self::OPTION_ONLINE);
        $allMonths = $this->input->getOption(self::OPTION_ALL_MONTHS);

        if ($online) {
            $this->output->title('Fetch Pull Requests');
            $this->fetchPullRequests();
            $this->output->title('Fetch Issues');
            $this->fetchIssues();
        }

        $year = $this->input->getArgument(self::ARGUMENT_YEAR);
        $publicRepos = explode(',', getenv('MAGENTO_REPOS'));

        if ($year && $year === 'current') {
            $year = date('Y');
        }

        $this->output->title(sprintf('From: %s to: %s', Carbon::createFromDate($year)->firstOfYear(), Carbon::createFromDate($year)->lastOfYear()));

        $this->output->text('Store contributors by year');
        $contributors->storeContributors($year);

        $this->output->text('Store pull requests by year');
        $pullRequests->storePullRequests($year);

        $this->output->text('Store issues by year');
        $issues->storeIssues($year);

        foreach ($publicRepos as $repo) {
            $this->output->text(sprintf('Store contributors for repo %s by year', $repo));
            $contributors->storeIssuesByRepository($repo, $year);

            $this->output->text(sprintf('Store pull requests for repo %s by year', $repo));
            $pullRequests->storePullRequestsByRepository($repo, $year);

            $this->output->text(sprintf('Store issues for repo %s by year', $repo));
            $issues->storeIssuesByRepository($repo, $year);

            if ($allMonths) {
                foreach (range(1, 12) as $month) {
                    $this->output->text(sprintf('Store pull requests for repo %s by year and month %s', $repo, $month));
                    $pullRequests->storePullRequestsByRepositoryAndMonth($repo, (int) $month, $year);

                    $this->output->text(sprintf('Store issues for repo %s by year and month %s', $repo, $month));
                    $issues->storeIssuesByRepositoryAndMonth($repo, (int)  $month, $year);
                }
            } else {
                $this->output->text(sprintf('Store pull requests for repo %s by year and month', $repo));
                $pullRequests->storePullRequestsByRepositoryAndMonth($repo, (int) date('n'), $year);

                $this->output->text(sprintf('Store issues for repo %s by year and month', $repo));
                $issues->storeIssuesByRepositoryAndMonth($repo, (int) date('n'), $year);
            }
        }
        $this->output->writeln(sprintf('Memory usage: %s', $this->convert(memory_get_usage(true))));
    }

    private function convert(int $size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    private function fetchPullRequests()
    {
        $command = $this->getApplication()->find('magento:fetch:pullrequests');
        $arguments = array(
            'command' => 'magento:fetch:pullrequests',
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $this->output);
    }

    private function fetchIssues()
    {
        $command = $this->getApplication()->find('magento:fetch:issues');
        $arguments = array(
            'command' => 'magento:fetch:issues',
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $this->output);
    }
}
