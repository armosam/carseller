{{--@props(['class', 'style' => ''])--}}

{{--{{dump($attributes)}}--}}

{{--<button @class($class) >{{$slot}}</button>--}}

<button {{$attributes
    ->merge(['style' => 'color: white;']) // This is default attribute
    ->class("btn")}} >{{$slot}}</button>
