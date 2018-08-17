@extends('layouts.app')

@section('content')
    <div class="container content">

        <h1>About Magestats</h1>

        <h2>Where does the data come from?</h2>
        <p>All data comes from <a href="https://github.com/">Github</a> and is regularly retrieved via its <a href="https://developer.github.com/">API</a>. The data is prepared for the statistics on <strong>Magestats</strong> and stored in JSON format.</p>

        <h2>Which projects are included and which are not?</h2>
        <p><strong>Magestats</strong> has access to all public projects of Magento, these are: @foreach (explode(',', env('MAGENTO_REPOS')) as $repo) <a href="https://github.com/{{$repo}}">{{ $repo }}</a>@if($repo !== 'magento-research/pwa-studio'),@else.@endif @endforeach</p><p><strong>Magestats</strong> has no access to private projects like: @foreach (explode(',', env('MAGENTO_PRIVATE_REPOS')) as $repo) <a href="https://github.com/{{$repo}}">{{ $repo }}</a>@if($repo !== 'magento/bulk-api-ee'),@else.@endif @endforeach </p>

        <h2>I found a problem on Magestats, what should I do now?</h2>
        <p>If you're encountering a problem on <strong>Magestats</strong>, please go to the issues section on <a href="https://github.com/magestats/issues/issues">github.com/magestats</a> and check if it is a known issues first. If not you can <a href="https://github.com/magestats/issues/issues/new">create a new issue here</a>.</p>

        <h2>I miss a statistic or have a suggestion for Magestats, what should I do now?</h2>
        <p>If you think there is a statistic missing or you have an idea to improve <strong>Magestats</strong> go to the issues section on <a href="https://github.com/magestats/ideas/issues">github.com/magestats</a> and check if anyone else had the same idea as you. <br />If not on <a href="https://github.com/magestats/ideas/issues/new">create a new issue here</a> and describe your idea.</p>

        <h2>Who's behind Magestats?</h2>
        <p><strong>Magestats</strong> is created by <a href="https://github.com/mhauri">Marcel Hauri</a>, Magento 2 <a href="https://magento.com/magento-contributors#/community-maintainers">Core</a> & <a href="https://github.com/orgs/magento/teams/devdocs-maintainers/members">Devdocs</a> Maintainer</a></p>

    </div>
@endsection