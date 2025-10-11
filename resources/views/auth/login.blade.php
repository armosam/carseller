<x-auth-layout title="Login" bodyClass="page-login" socialLogin="true">
    <form action="" method="post">
        @csrf

        <x-form.input type="email" name="email" :value="old('email')" placeholder="Your Email" />
        <x-form.input type="password" name="password" placeholder="Your Password" autocomplete="off" />

        <div class="text-right mb-medium">
            <a href="{{ route('password.resetRequest') }}" class="auth-page-password-reset">Reset Password</a>
        </div>

        <button class="btn btn-primary btn-login w-full">Login</button>

    </form>

    <x-slot:footerLink>
        Don't have an account? - <a href="{{route('signup')}}">Click here to create one</a>
    </x-slot:footerLink>

    <x-slot:pageImage>
        <img src="/img/car-png-39071.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>

