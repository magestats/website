<?php
declare(strict_types=1);

namespace App\Console\Components\Magento;

use App\Console\Components\AbstractCommand;
use App\PullRequests;
use App\Services\GitHub\Api;
use Symfony\Component\Console\Output\OutputInterface;

class FetchPullRequests extends AbstractCommand
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
                        'pr_id' => (int)$item['id'],
                        'node_id' => $item['node_id'],
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
                        'meta' => json_encode($item),
                    ];

                    $pullRequests->store($data);
                }
            } catch (\Exception $e) {
                $this->output->writeln($this->getMemoryUsage());
                $this->warn($e->getMessage());
            }
            $this->output->writeln($this->getMemoryUsage());
        }
    }

    /**
     * @param Api $api
     * @param string $repository
     * @param bool $all
     * @return array
     */
    private function fetchPullRequests(Api $api, string $repository, bool $all): array
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
