Last updated: {{ $updated_at }}

<ul>
@foreach ($contributors as $contributor)
    <li>{{ $contributor['login'] }} - {{ $contributor['contributions'] }}</li>
@endforeach
</ul>