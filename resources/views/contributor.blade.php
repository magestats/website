@extends('layouts.app')
@section('image'){{$avatar}}@endsection
@section('content')
    <div class="container content contributor">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <span class="avatar">
                        <a href="https://github.com/{{ $author }}" rel="nofollow"><img src="{{ $avatar }}" alt="{{ $author }}"/></a>
                    </span>
                    <span class="title">
                        {{ $author }}
                    </span>
                </h1>

                <hr/>
                <h2>Pull Requests</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="col-xs-2">Created</th>
                            <th class="col-xs-2">Repository</th>
                            <th class="col-xs-7">Title</th>
                            <th class="col-xs-1">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $year => $row)
                            @foreach ($row->_pull_requests as $pull_request)
                                <tr>
                                    <td>{{ date('d. F Y', strtotime($pull_request->created)) }}</td>
                                    <td>
                                        <a href="/repositories/{{ $pull_request->repo }}/{{ date('Y', strtotime($pull_request->created)) }}/{{ date('m', strtotime($pull_request->created)) }}">{{ $pull_request->repo }}</a>
                                    </td>
                                    <td><a href="{{ $pull_request->html_url }}"
                                           rel="nofollow">{{ $pull_request->title }}</a></td>
                                    <td>
                                        <span class="label label-{{$pull_request->state}}">{{$pull_request->state}}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <hr />
                <h2>Issues</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="col-xs-2">Created</th>
                            <th class="col-xs-2">Repository</th>
                            <th class="col-xs-7">Title</th>
                            <th class="col-xs-1">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $year => $row)
                            @foreach ($row->_issues as $issue)
                                <tr>
                                    <td>{{ date('d. F Y', strtotime($issue->created)) }}</td>
                                    <td>
                                        <a href="/repositories/{{ $issue->repo }}/{{ date('Y', strtotime($issue->created)) }}/{{ date('m', strtotime($issue->created)) }}">{{ $issue->repo }}</a>
                                    </td>
                                    <td><a href="{{ $issue->html_url }}" rel="nofollow">{{ $issue->title }}</a></td>
                                    <td>
                                        <span class="label label-{{$issue->state}}">{{$issue->state}}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection