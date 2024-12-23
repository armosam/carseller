<x-auth-layout title="Signup" bodyClass="page-signup">
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <input type="email" name="email" placeholder="Your Email"/>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Your Password" autocomplete="off"/>
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" placeholder="Repeat Password" autocomplete="off"/>
        </div>
        <hr/>
        <div class="form-group">
            <input type="text" name="first_name" placeholder="First Name"/>
        </div>
        <div class="form-group">
            <input type="text" name="last_name" placeholder="Last Name"/>
        </div>
        <div class="form-group">
            <input type="text" name="phone" placeholder="Phone"/>
        </div>
        <button class="btn btn-primary btn-login w-full">Register</button>

        <div class="grid grid-cols-2 gap-1 social-auth-buttons">
            <x-google-button name="Google"/>
            <x-facebook-button name="Facebook"/>
        </div>

        <div class="login-text-dont-have-account">
            Already have an account? -
            <a href="{{route('login')}}"> Click here to login </a>
        </div>
    </form>

    <x-slot:pageImage>
        <img src="/img/car-png-39071.png" alt="" class="img-responsive" />
    </x-slot:pageImage>
</x-auth-layout>
