@extends('layouts.app')

@section('content')
    <div class="container content" id="app">
        <div class="row">
            <div class="col-xs-8">
                <h1>{{ $title }}</h1>
            </div>
            <div class="col-xs-4">
                <div class="selector">
                    @if(count($selector) > 1)
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">{{ $year }} <span
                                        class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($selector as $option)
                                    <li{{ Request::is('*' . $option) ? ' class=active' : null }}><a
                                                href="{{route('repositories', [$repo, $option])}}">{{ $option }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-xs-12">
                <h2>Pull Requests</h2>
                <repository-pullrequests year="{{ $year }}" repo="{{ $repo }}/year"></repository-pullrequests>
            </div>
        </div>
    </div>
@endsection