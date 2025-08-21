<x-app-layout>
    @include('partials.navbar')

    <section class="Authentication">
        <div class="container signup">
            <div class="header">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo Image">
                </a>
            </div>
    
            <div class="custom_form">
                <form action="{{ route('register') }}" method="post">
                    @csrf
    
                    <div class="input_group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" placeholder="First Name" value={{ old('first_name') }}>
                        <span class="inline_alert">{{ $errors->first('first_name') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" placeholder="Last Name" value={{ old('last_name') }}>
                        <span class="inline_alert">{{ $errors->first('last_name') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}">
                        <span class="inline_alert">{{ $errors->first('email') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" value="{{ old('phone_number') }}">
                        <span class="inline_alert">{{ $errors->first('phone_number') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" value="{{ old('password') }}">
                        <span class="inline_alert">{{ $errors->first('password') }}</span>
                    </div>
    
                    <div class="input_group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}">
                        <span class="inline_alert">{{ $errors->first('password_confirmation') }}</span>
                    </div>
    
                    <button type="submit">Signup</button>
                </form>
    
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </section>
</x-app-layout>
