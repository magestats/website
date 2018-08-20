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
            $contributors = $this->getJsonFile($year, 'contributors');
            return view('contributors')->with([
                'title' => $this->getTitle('Contributors in ', $year),
                'active_year' => (int)$year,
                'total' => \count((array)$contributors->contributors)
            ]);
        }
        return redirect(sprintf('/contributors'));
    }

    private function getTitle(string $name, string $year)
    {
        return sprintf('%s %s', $name, $year);
    }
}
