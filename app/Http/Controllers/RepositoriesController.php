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
        $createdYear = date('Y', strtotime($data['created']));
        $createdMonth = date('m', strtotime($data['created']));
        $originalMonth = $month;

        if ($year === date('Y') && !$month) {
            return redirect(sprintf('/repositories/%s/%s', $name, $repo));
        }

        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if ($year >= $createdYear && $year <= date('Y')) {
            if ($originalMonth) {
                $pullRequests = $this->getJsonFile($year, sprintf('%s/%s/pullrequests/%s', $name, $repo, (int)$originalMonth));
                $issues = $this->getJsonFile($year, sprintf('%s/%s/issues/%s', $name, $repo, (int)$originalMonth));
                $contributors = $this->getJsonFile($year, sprintf('%s/%s/contributors/%s', $name, $repo, (int)$originalMonth));
            } else {
                $pullRequests = $this->getJsonFile($year, sprintf('%s/%s/pullrequests', $name, $repo));
                $issues = $this->getJsonFile($year, sprintf('%s/%s/issues', $name, $repo));
                $contributors = $this->getJsonFile($year, sprintf('%s/%s/contributors', $name, $repo));
            }

            return view('repositories')->with([
                'title' => $this->getTitle($name, $repo, $year, $month),
                'repo' => sprintf('%s/%s', $name, $repo),
                'active_year' => $year ?? date('Y'),
                'active_month' => $month ?? date('m'),
                'active_english_month' => Carbon::create($year, $month)->format('F'),
                'data' => $data,
                'year_selector' => range(date('Y'), $createdYear),
                'month_selector' => $this->getMonthRange((int)$year, (int)$createdYear, (int)$createdMonth),
                'created_year' => $createdYear,
                'created_month' => $createdMonth,
                'pullrequests' => $pullRequests->total,
                'issues' => $issues->total,
                'contributors' => (isset($contributors->contributors)) ? \count((array)$contributors->contributors) : 0
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

        return sprintf('%s/%s - %s %s', $name, $repo, Carbon::create($year, $month)->format('F'), $year);
    }

    /**
     * @param int $year
     * @param int $createdYear
     * @param int $createdMonth
     * @return array
     */
    private function getMonthRange(int $year, int $createdYear, int $createdMonth): array
    {
        $minMonth = 1;
        $maxMonth= 12;
        if ($year === (int) date('Y')) {
            $maxMonth = date('m');
        }

        if ($year === $createdYear) {
            $minMonth = $createdMonth;
        }

        $months = [];
        foreach (range($minMonth, $maxMonth) as $month) {
            if ($month < 10) {
                $month = sprintf('0%d', $month);
            }
            $months[$month] = Carbon::create($year, $month)->format('F');
        }
        return $months;
    }
}
