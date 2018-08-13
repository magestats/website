<?php

namespace App\Http\Controllers;

class ContributorsController extends Controller
{
    public function index()
    {
        return view('contributors')->with(['title' => 'Contributors']);
    }
}
