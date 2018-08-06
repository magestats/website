<?php

namespace App\Console\Components\Magento;

use App\PullRequests;
use Illuminate\Console\Command;
use App\Services\GitHub\Api;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchPullRequests extends Command
{
    const OPTION_ALL = 'all';
    protected $signature = 'magento:fetch-pull-requests {--all}';
    protected $description = 'Fetch Pull Requests';

    public function handle(Api $api)
    {
        $verbosityLevel = $this->getOutput()->getVerbosity();
        $all = $this->input->getOption(self::OPTION_ALL);
        $model = new PullRequests();
        $repositories = explode(',', getenv('MAGENTO_REPOS'));
        foreach ($repositories as $repository) {
            list($user, $repo) = explode('/', $repository);
            if ($verbosityLevel >= OutputInterface::VERBOSITY_NORMAL) {
                $this->info(sprintf('Fetch %s', $repository));
            }
            try {
                $result = $api->fetchPullRequests($user, $repo, $all);
                foreach ($result[Api::GITHUB_API_RESULT_DATA] as $item) {
                    if ($verbosityLevel >= OutputInterface::VERBOSITY_VERBOSE) {
                        $this->comment(sprintf('[%s] - %s by %s', $item['number'], $item['title'], $item['user']['login']));
                    }

                    $data = [
                        'number' => $item['number'],
                        'node_id' => $item['node_id'],
                        'state' => $item['state'],
                        'repo' => $repository,
                        'title' => $item['title'],
                        'author' => $item['user']['login'],
                        'author_association' => $item['author_association'],
                        'created' => $item['created_at'],
                        'updated' => $item['updated_at'],
                        'closed' => $item['closed_at'],
                        'merged' => $item['merged_at'],
                        'meta' => serialize($item),
                    ];

                    $model->store($data);
                }
            } catch (\Exception $e) {
                $this->warn($e->getMessage());
            }
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [self::OPTION_ALL, null, InputOption::VALUE_OPTIONAL, 'Fetch all', null],
        ];
    }
}
