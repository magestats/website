<?php

namespace App\Http\Controllers;

use App\Repositories;

class RepositoriesController extends Controller
{
    public function index(Repositories $repositories, string $name, string $repo, string $year = null)
    {
        $data = $repositories->where('owner', $name)->where('name', $repo)->first()->toArray();
        $created = date('Y', strtotime($data['created']));

        if (!$year) {
            $year = date('Y');
        }

        if ($year >= $created && $year <= date('Y')) {
            return view('repositories')->with([
                'title' => sprintf('%s/%s', $name, $repo),
                'repo' => sprintf('%s/%s', $name, $repo),
                'year' => $year ?? date('Y'),
                'data' => $data,
                'selector' => range(date('Y'), $created)
            ]);
        }

        return redirect(sprintf('/repositories/%s/%s', $name, $repo));
    }
}
