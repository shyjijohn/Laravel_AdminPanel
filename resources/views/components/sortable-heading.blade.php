@props(['column', 'label', 'sort', 'direction', 'align' => null])

@php
    $active = $sort === $column;
    $nextDirection = $active && $direction === 'asc' ? 'desc' : 'asc';
    $query = array_merge(request()->query(), [
        'sort' => $column,
        'direction' => $nextDirection,
    ]);
    unset($query['page']);
@endphp

<th scope="col" @class(['text-end' => $align === 'end']) aria-sort="{{ $active ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
    <a class="sortable-heading {{ $active ? 'is-active' : '' }}" href="{{ url()->current() }}?{{ http_build_query($query) }}">
        <span>{{ $label }}</span>
        <span class="sortable-heading__icon" aria-hidden="true">
            @if ($active)
                {{ $direction === 'asc' ? '↑' : '↓' }}
            @else
                ↕
            @endif
        </span>
        <span class="visually-hidden">
            {{ $active ? 'Sorted '.($direction === 'asc' ? 'ascending' : 'descending').'.' : '' }}
            Sort {{ $nextDirection === 'asc' ? 'ascending' : 'descending' }}.
        </span>
    </a>
</th>
