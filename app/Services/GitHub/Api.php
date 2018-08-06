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
     * @param bool $all
     * @return Collection
     */
    public function fetchContributors(string $userName, string $repoName, bool $all = false): Collection
    {
        return collect($this->fetchAllResults('repo', 'contributors', [$userName, $repoName], $all));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @param bool $all
     * @return Collection
     */
    public function fetchStatistics(string $userName, string $repoName, bool $all = false): Collection
    {
        return collect($this->fetchAllResults('repo', 'statistics', [$userName, $repoName], $all));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @param bool $all
     * @return Collection
     */
    public function fetchParticipations(string $userName, string $repoName, bool $all = false): Collection
    {
        return collect($this->fetchResults('repo', 'participation', [$userName, $repoName], $all));
    }

    /**
     * @param string $userName
     * @param string $repoName
     * @param bool $all
     * @return Collection
     */
    public function fetchPullRequests(string $userName, string $repoName, bool $all = false): Collection
    {
        return collect($this->fetchResults('pull_request', 'all', [$userName, $repoName, ['state' => 'all']], $all));
    }

    /**
     * @param string $interfaceName
     * @param string $method
     * @param array $parameters
     * @param bool $all
     * @return array
     */
    public function fetchResults(string $interfaceName, string $method, array $parameters, bool $all = false) : array
    {
        $cacheKey = $this->getCacheKey($interfaceName, $method, $parameters, $all);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        if ($all) {
            $result = $this->fetchAll($interfaceName, $method, $parameters);
        } else {
            $result = $this->fetch($interfaceName, $method, $parameters);
        }

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
     * @return array
     */
    private function fetch(string $interfaceName, string $method, array $parameters) : array
    {
        return (new ResultPager($this->client))->fetch(
            $this->client->api($interfaceName),
            $method,
            $parameters
        );
    }

    /**
     * @param string $interfaceName
     * @param string $method
     * @param array $parameters
     * @return array
     */
    private function fetchAll(string $interfaceName, string $method, array $parameters) : array
    {
        return (new ResultPager($this->client))->fetchAll(
            $this->client->api($interfaceName),
            $method,
            $parameters
        );
    }

    /**
     * @param string $interfaceName
     * @param string $method
     * @param array $parameters
     * @param bool $all
     * @return string
     */
    private function getCacheKey(string $interfaceName, string $method, array $parameters, bool $all) : string
    {
        return sprintf('%s_%s_%s_%d', $interfaceName, $method, sha1(serialize($parameters)), (int) $all);
    }
}
