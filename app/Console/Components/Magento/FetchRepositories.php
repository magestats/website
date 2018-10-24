<?php
declare(strict_types=1);

namespace App\Console\Components\Magento;

use App\Console\Components\AbstractCommand;
use App\Repositories;
use App\Services\GitHub\Api;

class FetchRepositories extends AbstractCommand
{
    protected $signature = 'magento:fetch:repositories';
    protected $description = 'Fetch Repositories Information';

    public function handle(Api $api, Repositories $repositories)
    {
        $repos = explode(',', getenv('MAGENTO_REPOS'));
        foreach ($repos as $repo) {
            $this->output->writeln(sprintf('<info>Fetch Repository: %s</info>', $repo));
            try {
                $result = $this->fetchRepository($api, $repo);
                $data = [
                    'repo_id' => (int)$result['id'],
                    'node_id' => $result['node_id'],
                    'owner' => $result['owner']['login'],
                    'owner_type' => $result['owner']['type'],
                    'name' => $result['name'],
                    'full_name' => $result['full_name'],
                    'html_url' => $result['html_url'],
                    'description' => $result['description'],
                    'homepage' => $result['homepage'],
                    'has_issues' => (int)$result['has_issues'],
                    'has_projects' => (int)$result['has_projects'],
                    'has_downloads' => (int)$result['has_downloads'],
                    'has_wiki' => (int)$result['has_wiki'],
                    'size' => (int)$result['size'],
                    'stargazers_count' => (int)$result['stargazers_count'],
                    'watchers_count' => (int)$result['watchers_count'],
                    'network_count' => (int)$result['network_count'],
                    'subscribers_count' => (int)$result['subscribers_count'],
                    'forks' => (int)$result['forks'],
                    'open_issues' => (int)$result['open_issues'],
                    'default_branch' => $result['default_branch'],
                    'created' => $result['created_at'],
                    'updated' => $result['updated_at'],
                ];
                $repositories->store($data);
            } catch (\Exception $e) {
                $this->output->writeln($this->getMemoryUsage());
                $this->output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
            $this->output->writeln($this->getMemoryUsage());
        }
    }

    /**
     * @param Api $api
     * @param string $repository
     * @return array
     */
    private function fetchRepository(Api $api, string $repository): array
    {
        list($user, $repo) = explode('/', $repository);
        $result = $api->fetchRepositories($user, $repo, true)->toArray();
        return $result[Api::GITHUB_API_RESULT_DATA];
    }
}
