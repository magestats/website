<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class SitemapController extends Controller
{
    public function index()
    {
        $contributors = [];
        foreach (range(2011, date('Y')) as $year) {
            if (Storage::exists(sprintf('public/%d/%s.json', $year, 'contributors'))) {
                $contributors[$year] = $this->getJsonFile($year, 'contributors');
            }
        }
        krsort($contributors);
        return view('sitemap')->with([
            'title' => 'Sitemap',
            'contributors' => $contributors,
            'repositories' => explode(',', getenv('MAGENTO_REPOS'))
        ]);
    }
}
