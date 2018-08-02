<?php

namespace App\Http\Controllers;

class ContributionsController extends Controller
{
    public function index(string $user, string $repo)
    {
        $data = ['user' => $user, 'repo' => $repo];
        return view('contributions')->with($data);
    }
}