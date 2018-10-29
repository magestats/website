<?php

namespace App\Http\Controllers;

use App\Issues;
use App\PullRequests;

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
        $data = array_merge($data, $this->getData('pull_requests', $pullRequests));
        $data = array_merge($data, $this->getData('issues', $issues));

        return view('contributor')->with([
            'title' => $name,
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
