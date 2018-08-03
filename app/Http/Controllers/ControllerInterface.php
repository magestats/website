<?php

namespace App\Http\Controllers;

use App\Services\GitHub\Api;

interface ControllerInterface
{
    /**
     * @param Api $api
     * @param string $user
     * @param string $repo
     * @return $this
     */
    public function index(Api $api, string $user, string $repo);
}