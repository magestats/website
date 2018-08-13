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
    protected $signature = 'magento:fetch:pullrequests {--all}';
    protected $description = 'Fetch Pull Requests';

    public function handle(Api $api, PullRequests $pullRequests)
    {
        $verbosityLevel = $this->getOutput()->getVerbosity();
        $all = $this->input->getOption(self::OPTION_ALL);
        $repositories = explode(',', getenv('MAGENTO_REPOS'));
        foreach ($repositories as $repository) {
            if ($verbosityLevel >= OutputInterface::VERBOSITY_NORMAL) {
                $this->info(sprintf('Fetch %s', $repository));
            }
            try {
                $result = $this->fetchPullRequests($api, $repository, $all);
                foreach ($result as $item) {
                    if ($verbosityLevel >= OutputInterface::VERBOSITY_VERBOSE) {
                        $this->comment(sprintf('[%s] - %s by %s', $item['number'], $item['title'], $item['user']['login']));
                    }

                    $data = [
                        'pr_id' => $item['id'],
                        'node_id' => (int)$item['node_id'],
                        'html_url' => $item['html_url'],
                        'number' => (int)$item['number'],
                        'repo' => $repository,
                        'state' => $item['state'],
                        'title' => $item['title'],
                        'author' => $item['user']['login'],
                        'author_association' => $item['author_association'],
                        'labels' => $this->getLabels($item['labels']),
                        'label_ids' => $this->getLabelIds($item['labels']),
                        'milestone' => $item['milestone']['title'],
                        'milestone_url' => $item['milestone']['html_url'],
                        'created' => $item['created_at'],
                        'updated' => $item['updated_at'],
                        'closed' => $item['closed_at'],
                        'merged' => $item['merged_at'],
                        'meta' => serialize($item),
                    ];

                    $pullRequests->store($data);
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

    /**
     * @param Api $api
     * @param string $repository
     * @param bool $all
     * @return array
     */
    private function fetchPullRequests(Api $api, string $repository, bool $all) : array
    {
        list($user, $repo) = explode('/', $repository);
        $result = $api->fetchPullRequests($user, $repo, $all)->toArray();
        return $result[Api::GITHUB_API_RESULT_DATA];
    }
    private function getLabels(array $item)
    {
        $data = [];
        foreach ($item as $label) {
            $data[] = $label['name'];
        }
        return implode(',', $data);
    }

    private function getLabelIds(array $item)
    {
        $data = [];
        foreach ($item as $label) {
            $data[] = $label['id'];
        }
        return implode(',', $data);
    }
}
