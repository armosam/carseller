<x-auth-layout title="Password Reset" bodyClass="page-login">
    <form action="{{route('password.storeResetRequest')}}" method="post">
        @csrf

        <x-form.input type="email" name="email" :value="old('email')" placeholder="Your Email" />

        <button class="btn btn-primary btn-login w-full">Reset Password</button>

    </form>

    <x-slot:footerLink>
        Don't need to reset your password? - <a href="{{route('login')}}">Click here to login</a>
    </x-slot:footerLink>

    <x-slot:pageImage>
        <img src="/img/car-png_5.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>

