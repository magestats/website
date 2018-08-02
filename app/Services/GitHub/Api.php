<?php
declare(strict_types=1);

namespace App\Services\GitHub;

use Github\Client;
use Github\ResultPager;
use Illuminate\Support\Collection;

class Api
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchContributors(string $userName, string $repoName): Collection
    {
        return collect($this->fetchAllResults('repo', 'statistics', [$userName, $repoName]));
    }

    private function fetchAllResults(string $interfaceName, string $method, array $parameters)
    {
        return (new ResultPager($this->client))->fetchAll(
            $this->client->api($interfaceName),
            $method,
            $parameters
        );
    }
}