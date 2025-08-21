<x-app-layout>
    @include('partials.navbar')

    <section class="Authentication">
        <div class="container forgot_password">
            <div class="header">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo Image">
                </a>
            </div>
    
            <div class="custom_form">
                @include('partials.messages')
                <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
    
                <form action="{{ route('password.email') }}" method="post">
                    @csrf
    
                    <div class="input_group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}">
                        <span class="inline_alert">{{ $errors->first('email') }}</span>
                    </div>
    
                    <button type="submit">Email Password Reset Link</button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>


{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
