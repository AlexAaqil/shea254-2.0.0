<div class="Authentication">
    <div class="container Login">
        <div class="custom_form">
            <div class="header">
                <h1>Login</h1>
            </div>

            <form wire:submit="login">
                <div class="inputs">
                    <label for="email">Email Address</label>
                    <input type="email" wire:model="email" name="email" id="email" autocomplete="username" autofocus>
                    <x-form-input-error field="email" />
                </div>

                <div class="inputs">
                    <label for="password">Password</label>
                    <input type="password" wire:model="password" name="password" id="password" autocomplete="current-password">
                    <x-form-input-error field="password" />
                </div>

                <div class="extra_inputs">
                    <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}" wire:navigate>Forgot your password?</a>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">Login</span>
                    <span wire:loading wire:target="login">Logging in...</span>
                </button>
            </form>

            <div class="extra_links">
                <p>Don't have an account? <a href="{{ Route::has('signup') ? route('signup') : '#' }}">Signup</a></p>
            </div>
        </div>
    </div>
</div>
