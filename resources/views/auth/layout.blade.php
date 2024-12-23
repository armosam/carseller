@props(['title' => '', 'bodyClass' => '', 'pageImage' => ''])
<x-base-layout :$title :$bodyClass>
    <main>
        <div class="container-small page-login">
            <div class="flex" style="gap: 5rem">
                <div class="auth-page-form">
                    <div class="text-center">
                        <a href="/">
                            <img src="/img/logoipsum-265.svg" alt="" />
                        </a>
                    </div>
                    <h1 class="auth-page-title">{{$title}}</h1>

                    {{$slot}}

                </div>
                @if($pageImage)
                    <div class="auth-page-image">
                        {{$pageImage}}
                    </div>
                @endif
            </div>
        </div>
    </main>
</x-base-layout>
