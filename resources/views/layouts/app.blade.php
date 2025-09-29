@props(['title' => '', 'bodyClass' => '', 'footerLinks' => ''])

<x-base-layout :$title :$bodyClass>
    <x-layouts.header />

    @session('success')
        <div class="container my-large">
            <div class="success-message">
                {{session('success')}}
            </div>
        </div>
    @endsession
    @session('error')
    <div class="container my-large">
        <div class="error-session-message">
            {{ session('error') }}
        </div>
    </div>
    @endsession

    {{$slot}}

    <x-layouts.footer>
        <x-slot:footerLinks>{{$footerLinks}}</x-slot>
    </x-layouts.footer>
</x-base-layout>
