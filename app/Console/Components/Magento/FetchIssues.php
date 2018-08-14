<?php

namespace App\Console\Components\Magento;

use App\Issues;
use Illuminate\Console\Command;
use App\Services\GitHub\Api;
use Symfony\Component\Console\Output\OutputInterface;

class FetchIssues extends Command
{
    const OPTION_ALL = 'all';
    protected $signature = 'magento:fetch:issues {--all}';
    protected $description = 'Fetch Issues Information';

    public function handle(Api $api, Issues $issues)
    {
        $all = $this->input->getOption(self::OPTION_ALL);
        $verbosityLevel = $this->getOutput()->getVerbosity();
        $repos = explode(',', getenv('MAGENTO_REPOS'));
        foreach ($repos as $repo) {
            $this->output->writeln(sprintf('<info>Fetch Repository: %s</info>', $repo));
            try {
                $result = $this->fetchIssues($api, $repo, $all);
                foreach ($result as $item) {
                    if (isset($item['pull_request'])) {
                        continue;
                    }

                    if ($verbosityLevel >= OutputInterface::VERBOSITY_VERBOSE) {
                        $this->comment(sprintf('[%s] - %s by %s', $item['number'], $item['title'], $item['user']['login']));
                    }
                    $data = [
                        'issue_id' => (int)$item['id'],
                        'node_id' => $item['node_id'],
                        'html_url' => $item['html_url'],
                        'number' => (int)$item['number'],
                        'repo' => $repo,
                        'state' => $item['state'],
                        'title' => $item['title'],
                        'author' => $item['user']['login'],
                        'author_association' => $item['author_association'],
                        'labels' => $this->getLabels($item['labels']),
                        'label_ids' => $this->getLabelIds($item['labels']),
                        'created' => $item['created_at'],
                        'updated' => $item['updated_at'],
                        'closed' => $item['closed_at'],
                        'meta' => json_encode($item),
                    ];
                    $issues->store($data);
                }
            } catch (\Exception $e) {
                $this->output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
        }
    }

    /**
     * @param Api $api
     * @param string $repository
     * @return array
     */
    private function fetchIssues(Api $api, string $repository, bool $all) : array
    {
        list($user, $repo) = explode('/', $repository);
        $result = $api->fetchIssues($user, $repo, $all)->toArray();
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
