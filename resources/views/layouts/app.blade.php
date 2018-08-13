<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/magestats-icon-colored.png') }}"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' | ': '' }}{{ config('app.name', 'Magestats') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar large"></span>
                <span class="icon-bar medium"></span>
                <span class="icon-bar small"></span>
            </button>
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/magestats.svg') }}" alt="{{ config('app.name', 'Magestats') }}"
                     class="logo"/>
            </a>
        </div>
        <div id="navbar" class="navbar navbar-inverse navbar-fixed-top navbar-collapse collapse">
            <div class="container">
                <ul class="nav navbar-nav">
                    <li{{ Request::is('contributors') ? ' class=active' : null }}><a href="{{route('contributors')}}">Contributors</a>
                    </li>
                    <li class="dropdown{{ Request::is('repositories/*') ? ' active' : null }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Repositories<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @foreach (explode(',', env('MAGENTO_REPOS')) as $repo)
                                <li{{ Request::is('repositories/' . $repo) ? ' class=active' : null }}><a
                                            href="/repositories/{{ $repo }}">{{ $repo }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li{{ Request::is('about') ? ' class=active' : null }}><a href="/about">About Magestats</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
@yield('content')
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>