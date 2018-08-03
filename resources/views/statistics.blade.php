Last updated: {{ $updated_at }}

<ul>
@foreach ($contributors as $contributor)
    <li>{{ $contributor['author']['login'] }} - {{ $contributor['total'] }}</li>
@endforeach
</ul>