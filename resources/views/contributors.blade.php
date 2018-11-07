@extends('layouts.app')
@if(!Request::is('*/' . $active_year))
@section('meta')<link rel="canonical" href="{{ route('contributors', [$active_year]) }}" />@endsection
@endif
@section('description')In @if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }} {{ $active_year }}, @else {{ $active_year }}, @endif a total of {{ $total }} individual contributors contributed across all Magento Community Projects.@endsection
@section('content')
    <div class="container content" id="app">
        <div class="row">
            <div class="col-xs-8">
                <h1>{{ $title }}</h1>
            </div>
            <div class="col-xs-4">
                <div class="selector">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">{{ $active_year }} <span
                                    class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(range(date('Y'),2011) as $year)
                                <li{{ Request::is('*/' . $year) ? ' class=active' : null }}><a
                                            href="{{route('contributors', [$year])}}">{{ $year }}</a>
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
                            <li><a href="{{route('contributors', [$active_year])}}">Entire year</a></li>
                            <li role="separator" class="divider"></li>
                            @foreach($month_selector as $month => $value)
                                <li{{ Request::is('*/' . $active_year . '/' . $month) ? ' class=active' : null }}><a
                                            href="{{route('contributors', [$active_year, $month])}}">{{ $value }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <p class="title">In @if(Request::is('*/' . $active_year . '/' . $active_month)){{ $active_english_month }} {{ $active_year }}, @else {{ $active_year }}, @endif a total of <span class="label label-default created">{{ $total }}</span> individual contributors contributed across all Magento Community Projects.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                @if(Request::is('*/' . $active_year . '/' . $active_month))
                    <contributors year="{{ $active_year }}"
                                      repo="/contributors/{{ (int) $active_month }}"></contributors>
                @else
                    <contributors year="{{ $active_year }}" repo="/contributors"></contributors>
                @endif
            </div>
        </div>
    </div>
@endsection