<x-auth-layout title="Password Reset" bodyClass="page-login">
    <form action="" method="post">
        @csrf

        <x-form.input type="password" name="password" placeholder="New Password" autocomplete="off" />
        <x-form.input type="password" name="password_confirmation" placeholder="Confirm Password" autocomplete="off" />

        <button class="btn btn-primary btn-login w-full">Set New Password</button>

        <div class="login-text-dont-have-account">
            Don't need to reset your password ? -
            <a href="{{route('login')}}"> Click here to login</a>
        </div>
    </form>

    <x-slot:pageImage>
        <img src="/img/car-png-39071.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>

