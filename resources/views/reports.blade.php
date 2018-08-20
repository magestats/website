@extends('layouts.app')
@section('content')
    <div class="container content reports" id="app">
        <div class="row">
            <div class="col-xs-12">
                <h2>Pull Requests</h2>
                <repository-chart year="{{ $active_year }}" repo="pullrequests"></repository-chart>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Parameter</th>
                            @foreach($pullrequests->labels as $label)
                                <th>{{ substr($label, 0, 3) }}.</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pullrequests->_data as $label => $values)
                            <tr class="{{ strtolower($label) }}-light">
                                <td>{{ $label }}</td>
                                @foreach($values as $month => $value)
                                    @if((int)$active_year === (int)date('Y') && (int)date('m') < $month)
                                        @continue
                                    @endif
                                    <td>{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Issues</h2>
                <repository-chart year="{{ $active_year }}" repo="issues"></repository-chart>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <h2>Contributors</h2>

            </div>
        </div>
    </div>
@endsection