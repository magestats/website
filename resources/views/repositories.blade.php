@extends('layouts.app')

@section('content')
    <div class="container content">
        <h1>{{ $title }}</h1>
        @if(isset($data['description']))
            <p><strong>Description:</strong></p>
            <p>{{ $data['description'] }}</p>
        @endif
        <ul>
            <li><strong>Open issues:</strong> {{ $data['open_issues'] }}</li>
        </ul>
    </div>
@endsection