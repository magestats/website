<?php

namespace App\Http\Controllers;

use App\Utils\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ContributorsController extends Controller
{
    use Date;

    public function index(string $year = '', string $month = '')
    {
        $originalMonth = $month;
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if ((int)$year >= 2011 && (int)$year <= (int)date('Y')) {
            if ($originalMonth) {
                $contributors = $this->getJsonFile($year, sprintf('contributors/%s', (int)$originalMonth));
            } else {
                $contributors = $this->getJsonFile($year, 'contributors');
            }


            return view('contributors')->with([
                'title' => $this->getTitle('Contributors in ', $year),
                'active_year' => $year ?? date('Y'),
                'active_month' => $month ?? date('m'),
                'active_english_month' => Carbon::create($year, $month)->format('F'),
                'month_selector' => $this->getValidatedMonthRange((int)$year),
                'total' => \count((array)$contributors->contributors)
            ]);
        }
        return redirect(sprintf('/contributors'));
    }

    public function byUsername(string $name)
    {
        $avatar = '';
        $author = $name;
        $data = [];
        foreach (range(2011, date('Y')) as $year) {
            if (Storage::exists(sprintf('public/%d/%s.json', $year, sprintf('contributors/%s', strtolower($name))))) {
                $data[$year] = $this->getJsonFile($year, sprintf('contributors/%s', strtolower($name)));
                $avatar = $data[$year]->avatar_url;
                $author = $data[$year]->author;
            }
        }
        krsort($data);
        if ($data) {
            return view('contributor')->with([
                'title' => $author,
                'author' => $author,
                'avatar' => $avatar,
                'data' => $data
            ]);
        }
        return abort(404);
    }

    private function getTitle(string $name, string $year)
    {
        return sprintf('%s %s', $name, $year);
    }

    private function getValidatedMonthRange(int $year): array
    {
        $monthRange = $this->getMonthRange($year, 2011, 12);
        foreach ($monthRange as $month => $name) {
            if (!Storage::exists(sprintf('public/%d/%s.json', $year, sprintf('contributors/%s', (int)$month)))) {
                unset($monthRange[$month]);
            }
        }
        return $monthRange;
    }
}
