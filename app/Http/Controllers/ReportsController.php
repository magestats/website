<?php

namespace App\Http\Controllers;

use App\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class ReportsController extends Controller
{
    public function index(string $year = '', string $month = '')
    {
        if (!$year) {
            $year = date('Y');
        }

        return view('reports')->with(
            [
                'title' => $this->getTitle('Reports for ', $year),
                'active_year' => $year,
                'pullrequests' => $this->getJsonFile($year, 'pullrequests'),
                'issues' => $this->getJsonFile($year, 'issues')
            ]
        );
    }

    private function getTitle(string $name, string $year)
    {
        return sprintf('%s %s', $name, $year);
    }
}
