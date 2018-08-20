<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/magestats-icon-colored.png') }}"/>
    <meta name="HandheldFriendly" content="True">
    <meta name="description" content="@section('description', config('app.name', 'Magestats'))">
    <meta name="keywords" content="magestats, magento, statistics, github, open source, community, magento community, open source statistics, @yield('keywords', 'magestats.net')">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ isset($title) ? $title . ' | ': '' }}{{ config('app.name', 'Magestats') }}" />
    <meta property="og:description" content="@yield('description', config('app.name', 'Magestats'))" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:image" content="@yield('image', 'https://magestats.net/images/profile.png')" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@magestats_net" />
    <meta name="twitter:title" content="{{ isset($title) ? $title . ' | ': '' }}{{ config('app.name', 'Magestats') }}" />
    <meta name="twitter:description" content="@yield('description', config('app.name', 'Magestats'))" />
    <meta name="twitter:url" content="{{ Request::url() }}" />
    <meta name="twitter:image" content="@yield('image', 'https://magestats.net/images/profile.png')" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' | ': '' }}{{ config('app.name', 'Magestats') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-1020866-28"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-1020866-28');
    </script>
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
                    <li class="dropdown{{ Request::is('contributors/*') ? ' active' : null }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Contributors<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @foreach (range((int)date('Y'), 2011) as $reportsYear)
                                <li{{ Request::is('contributors/' . $reportsYear) ? ' class=active' : null }}><a
                                            href="/contributors/{{ $reportsYear }}">{{ $reportsYear }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="dropdown{{ Request::is('reports/*') ? ' active' : null }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Reports<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @foreach (range((int)date('Y'), 2011) as $reportsYear)
                                <li{{ Request::is('reports/' . $reportsYear) ? ' class=active' : null }}><a
                                            href="/reports/{{ $reportsYear }}">{{ $reportsYear }}</a></li>
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
<a href="#top" id="back-to-top" class="btn btn-default">Top</a>
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>