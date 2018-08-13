@extends('layouts.app')

@section('content')
    <div class="container content" id="app">
        <h1>{{ $title }}</h1>
        <repository repo="{{ $repo }}" year="{{ $year }}"></repository>
    </div>
@endsection