@extends('layouts.app')

@section('content')
    <div id="app" class="fullpage">
        <div class="section">
            <h2>Pull Requests in 2018</h2>
            <repository repo="year" year="2018"></repository>
        </div>
        <div class="section orange">Section 2</div>
        <div class="section">Section 3</div>
    </div>
@endsection