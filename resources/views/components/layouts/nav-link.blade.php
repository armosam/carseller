@props(['type' => 'a', 'active' => false])

@if($type === 'a')
    <a {{ $active ? $attributes->style('text-shadow: 0 0 black;font-weight: bold;') : ''}} aria-current="{{$active ? 'page' : 'false'}}" {{$attributes}}>
        {{$slot}}
    </a>
@elseif($type === 'button')
    <button aria-current="{{$active ? 'page' : 'false'}}" {{ $attributes }}>{{$slot}}</button>
@endif
