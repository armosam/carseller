@props(['user'])

<x-app-layout title="My Profile">
    <main>
        <div class="container-small">
            <h1 class="page-title">My Profile</h1>
            <div class="form-content">
                <div class="form-details">
                    <div class="row">
                        <div class="col">
                            <form class="card p-large my-large" action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group @error('email') has-error @enderror">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{old('email', $user->email)}}" placeholder="Email" @disabled($user->isOauthUser()) />
                                    <p class="error-message">{{$errors->first('email')}}</p>
                                </div>
                                <div class="form-group @error('first_name') has-error @enderror">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" value="{{old('first_name', $user->first_name)}}" placeholder="First Name"/>
                                    <p class="error-message">{{$errors->first('first_name')}}</p>
                                </div>
                                <div class="form-group @error('last_name') has-error @enderror">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" value="{{old('last_name', $user->last_name)}}" placeholder="Last Name"/>
                                    <p class="error-message">{{$errors->first('last_name')}}</p>
                                </div>
                                <div class="form-group @error('phone') has-error @enderror">
                                    <label>Phone</label>
                                    <input type="phone" name="phone" value="{{old('phone', $user->phone)}}" placeholder="Phone"/>
                                    <p class="error-message">{{$errors->first('phone')}}</p>
                                </div>

                                <div class="p-medium">
                                    <div class="flex justify-end gap-1">
                                        <button type="reset" class="btn btn-default">Reset</button>
                                        <button class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col">
                            @if(!$user->isOauthUser())
                                <form class="card p-large my-large" action="{{ route('profile.updatePassword') }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group @error('current_password') has-error @enderror">
                                        <label>Current Password</label>
                                        <input type="password" name="current_password" placeholder="Current Password"/>
                                        <p class="error-message">{{$errors->first('current_password')}}</p>
                                    </div>
                                    <div class="form-group @error('password') has-error @enderror">
                                        <label>New Password</label>
                                        <input type="password" name="password" placeholder="New Password"/>
                                        <p class="error-message">{{$errors->first('password')}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Repeat Password</label>
                                        <input type="password" name="password_confirmation" placeholder="Repeat Password"/>
                                    </div>

                                    <div class="p-medium">
                                        <div class="flex justify-end gap-1">
                                            <button class="btn btn-primary">Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
