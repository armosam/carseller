@props(['title' => '', 'bodyClass' => '', 'footerLinks' => ''])

<x-base-layout :$title :$bodyClass>
    <x-layouts.header />

    {{$slot}}

    <x-layouts.footer>
        <x-slot:footerLinks>{{$footerLinks}}</x-slot>
    </x-layouts.footer>
</x-base-layout>
