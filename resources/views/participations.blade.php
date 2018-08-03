Last updated: {{ $updated_at }}

<ul>
@foreach ($participations['all'] as $participation)
    <li>{{ $participation }}</li>
@endforeach
</ul>