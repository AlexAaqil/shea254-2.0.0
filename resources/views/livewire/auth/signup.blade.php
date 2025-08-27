<div class="Authentication">
    <div class="container Signup">
        <div class="custom_form">
            <div class="header">
                <h1>Signup</h1>
            </div>

            <form wire:submit="signup">
                <div class="inputs">
                    <label for="first_name">First Name</label>
                    <input type="text" wire:model="first_name" name="first_name" id="first_name" autocomplete="first_name" autofocus>
                    <x-form-input-error field="first_name" />
                </div>

                <div class="inputs">
                    <label for="last_name">Last Name</label>
                    <input type="text" wire:model="last_name" name="last_name" id="last_name" autocomplete="last_name">
                    <x-form-input-error field="last_name" />
                </div>

                <div class="inputs">
                    <label for="email">Email Address</label>
                    <input type="email" wire:model="email" name="email" id="email" autocomplete="username">
                    <x-form-input-error field="email" />
                </div>

                <div class="inputs">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" wire:model="phone_number" name="phone_number" id="phone_number" autocomplete="phone_number">
                    <x-form-input-error field="phone_number" />
                </div>

                <div class="inputs">
                    <label for="password">Password</label>
                    <input type="password" wire:model="password" name="password" id="password" autocomplete="new-password">
                    <x-form-input-error field="password" />
                </div>

                <div class="inputs">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
                    <x-form-input-error field="password_confirmation" />
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="signup">
                    <span wire:loading.remove wire:target="signup">Signup</span>
                    <span wire:loading wire:target="signup">Signing up...</span>
                </button>
            </form>

            <div class="extra_links">
                <p>Already have an account? <a href="{{ Route::has('login') ? route('login') : '#' }}">Login</a></p>
            </div>
        </div>
    </div>
</div>
