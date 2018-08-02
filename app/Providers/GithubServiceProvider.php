<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\GitHub\Api;
use Github\Client;

class GithubServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Api::class, function () {
            $client = new Client();
            $client->authenticate(config('services.github.token'), null, Client::AUTH_HTTP_TOKEN);
            return new Api($client);
        });
    }
}