<x-app-layout>
    @include('partials.navbar')

    <section class="Authentication">
        <div class="container login">
            <div class="header">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo Image">
                </a>
            </div>
    
            <div class="custom_form">
                <form action="{{ route('login') }}" method="post">
                    @csrf
    
                    <div class="input_group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}">
                        <span class="inline_alert">{{ $errors->first('email') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" value="{{ old('password') }}">
                        <span class="inline_alert">{{ $errors->first('password') }}</span>
                    </div>
    
                    <div class="input_group">
                        <a href="{{ route('password.request') }}">Forgot your password?</a>
                    </div>
    
                    <button type="submit">Login</button>
                </form>
    
                <p>Don't have an account? <a href="{{ route('register') }}">Signup</a></p>
            </div>
        </div>
    </section>
</x-app-layout>

<!-- Remember Me -->
{{-- <div class="block mt-4">
    <label for="remember_me" class="inline-flex items-center">
        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
    </label>
</div>

<div class="flex items-center justify-end mt-4">
    @if (Route::has('password.request'))
        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
            {{ __('Forgot your password?') }}
        </a>
    @endif
</div> --}}


{{-- <x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
