@extends('layouts.app')
@section('content')
    <div class="container content">
        <div class="row">
            <div class="col-xs-12">
                @foreach ($data as $type => $row)
                    <h2>Created {{ ucwords(str_replace('_', ' ', $type)) }}</h2>
                    @foreach ($row as $year => $parent)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="col-xs-2">Date</th>
                                    <th class="col-xs-2">Repository</th>
                                    <th class="col-xs-7">Title</th>
                                    <th class="col-xs-1">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($parent as $child)
                                    <tr>
                                        <td>{{ date('Y F d', strtotime($child['created'])) }}</td>
                                        <td><a href="/repositories/{{ $child['repo'] }}/{{ date('Y', strtotime($child['created'])) }}/{{ date('m', strtotime($child['created'])) }}">{{ $child['repo'] }}</a></td>
                                        <td><a href="{{ $child['url'] }}"
                                               rel="nofollow">{{ strip_tags($child['title']) }}</a></td>
                                        <td><span class="label label-{{ $child['state'] }}">{{ $child['state'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    <hr/>
                @endforeach
            </div>
        </div>
    </div>
@endsection