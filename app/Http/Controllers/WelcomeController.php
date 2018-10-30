<?php

namespace App\Http\Controllers;

class WelcomeController extends Controller
{
    public function index()
    {
        $year = getenv('WELCOME_YEAR');
        $pullRequests = $this->getJsonFile($year, 'pullrequests');
        $issues = $this->getJsonFile($year, 'issues');
        $contributors = $this->getJsonFile($year, 'contributors');
        return view('welcome')->with([
            'title' => null,
            'year' => $year,
            'pullrequests' => $pullRequests->total,
            'issues' => $issues->total,
            'contributors' => \count((array)$contributors->contributors)
        ]);
    }
}
