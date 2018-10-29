<?php

namespace App\Http\Controllers;

use App\Issues;
use App\PullRequests;
use Illuminate\Support\Facades\Storage;

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
        return view('contributor')->with([
            'title' => $author,
            'author' => $author,
            'avatar' => $avatar,
            'data' => $data
        ]);
    }

    private function getTitle(string $name, string $year)
    {
        return sprintf('%s %s', $name, $year);
    }

    private function getData(string $type, array $dataSet)
    {
        $data = [];
        foreach ($dataSet as $row) {
            $state = $row['state'];
            if (isset($row['merged'])) {
                $state = 'merged';
            }
            $data[$type][date('Y', strtotime($row['created']))][$row['number']] = [
                'created' => $row['created'],
                'repo' => $row['repo'],
                'state' => $state,
                'title' => $row['title'],
                'url' => $row['html_url']
            ];
        }
        return $data;
    }
}
