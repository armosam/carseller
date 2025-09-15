<x-auth-layout title="Password Reset" bodyClass="page-login">
    <form action="{{route('password.store')}}" method="post">
        @csrf

        <x-form.input type="email" name="email" autocomplete="off" value="{{old('email', $email)}}" />
        <x-form.input type="hidden" name="token" autocomplete="off" value="{{old('token', $token)}}" />
        <x-form.input type="password" name="password" placeholder="New Password" autocomplete="off" />
        <x-form.input type="password" name="password_confirmation" placeholder="Confirm Password" autocomplete="off" />

        <button class="btn btn-primary btn-login w-full">Set New Password</button>

    </form>

    <x-slot:footerLink>
        Don't need to reset your password ? - <a href="{{route('login')}}"> Click here to login</a>
    </x-slot:footerLink>

    <x-slot:pageImage>
        <img src="/img/car-png-39071.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>

