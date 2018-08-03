Last updated: {{ $updated_at }}

<ul>
@foreach ($pullrequests as $pullrequest)
    <li>{{ $pullrequest['user']['login'] }} - {{ $pullrequest['merged_at'] }}</li>
@endforeach
</ul>