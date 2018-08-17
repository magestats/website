@extends('layouts.app')

@section('content')
    <div id="app" class="fullpage">
        <div class="section first">
            <h2>{{ $pullrequests->created }} Pull Requests</h2>
            <p>In {{ $year }}, a total of {{ $pullrequests->created }} pull requests were created across all Magento Community projects, {{ $pullrequests->merged }} of them were merged and {{ $pullrequests->closed - $pullrequests->merged  }} were closed without being merged.</p>
            <repository-chart repo="pullrequests" year="{{ $year }}"></repository-chart>
        </div>
        <div class="section second">
            <h2>{{ $issues->closed }} Issues Closed</h2>
            <p>A total of {{ $issues->created }} issues were opened across all Magento Community projects and {{ $issues->closed }} issues were closed in {{ $year }}.</p>
            <repository-chart repo="issues" year="{{ $year }}"></repository-chart>
        </div>
        <div class="section third">
            <h2>{{ $contributors }} individual Contributors</h2>
            <p>In {{ $year }}, a total of {{ $contributors }} individual contributors contributed to Magento open source projects.</p>
            <top-contributors year="{{ $year }}" repo="/contributors" limit="18"></top-contributors>
            <p><a href="{{ route('contributors') }}" class="btn btn-primary btn-lg" role="button">Show all contributors</a></p>
        </div>
    </div>
@endsection