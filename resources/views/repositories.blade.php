@extends('layouts.app')

@section('content')
    <div class="container content" id="app">
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
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Pull Requests</h2>
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <repository-chart year="{{ $active_year }}" repo="{{ $repo }}/pullrequests/{{ (int) $active_month }}"></repository-chart>
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
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <repository-chart year="{{ $active_year }}" repo="{{ $repo }}/issues/{{ (int) $active_month }}"></repository-chart>
                @else
                    <repository-chart year="{{ $active_year }}" repo="{{ $repo }}/issues"></repository-chart>
                @endif
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Contributors</h2>
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <contributors year="{{ $active_year }}" repo="{{ $repo }}/contributors/{{ (int) $active_month }}"></contributors>
                @else
                    <contributors year="{{ $active_year }}" repo="{{ $repo }}/contributors"></contributors>
                @endif
            </div>
        </div>
    </div>
@endsection