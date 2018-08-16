<?php

namespace App\Http\Controllers;

class ContributorsController extends Controller
{
    public function index(string $year = '')
    {
        if (!$year) {
            $year = date('Y');
        }
        if ((int)$year >= 2011 && (int)$year <= (int)date('Y')) {
            return view('contributors')->with([
                'title' => 'Contributors',
                'active_year' => (int)$year,
            ]);
        }
        return redirect(sprintf('/contributors'));
    }
}
