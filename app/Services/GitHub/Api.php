<?php
declare(strict_types=1);

namespace App\Services\GitHub;

use Github\Client;
use Github\ResultPager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Api
{
    const GITHUB_API_RESULT_UPDATED_AT = 'updated_at';
    const GITHUB_API_RESULT_DATA = 'data';

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @return Collection
     */
    public function fetchContributors(string $userName, string $repoName): Collection
    {
        return collect($this->fetchAllResults('repo', 'contributors', [$userName, $repoName]));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @return Collection
     */
    public function fetchStatistics(string $userName, string $repoName): Collection
    {
        return collect($this->fetchAllResults('repo', 'statistics', [$userName, $repoName]));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @return Collection
     */
    public function fetchParticipations(string $userName, string $repoName): Collection
    {
        return collect($this->fetchAllResults('repo', 'participation', [$userName, $repoName]));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @return Collection
     */
    public function fetchOpenPullRequests(string $userName, string $repoName): Collection
    {
        return collect($this->fetchAllResults('pull_request', 'all', [$userName, $repoName]));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @return Collection
     */
    public function fetchClosedPullRequests(string $userName, string $repoName): Collection
    {
        return collect($this->fetchAllResults('pull_request', 'all', [$userName, $repoName, ['state' => 'closed']]));
    }

    /**
     * @param string $interfaceName
     * @param string $method
     * @param array $parameters
     * @return array
     */
    private function fetchAllResults(string $interfaceName, string $method, array $parameters) : array
    {
        $cacheKey = $this->getCacheKey($interfaceName, $method, $parameters);
        if(Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $result = (new ResultPager($this->client))->fetchAll(
            $this->client->api($interfaceName),
            $method,
            $parameters
        );

        $data = [
            self::GITHUB_API_RESULT_UPDATED_AT => now(),
            self::GITHUB_API_RESULT_DATA => $result
        ];

        Cache::put($cacheKey, $data, now()->addMinutes(env('GITHUB_CACHE')));

        return $data;
    }

    /**
     * @param string $interfaceName
     * @param string $method
     * @param array $parameters
     * @return string
     */
    private function getCacheKey(string $interfaceName, string $method, array $parameters) : string
    {
        return sprintf('%s_%s_%s', $interfaceName, $method, sha1(serialize($parameters)));
    }
}