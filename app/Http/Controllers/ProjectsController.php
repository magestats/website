<?php

namespace App\Http\Controllers;

class ProjectsController extends Controller
{
    public function index(string $name, string $repo)
    {
        return view('welcome')->with(['title' => sprintf('%s/%s', $name, $repo)]);
    }
}
