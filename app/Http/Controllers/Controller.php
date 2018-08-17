<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getJsonFile(int $year, string $filename)
    {
        try {
            $content = Storage::get(sprintf('public/%d/%s.json', $year, $filename));
        } catch (\Exception $e) {
            $content = '{}';
        }
        return json_decode($content);
    }
}
