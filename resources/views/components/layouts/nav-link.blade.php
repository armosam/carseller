@props(['type' => 'a', 'active' => false])

@if($type === 'a')
    <a {{ $active ? $attributes->style('text-shadow: 0 0 black;font-weight: bold;') : ''}} {{$attributes}}>
        {{$slot}}
    </a>
@elseif($type === 'button')
    <button {{ $attributes }}>{{$slot}}</button>
@endif
