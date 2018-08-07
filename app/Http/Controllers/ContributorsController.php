<?php

namespace App\Http\Controllers;

use App\Statistics\StatisticsByYear;

class ContributorsController extends Controller
{
    public function index(StatisticsByYear $statisticsByYear)
    {
        return view('contributors')->with(['title' => 'Contributors']);
    }
}
