<?php

namespace App\Http\Controllers\PullRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerInterface;
use App\Services\GitHub\Api;

class ClosedController extends Controller implements ControllerInterface
{
    /**
     * @param Api $api
     * @param string $user
     * @param string $repo
     * @return $this
     */
    public function index(Api $api, string $user, string $repo)
    {
        $result = $api->fetchClosedPullRequests($user, $repo);
        return view('pullrequests')->with([
            'pullrequests' => $result->get(Api::GITHUB_API_RESULT_DATA),
            'updated_at' => $result->get(Api::GITHUB_API_RESULT_UPDATED_AT)
        ]);
    }
}
