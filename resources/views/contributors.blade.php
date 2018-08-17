@extends('layouts.app')

@section('description')In {{ $active_year }}, a total of {{ $total }} individual contributors contributed across all Magento Community projects.@endsection
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
                </div>
            </div>
            <div class="col-xs-12">
                <p class="title">In {{ $active_year }}, a total of {{ $total }} individual contributors contributed across all Magento Community projects.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <contributors year="{{ $active_year }}" repo="/contributors"></contributors>
            </div>
        </div>
    </div>
@endsection