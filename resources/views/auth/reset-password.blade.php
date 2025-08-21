<x-app-layout>
    @include('partials.navbar')

    <section class="Authentication">
        <div class="container reset_password">
            <div class="header">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo Image">
                </a>
            </div>
    
            <div class="custom_form">
                <p>Set a new password that's complex but easy to remember.</p>
    
                <form action="{{ route('password.store') }}" method="post">
                    @csrf
    
                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    
    
                    <div class="input_group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email', $request->email) }}">
                        <span class="inline_alert">{{ $errors->first('email') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" value="{{ old('password') }}">
                        <span class="inline_alert">{{ $errors->first('password') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="password_confirmation">Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Password" value="{{ old('password_confirmation') }}">
                        <span class="inline_alert">{{ $errors->first('password_confirmation') }}</span>
                    </div>
    
                    <button type="submit">Reset Password</button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
