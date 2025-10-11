<x-auth-layout title="Signup" bodyClass="page-signup" socialLogin="true">
    <form action="" method="post">
        @csrf

        <x-form.input type="email" name="email" :value="old('email')" placeholder="Your Email" />
        <x-form.input type="password" name="password" placeholder="Your Password" autocomplete="off" />
        <x-form.input type="password" name="password_confirmation" placeholder="Repeat Password" autocomplete="off" />
        <x-form.input type="text" name="first_name" :value="old('first_name')" placeholder="First Name" />
        <x-form.input type="text" name="last_name" :value="old('last_name')" placeholder="Last Name" />
        <x-form.input type="phone" name="phone" :value="old('phone')" placeholder="Phone Number" />

        <button class="btn btn-primary btn-login w-full">Register</button>

    </form>

    <x-slot:footerLink>
        Already have an account? - <a href="{{route('login')}}">Click here to login</a>
    </x-slot:footerLink>

    <x-slot:pageImage>
        <img src="/img/car-png_7.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>
