<?php

namespace App\Http\Controllers;

use App\Repositories;

class RepositoriesController extends Controller
{
    public function index(Repositories $repositories, string $name, string $repo)
    {
        return view('repositories')->with([
            'title' => sprintf('%s/%s', $name, $repo),
            'repo' => sprintf('%s/%s', $name, $repo),
            'year' => date('Y'),
            'data' => $repositories->where('owner', $name)->where('name', $repo)->get()->toArray()
            ]);
    }
}
