@extends('layouts.app')
@section('keywords'){{ $repo }}@endsection
@section('description')In @if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }} {{ $active_year }}, @else {{ $active_year }}, @endif a total of {{ $pullrequests->created }} pull requests were created, {{ $pullrequests->merged }} of them were merged and {{ $pullrequests->closed - $pullrequests->merged }} were closed without being merged.@endsection
@section('content')
    <div class="container content repositories" id="app">
        <div class="row">
            <div class="col-xs-6">
                <h1>{{ $repo }}</h1>
            </div>
            <div class="col-xs-6">
                <div class="selector">
                    @if(count($year_selector) > 1)
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">{{ $active_year }} <span
                                        class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($year_selector as $year)
                                    <li{{ Request::is('*/' . $year) ? ' class=active' : null }}><a
                                                href="{{route('repositories', [$repo, $year])}}">{{ $year }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">@if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }}@else
                                    Entire year @endif <span
                                        class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{route('repositories', [$repo, $active_year])}}">Entire year</a></li>
                                <li role="separator" class="divider"></li>
                                @foreach($month_selector as $month => $value)
                                    <li{{ Request::is('*/' . $active_year . '/' . $month) ? ' class=active' : null }}><a
                                                href="{{route('repositories', [$repo, $active_year, $month])}}">{{ $value }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-xs-12">
                <p>
                    <strong>Link:</strong> <a href="{{ $data['html_url'] }}">{{ $data['html_url'] }}</a>
                    <br/>
                    <strong>Created:</strong> {{ \Carbon\Carbon::createFromTimeString($data['created'])->englishMonth }} {{ \Carbon\Carbon::createFromTimeString($data['created'])->year }}
                    <br />
                    <strong>Default branch:</strong> {{ $data['default_branch'] }}
                </p>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Pull Requests</h2>
                <p class="title">
                    In @if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }} {{ $active_year }}
                    , @else {{ $active_year }}, @endif a total of <span
                            class="label label-default created">{{ $pullrequests->created }}</span> pull requests were
                    created, <span class="label label-default merged">{{ $pullrequests->merged }}</span> of them were
                    merged and <span
                            class="label label-default closed">{{ $pullrequests->closed - $pullrequests->merged }}</span>
                    were closed without being merged.</p>
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <repository-chart year="{{ $active_year }}"
                                      repo="{{ $repo }}/pullrequests/{{ (int) $active_month }}"></repository-chart>
                @else
                    <repository-chart year="{{ $active_year }}" repo="{{ $repo }}/pullrequests"></repository-chart>
                @endif
                <br/>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Issues</h2>
                <p class="title">
                    In @if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }} {{ $active_year }}
                    , @else {{ $active_year }}, @endif a total of <span
                            class="label label-default created">{{ $issues->created }}</span> issues were created and
                    <span class="label label-default closed">{{ $issues->closed }}</span> issues were closed.</p>
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <repository-chart year="{{ $active_year }}"
                                      repo="{{ $repo }}/issues/{{ (int) $active_month }}"></repository-chart>
                @else
                    <repository-chart year="{{ $active_year }}" repo="{{ $repo }}/issues"></repository-chart>
                @endif
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Contributors</h2>
                <p class="title">
                    In @if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }} {{ $active_year }}
                    , @else {{ $active_year }}, @endif a total of <span
                            class="label label-default created">{{ $contributors }}</span> individual contributors
                    contributed to <strong>{{ $repo }}</strong>.</p>
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <contributors year="{{ $active_year }}"
                                  repo="{{ $repo }}/contributors/{{ (int) $active_month }}"></contributors>
                @else
                    <contributors year="{{ $active_year }}" repo="{{ $repo }}/contributors"></contributors>
                @endif
            </div>
        </div>
    </div>
@endsection