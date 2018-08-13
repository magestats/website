@extends('layouts.app')

@section('content')
    <div id="app" class="fullpage">
        <div class="section">
            <h2>Total Pull Requests in {{ \Carbon\Carbon::now()->yearIso }}<sup><span class="small">*</span></sup></h2>
            <repository-pullrequests repo="year" year="2018"></repository-pullrequests>
            <span class="small">*for all Magento Community Projects</span>
        </div>
        <div class="section orange">Section 2</div>
        <div class="section">Section 3</div>
    </div>
@endsection