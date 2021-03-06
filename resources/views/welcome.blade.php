@extends('layouts.app')
@section('description')In {{ $year }}, a total of {{ $pullrequests->created }} pull requests were created across all Magento Community Projects, {{ $pullrequests->merged }} of them were merged and {{ $pullrequests->closed - $pullrequests->merged  }} were closed without being merged.@endsection
@section('content')
    <div id="app">
        <div id="fullpage" class="fullpage-wrapper">
            <div class="section first">
                <div class="section-content">
                    <h2>{{ $pullrequests->created }} Pull Requests</h2>
                    <p>In {{ $year }}, a total of <span
                                class="label label-default created">{{ $pullrequests->created }}</span> pull requests
                        were created across all Magento
                        Community Projects, <span class="label label-default merged">{{ $pullrequests->merged }}</span>
                        of them were merged
                        and <span
                                class="label label-default closed">{{ $pullrequests->closed - $pullrequests->merged  }}</span>
                        were closed without being merged.</p>
                    <p><a href="{{ route('reports') }}" class="btn btn-primary btn-lg" role="button">Show reports</a>
                    </p>
                </div>
            </div>
            <div class="section second">
                <div class="section-content">
                    <h2>{{ $issues->closed }} Issues Closed</h2>
                    <p>In {{ $year }}, a total of <span
                                class="label label-default created">{{ $issues->created }}</span> issues were
                        created and <span class="label label-default closed">{{ $issues->closed }}</span> issues were
                        closed across all Magento Community Projects.</p>
                    <p><a href="{{ route('reports') }}" class="btn btn-primary btn-lg" role="button">Show reports</a>
                    </p>
                </div>
            </div>
            <div class="section third">
                <div class="section-content">
                    <h2>{{ $contributors }} individual Contributors</h2>
                    <p>In {{ $year }}, a total of <span class="label label-default created">{{ $contributors }}</span>
                        individual contributors contributed across all Magento Community Projects.
                        <br/><span class="label label-default first-time-contributors">{{ $first_time_contributors }}</span> of them contributed for the first time.</p>
                    <p><a href="{{ route('contributors') }}" class="btn btn-primary btn-lg" role="button">Show all
                            contributors</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection