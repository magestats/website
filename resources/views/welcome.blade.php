@extends('layouts.app')

@section('content')
    <div id="app" class="fullpage">
        <div class="section first">
            <h2>Total Pull Requests in {{ \Carbon\Carbon::now()->yearIso }}<sup><span class="small">*</span></sup></h2>
            <repository-chart repo="pullrequests" year="2018"></repository-chart>
            <span class="small">*for all Magento Community Projects</span>
        </div>
        <div class="section second">
            <h2>Total Issues in {{ \Carbon\Carbon::now()->yearIso }}<sup><span class="small">*</span></sup></h2>
            <repository-chart repo="issues" year="2018"></repository-chart>
            <span class="small">*for all Magento Community Projects</span>
        </div>
        <div class="section third">Section 3</div>
    </div>
@endsection