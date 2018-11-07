<?php
declare(strict_types=1);

namespace App\Console\Components\Magento;

use App\Console\Components\AbstractCommand;
use App\Repositories;
use App\Statistics;
use Carbon\Carbon;
use Symfony\Component\Console\Input\ArrayInput;

class GenerateStatistics extends AbstractCommand
{
    const ARGUMENT_YEAR = 'year';
    const OPTION_ONLINE = 'online';
    const OPTION_ALL_MONTHS = 'all-months';
    const OPTION_ONLY = 'only';

    const SWITCH_CONTRIBUTORS = 'contributors';
    const SWITCH_PULLREQUESTS = 'pullrequests';
    const SWITCH_ISSUES = 'issues';

    protected $signature = 'magento:generate:statistics {year=current} {--online} {--all-months} {--only=[contributors,pullrequests,issues]}';
    protected $description = 'Generate Statistics';

    public function handle(
        Repositories $repositories,
        Statistics\PullRequests $pullRequests,
        Statistics\Issues $issues,
        Statistics\Contributors $contributors
    ) {
        $online = $this->input->getOption(self::OPTION_ONLINE);
        $allMonths = $this->input->getOption(self::OPTION_ALL_MONTHS);
        $only = $this->input->getOption(self::OPTION_ONLY);

        if ($online) {
            if (stripos($only, self::SWITCH_PULLREQUESTS) !== false) {
                $this->output->title('Fetch Pull Requests');
                $this->fetchPullRequests();
            }
            if (stripos($only, self::SWITCH_ISSUES) !== false) {
                $this->output->title('Fetch Issues');
                $this->fetchIssues();
            }
        }

        $year = $this->input->getArgument(self::ARGUMENT_YEAR);
        $publicRepos = $repositories->all()->toArray();
        if ($year && $year === 'current') {
            $year = date('Y');
        }

        $this->output->title(sprintf('From: %s to: %s', Carbon::createFromDate($year)->firstOfYear(), Carbon::createFromDate($year)->lastOfYear()));

        if (stripos($only, self::SWITCH_CONTRIBUTORS) !== false) {
            $this->output->text('Store contributors by year');
            $contributors->storeContributors((int)$year);
            if ($allMonths) {
                foreach (range(1, 12) as $month) {
                    $contributors->storeContributorsByMonth((int)$year, $month);
                }
            } else {
                $contributors->storeContributorsByMonth((int)$year, (int)date('n'));
            }
        }

        if (stripos($only, self::SWITCH_PULLREQUESTS) !== false) {
            $this->output->text('Store pull requests by year');
            $pullRequests->storePullRequests((int)$year);
        }

        if (stripos($only, self::SWITCH_ISSUES) !== false) {
            $this->output->text('Store issues by year');
            $issues->storeIssues((int)$year);
        }

        foreach ($publicRepos as $repository) {
            $repo = $repository['full_name'];
            if ((int)Carbon::createFromTimeString($repository['created'])->year > $year) {
                continue;
            }
            if (stripos($only, self::SWITCH_CONTRIBUTORS) !== false) {
                $this->output->text(sprintf('Store contributors for repo %s by year', $repo));
                $contributors->storeContributorsByRepository($repo, (int)$year);
            }
            if (stripos($only, self::SWITCH_PULLREQUESTS) !== false) {
                $this->output->text(sprintf('Store pull requests for repo %s by year', $repo));
                $pullRequests->storePullRequestsByRepository($repo, (int)$year);
            }
            if (stripos($only, self::SWITCH_ISSUES) !== false) {
                $this->output->text(sprintf('Store issues for repo %s by year', $repo));
                $issues->storeIssuesByRepository($repo, (int)$year);
            }

            if ($allMonths) {
                foreach (range(1, 12) as $month) {
                    if (
                        Carbon::createFromTimeString($repository['created'])->timestamp
                        >
                        Carbon::create(
                            $year,
                            $month,
                            Carbon::createFromTimeString($repository['created'])->day,
                            Carbon::createFromTimeString($repository['created'])->hour,
                            Carbon::createFromTimeString($repository['created'])->minute,
                            Carbon::createFromTimeString($repository['created'])->second
                        )->timestamp) {
                        continue;
                    }
                    if (stripos($only, self::SWITCH_CONTRIBUTORS) !== false) {
                        $this->output->text(sprintf('Store contributors for repo %s by year and month %s', $repo, $month));
                        $contributors->storeContributorsByRepositoryAndMonth($repo, (int)$month, (int)$year);
                    }
                    if (stripos($only, self::SWITCH_PULLREQUESTS) !== false) {
                        $this->output->text(sprintf('Store pull requests for repo %s by year and month %s', $repo, $month));
                        $pullRequests->storePullRequestsByRepositoryAndMonth($repo, (int)$month, (int)$year);
                    }

                    if (stripos($only, self::SWITCH_ISSUES) !== false) {
                        $this->output->text(sprintf('Store issues for repo %s by year and month %s', $repo, $month));
                        $issues->storeIssuesByRepositoryAndMonth($repo, (int)$month, (int)$year);
                    }
                }
            } else {
                if (stripos($only, self::SWITCH_CONTRIBUTORS) !== false) {
                    $this->output->text(sprintf('Store contributors for repo %s by year and month', $repo));
                    $contributors->storeContributorsByRepositoryAndMonth($repo, (int)date('n'), (int)$year);
                }
                if (stripos($only, self::SWITCH_PULLREQUESTS) !== false) {
                    $this->output->text(sprintf('Store pull requests for repo %s by year and month', $repo));
                    $pullRequests->storePullRequestsByRepositoryAndMonth($repo, (int)date('n'), (int)$year);
                }
                if (stripos($only, self::SWITCH_ISSUES) !== false) {
                    $this->output->text(sprintf('Store issues for repo %s by year and month', $repo));
                    $issues->storeIssuesByRepositoryAndMonth($repo, (int)date('n'), (int)$year);
                }
            }
        }
        $this->output->writeln($this->getMemoryUsage());
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
