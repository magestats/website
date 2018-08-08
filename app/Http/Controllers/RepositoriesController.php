<?php

namespace App\Http\Controllers;

use App\Repositories;

class RepositoriesController extends Controller
{
    public function index(Repositories $repositories, string $name, string $repo)
    {
        return view('repositories')->with([
            'title' => sprintf('%s/%s', $name, $repo),
            'data' => $repositories->where('owner', $name)->where('name', $repo)->first()->toArray()]);
    }
}
