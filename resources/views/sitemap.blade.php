@extends('layouts.app')
@section('robots')NOINDEX,FOLLOW@endsection
@section('content')
    <div class="container content sitemap">
        <h1>{{$title}}</h1>
        <hr />
        <h2>Repositories</h2>
        <ul class="row">
        @foreach ($repositories as $repository)
                <li class="col-xs-6 col-md-4"><a href="/repositories/{{$repository}}">{{$repository}}</a></li>
        @endforeach
        </ul>
        <hr/>
        @foreach ($contributors as $year => $row)
            <h2>Contributors in {{$year}}</h2>
            <ul class="row">
                @foreach ($row->contributors as $author => $contributor)
                    <li class="col-xs-6 col-md-3"><a href="/contributor/{{ $author }}">{{ $author }}</a></li>
                @endforeach
            </ul>
            <hr />
        @endforeach
    </div>
@endsection