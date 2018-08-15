<?php

namespace App\Http\Controllers;

use App\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class RepositoriesController extends Controller
{
    public function index(Repositories $repositories, string $name, string $repo, string $year = '', string $month = '')
    {
        $data = $repositories->where('owner', $name)->where('name', $repo)->first()->toArray();
        $created = date('Y', strtotime($data['created']));

        if ($year === date('Y') && !$month) {
            return redirect(sprintf('/repositories/%s/%s', $name, $repo));
        }

        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if ($year >= $created && $year <= date('Y')) {
            return view('repositories')->with([
                'title' => $this->getTitle($name, $repo, $year, $month),
                'repo' => sprintf('%s/%s', $name, $repo),
                'active_year' => $year ?? date('Y'),
                'active_month' => $month ?? date('m'),
                'active_english_month' => Carbon::create($year, $month)->englishMonth,
                'data' => $data,
                'year_selector' => range(date('Y'), $created),
                'month_selector' => $this->getMonthRange((int) $year),
            ]);
        }

        return redirect(sprintf('/repositories/%s/%s', $name, $repo));
    }

    private function getTitle(string $name, string $repo, string $year, string $month)
    {
        if ($year === date('Y') && $month === date('m')) {
            return sprintf('%s/%s - %s', $name, $repo, $year);
        }

        if (!Request::is('*/' . $month)) {
            return sprintf('%s/%s - %s', $name, $repo, $year);
        }

        return sprintf('%s/%s - %s %s', $name, $repo, Carbon::create($year, $month)->englishMonth, $year);
    }

    /**
     * @param int $year
     * @return array
     */
    private function getMonthRange(int $year): array
    {
        $maxMonths = 12;
        if ($year === (int) date('Y')) {
            $maxMonths = date('m');
        }

        $months = [];
        foreach (range(1, $maxMonths) as $month) {
            if ($month < 10) {
                $month = sprintf('0%d', $month);
            }
            $months[$month] = Carbon::create($year, $month)->englishMonth;
        }
        return $months;
    }
}
