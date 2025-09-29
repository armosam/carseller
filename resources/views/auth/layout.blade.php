@props(['title' => '', 'bodyClass' => '', 'pageImage' => '', 'socialLogin' => '', 'footerLink' => ''])
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

                    @session('success')
                        <div class="container my-large">
                            <div class="success-message">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endsession
                    @session('error')
                        <div class="container my-large">
                            <div class="error-message text-error">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endsession

                    {{$slot}}

                    @if($socialLogin)
                    <div class="grid grid-cols-2 gap-1 social-auth-buttons">
                        <x-google-button name="Google"/>
                        <x-facebook-button name="Facebook"/>
                    </div>
                    @endif

                    @if($footerLink)
                    <div class="login-text-dont-have-account">
                        {{ $footerLink }}
                    </div>
                    @endif
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
