<x-auth-layout title="Login" bodyClass="page-login">
    <form action="" method="post">
        @csrf

        <x-form.input type="email" name="email" :value="old('email')" placeholder="Your Email" />
        <x-form.input type="password" name="password" placeholder="Your Password" autocomplete="off" />

        <div class="text-right mb-medium">
            <a href="{{ route('password.reset') }}" class="auth-page-password-reset">Reset Password</a>
        </div>

        <button class="btn btn-primary btn-login w-full">Login</button>

        <div class="grid grid-cols-2 gap-1 social-auth-buttons">
            <x-google-button name="Google"/>
            <x-facebook-button name="Facebook"/>
        </div>

        <div class="login-text-dont-have-account">
            Don't have an account? -
            <a href="{{route('signup')}}"> Click here to create one</a>
        </div>
    </form>

    <x-slot:pageImage>
        <img src="/img/car-png-39071.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>

