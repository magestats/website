@extends('layouts.app')

@section('content')
    <div id="app">
        <div id="fullpage" class="fullpage-wrapper">
            <div class="section first">
                <div class="section-content">
                    <h2>{{ $pullrequests->created }} Pull Requests</h2>
                    <p>In {{ $year }}, across all Magento
                        Community Projects a total of <span
                                class="label label-default created">{{ $pullrequests->created }}</span> pull requests
                        were created, <span class="label label-default merged">{{ $pullrequests->merged }}</span>
                        of them were merged
                        and <span
                                class="label label-default closed">{{ $pullrequests->closed - $pullrequests->merged  }}</span>
                        were closed without being merged.</p>
                </div>
            </div>
            <div class="section second">
                <div class="section-content">
                    <h2>{{ $issues->closed }} Issues Closed</h2>
                    <p>In {{ $year }}, a total of <span class="label label-default created">{{ $issues->created }}</span> issues were
                        created and <span class="label label-default closed">{{ $issues->closed }}</span> issues were closed across all Magento Community Projects.</p>
                </div>
            </div>
            <div class="section third">
                <div class="section-content">
                    <h2>{{ $contributors }} individual Contributors</h2>
                    <p>In {{ $year }}, a total of <span class="label label-default created">{{ $contributors }}</span> individual contributors contributed across all Magento Community Projects.</p>
                    <p><a href="{{ route('contributors') }}" class="btn btn-primary btn-lg" role="button">Show all
                            contributors</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection