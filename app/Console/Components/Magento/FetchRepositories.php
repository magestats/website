<?php

namespace App\Console\Components\Magento;

use App\PullRequests;
use App\Repositories;
use Illuminate\Console\Command;
use App\Services\GitHub\Api;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchRepositories extends Command
{
    protected $signature = 'magento:fetch:repositories';
    protected $description = 'Fetch Repository Informations';

    public function handle(Api $api, Repositories $repositories)
    {
        $publicRepos = explode(',', getenv('MAGENTO_REPOS'));
        $privateRepos = explode(',', getenv('MAGENTO_PRIVATE_REPOS'));
        $repos = array_merge($publicRepos, $privateRepos);
        foreach ($repos as $repo) {
            $this->output->writeln(sprintf('<info>Fetch Repository: %s</info>', $repo));
            try {
                $result = $this->fetchRepository($api, $repo);
                $data = [
                    'repo_id' => $result['id'],
                    'node_id' => $result['node_id'],
                    'owner' => $result['owner']['login'],
                    'owner_type' => $result['owner']['type'],
                    'name' => $result['name'],
                    'full_name' => $result['full_name'],
                    'html_url' => $result['html_url'],
                    'description' => $result['description'],
                    'homepage' => $result['homepage'],
                    'has_issues' => (int) $result['has_issues'],
                    'has_projects' => (int) $result['has_projects'],
                    'has_downloads' => (int) $result['has_downloads'],
                    'has_wiki' => (int) $result['has_wiki'],
                    'size' => (int) $result['size'],
                    'stargazers_count' => (int) $result['stargazers_count'],
                    'watchers_count' => (int) $result['watchers_count'],
                    'network_count' => (int) $result['network_count'],
                    'subscribers_count' => (int) $result['subscribers_count'],
                    'forks' => (int) $result['forks'],
                    'open_issues' => (int) $result['open_issues'],
                    'default_branch' => $result['default_branch'],
                ];
                $repositories->store($data);
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
    private function fetchRepository(Api $api, string $repository) : array
    {
        list($user, $repo) = explode('/', $repository);
        $result = $api->fetchRepositories($user, $repo, true)->toArray();
        return $result[Api::GITHUB_API_RESULT_DATA];
    }
}
