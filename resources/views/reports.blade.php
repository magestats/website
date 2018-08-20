@extends('layouts.app')
@section('description')The numbers may differ from those listed in the monthly Community Engineering Hangouts. The reason for this is that Magestats does not have access to private or partner repositories of Magento.@endsection
@section('content')
    <div class="container content reports" id="app">
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
                            @foreach(range((int)date('Y'),2011) as $year)
                                <li{{ Request::is('*/' . $year) ? ' class=active' : null }}><a
                                            href="{{route('reports', [$year])}}">{{ $year }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <p>The numbers may differ from those listed in the monthly <a
                            href="https://www.youtube.com/channel/UCUsdK3NnJ0LqhNJCrJDdiug/featured">Community
                        Engineering Hangouts</a>. The reason for this is that Magestats does not have access to
                    private or partner repositories of Magento. More information on <a href="/about">about Magestats</a>.</p>
            </div>
        </div>
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
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                        $total = (array)$pullrequests->total;
                        @endphp
                        @foreach((array)$pullrequests->_data as $label => $values)
                            <tr class="{{ str_replace(' ','-', strtolower($label)) }}-light-20">
                                <td><strong>{{ $label }}</strong></td>
                                @foreach($values as $month => $value)
                                    @if((int)$active_year === (int)date('Y') && (int)date('n') < (int)$month)
                                        @continue
                                    @endif
                                    <td>{{ $value }}</td>
                                @endforeach

                                <td class="{{ str_replace(' ','-', strtolower($label)) }}-light-10">{{ $total[str_replace(' ','_', strtolower($label))] }}</td>
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
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Parameter</th>
                            @foreach($issues->labels as $label)
                                <th>{{ substr($label, 0, 3) }}.</th>
                            @endforeach
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $total = (array)$issues->total;
                        @endphp
                        @foreach((array)$issues->datasets as $values)
                            <tr class="{{ str_replace(' ','-', strtolower($values->label)) }}-light-20">
                                <td><strong>{{ $values->label }}</strong></td>
                                @foreach($values->data as $month => $value)
                                    @if((int)$active_year === (int)date('Y') && (int)date('n') <= (int)$month)
                                        @continue
                                    @endif
                                    <td>{{ $value }}</td>
                                @endforeach
                                <td class="{{ str_replace(' ','-', strtolower($values->label)) }}-light-10">{{ $total[str_replace(' ','_', strtolower($values->label))] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection