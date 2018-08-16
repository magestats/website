@extends('layouts.app')

@section('content')
    <div class="container content" id="app">
        <div class="row">
            <div class="col-xs-12">
                <h1>{{ $title }}</h1>
                <contributors year="2018" repo="/contributors"></contributors>
            </div>
        </div>
    </div>
@endsection